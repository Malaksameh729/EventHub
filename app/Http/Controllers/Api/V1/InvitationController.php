<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Speaker;
use App\Models\Sponsor;
use App\Models\Invitation;

class InvitationController extends Controller
{
    public function inviteSpeaker(Request $request, Event $event)
    {
        $invitation = Invitation::create([
            'event_id' => $event->id,
            'speaker_id' => $request->speaker_id,
            'type' => 'speaker',
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Speaker invited successfully', 'invitation' => $invitation]);
    }


    public function inviteSponsor(Request $request, Event $event)
    {
        $invitation = Invitation::create([
            'event_id' => $event->id,
            'sponsor_id' => $request->sponsor_id,
            'type' => 'sponsor',
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Sponsor invited successfully', 'invitation' => $invitation]);
    }
}
