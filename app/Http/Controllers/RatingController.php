<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Validator;
use App\Post;
use App\Rating;

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
        $rating = Rating::where([
            'id_post' => $id,
            'id_user' => $user->id
        ])->first();
        $ratings = DB::table('ratings')
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
        $rating = new Rating([
            'votes' => $request->votes,
            'comment' => $request->comment,
            'id_user' => $user->id,
            'id_post' => $id,
        ]);
        $rating->save();
        return response()->json([
            'error' => false,
            'message' => 'You succes comment on this post',
            'data' => $rating
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
        $rating = Rating::find($id);
        if ($rating == null) {
            return response()->json([
                'error' => true,
                'message' => 'Rating not found',
                'data' => null
            ]);
        }
        $rating->votes = $request->votes;
        $rating->comment = $request->comment;
        $rating->save();
        return response()->json([
            'error' => false,
            'message' => 'You success update on this rating',
            'data' => $rating
        ]);
    }

    public function delete($id) {
        $rating = Rating::find($id);
        if ($rating == null) {
            return response()->json([
                'error' => true,
                'message' => 'Rating not found',
                'data' => null
            ]);
        }
        $rating->delete();
        return response()->json([
            'error' => false,
            'message' => 'You success delete this rating',
            'data' => $rating
        ]);
    }
}
