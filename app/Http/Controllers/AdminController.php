<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Get all users from database
        $users = User::all();

        // Get total users from database
        $totalUsers = User::count();

        // New registrations today
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();

        // Pass data to the view
        return view('admin.dashboard', compact('users','totalUsers','newUsersToday'));
        

    }
}
