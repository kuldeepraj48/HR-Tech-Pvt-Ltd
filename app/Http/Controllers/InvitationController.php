<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class InvitationController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function index()
    {
        $user = Auth::user();
        $invitations = Invitation::with(['company', 'role', 'inviter'])
            ->where('invited_by', $user->id)
            ->latest()
            ->get();

        return view('invitations.index', compact('invitations'));
    }

    /**
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function create()
    {
        $user = Auth::user();
        
        // SuperAdmin invites clients (creates new companies)
        if ($user->hasRole('SuperAdmin')) {
            return view('invitations.create-client');
        }
        
        // Admin invites team members
        $roles = Role::whereNotIn('name', ['SuperAdmin'])->get();
        return view('invitations.create', compact('roles'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @author Kuldeep
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // SuperAdmin invites clients (creates new companies)
        if ($user->hasRole('SuperAdmin')) {
            // SuperAdmin cannot invite an Admin in a new company using regular invitation endpoint
            // They must use the client invitation flow (with name field)
            if ($request->has('role_id')) {
                $requestedRole = Role::findOrFail($request->role_id);
                if ($requestedRole->name === 'Admin') {
                    return back()->withErrors(['error' => 'SuperAdmin cannot invite an Admin in a new company.']);
                }
            }
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            // Create new company
            $company = Company::create([
                'name' => $request->name,
            ]);

            // Get Admin role
            $adminRole = Role::where('name', 'Admin')->firstOrFail();

            // Create invitation for Admin in new company
            $invitation = Invitation::create([
                'email' => $request->email,
                'company_id' => $company->id,
                'role_id' => $adminRole->id,
                'invited_by' => $user->id,
            ]);

            return redirect()->route('clients.index')
                ->with('success', 'Client invitation sent successfully.');
        }

        // Admin invites team members
        if ($user->hasRole('Admin')) {
            $requestedRole = Role::findOrFail($request->role_id);
            if (in_array($requestedRole->name, ['Admin', 'Member'])) {
                return back()->withErrors(['error' => 'Admin cannot invite another Admin or Member in their own company.']);
            }
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $invitation = Invitation::create([
            'email' => $request->email,
            'company_id' => $user->company_id,
            'role_id' => $request->role_id,
            'invited_by' => $user->id,
        ]);

        // In a real application, send email here
        // Mail::to($request->email)->send(new InvitationMail($invitation));

        return redirect()->route('team-members.index')
            ->with('success', 'Invitation sent successfully.');
    }

    /**
     * @param string $token
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function accept($token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * @param Request $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     * @author Kuldeep
     */
    public function processAcceptance(Request $request, $token)
    {
        $invitation = Invitation::where('token', $token)
            ->whereNull('accepted_at')
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::transaction(function () use ($invitation, $request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $invitation->email,
                'password' => Hash::make($request->password),
                'company_id' => $invitation->company_id,
            ]);

            $user->roles()->attach($invitation->role_id, [
                'company_id' => $invitation->company_id,
            ]);

            $invitation->update(['accepted_at' => now()]);
        });

        return redirect()->route('login')
            ->with('success', 'Account created successfully. Please login.');
    }
}
