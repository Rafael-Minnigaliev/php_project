<?php
namespace MyProject\Rafael\Controller;
use MyProject\Rafael\Controller\CBase;
use MyProject\Rafael\Model\MProfile;
use MyProject\Rafael\Model\MLogin;
use MyProject\Rafael\Model\MReg;

class CUser extends CBase{
    protected function actionProfile(){
        if ($_SESSION['user_id']) {
            $this->title = "Мой кабинет";
            $data = MProfile::getInfo($_SESSION['user_id']);
            $this->content = $this->template("v_profile.twig", array('data' => $data, 'admin' => $_SESSION['role']));
        } else {
            header("Location: index.php");
        }
    }

    protected function actionLogOut(){
        if ($_SESSION['user_id']) {
            MProfile::logOut();
        } else {
            header("Location: index.php");
        }
    }

    protected function actionLogin(){
        if (!$_SESSION['user_id']){
            if($this->isMethod('POST')){
                $login = $_POST['login'] ? strip_tags($_POST['login']) : "";
                $pass = $_POST['pass'] ? strip_tags($_POST['pass']) : "";
                MLogin::login($login, $pass);
            }
            $this->title = "Авторизация";
            $this->content = $this->template("v_login.twig", array('status' => $_GET['status']));
        } else {
            header("Location: index.php");
        }
    }

    protected function actionReg(){
        if (!$_SESSION['user_id']){
            if($this->isMethod('POST')){
                $name = $_POST['name'] ? strip_tags($_POST['name']) : "";
                $login = $_POST['login'] ? strip_tags($_POST['login']) : "";
                $pass = $_POST['pass'] ? strip_tags($_POST['pass']) : "";
                $tel = (int)$_POST['tel'];
                $address = $_POST['address'] ? strip_tags($_POST['address']) : "";
                MReg::reg($name, $login, $pass, $tel, $address);
            }else{
                $this->title = "Регистрация";
                $this->content = $this->template("v_reg.twig", array('status' => $_GET['status']));
            }
        } else {
            header("Location: index.php");
        }
    }
}
