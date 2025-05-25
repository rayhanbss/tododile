<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../model/User.php';

class Register extends Controller {
    private $userModel;
    public function __construct() {
        $this->userModel = new User();
        if(session_status() === PHP_SESSION_NONE) session_start();
    }
    public function index() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            if ($this->userModel->register($username, $password)) {
                header('Location: /tododile/public/login');
                exit;
            } else {
                $error = 'Username already exists or registration failed';
            }
        }
        $this->view('register/index', ['error' => $error]);
    }
}