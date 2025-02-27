<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Dashboard;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function AddImage(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        if (!$request->hasFile('image')) {
            return response()->json(['error' => 'No file uploaded'], 400);
        }

        try {
            $file = $request->file('image');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads', $filename, 'public');

            if (!$path) {
                return response()->json(['error' => 'File storage failed'], 500);
            }

            $user = $request->user();
            $dashboard = Dashboard::updateOrCreate(
                ['user_id' => $user->id],
                ['image' => $path]
            );

            return response()->json(['message' => 'added successfully', 'path' => $dashboard->image], 200);
        } catch (\Exception $e) {
            Log::error('An error occurred while uploading the image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'An error occurred while uploading the image', 'details' => $e->getMessage()], 500);
        }
    }

    public function checkUserImage(Request $request)
    {
        $user = $request->user();
        $dashboard = Dashboard::where('user_id', $user->id)->first();

        if ($dashboard && $dashboard->image) {
            return response()->json(['has_image' => true, 'image' => $dashboard->image], 200);
        }

        return response()->json(['has_image' => false], 200);
    }

    public function uploadMotto(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'motto' => 'required|string|max:255',
        ]);

        if ($validated->fails()) {
            return response()->json($validated->errors(), 422);
        }

        $user = $request->user();
        $dashboard = Dashboard::updateOrCreate(
            ['user_id' => $user->id],
            ['motto' => $request->motto]
        );

        return response()->json(['message' => 'Motto added successfully', 'motto' => $dashboard->motto], 200);

    }
}
