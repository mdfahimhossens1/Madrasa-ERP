<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

public function store(Request $request): RedirectResponse
{
    $request->validate([
        'phone' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    // Find user by phone
    $user = User::with('institution')
        ->where('phone', $request->phone)
        ->first();

    if (!$user) {
        return back()->withErrors([
            'phone' => 'মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
        ])->onlyInput('phone');
    }

    // User status
    if (!$user->status) {
        return back()->withErrors([
            'phone' => 'আপনার অ্যাকাউন্ট নিষ্ক্রিয় করা হয়েছে।',
        ])->onlyInput('phone');
    }

    // Institution status
    if (
        !$user->is_super_admin &&
        !$user->is_soft_admin &&
        $user->institution &&
        !$user->institution->status
    ) {
        return back()->withErrors([
            'phone' => 'প্রতিষ্ঠানটি বর্তমানে নিষ্ক্রিয়।',
        ])->onlyInput('phone');
    }

    // Password
    if (!Hash::check($request->password, $user->password)) {
        return back()->withErrors([
            'phone' => 'মোবাইল নম্বর অথবা পাসওয়ার্ড সঠিক নয়।',
        ])->onlyInput('phone');
    }

    // Update login info
    $user->update([
        'last_login_at' => now(),
        'last_login_ip' => $request->ip(),
    ]);

    // Login
// Login
Auth::login($user, $request->boolean('remember'));

$request->session()->regenerate();

// Save Institution Context
session([
    'institution_id' => $user->institution_id,
]);

return redirect()->route('dashboard.index');
}
private function redirectBasedOnRole($user): RedirectResponse
{
    return redirect()->route('dashboard.index');
}

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}