<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::user()->role == 'user') {
            return view('user.index');
        }elseif(Auth::user()->role == 'admin'){
            return view('admin.index');
        } else {
            abort(404);
        }
    }
}
