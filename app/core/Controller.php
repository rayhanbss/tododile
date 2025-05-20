<?php   

class Controller{
    public function view($view, $data = []){
        if(file_exists(__DIR__ . '/../views/' . $view . '.php')){
            require_once __DIR__ . '/../views/' . $view . '.php';
        }else{
            die('View does not exist');
        }
    }
}