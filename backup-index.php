<?php
    include_once("common_header.php");
    $user = new User();

    //include_once("class/User.php");
    $category = new Category();
    $order_obj = new Order();
    $cash_obj = new CashFeed();
    //$user = new Ca

    $cat = new Category();
    $category_details = $category->select_all_categories();

    $product = new Product();

if(isset($_POST['update_image'])){

    $product_id =  $_POST['hidden_image'];
    $image_name_new = $_FILES['image_edit']['name'];

    $get_product_details = $product->list_priduct_by_product_id($product_id);

     $mage_with_path = "uploads/".$get_product_details['feature_image'];


    $image_update_array = array(
        'feature_image'=>$image_name_new
    );

    // add new image to the database

    move_uploaded_file($_FILES['image_edit']['tmp_name'],"uploads/".$image_name_new);
    $add_image = $product->update_new_product($image_update_array,$product_id);
}
    if(isset($_GET['cat_id']) && $_GET['cat_id'] != '') {
        $product_details_default = $product->select_product_by_cat_id($_GET['cat_id']);
        $category_each_name = $cat->get_cat_name_by_cat_id($_GET['cat_id']);
    } else {
        $product_details_default = $product->select_product_by_cat_id($category_details[0]['category_id']);
        $category_each_name = $cat->get_cat_name_by_cat_id($category_details[0]['category_id']);
    }
if(isset($_POST['today_start_cash'])){

    $date = date('Y/m/d');
    $to_date =  date('Y-m-d');
    $cash_obj = new CashFeed();
    $get_cash = $cash_obj->today_cash($date);
    $today_start_cash = $get_cash['cash_amount'];


    $get_card_cash = $cash_obj->report_today_end_cash($date);
    $today_end_cash =  $get_card_cash['Total'];
    $sales_details =  $order_obj->get_daily_sales($to_date);
    $total_sale_end = $sales_details['Total'];

    header("Location:Test.php?today_cash_start=$today_start_cash&today_end_cash=$today_end_cash&total_sale=$total_sale_end");


}

if(isset($_POST['week_start_cash'])){

    $date = date('Y-m-d');
    $date_before_week_2  = strtotime("-7 day");
    $date_before_week = date('Y-m-d', $date_before_week_2);
    $cash_obj = new CashFeed();
    //$get_cash = json_encode($cash_obj->week_cash($date,$date_before_week));
    $get_cash = $cash_obj->week_cash($date,$date_before_week);

    //$get_card_cash = json_encode($cash_obj->report_week_end_cash($date,$date_before_week));
    //$total_sales_week = json_encode($order_obj->get_duration_sales($date,$date_before_week));
    $get_card_cash = $cash_obj->report_week_end_cash($date,$date_before_week);
    $total_sales_week = $order_obj->get_duration_sales($date,$date_before_week);

    $data_print_week_cash =array();
    $k =0;
    foreach($get_cash as $each_cash) {


        $data_print_week_cash[$each_cash['feed_time']][$k]['feed_type'] = 'In';

        $data_print_week_cash[$each_cash['feed_time']][$k]['amount'] = $each_cash['cash_amount'];
        $k++;
    }
    foreach($get_card_cash as $each_end_cash) {
        $data_print_week_cash[$each_end_cash['feed_time']][$k]['feed_type'] = 'Out';
        $data_print_week_cash[$each_end_cash['feed_time']][$k]['amount'] = $each_end_cash['Total'];
        $k++;
    }

    foreach($total_sales_week as $sales_week_each_cash) {
        $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['feed_type'] = 'Sale';
        $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['amount'] = $sales_week_each_cash['Total'];
        $k++;

    }


    $data_print_week_cash_encoded = json_encode($data_print_week_cash);

    header("Location:Test.php?week_rep=true&week_cash_start=$data_print_week_cash_encoded");

}

if(isset($_POST['Month_Report'])){
    $month = $_POST['month'];

    $cash_obj = new CashFeed();
    //$get_cash = json_encode($cash_obj->week_cash($date,$date_before_week));
    $get_cash = $cash_obj->month_s_cash($month);


    //$get_card_cash = json_encode($cash_obj->report_week_end_cash($date,$date_before_week));
    //$total_sales_week = json_encode($order_obj->get_duration_sales($date,$date_before_week));
    $get_card_cash = $cash_obj->report_month_end_cash($date);
    $total_sales_week = $order_obj->get_duration_sales_month($date);

    $data_print_week_cash =array();
    $k =0;
    foreach($get_cash as $each_cash) {


        $data_print_week_cash[$each_cash['feed_time']][$k]['feed_type'] = 'In';

        $data_print_week_cash[$each_cash['feed_time']][$k]['amount'] = $each_cash['cash_amount'];
        $k++;
    }
    foreach($get_card_cash as $each_end_cash) {
        $data_print_week_cash[$each_end_cash['feed_time']][$k]['feed_type'] = 'Out';
        $data_print_week_cash[$each_end_cash['feed_time']][$k]['amount'] = $each_end_cash['Total'];
        $k++;
    }

    foreach($total_sales_week as $sales_week_each_cash) {
        $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['feed_type'] = 'Sale';
        $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['amount'] = $sales_week_each_cash['Total'];
        $k++;

    }


    $data_print_month_cash_encoded = json_encode($data_print_week_cash);

    header("Location:Test.php?month_report=true&week_cash_start=$data_print_month_cash_encoded");


}

if(isset($_POST['today_sale_report'])){

    $report_date = date('Y-m-d');
    $get_data =  $order_obj->get_daily_sales($report_date);

    $total_sales_today = $get_data['Total']; // today sales income

    $get_today_orders =  $order_obj->get_today_orders($report_date);

    $report_detail = array();
    foreach($get_today_orders as $each_order){
        $each_order_id = $each_order['order_id'];

        $each_order_total = $each_order['order_total'];

        $get_order_details = $order_obj->order_inside_details($each_order_id);

        foreach($get_order_details as $each_product){
            $data_load_array = array();
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            array_push($report_detail,$data_load_array);
        }
    }


    $the_detail_send_array = json_encode($report_detail);

    header("Location:Test.php?sale_report_today=true&detail_array=$the_detail_send_array&Total=$total_sales_today");
}

