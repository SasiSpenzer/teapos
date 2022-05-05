<?php


class Product {

    public function select_product_by_cat_id($cat_id){
        $db_obj = new DB();
        $sql = "SELECT *
                FROM `product`
                WHERE category_id='".$cat_id."'
                ORDER BY Order_id ASC";
        $db_obj->query($sql);
        if ($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $parent_results = $db_obj->getResults($sql);
            $slider_details = $extra_obj->objectToArray($parent_results);
            return $slider_details;
        }
    }


    public function list_priduct_by_product_id($p_id) {

        $db_obj = new DB();
        $p_sql = "SELECT *  FROM product WHERE product_id = ".$p_id;
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }
    public function add_new_product($product_details) {

        $db_obj = new DB();
        $table = 'product';
        $db_obj->insert($table, $product_details);
        return $db_obj->getLastInsertId();


    }
    public function delete_each_cat_product($cat_id) {

        $db_obj = new DB();
        $p_sql = "DELETE FROM product WHERE category_id = ".$cat_id;
        $db_obj->query($p_sql);

    }
    public  function reduce_qty($data,$id){
        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $id);
        $db_obj->update($table,$data,$where);
    }
    public  function update_order_p($data,$id){
        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $id);
        $db_obj->update($table,$data,$where);
    }

    public function delete_product_by_id($p_id) {

        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $p_id);
        $db_obj->delete($table, $where);

        return true;

    }



    public function add_product_history($product_history_details) {

        $db_obj = new DB();
        $table = 'product_history';
        $db_obj->insert($table, $product_history_details);
        return $db_obj->getLastInsertId();


    }
    public function update_new_product($product_details,$product_id) {

        $db_obj = new DB();
        $table = 'product';
        $data = $product_details;
        $where = array("product_id =" . $product_id);
        $db_obj->update($table, $data, $where);

        return true;

    }

} 