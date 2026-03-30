<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Ticket;

class TicketController extends Controller
{
    public function index(Event $event)
    {
        return $event->tickets;
    }


    public function store(Request $request, Event $event)
    {
        $ticket = $event->tickets()->create($request->only(['name', 'price', 'quantity']));
        return response()->json(['message' => 'Ticket created', 'ticket' => $ticket]);
    }


    public function update(Request $request, Ticket $ticket)
    {
        $ticket->update($request->only(['name', 'price', 'quantity']));
        return response()->json(['message' => 'Ticket updated', 'ticket' => $ticket]);
    }


    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }
}
