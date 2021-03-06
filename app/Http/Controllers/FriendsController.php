<?php

namespace App\Http\Controllers;

use App\Events\FriendAdded;
use App\Http\Requests\UserAddedRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Multicaret\Acquaintances\Models\Friendship;

class FriendsController extends Controller
{

    public function index()
    {
        $medias = auth()->user()
            ->load('media')
            ->media;

        $chatrooms = auth()->user()
            ->getChatrooms();

        $requestFriends = auth()->user()
            ->getFriendRequests()
            ->map(function ($user) {
                return User::find($user->sender_id);
            });

        $requestCanals = auth()->user()
            ->getRequestedCanals();

        $friends = \auth()->user()
            ->getFriends()
            ->load('media', 'type');


        return view('app.friends.index',
            compact(
                'friends',
                'chatrooms',
                'requestFriends',
                'requestCanals',
                'medias'
            ));
    }

    public function add()
    {
        $friendToAdd = User::where('uuid', '=', \request('uuid'))
            ->first();

        auth()->user()->befriend($friendToAdd);

        broadcast(new FriendAdded($friendToAdd));

        return redirect()->route('homepage');
    }

    public function addHashtag(UserAddedRequest $request)
    {
        $data = $request->validated();
        $pseudo = Str::before($data['pseudo'],'#');
        $hashtag = Str::after($data['pseudo'],'#');

        $friendToAdd = User::where('pseudo', '=', $pseudo)
            ->where('hashtag', '=', $hashtag)
            ->first();

        auth()->user()->befriend($friendToAdd);

        broadcast(new FriendAdded($friendToAdd));

        return redirect()->route('homepage');
    }

    public function rename()
    {

        return redirect()->route('homepage');
    }

    public function accept()
    {
        $user = User::where('uuid', '=', \request('uuid'))
            ->first();

        auth()->user()->acceptFriendRequest($user);

        return redirect()->route('friends.index');
    }

    public function deny()
    {
        $user = User::where('uuid', '=', \request('uuid'))
            ->first();

        auth()->user()->denyFriendRequest($user);

        return redirect()->route('friends.index');
    }

    public function block()
    {
        $friend = User::where('uuid', '=', \request('uuid'))->first();

        auth()->user()->blockFriend($friend);

        return redirect()->route('friends.index');
    }

    public function unblock()
    {
        $friend = User::where('uuid', '=', \request('uuid'))->first();

        auth()->user()->unblockFriend($friend);

        return redirect()->route('friends.index');
    }

    public function delete()
    {
        $friend = User::where('uuid', '=', \request('uuid'))->first();

        auth()->user()->unfriend($friend);

        return redirect()->route('friends.index');
    }
}
