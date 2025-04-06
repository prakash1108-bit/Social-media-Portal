<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use App\Notifications\FriendRequestReceived;
use Illuminate\Http\Request;

class FriendController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->user()->id);

        $friends = FriendRequest::where(function ($query) use ($user) {
            $query->where('receiver_id', $user->id)
                ->orWhere('sender_id', $user->id);
        })
            ->with('sender', 'receiver')
            ->where('status', 'accepted')
            ->get();
        return view('friends.index', compact('friends'));
    }
    public function friendRequests()
    {
        $requests = FriendRequest::where('receiver_id', auth()->id())
            ->where('status', 'pending')
            ->with('sender')
            ->latest()
            ->get();

        return view('friends.requests', compact('requests'));
    }
    public function search(Request $request)
    {
        $query = $request->input('query');

        $users = User::where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                    ->orWhere('email', 'like', "%$query%");
            })
            ->get();

        return view('friends.search', compact('users'));
    }


    public function sendFriendRequest(User $receiver)
    {
        $sender = auth()->user();

        if ($sender->id === $receiver->id) {
            return back()->with('error', 'You cannot send a friend request to yourself.');
        }

        $exists = FriendRequest::where(function ($q) use ($sender, $receiver) {
            $q->where('sender_id', $sender->id)
                ->where('receiver_id', $receiver->id);
        })->orWhere(function ($q) use ($sender, $receiver) {
            $q->where('sender_id', $receiver->id)
                ->where('receiver_id', $sender->id);
        })->first();

        if ($exists) {
            return back()->with('error', 'Friend request already sent or exists.');
        }

        FriendRequest::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        $receiver->notify(new FriendRequestReceived($sender));
       
        return back()->with('success', 'Friend request sent!');
    }

    public function acceptFriendRequest(FriendRequest $request)
    {
        if ($request->receiver_id !== auth()->id()) {
            abort(403);
        }

        $request->update(['status' => 'accepted']);

        return back()->with('success', 'Friend request accepted.');
    }

    public function rejectFriendRequest(FriendRequest $request)
    {
        if ($request->receiver_id !== auth()->id()) {
            abort(403);
        }

        $request->update(['status' => 'rejected']);

        return back()->with('success', 'Friend request rejected.');
    }
    public function remove(User $user)
    {
        $authId = auth()->id();
        FriendRequest::where(function ($q) use ($authId, $user) {
            $q->where('sender_id', $authId)
                ->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($authId, $user) {
            $q->where('sender_id', $user->id)
                ->where('receiver_id', $authId);
        })->delete();

        return back()->with('success', 'Friend removed.');
    }
}
