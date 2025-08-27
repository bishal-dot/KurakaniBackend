<?php

namespace App\Http\Controllers;
<<<<<<< HEAD
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
=======

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
>>>>>>> origin/hehe
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
<<<<<<< HEAD
     // GET /api/messages/{otherUserId}
    public function getMessages(Request $request, $otherUserId)
    {
        $currentUserId = (int) $request->header('X-User-Id', 0);
        if (!$currentUserId) {
            return response()->json(['error' => 'X-User-Id header missing'], 400);
        }

        $messages = Message::where(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('sender_id', $currentUserId)
                  ->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($currentUserId, $otherUserId) {
                $q->where('sender_id', $otherUserId)
                  ->where('receiver_id', $currentUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // POST /api/messages/{otherUserId}  (form field: message)
    public function sendMessage(Request $request, $otherUserId)
    {
        $currentUserId = (int) $request->header('X-User-Id', 0);
        if (!$currentUserId) {
            return response()->json(['error' => 'X-User-Id header missing'], 400);
        }

        $request->validate(['message' => 'required|string']);

        $msg = Message::create([
            'sender_id'   => $currentUserId,
            'receiver_id' => (int) $otherUserId,
            'message'     => $request->message,
            'user_id'     => $currentUserId
        ]);

        return response()->json($msg, 201);
    }

=======
    // 1️⃣ Send a message (with authentication)
    public function send(Request $request)
    {
        Log::info($request);
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        /** @var User $user */
        $user = Auth::user(); // current logged-in user

        // Create message with is_read defaulting to false
        $message = $user->sentMessages()->create([
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_read' => false,
        ]);

        return response()->json($message, 201);
    }

    // 2️⃣ Fetch messages between current user and another user
        public function getMessages($userId)
        {
            // ✅ Ensure $userId is numeric
            if (!is_numeric($userId)) {
                return response()->json(['error' => 'Invalid user ID'], 400);
            }

            $userId = (int) $userId;

            /** @var User $user */
            $user = Auth::user();

            // Fetch messages between the two users
            $messages = Message::where(function($q) use ($user, $userId) {
                $q->where('sender_id', $user->id)
                ->where('receiver_id', $userId);
            })->orWhere(function($q) use ($user, $userId) {
                $q->where('sender_id', $userId)
                ->where('receiver_id', $user->id);
            })->orderBy('created_at')
            ->get();

            // Mark messages as read where the current user is the receiver
            Message::where('sender_id', $userId)
                ->where('receiver_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json($messages);
        }


    // 3️⃣ Get users the current user has chatted with
   public function getChatUsers()
{
    /** @var User $user */
    $user = Auth::user();

    // Fetch all receiver IDs from sent messages and sender IDs from received messages
    $chatUserIds = $user->sentMessages()->pluck('receiver_id')
        ->merge($user->receivedMessages()->pluck('sender_id'))
        ->unique()
        ->values(); // reindex

    // 🔹 Log the chat user IDs for debugging
    Log::info('Chat user IDs: ', $chatUserIds->toArray());

    // Early return if no chat users
    if ($chatUserIds->isEmpty()) {
        Log::info('No chat users found for user ID: ' . $user->id);
        return response()->json([]);
    }

    // Fetch chat users
    $chatUsers = User::whereIn('id', $chatUserIds)->get();

    // 🔹 Log the users fetched
    Log::info('Chat users fetched: ', $chatUsers->pluck('id', 'username')->toArray());

    // Attach unread message count for each chat user
    $chatUsers = $chatUsers->map(function ($chatUser) use ($user) {
        $unreadCount = Message::where('sender_id', $chatUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        $chatUser->unread_count = $unreadCount; // dynamically add property
        Log::info("Unread count for user {$chatUser->id}: {$unreadCount}");
        return $chatUser;
    });

    return response()->json($chatUsers);
}



    // 4️⃣ Search users by name or username for dropdown (excluding current user)
    public function searchUsers(Request $request)
    {   

        // Get the currently logged-in user
        $currentUser = $request->user();
        if ($currentUser) {
        } else {
            return response()->json([], 401); // return empty array on unauthorized
        }

        $search = trim($request->query('search')); // query parameter from Retrofit

        // Query users excluding the logged-in user
        $query = User::with('photos')->where('id', '!=', $currentUser->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%");
            });
        }

        $users = $query->get();

        // Return as plain array for Retrofit
        return response()->json($users);
    }
>>>>>>> origin/hehe
}
