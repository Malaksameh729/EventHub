<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\EventController;
use App\Http\Controllers\Api\V1\ParticipantController;
use App\Http\Controllers\Api\V1\SpeakerController;
use App\Http\Controllers\Api\V1\SponsorController;
use App\Http\Controllers\Api\V1\TicketController;
use App\Http\Controllers\Api\V1\SessionController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\InvitationController;
use App\Http\Controllers\Api\V1\AdminController;



Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{event}', [EventController::class, 'show']);
    Route::get('/categories', [EventController::class, 'categories']);
    Route::get('/categories/{category}/events', [EventController::class, 'eventsByCategory']);
    Route::get('/speakers', [SpeakerController::class, 'index']);
    Route::get('/speakers/{speaker}', [SpeakerController::class, 'show']);
    Route::get('/events/{event}/sessions', [SessionController::class, 'index']);
    Route::get('/events/{event}/tickets', [TicketController::class, 'index']);
    Route::get('/events/{event}/reviews', [EventController::class, 'reviews']);


    Route::middleware(['auth:sanctum'])->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });

        Route::middleware(['role:admin'])->group(function () {
            Route::apiResource('users', UserController::class);
            Route::get('/admin/dashboard', [AdminController::class, 'dashboardAnalytics']);
        });

        Route::middleware(['role:organizer'])->prefix('organizer')->group(function () {

            Route::apiResource('events', EventController::class)->except(['index', 'show']);
            Route::get('/my-events', [EventController::class, 'myEvents']);
            Route::get('/events/{event}/analytics', [EventController::class, 'analytics']);
            Route::patch('/events/{event}/status', [EventController::class, 'updateStatus']);
            Route::post('/events/{event}/image', [EventController::class, 'uploadImage']);

            Route::get('/events/{event}/participants', [ParticipantController::class, 'eventParticipants']);
            Route::delete('/events/{event}/participants/{participant}', [ParticipantController::class, 'removeParticipant']);
            Route::get('/events/{event}/participants/export', [ParticipantController::class, 'exportParticipants']);

            Route::post('/events/{event}/sessions', [SessionController::class, 'store']);
            Route::put('/sessions/{session}', [SessionController::class, 'update']);
            Route::delete('/sessions/{session}', [SessionController::class, 'destroy']);

            Route::post('/events/{event}/tickets', [TicketController::class, 'store']);
            Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
            Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);

            Route::post('/events/{event}/invite-speaker', [InvitationController::class, 'inviteSpeaker']);
            Route::post('/events/{event}/invite-sponsor', [InvitationController::class, 'inviteSponsor']);

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
            Route::post('/events/{event}/favorite', [FavoriteController::class, 'add']);
            Route::delete('/events/{event}/favorite', [FavoriteController::class, 'remove']);
            Route::get('/my-favorites', [FavoriteController::class, 'myFavorites']);
            Route::post('/events/{event}/checkin', [ParticipantController::class, 'checkin']);
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

        Route::post('/events/{event}/review', [EventController::class, 'addReview']);
        Route::get('/notifications', [UserController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [UserController::class, 'markAsRead']);
    });
});
