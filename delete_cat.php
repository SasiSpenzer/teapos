<?php
include_once("common_header.php");

$cat_id = $_GET['Cat_id'];
$cat_obj = new Category();
$cat_obj->delete_cat($cat_id);

$product_obj = new Product();
$product_obj->delete_each_cat_product($cat_id);



header("Location:remove_cat.php");
