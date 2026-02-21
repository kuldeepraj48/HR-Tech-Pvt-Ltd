<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     * @author Kuldeep
     */
    public function index()
    {
        $user = Auth::user();
        
        // Redirect based on role
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->route('clients.index');
        } elseif ($user->hasRole('Admin') || $user->hasRole('Member')) {
            // Admin and Member can view short URLs but not create them
            return redirect()->route('short-urls.index');
        } elseif ($user->hasRole('Sales') || $user->hasRole('Manager')) {
            // Sales and Manager can create and view short URLs
            return redirect()->route('short-urls.index');
        } else {
            return redirect()->route('short-urls.index');
        }
    }
}
