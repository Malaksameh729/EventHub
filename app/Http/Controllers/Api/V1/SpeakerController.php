<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Speaker;
use App\Models\Event;
use App\Models\Invitation;

class SpeakerController extends Controller
{
    public function index()
    {
        return response()->json(Speaker::all());
    }


    public function show(Speaker $speaker)
    {
        return response()->json($speaker);
    }


    public function myProfile(Request $request)
    {
        $speaker = $request->user();
        return response()->json($speaker);
    }


    public function updateProfile(Request $request)
    {
        $speaker = $request->user();
        $speaker->update($request->only(['name','email','bio','photo']));
        return response()->json(['message' => 'Profile updated successfully', 'speaker' => $speaker]);
    }


    public function myEvents(Request $request)
    {
        $speaker = $request->user();
        return response()->json($speaker->events);
    }


    public function invitations(Request $request)
    {
        $speaker = $request->user();
        return response()->json($speaker->invitations);
    }


    public function acceptInvitation(Request $request, Invitation $invitation)
    {
        $speaker = $request->user();
        if ($invitation->speaker_id != $speaker->id) {
            return response()->json(['message' => 'Not your invitation'], 403);
        }
        $invitation->status = 'accepted';
        $invitation->save();
        return response()->json(['message' => 'Invitation accepted']);
    }


    public function rejectInvitation(Request $request, Invitation $invitation)
    {
        $speaker = $request->user();
        if ($invitation->speaker_id != $speaker->id) {
            return response()->json(['message' => 'Not your invitation'], 403);
        }
        $invitation->status = 'rejected';
        $invitation->save();
        return response()->json(['message' => 'Invitation rejected']);
    }


    public function attachToEvent(Request $request, Event $event)
    {
        $speaker = $request->user();
        if (!$event->speakers()->where('speaker_id', $speaker->id)->exists()) {
            $event->speakers()->attach($speaker->id);
        }
        return response()->json(['message' => 'Speaker attached to event']);
    }


    public function detachFromEvent(Event $event, Speaker $speaker)
    {
        $event->speakers()->detach($speaker->id);
        return response()->json(['message' => 'Speaker detached from event']);
    }
}
