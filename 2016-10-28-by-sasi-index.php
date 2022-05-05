<?php
    include_once("common_header.php");

    error_reporting(0);
    $user = new User();
    if(empty($_SESSION['session_cart_total'])){
        $_SESSION['session_cart_total'] = '00.00';
    }


    //include_once("class/User.php");
    $category = new Category();
    $order_obj = new Order();
    $cash_obj = new CashFeed();
    //$user = new Ca

    $cat = new Category();
    $category_details = $category->select_all_categories();

    $product = new Product();

    if(isset($_POST['get_saved_barcoded'])){
        $file = $_POST['hidden_path_barcode'];
        $quoted = sprintf('"%s"', addcslashes(basename($file), '"\\'));
        $size   = filesize($file);

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $quoted);
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Content-Type: image/png');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . $size);
    }


if(isset($_POST['save_user'])){
    $user_id = $_POST['select_user'];
    $user_status = $_POST['users_list'];

    $user_obj = new Customer();

    $user = array(
        'is_admin'=>$user_status
    );
    $update = $user_obj->update_user_status($user,$user_id);
}

if(isset($_POST['change_pass'])){
    $new_pass = $_POST['password'];
    $new_pass_re = $_POST['password_re'];
    if($new_pass == $new_pass_re){
        $encripted = md5($new_pass);
        $user_id= $_SESSION['user_id'];
        $user_array = array(
            'password'=>$encripted
        );
        $customer_obj = new Customer();
        $customer_obj->update_pass($user_array,$user_id);

    }
}

if(isset($_POST['update_image'])){


    $product_id =  $_POST['hidden_image'];
    $cat_u = $_POST['hidden_cat'];
    $image_name_new = $_FILES['image_edit']['name'];
    $image_name_new_edited = $cat_u."_".$image_name_new ;
    $get_product_details = $product->list_priduct_by_product_id($product_id);

     $mage_with_path = "uploads/".$get_product_details['feature_image'];


    $image_update_array = array(
        'feature_image'=>$image_name_new_edited
    );

    // add new image to the database

    move_uploaded_file($_FILES['image_edit']['tmp_name'],"uploads/".$image_name_new_edited);
    $add_image = $product->update_new_product($image_update_array,$product_id);
}
    if(isset($_GET['cat_id']) && $_GET['cat_id'] != '') {

        $product_details_default = $product->select_product_by_cat_id($_GET['cat_id']);

        if(isset($_GET['pro_id']) && $_GET['pro_id'] != '' ) {

            $product_id = $_GET['pro_id'];
            $product_details_default_x = array();
            $k =1;
            foreach($product_details_default as $each_item) {
                if($product_id == $each_item['product_id']) {
                    $product_details_default_x[0] = $each_item;
                }
                else{
                $product_details_default_x[$k] = $each_item;
                    $k++;
                }
            }
            $product_details_default = $product_details_default_x;
            ksort($product_details_default);
        }

        $category_each_name = $cat->get_cat_name_by_cat_id($_GET['cat_id']);
    } else {
        $product_details_default = $product->select_product_by_cat_id($category_details[0]['category_id']);
        $category_each_name = $cat->get_cat_name_by_cat_id($category_details[0]['category_id']);
    }
if(isset($_POST['today_start_cash'])){

    $date = date('Y-m-d');
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
        $data_print_week_cash[$each_cash['feed_time']][0]['feed_type'] = 'In';

        $data_print_week_cash[$each_cash['feed_time']][0]['amount'] = $each_cash['cash_amount'];

    }
    foreach($get_card_cash as $each_end_cash) {
        $data_print_week_cash[$each_end_cash['feed_time']][1]['feed_type'] = 'Out';
        $data_print_week_cash[$each_end_cash['feed_time']][1]['amount'] = $each_end_cash['Total'];

    }
    foreach($total_sales_week as $sales_week_each_cash) {
        $data_print_week_cash[$sales_week_each_cash['order_date']][2]['feed_type'] = 'Sale';
        $data_print_week_cash[$sales_week_each_cash['order_date']][2]['amount'] = $sales_week_each_cash['Total'];
    }
    $_SESSION['week_start_cash'] = $data_print_week_cash;

    header("Location:Test.php?week_rep=true");

}