if(isset($_POST['Month__sale_Report'])){
    $selected_month = $_POST['month_sale'];

    $month_sales = $order_obj->month_sales_report($selected_month);
    $month_sale_count = $order_obj->month_sales_count($selected_month);
    $final_count = $month_sale_count['TM'];


    $report_detail = array();
    foreach($month_sales as $each_order){
        $each_order_id = $each_order['order_id'];

        $each_order_total = $each_order['order_total'];

        $get_order_details = $order_obj->order_inside_details($each_order_id);
        error_reporting('0');
        foreach($get_order_details as $each_product){
            $data_load_array = array();
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            array_push($report_detail,$data_load_array);
        }
    }
    $the_detail_send_array = json_encode($report_detail);
    $_SESSION['data_month'] = $the_detail_send_array ;
    header("Location:Test.php?sale_month_report=true&Total=$final_count");


}
if(isset($_POST['week_sale_report'])){

    $date = date('Y/m/d');
    $date_before_week_2  = strtotime("-7 day");
    $date_before_week = date('Y/m/d', $date_before_week_2);

    $get_data =  $order_obj->get_week_sales($date,$date_before_week);

    $total_sales_week = $get_data['Total'];

    $get_today_orders =  $order_obj->get_week_orders($date,$date_before_week);

    $report_detail = array();
    foreach($get_today_orders as $each_order){
        $each_order_id = $each_order['order_id'];

        $each_order_total = $each_order['order_total'];

        $get_order_details =  $order_obj->order_inside_details($each_order_id);
        error_reporting('0');
        foreach($get_order_details as $each_product){

            $data_load_array = array();
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            array_push($report_detail,$data_load_array);

        }
    }


    $the_detail_send_array = json_encode($report_detail);
    $_SESSION['data'] = $the_detail_send_array ;
    header("Location:Test.php?sale_report=true&Total=$total_sales_week");


}
if(isset($_POST['INVENTORY_REPORT'])){
    $product_obj = new Product();
    $product_inventory = $product_obj->list_priduct_inventory();

    $_SESSION['product_inventory'] = json_encode($product_inventory);
    header("Location:Test.php?product_inventory=true");
}
if(isset($_POST['week_sale_report'])){

    $date = date('Y/m/d');
    $date_before_week_2  = strtotime("-7 day");
    $date_before_week = date('Y/m/d', $date_before_week_2);

    $get_data =  $order_obj->get_week_sales($date,$date_before_week);

    $total_sales_week = $get_data['Total'];

    $get_today_orders =  $order_obj->get_week_orders($date,$date_before_week);

    $report_detail = array();
    foreach($get_today_orders as $each_order){
        $each_order_id = $each_order['order_id'];

        $each_order_total = $each_order['order_total'];

        $get_order_details =  $order_obj->order_inside_details($each_order_id);
        error_reporting('0');
        foreach($get_order_details as $each_product){

            $data_load_array = array();
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            array_push($report_detail,$data_load_array);

        }
    }


    $the_detail_send_array = json_encode($report_detail);
    $_SESSION['data'] = $the_detail_send_array ;
    header("Location:Test.php?sale_report=true&Total=$total_sales_week");


}
    if(isset($_POST['submit'])){
        $product_name = $_POST['product_name'];
        $category = $_POST['category'];
        $is_loose = $_POST['is_loose'];
        $product_description = $_POST['product_description'];
        $price = $_POST['price'];
        $product_qty = $_POST['product_qty'];
        $image_name = $_FILES['file']['name'];
        $date = date("Y-m-d") ;
        $file_tmp =$_FILES['file']['tmp_name'];
        if($is_loose == 1) {
            $loose_add = 'T';
        } else {
            $loose_add = 'F';
        }
        $product_array = array(
            'category_id' =>$category,
            'product_name' =>$product_name,
            'price' =>$price,
            'product_description' => $product_description,
            'feature_image' =>$image_name,
            'created_date' =>$date,
            'status' =>'1',
            'qty' => $product_qty,
            'is_loose' => $loose_add
        );

        $add_product = $product->add_new_product($product_array);
        move_uploaded_file($file_tmp,"uploads/".$image_name);




    }
    if(isset($_POST['submit_cat'])){
        $cat_name_new = $_POST['cat_name_new'];

        $cat_description = $_POST['cat_description'];

        $cat_image = $_FILES['cat_image_new_up']['name'];

        $tmp_name = $_FILES['cat_image_new_up']['tmp_name'];

        if(!empty ($cat_name_new)){

                    $cat_array = array(
                        'category_name' =>$cat_name_new,
                        'category_description' => $cat_description,
                        'cat_order'=>'1',
                        'cat_image'=>$cat_image

                    );
                    $add_catt = $cat->add_cat($cat_array);
                    move_uploaded_file($tmp_name,"uploads/".$cat_image);


        }
    }

    if(isset($_POST['add_user'])){

        $user_name = $_POST['user_name'];
        $user_password = $_POST['password'];
        $user_password_re = $_POST['password_re'];
        $user_email = $_POST['email_user'];
        $user_type = $_POST['type_user'];

        if(!empty($user_name) && !empty($user_password) && !empty($user_password_re)){

            if($user_password == $user_password_re ){
                $password_ready = md5($user_password);



                    $new_user_array = array(
                        'username' =>$user_name,
                        'password'=>$password_ready,
                        'email'=>$user_email,
                        'is_admin'=>$user_type,
                        'last_login'=>date("Y-m-d H:i:s"),
                        'status'=>'1',

                    );

                    $add_a_user = $user->add_new_user($new_user_array);
                    $error_msg = "New User Has Been added Successfully !";


            }
            else{
                $error_msg = "Your Passwords Does Not Match !";
            }


        }
        else{
            $error_msg = "Please Fill All Fields to Continue !";
        }

    }

    if(isset($_POST['edit_cat'])){

        $cat_details = $cat->list_category();
        $cat_new_details = array();

        //updating each row

        foreach($cat_details as $each_cat){
            if(empty($_FILES['cat_image_'.$each_cat['category_id']]['name'])){

                $data_array= array();

                $data_array['category_name'] = $_POST['cat_name_'.$each_cat['category_id']];
                $data_array['cat_order'] = $_POST['cat_order_'.$each_cat['category_id']];

                //array_push($cat_new_details,$data_array);

                $update_each_row = $cat->update_cat($data_array,$each_cat['category_id']);
            }
            else{

                $file_tmp = $_FILES['cat_image_'.$each_cat['category_id']]['tmp_name'];
                $image_name = $_FILES['cat_image_'.$each_cat['category_id']]['name'];
               // $image_name = $_FILES['cat_image_'.$each_cat['category_id']]['name'];
                $data_array= array();

                $data_array['category_name'] = $_POST['cat_name_'.$each_cat['category_id']];
                $data_array['cat_order'] = $_POST['cat_order_'.$each_cat['category_id']];
                $data_array['cat_image'] = $_FILES['cat_image_'.$each_cat['category_id']]['name'];

                //array_push($cat_new_details,$data_array);

                $update_each_row = $cat->update_cat($data_array,$each_cat['category_id']);
                move_uploaded_file($file_tmp,"uploads/".$image_name);
            }


        }


    }

    // Reports Generating Starts Here

    if(isset($_POST['today_start_cash'])){

        $date = date('Y/m/d');
        $to_date =  date('Y-m-d');
        $cash_obj = new CashFeed();
        $get_cash = $cash_obj->today_cash($date);
        $today_start_cash = $get_cash['cash_amount'];


        $get_card_cash = $cash_obj->report_today_end_cash($date);
        $today_end_cash =  $get_card_cash['Total'];
        $sales_details =  $order_obj->get_daily_sales($to_date);
        $total_sale_end = $sales_details['Total'];

        header("Location:Test.php?today_cash_start=$today_start_cash&today_end_cash=$today_end_cash&total_sale=$total_sale_end");


    }

    if(isset($_POST['week_start_cash'])){

        $date = date('Y-m-d');
        $date_before_week_2  = strtotime("-7 day");
        $date_before_week = date('Y-m-d', $date_before_week_2);
        $cash_obj = new CashFeed();
        //$get_cash = json_encode($cash_obj->week_cash($date,$date_before_week));
        $get_cash = $cash_obj->week_cash($date,$date_before_week);

        //$get_card_cash = json_encode($cash_obj->report_week_end_cash($date,$date_before_week));
        //$total_sales_week = json_encode($order_obj->get_duration_sales($date,$date_before_week));
        $get_card_cash = $cash_obj->report_week_end_cash($date,$date_before_week);
        $total_sales_week = $order_obj->get_duration_sales($date,$date_before_week);

        $data_print_week_cash =array();
            $k =0;
            foreach($get_cash as $each_cash) {


               $data_print_week_cash[$each_cash['feed_time']][$k]['feed_type'] = 'In';

                $data_print_week_cash[$each_cash['feed_time']][$k]['amount'] = $each_cash['cash_amount'];
                $k++;
            }
        foreach($get_card_cash as $each_end_cash) {
            $data_print_week_cash[$each_end_cash['feed_time']][$k]['feed_type'] = 'Out';
            $data_print_week_cash[$each_end_cash['feed_time']][$k]['amount'] = $each_end_cash['Total'];
            $k++;
        }

       foreach($total_sales_week as $sales_week_each_cash) {
           $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['feed_type'] = 'Sale';
           $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['amount'] = $sales_week_each_cash['Total'];
           $k++;

        }


        $data_print_week_cash_encoded = json_encode($data_print_week_cash);

        header("Location:Test.php?week_rep=true&week_cash_start=$data_print_week_cash_encoded");

    }

    if(isset($_POST['Month_Report'])){
        $month_name = $_POST['month'];

        $cash_obj = new CashFeed();
        $get_cash_month =  json_encode($cash_obj->month_start_cash_report($month_name));
        $get_cash_month_end =  json_encode($cash_obj->month_end_cash_report($month_name));
        header("Location:Test.php?month_report=true&month_data=$get_cash_month&get_cash_month_end=$get_cash_month_end");


    }

    if(isset($_POST['today_sale_report'])){
        $month_name = $_POST['month_sale'];
        $report_date = date('Y/m/d');
        $get_data =  $order_obj->get_daily_sales($report_date);

        $total_sales_today = $get_data['Total'];

        $get_today_orders =  $order_obj->get_today_orders($report_date);

        $report_detail = array();
        foreach($get_today_orders as $each_order){
            $each_order_id = $each_order['order_id'];

            $each_order_total = $each_order['order_total'];

            $get_order_details = $order_obj->order_inside_details($each_order_id);

            foreach($get_order_details as $each_product){
                $data_load_array = array();
                $data_load_array['order_id'] = $each_product_id;

                $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
                $Price_original = $product_data['price'];
                $each_qty = $each_product['no_of_products'];
                $order_price = $each_qty*$Price_original ;
                $data_load_array['product_name'] = $product_data['product_name'];
                $data_load_array['qty'] = $each_product['no_of_products'];
                $data_load_array['order_total'] = $order_price;

                array_push($report_detail,$data_load_array);
            }
        }


            $the_detail_send_array = json_encode($report_detail);

        header("Location:Test.php?sale_report_today=true&detail_array=$the_detail_send_array&Total=$total_sales_today");
    }
