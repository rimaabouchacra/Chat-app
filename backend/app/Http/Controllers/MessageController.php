<?php

namespace App\Http\Controllers;

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


      $chat = Chat::findOrFail($request->chat_id);
      $sender_id = $chat->sender_id;
      $receiver_id = $chat->receiver_id;

      // Create the message
      $message = Message::create([
          'chat_id' => $request->chat_id,
          'sender_id' => $sender_id,
          'receiver_id' => $receiver_id,
          'message' => $request->message,
      ]);

       return response()->json($message, 201);
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