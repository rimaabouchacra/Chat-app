<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all chats for the authenticated user.
     *
     * @return \Illuminate\Http\Response
     */

public function getChats()
{
    $userId = auth()->id();

    $chats = Chat::with(['sender:id,name', 'receiver:id,name'])
                 ->where(function ($query) use ($userId) {
                     $query->where('sender_id', $userId)
                           ->orWhere('receiver_id', $userId);
                 })
                 ->get();

    $chatsWithAllMessages = $chats->map(function ($chat) {
        $messages = $chat->messages()->orderBy('created_at', 'asc')->pluck('message');
        return [
            'id' => $chat->id,
            'sender_id' => $chat->sender->id,
            'sender_name' => $chat->sender->name,
            'receiver_id' => $chat->receiver->id,
            'receiver_name' => $chat->receiver->name,
            'messages' => $messages,
            'created_at' => $chat->created_at,
            'updated_at' => $chat->updated_at,
        ];
    });

    return response()->json($chatsWithAllMessages);
}


public function getChatsLastMessage()
{
    $userId = auth()->id();

    $chats = Chat::with(['sender:id,name', 'receiver:id,name'])
                 ->where(function ($query) use ($userId) {
                     $query->where('sender_id', $userId)
                           ->orWhere('receiver_id', $userId);
                 })
                 ->get();

    $chatsWithLastMessage = $chats->map(function ($chat) use ($userId) {
        $lastMessage = $chat->messages()->latest()->first();

        if ($chat->sender_id == $userId) {
            $participant = $chat->receiver;
        } else {
            $participant = $chat->sender;
        }

        $messageCount = $chat->messages()->where('receiver_id', $userId)->count();

        if ($lastMessage) {
            return [
                'id' => $chat->id,
                'sender_id' => $participant->id,
                'sender_name' => $participant->name,
                'receiver_id' => $chat->receiver->id,
                'receiver_name' => $chat->receiver->name,
                'last_message' => $lastMessage->message,
                'message_count' => $messageCount,
                'created_at' => $chat->created_at,
                'updated_at' => $chat->updated_at,
            ];
        } else {
            return [
                'id' => $chat->id,
                'sender_id' => $participant->id,
                'sender_name' => $participant->name,
                'receiver_id' => $chat->receiver->id,
                'receiver_name' => $chat->receiver->name,
                'last_message' => null,
                'message_count' => $messageCount,
                'created_at' => $chat->created_at,
                'updated_at' => $chat->updated_at,
            ];
        }
    });

    return response()->json($chatsWithLastMessage);
}


    /**
     * Store a new chat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);
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
        if ($chat->sender_id !== auth()->id() && $chat->receiver_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($chat);
    }


}


