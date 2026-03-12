<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_id' => ['required', 'integer', 'exists:users,id', 'different:'.auth()->id()],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::query()->create([
            'sender_id' => $request->user()->id,
            'recipient_id' => $data['recipient_id'],
            'body' => $data['body'],
        ]);

        $message->load('sender');

        broadcast(new MessageSent($message));

        return response()->json([
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'recipient_id' => $message->recipient_id,
                'body' => $message->body,
                'created_at' => $message->created_at->toDateTimeString(),
                'sender_name' => $message->sender->name,
            ],
        ], 201);
    }
}
