<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    public function add(Request $request, Event $event)
    {
        $favorite = Favorite::firstOrCreate([
            'user_id' => $request->user()->id,
            'event_id' => $event->id
        ]);

        return response()->json(['message' => 'Added to favorites', 'favorite' => $favorite]);
    }

    public function remove(Request $request, Event $event)
    {
        Favorite::where('user_id', $request->user()->id)
            ->where('event_id', $event->id)
            ->delete();

        return response()->json(['message' => 'Removed from favorites']);
    }

    public function myFavorites(Request $request)
    {
        return response()->json($request->user()->favorites);
    }
}
