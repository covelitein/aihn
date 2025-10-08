<?php

namespace App\Http\Controllers;

use App\Models\MentorRequest;
use App\Models\User;
use Illuminate\Http\Request;

class MentorRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'mentor_id' => 'required|exists:users,id'
        ]);

        $user = $request->user();
        $mentor = User::where('id', $request->mentor_id)->where('is_mentor', true)->firstOrFail();

        // If an accepted mapping already exists, block duplicate requests
        $hasAccepted = MentorRequest::where('user_id', $user->id)->where('status', 'accepted')->exists();
        if ($hasAccepted) {
            return back()->with('error', 'You already have a mentor assigned.');
        }

        $existing = MentorRequest::where('user_id', $user->id)
            ->where('mentor_id', $mentor->id)
            ->first();

        if ($existing && $existing->status === 'pending') {
            return back()->with('info', 'You already requested this mentor.');
        }

        MentorRequest::updateOrCreate(
            ['user_id' => $user->id, 'mentor_id' => $mentor->id],
            ['status' => 'pending']
        );

        return back()->with('success', 'Mentor request sent.');
    }

    public function accept(Request $request, MentorRequest $mentorRequest)
    {
        $this->authorizeAction($request, $mentorRequest);

        if ($mentorRequest->status !== 'pending') {
            return back()->with('info', 'Request already processed.');
        }

        $mentorRequest->status = 'accepted';
        $mentorRequest->save();

        $user = $mentorRequest->user;
        MentorRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('id', '!=', $mentorRequest->id)
            ->update(['status' => 'rejected']);

        return back()->with('success', 'Mentor request accepted.');
    }

    public function reject(Request $request, MentorRequest $mentorRequest)
    {
        $this->authorizeAction($request, $mentorRequest);

        if ($mentorRequest->status !== 'pending') {
            return back()->with('info', 'Request already processed.');
        }

        $mentorRequest->status = 'rejected';
        $mentorRequest->save();

        return back()->with('success', 'Mentor request rejected.');
    }

    protected function authorizeAction(Request $request, MentorRequest $mentorRequest): void
    {
        abort_unless($request->user() && $request->user()->id === $mentorRequest->mentor_id, 403);
    }
}


