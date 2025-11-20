<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Helpers\LogHelper;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // ========================================
        // LOG LOGIN - MANUAL
        // ========================================
        $user = Auth::user();
        if ($user) {
            $nama = $user->nama_lengkap ?? $user->email ?? 'Unknown';
            LogHelper::log('Login ke sistem - ' . $nama, $user->id_akun);
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ========================================
        // LOG LOGOUT - SEBELUM LOGOUT!
        // ========================================
        $user = Auth::user();
        if ($user) {
            $nama = $user->nama_lengkap ?? $user->email ?? 'Unknown';
            LogHelper::log('Logout dari sistem - ' . $nama, $user->id_akun);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}