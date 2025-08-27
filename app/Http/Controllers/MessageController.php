<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // 1️⃣ Send a message (with authentication)
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user(); // current logged-in user

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json($message, 201);
    }

    // 2️⃣ Fetch messages between current user and another user
    public function getMessages($userId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $messages = Message::where(function($q) use ($userId, $user) {
            $q->where('sender_id', $user->id)
              ->where('receiver_id', $userId);
        })->orWhere(function($q) use ($userId, $user) {
            $q->where('sender_id', $userId)
              ->where('receiver_id', $user->id);
        })->orderBy('created_at')
          ->get();

        return response()->json($messages);
    }

    // 3️⃣ Get users the current user has chatted with
    public function getChatUsers()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $chatUserIds = Message::where('sender_id', $user->id)
            ->pluck('receiver_id')
            ->merge(Message::where('receiver_id', $user->id)->pluck('sender_id'))
            ->unique();

        $chatUsers = User::whereIn('id', $chatUserIds)->get();

        return response()->json($chatUsers);
    }
}
