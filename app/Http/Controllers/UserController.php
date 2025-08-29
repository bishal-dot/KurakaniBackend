<?php

namespace App\Http\Controllers;

use App\Models\UserPhoto;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Interest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;


class UserController extends Controller
{
    // Complete or update user profile
   public function createProfile(Request $request)
{
    $request->validate([
        'fullname' => 'required|string|max:255',
        'age' => 'required|integer|min:18|max:100',
        'gender' => 'required|in:male,female',
        'purpose' => 'nullable|string|max:255',
        'job' => 'nullable|string|max:255',
        'education' => 'nullable|string|max:255',
        'about' => 'nullable|string',
        'interests' => 'nullable|array',
        // 'profile_base64' => 'nullable|string',
    ]);

    $user = Auth::user();

    $user->fullname = $request->fullname;
    $user->age = $request->age;
    $user->gender = $request->gender;
    $user->purpose = $request->purpose;
    $user->job = $request->job;
    $user->education = $request->education;
    $user->about = $request->about;
    $user->interests = $request->interests ? json_encode($request->interests) : null;

    // Handle Base64 profile
    // if ($request->profile_base64) {
    //     $imageData = $request->profile_base64;
    //     $image = base64_decode($imageData);
    //     $fileName = 'profile_' . time() . '.jpg';
    //     $filePath = storage_path('app/public/profiles/' . $fileName);

    //     // make sure directory exists
    //     if (!file_exists(dirname($filePath))) {
    //         mkdir(dirname($filePath), 0755, true);
    //     }

    //     file_put_contents($filePath, $image);
    //     $user->profile = asset('storage/profiles/' . $fileName);
    // }

    $user->profile_complete = true;
    $user->save();

    // Create Sanctum token
    $token = $user->createToken('auth_token')->plainTextToken;

     return response()->json([
        'success' => true,
        'message' => 'Profile created successfully.',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'fullname' => $user->fullname,
            'username' => $user->username,
            'email' => $user->email,
            'age' => $user->age,
            'gender' => $user->gender,
            'purpose' => $user->purpose,
            'job' => $user->job,
            'education' => $user->education,
            'about' => $user->about,
            'interests' => json_decode($user->interests) ?? [],
            'profile_complete' => $user->profile_complete,
            'is_verified' => $user->is_verified,
            'profile' => $user->profile,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ]
    ]);
}


    //upload profile photo
    public function uploadProfilePhoto(Request $request)
{
    $request->validate([
        'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
    ]);

    $user = $request->user();

    if ($request->hasFile('profile')) {
        $file = $request->file('profile');

        // Optional: Delete old profile photo if exists
        if ($user->profile) {
            Storage::disk('public')->delete($user->profile);
        }

        // Save new profile photo
        $path = $file->store('profiles', 'public');

        // Update user profile URL
        $user->profile = $path;
        $user->save();

        return response()->json([
            'error' => false,
            'message' => 'Profile photo uploaded successfully',
            'profile_url' => asset("storage/$path") // public URL
        ]);
    }

    return response()->json([
        'error' => true,
        'message' => 'No profile photo uploaded'
    ], 400);
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
                'profile' => $user->profile ? asset('storage/' . $user->profile) : null,
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
        $currentUserGender = strtolower($currentUser->gender);

        if ($currentUserGender === 'male') {
            $targetGender = 'female';
        } elseif ($currentUserGender === 'female') {
            $targetGender = 'male';
        } else {
            // if gender is unknown, just return empty
            return response()->json([]);
        }

        // Fetch all other users except the logged-in user
        $users = User::where('id', '!=', $currentUser->id)
                 ->whereRaw('Lower(gender) = ? ', $targetGender)
                 ->where('is_verified', true)
                 ->get();

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
                'is_verified' => $user->is_verified
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
    $currentUser = $request->user();
    $currentUserId = $currentUser->id;
    $search = $request->input('search'); // user input

    // Base query: exclude current user
    $query = User::with('photos')->where('id', '!=', $currentUserId);

    if ($search) {
        $searchLower = strtolower($search);

        $query->where(function ($q) use ($searchLower) {
            $q->whereRaw('LOWER(fullname) LIKE ?', ["%$searchLower%"])
              ->orWhereRaw('LOWER(username) LIKE ?', ["%$searchLower%"])
              ->orWhere(function ($subQ) use ($searchLower) {
                  // Match if any interest contains the search term
                  $subQ->whereJsonContains('interests', $searchLower);
              });
        });
    }

    $users = $query->get();

    $response = $users->map(function ($user) {
        $interests = $user->interests;
        if ($interests && !is_array($interests)) {
            $decoded = json_decode($interests, true);
            $interests = is_array($decoded) ? $decoded : explode(',', $user->interests);
        }

        $photos = $user->photos->map(fn($photo) => asset('storage/' . $photo->photo_path));

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
            'photos' => $photos,
            'is_verified' => $user->is_verified
        ];
    });

    return response()->json([
        'success' => true,
        'users' => $response
    ]);
}



public function getInterests()
{
    $users = User::pluck('interests')->toArray(); // get all users' interests
    $allInterests = [];

    foreach ($users as $interests) {
        if ($interests) {
            // Decode JSON if stored as array
            $decoded = is_array($interests) ? $interests : json_decode($interests, true);
            if (is_array($decoded)) {
                $allInterests = array_merge($allInterests, $decoded);
            }
        }
    }

    // Normalize: lowercase, trim, and remove duplicates
    $allInterests = array_unique(array_map(function($i) {
        return strtolower(trim($i));
    }, $allInterests));

    return response()->json(array_values($allInterests));
}

// change password
public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:6|confirmed',
    ]);

    $user = $request->user();

    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['error' => true, 'message' => 'Current password is incorrect'], 400);
    }

    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['error' => false, 'message' => 'Password updated successfully']);
}


}   
