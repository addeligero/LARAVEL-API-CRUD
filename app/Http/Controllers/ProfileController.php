<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    
    public function getUser(Request $request)
    {
        return response()->json(Auth::user());
    }

    
    public function UploadImage(Request $request)
    {
        
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        $validated = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], 422);
        }

        try {
            $user = Auth::user();

           
            if (!$request->hasFile('image')) {
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            
            $path = $file->storeAs("uploads/{$user->id}", $filename, 'public');

            if (!$path) {
                return response()->json(['error' => 'File storage failed'], 500);
            }

            
            $imagePath = "uploads/{$user->id}/{$filename}";
            $user->image = $imagePath;
            $user->save();

            
            if (!$user->wasChanged('image')) {
                return response()->json(['error' => 'Database update failed'], 500);
            }

            return response()->json([
                'message' => 'Image uploaded successfully',
                'image_path' => asset("storage/{$imagePath}"),
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
