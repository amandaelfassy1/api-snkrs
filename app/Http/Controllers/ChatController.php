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

    public function sendPrivateMessage(Request $request)
    {

        $input = $request->all();
        // $input['receiver_id'] = auth()->user()->id;
        $message = auth()->user()->messages()->create($input);

        broadcast(new PrivateMessageSent($message->load('user')))->toOthers();

        return response(
            ['status' => 'Message private sent successfully', 'message' => $message]

        );
    }
    public function allDiscussion(Request $request, $id)
    {
        $count = Message::where('receiver_id', $id)->count();
        return response()->json($count, 200);
    }
}
