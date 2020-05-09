<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function add(Request $request, $id) {
        $validator = Validator::make($request->all(), [
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
            ->where('id_post', $id)
            ->where('id_user', $user->id)
            ->first();

        if ($rating != null) {
            return response()->json([
                'error' => true,
                'message' => 'You have commented on this post',
                'data' => $rating
            ]);
        }
        $data = DB::table('ratings')->insert([
            'votes' => $request->votes,
            'comment' => $request->comment,
            'id_user' => $user->id,
            'id_post' => $id,
        ]);
        return response()->json([
            'error' => false,
            'message' => 'You succes comment on this post',
            'data' => null
        ]);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
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
            ->where('id_post', $id)
            ->where('id_user', $user->id)
            ->first();

        if ($rating == null) {
            return response()->json([
                'error' => true,
                'message' => 'Rating not found',
                'data' => null
            ]);
        }
        $data = DB::table('ratings')
            ->where('id_user', $user->id)
            ->where('id_post', $id)
            ->update([
                'votes' => $request->votes,
                'comment' => $request->comment,
            ]);
        return response()->json([
            'error' => false,
            'message' => 'You success update on this post',
            'data' => $data
        ]);
    }

    public function delete($id) {
        $user = Auth::user();
        $rating = DB::table('ratings')
            ->where('id_post', $id)
            ->where('id_user', $user->id)
            ->first();

        if ($rating == null) {
            return response()->json([
                'error' => true,
                'message' => 'Rating not found',
                'data' => null
            ]);
        }
        $data = DB::table('ratings')
            ->where('id_user', $user->id)
            ->where('id_post', $id)
            ->delete();
        return response()->json([
            'error' => false,
            'message' => 'You success delete this post',
            'data' => $data
        ]);
    }
}
