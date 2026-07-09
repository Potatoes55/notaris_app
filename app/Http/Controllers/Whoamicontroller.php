<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Whoamicontroller extends Controller
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

        session([
            'login_role' => $request->role,
        ]);

        if ($request->role == 'staff') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('settings');
    }
}
