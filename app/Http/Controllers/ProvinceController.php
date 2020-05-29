<?php

namespace App\Http\Controllers;

use App\Province;

class ProvinceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        $data = Province::all();
        return response()->json([
            'error' => false,
            'message' => 'Succes get Provinces data',
            'data' => $data
        ]);
    }
}
