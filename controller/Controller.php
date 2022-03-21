<?php
namespace MyProject\Rafael\Controller;

abstract class Controller{
    abstract function render();

    public function Request($action){
       $this->$action();
       $this->render();
    }

    public function ajaxRequest($action){
        $this->$action();
    }

    protected function template($fileName, $data = array()){
        $loader = new \Twig\Loader\FilesystemLoader("view");
        $twig = new \Twig\Environment($loader, [
            'cache' => '/path/to/compilation_cache',
        ]);
        return $twig->render($fileName, $data);
    }

    protected function isMethod($method){
        if($_SERVER['REQUEST_METHOD'] == $method){
            return true;
        }else{
            return false;
        }
    }

    public function __call($name, $pararms){
        die("Метода $name не существует!");
    }
}