<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('SuperAdmin')) {
            abort(403, 'Only SuperAdmin can access this page.');
        }

        $companies = Company::withCount(['users', 'shortUrls'])
            ->with(['users' => function($query) {
                $query->limit(1);
            }])
            ->where('name', '!=', 'System')
            ->latest()
            ->paginate(10);
        
        // Calculate hits sum manually
        foreach ($companies as $company) {
            $company->short_urls_sum_hits = ShortUrl::where('company_id', $company->id)->sum('hits');
        }

        return view('clients.index', compact('companies'));
    }
}
