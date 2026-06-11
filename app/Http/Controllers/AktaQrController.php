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
            ->first()
            ??
            NotaryRelaasAkta::with(['client', 'akta_type', 'notaris'])
                ->where('transaction_code', $decodedCode)
                ->first();

        if (! $akta) {
            abort(404);
        }

        return view('pages.PreviewTransaction.index', compact('akta'));
    }
}
