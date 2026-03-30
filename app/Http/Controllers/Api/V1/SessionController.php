<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Session;

class SessionController extends Controller
{
    public function index(Event $event)
    {
        return $event->sessions;
    }


    public function store(Request $request, Event $event)
    {
        $session = $event->sessions()->create($request->only(['title', 'description', 'start_time', 'end_time']));
        return response()->json(['message' => 'Session created', 'session' => $session]);
    }


    public function update(Request $request, Session $session)
    {
        $session->update($request->only(['title', 'description', 'start_time', 'end_time']));
        return response()->json(['message' => 'Session updated', 'session' => $session]);
    }


    public function destroy(Session $session)
    {
        $session->delete();
        return response()->json(['message' => 'Session deleted']);
    }
}
