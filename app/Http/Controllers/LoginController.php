<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Notaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class LoginController extends Controller
{
    /**
     * Display login page.
     *
     * @return Renderable
     */
    public function show()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credential = $request->validated();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->status === 'pending') {
                Auth::logout();
                notyf()
                    ->position('x', 'right')
                    ->position('y', 'top')
                    ->warning('Akun kamu belum aktif. Silakan hubungi admin untuk aktivasi.');

                return redirect()->route('login');
            }

            $lastSubscription = $user->subscriptions()->latest('end_date')->first();

            // Jika subscription sudah berakhir, hapus access_code di tabel subscription
            if ($lastSubscription && \Illuminate\Support\Carbon::parse($lastSubscription->end_date)->isPast()) {
                $user->update(['access_code' => null]);

                notyf()->position('x', 'right')->position('y', 'top')
                    ->warning('Subscription Anda telah berakhir. Akses fitur kini terbatas.');
            }

            $request->session()->regenerate();

            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->success('Selamat datang, '.$user->username.'!');

            // return redirect()->route('dashboard');
            return redirect()->intended(route('dashboard'));
        } else {
            notyf()->position('x', 'right')->position('y', 'top')->error('Email atau kata sandi salah.');

            return redirect()->route('login');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil Logout');

        return redirect()->route('login');
    }

    public function alertForgotPassword()
    {
        notyf()
            ->position('x', 'right')
            ->position('y', 'top')
            ->info('Silakan hubungi admin di nomor 0813-2312-3123 untuk mengatur ulang kata sandi Anda.');

        return redirect()->route('login');
    }

    public function profileNotaris($hash)
    {
        try {
            $id = Crypt::decryptString($hash);
        } catch (\Exception $e) {
            abort(404);
        }

        $notaris = Notaris::findOrFail($id);

        return view('pages.profile-notaris', compact('notaris'));
    }
}
