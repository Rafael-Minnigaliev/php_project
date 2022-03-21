<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MOrder{
    public static function getOrderList($id){
       return DB::Select("SELECT orders.id, status, date FROM orders JOIN order_status ON order_status_id = order_status.id 
WHERE users_id = :id ORDER BY date DESC", ['id' => $id]);
    }

    public static function getOrderInfo($id){
        return DB::Select("SELECT name, count, price, price * count total_price FROM cart JOIN goods ON cart.goods_id = goods.id WHERE order_id = :id", ['id' => $id]);
    }

    public static function getOrderPrice($id){
        return DB::getRow("SELECT sum(price * cart.count) sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE order_id = :id", ['id' => $id]);
    }

    public static function getOrderUserInfo($id){
        return DB::getRow("SELECT telephone, address FROM users WHERE id = :id", ['id' => $id]);
    }

    public static function getOrderRegInfo($id){
        if($id){
            return DB::Select("SELECT name, price, count FROM cart JOIN goods ON cart.goods_id = goods.id WHERE users_id = :id AND status = 0", ['id' => $id]);
        }else{
            return DB::Select("SELECT name, price, count FROM cart JOIN goods ON cart.goods_id = goods.id WHERE session_id = :id AND status = 0", ['id' => session_id()]);
        }
    }

    public static function getOrderTotalPrice($id){
        if($id){
            return DB::getRow("SELECT sum(price * cart.count) sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE users_id = :id AND status = 0", ['id' => $id]);
        }else{
            return DB::getRow("SELECT sum(price * cart.count) sum FROM cart JOIN goods ON cart.goods_id = goods.id WHERE session_id = :id AND status = 0", ['id' => session_id()]);
        }
    }

    public static function orderRegistration($var, $id, $sessId, $name, $tel, $addr, $payMethod){
        if($sessId){
            DB::connect()->beginTransaction();
            DB::insert("INSERT INTO orders(users_id, pay_method) VALUES(:id, :pm)", ['id' => $sessId, 'pm' => $payMethod]);
            $orderId = DB::getRow("SELECT id FROM orders WHERE users_id = :id ORDER BY date DESC LIMIT 1", ['id' => $sessId]);
            $update = DB::update("UPDATE cart SET status = 1, order_id = :oid WHERE users_id = :uid AND status = 0", ['oid' => $orderId['id'], 'uid' => $sessId]);
            if(!$update){
                $var = false;
            }
            if($var){
                DB::connect()->commit();
                return $orderId;
            }
            DB::connect()->rollBack();
                header("Location: index.php");
        }else{
            DB::connect()->beginTransaction();
            $data = DB::getRow("SELECT id, name, address, telephone FROM users_no_auth WHERE session_id = :id ORDER BY date DESC LIMIT 1", ['id' => $id]);
            if($data['name'] == $name && $data['address'] == $addr && $data['telephone'] == $tel){
                $userID =['id' => $data['id']];
            }else{
                DB::insert("INSERT INTO users_no_auth(session_id, name, address, telephone) VALUES(:id, :name, :addr, :tel)", ['id' => $id, 'name' => $name, 'addr' => $addr, 'tel' => $tel]);
                $userID = DB::getRow("SELECT id FROM users_no_auth WHERE session_id = :id ORDER BY date DESC LIMIT 1", ['id' => $id]);
            }
            DB::insert("INSERT INTO orders(users_no_auth_id, pay_method) VALUES(:id, :pm)", ['id' => $userID['id'], 'pm' => $payMethod]);
            $orderId = DB::getRow("SELECT id FROM orders WHERE users_no_auth_id = :id ORDER BY date DESC LIMIT 1", ['id' => $userID['id']]);
            $update = DB::update("UPDATE cart SET status = 1, order_id = :oid WHERE session_id = :sid AND status = 0", ['oid' => $orderId['id'], 'sid' => $id]);
            if(!$update){
                $var = false;
            }
            if($var){
                DB::connect()->commit();
                return $orderId;
            }
            DB::connect()->rollBack();
            header("Location: index.php");
        }
    }

    public static function getAdminOrderList(){
        return DB::Select("SELECT orders.id, status, orders.date, users.name, users.telephone, users.address, users_no_auth.name oName, users_no_auth.telephone oTelephone, users_no_auth.address oAddress FROM orders JOIN order_status ON order_status_id = order_status.id LEFT JOIN users ON users_id = users.id LEFT JOIN users_no_auth ON users_no_auth_id = users_no_auth.id WHERE order_status_id IN (1, 2) ORDER BY orders.date DESC");
    }

    public static function getAdminOrderInfo($id){
        return DB::Select("SELECT name, goods.id, count, price, users_id, cart.id AS cartId, price * count AS total_price FROM cart JOIN goods ON cart.goods_id = goods.id WHERE order_id = :id AND status = 1", ['id' => $id]);
    }

    public static function changeOrderStatus($status, $id){
        $sth = DB::update("UPDATE orders SET order_status_id = :status WHERE id = :id", ['status' => $status, 'id' => $id]);
        if($sth >= 0){
            header("Location: index.php?c=order&act=AdminOrderList");
        }
    }

    public static function DeleteGoodFromOrder($goodId, $user, $orderId){
        if(is_numeric($user)){
            DB::delete("DELETE FROM cart WHERE goods_id = :id AND users_id = :user AND order_id = :oid AND status = 1", ['id' => $goodId, 'user' => $user, 'oid' => $orderId]);
            $id = DB::getRow("SELECT id FROM cart WHERE order_id = :oid AND users_id = :user AND status = 1", ['user' => $user, 'oid' => $orderId]);
        }else{
            DB::delete("DELETE FROM cart WHERE goods_id = :id AND session_id = :user AND order_id = :oid AND status = 1", ['id' => $goodId, 'user' => $user, 'oid' => $orderId]);
            $id = DB::getRow("SELECT id FROM cart WHERE order_id = :oid AND session_id = :user AND status = 1", ['user' => $user, 'oid' => $orderId]);
        }
        if($id['id']){
            header("Location: index.php?c=order&act=AdminOrderInfo&id=$orderId");
        }else{
            DB::delete("DELETE FROM orders WHERE id = :oid", ['oid' => $orderId]);
            header("Location: index.php?c=order&act=AdminOrderList");
        }
    }

    public static function ChangeCountFromOrder($count, $goodId, $user, $orderId){
        if(is_numeric($user)){
            DB::update("UPDATE cart SET count = :count WHERE goods_id = :id AND users_id = :user AND order_id = :oid AND status = 1", ['count' => $count,'id' => $goodId, 'user' => $user, 'oid' => $orderId]);
        }else{
            DB::update("UPDATE cart SET count = :count WHERE goods_id = :id AND session_id = :user AND order_id = :oid AND status = 1", ['count' => $count,'id' => $goodId, 'user' => $user, 'oid' => $orderId]);
        }
        header("Location: index.php?c=order&act=AdminOrderInfo&id=$orderId");
    }

    public static function сheckStatusUpdate($id){
        $data = DB::Select("SELECT status, orders.id FROM orders JOIN order_status ON order_status_id = order_status.id WHERE users_id = :id ORDER BY date DESC", ['id' => $id]);
        return json_encode($data);
    }

    public static function orderRegChangeData($tel, $addr, $id){
        $info = DB::getRow("SELECT telephone, address FROM users WHERE id = :id", ['id' => $id]);
        if($tel != $info['telephone'] && $addr != $info['address']){
            DB::update("UPDATE users SET telephone = :tel, address = :addr WHERE id = :id", ['tel' => $tel, 'addr' => $addr, 'id' => $id]);
            return "Успешно!";
        }elseif($tel != $info['telephone'] && $addr == $info['address']){
            DB::update("UPDATE users SET telephone = :tel WHERE id = :id", ['tel' => $tel, 'id' => $id]);
            return "Успешно!";
        }elseif($tel == $info['telephone'] && $addr != $info['address']){
            DB::update("UPDATE users SET address = :addr WHERE id = :id", ['addr' => $addr, 'id' => $id]);
            return "Успешно!";
        }
    }
}