if(isset($_POST['Month_Report'])){
    $month = $_POST['month_cash'];
    $month_cash_year = $_POST['month_cash_year'];

    $cash_obj = new CashFeed();
    //$get_cash = json_encode($cash_obj->week_cash($date,$date_before_week));
    $get_cash = $cash_obj->month_s_cash($month,$month_cash_year);

    //$get_card_cash = json_encode($cash_obj->report_week_end_cash($date,$date_before_week));
    //$total_sales_week = json_encode($order_obj->get_duration_sales($date,$date_before_week));
    $get_card_cash = $cash_obj->report_month_end_cash($month, $month_cash_year);


    $total_sales_week = $order_obj->get_duration_sales_month($month, $month_cash_year);

    $data_print_week_cash =array();
    $k =0;
    foreach($get_cash as $each_cash) {


        $data_print_week_cash[$each_cash['feed_time']][0]['feed_type'] = 'In';

        $data_print_week_cash[$each_cash['feed_time']][0]['amount'] = $each_cash['cash_amount'];

    }
    foreach($get_card_cash as $each_end_cash) {
        $data_print_week_cash[$each_end_cash['feed_time']][1]['feed_type'] = 'Out';
        $data_print_week_cash[$each_end_cash['feed_time']][1]['amount'] = $each_end_cash['cash_amount'];

    }

    foreach($total_sales_week as $sales_week_each_cash) {
        $data_print_week_cash[$sales_week_each_cash['order_date']][2]['feed_type'] = 'Sale';
        $data_print_week_cash[$sales_week_each_cash['order_date']][2]['amount'] = $sales_week_each_cash['Total'];


    }


    $data_print_month_cash_encoded = json_encode($data_print_week_cash);


    $_SESSION['m_cash'] =  $data_print_week_cash;
    header("Location:Test.php?month_report=true&year=".$month_cash_year.'&month='.$month);

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

//if(isset($_POST['Month__sale_Report'])){
//    $selected_month = $_POST['month_sale'];
//
//    $month_sales = $order_obj->month_sales_report($selected_month);
//
//    $month_sale_count = $order_obj->month_sales_count($selected_month);
//
//    $final_count = $month_sale_count['TM'];
//
//    $report_detail = array();
//    $counter = 0;
//    foreach($month_sales as $each_order){
//        $each_order_id = $each_order['order_id'];
//
//        $each_order_total = $each_order['order_total'];
//
//        $get_order_details = $order_obj->order_inside_details($each_order_id);
//        error_reporting('0');
//        foreach($get_order_details as $each_product){
//
//            $data_load_array = array();
//            $data_load_array['order_id'] = $each_product_id;
//
//            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
//            $Price_original = $product_data['price'];
//            $each_qty = $each_product['no_of_products'];
//            $order_price = $each_qty*$Price_original ;
//            $data_load_array['product_name'] = $product_data['product_name'];
//            $data_load_array['qty'] = $each_product['no_of_products'];
//            $data_load_array['order_total'] = $order_price;
//
//            if(in_array_r($data_load_array['order_id'],$report_detail)) {
//
//                $report_detail[$counter]['qty'] =  $report_detail[$counter]['qty'] + $data_load_array['qty']  ;
//                $report_detail[$counter]['order_total'] = $report_detail[$counter]['order_total'] + $data_load_array['order_total'];
//
//            }
//            else {
//                array_push($report_detail,$data_load_array);
//            }
//
//        }
//    }
//    $counter++;
//    $the_detail_send_array = json_encode($report_detail);
//    $_SESSION['data_month'] = $the_detail_send_array ;
//
//    header("Location:Test.php?sale_month_report=true&Total=$final_count");
//}
if(isset($_POST['Month__sale_Report'])){
    $selected_month = $_POST['month_sale'];
    $selected_month_year = $_POST['month_sale_year'];
    $month_sales_sales_rep = $order_obj->month_sales_report($selected_month, $selected_month_year);

    $month_sale_count = $order_obj->month_sales_count($selected_month, $selected_month_year);

    $final_count = $month_sale_count['TM'];

    $report_detail = array();
    $counter = 0;
    foreach($month_sales_sales_rep as $each_order){
        $each_order_id = $each_order['order_id'];
        $each_order_total = $each_order['order_total'];

        $get_order_details = $order_obj->order_inside_details($each_order_id);
        error_reporting('0');

        foreach($get_order_details as $each_product){
            $data_load_array = array();
            $each_product_id = $each_product['product_id'];
            $data_load_array['order_id'] = $each_product_id;

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;

            if(in_array_r($data_load_array['order_id'],$report_detail)) {

                $report_detail[$counter]['qty'] =  $report_detail[$counter]['qty'] + $data_load_array['qty']  ;
                $report_detail[$counter]['order_total'] = $report_detail[$counter]['order_total'] + $data_load_array['order_total'];

            }
            else {
                array_push($report_detail,$data_load_array);
            }

        }
    }
    $counter++;


    $_SESSION['sales_data'] = $report_detail ;
    header("Location:Test.php?sale_report_monthly=true&year=".$selected_month_year."&month=".$selected_month);
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
    //$the_detail_send_array = json_encode($report_detail);
    $_SESSION['weekly_sale_data'] = $report_detail;
    header("Location:Test.php?week_sale_report=true");

}

//if(isset($_POST['week_sale_report'])){
//
//    $date = date('Y/m/d');
//    $date_before_week_2  = strtotime("-7 day");
//    $date_before_week = date('Y/m/d', $date_before_week_2);
//
//    $get_data =  $order_obj->get_week_sales($date,$date_before_week);
//
//    $total_sales_week = $get_data['Total'];
//
//    $get_today_orders =  $order_obj->get_week_orders($date,$date_before_week);
//
//    $report_detail = array();
//    foreach($get_today_orders as $each_order){
//        $each_order_id = $each_order['order_id'];
//
//        $each_order_total = $each_order['order_total'];
//
//        $get_order_details =  $order_obj->order_inside_details($each_order_id);
//        error_reporting('0');
//        foreach($get_order_details as $each_product){
//
//            $data_load_array = array();
//            $data_load_array['order_id'] = $each_product_id;
//
//            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
//            $Price_original = $product_data['price'];
//            $each_qty = $each_product['no_of_products'];
//            $order_price = $each_qty*$Price_original ;
//            $data_load_array['product_name'] = $product_data['product_name'];
//            $data_load_array['qty'] = $each_product['no_of_products'];
//            $data_load_array['order_total'] = $order_price;
//
//            array_push($report_detail,$data_load_array);
//        }
//    }
//    $the_detail_send_array = json_encode($report_detail);
//    $_SESSION['data'] = $the_detail_send_array ;
//    header("Location:Test.php?sale_report=true&Total=$total_sales_week");
//
//}
    if(isset($_POST['submit'])){
        $product_name = $_POST['product_name'];
        $category = $_POST['category'];
        $is_loose = $_POST['is_loose'];
        $product_description = $_POST['product_description'];
        $price = $_POST['price'];
        $product_qty = $_POST['product_qty'];
        $image_name = $_FILES['file']['name'];
        $image_name_edited = $category."_".$image_name ;
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
            'feature_image' =>$image_name_edited,
            'created_date' =>$date,
            'status' =>'1',
            'qty' => $product_qty,
            'is_loose' => $loose_add
        );

        $add_product = $product->add_new_product($product_array);
        move_uploaded_file($file_tmp,"uploads/".$image_name_edited);




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

//    if(isset($_POST['week_start_cash'])){
//
//        $date = date('Y-m-d');
//        $date_before_week_2  = strtotime("-7 day");
//        $date_before_week = date('Y-m-d', $date_before_week_2);
//        $cash_obj = new CashFeed();
//        //$get_cash = json_encode($cash_obj->week_cash($date,$date_before_week));
//        $get_cash = $cash_obj->week_cash($date,$date_before_week);
//
//        //$get_card_cash = json_encode($cash_obj->report_week_end_cash($date,$date_before_week));
//        //$total_sales_week = json_encode($order_obj->get_duration_sales($date,$date_before_week));
//        $get_card_cash = $cash_obj->report_week_end_cash($date,$date_before_week);
//        $total_sales_week = $order_obj->get_duration_sales($date,$date_before_week);
//
//        $data_print_week_cash =array();
//            $k =0;
//            foreach($get_cash as $each_cash) {
//
//
//               $data_print_week_cash[$each_cash['feed_time']][$k]['feed_type'] = 'In';
//
//                $data_print_week_cash[$each_cash['feed_time']][$k]['amount'] = $each_cash['cash_amount'];
//                $k++;
//            }
//        foreach($get_card_cash as $each_end_cash) {
//            $data_print_week_cash[$each_end_cash['feed_time']][$k]['feed_type'] = 'Out';
//            $data_print_week_cash[$each_end_cash['feed_time']][$k]['amount'] = $each_end_cash['Total'];
//            $k++;
//        }
//
//       foreach($total_sales_week as $sales_week_each_cash) {
//           $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['feed_type'] = 'Sale';
//           $data_print_week_cash[$sales_week_each_cash['order_date']][$k]['amount'] = $sales_week_each_cash['Total'];
//           $k++;
//
//        }
//
//
//        $data_print_week_cash_encoded = json_encode($data_print_week_cash);
//
//        header("Location:Test.php?week_rep=true&week_cash_start=$data_print_week_cash_encoded");
//
//    }

//    if(isset($_POST['Month_Report'])){
//        $month_name = $_POST['month'];
//
//        $cash_obj = new CashFeed();
//        $get_cash_month =  json_encode($cash_obj->month_start_cash_report($month_name));
//        $get_cash_month_end =  json_encode($cash_obj->month_end_cash_report($month_name));
//        header("Location:Test.php?month_report=true&month_data=$get_cash_month&get_cash_month_end=$get_cash_month_end");
//
//
//    }

 /**
  * Each Product Sales  reports Starts version 2.0
 * by Spenzer
 */

if(isset($_POST['product_today_sale_report'])){

    $product_id = $_POST['uniq_pro_name'];
    $report_date = date('Y-m-d');
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    $product_name = $product_details['product_name'];
    $sales_details_this = $product_obj->get_each__product_sales_today($product_id,$report_date);

   // $the_detail_send_array = json_encode($sales_details);
    $_SESSION['product_sale_report_today'] = $sales_details_this;

    header("Location:Test.php?product_sale_report_today=true&name=$product_name");
}

if(isset($_POST['product_week_sale_report'])){

    $product_id = $_POST['uniq_pro_name'];
    $report_date = date('Y-m-d');
    $date2 = strtotime($report_date);
    $date_end = strtotime("-7 day", $date2);
    $report_date_end = date('Y-m-d', $date_end);

    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    $product_name = $product_details['product_name'];
    $week_sale_report = $product_obj->get_each_product_week_sales($product_id,$report_date,$report_date_end);

    $_SESSION['product_sale_report_week'] = json_encode($week_sale_report);

    header("Location:Test.php?product_sale_report_week=true&name=$product_name");
}
if(isset($_POST['product_Month__sale_Report_selected'])){

    $product_id = $_POST['u_month_pro_name'];

    $selected_month = $_POST['month_sale'];
    $product_obj = new Product();
    $product_details_month = $product_obj->get_each_product_month_sales($product_id,$selected_month);
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    $product_name = $product_details['product_name'];

    $_SESSION['product_sale_report_month'] = json_encode($product_details_month);

    header("Location:Test.php?product_sale_report_month=true&name=$product_name");
}

if(isset($_POST['date_sale_Report_product'])){

    $report_date = $_POST['report_date2'];
    $product_id = $_POST['product_name'];
    $report_date = date('Y-m-d');
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    $product_name = $product_details['product_name'];
    $sales_details_thisa = $product_obj->get_each__product_sales_today($product_id,$report_date);

    // $the_detail_send_array = json_encode($sales_details);
    $_SESSION['product_sale_report_today'] = json_encode($sales_details_thisa);

    header("Location:Test.php?product_sale_report_today=true&detail_array=true&name=$product_name");

}

/**
 * Each Product Sales Reports Ends version 2.0
 * By Spenzer
 */



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

        $_SESSION['sales_data'] = $report_detail;
        header("Location:Test.php?sale_report_today=true");
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


    $_SESSION['sale_report_today'] = $report_detail;

    header("Location:Test.php?sale_report_today=true");
}



/**
* Sales Reports Per User Starts Here
* By Spenzer
 */

if(isset($_POST['date_sale_Report_product_per_user'])){

    $user_id = $_POST['user_name'];
    $report_date =  $_POST['report_date3'];

    $order_total =  $order_obj->get_daily_sales_users($report_date,$user_id);

    $total_sales_today = $order_total['Total']; // today sales income

    $get_today_orders =  $order_obj->get_today_orders_users($report_date,$user_id);

    $report_detail = array();
    $counter = 0;
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

            if(in_array_r($data_load_array['order_id'],$report_detail)) {

                $report_detail[$counter]['qty'] =  $report_detail[$counter]['qty'] + $data_load_array['qty']  ;
                $report_detail[$counter]['order_total'] = $report_detail[$counter]['order_total'] + $data_load_array['order_total'];

            }
            else {
                array_push($report_detail,$data_load_array);
            }

        }
    }
    $counter++;
    $the_detail_send_array = json_encode($report_detail);

    header("Location:Test.php?sale_report_today=true&detail_array=$the_detail_send_array&Total=$total_sales_today");

}



if(isset($_POST['user_for_today_sale_report'])){

    $user_id = $_POST['user_name'];
    $report_date = date('Y-m-d');
    $order_total =  $order_obj->get_daily_sales_users($report_date,$user_id);

    $total_sales_today = $order_total['Total']; // today sales income

    $get_today_orders =  $order_obj->get_today_orders_users($report_date,$user_id);

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

function in_array_r($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}
if(isset($_POST['user_for_week_sale_report'])){
    $date = date('Y-m-d');
    $user_id = $_POST['user_name'];
    $date_before_week_2  = strtotime("-7 day");
    $date_before_week = date('Y-m-d', $date_before_week_2);
    $user_obj = new User();
    $user_details = $user_obj->getUserByID($user_id);
    $user_name = $user_details['username'];
    $get_data =  $order_obj->get_week_sales($date,$date_before_week,$user_id);

    echo $total_sales_week = $get_data['Total'];

    $get_today_orders =  $order_obj->get_week_orders($date,$date_before_week,$user_id);

    $report_detail = array();
    $counter = 0 ;
    foreach($get_today_orders as $each_order){
        $each_order_id = $each_order['order_id'];
        $each_order_total = $each_order['order_total'];
        $get_order_details =  $order_obj->order_inside_details($each_order_id);

        error_reporting('0');

        foreach($get_order_details as $each_product){

            $data_load_array = array();
            $data_load_array['order_id'] = $each_product['product_id'];

            $product_data = $product->list_priduct_by_product_id($each_product['product_id']);
            $Price_original = $product_data['price'];
            $each_qty = $each_product['no_of_products'];
            $order_price = $each_qty*$Price_original ;
            $toti += $order_price;

            $data_load_array['product_name'] = $product_data['product_name'];
            $data_load_array['qty'] = $each_product['no_of_products'];
            $data_load_array['order_total'] = $order_price;



          if(in_array_r($data_load_array['order_id'],$report_detail)) {

            $report_detail[$counter]['qty'] =  $report_detail[$counter]['qty'] + $data_load_array['qty']  ;
             $report_detail[$counter]['order_total'] = $report_detail[$counter]['order_total'] + $data_load_array['order_total'];

          }
          else {
                array_push($report_detail,$data_load_array);
          }

        }

    }
    $counter++;
    $the_detail_send_array = json_encode($report_detail);
    $_SESSION['per_user_week_sales'] = $the_detail_send_array ;
    header("Location:Test.php?per_user_week_sale_report=true&Total=$total_sales_week&name=$user_name");
}
if(isset($_POST['Month__sale_Report_per_user'])){
    $user_id = $_POST['user_name'];
    $selected_month = $_POST['month_sale'];
    error_reporting('0');
    $month_sales = $order_obj->month_sales_report_per_user($selected_month,$user_id);
    $month_sale_count = $order_obj->month_sales_count_per_user($selected_month,$user_id);

    $final_count = $month_sale_count[0]['Total'];
    

    $report_detail = array();
    $counter = 0 ;
    error_reporting('0');
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

            if(in_array_r($data_load_array['order_id'],$report_detail)) {

                $report_detail[$counter]['qty'] =  $report_detail[$counter]['qty'] + $data_load_array['qty']  ;
                $report_detail[$counter]['order_total'] = $report_detail[$counter]['order_total'] + $data_load_array['order_total'];

            }
            else {
                array_push($report_detail,$data_load_array);
            }
        }
    }
    $counter++;


    $the_detail_send_array = json_encode($report_detail);
    $_SESSION['per_user_month_sales'] = $the_detail_send_array ;
    header("Location:Test.php?per_user_month_sale_report=true&Total=$final_count&name=$user_name");
}