if(isset($_POST['date_sale_Report'])){

    $report_date = $_POST['report_date'];
    $get_data =  $order_obj->get_daily_sales($report_date);

    $total_sales_today = $get_data['Total'];

    $get_today_orders =  $order_obj->get_today_orders($report_date);

    $report_detail = array();
    foreach($get_today_orders as $each_order){
        $each_order_id = $each_order['order_id'];

        $each_order_total = $each_order['order_total'];

        $get_order_details = $order_obj->order_inside_details($each_order_id);

        foreach($get_order_details as $each_product){
            $data_load_array = array();
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            array_push($report_detail,$data_load_array);
        }
    }


    $the_detail_send_array = json_encode($report_detail);

    header("Location:Test.php?sale_report_today=true&detail_array=$the_detail_send_array&Total=$total_sales_today");
}
    if(isset($_POST['Month__sale_Report'])){
          $selected_month = $_POST['month_sale'];

            $month_sales = $order_obj->month_sales_report($selected_month);
            $month_sale_count = $order_obj->month_sales_count($selected_month);
            $final_count = $month_sale_count['Total'];


        $report_detail = array();
        foreach($month_sales as $each_order){
            $each_order_id = $each_order['order_id'];

            $each_order_total = $each_order['order_total'];

            $get_order_details = $order_obj->order_inside_details($each_order_id);
            error_reporting('0');
            foreach($get_order_details as $each_product){
                $data_load_array = array();
                $data_load_array['order_id'] = $each_product_id;

                $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
                $Price_original = $product_data['price'];
                $each_qty = $each_product['no_of_products'];
                $order_price = $each_qty*$Price_original ;
                $data_load_array['product_name'] = $product_data['product_name'];
                $data_load_array['qty'] = $each_product['no_of_products'];
                $data_load_array['order_total'] = $order_price;

                array_push($report_detail,$data_load_array);
            }
        }
        $the_detail_send_array = json_encode($report_detail);
        $_SESSION['data_month'] = $the_detail_send_array ;
        header("Location:Test.php?sale_month_report=true&Total=$final_count");


    }

?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Demo</title>

        <link rel="stylesheet" href="css/idangerous.swiper.css">
        <link type="text/css" rel="stylesheet" href="css/demo.css" />
        <link type="text/css" rel="stylesheet" href="css/jquery.mmenu.all.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap.css" />

        <script type="text/javascript" src="js/jquery-2.1.3.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.mmenu.min.all.js"></script>
    <script type="text/javascript" src="js/product.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>


    <style>
        body {
            background: #fff;
            font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
            font-size: 14px;
            color:#000;
            margin: 0;
            padding: 0;
        }
        .swiper-container {
            width: 100%;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 300px;
            height: 300px;
        }
    </style>

  <style>
/* Demo Styles */
body {
  margin: 0;
  font-family: Arial, Helvetica, sans-serif;
  font-size: 13px;
  line-height: 1.5;
  background-color: #000000;
}
.device {
  position: relative;
  margin: 10px auto;
  height: 600px;
  padding:100px 40px;
  background:#000;
}
.swiper-container {
  height: 700px;
  color: #fff;
  text-align: center;
}

.swiper-slide {


          background-position: center;
          background-size: cover;
          width: 300px;
          height: 300px;



    height: 100%;
    opacity: 1;
    -webkit-transition: 300ms;
    -moz-transition: 300ms;
    -ms-transition: 300ms;
    -moz-transform: scale(0.8);
    -o-transition: 300ms;
    transition: 300ms;
    -webkit-transform: scale(0);
    -moz-transform: scale(0);
    -ms-transform: scale(0);
    -o-transform: scale(0);
    transform: scale(0);
}


      .swiper-slide-visible {
        opacity: 1;
        -webkit-transform: scale(0.8);
        -ms-transform: scale(0.8);
        -o-transform: scale(0.8);
        transform: scale(0.8);
        z-index: 1000;

          margin-right: -8%;
          margin-left: 8%;
          position:relative;





}
.swiper-slide-active {
  top: 0;
  opacity: 1;
    z-index: 999999;
  -webkit-transform: scale(1);
  -moz-transform: scale(1);
  -ms-transform: scale(1);
  -o-transform: scale(1);
  transform: scale(1);
    position: relative;






}
.red-slide {
  background: #ca4040;
}
.blue-slide {



}


.orange-slide {
  background: #ff8604;
}
.green-slide {
  background: #49a430;
}
.pink-slide {
  background: #973e76;
}
.swiper-slide .title {
  font-style: italic;
  font-size: 42px;
  margin-top: 30px;
  margin-bottom: 0;
  line-height: 45px;
}
.pagination {
  position: absolute;
  z-index: 20;
  left: 0px;
  width: 100%;
  text-align: center;
  bottom: 5px;
}
.swiper-pagination-switch {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 8px;
  background: #aaa;
  margin-right: 8px;
  cursor: pointer;
  -webkit-transition: 300ms;
  -moz-transition: 300ms;
  -ms-transition: 300ms;
  -o-transition: 300ms;
  transition: 300ms;

  position: relative;
  top: -50px;
}
.swiper-visible-switch {
  opacity: 1;
  top: 0;
  background: #aaa;
}
.swiper-active-switch {
  background: #fff;
}
.modal.in .modal-dialog {
    z-index: 10000 !important;
}
.small-Visible{
    -webkit-transform: scale(0.6);
    -ms-transform: scale(0.6);
    -o-transform: scale(0.6);
    transform: scale(0.6);
    margin-right: -10%;
    margin-left: 8%;
    z-index: -1;

    position:relative;
}
.swiper-wrapper{
  padding-left: 329.5px !important;
}

      body {
          font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";

      }


</style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">


</head>
<body>



<div class="container" id="customer_top_bar" style="display:none;position:fixed;top:0;width:90%;z-index:100;color:white">
    <div class="row">
        <div class="col-md-12">
            <nav class="navbar navbar-default" role="navigation">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">

                    <a class="navbar-brand" href="index.php">Point of Sales</a>
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse collapse_cus" id="bs-example-navbar-collapse-1">

                    <ul class="nav navbar-nav navbar-right">

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" > <span id="cart_total_display2"> Cart Total : Rs.<?php echo $cart_total; ?> </span> <b class="caret"></b></a>
                            <ul  class="dropdown-menu" style="padding: 15px;min-width: 250px; color: #000000">

                                <li style="color: #000000">

                                    <a class="" href="checkout.php">Checkout</a>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>

            </nav>
        </div>
    </div>
