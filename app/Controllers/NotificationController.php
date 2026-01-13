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


    public function getAllNotifications()
    {
        $page     = (int) ($this->request->getVar('page') ?? 1);
        $perPage = 10;

        $notifications = $this->notificationModel
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage, 'default', $page);

        $pager = [
            'currentPage' => $this->notificationModel->pager->getCurrentPage('default'),
            'totalPages'  => $this->notificationModel->pager->getPageCount('default'),
            'limit'       => $perPage
        ];

        $data = [
            'data'  => $notifications,
            'pager' => $pager
        ];

        return view('notifications/v_index', $data);
    }


    public function getUnreadNotifications()
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
