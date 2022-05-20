<?php

namespace App\Http\Controllers;

use App\Models\Followers;
use App\Models\User;
use Illuminate\Http\Request;

class FollowersController extends Controller
{
    public function followers(Request $request, $id)
    {
        $data = $request->json()->all();
        if (env('APP_ENV') == 'testing') {
            $user_id = $data['user_id'];
        } else {
            $user_id = auth()->user()->id;
        }

        Followers::insert([
            'user_id' => $user_id,
            'followers_id' => $id,
        ]);

        return response()->json([
            'user_id' => $user_id,
            'followers_id' => $id,
        ], 201);
    }


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
            "nbfollowers" => $count,
            "subscribe" => $subscribe
        ], 201);
    }





    public function getFollowers($id)
    {
        $followers = Followers::where('user_id', $id)->pluck('followers_id')->toArray();
        return response()->json([
            "followers" => $followers
        ], 200);
    }
    public function countFollowers($id)
    {
        $followers = Followers::where('followers_id', $id)->count();
        return response()->json([
            "followers" => $followers
        ], 200);
    }
    public function countFollowing($id)
    {
        $followers = Followers::where('user_id', $id)->count();
        return response()->json([
            "following" => $followers
        ], 200);
    }
}
