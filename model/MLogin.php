<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MLogin{
    private static function getSecurePass($login, $pass){
        return strrev(md5($login)).md5($pass).strrev(md5($login));
    }

    public static function login($login, $pass)
    {
        $sPass = self::getSecurePass($login, $pass);
        $data = DB::getRow("SELECT id, role FROM users WHERE login = :login AND pass = :pass", ['login' => $login, 'pass' => $sPass]);
        if ($data['id']) {
            if ($data['role'] == 1) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['role'] = $data['role'];
                header("Location: index.php?c=user&act=profile");
            } else {
                $_SESSION['user_id'] = $data['id'];
                header("Location: index.php?c=user&act=profile");
            }
        } else {
            header("Location: index.php?c=user&act=Login&status=error");
        }
    }
}


