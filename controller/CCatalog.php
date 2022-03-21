<?php
namespace MyProject\Rafael\Controller;
use MyProject\Rafael\Controller\CBase;
use MyProject\Rafael\Model\MCatalog;

class CCatalog extends CBase {
    protected function actionIndex(){
        $this->title = "Каталог";
        if(!$_SESSION['role']){
            if($this->isMethod('POST')){
                $gCat = (int)$_POST['gCategory'] != "none" ? (int)$_POST['gCategory'] : false;
                $gen = (int)$_POST['gender'] != "none" ? (int)$_POST['gender'] : false;
                $data = MCatalog::getGoods($gCat, $gen);
                $count = MCatalog::getCount($gCat, $gen);
                $maxPage = round($count['count'] / 20);
                $this->content = $this->template("catalog/v_catalog.twig", array('data' => $data, 'gCat' => $gCat, 'gen' => $gen, 'maxPage' => $maxPage, 'id' => $_SESSION['user_id'] ? $_SESSION['user_id'] : session_id()));
            }else{
                $data = MCatalog::getGoods();
                $count = MCatalog::getCount();
                $maxPage = round($count['count'] / 20);
                $this->content = $this->template("catalog/v_catalog.twig", array('data' => $data, 'maxPage' => $maxPage, 'id' => $_SESSION['user_id'] ? $_SESSION['user_id'] : session_id()));
            }
        }else{
            if($this->isMethod('POST')){
                $gCat = (int)$_POST['gCategory'] != "none" ? (int)$_POST['gCategory'] : false;
                $gen = (int)$_POST['gender'] != "none" ? (int)$_POST['gender'] : false;
                $data = MCatalog::getGoods($gCat, $gen);
                $count = MCatalog::getCount($gCat, $gen);
                $maxPage = round($count['count'] / 20);
                $this->content = $this->template("catalog/v_admin_catalog.twig", array('data' => $data, 'gCat' => $gCat, 'gen' => $gen, 'maxPage' => $maxPage, 'id' => $_SESSION['user_id'] ? $_SESSION['user_id'] : session_id(), 'status' => $_GET['status'], 'admin' => $_SESSION['role']));
            }else{
                $data = MCatalog::getGoods();
                $count = MCatalog::getCount();
                $maxPage = round($count['count'] / 20);
                $this->content = $this->template("catalog/v_admin_catalog.twig", array('data' => $data, 'maxPage' => $maxPage, 'id' => $_SESSION['user_id'] ? $_SESSION['user_id'] : session_id(), 'status' => $_GET['status'], 'admin' => $_SESSION['role']));
            }
        }
    }

    protected function actionAddGood(){
        if($_SESSION['role']){
            $name = $_POST['goodName'] ? strip_tags($_POST['goodName']) : "";
            $price = (int)$_POST['goodPrice'];
            $info = $_POST['goodInfo'] ? strip_tags($_POST['goodInfo']) : "";
            $genId = (int)$_POST['genderId'];
            $goodCatId = (int)$_POST['goodCategoryId'];
            $fullInfo = $_POST['goodFullInfo'] ? strip_tags($_POST['goodFullInfo']) : "";
            $photo = $_FILES['photo']['name'];
            MCatalog::addGood($name, $price, $info, $genId, $goodCatId, $fullInfo, $photo);
        }else{
            header("Location: index.php");
        }
    }

    protected function actionGetGoodInfo(){
        $id = (int)$_GET['id'];
        $data = MCatalog::getGoodInfo($id);
        $path = $_SERVER['HTTP_REFERER'];
        $this->title = $data['name'];
        $this->content = $this->template("catalog/v_product.twig", array('data' => $data, 'sid' => session_id(), 'uid' => $_SESSION['user_id'], 'path' => $path, 'admin' => $_SESSION['role']));
    }

    protected function actionShowMoreGoods(){
        $gCat = (int)$_POST['gcat'] != "" ? (int)$_POST['gcat'] : false;
        $gen = (int)$_POST['gen'] != "" ? (int)$_POST['gen'] : false;
        $page = (int)$_POST['page'];
        $start = $page * 20 - 20;
        $id = is_numeric($_POST['uid']) ? (int)$_POST['uid'] : (string)$_POST['uid'];
        $admin = (int)$_POST['admin'];
        $data = MCatalog::getGoods($gCat, $gen, $start);
        echo $this->template("catalog/v_goods_card.twig", array('admin' => $admin, 'data' => $data, 'id' => $id));
    }

    protected function actionGetChangeForm(){
        if($_SESSION['role']){
            $id = (int)$_POST['id'];
            $data = MCatalog::getGoodInfo($id);
            echo $this->template("catalog/v_change_form.twig", array('id' => $id, 'data' => $data));
        }else{
            header("Location: index.php");
        }
    }

    protected function actionChangeGood(){
        if($_SESSION['role']){
            $id = (int)$_POST['id'];
            $name = $_POST['goodName'] ? strip_tags($_POST['goodName']) : false;
            $price = (int)$_POST['goodPrice'];
            $info = $_POST['goodInfo'] ? strip_tags($_POST['goodInfo']) : false;
            $genId = (int)$_POST['genderId'];
            $goodCatId = (int)$_POST['goodCategoryId'];
            $fullInfo = $_POST['goodFullInfo'] ? strip_tags($_POST['goodFullInfo']) : false;
            $photoCheck = $_POST['photoCheck'] ? strip_tags($_POST['photoCheck']) : false;
            $photo = $_FILES['photo']['name'];
            $data[] = MCatalog::changeGood($id, $name, $price, $info, $genId, $goodCatId, $fullInfo, $photoCheck, $photo);
            echo $this->template("catalog/v_goods_card.twig", array('admin' => $_SESSION['role'], 'data' => $data));
        }else{
            header("Location: index.php");
        }
    }

    protected function actionDeleteGood(){
        if($_SESSION['role']){
            $id = (int)$_POST['id'];
            MCatalog::deleteGood($id);
        }else{
            header("Location: index.php");
        }
    }
}