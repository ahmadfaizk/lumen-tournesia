<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Auth;
use Validator;
use App\Post;
use App\Image;

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

        $this->query = DB::table('posts')
            ->leftJoin('comments', 'comments.id_post', '=', 'posts.id')
            ->join('users', 'users.id', '=', 'posts.id_user')
            ->join('categories', 'categories.id', '=', 'posts.id_category')
            ->join('provinces', 'provinces.id', '=', 'posts.id_province')
            ->join('regencies', 'regencies.id', '=', 'posts.id_regency')
            ->select('posts.*',
                'users.name as user_name',
                'categories.name as category',
                'provinces.name as province',
                'regencies.name as regency',
                DB::raw('AVG(COALESCE(comments.votes, 0)) as votes'),
                DB::raw('COUNT(comments.votes) as votes_count')
            )
            ->groupBy('posts.id');
    }

    private $query;

    public function index() {
        $user = Auth::user();
        $data = $this->query
            ->where('posts.id_user', $user->id)
            ->get();
        for ($i=0; $i<$data->count(); $i++) {
            $images = Image::find(DB::table('post_image')
                ->select('id_image')
                ->where('id_post', $data[$i]->id)
                ->get()
                ->pluck('id_image')
            );
            $data[$i]->images = $images;
        }
        return response()->json([
            'error' => false,
            'message' => 'Success get My Posts',
            'data' => $data
        ]);
    }

    public function all() {
        $data = $this->query
            ->get();
        for ($i=0; $i<$data->count(); $i++) {
            $images = Image::find(DB::table('post_image')
                ->select('id_image')
                ->where('id_post', $data[$i]->id)
                ->get()
                ->pluck('id_image')
            );
            $data[$i]->images = $images;
        }
        return response()->json([
            'error' => false,
            'message' => 'Success get All Posts',
            'data' => $data
        ]);
    }

    public function search(Request $request) {
        $data = $this->query
            ->where('posts.name', 'like', '%'. $request->name . '%')
            ->get();
        for ($i=0; $i<$data->count(); $i++) {
            $images = Image::find(DB::table('post_image')
                ->select('id_image')
                ->where('id_post', $data[$i]->id)
                ->get()
                ->pluck('id_image')
            );
            $data[$i]->images = $images;
        }
        return response()->json([
            'error' => false,
            'message' => 'Success Search',
            'data' => $data,
            'query' => $request->name
        ]);
    }

    public function detail($id) {
        $data = $this->query
            ->where('posts.id', $id)
            ->first();
        if($data == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Post Not Found!',
                'data' => null
            ]);
        }
        $data->images = Image::find(DB::table('post_image')
            ->select('id_image')
            ->where('id_post', $id)
            ->get()
            ->pluck('id_image')
        );
        return response()->json([
            'error' => false,
            'message' => 'Success get Detail Posts',
            'data' => $data,
        ]);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'id_category' => 'required|int',
            'description' => 'required|string',
            'address' => 'required|string',
            'id_province' => 'required|int',
            'id_regency' => 'required|int',
            'images' => 'required',
            'images.*' => 'image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors_detail' => $validator->errors()->all(),
                'data' => null
            ]);
        }
        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $img_name = $this->saveImage($image);
                $imgs[] = Image::create(['name' => $img_name])->id;
            }
        }
        $user = Auth::user()->id;
        $post = new Post([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'id_province' => $request->id_province,
            'id_regency' => $request->id_regency,
            'id_category' => $request->id_category,
            'id_user' => $user
        ]);
        $post->save();
        foreach ($imgs as $img) {
            DB::table('post_image')->insert([
                'id_post' => $post->id,
                'id_image' => $img
            ]);
        }

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
            'address' => 'required|string',
            'id_province' => 'required|int',
            'id_regency' => 'required|int',
            'id_category' => 'required|int',
            'images' => 'nullable',
            'images.*' => 'image'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors_detail' => $validator->errors()->all(),
                'data' => null
            ]);
        }
        $post->name = $request->name;
        $post->description = $request->description;
        $post->address = $request->address;
        $post->id_province = $request->id_province;
        $post->id_regency = $request->id_regency;
        $post->id_category = $request->id_category;
        $post->save();

        if($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imgName = $this->saveImage($image);
                $imgId = Image::create(['name' => $imgName])->id;
                DB::table('post_image')->insert([
                    'id_post' => $post->id,
                    'id_image' => $imgId
                ]);
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'Succes Update Post!',
            'data' => $post
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
        unlink('posts/' . $img->name);
        return response()->json([
            'error' => false,
            'message' => 'Succes Delete Image',
            'data' => $img
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
        $t = time();
        $image_name = Str::random(5) . $t . '.' . $image->getClientOriginalExtension();
        $image->move('posts', $image_name);
        return $image_name;
    }
}
