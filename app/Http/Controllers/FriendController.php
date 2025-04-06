<?php

namespace App\Http\Controllers;

use App\Models\FriendRequest;
use App\Models\User;
use App\Notifications\FriendRequestReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        try {
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

            DB::beginTransaction();
            FriendRequest::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'status' => 'pending',
            ]);
            DB::commit();

            $receiver->notify(new FriendRequestReceived($sender));

            return back()->with('success', 'Friend request sent!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function acceptFriendRequest(FriendRequest $request)
    {
        if ($request->receiver_id !== auth()->id()) {
            abort(403);
        }
        try {
            DB::beginTransaction();
            $request->update(['status' => 'accepted']);
            DB::commit();

            return back()->with('success', 'Friend request accepted.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function rejectFriendRequest(FriendRequest $request)
    {
        if ($request->receiver_id !== auth()->id()) {
            abort(403);
        }
        try {
            DB::beginTransaction();
            $request->update(['status' => 'rejected']);
            DB::commit();
            
            return back()->with('success', 'Friend request rejected.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong.');
        }

    }
    public function remove(User $user)
    {
        try {
            $authId = auth()->id();

            DB::beginTransaction();
            FriendRequest::where(function ($q) use ($authId, $user) {
                $q->where('sender_id', $authId)
                    ->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($authId, $user) {
                $q->where('sender_id', $user->id)
                    ->where('receiver_id', $authId);
            })->delete();
            DB::commit();

            return back()->with('success', 'Friend removed.');
        } catch (\Throwable $th) {
            return back()->with('error', 'Something went wrong.');
        }
    }
}