</div>
<div id="">

    <div class="content">
        <div class="device">


            <div class="swiper-container">
                <div id="slider_divs" class="swiper-wrapper">

                    <?php
                  //  print_r($product_details_default);
                    if(!empty($product_details_default)) {
                            $x = 0;
                        foreach($product_details_default as $value){
                            $product_qty = $value['qty'];
                            $is_loos = $value['is_loose'];
                            $x++;
                            echo "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide sl-".$x." \" style=\"width: 600px; height: 600px;\">";

                            echo "<div id='image_".$value['product_id']."' class='image_class' style='padding-top:0px; border:2px solid cdcdcd#;'><img style='padding: 1px;-webkit-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    -moz-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);'  src=\"uploads/".$value['feature_image']."\">

    <div align='center' style='text-align: center; width: 167% '>".$value['product_description']."</div>

    ";
                            if($_SESSION['user_level'] == 1) {?>
                              <div>

                                  <a href="#" id="<?php echo $value['product_id']  ; ?>" style="position: absolute; right: 48px; top: 5px;" class="edit_product" data-toggle="modal" data-target="#added_car">
                                      <span class="glyphicon glyphicon-pencil btn btn-warning"></span>
                                  </a>
                                  <a href='#' id="<?php echo $value['product_id']  ;?>" class='edit_photo' onclick="edit_photo(<?php echo $value['product_id']  ;?>)" style="position: absolute; right: -41px; top: 5px;">
                                      <span class='glyphicon glyphicon-picture btn btn-danger'></span>
                                  </a>

                             <a href='#' id="<?php echo $value['product_id']  ;?>"  style='position: absolute; right: 5px; top: 5px;' class='delete_product' onclick="delete_product(<?php echo $value['product_id']  ;?>)">
                                    <span class='glyphicon glyphicon-minus  btn btn-danger'></span>
                             </a>



                            </div>
               <?php   }


                echo "   </div>";

                          echo "<div style='padding-top: 0px;'>
                                <div  style='display:none; background-color: #333333;
                                     width: 606px; height: 80px;padding-top:20px;margin-left:0px;' class='details_div' id='product_detail_".$value['product_id']."'>
                                    <form id = 'product_form_$value[product_id]'  method ='post' action = ''>";


                                   if($is_loos == 'T'){
                                       echo "  <div class=\"col-md-3\">
                                                <span style='color:#ffffff;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_size\" id=\"product_size\">

                                                                <option value='0.025'>25g - Rs :".number_format((0.025)*($value['price']),2)."</option>
                                                                <option value='0.05'>50g - Rs :".number_format((0.05)*($value['price']),2)."</option>
                                                                <option value='0.08'>80g - Rs :".number_format((0.08)*($value['price']),2)."</option>
                                                                <option selected='selected' value='0.1'>100g - Rs :".number_format((0.1)*($value['price']),2)."</option>
                                                                <option value='0.25'>250g- Rs :".number_format((0.25)*($value['price']),2)."</option>
                                                                <option value='0.5'>500g- Rs :".number_format((0.5)*($value['price']),2)."</option>
                                                                <option value='1'>1Kg- Rs :".number_format($value['price'],2)."</option>

                                                           </select>
                                                   </div>
                                        </div>";
                                       echo "  <div class=\"col-md-2\">

                                                   <div  style='padding-left:20px; width:200px'><h4>".$value['product_name']."</h4>
                                                   </div>
                                        </div>";
                                   }

                                   else {
                                       echo "<div class=\"col-md-2\">

                                                            <span style='color:#ffffff;font-weight:bold;'>&nbsp;</span>

                                                            <div style='font-size:24px;color:white;font-weight:bold;'>Rs :".$value['price']." </div>

                                                   </div>";
                                       echo "  <div class=\"col-md-2\">
                                                <span style='color:#ffffff; font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_qty\" id=\"product_qty\">";

                                                                for($a=1;$a<=$product_qty;$a++){
                                                                    echo "<option value=".$a.">".$a."</option>";
                                                                    if($a == 5)
                                                                        break;
                                                                }



                                                           echo "</select>
                                                   </div>
                                        </div>";
                                       echo "  <div class=\"col-md-2\">

                                                   <div  style='padding-right:10px; width:200px'><h4>".$value['product_name']."</h4>
                                                   </div>
                                        </div>";
                                       echo "  <div class=\"col-md-2\">

                                                   <div>".$value['product_description']."
                                                   </div>
                                        </div>";
                                   }
                                   echo " <div class='price col-md-3 pull-right'>

                                                <button style='margin-left:20%; margin-top: 0px;  padding-top: 0px;'
   type='button' value='' title='Add to Cart' class='addtocart-button cart-click pull-right' onclick='add_to_cart($value[product_id])'>Add to&nbsp;&nbsp;<img src='img/2772.png' width='16' height'16'> <span>&nbsp;</span></button>

                                     </div>
                                </div>
                                </div>


                        </form>
                            ";
                            echo "</div>";


                        }
                    } else {
                        echo "No Products available";
                    }
                    ?>
                </div>
            </div>

        </div>
    </div>



    <br>
    <nav class="navbar navbar-default" role="navigation" style="margin-top: 50px;border:none;color:#ffffff;">
        <!-- Brand and toggle get grouped for better mobile display -->

        <div class="navbar-header">

            <a id="asd" href="#menu"><img style="margin-top: -800px;margin-left:-11px" src="img/handler.png">    </a>


            <!--            <a class="navbar-brand" href="index.php">Point of Sales</a>-->
        </div>
        <?php
        $user_details = $user->getUserByID($_SESSION['user_id']);
        $user_status_id = $user_details['is_admin'];

        // getting daily sales
        $date_today = date("Y-m-d");
        $daily_sales = $order_obj->get_daily_sales($date_today);
        $total_of_the_day =  $daily_sales['Total'];


//        foreach($user_details as $user_status){
//            $user_status_id = $user_status['is_admin'];
//        }

        ?>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div style="z-index: 20000000000; bottom: 0px;background-color:#333333;color:blue; font-size: 17px;margin-top:25px" class="collapse navbar-collapse collapse_cus" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav navbar-right">
               <?php  if($user_status_id == 1) {?>

                    <?php
                    if($user_status_id == '1'){

                        if($user_status_id == $_SESSION['user_level'] ){?>
                            <li>
                                <a id="cat_name_bar" class="pDetails" data-toggle="modal" style="color:#ffffff;"><?php echo ucwords(strtolower($category_each_name['category_name'])); ?></a>
                            </li>
                            <li>
                                <a href="remove_cat.php" class="pDetails"   style="color:#ffffff;">Remove Category</a>
                            </li>
                            <li>
                                <a href="Sort_cat.php" class="pDetails"   style="color:#ffffff;">Sort Category</a>
                            </li>
                            <li>
                                <a class="pDetails" data-toggle="modal" style="color:#ffffff;"> Today :<?php echo $total_of_the_day ; ?></a>
                            </li>
                            <li>
                                <a  href="javascript:history.go(0)" id="change_to_pos"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Change to POS</a>
                            </li>
                            <li>
                                <a data-target="#cat_add" href="#" id="create_product"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Create Category </a>
                            </li>
                            <li>
                                <a data-target="#cat_order"   href="#" id="cat_order_ed"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Edit Category </a>
                            </li>
                            <li>
                                <a data-target="#product_add" href="#" id="add_cat"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Create Product</a>
                            </li>

                       <?php  }
                        else{?>
                            <li>
                                <a class="pDetails" data-toggle="modal" style="color:#ffffff;"> Today :<?php echo $total_of_the_day ; ?></a>

                            </li>
                            <li>
                                <a  href="javascript:history.go(0)" id="change_to_admin"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Change to Admin</a>

                            </li>

                       <?php  }

                    }

                    ?>



                <?php } ?>
                <li class="btn-group dropup">
