<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;

class EventController extends Controller
{
    public function index() {
        return Event::all();
    }


    public function show(Event $event) {
        return $event;
    }


    public function categories() {
        return Category::all();
    }


    public function eventsByCategory(Category $category) {
        return $category->events; // assuming relation defined
    }


    public function reviews(Event $event) {
        return $event->reviews; // assuming relation defined
    }



    public function myEvents(Request $request) {
        return $request->user()->organizedEvents;
    }


    public function analytics(Event $event) {
        return [
            'participants_count' => $event->participants()->count(),
            'tickets_count' => $event->tickets()->count(),
        ];
    }


    public function updateStatus(Request $request, Event $event) {
        $event->status = $request->status;
        $event->save();
        return response()->json(['message'=>'Status updated']);
    }


    public function uploadImage(Request $request, Event $event) {
        if($request->hasFile('image')){
            $path = $request->file('image')->store('events','public');
            $event->image = $path;
            $event->save();
        }
        return response()->json($event);
    }


    public function addReview(Request $request, Event $event) {
        $review = $event->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
        return response()->json($review);
    }
}
