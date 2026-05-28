<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ForgotPasswordController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = rtrim(env('SUBSCRIPTION_API_URL'), '/');
    }

    public function showLinkRequestForm()
    {
        return view('auth.password.email');
    }

    // 1. Menembak ke /api/reset-password/send
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        try {
            // Kita sesuaikan URL targetnya di sini:
            $response = Http::timeout(20)->post("{$this->apiUrl}/reset-password/send", [
                'email' => $request->email,
                'client_url' => url('/'),
            ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['success']) && $responseData['success'] === true) {
                return redirect()->route('password.request')->with('status', $responseData['message']);
            }

            $errorMessage = $responseData['errors']['email'][0] ?? ($responseData['message'] ?? 'Terjadi kesalahan sistem.');

            return redirect()->route('password.request')->withErrors(['email' => $errorMessage]);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return redirect()->route('password.request')->withErrors(['email' => 'Gagal terhubung ke server Subscription.']);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.password.reset')->with([
            'token' => $request->query('token') ?? $token,
            'email' => $request->query('email'),
        ]);
    }

    // 2. Menembak ke /api/reset-password/handle
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Kita sesuaikan URL target eksekusinya di sini:
            $response = Http::timeout(5)->post("{$this->apiUrl}/reset-password/handle", [
                'token' => $request->token,
                'email' => $request->email,
                'new_password' => $request->password,
                'new_password_confirmation' => $request->password_confirmation,
            ]);

            if ($response->successful()) {
                return redirect()->route('login')->with('status', 'Password Anda berhasil diperbarui!');
            }

            $responseData = $response->json();
            $errors = $responseData['errors'] ?? ['email' => [$responseData['message'] ?? 'Gagal mereset password.']];

            return back()->withErrors($errors)->withInput($request->only('email', 'token'));

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return back()->withErrors(['email' => 'Gagal terhubung ke server untuk pembaruan password.']);
        }
    }
}
