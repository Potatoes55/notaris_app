<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Notaris;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use function Flasher\Notyf\Prime\notyf;

class UserProfileController extends Controller
{
    public function unlock(Request $request)
    {
        // 1. Validasi input: minimal salah satu harus diisi (access_code ATAU pin)
        $request->validate([
            'access_code' => 'required_without:pin',
            'pin' => 'required_without:access_code',
        ], [
            'access_code.required_without' => 'Kode akses wajib diisi jika PIN kosong.',
            'pin.required_without' => 'PIN wajib diisi jika kode akses kosong.',
        ]);

        $user = auth()->user();
        $isValid = false;

        // 2. Cek jika user menginput PIN dari form
        if ($request->filled('pin') && $user->pin) {
            if (Hash::check($request->pin, $user->pin)) {
                $isValid = true;
            }
        }

        // 3. Cek jika user menginput Access Code dari form
        if ($request->filled('access_code') && $user->access_code) {
            if ($request->access_code === $user->access_code) {
                $isValid = true;
            }
        }

        // 4. Eksekusi hasil pengecekan
        if ($isValid) {
            session([
                'access_all_menu' => true,
            ]);

            notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil membuka akses');

            return redirect()->intended(route('dashboard'));
        }

        notyf()->position('x', 'right')->position('y', 'top')->error('Kode akses atau PIN salah.');

        return back();
    }

    public function show()
    {
        $user = User::where('id', Auth::user()->id)->first();
        $notaris = $user->notaris;
        // dd($notaris->toArray());

        return view('pages.user-profile', compact('user', 'notaris'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $credential = $request->validated();
        $credential['user_id'] = $user->id;

        // dd($credential);

        // Upload image jika ada
        if ($request->hasFile('image')) {
            $credential['image'] = $request->file('image')->storeAs(
                'img',
                $request->file('image')->getClientOriginalName()
            );
        }

        // Jika user belum punya notaris
        if (! $user->notaris_id) {

            // Buat notaris baru
            $notaris = Notaris::create($credential);

            // Update user
            $user->update([
                'notaris_id' => $notaris->id,
            ]);
        } else {

            // Update existing notaris
            $notaris = $user->notaris;

            if (! $request->hasFile('image')) {
                unset($credential['image']);
            }

            $notaris->update($credential);
        }

        notyf()->position('x', 'right')->position('y', 'top')->success('Berhasil mengubah profil.');

        return redirect()->route('profile');
    }
}
