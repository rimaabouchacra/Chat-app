<?php

namespace App\Http\Controllers;
use App\Models\User;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class MessageController extends Controller
{
    /**
     * Retrieve all messages.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMessages()
    {
      $messages = DB::table('messages')
          ->select('messages.*', 'users.name as sender_name')
          ->join('chats', 'messages.chat_id', '=', 'chats.id')
          ->join('users', 'messages.sender_id', '=', 'users.id')
          ->get();

      return response()->json($messages);
    }

   public function getMessagesChat($chat_id)
   {
      try {
          // Retrieve the Chat instance based on the chat_id
          $chat = Chat::findOrFail($chat_id);

          // Use the retrieved Chat instance to fetch messages for the specified chat
          $messages = Message::select('messages.*', 'users.name as sender_name')
              ->join('users', 'messages.sender_id', '=', 'users.id')
              ->where('messages.chat_id', $chat->id)
              ->get();

          return response()->json($messages);
      } catch (ModelNotFoundException $exception) {
          return response()->json(['error' => 'Chat not found'], 404);
      }
    }

    /**
     * Create a new message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */


public function sendMessage(Request $request)
{
    $request->validate([
        'chat_id' => 'required|exists:chats,id',
        'message' => 'required|string',
    ]);

    // Get the authenticated user as the sender
    $sender_id = auth()->id();

    // Retrieve the chat instance
    $chat = Chat::findOrFail($request->chat_id);

    // Determine the receiver ID based on the chat's participants
    $receiver_id = ($sender_id === $chat->sender_id) ? $chat->receiver_id : $chat->sender_id;

    // Update the chat table to ensure the authenticated user is the sender
    $chat->update([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
    ]);

    // Retrieve sender and receiver names
    $sender_name = User::findOrFail($sender_id)->name;
    $receiver_name = User::findOrFail($receiver_id)->name;

    // Create the message
    $message = Message::create([
        'chat_id' => $request->chat_id,
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $request->message,
    ]);

    // Return the response with sender and receiver names
    return response()->json([
        'sender_id' => $sender_id,
        'sender_name' => $sender_name,
        'receiver_id' => $receiver_id,
        'receiver_name' => $receiver_name,
        'message' => $request->message,
    ], 201);
}


    /**
     * Retrieve a specific message.
     *
     * @param  \App\Models\Message  $message
     * @return \Illuminate\Http\JsonResponse
     */

    public function showMessage(Message $message)
    {
        return response()->json($message);
    }
}
