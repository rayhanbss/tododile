<?php
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../model/User.php';

class Login extends Controller {
    private $userModel;
    public function __construct() {
        $this->userModel = new User();
        if(session_status() === PHP_SESSION_NONE) session_start();
    }    
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['rememberme']);
            $result = $this->userModel->login($username, $password);
            if (is_array($result) && (isset($result['usernameError']) || isset($result['passwordError']))) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => $result];
                header('Location: /tododile/public/login');
                exit;
            } else if (is_array($result) && isset($result['error'])) {
                $_SESSION['flash_message'] = ['type' => 'error', 'message' => ['error' => $result['error']]];
                header('Location: /tododile/public/login');
                exit;
            } else if (is_array($result) && isset($result['id'])) {
                $user = $result;
            }
            if (isset($user) && $user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('rememberme', $token, time() + (86400 * 30), "/"); // 30 hari
                    $_SESSION['rememberme_token'] = $token;
                    $_SESSION['rememberme_user'] = $user['id'];
                }
                header('Location: /tododile/public/home');
                exit;
            }
        }
        // Cek cookie rememberme jika belum login
        if (!isset($_SESSION['user_id']) && isset($_COOKIE['rememberme']) && isset($_SESSION['rememberme_token']) && $_COOKIE['rememberme'] === $_SESSION['rememberme_token']) {
            $_SESSION['user_id'] = $_SESSION['rememberme_user'];
            // Anda bisa tambahkan query ke DB untuk ambil username jika perlu
            header('Location: /tododile/public/home');
            exit;
        }
        $this->view('login/index');
    }    
    
    public function logout() {
        session_start();
        setcookie('rememberme', '', time() - 3600, "/"); // hapus cookie
        unset($_SESSION['rememberme_token']);
        unset($_SESSION['rememberme_user']);
        session_destroy();
        header('Location: /tododile/public/login');
        exit;
    }
}