<?php

namespace App\Http\Controllers;

use Auth;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index() {
        return response()->json([
            'message' => 'Succes get User Data',
            'data' => Auth::user()
        ]);
    }
}
