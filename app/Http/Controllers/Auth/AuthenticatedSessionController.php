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
        // Validate login inputs
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'madrasa_code' => 'nullable|string',
        ]);

        $loginField = $request->login;
        $madrasaCode = $request->madrasa_code;

        // Query builder with multi-tenant support
        $query = User::query();

        // If madrasa code is provided, filter by madrasa code
        if ($madrasaCode) {
            $query->whereHas('madrasa', function($q) use ($madrasaCode) {
                $q->where('madrasa_code', $madrasaCode);
            });
        }

        // Check if login is email or username
        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            $query->where('email', $loginField);
        } else {
            $query->where('username', $loginField);
        }

        $user = $query->first();

        // User not found
        if (!$user) {
            return back()->withErrors([
                'login' => 'ইউজারনেম/ইমেইল বা পাসওয়ার্ড ভুল।',
            ])->onlyInput('login', 'madrasa_code');
        }

        // Check if user is active
        if (!$user->status) {
            return back()->withErrors([
                'login' => 'আপনার অ্যাকাউন্টটি নিষ্ক্রিয় করা হয়েছে।',
            ])->onlyInput('login', 'madrasa_code');
        }

        // Check madrasa status (for non-super admin)
        if (!$user->is_super_admin && $user->madrasa && !$user->madrasa->status) {
            return back()->withErrors([
                'login' => 'মাদ্রাসাটি নিষ্ক্রিয় করা হয়েছে।',
            ])->onlyInput('login', 'madrasa_code');
        }

        // Check password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'ইউজারনেম/ইমেইল বা পাসওয়ার্ড ভুল।',
            ])->onlyInput('login', 'madrasa_code');
        }

        // Update last login info
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
        ]);

        // Login user
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        // Redirect based on role (FIXED)
        return $this->redirectBasedOnRole($user);
    }

    private function redirectBasedOnRole($user): RedirectResponse
{
    // Super Admin
    if ($user->is_super_admin) {
        return redirect('/super-admin/dashboard');
    }
    
    // Soft Admin
    if ($user->is_soft_admin) {
        return redirect('/soft-admin/dashboard');
    }
    
    // Madrasa Admin
    if ($user->is_madrasa_admin) {
        return redirect('/madrasa-admin/dashboard');
    }
    
    // Teacher
    if ($user->is_teacher) {
        return redirect('/teacher/dashboard');
    }
    
    // Student
    if ($user->is_student) {
        return redirect('/student/dashboard');
    }
    
    // Guardian
    if ($user->is_guardian) {
        return redirect('/guardian/dashboard');
    }
    
    // Default fallback
    return redirect('/');
}

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}