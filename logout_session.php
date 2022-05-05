<?php
require_once 'common_header.php';
$user_obj = new User();
$user_login_history = array();
$user_login_history["user_id"] = $_SESSION['user_id'];
$user_login_history['user_logout_time'] = date("Y-m-d h:i:s");
$user_login_history['login_type'] = "OUT";
$user_login_history['logout_method'] = "AWAY";
$user_obj->insert_user_log($user_login_history);
session_destroy();
header("Location:index.php");

?>