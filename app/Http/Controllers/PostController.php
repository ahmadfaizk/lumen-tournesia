<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Auth;
use Validator;
use App\Post;

class PostController extends Controller
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

    public function index() {
        $user = Auth::user();
        $data = DB::table('posts')
            ->leftJoin('ratings', 'ratings.id_post', '=', 'posts.id')
            ->select('posts.*', DB::raw('AVG(ratings.votes) as votes'))
            ->where('posts.id_user', $user->id)
            ->groupBy('posts.id')
            ->get();
        $posts = Post::where('id_user', $user)->get();
        return response()->json([
            'error' => false,
            'message' => 'Success get My Posts',
            'data' => $data
        ]);
    }

    public function all() {
        $data = DB::table('posts')
            ->leftJoin('ratings', 'ratings.id_post', '=', 'posts.id')
            ->select('posts.*', DB::raw('AVG(COALESCE(ratings.votes, 0)) as votes'))
            ->groupBy('posts.id')
            ->get();
            return response()->json([
                'error' => false,
                'message' => 'Success get All Posts',
                'data' => $data
            ]);
    }

    public function detail($id) {
        $data = DB::table('posts')
            ->leftJoin('ratings', 'ratings.id_post', '=', 'posts.id')
            ->select('posts.*', DB::raw('AVG(ratings.votes) as votes'))
            ->where('posts.id', $id)
            ->groupBy('posts.id')
            ->get();
        if($data == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Post Not Found!',
                'data' => null
            ]);
        }
        $comments = DB::table('ratings as r')
            ->join('users as u', 'u.id', '=', 'r.id_user')
            ->select('r.votes', 'r.comment', 'u.name')
            ->where('r.id_post', $id)
            ->get();
            return response()->json([
                'error' => false,
                'message' => 'Success get Detail Posts',
                'data' => $data,
                'comment' => $comments
            ]);
    }

    public function upload(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'image' => 'required|image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }
        $image_name = null;
        if($request->hasFile('image')) {
            $image_name = $this->saveImage($request->file('image'));
        }
        $user = Auth::user()->id;
        $post = new Post([
            'name' => $request->name,
            'description' => $request->description,
            'province' => $request->province,
            'city' => $request->city,
            'image' => $image_name,
            'id_user' => $user
        ]);
        $post->save();

        return response()->json([
            'error' => false,
            'message' => 'Succes Upload Post!',
            'data' => $post
        ]);
    }

    public function update(Request $request, $id) {
        $post = Post::find($id);
        if($post == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Post Not Found!',
                'data' => null
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'image' => 'nullable|image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }

        if($request->hasFile('image')) {
            unlink('posts/' . $post->image);
            $post->image = $this->saveImage($request->file('image'));
        }
        $post->name = $request->name;
        $post->description = $request->description;
        $post->province = $request->province;
        $post->city = $request->city;
        $post->save();

        return response()->json([
            'error' => false,
            'message' => 'Succes Update Post!',
            'data' => $post
        ]);
    }

    public function delete($id) {
        $post = Post::find($id);
        if($post == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Person Not Found!',
                'data' => null
            ]);
        }

        if ($post->image != null) {
            unlink('posts/' . $post->image);
        }
        $post->delete();
        return response()->json([
            'error' => false,
            'message' => 'Succes Delete Person',
            'data' => $post
        ]);
    }

    private function saveImage($image) {
        $user = Auth::user()->id;
        $t = time();
        $image_name = $user . '_' . $t . '.' . $image->getClientOriginalExtension();
        $image->move('posts', $image_name);
        return $image_name;
    }
}
