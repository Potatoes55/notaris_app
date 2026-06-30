<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // try {

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

        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
        //     return redirect()->route('password.request')->withErrors(['email' => 'Gagal terhubung ke server Subscription.']);
        // }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.password.reset')->with([
            'token' => $request->query('token') ?? $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // try {

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

        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
        //     return back()->withErrors(['email' => 'Gagal terhubung ke server untuk pembaruan password.']);
        // }
    }

    public function showPinRequestForm()
    {
        return view('auth.pin.email');
    }

    public function sendResetPinEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        // dd($request);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan di sistem kami.']);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // try {
        $resetUrl = route('pin.reset', ['token' => $token, 'email' => $request->email]);

        Mail::send('auth.pin.email-pin-reset', ['url' => $resetUrl, 'user' => $user], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Permintaan Reset PIN Aplikasi Notaris');
        });

        return redirect()->route('pin.request')->with('status', 'Link reset PIN telah dikirim ke email Anda.');

        // } catch (\Exception $e) {
        //     return back()->withErrors(['email' => 'Gagal mengirim email: '.$e->getMessage()]);
        // }
    }

    public function showResetPinForm(Request $request, $token = null)
    {
        return view('auth.pin.reset')->with([
            'token' => $request->query('token') ?? $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPin(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'pin' => 'required|digits:6|confirmed',
        ]);

        $tokenData = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $tokenData || ! Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['email' => 'Token reset PIN tidak valid atau sudah kedaluwarsa.']);
        }

        if (\Carbon\Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors(['email' => 'Token sudah kedaluwarsa, silakan minta link baru.']);
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        if (! $user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        $oldPin = $user->pin;

        $user->pin = Hash::make($request->pin);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // try {
        $response = Http::timeout(60)->post("{$this->apiUrl}/sync-pin", [
            'email' => $request->email,
            'pin' => $user->pin,
        ]);
        // dd($response->status(), $response->json());

        if ($response->successful()) {
            return redirect()->route('login')->with('status', 'PIN Anda berhasil diperbarui dan disinkronkan!');
        }

        $user->pin = $oldPin;
        $user->save();

        $responseData = $response->json();
        $errorMessage = $responseData['message'] ?? 'Gagal menyinkronkan PIN dengan server Subscription.';

        return back()->withErrors(['pin' => $errorMessage])->withInput($request->only('email', 'token'));

        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
        //     // Rollback PIN
        //     $user->pin = $oldPin;
        //     $user->save();

        //
        //     dd($e->getMessage());

        //     return back()->withErrors(['pin' => 'Gagal terhubung...']);

        // }
    }
}
