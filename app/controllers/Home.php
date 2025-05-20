<?php

class Home extends Controller
{
    // Page Method
    public function index()
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/?url=home/login');
            exit;
        }
        // Fetch username and tasks
        $username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
        require_once __DIR__ . '/../model/TaskModel.php';
        $taskModel = new TaskModel();
        $tasks = $taskModel->getAll($_SESSION['user_id']);
        $this->view('home/index', ['username' => $username, 'tasks' => $tasks]);
    }

    public function addTask()
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            require_once __DIR__ . '/../model/TaskModel.php';
            $taskModel = new TaskModel();
            $taskModel->add($_SESSION['user_id'], $_POST['task']);
        }
        header('Location: /tododile/public/?url=home');
        exit;
    }

    public function updateTask($id)
    {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['task'])) {
            require_once __DIR__ . '/../model/TaskModel.php';
            $taskModel = new TaskModel();
            $taskModel->update($id, $_SESSION['user_id'], $_POST['task']);
        }
        header('Location: /tododile/public/?url=home');
        exit;
    }

    public function deleteTask($id)
    {
        session_start();
        require_once __DIR__ . '/../model/TaskModel.php';
        $taskModel = new TaskModel();
        $taskModel->delete($id, $_SESSION['user_id']);
        header('Location: /tododile/public/?url=home');
        exit;
    }

    public function toggleStatus($id)
    {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /tododile/public/?url=home/login');
            exit;
        }

        require_once __DIR__ . '/../model/TaskModel.php';
        $taskModel = new TaskModel();
        
        // First get the current task to see its status
        $tasks = $taskModel->getAll($_SESSION['user_id']);
        $currentTask = null;
        foreach ($tasks as $task) {
            if ($task['id'] == $id) {
                $currentTask = $task;
                break;
            }
        }
        
        if ($currentTask) {
            // Toggle completion status
            $newStatus = isset($currentTask['completed']) ? !$currentTask['completed'] : true;
            $taskModel->updateStatus($id, $_SESSION['user_id'], $newStatus);
        }
        
        header('Location: /tododile/public/?url=home');
        exit;
    }

    public function login()
    {
        $this->view('home/login');
    }
}