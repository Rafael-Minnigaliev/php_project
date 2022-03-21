<?php
namespace MyProject\Rafael\Model;
use MyProject\Rafael\DB;

class MCatalog{
    const SQL = "SELECT goods.id, goods.name, img, price, info, gender_category.name gen_name, goods_category.name good_cat_name 
                FROM goods JOIN category ON category_id = category.id JOIN gender_category ON gender_category_id = gender_category.id 
                JOIN goods_category ON goods_category_id = goods_category.id";
    const SQL_COUNT = "SELECT count(goods.id) count FROM goods JOIN category ON category_id = category.id JOIN gender_category ON gender_category_id = gender_category.id 
                JOIN goods_category ON goods_category_id = goods_category.id";

    public static function getCount($gCat = false, $gen = false){
        if($gCat && $gen){
            $catId = DB::getRow("SELECT id FROM category WHERE gender_category_id = :gen AND goods_category_id = :gCat", ['gen' => $gen, 'gCat' => $gCat]);
            return DB::getRow(self::SQL_COUNT." WHERE category_id = :id", ['id' => $catId['id']]);
        }elseif($gCat && !$gen){
            $catId = DB::Select("SELECT id FROM category WHERE goods_category_id = :gCat", ['gCat' => $gCat]);
            return DB::getRow(self::SQL_COUNT." WHERE category_id IN (:id1, :id2, :id3)",
                ['id1' => $catId[0]['id'], 'id2' => $catId[1]['id'], 'id3' => $catId[2]['id']]);
        }elseif(!$gCat && $gen){
            $catId = DB::Select("SELECT id FROM category WHERE gender_category_id = :gen", ['gen' => $gen]);
            return DB::getRow(self::SQL_COUNT." WHERE category_id IN (:id1, :id2, :id3, :id4)",
                ['id1' => $catId[0]['id'], 'id2' => $catId[1]['id'], 'id3' => $catId[2]['id'], 'id4' => $catId[3]['id']]);
        }else{
            return DB::getRow(self::SQL_COUNT);
        }
    }

    public static function getGoods($gCat = false, $gen = false, $start = 0){
        if($gCat && $gen){
            $catId = DB::getRow("SELECT id FROM category WHERE gender_category_id = :gen AND goods_category_id = :gCat", ['gen' => $gen, 'gCat' => $gCat]);
            return DB::Select(self::SQL." WHERE category_id = :id LIMIT $start, 20", ['id' => $catId['id']]);
        }elseif($gCat && !$gen){
            $catId = DB::Select("SELECT id FROM category WHERE goods_category_id = :gCat", ['gCat' => $gCat]);
            return DB::Select(self::SQL." WHERE category_id IN (:id1, :id2, :id3) LIMIT $start, 20",
                    ['id1' => $catId[0]['id'], 'id2' => $catId[1]['id'], 'id3' => $catId[2]['id']]);
        }elseif(!$gCat && $gen){
            $catId = DB::Select("SELECT id FROM category WHERE gender_category_id = :gen", ['gen' => $gen]);
            return DB::Select(self::SQL." WHERE category_id IN (:id1, :id2, :id3, :id4) LIMIT $start, 20",
                ['id1' => $catId[0]['id'], 'id2' => $catId[1]['id'], 'id3' => $catId[2]['id'], 'id4' => $catId[3]['id']]);
        }else{
            return DB::Select(self::SQL." ORDER BY goods.id LIMIT $start, 20");
        }
    }

    public static function getGoodInfo($id){
        return DB::getRow("SELECT goods.id, goods.name, img, price, info, full_info, gender_category.name gen_name, goods_category.name good_cat_name, gender_category_id, goods_category_id 
                            FROM goods JOIN category ON category_id = category.id JOIN gender_category ON gender_category_id = gender_category.id 
                            JOIN goods_category ON goods_category_id = goods_category.id WHERE goods.id = :id", ['id' => $id]);
    }

    public static function addGood($name, $price, $info, $genId, $goodCatId, $fullInfo, $photo){
        if ($photo){
            $catId = DB::getRow("SELECT id FROM category WHERE gender_category_id = :genid AND goods_category_id = :goodid", ['genid' => $genId, 'goodid' => $goodCatId]);
            DB::insert("INSERT INTO goods(name, img, price, info, full_info, category_id) VALUES(:name, :photo, :price, :info, :finfo, :cid)", ['name' => $name, 'photo' => $photo, 'price' => $price, 'info' => $info, 'finfo' => $fullInfo, 'cid' => $catId['id']]);
            move_uploaded_file("{$_FILES['photo']['tmp_name']}", "public/images/goods/$photo");
            header("Location: index.php?c=catalog&status=1");
        }else{
            header("Location: index.php?c=catalog&status=2");
        }
    }

    public static function changeGood($id, $name, $price, $info, $genId, $goodCatId, $fullInfo, $photoCheck, $photo){
        if(!$photoCheck){
//          $img = DB::getRow("SELECT img FROM goods WHERE id = :id", ['id' => $id]);
//          if(file_exists("../public/images/goods/{$img['img']}")){
//              unlink("../public/images/goods/{$img['img']}");
//          }  //Удаление файлов изображений товаров. Отключено т.к. на данный момент многие товары выводять одно и то же изображение!
            $catId = DB::getRow("SELECT id FROM category WHERE gender_category_id = :genid AND goods_category_id = :goodid", ['genid' => $genId, 'goodid' => $goodCatId]);
            DB::update("UPDATE goods SET name = :name, img = :photo, price = :price, info = :info, full_info = :finfo, category_id = :cid WHERE id = :id",
                ['name' => $name, 'photo' => $photo, 'price' => $price, 'info' => $info, 'finfo' => $fullInfo, 'cid' => $catId['id'], 'id' => $id]);
            move_uploaded_file("{$_FILES['photo']['tmp_name']}", "{$_SERVER['DOCUMENT_ROOT']}/public/images/goods/$photo");
            return self::getGoodInfo($id);
        }else{
            $catId = DB::getRow("SELECT id FROM category WHERE gender_category_id = :genid AND goods_category_id = :goodid", ['genid' => $genId, 'goodid' => $goodCatId]);
            DB::update("UPDATE goods SET name = :name, price = :price, info = :info, full_info = :finfo, category_id = :cid WHERE id = :id",
                ['name' => $name, 'price' => $price, 'info' => $info, 'finfo' => $fullInfo, 'cid' => $catId['id'], 'id' => $id]);
            return self::getGoodInfo($id);
        }
    }

    public static function deleteGood($id){
        //$img = DB::getRow("SELECT img FROM goods WHERE id = :id", ['id' => $id]);
        //if(file_exists("../public/images/goods/{$img['img']}")){
            //unlink("../public/images/goods/{$img['img']}");
        //}  //Удаление файлов изображений товаров. Отключено т.к. на данный момент многие товары выводять одно и то же изображение!
        DB::delete("DELETE FROM goods WHERE id = :id", ['id' => $id]);
    }
}



