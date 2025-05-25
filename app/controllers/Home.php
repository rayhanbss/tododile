<?php
require_once __DIR__ . '/../core/Controller.php';

class Home extends Controller
{
    // Page Method
    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/login');
            exit;
        }
        // Fetch username and tasks
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        $tasks = $task->getAll($_SESSION['user_id']);
        $this->view('home/index', ['username' => $username, 'tasks' => $tasks]);
    }

    public function addTask()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            require_once __DIR__ . '/../model/Task.php';
            $task = new Task();
            $task->add($_SESSION['user_id'], $_POST['task']);
        }
        header('Location: /tododile/public/home');
        exit;
    }

    public function updateTask($id)
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            require_once __DIR__ . '/../model/Task.php';
            $task = new Task();
            $task->update($id, $_SESSION['user_id'], $_POST['task']);
        }
        header('Location: /tododile/public/home');
        exit;
    }

    public function deleteTask($id)
    {
        session_start();
        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        $task->delete($id, $_SESSION['user_id']);
        header('Location: /tododile/public/home');
        exit;
    }    
    public function toggleStatus($id)
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/login');
            exit;
        }

        require_once __DIR__ . '/../model/Task.php';
        $task = new Task();
        
        // First get the current task to see its status
        $tasks = $task->getAll($_SESSION['user_id']);
        $currentTask = null;
        foreach ($tasks as $t) { // Changed variable name from $task to $t
            if ($t['id'] == $id) {
                $currentTask = $t;
                break;
            }
        }
        
        if ($currentTask) {
            // Toggle completion status
            $newStatus = isset($currentTask['completed']) ? !$currentTask['completed'] : true;
            $task->updateStatus($id, $_SESSION['user_id'], $newStatus);
        }
        header('Location: /tododile/public/home');
        exit;
    }
}