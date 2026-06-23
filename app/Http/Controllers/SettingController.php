<?php

namespace App\Http\Controllers;

class SettingController extends Controller
{
    //

    public function index()
    {
        return view('pages.Setting.index');
    }

    public function indexPIN()
    {
        return view('auth.pin.index');
    }
}
