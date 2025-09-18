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
        $this->view('users/dashboard',['user' => $user]);

    }
}