<!--                    <a  href="#" class="dropdown-toggle" data-toggle="dropdown" ><span id="cart_total_display" style="color:#ffffff;"> Cart Total:</span> <b class="caret"></b></a>-->
<!---->
<!--                    <ul class="dropdown-menu" style="padding:15px;min-width:250px;">-->
<!---->
<!--                        <li>-->
<!--                            <a class="" href="checkout.php" style="color:#ffffff;">Checkout</a>-->
<!--                        </li>-->
<!---->
<!--                    </ul>-->
                    <a class="" href="checkout.php"><input id="cart_btn_total" style="margin-top: -5px;background-color: #164293 !important;border: 0px;width: 200px;" type="button" class="btn-success btn btn-block" value="Cart Total :00.00"></a>
                </li>
               <?php
                $today =  $date = date('Y/m/d');
                $cash = $cash_obj->today_cash($today);
                $end_cash = $cash_obj->end_cash_total($today);
                $really_cash = $cash['cash_amount'];
                $really_end_cash = $end_cash['Total'];

                ?>
                <li><a class="cash_feed_class" data-toggle="modal" data-target="#cash_feed_box" href="#" style="color:#ffffff;">
                       Start Cash:<?php  echo $really_cash ;?>
                    </a>
                </li>
                <li>
                    <a class="cash_feed_class" data-toggle="modal" data-target="#cash_feed_box_end" href="#" style="color:#ffffff;">
                        End Cash:<?php  echo $really_end_cash ;?>
                    </a>
                </li>
                <!--reports-->
                <li class="btn-group dropup">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;">Reports</a>
                    <ul class="dropdown-menu" >
                        <li>
                            <a href="" id="" >Product History</a>
                        </li>
                        <li>
                            <a hFref="#" id="" >Product List</a>
                        </li>
                        <li>
                            <a href="#" id="" data-toggle="modal" data-target="#cash_reports">Cash Reports</a>
                        </li>
                        <li>
                            <a href="#" id="" data-toggle="modal" data-target="#sale_reports">Sales Report</a>
                        </li>
                    </ul>
                </li>
                <li class="btn-group dropup">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"  style="color:#ffffff;"> User :  <b class="caret"></b></a>
                    <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;">

                        </li>
                        <li>
                            <?php if(isset($_SESSION['user_id'])){?>
                                                                 <a class=""  href="logout.php"> <span class="glyphicon glyphicon-user"></span>  Sign out</a>

                                                              <?php  } ?>

                        </li>
                <li>
                    <?php if(isset($_SESSION['user_id'])){?>
                        <a class=""  href="logout_session.php"> <span class="glyphicon glyphicon-user"></span>  Sign out With Work</a>

                    <?php  } ?>

                </li>
                        <li>
                            <?php if($_SESSION['user_level']== 1){ ?>
                                <a id="add_user" class="pDetails"
                                   href="#">Create a User</a>

                            <?php  } ?>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <nav id="menu">
        <ul>
            <?php
            foreach($category_details as $key=>$value){
                echo "<a href='index.php?cat_id=".$value['category_id']."'><img width='50' height = '50' src = 'uploads/".$value['cat_image']."'></a><li style='font-size: 15px;color: #ffffff;' id=\"".$value['category_id']."\"><a href='index.php?cat_id=".$value['category_id']."'>".$value['category_name']."</a></li>";
            }
            ?>
        </ul>
    </nav>
</div>


<script src="js/idangerous.swiper.min.js"></script>
  <script>

      function delete_product(product_id) {



          bootbox.confirm("Are you sure want to delete ?", function(result) {

              if(result) {

                  $.ajax({
                      url: 'extra_function.php',
                      dataType: 'json',
                      data: {'delete_product_by_id':true,'product_id': product_id},
                      type: 'post',
                      success: function(data)
                      {
                          $('.delete_product_success_message').show();
                          window.location.href='index.php';
                      }
                  });

              }

          });


      }

      var mySwiper = new Swiper('.swiper-container',{
        paginationClickable: true,
        centeredSlides: true,
        slidesPerView: 5,
        watchActiveIndex: true
      });
      $(function() {
          $('nav#menu').mmenu();
      });

      function reinitSwiper(swiper) {
          setTimeout(function () {
              swiper.reInit();
          }, 500);
      }

      $('#slider_divs .swiper-visible-switch').click(function(){
          this.attr('disabled','disabled');
      });

      $('#slider_divs').click(function(event){
          event.stopPropagation();
      });


      function add_to_cart(product_id) {
          var val = product_id ;

          var item_data = $('#product_form_'+product_id).serializeArray();
          console.log(item_data);
          var product_qty = "";
          var product_size = "";
          $.each( item_data, function( key, value ) {
              if(value.name == 'product_size') {
                  product_size = value.value;
              }
              if(value.name == 'product_qty') {
                  product_qty = value.value;
              }

          });

          if(product_qty == ''){
              var cart_product_id = product_id;
              $.ajax({
                  url: "extra_function.php",
                  type: "POST",
                  cache: false,
                  async:false,
                  data: {add_to_cart_detail_size:true,product_id:cart_product_id,product_size:product_size},
                  success: function(theResponse){
                      var theResponse = $.parseJSON(theResponse);
                      var cart_total = 0;
                      $.each(theResponse, function(index, value) {

                          cart_total += (value.product_price*value.product_size);
                      });
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $('#added_cart').modal('show');


                  }
              });
          }
          else{
              var cart_product_id = product_id;
              $.ajax({
                  url: "extra_function.php",
                  type: "POST",
                  cache: false,
                  async:false,
                  data: {add_to_cart_detail_qty:true,product_id:cart_product_id,product_qty:product_qty},
                  success: function(theResponse){
                      var theResponse = $.parseJSON(theResponse);
                      var cart_total = 0;
                      $.each(theResponse, function(index, value) {

                          cart_total += (value.product_price*value.product_qty);
                      });
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $('#added_cart').modal('show');


                  }
              });
          }



      }
  </script>
