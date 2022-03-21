<?php
namespace MyProject\Rafael\Controller;
use MyProject\Rafael\Controller\CBase;

class CPage extends CBase {
    protected function actionIndex(){
        $this->title = "Интернет магазин";
        $img = "public/images/shop.jpg";
        $this->content = $this->template("v_index.twig", array('img' => $img));
    }
}