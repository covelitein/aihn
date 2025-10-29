<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->limit(20)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return response()->json([
            'unread' => $unreadCount,
            'items' => $notifications->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => class_basename($n->type),
                    'message' => $n->data['message'] ?? 'Notification',
                    'created_at' => $n->created_at->diffForHumans(),
                    'read_at' => optional($n->read_at)?->toIso8601String(),
                ];
            })
        ]);
    }

    public function markRead(Request $request, string $id)
    {
        $user = $request->user();
        $notification = $user->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();
        return response()->json(['ok' => true]);
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }
}


