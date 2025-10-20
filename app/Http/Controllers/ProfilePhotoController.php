<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use Intervention\Image\Facades\Image;

class ProfilePhotoController extends Controller
{
    /**
     * Upload profile photo
     */
    public function upload(Request $request)
    {
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

        // Delete old photo if exists
        if ($user->profile_photo) {
            Storage::delete('profile-photos/' . $user->profile_photo);
        }

        // Generate unique filename
        $filename = Str::uuid() . '.' . $request->file('profile_photo')->getClientOriginalExtension();

        // Store the file
        $path = $request->file('profile_photo')->storeAs('profile-photos', $filename, 'public');

        // Create thumbnail (optional) - disabled for now
        // $this->createThumbnail($path, $filename);

        // Update user profile photo
        $user->update(['profile_photo' => $filename]);

        return response()->json([
            'success' => true,
            'message' => 'Profile photo updated successfully',
            'photo_url' => $user->profile_photo_url
        ]);
    }

    /**
     * Delete profile photo
     */
    public function delete()
    {
        $user = auth()->user();

        if ($user->deleteProfilePhoto()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile photo deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No profile photo to delete'
        ], 400);
    }

    /**
     * Create thumbnail for profile photo (disabled for now)
     */
    private function createThumbnail($path, $filename)
    {
        // Thumbnail creation disabled - requires Intervention Image package
        // To enable: composer require intervention/image
        return;
    }
}
