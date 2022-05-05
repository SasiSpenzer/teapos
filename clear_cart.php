<?php
session_start();
$_SESSION['shopping_cart_final'] = "";
$_SESSION['shopping_cart'] = "";
$_SESSION['session_cart_total'] = "";
$url = $_SESSION['sales_url'] ;
header("Location:".$url."");




 ?>