<?php
if(isset($_POST['make_model'])){

    $cart_id = $_POST['cart_id'];

    include_once("common_header.php");
    error_reporting(0);
    $pro_obj = new Product();
    $cat_obj = new Category();
    $customer_obj = new Customer();
    $get_customer = $customer_obj->existing_customers();


    $selected_cart = $_SESSION['pending_cart'][$cart_id];

    $list_cat = $cat_obj->list_category();
    $cart_total = 0;
    $cart_items = array();
    $cart_temp_array = array();
    if(isset($selected_cart)) {

        if(!empty($selected_cart)) {
            foreach($selected_cart as $each_item) {
                if($each_item['product_qty'] == '') {
                    $cart_total += ($each_item['product_price'] * $each_item['product_size']);
                } else {
                    $cart_total += ($each_item['product_price'] * $each_item['product_qty']);
                }

                if(empty($cart_temp_array)) {
                    array_push($cart_temp_array,$each_item['product_id']);
                    $cart_items[$each_item['product_id']]['product_id'] = $each_item['product_id'];
                    if($each_item['product_qty'] == '') {
                        $cart_items[$each_item['product_id']]['product_qty'] = $each_item['product_size'];
                        $cart_items[$each_item['product_id']]['type'] = 'Kg';
                        $cart_items[$each_item['product_id']]['order_type'] = $each_item['order_type'];
                    } else {
                        $cart_items[$each_item['product_id']]['product_qty'] = $each_item['product_qty'];
                        $cart_items[$each_item['product_id']]['type'] = ' Item(s)';
                    }

                    $cart_items[$each_item['product_id']]['product_price'] = $each_item['product_price'];
                } else {
                    if(in_array($each_item['product_id'],$cart_temp_array)) {
                        if($each_item['product_qty'] == '') {

                            $cart_items[$each_item['product_id']]['product_qty'] = $cart_items[$each_item['product_id']]['product_qty'] + $each_item['product_size'];
                            $cart_items[$each_item['product_id']]['type'] = 'Kg';
                            $cart_items[$each_item['product_id']]['order_type'] = $each_item['order_type'];
                        } else {

                            $cart_items[$each_item['product_id']]['product_qty'] = $cart_items[$each_item['product_id']]['product_qty'] + $each_item['product_qty'];
                            $cart_items[$each_item['product_id']]['type'] = ' Item(s)';
                        }

                    } else {
                        array_push($cart_temp_array,$each_item['product_id']);
                        $cart_items[$each_item['product_id']]['product_id'] = $each_item['product_id'];
                        if($each_item['product_qty'] == '') {
                            $cart_items[$each_item['product_id']]['product_qty'] = $each_item['product_size'];
                            $cart_items[$each_item['product_id']]['type'] = 'Kg';
                            $cart_items[$each_item['product_id']]['order_type'] = $each_item['order_type'];
                        } else {
                            $cart_items[$each_item['product_id']]['product_qty'] = $each_item['product_qty'];
                            $cart_items[$each_item['product_id']]['type'] = ' Item(s)';
                        }

                        $cart_items[$each_item['product_id']]['product_price'] = $each_item['product_price'];
                    }
                }

            }


        }
    }

    $_SESSION['pending_shopping_cart_final'] = $cart_items;

    $the_variable = "";
    $k = 1;
    foreach($cart_items as $each_cart_item) {
        $product_details = $pro_obj->list_priduct_by_product_id($each_cart_item['product_id']);
//        print_r($product_details);
//        exit;
        $the_variable .="<input type='hidden' id='hidden_total' value='".  number_format($cart_total,2)."'>";
        $the_variable .="
        <tr>
            <td><a href='#'> </a></td>
            <td style='width: 100px; height: 100px;'>

            <img width='25' height='25' alt='50%x50%' src='uploads/".$product_details['feature_image']."' style='height: 100%; width: 100%; display: block;'>

            </td>";

        $the_variable .="<td>".$product_details['product_name'];
                if($each_cart_item['order_type']== 'tea_pot'){

                 }

        $the_variable .="</td>";
        $the_variable .="<td>";
                if($each_cart_item['order_type']== 'tea_pot'){  $the_variable.= $product_details['pot_price']; } else {  $the_variable.= number_format($each_cart_item['product_price'],2); }
        $the_variable .="</td>";
            $the_variable .="<td>".$each_cart_item['product_qty']; if($each_cart_item['order_type'] != 'tea_pot') { $each_cart_item['type']; }
        $the_variable .="</td>";
        $the_variable .="<td>";
            if($each_cart_item['order_type']== 'tea_pot'){ $the_variable.= number_format(($each_cart_item['product_qty']*$product_details['pot_price']),2);} else { $the_variable.= number_format(($each_cart_item['product_qty']*$each_cart_item['product_price']),2); }
            $the_variable .="</td>";
        $the_variable .="</tr>";



        $k++;
    }
    $the_variable .="<tr>";
    $the_variable .="<td colspan='6' align='center'>";
    $the_variable .= "<h1>Grand Total  :".  number_format($cart_total,2)."</h1>";
    $the_variable .="</td>";
    $the_variable .="</tr>";
    echo $the_variable;

}




?>