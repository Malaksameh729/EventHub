<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;

class ParticipantController extends Controller
{

    public function joinEvent(Request $request, Event $event)
    {
        $user = $request->user();
        if (!$event->participants()->where('user_id', $user->id)->exists()) {
            $event->participants()->attach($user->id);
        }
        return response()->json(['message' => 'Joined event successfully']);
    }


    public function leaveEvent(Request $request, Event $event)
    {
        $user = $request->user();
        $event->participants()->detach($user->id);
        return response()->json(['message' => 'Left event successfully']);
    }


    public function myEvents(Request $request)
    {
        $user = $request->user();
        return response()->json($user->participatedEvents);
    }


    public function myTickets(Request $request)
    {
        $user = $request->user();
        return response()->json($user->tickets);
    }


    public function add(Request $request, Event $event)
    {
        $user = $request->user();
        if (!$user->favoriteEvents()->where('event_id', $event->id)->exists()) {
            $user->favoriteEvents()->attach($event->id);
        }
        return response()->json(['message' => 'Added to favorites']);
    }


    public function remove(Request $request, Event $event)
    {
        $request->user()->favoriteEvents()->detach($event->id);
        return response()->json(['message' => 'Removed from favorites']);
    }


    public function myFavorites(Request $request)
    {
        return response()->json($request->user()->favoriteEvents);
    }


    public function checkin(Request $request, Event $event)
    {
        $user = $request->user();
        if (!$event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Not joined this event'], 400);
        }
        $event->participants()->updateExistingPivot($user->id, ['checked_in' => true]);
        return response()->json(['message' => 'Checked in successfully']);
    }


    public function eventParticipants(Event $event)
    {
        return response()->json($event->participants);
    }


    public function removeParticipant(Event $event, User $participant)
    {
        $event->participants()->detach($participant->id);
        return response()->json(['message' => 'Participant removed successfully']);
    }


    public function exportParticipants(Event $event)
    {
        $participants = $event->participants;
        $data = $participants->map(function($p){
            return [
                'id' => $p->id,
                'name' => $p->name,
                'email' => $p->email
            ];
        });
        return response()->json(['participants' => $data]);
    }
}
