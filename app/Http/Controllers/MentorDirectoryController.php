<?php

namespace App\Http\Controllers;

use App\Models\User;

class MentorDirectoryController extends Controller
{
    public function index()
    {
        // Public directory for authenticated users to see mentors' basic contact info
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $mentorsArray = User::where('is_mentor', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        $pendingMentorIdsList = \App\Models\MentorRequest::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->pluck('mentor_id')
            ->toArray();

        return view('mentors.index', compact('mentorsArray', 'pendingMentorIdsList'));
    }
}


