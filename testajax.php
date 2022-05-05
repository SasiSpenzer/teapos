<?php
session_start();
ob_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';

}
if(isset($_POST['get_sales_compare'])) {



    $salesyear1 = trim($_POST['salesyear1']);
    $salesyear2 = trim($_POST['salesyear2']);

    $order_obj = new Order();
    $sales1 = $order_obj->CompareSalesYear($salesyear1);
    $sales2 = $order_obj->CompareSalesYear($salesyear2);

    $Total_of_first_date = $sales1[0]['Total'];
    $Total_of_secound_date = $sales2[0]['Total'];

    $salesarray = array();
    $salesarray['one'] = number_format($Total_of_first_date);
    $salesarray['two'] = number_format($Total_of_secound_date);
    echo json_encode($salesarray);


}


if(isset($_POST['get_sales_compare2'])){

    $salesmonth1 = trim($_POST['salesmonth1']);
    $salesmonth2 = trim($_POST['salesmonth2']);
    $salesyear1 = trim($_POST['salesyear1']);
    $salesyear2 = trim($_POST['salesyear2']);

    $order_obj = new Order();
    $sales1 = $order_obj->CompareSales($salesmonth1,$salesyear1);
    $sales2 = $order_obj->CompareSales($salesmonth2,$salesyear2);

    $Total_of_first_date = $sales1[0]['Total'];
    $Total_of_secound_date = $sales2[0]['Total'];

    $salesarray = array();
    $salesarray['one'] = number_format($Total_of_first_date);
    $salesarray['two'] = number_format($Total_of_secound_date);
    echo json_encode($salesarray);
}