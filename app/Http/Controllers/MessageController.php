<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
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

}
