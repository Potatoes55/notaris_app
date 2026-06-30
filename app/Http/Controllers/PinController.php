<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class PinController extends Controller
{
    protected $apiUrl;

    public function __construct()
    {

        $this->apiUrl = rtrim(env('SUBSCRIPTION_API_URL'), '/');
    }

    public function showCreateForm()
    {
        return view('auth.pin.create_pin');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6|confirmed',
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.digits' => 'PIN harus berupa 6 digit angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.',
        ]);

        $user = Auth::user();

        $oldPin = $user->pin;

        $user->pin = Hash::make($request->pin);
        $user->save();

        // try{
        $response = Http::timeout(30)->post("{$this->apiUrl}/sync-pin", [
            'email' => $user->email,
            'pin' => $user->pin,
        ]);

        if ($response->successful()) {
            return redirect()->route('settings')->with('success', 'PIN berhasil dibuat!');
        }

        $user->pin = $oldPin;
        $user->save();

        $responseData = $response->json();
        $errorMessage = $responseData['message'] ?? 'Gagal menyinkronkan PIN ke server Subscription.';

        return back()->withErrors(['pin' => $errorMessage])->withInput();

        // } catch (\Illuminate\Http\Client\ConnectionException $e) {
        //     $user->pin = $oldPin;
        //     $user->save();

        //     return back()->withErrors(['pin' => 'Gagal terhubung ke server Subscription untuk sinkronisasi PIN.'])->withInput();
        // }
    }
}
