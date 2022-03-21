<?php
session_start();
require_once 'autoload.php';

use \MyProject\Rafael\Controller\CPage;
use \MyProject\Rafael\Controller\CUser;
use \MyProject\Rafael\Controller\COrder;
use \MyProject\Rafael\Controller\CCatalog;
use \MyProject\Rafael\Controller\CGood;
use \MyProject\Rafael\Controller\CCart;

$action = 'action';
$action .= isset($_GET['act']) ? $_GET['act'] : 'Index';

switch ($_GET['c']){
    case 'user':
        $controller = new CUser();
        break;
    case 'order':
        $controller = new COrder();
        break;
    case 'catalog':
        $controller = new CCatalog();
        break;
    case 'good':
        $controller = new CGood();
        break;
    case 'cart':
        $controller = new CCart();
        break;
    default:
        $controller = new CPage();
}

if((int)$_GET['ajax'] == 1){
    $controller->ajaxRequest($action);
}else{
    $controller->Request($action);
}



