<?php
require_once '../core/Controller.php';

class DashboardController extends Controller{
    
    public function __construct() {
    }
    public function index() {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'avatar' => $_SESSION['user_avatar']
        ];
        $this->view('admin/dashboard/index',['user' => $user]);

    }
    public function inicio() {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'avatar' => $_SESSION['user_avatar']
        ];
        $this->view('admin/dashboard/inicio',['user' => $user]);

    }
    public function mision() {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'avatar' => $_SESSION['user_avatar']
        ];
        $this->view('admin/dashboard/mision',['user' => $user]);

    }
    public function vision() {
        $user = [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'avatar' => $_SESSION['user_avatar']
        ];
        $this->view('admin/dashboard/vision',['user' => $user]);

    }
}

?>