/**
 * Sales Reports Per User Ends Here
 * By Spenzer
 */

/**
 * Stock Reports Starts Here
 * By Spenzer
 * */

if(isset($_POST['get_stock_in_hand'])){
    $product_obj = new Product();
    $product_inventory = $product_obj->list_priduct_inventory();

    $_SESSION['product_inventory'] = $product_inventory;
    header("Location:Test.php?product_inventory=true");
}

/**
 * Stock Reports Ends Here
 * By Spenzer
 * */

?>

<!doctype html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>The Showcase</title>

        <link rel="stylesheet" href="css/idangerous.swiper.css">
        <link type="text/css" rel="stylesheet" href="css/demo.css" />
        <link type="text/css" rel="stylesheet" href="css/jquery.mmenu.all.css" />
        <link type="text/css" rel="stylesheet" href="css/bootstrap.css" />
        <link href="styles.css" rel="stylesheet" type="text/css" />

        <script type="text/javascript" src="js/jquery-2.1.3.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/jquery.mmenu.min.all.js"></script>
    <script type="text/javascript" src="js/product.js"></script>
    <script type="text/javascript" src="js/bootbox.min.js"></script>
    <script src="js/flickity.pkgd.js"></script>




    <style>
        <?php if($_SESSION['user_level'] == 0) { ?>
        body {
            /* screen totation code starts here  */


            /*-moz-transform: rotate(180deg);*/
            /*-o-transform: rotate(180deg);*/
            /*-webkit-transform: rotate(180deg);*/
            /*-ms-transform: rotate(180deg);*/
            /*transform: rotate(180deg);*/
            /*filter: progid:DXImageTransform.Microsoft.Matrix(*/
                /*M11=-1, M12=-1.2246063538223773e-16, M21=1.2246063538223773e-16, M22=-1, sizingMethod='auto expand');*/
            /*zoom: 1;*/
        }
        <?php } else {  ?>
        body {
            /*-moz-transform: rotate(0deg);*/
            /*-o-transform: rotate(0deg);*/
            /*-webkit-transform: rotate(0deg);*/
            /*-ms-transform: rotate(0deg);*/
            /*transform: rotate(0deg);*/
            /*filter: progid:DXImageTransform.Microsoft.Matrix(SizingMethod='auto expand', M11=0.7071067811865476, M12=-0.7071067811865475, M21=0.7071067811865475, M22=0.7071067811865476)";*/
            /*zoom: 1;*/
        }
        <?php } ?>

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
            padding-top: 20px;
            padding-bottom: 20px;
        }
        .swiper-slide {
            background-position: center;
            background-size: cover;
            width: 300px;
            height: 300px;
        }
        .flickity-viewport:focus{
            outline-color: #000 !important;
            outline-width: 0 !important;
        }
        .settingClass{
            margin-top:-13px;
        }
        .newlio{
            margin-left:-3px!important;
        }
        a:active, a:focus { outline-style: none; -moz-outline-style:none; }
        div {
            outline:none !important;
        }
		#cssmenu>ul{
			background:none;
		}
		#cssmenu>ul>li{
			margin-bottom:-18px;
		}
		 .btn-success .btn-success:hover, .btn-success:focus, .btn-success:active, .btn-success.active, .open .dropdown-toggle.btn-success{
			 background:#316896 ;
			 border-color:#316896 ;
		 }

		 .btn-danger .btn-danger:hover, .btn-danger:focus, .btn-danger:active, .btn-danger.active, .open .dropdown-toggle.btn-danger{
			 background:#316896 !important;
			 border-color:#316896 !important;
		 }

		 .nav > li > a{
			 padding:10px 11px !important;
		 }
		 .btn-red{
			 background: #DF0F13;
		 }
		 .btn-red:hover{
			 background: #DF0F13;
		 }
		 .open_excel .btn:hover, .open_excel .btn:focus{
			 color:#fff !important;
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
  margin: 5px auto;
  height: 550px;
  padding: 20px 40px 80px 60px;
  background:#000;
}
.swiper-container {
  height: 700px;
  color: #fff;
  text-align: center;
}
.bgcolor{
    background-color: white;
}
.bgcolor:hover{
          background-color:cornflowerblue;
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
    margin-right: -9%;
    margin-left: 8%;
    z-index: -1;

    position:relative;
}
.swiper-wrapper{

}

      body {
          font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";

      }
	  
	  .gallery {
  overflow:hidden;

}
.flickity-page-dots{
	display:none;
}
.flickity-prev-next-button{
	display:none;
}
.gallery-cell {
  width: 150px;
  height: 150px;
  margin-right: 10px;
  background: #8C8;
  counter-increment: gallery-cell;
}

.gallery-cell.is-selected {
  background: #ED2;
}

/* cell number */
.gallery-cell:before {
  display: block;
  text-align: center;
  content: counter(gallery-cell);
  line-height: 200px;
  font-size: 80px;
  color: white;
}
.sign-out{
	float:left;
}
.button-blue{
			color:#fff;
			text-transform:uppercase;
			float:left;
			padding:5px 8px;
			background:#164293 !important;
			margin-top:5px;
			margin-right:10px;
}
.button-blue:hover{
	color:#fff;
}


</style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">


</head>
<body>
<a href="#"> <div id="search_model" style="margin-right: 20px" align="right">
  <marquee scrollamount="600" behavior="slide" direction="right"> <font style="color: white">THE WITHERED LEAVES TEA AND SPICES COMPANY POINT OF SALE SYSTEM&nbsp;&nbsp;</font><img width="30" height="30" src="img/Search-icon.png">&nbsp;&nbsp;<font style="color: white">Search</font></marquee>

</div></a>

  

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
                     

                    </ul>
                </div>
            </nav>
        </div>
    </div>
