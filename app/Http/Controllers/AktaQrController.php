<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AktaQrController extends Controller
{
    public function show(Request $request, $transaction_code)
    {
        // if (!auth()->check()) {
        //     return redirect()->route('login');
        // }

        try {
            $decodedCode = Crypt::decryptString($transaction_code);
        } catch (\Exception $e) {
            abort(404); // jika URL diubah / rusak
        }

        $akta = NotaryAktaTransaction::with(['client', 'akta_type', 'notaris'])
            ->where('transaction_code', $decodedCode)
            // ->where('notaris_id', auth()->user()->notaris_id)
            ->first();

        // if ($akta->notaris_id !== auth()->user()->notaris_id) {
        //     notyf()
        //         ->position('x', 'right')
        //         ->position('y', 'top')
        //         ->warning('Anda tidak memiliki akses ke transaksi ini.');

        //     return redirect()->route('dashboard');
        // }

        return view('pages.PreviewTransaction.index', compact('akta'));
    }
}
