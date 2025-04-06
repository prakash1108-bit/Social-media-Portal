<?php

use App\Http\Controllers\FriendController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Friends routes
    Route::prefix('friends')->group(function () {
        Route::get('/search', [FriendController::class, 'search'])->name('friends.search');
        Route::get('/', [FriendController::class, 'index'])->name('friends.index');
        Route::delete('/{user}', [FriendController::class, 'remove'])->name('friends.remove');
    });

    // Friend requests routes
    Route::prefix('requests')->group(function () {
        Route::get('/', [FriendController::class, 'friendRequests'])->name('friends.requests');
        Route::post('/{receiver}/send', [FriendController::class, 'sendFriendRequest'])->name('friend.request.send');
        Route::post('/{request}/accept', [FriendController::class, 'acceptFriendRequest'])->name('friend.request.accept');
        Route::post('/{request}/reject', [FriendController::class, 'rejectFriendRequest'])->name('friend.request.reject');
    });
});

require __DIR__ . '/auth.php';
