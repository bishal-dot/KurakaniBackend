<?php

namespace App\Http\Controllers;

use App\Models\Interest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    // List all users (already shown in your blade)
   public function index()
{
    $users = User::all();

    // Total users
    $totalUsers = $users->count();

    // Users joined today
    $newUsersToday = User::whereDate('created_at', now()->toDateString())->count();

    // Active users (assuming 'status' column indicates active/inactive)
    $activeUsers = User::where('is_suspended', 'false')->count();

    // Male/female percentages
    $maleCount = User::where('gender', 'male')->count();
    $femaleCount = User::where('gender', 'female')->count();
    $totalGenderCount = $maleCount + $femaleCount;

    // Avoid division by zero
    $malePercentage = $totalGenderCount > 0 ? round(($maleCount / $totalGenderCount) * 100) : 0;
    $femalePercentage = $totalGenderCount > 0 ? round(($femaleCount / $totalGenderCount) * 100) : 0;

    return view('admin.dashboard', compact(
        'users',
        'totalUsers',
        'newUsersToday',
        'activeUsers',
        'malePercentage',
        'femalePercentage'
    ));
}

    // ✅ Show edit form for a single user
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.edit', compact('user'));
    }

    // Edit/update user data
   public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $request->validate([
        'username'  => 'required|string|max:255',
        'email'     => 'required|email',
        'age'       => 'nullable|integer|min:18',
        'education' => 'nullable|string|max:255',
        'job'       => 'nullable|string|max:255',
        'about'     => 'nullable|string',
        'role'      => 'required|in:user,admin',
        'gender'    => 'nullable|in:male,female,other',
        'status'    => 'required|in:active,inactive,suspended',
    ]);

    $user->update([
        'username'  => $request->username,
        'email'     => $request->email,
        'age'       => $request->age,
        'education' => $request->education,
        'job'       => $request->job,
        'about'     => $request->about,
        'role'      => $request->role,
        'gender'    => $request->gender,
        'status'    => $request->status,
    ]);

    return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
}


    // Suspend/unsuspend user
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        $user->is_suspended = !$user->is_suspended; // toggle status
        $user->save();

        return response()->json([
            'message' => $user->is_suspended ? 'User suspended' : 'User re-activated'
        ]);
    }

    // Delete user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
