<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;

class AdminController extends Controller
{
    public function dashboardAnalytics()
    {
        $totalUsers = User::count();
        $totalEvents = Event::count();
        $totalParticipants = Event::withCount('participants')->get()->sum('participants_count');

        return response()->json([
            'total_users' => $totalUsers,
            'total_events' => $totalEvents,
            'total_participants' => $totalParticipants
        ]);
    }
}
