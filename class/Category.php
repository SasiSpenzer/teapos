<?php

class Category {

    function __construct() {

    }

    public function select_all_categories(){

        $db_obj = new DB();
        if($_SESSION['user_level'] == 0) {
            $sql = "SELECT *
                FROM `category`
               WHERE customer_mode= '0'
            ORDER BY cat_order ASC,category_name ASC";
        } else {

        $sql = "SELECT *
                FROM `category`  ORDER BY cat_order,category_name ASC";
        }
        $db_obj->query($sql);
        if ($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $categories_results = $db_obj->getResults($sql);
            $categories_details = $extra_obj->objectToArray($categories_results);
            return $categories_details;
        }
    }

    public function selectCustomerCategories(){
        $db_obj = new DB();
        $sql = "SELECT *
                FROM `category`
               WHERE customer_mode= '0'
            ORDER BY cat_order ASC,category_name ASC";

        $db_obj->query($sql);
        if ($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $categories_results = $db_obj->getResults($sql);
            $categories_details = $extra_obj->objectToArray($categories_results);
            return $categories_details;
        }
    }




    public function get_num_of_products($cat_id) {

        $db_obj = new DB();
        $cat_sql = "SELECT COUNT(*) AS Total FROM product WHERE category_id = '".$cat_id."'";
        $db_obj->query($cat_sql);

        if ($db_obj->rowCount() > 0) {
            $cat_sql_details = $db_obj->getResults($cat_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($cat_sql_details);
        } else {
            return false;
        }
    }
    public function list_category() {

        $db_obj = new DB();
        $cat_sql = "SELECT * FROM category ORDER BY cat_order";
        $db_obj->query($cat_sql);

        if ($db_obj->rowCount() > 0) {
            $cat_sql_details = $db_obj->getResults($cat_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($cat_sql_details);
        } else {
            return false;
        }
    }

    public function get_cat_name_by_cat_id($cat_id) {

        $db_obj = new DB();
        $c_sql = "SELECT category_name  FROM category WHERE category_id = ".$cat_id;
        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }

    }
    public function get_cat_all_by_cat_id($cat_id) {

        $db_obj = new DB();
        $c_sql = "SELECT *  FROM category WHERE category_id = ".$cat_id;
        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }

    }

    public function add_cat($data_array){
        $db_obj = new DB();
        $table = 'category';
        $results = $db_obj->insert($table,$data_array);
    }
    public function delete_cat($cat_id){
        $db_obj = new DB();
        $c_sql = "DELETE FROM category WHERE category_id = ".$cat_id;
        $db_obj->query($c_sql);
    }
    public  function update_cat($data,$id){
        $db_obj = new DB();
        $table = 'category';
        $where = array("category_id =" . $id);
        $db_obj->update($table,$data,$where);
    }
    public function UpdateAllCat(){
        $db_obj = new DB();
        $c_sql = "UPDATE category SET customer_mode = '1' ";
        $db_obj->query($c_sql);
    }
    public  function get_master_code(){
        $db_obj = new DB();
        $sql = "SELECT *
                FROM `master_code`";
        $db_obj->query($sql);
        if ($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $categories_results = $db_obj->getResults($sql);
            $categories_details = $extra_obj->objectToArray($categories_results);
            return $categories_details;
        }
    }





}
