<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Post;

class RatingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'id_post' => 'required|integer',
            'votes' => 'required|integer|max:5|min:1',
            'comment' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }
        $user = Auth::user();
        $rating = DB::table('ratings')
            ->select('*')
            ->where('id_post', $request->id_post)
            ->where('id_user', $user->id)
            ->get();

        if ($rating != null) {
            return response()->json([
                'error' => true,
                'message' => 'You have commented on this post',
                'data' => null
            ]);
        }
        $data = DB::table('ratings')->insert([
            'vote' => $request->votes,
            'comment' => $request->comment,
            'id_user' => $user->id,
            'id_post' => $request->id_post,
        ]);
    }

    public function update(Request $request) {
        
    }
}
