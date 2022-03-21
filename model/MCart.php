<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MCart{
    public static function getCount($id, $sessID){
        if($id){
            return DB::getRow("SELECT sum(count) sum FROM cart WHERE users_id = :id AND status = 0", ['id' => $id]);
        }else{
            return DB::getRow("SELECT sum(count) sum FROM cart WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $sessID]);
        }
    }

    public static function getCartList($id, $sessId){
        if($sessId){
            return DB::Select("SELECT cart.id, goods_id, goods.name, price, count, img, gender_category.name gen_name, goods_category.name good_cat_name FROM cart JOIN goods ON cart.goods_id = goods.id JOIN category ON category_id = category.id JOIN gender_category ON gender_category_id = gender_category.id 
                JOIN goods_category ON goods_category_id = goods_category.id WHERE users_id = :id AND status = 0", ['id' => $sessId]);
        }else{
            return DB::Select("SELECT cart.id, goods_id, goods.name, price, count, img, gender_category.name gen_name, goods_category.name good_cat_name FROM cart JOIN goods ON cart.goods_id = goods.id JOIN category ON category_id = category.id JOIN gender_category ON gender_category_id = gender_category.id 
                JOIN goods_category ON goods_category_id = goods_category.id WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $id]);
        }
    }

    public static function getTotalPrice($id, $sessId){
        if($sessId){
            return DB::getRow("SELECT sum(price * cart.count) sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE users_id = :id AND status = 0", ['id' => $sessId]);
        }else{
            return DB::getRow("SELECT sum(price * cart.count) sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $id]);
        }
    }

    public static function addToCart($gId, $id){
        if(is_numeric($id)){
            $idCart = DB::getRow("SELECT id FROM cart WHERE users_id = :id AND goods_id = :gid AND status = 0", ['id' => $id, 'gid' => $gId]);
            if($idCart['id']){
                DB::update("UPDATE cart SET count = count + 1 WHERE id = :id", ['id' => $idCart['id']]);
            }else{
                DB::insert("INSERT INTO cart(goods_id, users_id) VALUES(:gid, :id)", ['gid' => $gId, 'id' => $id]);
            }
            $count = DB::getRow("SELECT sum(count) sum FROM cart WHERE users_id = :id AND status = 0", ['id' => $id]);
        }else{
            $idCart = DB::getRow("SELECT id FROM cart WHERE session_id = :id AND goods_id = :gid AND users_id = 0 AND status = 0", ['id' => $id, 'gid' => $gId]);
            if($idCart['id']){
                DB::update("UPDATE cart SET count = count + 1 WHERE id = :id", ['id' => $idCart['id']]);
            }else{
                DB::insert("INSERT INTO cart(goods_id, session_id) VALUES(:gid, :id)", ['gid' => $gId, 'id' => $id]);
            }
            $count = DB::getRow("SELECT sum(count) sum FROM cart WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $id]);
        }
        return "{$count['sum']}";
    }

    public static function deleteFromCart($id){
        $userId = DB::getRow("SELECT users_id, session_id FROM cart WHERE id = :id AND status = 0", ['id' => $id]);
        DB::delete("DELETE FROM cart WHERE id = :id AND status = 0", ['id' => $id]);
        if($userId['users_id'] != 0){
            $count = DB::getRow("SELECT sum(count) c_sum FROM cart WHERE users_id = :id AND status = 0", ['id' => $userId['users_id']]);
            $totalPrice = DB::getRow("SELECT sum(price * cart.count) p_sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE users_id = :id AND status = 0", ['id' => $userId['users_id']]);
        }else{
            $count = DB::getRow("SELECT sum(count) c_sum FROM cart WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $userId['session_id']]);
            $totalPrice = DB::getRow("SELECT sum(price * cart.count) p_sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $userId['session_id']]);
        }
        return "{$count['c_sum']} {$totalPrice['p_sum']}";
    }

    public static function changeCountFromCart($id, $val){
        DB::update("UPDATE cart SET count = :val WHERE id = :id AND status = 0",['val' => $val, 'id' => $id]);
        $userId = DB::getRow("SELECT users_id, session_id FROM cart WHERE id = :id AND status = 0", ['id' => $id]);
        if($userId['users_id'] != 0){
            $count = DB::getRow("SELECT sum(count) c_sum FROM cart WHERE users_id = :id AND status = 0", ['id' => $userId['users_id']]);
            $totalPrice = DB::getRow("SELECT sum(price * cart.count) p_sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE users_id = :id AND status = 0", ['id' => $userId['users_id']]);
        }else{
            $count = DB::getRow("SELECT sum(count) c_sum FROM cart WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $userId['session_id']]);
            $totalPrice = DB::getRow("SELECT sum(price * cart.count) p_sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE session_id = :id AND users_id = 0 AND status = 0", ['id' => $userId['session_id']]);
        }
        $price = DB::getRow("SELECT price * count price FROM cart JOIN goods ON cart.goods_id = goods.id WHERE cart.id = :id", ['id' => $id]);
        return "{$price['price']} {$totalPrice['p_sum']} {$count['c_sum']}";
    }
}