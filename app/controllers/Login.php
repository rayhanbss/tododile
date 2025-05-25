<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../model/User.php';

class Login extends Controller {
    private $userModel;
    public function __construct() {
        $this->userModel = new User();
        if(session_status() === PHP_SESSION_NONE) session_start();
    }    public function index() {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $user = $this->userModel->login($username, $password);
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: /tododile/public/home');
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        }
        $this->view('login/index', ['error' => $error]);
    }    public function logout() {
        session_start();
        session_destroy();
        header('Location: /tododile/public/login');
        exit;
    }
}