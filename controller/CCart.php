<?php
namespace MyProject\Rafael\Controller;
use MyProject\Rafael\Controller\CBase;
use MyProject\Rafael\Model\MCart;

class CCart extends CBase{
    protected function actionIndex(){
        if (!$_SESSION['role']){
            $this->title = "Корзина";
            $id = session_id();
            $sessId = $_SESSION['user_id'];
            $data = MCart::getCartList($id, $sessId);
            $totalPrice = MCart::getTotalPrice($id, $sessId);
            $this->content = $this->template("v_cart.twig", array('data' => $data, 'totalPrice' => $totalPrice));
        }else{
            header("Location: index.php");
        }
    }

    protected function actionAddToCart(){
        $gId = (int)$_POST['gId'];
        $id = is_numeric($_POST['id']) ? (int)$_POST['id'] : (string)$_POST['id'];
        echo MCart::addToCart($gId, $id);
    }

    protected function actionDeleteFromCart(){
        $id = (int)$_POST['id'];
        echo MCart::deleteFromCart($id);
    }

    protected function actionChangeCountFromCart(){
        $id = (int)$_POST['id'];
        $val = (int)$_POST['val'];
        echo MCart::changeCountFromCart($id, $val);
    }
}