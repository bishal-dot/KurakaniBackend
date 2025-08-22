<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\User;

class AdminController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        dd($request);
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            } else {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. You are not an admin.']);
            }
        }

        return back()->withErrors(['email' => 'Invalid login credentials.']);
    }

    public function dashboard()
    {
        $users = User::all();
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();

        $dates = [];
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $counts[] = User::whereDate('created_at', $date)->count();
        }

        return view('admin.dashboard', compact('users','totalUsers','newUsersToday','dates','counts'));
    }
}
