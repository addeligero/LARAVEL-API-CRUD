<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Make sure to import
use Illuminate\Support\Facades\Hash; // Make sure to import
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // $validated=$request->validate([
        // 'name'=>'required|max:255|string',
        // 'email'=>'required|email|string|max:255|unique:users',
        // 'password'=>'required|min:6|string|confirmed'

        // ]);

        $validated=Validator::make($request->all(), [
        'name'=>'required|max:255|string',
        'email'=>'required|email|string|max:255|unique:users',
        'password'=>'required|min:6|string|confirmed'

        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        try {
            $user= User::create([
        'name'=>$request->name,
        'email'=>$request->email,
        'password'=>Hash::make($request->password),
        ]);

            $token=$user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token'=> $token,
                'user'=> $user
            ], 200);
        } catch (\Exception $th) {
            return response()->json(['error'=>$th->getMessage()]);
        }
    }
    public function login(Request $request)
    {
        $validated=Validator::make($request->all(), [
        'email'=>'required|email|string',
        'password'=>'required|min:6|string'
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }
        $credentials = ['email'=> $request->email, 'password'=> $request->password];
        try {
            if(!auth()->attempt($credentials)) {
                return response()->json(['error'=>'Invalid credentials']);
            }

            $user = User::where('email', $request->email)->firstOrFail();
            $token=$user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'access_token'=> $token,
                'user'=> $user
            ], 200);

        } catch (\Exception $th) {
            return response()->json(['error'=>$th->getMessage()]);
        }

    }

    //logpouy

    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'message'=> 'Logged out successfully'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json(['error'=>$th->getMessage()]);

        }

      


    }
}
