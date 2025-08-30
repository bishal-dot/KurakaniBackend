<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
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


    // Early return if no chat users
    if ($chatUserIds->isEmpty()) {
        return response()->json([]);
    }

    // Fetch chat users
    $chatUsers = User::whereIn('id', $chatUserIds)->get();

    // Attach unread message count for each chat user
    $chatUsers = $chatUsers->map(function ($chatUser) use ($user) {
        $unreadCount = Message::where('sender_id', $chatUser->id)
            ->where('receiver_id', $user->id)
            ->where('is_read', false)
            ->count();

        $chatUser->unread_count = $unreadCount; // dynamically add property
        return $chatUser;
    });

    return response()->json($chatUsers);
}


   public function searchUsers(Request $request)
{
    $currentUser = $request->user();
    if (!$currentUser) {
        return response()->json([], 401);
    }

    $search = trim($request->query('search'));

    $query = User::with('photos')->where('id', '!=', $currentUser->id);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('fullname', 'like', "%$search%")
              ->orWhere('username', 'like', "%$search%");
        });
    }

    $users = $query->get()->map(function($user) {
        $user->unread_count = 0; // replace with actual unread count if needed
        return $user;
    });

    return response()->json($users);
}
  
}
