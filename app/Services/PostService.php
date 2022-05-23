<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PostService
{

    public function getPosts()
    {
        return $this->getPostsQuery()
            ->with(['comments', 'comments.user'])
            ->paginate();
    }

    private function getPostsQuery(): Builder
    {
        return Post::query()
            ->with(['user', 'likes'])
            ->orderByDesc('created_at');
    }
}
