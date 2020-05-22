<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Register Failed!',
                'errors_detail' => $validator->errors()->all(),
                'data' => null
            ]);
        }

        $name = $request->name;
        $email = $request->email;
        $address = $request->address;
        $password = Hash::make($request->password);
        $apiToken = base64_encode(Str::random(40));

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'address' => $address,
            'password' => $password,
            'api_token' => $apiToken
        ]);

        return response()->json([
            'error' => false,
            'message' => 'Register Success!',
            'data' => [
                'token' => $apiToken,
                'type' => 'Bearer'
            ],
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => true,
                'message' => 'Login Failed!',
                'errors_detail' => $validator->errors()->all(),
                'data' => null
            ]);
        }

        $email = $request->email;
        $password = $request->password;

        $user = User::where('email', $email)->first();
        if ($user != null) {
            if (Hash::check($password, $user->password)) {
                $apiToken = base64_encode(Str::random(40));
                $user->update([
                    'api_token' => $apiToken
                ]);
                return response()->json([
                    'error' => false,
                    'message' => 'Login Succes!',
                    'data' => [
                        'token' => $apiToken,
                        'type' => 'Bearer'
                    ],
                ]);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => 'Login Failed!',
                    'errors_detail' => [
                        'password' => 'Password wrong'
                    ],
                    'data' => null
                ]);
            }
        } else {
            return response()->json([
                'error' => true,
                'message' => 'Login Failed!',
                'errors_detail' => [
                    'email' => 'Email Not Found!'
                ],
                'data' => null
            ]);
        }
    }
}
