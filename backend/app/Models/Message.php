<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'sender_id',
        'receiver_id',
        'message',
    ];

    public function sender()
    {
        return $this->belongsTo(Chat::class, 'sender_id');
    }

    /**
     * Get the receiver of the chat.
     */
    public function receiver()
    {
        return $this->belongsTo(Chat::class, 'receiver_id');
    }
}
