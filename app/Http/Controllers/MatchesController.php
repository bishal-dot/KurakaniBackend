<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MatchesController extends Controller
{
    public function index(Request $request)
    {
        Log::info($request->all());

        $statusFilter = $request->query('status'); // All, Matched, Pending
        $currentUserId = $request->query('user_id'); // current user ID

        if (!$currentUserId) {
            return response()->json(['error' => 'Missing user_id'], 400);
        }

        // Fetch all matches initiated by current user (with relationships)
        $userMatches = Matches::with(['user', 'matchedUser'])
            ->where('user_id', $currentUserId)
            ->get();

        // Find mutual matches (reciprocated entries)
        $matchedIds = Matches::whereIn('user_id', $userMatches->pluck('matched_user_id'))
            ->where('matched_user_id', $currentUserId)
            ->pluck('user_id')
            ->toArray();

        // Transform matches for API response
        $apiMatches = $userMatches->map(function ($match) use ($matchedIds, $statusFilter) {
            $status = in_array($match->matched_user_id, $matchedIds) ? 'Matched' : 'Pending';

            // Apply filter
            if ($statusFilter && strtolower($statusFilter) !== 'all' && strtolower($status) !== strtolower($statusFilter)) {
                return null;
            }

            return [
                'id' => $match->id,
                'name' => $match->matchedUser->fullname ?? 'Unknown',
                'status' => $status,
            ];
        })
        ->filter() // remove nulls
        ->values();

        return response()->json($apiMatches);
    }
}
