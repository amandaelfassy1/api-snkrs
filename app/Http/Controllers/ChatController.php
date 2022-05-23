<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\PrivateMessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function fetchMessages()
    {
        return Message::with('user')->get();
    }

    public function privateMessages($user)
    {
        $privateCommunication = Message::with('user')
            ->where(['user_id' => auth()->id(), 'receiver_id' => $user->id])
            ->orWhere(function ($query) use ($user) {
                $query->where(['user_id' => $user->id, 'receiver_id' => auth()->id()]);
            })
            ->get();

        return $privateCommunication;
    }

    public function sendPrivateMessage(Request $request, User $user)
    {

        $input = $request->all();
        $input['receiver_id'] = $user->id;
        $message = auth()->user()->messages()->create($input);

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();

        return response(['status' => 'Message private sent successfully', 'message' => $message]);
    }
}