<script type="text/javascript">
    $(document).ready(function(){
            $('#month_start_cash').click(function(){
                $('#name').modal('show');
                $('#cash_reports').modal('hide');
            })
            $( "#report_date" ).datepicker({ dateFormat: 'yy-mm-dd' });

        $('#add_user').click(function(){
            $('#master_code').modal('show');

        });

        $('#master_code_btn').click(function(){
            $.ajax({
             url: 'extra_function.php',
             data: {'master_code':true},
             type: 'post',
             success: function(data)
              {
                  var code = data ;

                  var inputcode = $('#master_code_txt').val();

                  if(code == inputcode){
                      $('#master_code').modal('hide');
                      $('#modal_add_user').modal('show');
                  }
                  else{
                      alert('Master Code is Incorrect !');
                  }

              }
         });




//
        });


        $('#month_sales_report').click(function(){
            $('#sales').modal('show');
            $('#sale_reports').modal('hide');
        });
        $('#date_sales_report').click(function(){
            $('#sales_date').modal('show');
            $('#sale_reports').modal('hide');
        });

        $('#get_date_report').click(function(){
            var date = $('#report_date').val();




        });
//        $('#today_start_cash').click(function(){
//            $.ajax({
//                url: 'test.php',
//
//                data: {'today_start_cash':true},
//                type: 'post',
//                success: function(data)
//                {
//
//
//                }
//            });
//        });


        $('#change_to_admin').click(function(){
            $.ajax({
                url: 'extra_function.php',
                dataType: 'json',
                data: {'change_to_admin':true},
                type: 'post',
                success: function(data)
                {
                    window.location('index.php');

                }
            });
        });
        $("#change_to_pos").click(function(){
            $.ajax({
                url: 'extra_function.php',
                dataType: 'json',
                data: {'change_to_pos':true},
                type: 'post',
                success: function(data)
                {
                    window.location('index.php');

                }
            });
        });

        $(".product-edit-submit-form_awesome").on('click',function(e) {

            var price           = $('.edit_prduct_price').val();

            var name = $('.edit_prduct_name').val();
            var description = $('.edit_prduct_des').val();

            var product_qty     = parseInt($('.edit_prduct_quantity').val());
            var product_id     = $('.edit_product_id').val();

            var add_new_prduct_quantity = parseInt($('.add_new_prduct_quantity').val());
            var cal_operator =   $('input:radio[name=cal_operator]').filter(":checked").val()

            if (typeof cal_operator === "undefined") {
                var cal_operator = "1";
            }
            var error_messages = [];



            if(price == ""){
                error_messages[2]=true;
                $('.edit_prduct_price').addClass('error_msg');
            }else if(!$.isNumeric($('.edit_prduct_price').val())){
                error_messages[2]=true;
                $('.edit_prduct_price').addClass('error_msg');
            }else{
                $('.edit_prduct_price').removeClass('error_msg');
            }

            if(error_messages.length === 0){


                $.ajax({
                    url: 'extra_function.php',
                    dataType: 'json',
                    data: {'edit_product_details':true,'product_des':description,'product_id':product_id,'price': price,'name':name,'product_qty':product_qty,'add_new_prduct_quantity':add_new_prduct_quantity,'cal_operator':cal_operator},
                    type: 'post',		// To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
                    success: function(data)  		// A function to be called if request succeeds
                    {



                        $('#product_edit_pop').modal('hide');
                        window.location.href='index.php';

                    }
                });

            }

        });




//        $('#close_new_user').click(function(){
//
//            $('cat_order_edit').modal('hide');
//        });

        /*$('.image_class').click(function(){
            alert('ttt');
            $('.details_div').hide('slow');
            //alert($('.swiper-wrapper').find('.image_class').parent().prop('className'));
            var bb = this.id.replace("image_", "product_detail_");

            $('#'+bb).show('slow');
        });*/
        $('sl_id#1 div .details_div').show();
         var total_cash_in =0;
        var  last_cash_in = 0
        $('#cash_feed_add').click(function(){


            // calculation goes here
            total_cash_in +=  parseFloat($('#cash_amount').val());

           // var total = cash_amount_value + entered_cash ;
            last_cash_in = parseFloat($('#cash_amount').val());
            $('#main_total').html(total_cash_in);

            $('#cash_amount').val('');

        });
        $('#cash_feed_reset').click(function(){



            $('#main_total').html((total_cash_in - last_cash_in));


        });
        $('#cash_feed_clear').click(function(){
            total_cash_in =0;
            last_cash_in = 0


            $('#main_total').html(0);


        });

        $("#cash_feed_save").click(function(){

            var cash_amount_value = $('#main_total').text();


            var hidden_user = $('#hidden_user').val();

            var d  = new Date();

            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + '/' +
                (month<10 ? '0' : '') + month + '/' +
                (day<10 ? '0' : '') + day;
            var date_today = output ;
            var cash_feed_array = {
                cash_amount:cash_amount_value,
                user_id:hidden_user,
                feed_time:date_today,
                end_or_start:1,
                cash_or_card:1
            } ;

                $.ajax({
                    url: "extra_function.php",
                    type: "POST",
                    cache: false,
                    async:false,
                    data: {add_cash_feed:true,cash_feed_array:cash_feed_array},
                    success: function(theResponse){
                        $('#cash_feed_box').modal('toggle');
                        $( "#success_massage" ).show();
                        $( "#success_massage" ).fadeOut(6000 );                            }
                });



        });
        var total_cash_in_end =0;
        var  last_cash_in_end = 0
        $('#cash_feed_add_end').click(function(){


            // calculation goes here
            total_cash_in_end +=  parseFloat($('#main_total_cash_end').val());

            // var total = cash_amount_value + entered_cash ;
            last_cash_in_end = parseFloat($('#main_total_cash_end').val());
            $('#main_total_end_cash_lable').html(total_cash_in_end);

            $('#main_total_cash_end').val('');

        });
        $('#cash_feed_reset_end').click(function(){



            $('#main_total_end_cash_lable').html((total_cash_in_end - last_cash_in_end));


        });
        $('#cash_feed_clear_end').click(function(){
            total_cash_in_end =0;
            last_cash_in_end = 0


            $('#main_total_end_cash_lable').html(0);


        });
        var total_cash_in_end_card =0;
        var  last_cash_in_end_card = 0
        $('#cash_feed_add_end_card').click(function(){


            // calculation goes here
            total_cash_in_end_card +=  parseFloat($('#main_total_cash_end_card').val());

            // var total = cash_amount_value + entered_cash ;
            last_cash_in_end_card = parseFloat($('#main_total_cash_end_card').val());
            $('#main_total_end_card_lable').html(total_cash_in_end_card);

            $('#main_total_cash_end_card').val('');

        });
        $('#cash_feed_reset_end_card').click(function(){



            $('#main_total_end_card_lable').html((total_cash_in_end_card - last_cash_in_end_card));


        });
        $('#cash_feed_clear_end_card').click(function(){
            total_cash_in_end_card =0;
            last_cash_in_end_card = 0


            $('#main_total_end_card_lable').html(0);


        });
        $("#cash_feed_save_end").click(function(){


            var cash_amount = $('#main_total_end_cash_lable').text();
            var Card_amount = $('#main_total_end_card_lable').text();
            var hidden_user = $('#hidden_user').val();

            var d  = new Date();

            var month = d.getMonth()+1;
            var day = d.getDate();

            var output = d.getFullYear() + '/' +
                (month<10 ? '0' : '') + month + '/' +
                (day<10 ? '0' : '') + day;
            var date_today = output ;
            var cash_feed_array_cash= {
                cash_amount:cash_amount,
                user_id:hidden_user,
                feed_time:date_today,
                end_or_start:0,
                cash_or_card:1
            } ;

            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {add_cash_feed:true,cash_feed_array:cash_feed_array_cash},
                success: function(theResponse){
                    var cash_feed_array_card= {
                        cash_amount:Card_amount,
                        user_id:hidden_user,
                        feed_time:date_today,
                        end_or_start:0,
                        cash_or_card:0
                    } ;
                    $.ajax({
                        url: "extra_function.php",
                        type: "POST",
                        cache: false,
                        async:false,
                        data: {add_cash_feed:true,cash_feed_array:cash_feed_array_card},
                        success: function(theResponse){
                            $('#cash_feed_box_end').modal('toggle');
                            $( "#success_massage" ).show();
                            $( "#success_massage" ).fadeOut(6000 );
                        }
                    });
                }
            });




        });
    });
</script>

<div class="modal fade" id="modal_add_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add a New User</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form enctype="multipart/form-data" data-toggle="validator" id="product-
submit-form" method="post" action="">

                            <div class="form-group"
                                     <?php if(isset($error_msg)){
                                         echo $error_msg ;
                                     } ?>
                                <label for="product_name">User Name</label>
                                <input  id="user_name" name="user_name" type="text" class="form-control"/>
                            </div>

                    <div class="form-group">
                        <label for="product_description">Password</label>
                        <input  id="password" name="password" type="password" class="form-control"
                            />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden"
                               id="hidden_user">
                    </div>

                    <div class="form-group">
                        <label for="product_description"> Retype Password </label>
                        <input  id="password_re" name="password_re" type="password" class="form-control" />

                    </div>

                    <div class="form-group">
                        <label for="price">User Email</label>
                        <input  id="email_user" name="email_user" type="text"  class="form-control"
                            />
                    </div>

                    <div class="form-group">
                        <label>Member Type</label>
                        <select name='type_user' id ='type_user'  class='form-control'>
                            <option value="0">Normal User</option>
                            <option value="1">Admin User</option>
                        </select>
                    </div>

                    <div id="form-group">
                        <label></label>
                    </div>

                    <div class="form-group text-center">
                        <button id="close_new_user" type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>

                        <input data-toggle="modal" data-target="#largeModal" id="add_user"
                               name="add_user" type="submit" class="product_create btn btn-success btn-login-submit" value="Add
User" />
                    </div>

                    <div style="clear: both"></div>
                    </form>

                </div>



                <!--End  Form-->



            </div>

        </div>

        <div class="modal-footer" style="border: none;">

        </div>
    </div>
</div>
</div>
<div class="modal fade" id="master_code" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Enter Your Master Code</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form id="product-
submit-form" method="post" action="">

                            <div class="form-group"

                            <label for="product_name">Master Code</label>
                            <input  id="master_code_txt" name="master_code" type="text" class="form-control"/>
                    </div>



                    <div class="form-group text-center">
                        <button id="close_new_user" type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>

                        <input id="master_code_btn"
                               name="master_code" type="button" class="product_create btn btn-success btn-login-submit" value="Submit" />

                    </div>

                    <div style="clear: both"></div>
                    </form>

                </div>



                <!--End  Form-->



            </div>

        </div>

        <div class="modal-footer" style="border: none;">

        </div>
    </div>
