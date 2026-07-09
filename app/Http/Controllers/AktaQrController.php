<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AktaQrController extends Controller
{
    public function show(Request $request, $transaction_code)
    {
        // try {
        $decodedCode = Crypt::decryptString($transaction_code);
        // } catch (\Exception $e) {
        //     abort(404); // jika URL diubah / rusak
        // }
        // DB::enableQueryLog();

        $akta = NotaryAktaTransaction::with(['client', 'akta_type', 'notaris', 'parties'])
            ->where('transaction_code', $decodedCode)
            ->first()
            ??
            NotaryRelaasAkta::with(['client', 'akta_type', 'notaris', 'parties'])
                ->where('transaction_code', $decodedCode)
                ->first();

        if (! $akta) {
            abort(404);
        }
        // dd([
        //     'ID dari Akta yang ditemukan' => $akta->id,
        //     'Semua data Akta' => $akta->toArray(),
        // ]);
        // dd($akta);
        // dd(DB::getQueryLog());
        // session()->forget("pin_verified_{$transaction_code}");

        return view('pages.PreviewTransaction.index', compact('akta'));
    }

    public function showPinForm($transaction_code)
    {
        return view('pages.BackOffice.AktaQr.pin_form', compact('transaction_code'));
    }

    public function checkPin(Request $request, $transaction_code)
    {
        $request->validate([
            'access_code' => 'required_without:pin',
            'pin' => 'required_without:access_code',
        ], [
            'access_code.required_without' => 'Kode akses wajib diisi jika PIN kosong.',
            'pin.required_without' => 'PIN wajib diisi jika kode akses kosong.',
        ]);
        // dd($request);

        // try {
        $decodedCode = Crypt::decryptString($transaction_code);
        // } catch (\Exception $e) {
        //     abort(404); // jika URL diubah / rusak
        // }
        // DB::enableQueryLog();

        $akta = NotaryAktaTransaction::with(['client', 'akta_type', 'notaris', 'parties'])
            ->where('transaction_code', $decodedCode)
            ->first()
            ??
            NotaryRelaasAkta::with(['client', 'akta_type', 'notaris', 'parties'])
                ->where('transaction_code', $decodedCode)
                ->first();
        // dd($decodedCode, $akta);
        if (! $akta) {
            abort(404);
        }

        $notarisData = DB::table('notaris') // sesuaikan dengan nama tabel notaris Anda jika berbeda
            ->where('id', $akta->notaris_id)
            ->first();
        // dd($notarisData);
        if (! $notarisData) {
            return back()->withErrors(['access_code' => 'Data profil Notaris untuk akta ini tidak ditemukan di database.']);
        }

        // Setelah data notarisnya ketemu, baru kita cari akun User login-nya menggunakan 'user_id'
        $notaryUser = User::find($notarisData->user_id);

        if (! $notaryUser) {
            return back()->withErrors(['access_code' => 'Akun Login untuk Notaris ini belum terdaftar atau tidak ditemukan.']);
        }

        $isValid = false;

        if ($request->filled('pin') && $notaryUser->pin) {
            if (Hash::check($request->pin, $notaryUser->pin)) {
                $isValid = true;
            }
        }
        if ($request->filled('access_code') && $notaryUser->access_code) {
            if ($request->access_code === $notaryUser->access_code) {
                $isValid = true;
            }
        }
        if ($isValid) {
            session(["pin_verified_{$transaction_code}" => true]);

            return redirect()->route('akta.qr.show', $transaction_code);

        }

        // Jika PIN salah, kembalikan ke form dengan pesan error
        return back()->withErrors(['access_code' => 'Kode akses atau PIN yang Anda masukkan salah.']);
    }
}
