<?php

use App\Http\Controllers\MessageController;
use App\Models\Message;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    $users = User::query()
        ->whereKeyNot($user->id)
        ->orderBy('name')
        ->get(['id', 'name', 'email']);

    $incomingMessages = Message::query()
        ->with('sender:id,name')
        ->where('recipient_id', $user->id)
        ->orderBy('created_at')
        ->get();

    return view('dashboard', [
        'users' => $users,
        'incomingMessages' => $incomingMessages,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
