<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Category;
use App\Models\Review;

class EventController extends Controller
{
    public function index()
    {
        return Event::all();
    }


    public function show(Event $event)
    {
        return $event;
    }


    public function categories()
    {
        return Category::all();
    }


    public function eventsByCategory(Category $category)
    {
        return $category->events; // افتراض علاقة Category -> events
    }


    public function addReview(Request $request, Event $event)
    {
        $review = $event->reviews()->create([
            'user_id' => $request->user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => 'Review added', 'review' => $review]);
    }


    public function reviews(Event $event)
    {
        return $event->reviews; // افتراض علاقة Event -> reviews
    }


    public function myEvents(Request $request)
    {
        return $request->user()->organizedEvents; // افتراض علاقة User -> organizedEvents
    }


    public function store(Request $request)
    {
        $event = Event::create($request->only(['name', 'description', 'category_id', 'start_date', 'end_date', 'location']));
        return response()->json(['message' => 'Event created', 'event' => $event]);
    }


    public function update(Request $request, Event $event)
    {
        $event->update($request->only(['name', 'description', 'category_id', 'start_date', 'end_date', 'location']));
        return response()->json(['message' => 'Event updated', 'event' => $event]);
    }


    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted']);
    }


    public function uploadImage(Request $request, Event $event)
    {
        if($request->hasFile('image')){
            $path = $request->file('image')->store('events', 'public');
            $event->image = $path;
            $event->save();
            return response()->json(['message' => 'Image uploaded', 'path' => $path]);
        }
        return response()->json(['message' => 'No image provided'], 400);
    }


    public function updateStatus(Request $request, Event $event)
    {
        $event->status = $request->status;
        $event->save();
        return response()->json(['message' => 'Event status updated']);
    }


    public function analytics(Event $event)
    {
        $participantsCount = $event->participants()->count();
        $reviewsCount = $event->reviews()->count();
        return response()->json([
            'participants_count' => $participantsCount,
            'reviews_count' => $reviewsCount
        ]);
    }
}
