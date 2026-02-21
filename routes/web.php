<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Sembark URL Shortener: authentication, dashboard, short URLs, clients,
| team members, and invitations. Redirect /s/{code} resolved in ShortUrlController.
|
| @author Kuldeep
|
*/

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\TeamMemberController;

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Invitation Acceptance Routes (public - no auth required)
Route::get('/invitations/accept/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/accept/{token}', [InvitationController::class, 'processAcceptance'])->name('invitations.process');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Invitation Routes
    Route::resource('invitations', InvitationController::class);
    
    // Short URL Routes
    Route::resource('short-urls', ShortUrlController::class);
    Route::get('/short-urls/download', [ShortUrlController::class, 'download'])->name('short-urls.download');
    
    // Client Routes (SuperAdmin only)
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    
    // Team Member Routes (Admin only)
    Route::get('/team-members', [TeamMemberController::class, 'index'])->name('team-members.index');
});

// Short URL redirect route (must be last to avoid conflicts)
// Note: Auth check is done inside the controller, not via middleware
Route::get('/s/{code}', [ShortUrlController::class, 'redirect'])->name('short-urls.redirect');

// Home redirect
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');
