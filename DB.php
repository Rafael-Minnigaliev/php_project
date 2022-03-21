<?php
namespace MyProject\Rafael;
use PDO;

class DB{
    const DB = 'mysql';
    const DB_HOST = 'localhost';
    const DB_NAME = 'store';
    const DB_USER = 'root';
    const DB_PASS = 'root';
    const DB_CHAR = 'utf8';

    private static $connect;

    private function __construct() {}
    private function __sleep() {}
    private function __wakeup() {}
    private function __clone() {}

    private static function getConnect(){
        if(self::$connect === null){
            $opt = array(
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            );
            $connectStr = self::DB.":host=".self::DB_HOST.";dbname=".self::DB_NAME.';charset='.self::DB_CHAR;
            self::$connect = new PDO($connectStr, self::DB_USER,self::DB_PASS, $opt);
        }
        return self::$connect;
    }

    private static function sql($sql, $args = []){
        $sth = self::getConnect()->prepare($sql);
        $sth->execute($args);
        return $sth;
    }

    public static function Select($sql, $args = []) {
        return self::sql($sql, $args)->fetchAll();
    }

    public static function getRow($sql, $args = []) {
        return self::sql($sql, $args)->fetch();
    }

    public static function insert($sql, $args = []) {
        $stmt = self::sql($sql, $args);
        return $stmt->rowCount();
    }

    public static function update($sql, $args = []) {
        $stmt = self::sql($sql, $args);
        return $stmt->rowCount();
    }

    public static function delete($sql, $args = []) {
        $stmt = self::sql($sql, $args);
        return $stmt->rowCount();
    }

    public static function connect(){
        return self::getConnect();
    }
}
