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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = $this->userModel->register($username, $password);
            if (is_array($result)) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
                // Show the form with errors, do not redirect
                $this->view('register/index');
                return;
            } else if ($result === true) {
                // Registration successful, redirect to login
                $_SESSION['flash_message'] = ['type' => 'success', 'message' => 'Registration complete! You can now log in to your account.'];
                header('Location: /tododile/public/login');
                exit;
            } else {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'An error occurred during registration. Please try again.'];
                $this->view('register/index');
                return;
            }
        }
        $this->view('register/index');
    }
}