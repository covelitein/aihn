<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class MentorController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('can:admin'),
        ];
    }

    public function index()
    {
        $mentors = User::where('is_mentor', true)->orderBy('name')->get(['id', 'name', 'email']);
        return view('admin.mentors.index', compact('mentors'));
    }
}


