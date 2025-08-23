<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    // Show all users
    public function index()
    {
        $users = User::all();
        return view('admin.dashboard', compact('users'));
    }

    // Show edit form
   public function bulkAction(Request $request)
{
    $request->validate([
        'user_ids' => 'required|array',
        'action'   => 'required|string|in:suspend,activate,delete',
    ]);

    $users = User::whereIn('id', $request->user_ids);

    switch ($request->action) {
        case 'suspend':
            $users->update(['is_suspended' => true]);
            $msg = 'Selected users have been suspended.';
            break;
        case 'activate':
            $users->update(['is_suspended' => false]);
            $msg = 'Selected users have been activated.';
            break;
        case 'delete':
            $users->delete();
            $msg = 'Selected users have been deleted.';
            break;
        default:
            $msg = 'No valid action selected.';
    }

    return redirect()->route('admin.users.index')->with('success', $msg);
}


}
