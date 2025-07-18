<?php

namespace App\Http\Controllers;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $companyId = $request->input('company_id');

        $notifications = Notification::whereHas('user', function ($query) use ($companyId) {
                $query->where('company_id', $companyId);
            })
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notifications
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string',
            'body' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $notification = Notification::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'body' => $request->body,
            'type' => $request->type,
            'is_read' => false,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi berhasil dibuat.',
            'data' => $notification
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json([
            'status' => true,
            'message' => 'Notifikasi ditandai sebagai sudah dibaca.'
        ]);
    }

    public function notificationsUser($userId)
    {
        $notifications = Notification::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        
        return response()->json([
            'status' => true,
            'data' => $notifications,
        ]);
    }

    public function unreadNotificationCount($userId)
    {
        $count = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => true,
            'count' => $count,
        ]);
    }
}
