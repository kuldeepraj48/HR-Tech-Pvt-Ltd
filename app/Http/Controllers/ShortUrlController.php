<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShortUrlController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // SuperAdmin cannot see the list of all short URLs
        if ($user->hasRole('SuperAdmin')) {
            abort(403, 'SuperAdmin cannot see the list of all short URLs.');
        }
        
        $query = ShortUrl::with(['user', 'company']);

        // Admin can only see short URLs not created in their own company
        if ($user->hasRole('Admin')) {
            $query->where('company_id', '!=', $user->company_id);
        } elseif ($user->hasRole('Member')) {
            // Member can only see short URLs not created by themselves
            $query->where('user_id', '!=', $user->id);
        }

        // Date filtering
        $dateFilter = $request->get('date_filter', 'this_month');
        switch ($dateFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'last_week':
                $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                      ->whereYear('created_at', now()->subMonth()->year);
                break;
            case 'this_month':
            default:
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        $shortUrls = $query->latest()->paginate(10);

        return view('short-urls.index', compact('shortUrls', 'dateFilter'));
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @author Kuldeep
     */
    public function download(Request $request)
    {
        $user = Auth::user();
        
        // SuperAdmin cannot see the list of all short URLs
        if ($user->hasRole('SuperAdmin')) {
            abort(403, 'SuperAdmin cannot see the list of all short URLs.');
        }
        
        $query = ShortUrl::with(['user', 'company']);

        if ($user->hasRole('Admin')) {
            $query->where('company_id', '!=', $user->company_id);
        } elseif ($user->hasRole('Member')) {
            $query->where('user_id', '!=', $user->id);
        }

        // Apply same date filter
        $dateFilter = $request->get('date_filter', 'this_month');
        switch ($dateFilter) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'last_week':
                $query->whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()]);
                break;
            case 'last_month':
                $query->whereMonth('created_at', now()->subMonth()->month)
                      ->whereYear('created_at', now()->subMonth()->year);
                break;
            case 'this_month':
            default:
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }

        $shortUrls = $query->latest()->get();

        $filename = 'short_urls_' . $dateFilter . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($shortUrls, $user) {
            $file = fopen('php://output', 'w');
            
            if ($user->hasRole('Admin')) {
                fputcsv($file, ['Short URL', 'Long URL', 'Hits', 'User', 'Created On']);
                foreach ($shortUrls as $url) {
                    fputcsv($file, [
                        url('/s/' . $url->short_code),
                        $url->original_url,
                        $url->hits,
                        $url->user->name,
                        $url->created_at->format('d M \'y'),
                    ]);
                }
            } else {
                fputcsv($file, ['Short URL', 'Long URL', 'Hits', 'Created On']);
                foreach ($shortUrls as $url) {
                    fputcsv($file, [
                        url('/s/' . $url->short_code),
                        $url->original_url,
                        $url->hits,
                        $url->created_at->format('d M \'y'),
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function create()
    {
        $user = Auth::user();
        
        // SuperAdmin cannot create short URLs
        if ($user->hasRole('SuperAdmin')) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }

        // Admin and Member cannot create short URLs
        if ($user->hasRole('Admin') || $user->hasRole('Member')) {
            abort(403, 'Admin and Member cannot create short URLs.');
        }

        // Only Sales and Manager can create short URLs
        if (!$user->hasRole('Sales') && !$user->hasRole('Manager')) {
            abort(403, 'You do not have permission to create short URLs.');
        }

        return view('short-urls.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Kuldeep
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // SuperAdmin cannot create short URLs
        if ($user->hasRole('SuperAdmin')) {
            abort(403, 'SuperAdmin cannot create short URLs.');
        }

        // Admin and Member cannot create short URLs
        if ($user->hasRole('Admin') || $user->hasRole('Member')) {
            abort(403, 'Admin and Member cannot create short URLs.');
        }

        // Only Sales and Manager can create short URLs
        if (!$user->hasRole('Sales') && !$user->hasRole('Manager')) {
            abort(403, 'You do not have permission to create short URLs.');
        }

        $validator = Validator::make($request->all(), [
            'original_url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        ShortUrl::create([
            'original_url' => $request->original_url,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'hits' => 0,
        ]);

        return redirect()->route('short-urls.index')
            ->with('success', 'Short URL created successfully.');
    }

    /**
     * @param string $code
     * @return \Illuminate\Http\RedirectResponse
     * @author Kuldeep
     */
    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();
        
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Short URLs are not publicly resolvable.');
        }

        // Increment hit count
        $shortUrl->incrementHits();

        return redirect($shortUrl->original_url);
    }
}
