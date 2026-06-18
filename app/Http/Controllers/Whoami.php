<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Whoami extends Controller
{
    public function index()
    {
        return view('auth.whoami');
    }

    public function select(Request $request)
    {
        $request->validate([
            'role' => 'required|in:notaris,staff',
        ]);
        $user = Auth::user();

        if ($request->role === 'notaris') {
            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->success('Anda Login Sebagai Notaris/PPAT, '.$user->username.'!');

            return redirect()->route('settings')->with('silahkan masukan kode akses untuk masuk ke dashboard notaris');
        }
        if ($request->role === 'staff') {
            notyf()
                ->position('x', 'right')
                ->position('y', 'top')
                ->success('Anda Login Sebagai PIC, '.$user->username.'!');

            return redirect()->route('dashboard')->with('sukses masuk sebagai PIC Staff');
        }

    }
}
