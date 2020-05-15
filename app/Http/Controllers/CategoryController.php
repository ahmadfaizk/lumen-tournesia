<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Validator;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        #$this->middleware('auth');
    }

    public function index()
    {
        $data = Category::all();
        return response()->json([
            'error' => false,
            'message' => 'Success get All Category',
            'data' => $data
        ]);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }
        $category = new Category(['name' => $request->name]);
        $category->save();
        return response()->json([
            'error' => false,
            'message' => 'Success Add Category',
            'data' => $category
        ]);
    }

    public function update(Request $request, $id) {
        $category = Category::find($id);
        if($category == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Category Not Found!',
                'data' => null
            ]);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Error Request',
                'errors' => $validator->errors(),
                'data' => null
            ]);
        }
        $category->name = $request->name;
        $category->save();
        return response()->json([
            'error' => false,
            'message' => 'Success Update Category',
            'data' => $category
        ]);
    }

    public function delete($id) {
        $category = Category::find($id);
        if($category == null) {
            return response()->json([
                'error' => true,
                'message' => 'ID Category Not Found!',
                'data' => null
            ]);
        }
        $category->delete();
        return response()->json([
            'error' => false,
            'message' => 'Success Delete Category',
            'data' => $category
        ]);
    }
}
