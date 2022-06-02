<?php

namespace App\Http\Controllers;

use App\Models\Followers;
use App\Models\User;
use Illuminate\Http\Request;

class FollowersController extends Controller
{

    public function UpdateFollowers(Request $request, $id)
    {
        $followers = Followers::where('user_id', auth()->user()->id)->where('followers_id', $id)->first();
        $subscribe = false;
        if ($followers) {
            $followers->delete();
        } else {
            Followers::insert([
                'user_id' => auth()->user()->id,
                'followers_id' => $id,
            ]);
            $subscribe = true;
        }

        $count = Followers::where('followers_id', $id)->count();
        return response()->json([
            "nbFollowers" => $count,
            "subscribe" => $subscribe
        ], 201);
    }
}
