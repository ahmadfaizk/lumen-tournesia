<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Auth;
use Validator;
use App\Post;
use App\Comment;
use App\Image;

class CommentController extends Controller
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

    public function index($id) {
        $data = Comment::where('id_post', $id)->get();
        for ($i=0; $i<$data->count(); $i++) {
            $images = Image::find(DB::table('comment_image')
                ->select('id_image')
                ->where('id_comment', $data[$i]->id)
                ->get()
                ->pluck('id_image')
            );
            $data[$i]->images = $images;
        }
        return response()->json([
            'error' => false,
            'message' => 'Success get Comments',
            'data' => $data,
        ]);
    }

    public function add(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'votes' => 'required|integer|max:5|min:1',
            'comment' => 'required|string',
            'images' => 'nullable',
            'images.*' => 'image'
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
        $comment = Comment::where([
            'id_post' => $id,
            'id_user' => $user->id
        ])->first();
        if ($comment != null) {
            return response()->json([
                'error' => true,
                'message' => 'You have commented on this post',
                'data' => $comment
            ]);
        }
        $comment = new Comment([
            'votes' => $request->votes,
            'comment' => $request->comment,
            'id_user' => $user->id,
            'id_post' => $id,
        ]);
        $comment->save();
        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imgName = $this->saveImage($image);
                $imgId = Image::create(['name' => $imgName])->id;
                DB::table('comment_image')->insert([
                    'id_comment' => $comment->id,
                    'id_image' => $imgId
                ]);
            }
        }
        return response()->json([
            'error' => false,
            'message' => 'You succes comment on this post',
            'data' => $comment
        ]);
    }

    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'votes' => 'required|integer|max:5|min:1',
            'comment' => 'required|string',
            'images' => 'nullable',
            'images.*' => 'image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }
        $comment = Comment::where('id_post', $id)->first();
        if ($comment == null) {
            return response()->json([
                'error' => true,
                'message' => 'Comment not found',
                'data' => null
            ]);
        }
        $comment->votes = $request->votes;
        $comment->comment = $request->comment;
        $comment->save();
        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imgName = $this->saveImage($image);
                $imgId = Image::create(['name' => $imgName])->id;
                DB::table('comment_image')->insert([
                    'id_comment' => $comment->id,
                    'id_image' => $imgId
                ]);
            }
        }
        return response()->json([
            'error' => false,
            'message' => 'You success update on this rating',
            'data' => $comment
        ]);
    }

    public function deleteImage($id) {
        $img = Image::find($id);
        if ($img == null) {
            return response()->json([
                'error' => true,
                'message' => 'Image not found',
                'data' => null
            ]);
        }
        $img->delete();
        unlink('comments/' . $img->name);
        return response()->json([
            'error' => false,
            'message' => 'Success Delete Images',
            'data' => null
        ]);
    }

    public function delete($id) {
        $comment = Comment::where('id_post', $id)->first();
        if ($comment == null) {
            return response()->json([
                'error' => true,
                'message' => 'Rating not found',
                'data' => null
            ]);
        }
        $images = DB::table('comment_image')
                ->select('id_image')
                ->where('id_comment', $comment->id)
                ->get()
                ->pluck('id_image');
                
        foreach ($images as $img) {
            $this->deleteImage($img);
        }
        $comment->delete();
        return response()->json([
            'error' => false,
            'message' => 'You success delete this rating',
            'data' => $comment
        ]);
    }

    private function saveImage($image) {
        $t = time();
        $image_name = Str::random(5) . $t . '.' . $image->getClientOriginalExtension();
        $image->move('comments', $image_name);
        return $image_name;
    }
}
