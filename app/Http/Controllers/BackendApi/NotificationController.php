<?php

namespace App\Http\Controllers\backendApi;

use App\Classes\JsonRequest;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use JsonRequest;

    public function all()
    {
        $notifications = [];
        $admin = Admin::find(auth()->id());
        //loop to retrive notification
        foreach ($admin->notifications as $notification) {
            $notifications[] = $notification->data;
        }

        return $this->success([
            'status' => true,
            'data' => $notifications,
            'message' => 'All Notifications',
        ], 'notification');
    }

    public function unreadNotification()
    {
        $unReadNotification = [];
        $admin = Admin::find(auth()->id());

        //loop to retrive notification
        foreach ($admin->unreadNotifications as $notification) {
            $unReadNotification[] = $notification->data;
        }

        return $this->success([
            'status' => true,
            'data' => $unReadNotification,
            'message' => 'Unread Notification',
        ], 'notification');
    }

    public function markAsReadNotification()
    {
        $admin = Admin::find(auth()->id());

        $admin->unreadNotifications->markAsRead();

        return response()->noContent();
    }
}
