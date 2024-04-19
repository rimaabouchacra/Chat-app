<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all chats for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all chats where the authenticated user is either the sender or receiver
        $chats = Chat::where('sender_id', auth()->id())
                     ->orWhere('receiver_id', auth()->id())
                     ->get();

        return response()->json($chats);
    }

    /**
     * Store a new chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);

        // Create a new chat
        $chat = new Chat();
        $chat->sender_id = auth()->id();
        $chat->receiver_id = $request->receiver_id;
        $chat->save();

        return response()->json($chat, 201);
    }

    /**
     * Get a specific chat by its ID.
     *
     * @param  \App\Models\Chat  $chat
     * @return \Illuminate\Http\Response
     */
    public function show(Chat $chat)
    {
        // Check if the authenticated user is a participant of the chat
        if ($chat->sender_id !== auth()->id() && $chat->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Return the chat details
        return response()->json($chat);
    }
}

