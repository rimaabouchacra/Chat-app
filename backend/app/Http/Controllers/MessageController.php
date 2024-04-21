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
          $chat = Chat::findOrFail($chat_id);

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

    $sender_id = auth()->id();
    $chat = Chat::findOrFail($request->chat_id);
    $receiver_id = ($sender_id === $chat->sender_id) ? $chat->receiver_id : $chat->sender_id;

    $chat->update([
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
    ]);

    $sender_name = User::findOrFail($sender_id)->name;
    $receiver_name = User::findOrFail($receiver_id)->name;

    $message = Message::create([
        'chat_id' => $request->chat_id,
        'sender_id' => $sender_id,
        'receiver_id' => $receiver_id,
        'message' => $request->message,
    ]);

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
