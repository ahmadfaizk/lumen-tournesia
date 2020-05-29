<?php

namespace App\Http\Controllers;

use App\Regency;

class RegencyController extends Controller
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

    public function index($id) {
        $data = Regency::where('id_province', $id)->get();
        return response()->json([
            'error' => false,
            'message' => 'Succes get Regencies data',
            'data' => $data
        ]);
    }
}
