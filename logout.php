<?php
require_once 'common_header.php';
$user_obj = new User();
$user_login_history = array();
$user_login_history["user_id"] = $_SESSION['user_id'];
$user_login_history['user_logout_time'] = date("Y-m-d h:i:s");
$user_login_history['login_type'] = "OUT";
$user_login_history['logout_method'] = "OFF";
$user_obj->insert_user_log($user_login_history);

$cash_obj = new CashFeed();
$date = date("Y-m-d");
$type = '0';
$check_results = $cash_obj->check_cash_already($date,$type);
//if($check_results == true){
////    session_destroy();
////    header('Location: login.php');
////    exit;
//}
//else{
    header('Location: check_cash_end.php');
    exit;
//}


?>

