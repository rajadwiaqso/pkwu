<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\EmailVerificationToken;
use App\Services\EmailApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

/**
 * AuthController - Menangani authentication
 */
class AuthController extends Controller
{
    /**
     * Show login page
     */
    public function showLogin()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Jika email belum diverifikasi, redirect ke halaman verifikasi
            if (!$user->isVerified()) {
                return redirect()->route('email.verify-page')
                    ->with('info', 'Silakan verifikasi email Anda untuk melanjutkan.');
            }

            // Redirect based on role
            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration page
     */
    public function showRegister()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'buyer',
        ]);

        // Generate verification token and send email
        $this->sendVerificationEmail($user);

        // Auto login
        Auth::login($user);

        // Redirect to email verification page
        return redirect()->route('email.verify-page')->with('status', 'Registrasi berhasil! Silakan verifikasi email Anda untuk mendapatkan harga khusus member.');
    }

    /**
     * Send verification email
     */
    protected function sendVerificationEmail(User $user)
    {
        // Delete old tokens
        EmailVerificationToken::where('user_id', $user->id)->delete();

        // Create new token
        $token = EmailVerificationToken::create([
            'user_id' => $user->id,
            'token' => Str::random(64),
            'expires_at' => now()->addHours(24),
        ]);

        // Send email via external API
        $verificationUrl = route('email.verify', ['token' => $token->token]);
        
        $emailService = new EmailApiService();
        $emailService->sendVerificationEmail(
            $user->email,
            $user->name,
            $verificationUrl
        );
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $token)
    {
        $verificationToken = EmailVerificationToken::where('token', $token)->first();

        if (!$verificationToken) {
            return redirect()->route('home')->with('error', 'Token verifikasi tidak valid.');
        }

        if ($verificationToken->isExpired()) {
            $verificationToken->delete();
            return redirect()->route('home')->with('error', 'Token verifikasi sudah kadaluarsa.');
        }

        $user = $verificationToken->user;
        $user->email_verified_at = now();
        $user->save();

        $verificationToken->delete();

        return redirect()->route('home')->with('success', 'Email berhasil diverifikasi! Anda sekarang mendapat harga khusus member.');
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $user = $request->user();

        if ($user->isVerified()) {
            return back()->with('info', 'Email Anda sudah diverifikasi.');
        }

        $this->sendVerificationEmail($user);

        return back()->with('success', 'Email verifikasi telah dikirim ulang.');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
