<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminUserController extends Controller implements HasMiddleware
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
        $admins = User::where('is_admin', true)->where('is_super_admin', false)->orderBy('name')->paginate(25);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admin.admins.create');
    }

    public function store(Request $request)
    {
        $this->authorize('superadmin');
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'is_mentor' => 'nullable|boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_admin' => true,
            'is_mentor' => (bool) $request->filled('is_mentor'),
        ]);

        try {
            $request->user()->notify(new \App\Notifications\GenericMessageNotification('Admin '.$user->name.' has been created.'));
        } catch (\Throwable $e) {}

        return redirect()->route('admin.admins.index')
            ->with('success', 'Admin added successfully. Password set by super admin.');
    }

    public function destroy(User $admin)
    {
        $this->authorize('superadmin');
        if (!$admin->is_admin) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Target user is not an admin.'], 422);
            }
            return redirect()->back()->with('error', 'Target user is not an admin.');
        }
        if ($admin->is_super_admin) {
            if (request()->ajax()) {
                return response()->json(['error' => 'Cannot delete a superadmin account.'], 403);
            }
            return redirect()->back()->with('error', 'Cannot delete a superadmin account.');
        }
        $admin->delete();
        if (request()->ajax()) {
            return response()->noContent();
        }
        return redirect()->route('admin.admins.index')->with('success', 'Admin deleted successfully.');
    }
}


