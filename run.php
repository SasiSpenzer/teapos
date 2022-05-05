<?php
include_once("common_header.php");





$base_string  = '11111111111111';

$start_no=1;

$pro_obj   = new Product();
$all_data = $pro_obj->get_all_nonLoos_tea();
foreach($all_data as $each_products){
    $product_id = $each_products['product_id'];
    $product_price = $each_products['price'];
    $tempArray = array();
    $tempArray['sales_price'] = $product_price;
    $pro_obj->update_new_product($tempArray,$product_id);
}
exit;
$all_data = $pro_obj->get_all_loose_tea();

foreach($all_data as $each_products){
    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar25= $new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar50 = $new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar80 =$new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar100 = $new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar250 = $new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar500 = $new_number;

    $str = $start_no++;
    $new_number = str_pad($str,14,"1",STR_PAD_LEFT);
    $bar1000 = $new_number;


    $query = 'UPDATE product SET bar25 ="'. $bar25.'", bar50 ="'. $bar50.'", bar80 ="'. $bar80.'",bar100 ="'. $bar100.'",bar250 ="'. $bar250.'",bar500 ="'. $bar500.'",bar1000 ="'. $bar1000.'" WHERE product_id ='.$each_products['product_id'];
    $db_obj = new DB();
    $db_obj->query($query);


}

?>