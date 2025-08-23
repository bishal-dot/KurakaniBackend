<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Interest;

class UserController extends Controller
{
    // Complete or update user profile
    public function completeProfile(Request $request)
    {
        $request->validate([
            'fullname'              => 'required|string|max:255',
            'age'                   => 'required|numeric|min:18',
            'gender'                => 'nullable|in:male,female',
            'profile_photo_base64'  => 'nullable|string',
            'purpose'               => 'nullable|string|max:255',
            'job'                   => 'nullable|string|max:255',
            'interests'             => 'nullable|array',
            'education'             => 'nullable|string|max:255',
            'about'                 => 'nullable|string|max:1000',
        ]);

        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => true, 'message' => 'Unauthorized'], 401);
        }

        try {
            $updateData = $request->only([
                'fullname', 'age', 'gender', 'purpose', 'job', 'education', 'about', 'interests'
            ]);

            // Encode interests if present
            if (isset($updateData['interests'])) {
                $updateData['interests'] = json_encode($updateData['interests']);
            }

            // Handle profile photo upload
            if (!empty($request->profile_photo_base64)) {
                $decoded = base64_decode($request->profile_photo_base64);
                if ($decoded !== false) {
                    $profilePhotoName = 'profile_' . Str::random(10) . '.jpg';
                    Storage::disk('public')->put('profile_photos/' . $profilePhotoName, $decoded);
                    $updateData['profile'] = $profilePhotoName;
                }
            }

            if (isset($updateData['interests'])) {
                $updateData['interests'] = $request->interests;
            }

            $user->update($updateData);

            return response()->json([
                'error'   => false,
                'message' => 'Profile completed successfully',
                'user'    => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => 'Internal server error',
                'debug'   => $e->getMessage(),
            ], 500);
        }
    }

    public function showProfile(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $interests = [];
        if ($user->interests) {
            if (is_array($user->interests)) {
                $interests = $user->interests;
            } else {
                $decoded = json_decode($user->interests, true);
                $interests = is_array($decoded) ? $decoded : explode(',', $user->interests);
            }
        }

        $matchesCount = $user->matches()->count();

        $photos = $user->photos()->get()->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => asset('storage/' . $photo->photo_path)
            ];
        });

        return response()->json([
            'error' => false,
            'message' => 'Profile fetched successfully',
            'user' => [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'username' => $user->username,
                'email' => $user->email,
                'age' => $user->age,
                'gender' => $user->gender,
                'purpose' => $user->purpose,
                'interests' => $interests,
                'about' => $user->about,
                'job' => $user->job,
                'education' => $user->education,
                'profile' => $user->profile ? url('storage/' . $user->profile) : null,
                'is_verified' => $user->is_verified,
                'matches_count' => $matchesCount,
                'photos' => $photos
            ]
        ], 200);
    }

  public function updateProfile(Request $request)
{
    $user = $request->user();
    if (!$user) {
        return response()->json(['error' => true, 'message' => 'Unauthorized'], 401);
    }

    $request->validate([
        'fullname'             => 'sometimes|required|string|max:255',
        'age'                  => 'sometimes|required|numeric|min:18',
        'gender'               => 'nullable|in:male,female',
        'purpose'              => 'nullable|string|max:255',
        'interests'            => 'nullable|array',
        'about'                => 'nullable|string|max:1000',
        'job'                  => 'nullable|string|max:255',
        'education'            => 'nullable|string|max:255',
        'profile_photo_base64' => 'nullable|string'
    ]);

    try {
        $updateData = $request->only([
            'fullname', 'age', 'gender', 'purpose', 'job', 'education', 'about', 'interests'
        ]);

        // Handle profile photo if sent
        if (!empty($request->profile_photo_base64)) {
            $decoded = base64_decode($request->profile_photo_base64);
            if ($decoded !== false) {
                $profilePhotoName = 'profile_' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('profile_photos/' . $profilePhotoName, $decoded);
                $updateData['profile'] = $profilePhotoName;
            }
        }

        $user->update($updateData);

        // Prepare response user data
        $interests = $user->interests ?? [];
        if (!is_array($interests)) {
            $decoded = json_decode($interests, true);
            $interests = is_array($decoded) ? $decoded : explode(',', $interests);
        }

        $photos = $user->photos()->get()->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => asset('storage/' . $photo->photo_path)
            ];
        });

        return response()->json([
            'error' => false,
            'message' => 'Profile updated successfully',
            'user' => [
                'id'        => $user->id,
                'fullname'  => $user->fullname,
                'username'  => $user->username,
                'age'       => $user->age,
                'gender'    => $user->gender,
                'purpose'   => $user->purpose,
                'about'     => $user->about,
                'job'       => $user->job,
                'education' => $user->education,
                'profile'   => $user->profile ? asset('storage/' . $user->profile) : null,
                'interests' => $interests,
                'photos'    => $photos
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'error'   => true,
            'message' => 'Internal server error',
            'debug'   => $e->getMessage()
        ], 500);
    }
}

    // Upload multiple photos
    public function uploadPhoto(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => true, 'message' => 'Unauthorized'], 401);

        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $uploadedPhotos = [];

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $file) {
                $filename = 'user_' . $user->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('user_photos', $filename, 'public');

                $photo = UserPhoto::create([
                    'user_id' => $user->id,
                    'photo_path' => $path
                ]);

                $uploadedPhotos[] = [
                    'id' => $photo->id,
                    'url' => asset('storage/' . $path)
                ];
            }
        }

        return response()->json([
            'error' => false,
            'message' => 'Photos uploaded successfully',
            'photos' => $uploadedPhotos
        ]);
    }

    public function deletePhoto(Request $request, $photoId)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => true, 'message' => 'Unauthorized'], 401);

        $photo = UserPhoto::where('id', $photoId)->where('user_id', $user->id)->first();
        if (!$photo) {
            return response()->json(['error' => true, 'message' => 'Photo not found'], 404);
        }

        if (Storage::disk('public')->exists($photo->photo_path)) {
            Storage::disk('public')->delete($photo->photo_path);
        }

        $photo->delete();

        return response()->json([
            'error' => false,
            'message' => 'Photo deleted successfully'
        ]);
    }

    // Fetch all uploaded photos
    public function getPhotos(Request $request)
    {
        $user = $request->user();
        if (!$user) return response()->json(['error' => true, 'message' => 'Unauthorized'], 401);

        $photos = $user->photos()->get()->map(function ($photo) {
            return [
                'id' => $photo->id,
                'url' => asset('storage/' . $photo->photo_path)
            ];
        });

        return response()->json([
            'error' => false,
            'message' => 'Photos fetched successfully',
            'photos' => $photos
        ]);
    }
    
    public function otherUsers(Request $request)
    {
        $currentUser = Auth::user();

        // Fetch all other users except the logged-in user
        $users = User::where('id', '!=', $currentUser->id)->get();

        $response = $users->map(function($user) {
            $interests = $user->interests;
            if ($interests && !is_array($interests)) {
                $decoded = json_decode($interests, true);
                $interests = is_array($decoded) ? $decoded : explode(',', $user->interests);
            }

            $photos = $user->photos()->get()->map(function(UserPhoto $photo){
                return asset('storage/' . $photo->photo_path);
            });

            return [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'username' => $user->username,
                'age' => $user->age,
                'gender' => $user->gender,
                'purpose' => $user->purpose,
                'interests' => $interests ?? [],
                'about' => $user->about ?? '',
                'profile' => $user->profile ? asset('storage/' . $user->profile) : null,
                'photos' => $photos, // directly include other photos
            ];
        });

        return response()->json($response);
    }

    public function getUser($id){
        $user = User::with('photos')->find($id);

        if (!$user) {
            return response()->json(['error' => true, 'message' => 'User not found'], 404);
        }

        // Decode interests
        $interests = [];
        if ($user->interests) {
            if (is_array($user->interests)) {
                $interests = $user->interests;
            } else {
                $decoded = json_decode($user->interests, true);
                $interests = is_array($decoded) ? $decoded : explode(',', $user->interests);
            }
        }

        // Map photos
        $photos = $user->photos->map(function (UserPhoto $photo) {
            return [
                'id' => $photo->id,
                'url' => asset('storage/' . $photo->photo_path),
            ];
        });

        // Return user directly (no wrapper)
        return response()->json([
            'id' => $user->id,
            'fullname' => $user->fullname,
            'username' => $user->username,
            'age' => $user->age,
            'gender' => $user->gender,
            'purpose' => $user->purpose,
            'about' => $user->about,
            'interests' => $interests,
            'profile' => $user->profile ? asset('storage/' . $user->profile) : null,
            'photos' => $photos
        ], 200);
    }

    
    // Search users with filters
 public function search(Request $request)
{
    $search = $request->input('search');
    $interests = $request->input('interests'); // comma separated e.g. "Music,Sports"

    $query = User::with('photos');

    if ($search || $interests) {
        $query->where(function ($q) use ($search, $interests) {
            if ($search) {
                $q->where('fullname', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            }

            if ($interests) {
                $interestArray = explode(',', $interests);
                $q->orWhereHas('interests', function ($sub) use ($interestArray) {
                    $sub->whereIn('name', $interestArray);
                });
            }
        });
    }

    $users = $query->get();

    $allInterests = Interest::all();

    return response()->json([
        'success' => true,
        'users' => $users,
        'interests' => $allInterests
    ]);
}



}   
