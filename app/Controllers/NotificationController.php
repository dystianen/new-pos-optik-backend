<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * API realtime (dipanggil via setInterval)
     * GET /notifications
     */
    public function index()
    {
        $notifications = $this->notificationModel->getNotifications();
        $unreadCount  = $this->notificationModel->countUnread();

        return $this->response->setJSON([
            'status' => true,
            'count'  => $unreadCount,
            'data'   => $notifications
        ]);
    }

    public function markAllRead()
    {

        $this->notificationModel
            ->where('is_read', 0)
            ->set(['is_read' => 1])
            ->update();

        return $this->response->setJSON([
            'status' => true
        ]);
    }

    // opsional: jika klik satu notif
    public function markRead($id)
    {
        $this->notificationModel
            ->update($id, ['is_read' => 1]);

        return $this->response->setJSON(['status' => true]);
    }
}
