<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamMemberController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\View
     * @author Kuldeep
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user->hasRole('Admin')) {
            abort(403, 'Only Admin can access this page.');
        }

        $teamMembers = User::where('company_id', $user->company_id)
            ->with('roles')
            ->withCount(['shortUrls'])
            ->get()
            ->map(function ($member) {
                $member->total_hits = ShortUrl::where('user_id', $member->id)->sum('hits');
                return $member;
            });

        return view('team-members.index', compact('teamMembers'));
    }
}
