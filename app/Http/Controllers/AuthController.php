<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User; // Make sure to import
use Illuminate\Support\Facades\Hash; // Make sure to import

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated=$request->validate([
        'name'=>'required|max:255|string',
        'email'=>'required|email|string|max:255|unique:users',
        'password'=>'required|min:6|string|confirmed'

        ]);
        $user= User::create([
        'name'=>$validated['name'],
        'email'=>$validated['email'],
        'password'=>Hash::make($validated['password']),
        ]);

        $token=$user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token'=> $token,
            'user'=> $user
        ], 200);
    }
}
