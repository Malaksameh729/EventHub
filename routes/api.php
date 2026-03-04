<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\ParticipantController;
use App\Http\Controllers\Api\V1\SpeakerController;
use App\Http\Controllers\Api\V1\SponsorController;




Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);

    Route::get('/speakers', [SpeakerController::class, 'index']);
    Route::get('/speakers/{speaker}', [SpeakerController::class, 'show']);
});




Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
    });



    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('users', UserController::class);
    });


    Route::middleware(['role:organizer'])->prefix('organizer')->group(function () {
        Route::apiResource('events', EventController::class)->except(['index', 'show']);
        Route::get('/my-events', [EventController::class, 'myEvents']);
        Route::get('/events/{event}/analytics', [EventController::class, 'analytics']);

        Route::get('/events/{event}/participants', [ParticipantController::class, 'eventParticipants']);
        Route::delete('/events/{event}/participants/{participant}', [ParticipantController::class, 'removeParticipant']);

        Route::post('/events/{event}/speakers', [SpeakerController::class, 'attachToEvent']);
        Route::delete('/events/{event}/speakers/{speaker}', [SpeakerController::class, 'detachFromEvent']);

        Route::post('/events/{event}/sponsors', [SponsorController::class, 'attachToEvent']);
        Route::delete('/events/{event}/sponsors/{sponsor}', [SponsorController::class, 'detachFromEvent']);
    });



    Route::middleware(['role:user'])->prefix('participant')->group(function () {

        Route::post('/events/{event}/join', [ParticipantController::class, 'joinEvent']);
        Route::delete('/events/{event}/leave', [ParticipantController::class, 'leaveEvent']);

        Route::get('/my-events', [ParticipantController::class, 'myEvents']);
        Route::get('/my-tickets', [ParticipantController::class, 'myTickets']);
    });



    Route::middleware(['role:speaker'])->prefix('speaker')->group(function () {
        Route::get('/profile', [SpeakerController::class, 'myProfile']);
        Route::put('/profile', [SpeakerController::class, 'updateProfile']);
        Route::get('/my-events', [SpeakerController::class, 'myEvents']);
        Route::get('/invitations', [SpeakerController::class, 'invitations']);
        Route::post('/invitations/{invitation}/accept', [SpeakerController::class, 'acceptInvitation']);
        Route::post('/invitations/{invitation}/reject', [SpeakerController::class, 'rejectInvitation']);
    });



    Route::middleware(['role:sponsor'])->prefix('sponsor')->group(function () {
        Route::get('/profile', [SponsorController::class, 'myProfile']);
        Route::put('/profile', [SponsorController::class, 'updateProfile']);
        Route::get('/sponsored-events', [SponsorController::class, 'sponsoredEvents']);
        Route::post('/events/{event}/sponsor-request', [SponsorController::class, 'requestSponsorship']);
    });




    Route::get('/search/events', [EventController::class, 'search']);
    Route::get('/categories', [EventController::class, 'categories']);

    Route::post('/events/{event}/review', [EventController::class, 'addReview']);
    Route::get('/events/{event}/reviews', [EventController::class, 'reviews']);

    Route::get('/notifications', [UserController::class, 'notifications']);
    Route::post('/notifications/{id}/read', [UserController::class, 'markAsRead']);
});
