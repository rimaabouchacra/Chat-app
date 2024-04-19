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
    // public function index()
    // {
    //     // Retrieve all chats where the authenticated user is either the sender or receiver
    //     $chats = Chat::where('sender_id', auth()->id())
    //                  ->orWhere('receiver_id', auth()->id())
    //                  ->get();

    //     return response()->json($chats);
    // }

    public function index()
    {
        // Retrieve all chats where the authenticated user is either the sender or receiver
        $chats = Chat::with(['sender:id,name', 'receiver:id,name'])
                     ->where('sender_id', auth()->id())
                     ->orWhere('receiver_id', auth()->id())
                     ->get();

        // Transform chat objects to include sender and receiver IDs and names
        $formattedChats = $chats->map(function ($chat) {
            return [
                'id' => $chat->id,
                'sender_id' => $chat->sender->id,
                'sender_name' => $chat->sender->name,
                'receiver_id' => $chat->receiver->id,
                'receiver_name' => $chat->receiver->name,
                'created_at' => $chat->created_at,
                'updated_at' => $chat->updated_at,
            ];
        });

        return response()->json($formattedChats);
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

