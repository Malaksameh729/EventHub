<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\Event;

class SponsorController extends Controller
{
    public function myProfile(Request $request)
    {
        $sponsor = $request->user()->sponsor; // افتراض علاقة User -> sponsor
        return response()->json($sponsor);
    }


    public function updateProfile(Request $request)
    {
        $sponsor = $request->user()->sponsor;

        $sponsor->update($request->only(['name', 'company', 'logo']));

        return response()->json(['message' => 'Profile updated', 'sponsor' => $sponsor]);
    }


    public function sponsoredEvents(Request $request)
    {
        $sponsor = $request->user()->sponsor;
        return response()->json($sponsor->events);
    }


    public function requestSponsorship(Request $request, Event $event)
    {
        $sponsor = $request->user()->sponsor;

        $event->sponsors()->attach($sponsor->id, ['status' => 'pending']);

        return response()->json(['message' => 'Sponsorship request sent']);
    }


    public function attachToEvent(Request $request, Event $event)
    {
        $sponsorId = $request->sponsor_id;
        $event->sponsors()->attach($sponsorId);

        return response()->json(['message' => 'Sponsor attached to event']);
    }


    public function detachFromEvent(Event $event, Sponsor $sponsor)
    {
        $event->sponsors()->detach($sponsor->id);

        return response()->json(['message' => 'Sponsor detached from event']);
    }
}
