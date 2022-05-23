<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class LikesController extends Controller
{
    public function Updatelike(Request $request, $id)
    {
        $like = Like::where('user_id', auth()->user()->id)->where('post_id', $id)->first();

        $isLiked = false;
        if ($like) {
            $like->delete();
        } else {
            Like::insert([
                'user_id' => auth()->user()->id,
                'post_id' => $id,
            ]);
            $isLiked = true;
        }

        $count = Like::where('post_id', $id)->count();
        return response()->json([
            "nblike" => $count,
            "isLiked" => $isLiked
        ], 201);
    }
}
