<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserEditRequest;
use App\Models\Comment;
use App\Models\Followers;
use App\Models\Like;
use App\Models\Post;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
        ]);

        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json(["message" => "Tu as déjà un compte. Merci de te connecter."], 409);
        }

        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
        ]);


        $token = $user->createToken("kodiweb")->plainTextToken;

        return response()->json([
            "token" => $token,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "created_at" => $user->created_at,
            "message" => "Tu es maintenant inscrit"
        ], 200);
    }

    public function login(Request $request)
    {

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "message" => "Les identifiants ne sont pas corrects"
            ], 401);
        }

        $user->tokens()->where('tokenable_id',  $user->id)->delete();

        $token = $user->createToken("snkrsweb")->plainTextToken;


        $user = [
            "id" => $user->id,
            "token" => $token,
            "first_name" => $user->first_name,
            "last_name" => $user->last_name,
            "email" => $user->email,
            "created_at" => $user->created_at,
            "biography" => $user->biography,
            "img_url" => $user->img_url,
        ];

        return response()->json([
            'user' => $user,
            "message" => "Tu es maintenant connecté"
        ], 200);
    }



    public function me(Request $request)
    {
        return response()->json([
            "user_id" => $request->user()->id,
            "first_name" => $request->user()->first_name,
            "last_name" => $request->user()->last_name,
            "email" => $request->user()->email,
            "biography" => $request->user()->biography,
            "img_url" => $request->user()->img_url,
            "created_at" => $request->user()->created_at,
            "updated_at" => $request->user()->updated_at,
        ], 200);
    }

    public function userInfoPublic(Request $request, $id)
    {
        $userInfo = User::where('id', $id)->first();
        if (!$userInfo) {
            return response()->json(['message' => 'Not found'], 404);
        }
        $exist = Followers::where('follower_id', $userInfo->id)->where('user_id', auth()->user()->id)->first();
        $nbFollowers = Followers::where('follower_id', $userInfo->id)->count();
        $user = [
            "user_id" => $userInfo->id,
            "first_name" => $userInfo->first_name,
            "last_name" => $userInfo->last_name,
            "biography" => $userInfo->biography,
            "img_url" => $userInfo->img_url,
            "nbPost" => $userInfo->posts()->count(),
            "nbFollowers" => $nbFollowers,
            "nbFollowing" => $userInfo->following()->count(),
            "nbLikes" => $userInfo->likes()->count(),
            "isSusbscribe" => $exist ? true : false
        ];

        return response()->json(
            $user

        );
    }

    public function userInfo(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }
        if ($user->id != $request->user()->id) {
            return response()->json(["message" => 'Forbidden'], 403);
        }
        return response()->json([
            $user

        ], 200);
    }
    public function allUser()
    {
        $users = User::orderBy('created_at', 'desc')->get()->all();
        return ($users);
    }

    public function update($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($user->id != $request->user()->id) {
            return response()->json(["message" => 'Forbidden'], 403);
        }

        // dd($request);
        $request->validate([
            'email' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'biography' => 'required',
        ]);


        $user->email = $request->email;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->biography = $request->biography;

        if ($request->has('img')) {

            $uploadedFileUrl = Cloudinary::upload($request->img->getRealPath())->getSecurePath();
            $user->img_url = $uploadedFileUrl;
        }

        $user->fill($request->all());

        if (strlen($request->get('password', '')) > 0) {
            $user->password = Hash::make($request->get('password'));
        }

        $user->save();


        return response()->json([
            'user' => $user,
            "message" => "Ton profil a été mis à jour"
        ], 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
