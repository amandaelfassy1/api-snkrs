<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Models\Comment;
use App\Models\Followers;

class PostsController extends Controller
{
    public function showProfilUser(Request $request)
    {
        $user_id = $request->user()->id;
        $post = Post::with("user")
            ->where('user_id', $user_id)->get();
        return response()->json($post, 200);
    }
    public function show(Request $request)
    {
        $user_id = $request->user_id;
        $post = Post::orderBy('created_at', 'desc')->with("user")
            ->where('user_id', $user_id)->get();
        return response()->json($post, 200);
    }

    public function index()
    {
        $user_id = auth()?->user()?->id;
        $posts = Post::orderBy('created_at', 'desc')->get()->all();
        $arrayPosts = [];

        foreach ($posts as $post) {

            $userInfo = User::where('id', $post->user_id)->first();
            $likes = Like::where('post_id', $post->id)->get();
            $comments = Comment::where('post_id', $post->id)->get();
            $countLikes = Like::where('post_id', $post->id)->count();

            $likeExist = Like::where([
                ['post_id', '=', $post->id],
                ['user_id', '=', $user_id]
            ])->exists();

            $followerExist = Followers::where([
                ['follower_id', '=', $post->id],
                ['user_id', '=', $user_id]
            ])->exists();

            $arrayComments = [];
            foreach ($comments as $comment) {
                $commentInfo = Post::where('user_id', $comment->user_id)->first();
                $likesComment = Like::where('comment_id', $comment->id)->get();

                array_push($arrayComments, [
                    "author" => $commentInfo,
                    "comment" => $comment,
                    "likes" => $likesComment,
                ]);
            }

            $post = [
                "author" => $userInfo,
                "posts" => $post,
                "likes" => $likes,
                "comments" => $arrayComments,
            ];

            if ($user_id !== null) {
                $post['likeExist'] = $likeExist;
                $post['nblike'] = $countLikes;
            }


            array_push($arrayPosts, $post);
        }
        return response()->json($arrayPosts, 200);
    }

    public function index_followers()
    {
        $user_id = auth()?->user()?->id;
        $posts = Post::orderBy('created_at', 'desc')->get()->all();
        $arrayPosts = [];

        foreach ($posts as $post) {
            $exist = auth()->user()->following()->where('follower_id', $post->user_id)->exists();
            if ($exist) {

                $userInfo = User::where('id', $post->user_id)->first();
                $likes = Like::where('post_id', $post->id)->get();
                $comments = Comment::where('post_id', $post->id)->get();
                $countLikes = Like::where('post_id', $post->id)->count();

                $likeExist = Like::where([
                    ['post_id', '=', $post->id],
                    ['user_id', '=', $user_id]
                ])->exists();

                $followerExist = Followers::where([
                    ['follower_id', '=', $post->id],
                    ['user_id', '=', $user_id]
                ])->exists();

                $arrayComments = [];
                foreach ($comments as $comment) {
                    $commentInfo = Post::where('user_id', $comment->user_id)->first();
                    $likesComment = Like::where('comment_id', $comment->id)->get();

                    array_push($arrayComments, [
                        "author" => $commentInfo,
                        "comment" => $comment,
                        "likes" => $likesComment,
                    ]);
                }
                $post = [
                    "author" => $userInfo,
                    "posts" => $post,
                    "likes" => $likes,
                    "comments" => $arrayComments,
                ];

                if ($user_id !== null) {
                    $post['likeExist'] = $likeExist;
                    $post['nblike'] = $countLikes;
                }


                array_push($arrayPosts, $post);
            }
        }
        return response()->json($arrayPosts, 200);
    }

    public function create(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
        ]);

        $data = $request->json()->all();
        if (env('APP_ENV') == 'testing') {
            $user_id = $data['user_id'];
        } else {
            $user_id = auth()->user()->id;
        }
        $img_url = "";
        if ($request->has('img')) {

            $uploadedFileUrl = Cloudinary::upload($request->img->getRealPath())->getSecurePath();
            $img_url = $uploadedFileUrl;
        }

        Post::insert([
            'user_id' => $user_id,
            'body' => $request->body,
            'price' => $request->price,
            'img' => $img_url,
        ]);

        return response()->json([
            "post" => $request->body,
            "message" => "Votre post a bien été publié"
        ], 201);
    }

    public function showById($id)
    {
        $post = Post::where('id', $id)->first();
        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($post);
    }

    public function delete(Request $request, $id)
    {
        $user_id = $request->user()->id;

        $post = Post::where('user_id', '=', $user_id)->where('id', '=', $id)->delete();

        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['message' => "Post supprimé"], 200);
    }

    public function buy($id)
    {
        $post = Post::where('id', '=', $id)->delete();

        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json(['message' => "Elle est à vous !"], 200);
    }

    public function allPostUser(Request $request, $id)
    {
        $user_id = $request->user()->id;

        $posts = Post::orderBy('created_at', 'desc')->where('user_id', $user_id)->get()->all();

        $arrayPosts = [];
        foreach ($posts as $post) {
            $userInfo = User::where('id', $post->user_id)->first();
            $likes = Like::where('post_id', $post->id)->get();
            $comments = Comment::where('post_id', $post->id)->get();

            $arrayComments = [];
            foreach ($comments as $comment) {
                $commentInfo = Post::where('user_id', $comment->user_id)->first();
                $likesComment = Like::where('comment_id', $comment->id)->get();

                array_push($arrayComments, [
                    "author" => $commentInfo,
                    "comment" => $comment,
                    "likes" => $likesComment,
                ]);
            }
            array_push($arrayPosts, [
                "author" => $userInfo,
                "posts" => $post,
                "likes" => $likes,
                "comments" => $arrayComments,
            ]);
        }
        return response()->json($arrayPosts, 200);
    }

    public function search($key)
    {
        $posts = Post::where('body', 'LIKE', "%$key%")->orderBy('created_at', 'desc')->get()->all();

        $arrayPosts = [];
        foreach ($posts as $post) {
            $userInfo = User::where('id', $post->user_id)->first();
            $likes = Like::where('post_id', $post->id)->get();
            $comments = Comment::where('post_id', $post->id)->get();

            $arrayComments = [];
            foreach ($comments as $comment) {
                $commentInfo = Post::where('user_id', $comment->user_id)->first();
                $likesComment = Like::where('comment_id', $comment->id)->get();

                array_push($arrayComments, [
                    "author" => $commentInfo,
                    "comment" => $comment,
                    "likes" => $likesComment,
                ]);
            }
            array_push($arrayPosts, [
                "author" => $userInfo,
                "posts" => $post,
                "likes" => $likes,
                "comments" => $arrayComments,
            ]);
        }
        return response()->json($arrayPosts, 200);
    }


    public function updatePost($id, Request $request)
    {
        $post = Post::find($id);
        if (!$post) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $request->validate([
            'body' => 'required'
        ]);

        $post->body = $request->body;
        if ($request->has('img_url')) {
            $uploadedFileUrl = Cloudinary::upload($request->img_url->getRealPath())->getSecurePath();
            $post->img = $uploadedFileUrl;
        }

        $post->fill($request->all());
        $post->save();


        return response()->json([
            'post' => $post,
            "message" => "Ton post a été mis à jour"
        ], 200);
    }
}
