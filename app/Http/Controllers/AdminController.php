<?php

namespace App\Http\Controllers;

use App\Models\Matches;
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

    $maleCount = User::where('gender', 'male')->count();
    $femaleCount = User::where('gender', 'female')->count();

    $malePercentage = $totalUsers > 0 ? round(($maleCount / $totalUsers) * 100, 2) : 0;
    $femalePercentage = $totalUsers > 0 ? round(($femaleCount / $totalUsers) * 100, 2) : 0;

    $activeUsers = User::where('is_suspended', false)->count();

    // ✅ Collect matches grouped by user without duplicates
    $matchesByUser = collect();
    $allMatches = Matches::orderBy('created_at', 'desc')->get();

    $processedPairs = []; // Track processed user pairs to avoid duplicate rows
    $matchedCount = 0;
    $pendingCount = 0;

    foreach ($allMatches as $match) {
        $pairKey = $match->user_id < $match->matched_user_id
            ? $match->user_id . '_' . $match->matched_user_id
            : $match->matched_user_id . '_' . $match->user_id;

        if (in_array($pairKey, $processedPairs)) {
            continue; // Skip duplicate pair
        }

        // Check if mutual match exists
        $mutual = $allMatches->first(function($m) use ($match) {
            return $m->user_id == $match->matched_user_id && $m->matched_user_id == $match->user_id;
        });

        $status = $mutual ? 'Matched' : 'Pending';

        // Count matches by status
        if ($mutual) {
            $matchedCount++;
        } else {
            $pendingCount++;
        }

        // Add the match to the initiator
        $matchesByUser->put(
            $match->user_id,
            collect($matchesByUser->get($match->user_id, []))->push([
                'id' => $match->id,
                'user_name' => $match->user_name,
                'matched_user_name' => $match->matched_user_name,
                'created_at' => $match->created_at,
                'status' => $status,
            ])
        );

        // If mutual, add to target user's table as well
        if ($mutual) {
            $matchesByUser->put(
                $match->matched_user_id,
                collect($matchesByUser->get($match->matched_user_id, []))->push([
                    'id' => $match->id,
                    'user_name' => $match->matched_user_name,
                    'matched_user_name' => $match->user_name,
                    'created_at' => $match->created_at,
                    'status' => 'Matched',
                ])
            );
        }

        $processedPairs[] = $pairKey; // Mark this pair as processed
    }

    return view('admin.dashboard', compact(
        'users',
        'totalUsers',
        'newUsersToday',
        'dates',
        'counts',
        'maleCount',
        'femaleCount',
        'malePercentage',
        'femalePercentage',
        'activeUsers',
        'matchesByUser',
        'matchedCount',   // ✅ pass to view
        'pendingCount'    // ✅ pass to view
    ));
}





}