</div>
<div id="">

    <div class="content">
        <div class="device">

            <?php
           $swipper_wrapper_pad = "style='padding-left: 329.5px !important;'";
            $product_count = 0;
            if(!empty($product_details_default)) {

            foreach($product_details_default as $value){
                $product_count++;
            }
            }

            if($product_count > 15 && $product_count <= 20) {
                $swipper_wrapper_pad = "style='padding-left: 70.5px !important;'";
            } else if($product_count > 20 && $product_count <= 25) {
                $swipper_wrapper_pad = "style='padding-left: 45.5px !important;'";
            } else if($product_count > 25) {
                $swipper_wrapper_pad = "style='padding-left: 1px !important;'";
            }


            ?>
            <div class="swiper-container">
                <div id="slider_divs"
                    <?php if(isset($_GET['cat_id'])){
                    if($_GET['cat_id'] == '17') { ?>
                        style="padding-left:348.5px !important; width:4236px !important;"

                    <?php }else if($_GET['cat_id'] == '19'){?>
                        style="padding-left:472.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '3'){?>
                        style="padding-left:346.5px !important;width:4237px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '33'){ ?>
                        style="padding-left:350.5px !important;width:4237px !important;"

                    <?php }
                    else if($_GET['cat_id'] == '14'){ ?>
                        style="padding-left:498.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '6'){ ?>
                        style="padding-left:447.5px!important;"
                    <?php }
                    else if($_GET['cat_id'] == '7'){ ?>
                        style="padding-left:7.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '4'){ ?>
                        style="padding-left:119.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '30'){ ?>
                        style="padding-left:365.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '34'){ ?>
                        style="padding-left:249.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '5'){ ?>
                        style="padding-left:344.5px !important; width:4237px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '11'){ ?>
                        style="padding-left:348.5px !important; width:4237px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '28'){ ?>
                        style="padding-left:445.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '27'){ ?>
                        style="padding-left:529.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '22'){ ?>
                        style="padding-left:339.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '1'){ ?>
                        style="padding-left:338.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '35'){ ?>
                        style="padding-left:528.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '23'){ ?>
                        style="padding-left:448.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '21'){ ?>
                        style="padding-left:120.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '25'){ ?>
                        style="padding-left:569.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '29'){ ?>
                        style="padding-left:584.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '31'){ ?>
                        style="padding-left:389.5px !important;"
                    <?php }
                    else if($_GET['cat_id'] == '32'){ ?>
                        style="padding-left:569.5px  !important;"
                    <?php }


                }
                else { ?>

                    style="padding-left:348.5px !important; width:4236px !important;"
                <?php } ?>



                     class="swiper-wrapper" <?php echo $swipper_wrapper_pad; ?>>

                    <?php
                  //  print_r($product_details_default);
                    if(!empty($product_details_default)) {
                            $x = 0;
                        foreach($product_details_default as $value){
                            $product_qty = $value['qty'];
                            $is_loos = $value['is_loose'];
                            $x++;
                            echo "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide sl-".$x." \" style=\"width: 600px; height: 600px;\">";

                            echo "<div id='image_".$value['product_id']."' class='image_class' style='padding-top:0px; border:2px solid cdcdcd#;'><img style=' -webkit-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    -moz-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);'  src=\"uploads/".$value['feature_image']."\">

    <div align='center' style='text-align: center; width: 167% '>".$value['product_description']."</div>

    ";
                            if($_SESSION['user_level'] == 1) {?>
                              <div>
                              		 <a href='#' id="<?php echo $value['product_id']  ;?>"  style='position: absolute; left: 10px; top: 5px;' class='delete_product' onClick="delete_product(<?php echo $value['product_id']  ;?>)">
                                    <span class='btn btn-danger btn-red' style="width:60px;">Delete</span>
                             		</a>

                                  <a href="#" id="<?php echo $value['product_id']  ; ?>" style="position: absolute; right: 22px; top: 5px;" class="edit_product" data-toggle="modal" data-target="#added_car">
                                      <span class="btn btn-warning" style="width:60px;">Edit</span>
                                  </a>
                                  <a href='#' id="<?php echo $value['product_id']  ;?>" class='edit_photo' onClick="edit_photo(<?php echo $value['product_id']  ;?>)" style="position: absolute; right: -230px; top: 5px;">
                                      <span class='btn btn-danger' style="width:60px; background:#164293 !important;">Image</span>
                                  </a>

                            



                            </div>
               <?php   }


                echo "   </div>";

                          echo "<div style='padding-top: 0px;'>
                               <div  style='display:none; background-color: #333333;
                                     width: 606px; height: 65px;padding-top:10px;margin-left:0px;' class='details_div_small' id='product_detail_".$value['product_id']."'>
                                     <h4 style='color:white';>".$value['product_name']."</h4>
                                     </div>
                                <div  style='display:none; background-color: #333333;
                                     width: 606px; height: 70px;padding-top:10px;margin-left:0px;' class='details_div' id='product_detail_".$value['product_id']."'>
                                    <form id = 'product_form_$value[product_id]'  method ='post' action = ''>";


                                   if($is_loos == 'T'){
                                       echo "  <div class=\"col-md-3\">
                                                <span style='color:#ffffff;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_size\" id=\"product_size\">
                                                         <option id='".$value['pot_price']."' value='tea_pot'>Pot - Rs :".number_format(($value['pot_price']),2)."</option>

                                                                <option value='0.025'>25g - Rs :".number_format((0.025)*($value['price']),2)."</option>
                                                                <option value='0.05'>50g - Rs :".number_format((0.05)*($value['price']),2)."</option>
                                                                <option value='0.08'>80g - Rs :".number_format((0.08)*($value['price']),2)."</option>
                                                                <option selected='selected' value='0.1'>100g - Rs :".number_format((0.1)*($value['price']),2)."</option>
                                                                <option value='0.25'>250g- Rs :".number_format((0.25)*($value['price']),2)."</option>
                                                                <option value='0.5'>500g- Rs :".number_format((0.5)*($value['price']),2)."</option>
                                                                <option value='1'>1Kg- Rs :".number_format($value['price'],2)."</option>

                                                           </select>";
                                       if($_SESSION['user_level']  == 0) {

                                           echo"<label class=\"see_price\">Click arrow to see the price</label>";
                                       }
                                       if($_SESSION['user_level'] != 0) {
														   
                                                           echo "<select  style='color:red;font-size:25px;width:85px;position:absolute;margin-left:10px;' name=\"spenzer_multy\" id=\"spenzer_multy\">";

                                       for($a=1;$a<=10;$a++){
                                           echo "<option value=".$a.">".$a."</option>";
                                           if($a == 5)
                                               break;
                                       }

                                       }
                                       echo "</select>

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
												   <
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

                                   }
                  if($_SESSION['user_level'] != 0) {
                                   echo " <div class='price col-md-3 pull-right'>

                                                <button style='margin-left:20%; margin-top: 0px;  padding-top: 0px;'
   type='button' value='' title='Add to Cart' class='addtocart-button cart-click pull-right' onclick='add_to_cart($value[product_id])'>Add to&nbsp;&nbsp;<img src='img/2772.png' width='16' height'16'> <span>&nbsp;</span></button>

                                     </div>"; }
                            if($is_loos == 'T'){
                                echo "<div style='text-align:right;margin-left:70px' class=\"col-md-3\">".$value['qty']."KG</div>";
                            }
                            else{
                                echo "<div style='text-align:right;margin-left:130px' class=\"col-md-3\">".$value['qty']."ITEMS</div>";
                            }
                            echo "  </div> </div></form>  ";



                         echo "</div>";
                        }}
                    else {
                        echo "No Products available";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>



    <br>
    
    <nav class="navbar navbar-default" role="navigation" style="margin-top: 50px;border:none;color:#ffffff;margin-bottom: 0;">
        <!-- Brand and toggle get grouped for better mobile display -->
        
 <?php if(isset($_SESSION['pending_cart'])) { ?>
    <div id='cssmenu'>
        <ul>
            <?php
            $pending_cart = $_SESSION['pending_cart'];
            foreach($pending_cart as $key=>$each_cart){ ?>
                <li onClick="get_pending_cart(<?php echo $key ?>)" class='active'><a style="background: dodgerblue;border-radius: 5px;margin-left: 5px; padding:7px;" href='#'>Cart &nbsp;<img src='img/2772.png' width='16' height'16'>  : <?php echo $key+1; ?></a></li>
            <?php }?>




        </ul>
    </div>
    <?php } ?>

        <div class="navbar-header">

            <a id="asd" href="#menu"><img style="margin-top: -800px;margin-left:-11px" src="img/handler2.png">    </a>


            <!--            <a class="navbar-brand" href="index.php">Point of Sales</a>-->
        </div>
        <?php
        $user_details = $user->getUserByID($_SESSION['user_id']);
        $user_status_id = $user_details['is_admin'];

        // getting daily sales
        $date_today = date("Y-m-d");
        $last_day_1 = date('Y-m-d',strtotime("-1 days"));
        $last_day_2 = date('Y-m-d',strtotime("-2 days"));
        $last_day_3 = date('Y-m-d',strtotime("-3 days"));
        $last_day_4 = date('Y-m-d',strtotime("-4 days"));
        $last_day_5 = date('Y-m-d',strtotime("-5 days"));
        $last_day_6 = date('Y-m-d',strtotime("-6 days"));
        $last_day_7 = date('Y-m-d',strtotime("-7 days"));

        $daily_sales_7 = $order_obj->get_daily_sales($last_day_7);
        $total_of_the_day_7 = $daily_sales_7['Total'];

        $daily_sales_6 = $order_obj->get_daily_sales($last_day_6);
        $total_of_the_day_6 = $daily_sales_6['Total'];



        $daily_sales_5 = $order_obj->get_daily_sales($last_day_5);
        $total_of_the_day_5 = $daily_sales_5['Total'];


        $daily_sales_4 = $order_obj->get_daily_sales($last_day_4);
        $total_of_the_day_4 = $daily_sales_4['Total'];

        $daily_sales_3 = $order_obj->get_daily_sales($last_day_3);
        $total_of_the_day_3 = $daily_sales_3['Total'];

        $daily_sales_2 = $order_obj->get_daily_sales($last_day_2);
        $total_of_the_day_2 = $daily_sales_2['Total'];

        $daily_sales_1 = $order_obj->get_daily_sales($last_day_1);
        $total_of_the_day_1 = $daily_sales_1['Total'];

        $daily_sales = $order_obj->get_daily_sales($date_today);
        $total_of_the_day =  $daily_sales['Total'];


//        foreach($user_details as $user_status){
//            $user_status_id = $user_status['is_admin'];
//        }

        ?>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div style="z-index: 20000000000; bottom: 0px;background-color:#333333;color:blue; font-size: 17px;margin-top:25px" class="collapse navbar-collapse collapse_cus" id="bs-example-navbar-collapse-1">
        <div class="sign-out">
           <?php if(isset($_SESSION['user_id'])){?>
               <a id="signing_out" class="btn button-blue"  href="logout.php">Log out</a>
                       <?php  } ?>

          </div>
          <div class="open_excel">
          <a href="Database 1.xlsx" target="_blank" class="btn button-blue">  Open Excel</a>
          </div>
            <ul class="nav navbar-nav navbar-right">
            <?php if($_SESSION['user_level'] ==1) { ?>
                <li>
                    <a id="cat_name_bar" class="pDetails" data-toggle="modal" style="color:#ffffff;padding-top:10px; padding-bottom:8px;"><?php echo ucwords(strtolower($category_each_name['category_name'])); ?></a>
                </li>
            <li>
                <a href="#" id="change_to_pos"  class="pDetails" data-toggle="modal" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Customer Mode</a>
            </li>
            <?php } ?>
            <input type="hidden" id="hidden_cart_total" name="hidden_cart_total" value="<?php echo $_SESSION['session_cart_total'] ; ?>">
                    <?php

                        if($user_status_id == $_SESSION['user_level'] ){?>



                            <li>
                                <a href="scan_add.php" class="pDetails" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Scan Add</a>
                            </li>

                            <li>
                                <a href="remove_cat.php" class="pDetails"   style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Remove</a>
                            </li>
                            <li>
                                <a href="Sort_cat.php" class="pDetails"   style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Sort</a>
                            </li>

<!--                            <li>-->
<!--                                <a href="#" class="pDetails"   style="color:#ffffff;"> Today :--><?php //echo $total_of_the_day ; ?><!--</a>-->
<!--                                <ul class="dropdown-menu">-->
<!--                                    <li>-->
<!--                                        <a href="" id="" ></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a hFref="#" id="" ></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a href="#" id="" data-toggle="modal" data-target="#cash_reports"></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports"></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports"></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports"></a>-->
<!--                                    </li>-->
<!--                                    <li>-->
<!--                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports"></a>-->

<!--                                    </li>-->
<!--                                </ul>-->
<!--                            </li>-->
                            <li class="btn-group dropup">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Today :<?php echo $total_of_the_day ; ?></a>
                                <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >

                                    <li>
                                        <a href="#" id="" data-toggle="modal" data-target="#cash_reports">1 Day Befor:<?php echo $total_of_the_day_1 ; ?></a>
                                    </li>
                                    <li>
                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports">2 Days Befor:<?php echo $total_of_the_day_2 ; ?></a>
                                    </li>
                                    <li>
                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports">3 Days Befor:<?php echo $total_of_the_day_3 ; ?></a>
                                    </li>
                                    <li>
                                        <a href="#" id="" data-toggle="modal" data-target="#sale_reports">4 Days Befor:<?php echo $total_of_the_day_4 ; ?></a>
                                    </li>
                                    <li>
                                        <a href="" id="" >5 Days Befor:<?php echo $total_of_the_day_5 ; ?></a>
                                    </li>
                                    <li>
                                        <a hFref="#" id="" >6 Days Befor:<?php echo $total_of_the_day_6 ; ?></a>
                                    </li>
                                    <li>
                                        <a hFref="#" id="" >7 Days Befor:<?php echo $total_of_the_day_7 ; ?></a>
                                    </li>

                                </ul>
                            </li>

                           <?php  if($user_status_id == 1) {
                            if($user_status_id == '1'){ ?>
<!--                                --><?php //if ($_SESSION['user_type_level'] ==1){ ?>


                            <li>
                                <a data-target="#cat_add" href="#" id="create_product"  class="pDetails" data-toggle="modal" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Category </a>
                            </li>
                            <li>
                                <a data-target="#cat_order"   href="#" id="cat_order_ed"  class="pDetails" data-toggle="modal" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Edit</a>
                            </li>
                            <li>
                                <a data-target="#product_add" href="#" id="add_cat"  class="pDetails" data-toggle="modal" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Product</a>
                            </li>
                                <?php

                                $today =  $date = date('Y-m-d');
                                $cash = $cash_obj->today_cash($today);
                                $end_cash = $cash_obj->end_cash_total($today);
                                $really_cash = $cash['cash_amount'];
                                $really_end_cash = $end_cash['Total'];

                                ?>
                                <li><a class="cash_feed_class" data-toggle="modal" data-target="#cash_feed_box" href="#" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">
                                        Start Cash:<?php  echo $really_cash ;?>
                                    </a>
                                </li>
                                <li>
                                    <a class="cash_feed_class" data-toggle="modal" data-target="#cash_feed_box_end" href="#" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">
                                        End Cash:<?php  echo $really_end_cash ;?>
                                    </a>
                                </li>
                                <!--reports-->
                                <li class="btn-group dropup">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:#ffffff;padding-top:10px; padding-bottom:8px;">Reports</a>
                                    <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >


                                        <li>
                                            <a href="#" id="" data-toggle="modal" data-target="#cash_reports">Cash Reports</a>
                                        </li>
                                        <li>
                                            <a href="#" id="" data-toggle="modal" data-target="#sale_reports">Sales Report</a>
                                        </li>
                                        <li>
                                            <a href="#" id="" data-toggle="modal" data-target="#product_sale_reports">Product Sales Report</a>
                                        </li>
                                        <li>
                                            <a href="#" id="" data-toggle="modal" data-target="#sale_reports_by_user">Sales Reports By User</a>
                                        </li>

                                        <li>
                                            <a href="#" id="" data-toggle="modal" data-target="#stock_report">Stock Report</a>
                                        </li>
                                        <li>

                                            <a href="\\computername\folder">Open report folder</a>
                                        </li>
                                    </ul>
                                </li>
                       <?php  }
                        else{

                            ?>
                            <li>
<!--                                <a href="#" class="pDetails"   style="color:#ffffff;"> Today :--><?php //echo $total_of_the_day ; ?><!--</a>-->
                                <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >
<!--                                    --><?php //if ($_SESSION['user_type_level'] ==1){ ?>
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
<!--                                    --><?php //} ?>
                                </ul>
                            </li>
                            <li>
                                <a  href="javascript:history.go(0)" id="change_to_admin"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Change to Admin</a>

                            </li>

                       <?php  }}}
                    ?>
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
                    <?php if($_SESSION['user_level'] != 0) { ?>
                    <a class="" href="checkout.php"><input id="cart_btn_total" style="margin-top: -8px;margin-bottom:-8px;padding:3px;background-color: #164293 !important;border: 0px;width: 200px;" type="button" class="btn-success btn btn-block" value=''></a>
                    <?php }  ?>
                </li>
                <?php if($_SESSION['user_level'] == 0) { ?>

                    <input type="hidden" id="hidden_cart_total" name="hidden_cart_total" value="<?php echo $_SESSION['session_cart_total'] ; ?>">
                <li>
                    <a  href="javascript:history.go(0)" id="change_to_admin"  class="pDetails" data-toggle="modal" style="color:#ffffff;">Admin Mode</a>
                </li>
               <?php }  ?>
                <?php if($_SESSION['user_level'] != 0) { ?>
                <li class="btn-group dropup">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"  style="color:#ffffff;padding-top:10px; padding-bottom: 8px;"> User :  <b class="caret"></b></a>
                    <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;">

                        </li>
                        <li>
                            <?php if(isset($_SESSION['user_id'])){?>
                                                                 <a id="signing_out" class=""  href="logout.php"> <span class="glyphicon glyphicon-user"></span>  Sign out</a>

                                                              <?php  } ?>

                        </li>
                
                <li>
                    <?php if($_SESSION['user_level']== 1 AND $_SESSION['is_super_admin']== 1){ ?>
                        <a id="add_user" class="pDetails"
                           href="#">Create a User</a>

                    <?php  } ?>
                </li>
                <li>
                    <?php if($_SESSION['user_level']== 1){ ?>
                        <a data-toggle="modal" data-target="#change_password" id="change_password" class="pDetails"
                           href="#">Change Password</a>

                    <?php  } ?>
                </li>
                <li>
                    <?php if($_SESSION['user_level']== 1 AND $_SESSION['is_super_admin']== 1){ ?>
                        <a id="manage_user" class="pDetails"
                           href="#">Manage Users</a>

                    <?php  } ?>
                </li>
                    </ul>
                </li>
                <?php }  ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </nav>

    <div class="gallery js-flickity">
        <?php
       // if($_SESSION['user_level'] != 0) {
            $class_count = 0;
            foreach($category_details as $value){?>

                <a href='index.php?cat_id=<?php echo $value['category_id']; ?>'><button  ondblclick='go_to_product(<?php echo  $value['category_id'];?>,<?php echo $value['category_id']?>)' style="background-color: royalblue;width: 150px;margin-left: 10px !important;"  class="btn btn-primary <?php if($class_count == 0){?> newlio <?php } ?>"><?php echo $value['category_name']; ?></button></a>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
               <?php $class_count++; ?>
            <?php }//} ?>
    </div>

        <div class="gallery js-flickity">
        <?php
           // if($_SESSION['user_level'] != 0) {
        foreach($product_details_default as $value){?>

             <div ondblclick='go_to_product(<?php echo  $value['product_id'];?>,<?php echo $value['category_id']?>)' style="background-color: royalblue;width: 150px;height: 150px;margin-left: 10px;"  class=""><p class="first_letter" style="color:white" align="center"><?php echo $value['product_name'] ; ?><img width="150" height="130" src="uploads/<?php echo $value['feature_image'] ; ?>"></p></div>
             &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

        <?php }//} ?>
</div>
    
    
    
    
   
    <!-- pending Cart by Spenzer  -->
    <style type="text/css">
        .first_letter::first-letter {
            font-size: 150%;
            color:#ffff00;
            text-decoration: bold;
        }
        .see_price{
            margin-top:-3px;
            width:60%;
            position: absolute;
        }
    </style>

    <script type="text/javascript">

        function get_pending_cart(cart_id){

            var pending_cart_id = cart_id ;
            $.ajax({
                url: "pending_model_process.php",
                type: "POST",
                cache: false,
                async:false,
                data: {make_model:true,cart_id:pending_cart_id},
                success: function(model_data){
                    $('#pending_model_body').html(model_data);
                    $('#hidden_pending_cart_key').val(pending_cart_id);
                    $('#pending_cart').modal('show');



                }
            });

        }

    </script>
    <!-- pending Cart Ends by Spenzer  -->
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
<script type="text/javascript" src="js/interface.js"></script>
  <script>
      $('#open_excel').click(function(){
          window.location.replace("file:///C:/Users/galle/Desktop/Database 1.xlsx");
      });
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
      var getUrlParameter = function getUrlParameter(sParam) {
          var sPageURL = decodeURIComponent(window.location.search.substring(1)),
              sURLVariables = sPageURL.split('&'),
              sParameterName,
              i;

          for (i = 0; i < sURLVariables.length; i++) {
              sParameterName = sURLVariables[i].split('=');

              if (sParameterName[0] === sParam) {
                  return sParameterName[1] === undefined ? true : sParameterName[1];
              }
          }
      };
      //      alert(getUrlParameter('pro_id'));


      if (typeof getUrlParameter('pro_id') !== 'undefined') {
          mySwiper.swipeTo(0);
      }
      else{
          mySwiper.swipeTo(2);
      }
      $(function() {
          $('nav#menu').mmenu();
      });

      function reinitSwiper(swiper) {
          setTimeout(function () {
              swiper.reInit();
          }, 500);
      }

      $('#cart_btn_total').click(function(){
          var url      = window.location.href;
          $.ajax({
              url: "extra_function.php",
              type: "POST",
              cache: false,
              async:false,
              data: {url:true,site_url:url},
              success: function(theResponse){

              }
          });
      });
      $('#slider_divs .swiper-visible-switch').click(function(){
          this.attr('disabled','disabled');
      });

      $('#slider_divs').click(function(event){
          event.stopPropagation();
      });

      function go_to_product(product_id,cat_id){
          var product_id =   product_id ;
          var cat_id = cat_id ;

//          alert(product_id);
//          alert(cat_id);
          window.location.href= "http://localhost/tea_showcase_ccc/index.php?cat_id="+cat_id+"&pro_id="+product_id+"";

//          $("'#"+product_id+"'").addClass( "swiper-slide-visible swiper-slide-active" );

      }


      function add_to_cart_sub_barcode(product_id,qty_from) {

          var val = product_id ;

          $.ajax({
              url: "extra_function.php",
              type: "POST",
              cache: false,
              async:false,
              data: {add_to_cart_detail_size:true,product_id:val,product_size:qty_from},
              success: function(theResponse){
                  var theResponse = $.parseJSON(theResponse);
                  var cart_total = 0;
                  $.each(theResponse, function(index, value) {

                      cart_total += (value.product_price*value.product_size);
                  });
                  $.ajax({
                      url: "extra_function.php",
                      type: "POST",
                      cache: false,
                      async:false,
                      data: {make_cart_value:true,total:cart_total.toFixed(2)},
                      success: function(theResponse){

                      }
                  });
                  $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                  $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                  $.ajax({
                      url: "model_process.php",
                      type: "POST",
                      cache: false,
                      async:false,
                      data: {make_model:true},
                      success: function(model_data){
                          $('#cart_body_model').html(model_data);
                          $('#added_cart').modal('show');
                          setTimeout(function(){ window.location.href='index.php'; }, 1000);
                        //  setTimeout(function(){ location.reload(); }, 1500);


                      }
                  });


              }
          });
      }

      function add_to_cart_from_barcode(product_id) {
          var val = product_id ;

                  $.ajax({
                      url: "extra_function.php",
                      type: "POST",
                      cache: false,
                      async:false,
                      data: {add_to_cart_detail_size:true,product_id:val,product_size:1},
                      success: function(theResponse){
                          var theResponse = $.parseJSON(theResponse);
                          var cart_total = 0;
                          $.each(theResponse, function(index, value) {

                              cart_total += (value.product_price*value.product_size);
                          });
                          $.ajax({
                              url: "extra_function.php",
                              type: "POST",
                              cache: false,
                              async:false,
                              data: {make_cart_value:true,total:cart_total.toFixed(2)},
                              success: function(theResponse){

                              }
                          });
                          $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                          $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                          $.ajax({
                              url: "model_process.php",
                              type: "POST",
                              cache: false,
                              async:false,
                              data: {make_model:true},
                              success: function(model_data){
                                  $('#cart_body_model').html(model_data);
                                  $('#added_cart').modal('show');
                                  setTimeout(function(){ window.location.href='index.php'; }, 1000);
                                //  setTimeout(function(){ location.reload(); }, 1500);


                              }
                          });


                      }
                  });
      }

      function add_to_cart(product_id) {
          var val = product_id ;
          var product_count = "";
          var item_data = $('#product_form_'+product_id).serializeArray();

          $.each( item_data, function( key, value ) {
              if(value.name == 'spenzer_multy') {
                  product_count = value.value;
              }
          });

          var product_qty = "";
          var product_size = "";
          var tea_pot_price = "";
          $.each( item_data, function( key, value ) {
                console.log(value);
              if(value.name == 'product_size') {
                  product_size = value.value * product_count;
                  tea_pot_price = value.id;

              }

              if(value.name == 'product_qty') {
                  product_qty = value.value;

              }

          });



          if(product_qty == ''){
              var cart_product_id = product_id;
              if(product_size == 'tea_pot') {
                  $.ajax({
                      url: "extra_function.php",
                      type: "POST",
                      cache: false,
                      async:false,
                      data: {tea_pot:true,tea_pot_price:tea_pot_price,product_id:val},
                      success: function(theResponse){
                          var theResponse = $.parseJSON(theResponse);
                          var cart_total = 0;
                          $.each(theResponse, function(index, value) {

                              cart_total += (value.product_price*value.product_size);
                          });

                          $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                          $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                          $.ajax({
                              url: "model_process.php",
                              type: "POST",
                              cache: false,
                              async:false,
                              data: {make_model:true},
                              success: function(model_data){
                                  $('#cart_body_model').html(model_data);
                                  $('#added_cart').modal('show');
                                 // setTimeout(function(){ window.location.href='index.php'; }, 1000);
                              }
                          });
                      }
                  });
              }
              else{
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

                      $.ajax({
                          url: "extra_function.php",
                          type: "POST",
                          cache: false,
                          async:false,
                          data: {make_cart_value:true,total:cart_total.toFixed(2)},
                          success: function(theResponse){
                              $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                              $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                              $.ajax({
                                  url: "model_process.php",
                                  type: "POST",
                                  cache: false,
                                  async:false,
                                  data: {make_model:true},
                                  success: function(model_data){
                                      $('#cart_body_model').html(model_data);
                                      $('#added_cart').modal('show');
                                      //setTimeout(function(){ window.location.href='index.php'; }, 1000);
                                  }
                              });

                          }
                      });

                  }
              });
              }
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

                      $.ajax({
                          url: "extra_function.php",
                          type: "POST",
                          cache: false,
                          async:false,
                          data: {make_cart_value:true,total:cart_total.toFixed(2)},
                          success: function(theResponse){

                          }
                      });
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+cart_total.toFixed(2)+'');
                      $.ajax({
                          url: "model_process.php",
                          type: "POST",
                          cache: false,
                          async:false,
                          data: {make_model:true},
                          success: function(model_data){
                                    $('#cart_body_model').html(model_data);
                                    $('#added_cart').modal('show');
                                    //setTimeout(function(){ window.location.href='index.php'; }, 1000);
                          }
                      });




                  }
              });
          }
      }
  </script>

