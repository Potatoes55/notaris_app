<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PinController extends Controller
{
    // Menampilkan halaman form pembuatan PIN
    public function showCreateForm()
    {
        return view('auth.pin.create_pin');
    }

    // Menyimpan PIN baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'pin' => 'required|digits:6|confirmed', // Memastikan 6 digit angka dan cocok dengan konfirmasi
        ], [
            'pin.required' => 'PIN wajib diisi.',
            'pin.digits' => 'PIN harus berupa 6 digit angka.',
            'pin.confirmed' => 'Konfirmasi PIN tidak cocok.',
        ]);

        $user = Auth::user();
        $user->pin = Hash::make($request->pin); // Amankan PIN dengan Hashing
        $user->save();

        return redirect()->route('settings')->with('success', 'PIN berhasil dibuat!');
    }
}
