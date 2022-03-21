<?php
namespace MyProject\Rafael\Controller;
use MyProject\Rafael\Controller\Controller;
use MyProject\Rafael\Model\MCart;

class CBase extends Controller {
    protected $title;
    protected $content;

    public function render(){
        $data = array('title' => $this->title, 'cartCount' => MCart::getCount($_SESSION['user_id'], session_id()), 'content' => $this->content, 'userSession' => $_SESSION['user_id'], 'admin' => $_SESSION['role']);
        echo $this->template("v_main.twig", $data);
    }
}