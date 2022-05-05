<?php
session_start();
ob_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';

}

if(!isset($_SESSION['user_id']) || !isset($_SESSION['start_cash_session'])){

    header('Location: login.php');
}

?>
