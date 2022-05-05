<?php
require_once 'common_header.php';
if(isset($_GET['pid'])) {

	$product_id = trim($_GET['pid']);

	foreach($_SESSION['shopping_cart'] as $each_key=>$each_item) {


		if($each_item['product_id'] == $product_id) {


            if(empty($_SESSION['shopping_cart'][$each_key]['product_size'])){
                $sub = $_SESSION['shopping_cart'][$each_key]['product_price']*$_SESSION['shopping_cart'][$each_key]['product_qty'] ;

                $_SESSION['session_cart_total'] = $_SESSION['session_cart_total'] -  $sub ;
            }
            else{

                $sub = $_SESSION['shopping_cart'][$each_key]['product_price']*$_SESSION['shopping_cart'][$each_key]['product_size'] ;

                $_SESSION['session_cart_total'] = $_SESSION['session_cart_total'] -  $sub   ;
            }

            echo $_SESSION['session_cart_total'] ;

			unset($_SESSION['shopping_cart'][$each_key]);
		}
	}
	
	
}
header("Location:checkout.php");
