<?php

namespace App\Services;

use App\Models\Post;
use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Builder;

class PostService
{

    public function getPosts()
    {
        return $this->getPostsQuery()
            ->with(['comments', 'comments.user'])
            ->get();
    }

    private function getPostsQuery(Request $request): Builder
    {
        $user_id = $request->user_id;
        return Post::query()
            ->with('user', 'likes', 'followers')
            ->where('user_id', $user_id)->get();
    }
}
