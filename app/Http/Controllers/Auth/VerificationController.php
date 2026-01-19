<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmailVerificationToken;
use App\Models\User;
use App\Services\EmailApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VerificationController extends Controller
{
    /**
     * Show verification page - for users who need to verify their email
     */
    public function showVerificationPage()
    {
        if (Auth::check() && Auth::user()->isVerified()) {
            return redirect()->route('home')->with('info', 'Email Anda sudah diverifikasi.');
        }

        return Inertia::render('Auth/VerifyEmail', [
            'status' => session('status'),
        ]);
    }

    /**
     * Verify email using token
     */
    public function verify(Request $request, string $token)
    {
        $verificationToken = EmailVerificationToken::where('token', $token)->first();

        if (!$verificationToken) {
            return redirect()->route('email.verify-page')->with('error', 'Token verifikasi tidak valid.');
        }

        if ($verificationToken->isExpired()) {
            $user = $verificationToken->user;
            $verificationToken->delete();

            // Redirect ke resend page dengan error
            return redirect()
                ->route('email.verify-page')
                ->with('error', 'Token verifikasi sudah kadaluarsa. Silakan request verifikasi ulang.');
        }

        $user = $verificationToken->user;

        // If already verified, just delete token and redirect
        if ($user->isVerified()) {
            $verificationToken->delete();
            return redirect()
                ->route('email.verify-page')
                ->with('success', 'Email Anda sudah diverifikasi sebelumnya.');
        }

        // Mark as verified
        $user->email_verified_at = now();
        $user->save();

        $verificationToken->delete();

        // Auto login if not authenticated
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()
            ->route('home')
            ->with('success', 'Email berhasil diverifikasi! Anda sekarang mendapat harga khusus member.');
    }

    /**
     * Resend verification email
     */
    public function resend(Request $request)
    {
        // Get authenticated user or from request
        $user = Auth::user();

        if (!$user) {
            return back()->with('error', 'Anda harus login terlebih dahulu.');
        }

        if ($user->isVerified()) {
            return back()->with('info', 'Email Anda sudah diverifikasi.');
        }

        // Check rate limiting - prevent spam
        $lastToken = EmailVerificationToken::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subMinutes(1))
            ->first();

        if ($lastToken) {
            return back()->with('error', 'Silakan tunggu 1 menit sebelum meminta verifikasi ulang.');
        }

        // Send verification email
        $this->sendVerificationEmail($user);

        return back()->with('success', 'Email verifikasi telah dikirim ulang. Silakan cek email Anda.');
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
     * Request verification - for cases where user needs to reverify
     */
    public function requestVerification()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu.');
        }

        if ($user->isVerified()) {
            return redirect()->route('home')->with('info', 'Email Anda sudah diverifikasi.');
        }

        $this->sendVerificationEmail($user);

        return redirect()
            ->route('email.verify-page')
            ->with('status', 'Verification email has been sent. Please check your email.');
    }
}
