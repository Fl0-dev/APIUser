<?php


class Batch extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function deleteInactiveUsers()
    {
        $userModel = new UserModel();
        $limitDate = date('Y-m-d H:i:s', strtotime('-36 months'));
        try {
            echo "Deleting users inactive since {$limitDate}" . PHP_EOL;
            $deletedUsersCount = $userModel->deletingInactiveUsers($limitDate);

            echo "Deleted {$deletedUsersCount} users" . PHP_EOL;
        } catch (Exception $e) {
            echo "error : " . $e->getMessage() . PHP_EOL;
        }
    }
}