<script type="text/javascript">
    $(document).ready(function(){

            $('.flickity-slider').addClass('newClass');

        /**
         * Barcode Started Spenzer
         * */


         var getUrlParameter = function getUrlParameter(sParam) {
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        };
        var balance = getUrlParameter('show_balance');
        if(typeof(balance) != "undefined" && balance !== null) {
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {balance_show:true},
                success: function(balance){

                    $('#balance_model').modal('show');
                    $('#balance_div').append(balance);
                }
            });
        }

        $('#product_name_for_barcode').change(function(){

            var product_id_selected = $('#product_name_for_barcode').val();
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {get_bar_code:true,id:product_id_selected},
                success: function(image){
                    var array = jQuery.parseJSON(image);
                    $('#barcode_image_here').html(array['image']);
                    $('#hidden_path_barcode').val(array['path']);
                    $('#action_here').html(array['link']);
                    //$('#link_here').attr("href").replace("file:12310041_783153441793584_7968966715744211821_o.jpg","file:"+array['path']);
                }
            });
        });


        $('#balance_done').click(function(){
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {done_balance:true},
                success: function(url){

                    $('#balance_model').modal('hide');
                    window.location = url;
                }
            });

        });

        $('#make_this_disappear').click(function(e){



            var pending_cart_id  = $('#hidden_pending_cart_key').val();

            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {make_this_disappear:true,pending_cart_id:pending_cart_id},
                success: function(url){


                }
            });

        });
        $('#make_this_active').click(function(e){



            var pending_cart_id  = $('#hidden_pending_cart_key').val();
            var pending_cart_total = $('#hidden_total').val();
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {make_this_active:true,pending_cart_id:pending_cart_id,pending_cart_total:pending_cart_total},
                success: function(url){


                }
            });

        });


        var barcode="";
        $(document).keydown(function(e) {

            var code = (e.keyCode ? e.keyCode : e.which);
           
            if(code==13){ // Enter key hit

                $.ajax({
                    url: 'extra_function.php',
                    data: {'barcode':true,'barcode_data':barcode,"starStatus":false},
                    type: 'post',
                    dataType : 'json',
                    success: function(data)
                    {
                        var got = data;

                        if(got['yes'] == 'null'){

                            if(got['grame'] == '') {


                                    alert('This product is not intended');
                                }
                                else{

                                    var product_id = got['pid'] ;
                                    var qty = got['grame'] ;

                                    add_to_cart_sub_barcode(product_id,qty);
                                }
                        }

                        else{

                            var id = got['yes'] ;
                            var qty = got['grame'] ;

                            add_to_cart_from_barcode(id);
                        }

                    }
                });
            }
            else {
               barcode=barcode+String.fromCharCode(code);
            }
        });

        // barcode end by spenzer

          var hidden_cart_total = $('#hidden_cart_total').val();

          $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+hidden_cart_total);

            $('#month_start_cash').click(function(){
                $('#name').modal('show');
                $('#cash_reports').modal('hide');
            });
        $("#report_date").datepicker({dateFormat:'yy-mm-dd'});
        $("#report_date2").datepicker({dateFormat:'yy-mm-dd'});
        $("#report_date3").datepicker({dateFormat:'yy-mm-dd'});

    $('#add_user').click(function(){
        $('#modal_add_user').modal('show');
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

   });

        $('#signing_out').click(function(){
            var url      = window.location.href;
            $.ajax({
                url: 'extra_function.php',

                data: {'make_page_cat_session':true,'url':url},
                type: 'post',
                success: function(data)
                {


                }
            });
        });


        $('#month_sales_report_per_user').click(function(){
            $('#sales_user_month').modal('show');
            $('#sale_reports').modal('hide');
        });

        $('#month_sales_report').click(function(){
            $('#sales').modal('show');
            $('#sale_reports').modal('hide');
        });
        $('#product_month_sales_report').click(function(){
            $('#sales2').modal('show');
            $('#product_sale_reports').modal('hide');
        });
        $('#date_sales_report').click(function(){
            $('#sales_date').modal('show');
            $('#sale_reports').modal('hide');
        });
        $('#date_sales_report_per_user').click(function(){
            $('#sales_date3').modal('show');
            $('#sale_reports').modal('hide');
        });
        $('#date_sales_report_product').click(function(){
            $('#sales_date2').modal('show');
            $('#product_sale_reports').modal('hide');
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
        $("#change_to_cust").click(function(){
            var hidden_cart_total = $('#hidden_cart_total').val();

            $.ajax({
                url: 'extra_function.php',
                dataType: 'json',
                data: {'change_to_cust':true,'cart':hidden_cart_total},
                type: 'post',
                success: function(data)
                {
                    window.location('index.php');
                    $("#cart_btn_total").prop('value', 'Cart Total : Rs.'+ hidden_cart_total+'');

                }
            });
        });
        $("#change_to_pos").click(function(){
            var hidden_cart_total = $('#hidden_cart_total').val();

            $.ajax({
                url: 'extra_function.php',
                dataType: 'json',
                data: {'change_to_pos':true,'cart':hidden_cart_total},
                type: 'post',
                success: function(data)
                {   $("#cart_btn_total_btm").prop('value', 'Cart Total : Rs.'+ hidden_cart_total+'');
                    window.location('index.php');


                }
            });
        });

        $(".product-edit-submit-form_awesome").on('click',function(e) {

            var price           = $('.edit_prduct_price').val();
            var url      = window.location.href;
            var name = $('.edit_prduct_name').val();
            var description = $('.edit_prduct_des').val();
            var pot_price = $('.pot_price').val();
            var barcode = $('.edit_prduct_barcode').val();

            var var_25 = $('.edit_prduct_barcode_25').val();
            var var_50 = $('.edit_prduct_barcode_50').val();
            var var_80 = $('.edit_prduct_barcode_80').val();
            var var_100 = $('.edit_prduct_barcode_100').val();
            var var_250 = $('.edit_prduct_barcode_250').val();
            var var_500 = $('.edit_prduct_barcode_500').val();
            var var_1000 = $('.edit_prduct_barcode_1000').val();


            var product_qty     = parseFloat($('.edit_prduct_quantity').val());
            var product_id     = $('.edit_product_id').val();

            var add_new_prduct_quantity = parseFloat($('.add_new_prduct_quantity').val());
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
                    data: {'edit_product_details':true,var_25:var_25,var_50:var_50,var_80:var_80,var_100:var_100,var_250:var_250,var_500:var_500,var_1000:var_1000,'pot_price':pot_price,'product_des':description,'product_id':product_id,'price': price,'name':name,'product_qty':product_qty,'add_new_prduct_quantity':add_new_prduct_quantity,'cal_operator':cal_operator,'barcode':barcode},
                    type: 'post',		// To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
                    success: function(data)  		// A function to be called if request succeeds
                    {



                        $('#product_edit_pop').modal('hide');
                       // window.location.href=url;

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
<div class="modal fade" id="change_password8" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Password</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form method="post" action="">



                    <div class="form-group">
                        <label for="product_description">New Password</label>
                        <input  id="password" name="password" type="password" class="form-control"
                            />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden"
                               id="hidden_user">
                    </div>

                    <div class="form-group">
                        <label for="product_description"> Retype Password </label>
                        <input  id="password_re" name="password_re" type="password" class="form-control" />

                    </div>



                    <div class="form-group text-center">
                        <button id="close_new_user" type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>

                        <input  id="change_pass"
                                name="change_pass" type="submit" class="product_create btn btn-success btn-login-submit" value="
Change " />
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


<div class="modal fade" id="manage_user2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Manage Users</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">
                        <div id="message"></div>
                        <div></div>


                        <form method="post" action="">



                            <div class="form-group">
                                <label for="product_description"> Select User </label>
                                <select name="select_user" class="form-control" id="users_list">
                                    <?php
                                    $customer = new Customer();
                                    $users= $customer->existing_customers();
                                    foreach($users as $each_customer){?>
                                        <option value="<?php echo $each_customer['id']; ?>"><?php echo $each_customer['username']; ?></option>
                                    <?php }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="product_description">Select Status</label>
                                <select name="users_list" class="form-control" id="Status_list">
                                    <option value="1">Admin</option>
                                    <option value="0">User</option>
                                </select>
                            </div>

                            <div class="form-group text-center">
                                <button id="close_new_user" type="button" class="btn btn-danger col-lg-push-2" data-dismiss="modal">Close</button>

                                <input  id=""
                                        name="save_user" type="submit" class="btn btn-success btn-login-submit" value="Select" />
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
<div id="please_wait_model" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            ...
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







                    <div class="form-group">
                        <label for="product_description">Category Description</label>
                        <input  id="cat_description" name="cat_description" type="text" class="form-control" />
                        <input value="<?php echo $_SESSION['user_id'] ; ?>" type="hidden" id="hidden_user">
                    </div>












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
                    <label>Cash Reports</label>


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
<!--                <button type="button" class="btn btn-primary" id="cash_feed_save_end">Add value</button>-->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="product_sale_reports" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Each Product Sales Reports</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post">
                        <label>Product Sales Reports</label>
                        <br/>
                        <select name="uniq_pro_name" type="text" class="form-control" name="product_name">
                            <?php
                            $product_obj = new Product();
                            $list_of_product = $product_obj->list_priduct_inventory();

                            foreach($list_of_product as $each_product){ ?>
                                <option value="<?php echo $each_product['product_id'] ; ?>"><?php echo $each_product['product_name'] ; ?></option>
                         <?php  } ?>
                        </select>
                        <br/>
                        <label>Product Sales Reports</label>


                        <br/>

                            <input value="Today's Report" type="submit" name="product_today_sale_report" class="btn btn-primary" id="product_today_sale_report">
                            <input value="This Week Report" name="product_week_sale_report" type="submit"class="btn btn-primary" id="product_week_sale_report">
                            <button type="button" name="product_month_sales_report" class="btn btn-primary" id="product_month_sales_report"> Month Report</button>
<!--                            <button   type="button" name="date_sales_report_product" class="btn btn-primary" id="date_sales_report_product">Date Sales</button>-->
                            <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="sale_reports" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Each Product Sales Reports</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post">
                        <label>Product Sales Reports</label>
                        <br/>






                        <input value="Today's Report" type="submit" name="today_sale_report" class="btn btn-primary" id="today_sale_report">
                        <input value="This Week Report" name="week_sale_report" type="submit"class="btn btn-primary" id="week_sale_report">
                        <button type="button" name="month_sales_report" class="btn btn-primary" id="month_sales_report"> Month Report</button>
                        <button   type="button" name="date_sales_report_product" class="btn btn-primary" id="date_sales_report">Date Sales</button>
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="product_barcode_create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Barcodes For your Products</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post">
                        <label>Please Select a Product to get Barcode</label>
                        <br/>
                        <select type="text" class="form-control" name="product_name_for_barcode" id="product_name_for_barcode">
                            <?php
                            $product_obj = new Product();
                            $list_of_product = $product_obj->list_priduct_inventory();

                            foreach($list_of_product as $each_product){ ?>
                                <option value="<?php echo $each_product['product_id'] ; ?>"><?php echo $each_product['product_name'] ; ?></option>
                            <?php  } ?>
                        </select>
                        <br/>
                        <div style="width: 100%;height: 100px;" id="barcode_image_here">
                            <h4 style="vertical-align: middle" align="center">No Product Selected</h4>
                        </div>
                        <br>


<!--                            <input value="Save it" type="submit" name="get_saved_barcoded" class="btn btn-primary" id="get_saved_barcoded">-->

                            <input type="hidden" name="hidden_path_barcode" id="hidden_path_barcode" value="">
<!--                            <a href='file:New Vithanakande FBOPFXSP.png' download>Save</a>-->
                            <div id="action_here"></div>
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>
<!--<div class="modal fade" id="sale_reports" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">-->
<!--    <div class="modal-dialog">-->
<!--        <div class="modal-content">-->
<!--            <div class="modal-header">-->
<!--                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
<!--                <h4 class="modal-title" id="myModalLabel">Sales Reports</h4>-->
<!--            </div>-->
<!--            <div class="modal-body">-->
<!--                <div class="form-group">-->
<!--                    <label>Sales Reports</label>-->
<!---->
<!---->
<!--                    <br/>-->
<!--                    <form action="" method="post">-->
<!---->
<!--                        <br>-->
<!--                        <input value="Today's Report" type="submit" name="today_sale_report" class="btn btn-primary" id="today_start_cash">-->
<!--                        <input value="This Week Report" name="week_sale_report" type="submit"class="btn btn-primary" id="week_start_cash">-->
<!--                        <button type="button" class="btn btn-primary" id="month_sales_report"> Month Report</button>-->
<!--                        <button   type="button" class="btn btn-primary" id="date_sales_report">Date Sales</button>-->
<!--                        <br><br>-->
<!--                    </form>-->
<!---->
<!--                </div>-->
<!---->
<!--            </div>-->
<!---->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div class="modal fade" id="sale_reports_by_user" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Sales Reports By User</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post">
                        <label>Sales Reports</label>
                        <br/>
                            <?php
                            $user_obj = new User();
                            $user_details = $user_obj->list_users();

                            ?>
                            <select  class="form-control" name="user_name">
                                <?php
                                foreach($user_details as $each_user){?>
                                    <option value="<?php echo $each_user['id'] ; ?>"><?php echo  $each_user['username'] ;?></option>
                                <?php } ?>
                            </select>
                            <br>
                            <input value="Today's Report" type="submit" name="user_for_today_sale_report" class="btn btn-primary" id="today_start_cash">
                            <input value="This Week Report" name="user_for_week_sale_report" type="submit"class="btn btn-primary" id="week_start_cash">
                            <button type="button" class="btn btn-primary" id="month_sales_report_per_user"> Month Report</button>
                            <button   type="button" class="btn btn-primary" id="date_sales_report_per_user">Date Sales</button>
                            <br><br>
                    </form>

                </div>

            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="stock_report" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Get Stock Report</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post">

                        <br>
                        <input value="Get stock in hand" type="submit" name="get_stock_in_hand" class="btn btn-primary" id="">


                        <br><br>
                    </form>

                </div>

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
                        <select class="form-control" name="month_cash_year">
                            <?php
                            for($sale_year_report = 2015;date('Y')>=$sale_year_report;$sale_year_report++) {
                                ?>
                                <option value="<?php echo $sale_year_report; ?>" <?php if(date('Y') ==$sale_year_report ) { echo "selected='selected'"; } ?>><?php echo $sale_year_report; ?></option>

                            <?php } ?>
                        </select>
                        <br>
                        <br>
                        <select class="form-control" name="month_cash">
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
                    <select class="form-control" name="month_sale_year">
                        <?php
                        for($sale_year_report = 2015;date('Y')>=$sale_year_report;$sale_year_report++) {
                        ?>
                        <option value="<?php echo $sale_year_report; ?>" <?php if(date('Y') ==$sale_year_report ) { echo "selected='selected'"; } ?>><?php echo $sale_year_report; ?></option>

                        <?php } ?>
                    </select>
                    <br>
                    <br>
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
<div class="modal fade" id="sales2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                        <select type="text" class="form-control" name="u_month_pro_name">
                            <?php
                            $product_obj = new Product();
                            $list_of_product = $product_obj->list_priduct_inventory();

                            foreach($list_of_product as $each_product){ ?>
                                <option value="<?php echo $each_product['product_id'] ; ?>"><?php echo $each_product['product_name'] ; ?></option>
                            <?php  } ?>
                        </select>
                        <br>
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
                        <input type="submit" name="product_Month__sale_Report_selected" value="Month Report" class="btn btn-primary" id="month_start_cash">
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
<div class="modal fade" id="sales_date2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Please Select a Date And the Product  to Generate a Date Report</label>
                    <br/>
                    <form action="" method="post" >
                        <select type="text" class="form-control" name="product_name">
                            <?php
                            $product_obj = new Product();
                            $list_of_product = $product_obj->list_priduct_inventory();

                            foreach($list_of_product as $each_product){ ?>
                                <option value="<?php echo $each_product['product_id'] ; ?>"><?php echo $each_product['product_name'] ; ?></option>
                            <?php  } ?>
                        </select>
                        <br>
                        <input type="text" id="report_date2" name="report_date2">
                        <br>
                        <br>
                        <input type="submit" name="date_sale_Report_product" value="Submit" class="btn btn-primary" id="get_date_report">
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sales_date3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select date</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Please Select a Month And the User  to Generate a Date Report</label>
                    <br/>
                    <form action="" method="post" >
                        <?php
                        $user_obj = new User();
                        $user_details = $user_obj->list_users();

                        ?>
                        <select  class="form-control" name="user_name">
                            <?php
                            foreach($user_details as $each_user){?>
                                <option value="<?php echo $each_user['id'] ; ?>"><?php echo  $each_user['username'] ;?></option>
                            <?php } ?>
                        </select>
                        <br>
                        <input type="text" id="report_date3" name="report_date3">
                        <br>
                        <br>
                        <input type="submit" name="date_sale_Report_product_per_user" value="Submit" class="btn btn-primary" id="get_date_report">
                        <br><br>
                    </form>

                </div>

            </div>
            <div class="modal-footer">

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sales_user_month" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select Month</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <form action="" method="post" >
                    <label>Please Select a Month and The <User></User> to Generate a Monthly Report Per User</label>


                    <br/>

                        <label>Sales Reports</label>
                        <br/>
                        <?php
                        $user_obj = new User();
                        $user_details = $user_obj->list_users();

                        ?>
                        <select  class="form-control" name="user_name">
                            <?php
                            foreach($user_details as $each_user){?>
                                <option value="<?php echo $each_user['id'] ; ?>"><?php echo  $each_user['username'] ;?></option>
                            <?php } ?>
                        </select>
                        <br>
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
                        <input type="submit" name="Month__sale_Report_per_user" value="Month Report" class="btn btn-primary" id="month_start_cash">
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

<div id="balance_model" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div  class="modal-content">
            <h2 id="balance_div">Balance Is :</h2><input id="balance_done" value="Done" type="button" class="btn-primary btn">
        </div>
    </div>
</div>

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

<div id="added_cart" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">


            <table class="table">
                <thead>
                <tr>
                    <?php if($_SESSION['user_level'] != 0) { ?>
                    <td align="center" colspan="6">
                        <a id="cart_btn_total" class="" href="checkout.php"><input id="cart_btn_total" style="margin-top: -5px;background-color: #164293 !important;border: 0px;width: 200px;" type="button" class="btn-success btn btn-block" value='Check out'></a>
                    </td>
                    <?php } ?>
                </tr>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody id="cart_body_model">



                </tbody>
            </table>

        </div>
    </div>
</div>
<div id="pending_cart" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <input type="hidden" id="hidden_pending_cart_key" value="">
            <table class="table">
                <thead>
                <tr>
                    <td align="center" colspan="3">
                        <a id="make_this_active" class="" href="checkout.php"><input id="make_this_active" style="margin-top: -5px;background-color: #164293 !important;border: 0px;width: 200px;" type="button" class="btn-success btn btn-block" value='Make This Active'></a>

                    </td>
                    <td align="center" colspan="3">
                        <a id="make_this_disappear" class="" href=""><input id="make_this_active" style="margin-top: -5px;border: 0px;width: 200px;" type="button" class="btn-danger btn btn-block" value='Ignore This'></a>

                    </td>
                </tr>
                <tr>
                    <th>#</th>
                    <th></th>
                    <th>Product Name</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
                </thead>
                <tbody id="pending_model_body">



                </tbody>
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
                            <div class="form-group">
                                <label for="price">Product Barcode</label>

                                <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode"  />
                            </div>

                            <?php if(isset($error_message['price'])){   ?>
                                <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">×</span></button>
                                    <?php   echo $error_message['price'] ; ?>
                                </div>
                            <?php }  ?>
                            <?php   if($is_loos == 'T'){ ?>
                            <div class="form-group">

                                <table class="" align='center'>
                                    <tr>
                                        <th align='center'>
                                            &nbsp;&nbsp;Gramage
                                        </th>
                                        <th align='center'>
                                            &nbsp;&nbsp;Barcode
                                        </th>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            25g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_25"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            50g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_50"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            80g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_80"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            100g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_100"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            250g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_250"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            500g
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_500"  />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align='center'>
                                            1kg
                                        </td>
                                        <td>
                                            <input  id="barcode" name="barcode" type="text"  class="form-control edit_prduct_barcode_1000"  />
                                        </td>
                                    </tr>
                                </table>


                            </div>
                            <?php   } ?>


                            <div class="form-group">
                                <label for="qty"> Current Quantity</label>
                                <input size="4" id="product_qty" name="current_qty" type="text" class="form-control edit_prduct_quantity"  maxlength="3"  disabled="disabled" />
                            </div>
                            <div class="form-group">
                                <label for="qty">Tea Pot Price</label>
                                <input size="4" id="pot_price" name="pot_price" type="text" class="form-control pot_price" />
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
                                <input type="hidden" name="hidden_cat" id="hidden_cat" class="form-control edit_prduct_price"  />
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



<div class="modal fade" id="search_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Search Product</h4>
            </div>
            <div class="modal-body">

                <div class="col-sm-12">

                    <!--Start Form-->


                    <div class="row">

                        <form  data-toggle="validator" id="product-edit-form" method="post" action="">


                            <div class="form-group">
                                <label for="price">Search Keywords</label>

                                <input type="text" id = "search_keyword" name="search_keyword" class="form-control" autofocus>
                                <br>
                                <table>

                                        <tr>
                                            <td>Product ID</td>
                                            <td>Product Name</td>
                                            <td>Price (LKR )</td>
                                            <td>Category</td>
                                            <td>Image</td>
                                        </tr>

                                <tr>
                                    <td colspan="5">
                                        <table class="table" id ="result_tred">
                                            <tr>
                                                    <td colspan="5">No Product Results</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                </table>

                            </div>

                            <div class="form-group text-center">
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

<!--                                <input name="update_image" type="submit" class="product-edit-submit-form btn btn-success btn-login-submit" value="Go To Product" />-->

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
    $('#search_product').on('shown.bs.modal', function () {
        $('#search_keyword').focus();
    })
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

                $('#hidden_cat').attr('value',data['category_id']);

                return false;

            }
        });

        e.preventDefault();
    });
    $('#change_password').click(function(){
        $('#change_password8').modal('show');
    });



    $('#manage_user').click(function(){
       $('#manage_user2').modal('show');
    });





    $(".edit_product").on('click',function(e) {

        product_id = $(this).attr('id');
        var url      = window.location.href;


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
                $('.pot_price').attr('value',data['pot_price']);
                // getting differernt barcordes

                $('.edit_prduct_barcode').attr('value',data['barcode']);

                $('.edit_prduct_barcode_25').attr('value',data['bar25']);
                $('.edit_prduct_barcode_50').attr('value',data['bar50']);
                $('.edit_prduct_barcode_80').attr('value',data['bar80']);
                $('.edit_prduct_barcode_100').attr('value',data['bar100']);
                $('.edit_prduct_barcode_250').attr('value',data['bar250']);
                $('.edit_prduct_barcode_500').attr('value',data['bar500']);
                $('.edit_prduct_barcode_1000').attr('value',data['bar1000']);

                $('textarea#edit_product_description').text(data['product_description']);
               // $(".edit_prduct_des").text(result.data['product_description']);

                return false;

            }
        });
        e.preventDefault();
    });

(function($) {
if (!$.curCSS) {
$.curCSS = $.css;
}
})(jQuery);
</script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

</body>
</html>
