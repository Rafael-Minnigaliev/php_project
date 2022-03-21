<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MProfile{
    public static function getInfo($id){
       return DB::getRow("SELECT * FROM users WHERE id = :id", ['id' => $id]);
    }

    public static function logOut(){
        session_destroy();
        header("Location: index.php?c=index");
    }
}