</div>
</div>
<div class="modal fade" id="cat_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Edit Category</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                    <div></div>


                                <form enctype="multipart/form-data" data-toggle="validator" id="product-submit-form" method="post" action="">
                    <div class="col-sm-12">

                        <?php
                        $results = $cat->list_category();
                        foreach($results as $value){ ?>
                        <div class="form-group col-sm-6">
                            <label for="product_name">Category Name</label>
                            <input value="<?php echo $value['category_name'] ; ?>" id="product_name" name="cat_name_<?php echo $value['category_id'] ; ?>" type="text" class="form-control" />
                            <label for="product_name">Category Order</label>
                            <input value="<?php echo $value['cat_order'] ; ?>"  id="product_name" name="cat_order_<?php echo $value['category_id'] ; ?>" type="text" class="form-control" />
                            <label for="product_name">Category Image</label>
                            <input id="" name="cat_image_<?php echo $value['category_id'] ; ?>" type="file" class="form-control" />
                        </div>
                        <br>
                        <?php } ?>



                    </div>


                            <div id="form-group">
                                <label></label>
                            </div>

                            <div class="form-group text-center">
                                <button type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>
                                <input data-toggle="modal" data-target="#largeModal" id="product_create" name="edit_cat" type="submit" class="product_create btn btn-success btn-login-submit" value="Edit Category" />
                            </div>

                            <div style="clear: both"></div>
                        </form>



                    </div>



                    <!--End  Form-->



                </div>

            </div>

            <div class="modal-footer" style="border: none;">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add new product</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form enctype="multipart/form-data" data-toggle="validator" id="product-submit-form" method="post" action="">

                            <div class="form-group"
                            <label for="product_name">Product Name</label>
                            <input  id="product_name" name="product_name" type="text" class="form-control" />

                    </div>

                    <?php if(isset($error_message['product_name'])){?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['product_name'] ; ?>
                        </div>
                    <?php }  ?>

                    <div class="form-group">
                        <label>Category</label>

                        <?php
                        echo "<select name='category' id ='category_data'  class='form-control'>";
                        echo "<option >Select Category</option>";

                        foreach($category_details as $cat){?>

                            <option  value="<?php echo $cat['category_id'] ; ?>"><?php echo $cat['category_name'] ; ?></option>

                        <?php  } ?>
                        </select>
                    </div>

                    <?php if(isset($error_message['category'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['category'] ; ?>
                        </div>
                    <?php }  ?>

                    <div class="form-group">
                        <label for="product_description">Product Description</label>
                        <input  id="product_description" name="product_description" type="text" class="form-control" />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden" id="hidden_user">
                    </div>


                    <?php if(isset($error_message['product_description'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['product_description'] ; ?>
                        </div>
                    <?php }  ?>

                    <div class="form-group">
                        <label for="price">Product Price</label>
                        <input  id="price" name="price" type="text"  class="form-control"  />
                    </div>

                    <?php if(isset($error_message['price'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['price'] ; ?>
                        </div>
                    <?php }  ?>


                    <div class="form-group">
                        <label for="qty">Qty</label>
                        <input  id="product_qty" name="product_qty" type="text" class="form-control"  maxlength="200" />
                    </div>
                    <div class="form-group">
                        <label for="qty">Loose Tea</label>

                        <input type="radio" name="is_loose" value="1" checked>Yes
                        &nbsp;
                        <input type="radio" name="is_loose" value="female">No
                    </div>

                    <?php if(isset($error_message['product_qty'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['product_qty'] ; ?>
                        </div>
                    <?php }  ?>


                    <div id="form-group">
                        <label>Select Your Image</label><br/>
                        <input type="file" name="file" id="file" required />

                    </div>

                    <div id="form-group">
                        <label></label>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>
                        <input data-toggle="modal" data-target="#largeModal" id="product_create" name="submit" type="submit" class="product_create btn btn-success btn-login-submit" value="Add Product" />
                    </div>

                    <div style="clear: both"></div>
                    </form>



                </div>



                <!--End  Form-->



            </div>

        </div>

        <div class="modal-footer" style="border: none;">

        </div>
    </div>
</div>
</div>




<div class="modal fade" id="cat_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add new Category </h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form enctype="multipart/form-data" data-toggle="validator" id="product-submit-form" method="post" action="">

                            <div class="form-group"
                            <label for="product_name">Category Name</label>
                            <input  id="cat_name" name="cat_name_new" type="text" class="form-control" />

                    </div>





                    <?php if(isset($error_message['category'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['category'] ; ?>
                        </div>
                    <?php }  ?>

                    <div class="form-group">
                        <label for="product_description">Category Description</label>
                        <input  id="cat_description" name="cat_description" type="text" class="form-control" />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden" id="hidden_user">
                    </div>
                    <div class="form-group">
                        <label for="product_description">Category Image</label>
                        <input  id="cat_description" name="cat_image_new_up" type="file" class="form-control" />

                    </div>

                    <?php if(isset($error_message['product_qty'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['product_qty'] ; ?>
                        </div>
                    <?php }  ?>




                    <div id="form-group">
                        <label></label>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>
                        <input data-toggle="modal" data-target="#largeModal" id="product_create" name="submit_cat" type="submit" class="product_create btn btn-success btn-login-submit" value="Add Category " />
                    </div>

                    <div style="clear: both"></div>
                    </form>



                </div>



                <!--End  Form-->



            </div>

        </div>

        <div class="modal-footer" style="border: none;">

        </div>
    </div>
</div>
</div>
<div class="modal fade" id="cat_add" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Add new Category </h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form enctype="multipart/form-data" data-toggle="validator" id="product-submit-form" method="post" action="">

                            <div class="form-group"
                            <label for="product_name">Category Name</label>
                            <input  id="cat_name" name="cat_name" type="text" class="form-control" />

                    </div>





                    <?php if(isset($error_message['category'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['category'] ; ?>
                        </div>
                    <?php }  ?>

                    <div class="form-group">
                        <label for="product_description">Category Description</label>
                        <input  id="cat_description" name="cat_description" type="text" class="form-control" />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden" id="hidden_user">
                    </div>








                    <?php if(isset($error_message['product_qty'])){   ?>
                        <div class="alert alert-danger alert-dismissible fade in" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">×</span></button>
                            <?php   echo $error_message['product_qty'] ; ?>
                        </div>
                    <?php }  ?>




                    <div id="form-group">
                        <label></label>
                    </div>

                    <div class="form-group text-center">
                        <button type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>
                        <input data-toggle="modal" data-target="#largeModal" id="product_create" name="submit_cat" type="submit" class="product_create btn btn-success btn-login-submit" value="Add Category " />
                    </div>

                    <div style="clear: both"></div>
                    </form>



                </div>



                <!--End  Form-->



            </div>

        </div>

        <div class="modal-footer" style="border: none;">

        </div>
    </div>
</div>
</div>






<div style="display: none" class="edit_success_message alert alert-success alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    Product  has been Updated.
</div>
<div style="display: none" class="delete_product_success_message alert alert-success alert-dismissible fade in" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
    Product  has been Deleted.
</div>
<div class="modal fade" id="cash_feed_box_end" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Enter End Cash Value</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Cash  Sales amount</label><label id="main_total_container_end" style="margin-right: 50px;" class="pull-right"><b>Total:<label id ='main_total_end_cash_lable'></label></b></label>

                    <input name="cash_amount" id="main_total_cash_end" class="form-control" />
                    <br/>
                    <button type="button" class="btn btn-primary" id="cash_feed_add_end">Add New Value</button>
                    <button type="button" class="btn btn-primary" id="cash_feed_reset_end">Reset Amount</button>
                    <button type="button" class="btn btn-primary" id="cash_feed_clear_end">Clear End Cash</button>
                    <br><br>
                    <label>Credit Card Sales  amount</label><label id="main_total_container_end_card" style="margin-right: 50px;" class="pull-right"><b>Total:<label id ='main_total_end_card_lable'></label></b></label>
                    <input name="cash_amount" id="main_total_cash_end_card" class="form-control" />
                    <br/>
                    <button type="button" class="btn btn-primary" id="cash_feed_add_end_card">Add New Value</button>
                    <button type="button" class="btn btn-primary" id="cash_feed_reset_end_card">Reset Amount</button>
                    <button type="button" class="btn btn-primary" id="cash_feed_clear_end_card">Clear End Cash</button>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cash_feed_save_end">Add value</button>
            </div>
        </div>
    </div>
</div>


<!-- Report Models starts Here  -->

<div class="modal fade" id="cash_reports" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Cash Reports</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Start Cash Reports</label>


                    <br/>
                    <form action="" method="post" >
                        <input value="Today's Report" type="submit" name="today_start_cash" class="btn btn-primary" id="today_start_cash">
                        <input value="This Week Report" name="week_start_cash" type="submit"class="btn btn-primary" id="week_start_cash">
                        <button type="button" class="btn btn-primary" id="month_start_cash"> Month Report</button>
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cash_feed_save_end">Add value</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sale_reports" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Sales Reports</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Sales Reports</label>


                    <br/>
                    <form action="" method="post">
                        <input value="Today's Report" type="submit" name="today_sale_report" class="btn btn-primary" id="today_start_cash">
                        <input value="This Week Report" name="week_sale_report" type="submit"class="btn btn-primary" id="week_start_cash">
                        <button type="button" class="btn btn-primary" id="month_sales_report"> Month Report</button>
                        <button   type="button" class="btn btn-primary" id="date_sales_report">Date Sales</button>
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cash_feed_save_end">Add value</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="name" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select Month</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>please Select a Month to Genarate a Monthly Report</label>


                    <br/>
                    <form action="" method="post" >
                        <select class="form-control" name="month">
                            <option value="01">January</option>
                            <option value="02">February</option>
                            <option value="03">March</option>
                            <option value="04">April</option>
                            <option value="05">May</option>
                            <option value="06">June</option>
                            <option value="07">July</option>
                            <option value="08">August</option>
                            <option value="09">September</option>
                            <option value="10">October</option>
                            <option value="11">November</option>
                            <option value="12">December</option>
                        </select>
                        <br>
                        <br>
                        <input type="submit" name="Month_Report" value="Month Report" class="btn btn-primary" id="month_start_cash">
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sales" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Select Month</h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>please Select a Month to Generate a Monthly Report</label>


                <br/>
                <form action="" method="post" >
                    <select class="form-control" name="month_sale">
                        <option value="01">January</option>
                        <option value="02">February</option>
                        <option value="03">March</option>
                        <option value="04">April</option>
                        <option value="05">May</option>
                        <option value="06">June</option>
                        <option value="07">July</option>
                        <option value="08">August</option>
                        <option value="09">September</option>
                        <option value="10">October</option>
                        <option value="11">November</option>
                        <option value="12">December</option>
                    </select>
                    <br>
                    <br>
                    <input type="submit" name="Month__sale_Report" value="Month Report" class="btn btn-primary" id="month_start_cash">
                    <br><br>
                </form>

            </div>

        </div>
        <div class="modal-footer">

        </div>
    </div>
</div>
</div>
<div class="modal fade" id="sales_date" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>please Select a Date to Generate a Date Report</label>
                    <br/>
                    <form action="" method="post" >
                        <input type="text" id="report_date" name="report_date">
                        <br>
                        <br>
                        <input type="submit" name="date_sale_Report" value="Submit" class="btn btn-primary" id="get_date_report">
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>






<!-- report models ends here -->



<div class="modal fade" id="cash_feed_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Enter Cash Value</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Cash amount</label><label id="main_total_container" style="margin-right: 50px;" class="pull-right"><b>Total:<label id ='main_total'></label></b></label>
                    <input name="cash_amount" id="cash_amount" class="form-control" />
                </div>
                <button type="button" class="btn btn-primary" id="cash_feed_add">Add New Value</button>
                <button type="button" class="btn btn-primary" id="cash_feed_reset">Reset Amount</button>
                <button type="button" class="btn btn-primary" id="cash_feed_clear">Clear Start Cash</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="cash_feed_save">Add value</button>
            </div>
        </div>
    </div>
</div>

<div id="added_car"  style="display: none" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <table class="table">
                <tr>
                    <th>
                        Product Added Successfully to  the Cart
                    </th>
                </tr>
                <tr>
                    <td>
                        <button onclick="this.hide;" id="ok" type="button" class="btn btn-primary">Ok </button>

                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="modal fade" id="product_edit_pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Edit product</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">



                        <form data-toggle="validator" id="product-edit-form" method="post" action="">

                            <div class="form-group">
                                <label for="price">Product Name</label>
                                <input value=""  id="name" name="name" type="text"  class="form-control edit_prduct_name"  />
                            </div>

                            <div class="form-group">
                                <label for="price">Product Price</label>
                                <input  id="price" name="price" type="text"  class="form-control edit_prduct_price"  />
                            </div>
                            <div class="form-group">
                                <label for="price">Product Description</label>

                                <textarea id="edit_product_description" name="edit_product_description" class="form-control edit_prduct_des"></textarea>
                            </div>

                            <?php if(isset($error_message['price'])){   ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <?php   echo $error_message['price'] ; ?>
                                </div>
                            <?php }  ?>


                            <div class="form-group">
                                <label for="qty"> Current Quantity</label>
                                <input size="4" id="product_qty" name="current_qty" type="text" class="form-control edit_prduct_quantity"  maxlength="3"  disabled="disabled" />
                            </div>

                            <div class="form-group">
                                <label for="qty">Qty</label>

                                <div class="checkbox">
                                    <label align="center" class="radio-inline">
                                        <input type="radio" name="cal_operator" style="float:none!important" class="cal_operator" value="1"> (+)
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="cal_operator" style="float:none!important" class="cal_operator" value="0"> (-)
                                    </label>
                                </div>

                                <input size="4"  id="new_product_qty" name="new_product_qty" type="text" class=" add_new_prduct_quantity"  maxlength="200" />
                            </div>


                            <?php if(isset($error_message['product_qty'])){   ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <?php   echo $error_message['product_qty'] ; ?>
                                </div>
                            <?php }  ?>

                            <div class="form-group text-center">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <input type="hidden" name="edit_prduct_id" class="edit_product_id"/>
                                <input name="submit" type="button" class="product-edit-submit-form_awesome btn btn-success btn-login-submit" value="Edit Product" />

                            </div>

                            <div style="clear: both"></div>
                        </form>



                    </div>



                    <!--End  Form-->



                </div>

            </div>
            <div class="modal-footer" style="border: none;">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="photo_edit_pop" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"> Edit Photo</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">



                        <form enctype="multipart/form-data" data-toggle="validator" id="product-edit-form" method="post" action="">


                            <div class="form-group">
                                <label for="price">Product Image</label>
                                <img width="300" height="400" src=""  id="photo" name="price" type="text"  class="form-control edit_prduct_price"  />
                                <input type="hidden" name="hidden_image" id="hidden_image" class="form-control edit_prduct_price"  />
                            </div>




                            <div class="form-group">
                                <label for="qty">Upload Your Image</label>
                                <input  id="image_edit" name="image_edit" type="file" class="form-control"    />
                            </div>





                            <div class="form-group text-center">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                <input type="hidden" name="edit_prduct_id" class="edit_product_id"/>
                                <input name="update_image" type="submit" class="product-edit-submit-form btn btn-success btn-login-submit" value="Edit Photo" />

                            </div>

                            <div style="clear: both"></div>
                        </form>



                    </div>



                    <!--End  Form-->



                </div>

            </div>
            <div class="modal-footer" style="border: none;">

            </div>
        </div>
    </div>
</div>
<script>
    $(".edit_photo").on('click',function(e) {

        product_id = $(this).attr('id');

        $.ajax
        ({
            url: 'extra_function.php',
            dataType: 'json',
            data: {'get_product_details':true,'product_id': product_id},
            type: 'post',
            success: function(data)
            {    $('#photo_edit_pop').modal('show');
                $('#photo').attr('src','uploads/'+data['feature_image']);
                $('#hidden_image').attr('value',data['product_id']);

                return false;

            }
        });

        e.preventDefault();
    });


    $(".edit_product").on('click',function(e) {

        product_id = $(this).attr('id');

        $.ajax
        ({
            url: 'extra_function.php',
            dataType: 'json',
            data: {'get_product_details':true,'product_id': product_id},
            type: 'post',
            success: function(data)
            {    $('#product_edit_pop').modal('show');
                $('.edit_prduct_quantity').attr('value',data['qty']);
                $('.edit_prduct_price').attr('value',data['price']);
                $('.edit_prduct_name').attr('value',data['product_name']);
                $('.edit_product_id').attr('value',data['product_id']);
                $('textarea#edit_product_description').text(data['product_description']);
               // $(".edit_prduct_des").text(result.data['product_description']);

                return false;

            }
        });
        e.preventDefault();
    });
</script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
</body>
</html>
