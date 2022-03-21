<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MReg{
    public static function reg($name, $login, $pass, $tel, $address){
        $sth1 = DB::getRow("SELECT login FROM users WHERE login = :login", ['login' => $login]);
        if($sth1['login']){
            header("Location: index.php?c=user&act=Reg&status=loginExists");
        }else{
            $sth2 = DB::insert("INSERT INTO users(name, login, pass, telephone, address) 
                VALUES(:name, :login, CONCAT(REVERSE(md5(:login)),md5(:pass),REVERSE(md5(:login))), :tel, :address)",
                ['name' => $name, 'login' => $login, 'pass' => $pass, 'tel' => $tel, 'address' => $address]);
            if($sth2){
                header("Location: index.php?c=user&act=Login");
            }
        }
    }
}