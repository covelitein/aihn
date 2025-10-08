<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\User;
use App\Models\MentorRequest;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Prevent admins from entering the user dashboard
        if ($user && ($user->is_admin ?? 0) == 1) {
            return redirect()->route('admin.dashboard.index');
        }

        // Get recently added content (no plan filtering — access is handled by admin provisioning)
        $recentContent = Content::published()
            ->latest()
            ->take(4)
            ->get();

        // Content stats: show overall counts
        $contentStats = Content::published()
            ->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        // Subscription-related variables intentionally removed
        $pendingApplication = null;
        $availablePlans = null;
        $currentPlan = null;

        // Mentors directory and requests
        $mentors = User::where('is_mentor', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $pendingMentorIds = MentorRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->pluck('mentor_id')
            ->toArray();

        $pendingRequestsForMentor = [];
        $mentees = collect();
        if ($user->is_mentor) {
            $pendingRequestsForMentor = MentorRequest::with('user')
                ->where('mentor_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->take(10)
                ->get();
            $mentees = $user->mentees()->latest()->take(10)->get();
        }

        return view('dashboard', compact(
            'recentContent',
            'pendingApplication',
            'availablePlans',
            'contentStats',
            'currentPlan',
            'mentors',
            'pendingMentorIds',
            'pendingRequestsForMentor',
            'mentees'
        ));
    }
}