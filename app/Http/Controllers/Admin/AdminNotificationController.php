<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminNotificationController extends Controller
{
    private function adminUser(Request $request)
    {
        // Si usas guard por middleware custom y necesitas el guard 'admin', cambia a:
        // return $request->user('admin');
        return $request->user();
    }

    public function index(Request $request)
    {
        $admin = $this->adminUser($request);
        $notifications = $admin->notifications()->latest()->paginate(15);
        $unreadCount   = $admin->unreadNotifications()->count();

        return view('admin.notifications.index', compact('notifications','unreadCount'));
    }

    public function markAsRead(Request $request, string $notificationId)
    {
        $admin = $this->adminUser($request);
        $notification = $admin->notifications()->findOrFail($notificationId);
        $notification->markAsRead();

        return back()->with('status', 'Notificación marcada como vista.');
    }

    public function markAllAsRead(Request $request)
    {
        $admin = $this->adminUser($request);
        $admin->unreadNotifications->markAsRead();
        return back()->with('status', 'Todas marcadas como vistas.');
    }

    public function destroy(Request $request, string $notificationId)
    {
        $admin = $this->adminUser($request);
        $notification = $admin->notifications()->findOrFail($notificationId);
        $notification->delete();

        return back()->with('status', 'Notificación eliminada.');
    }

    public function destroyAll(Request $request)
    {
        $admin = $this->adminUser($request);
        $admin->notifications()->delete();

        return back()->with('status', 'Todas las notificaciones fueron eliminadas.');
    }
}
