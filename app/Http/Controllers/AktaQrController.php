<?php

namespace App\Http\Controllers;

use App\Models\NotaryAktaTransaction;
use App\Models\NotaryRelaasAkta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class AktaQrController extends Controller
{
    public function show(Request $request, $transaction_code)
    {
        try {
            $decodedCode = Crypt::decryptString($transaction_code);
        } catch (\Exception $e) {
            abort(404); // jika URL diubah / rusak
        }

        $akta = NotaryAktaTransaction::with(['client', 'akta_type', 'notaris'])
            ->where('transaction_code', $decodedCode)
            // ->where('notaris_id', auth()->user()->notaris_id)
            ->first();
        if (! $akta) {
            $akta = NotaryRelaasAkta::with(['client', 'akta_type', 'notaris'])
                ->where('transaction_code', $decodedCode)->first();
        } else {
            abort(404); // jika data tidak ditemukan
        }

        return view('pages.PreviewTransaction.index', compact('akta'));
    }
}
