<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Matches;
use Exception;
use Illuminate\Support\Facades\Log;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;

class MatchController extends Controller
{
    public function sendMatch(Request $request)
    {

        $request->validate([
            'user_id'         => 'required|integer|exists:users,id',
            'matched_user_id' => 'required|integer|exists:users,id',
        ]);

        $matcher = User::find($request->user_id);
        $targetUser = User::find($request->matched_user_id);

        if (!$matcher || !$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($matcher->id === $targetUser->id) {
            return response()->json(['success' => false, 'message' => 'You cannot like yourself'], 400);
        }

        // Check if like already exists
        $existingLike = Matches::where('user_id', $matcher->id)
            ->where('matched_user_id', $targetUser->id)
            ->first();

        if ($existingLike) {
            return response()->json(['success' => false, 'message' => 'You already liked this user']);
        }

        // Save one-sided like
        Matches::create([
            'user_id'           => $matcher->id,
            'user_name'         => $matcher->username,
            'matched_user_id'   => $targetUser->id,
            'matched_user_name' => $targetUser->username,
        ]);

        // Check if mutual match exists BEFORE sending any notification
        $mutualMatch = Matches::where('user_id', $targetUser->id)
            ->where('matched_user_id', $matcher->id)
            ->first();

        if ($mutualMatch) {
            // Ensure reverse entry exists
            Matches::firstOrCreate([
                'user_id'           => $targetUser->id,
                'user_name'         => $targetUser->username,
                'matched_user_id'   => $matcher->id,
                'matched_user_name' => $matcher->username,
            ]);

            // Notify both users
            if ($targetUser->fcm_token) {
                $this->sendNotification(
                    $targetUser->fcm_token,
                    "It’s a Match! 🎉",
                    "{$matcher->username} liked you back!",
                    $matcher->id
                );
            }

            if ($matcher->fcm_token) {
                $this->sendNotification(
                    $matcher->fcm_token,
                    "It’s a Match! 🎉",
                    "You and {$targetUser->username} are now matched!",
                    $targetUser->id
                );
            }

            return response()->json(['success' => true, 'message' => 'Mutual match created!']);
        }

        // If not mutual, send one-sided notification only
        if ($targetUser->fcm_token) {
            $this->sendNotification(
                $targetUser->fcm_token,
                "Someone liked you! ❤️",
                "{$matcher->username} liked you!",
                $matcher->id
            );
        }

        return response()->json(['success' => true, 'message' => 'Like saved, waiting for mutual match']);
    }

    private function sendNotification($deviceToken, $title, $body, $matchedUserId)
    {
        Log::info("FCM token: $deviceToken, title: $title, body: $body");

        try {
            $messaging = Firebase::messaging();
            $message = CloudMessage::fromArray([
                'token' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body'  => $body,
                ],
                'data' => [
                    'type'    => 'match',
                    'user_id' => (string)$matchedUserId,
                ],
            ]);

            Log::info("Sending match notification for user_id: " . $matchedUserId);
            $messaging->send($message);

            return true;
        } catch (Exception $e) {
            Log::error('FCM error: ' . $e->getMessage());
            return false;
        }
    }
}
