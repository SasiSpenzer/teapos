<?php
include_once("common_header.php");

if(isset($_POST['cat_details'])){
    $category_id = $_POST['cat_id'];
    $is_loos = 'T';
    $product = new Product();

    $product_result = $product->select_product_by_cat_id($category_id);

    $output = '';
    $k = 0;
    $x = 0;
    foreach($product_result as $value){
        $x++;
        if($k == 0) {
            $output  .= "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide swiper-slide-active sl-".$x." \" style=\"width: 600px; height: 600px;\">";
        } else if($k == 1) {
            $output  .= "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide swiper-slide-visible sl-".$x." \" style=\"width: 600px; height: 600px;\">";
        } else {
            $output  .= "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide sl-".$x." \" style=\"width: 600px; height: 600px;\">";
        }

        $output .= "<div class=\"title\"></div>";
        $output .=  "<div id='image_".$value['product_id']."' class='image_class' style='padding-top:0px;'><img width='607' height='600'  src=\"uploads/".$value['feature_image']."\"></div>";

        if($_SESSION['user_level'] == 1) {?>
           <div>

                <a href="#" id="<?php echo $value['product_id']  ; ?>" style="position: absolute; right: 48px; top: 5px;" class="edit_product" data-toggle="modal" data-target="#added_cart">
                    <span class="glyphicon glyphicon-pencil btn btn-warning"></span>
                </a>

                <a href='#' id="<?php echo $value['product_id']  ;?>"  style='position: absolute; right: 5px; top: 5px;' class='delete_product' onclick="delete_product(<?php echo $value['product_id']  ;?>)">
                    <span class='glyphicon glyphicon-remove btn btn-danger'></span>
                </a>

            </div>
        <?php   }
        $output .= "<div style='padding-top: 0px;'>
                        <div style='display:none; background-color: #938a6a;
                                     width: 606px; height: 80px;padding-top:5px;margin-left:0px;' class='details_div' id='product_detail_".$value['product_id']."'>
                                    <form id = 'product_form_$value[product_id]'  method ='post' action = ''>";
        if($is_loos == 'T'){
        $output .=   "  <div class=\"col-md-4\">
                                                <span style='color:blue;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_size\" id=\"product_size\">


                                                                <option value='0.1'>100g - Rs :".(0.1)*($value['price']).".00</option>
                                                                <option value='0.25'>250g- Rs :".(0.25)*($value['price']).".00</option>
                                                                <option value='0.5'>500g- Rs :".(0.5)*($value['price']).".00</option>
                                                                <option value='1'>1Kg- Rs :".$value['price'].".00</option>

                                                           </select>
                                                   </div>
                                        </div>";
        } else {
            $output .=   "<div class=\"col-md-2\">

                                                            <span style='color:blue;font-weight:bold;'>Price</span>

                                                            <div style='font-size:18px;color:blue;font-weight:bold;'>Rs :".$value['price']." </div>

                                                   </div>";
            $output .=  "  <div class=\"col-md-2\">
                                                <span style='color:blue;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_qty\" id=\"product_qty\">";

            for($a=1;$a<=$product_qty;$a++){
                echo "<option value=".$a.">".$a."</option>";
                if($a == 5)
                    break;
            }



            $output .=   "</select>
                                                   </div>
                                        </div>";
        }
        $output .=  " <div class='price col-md-3 pull-right'>

                                                <button style='margin-left:20%; margin-top: 0px;  padding-top: 0px;'
   type='button' value='' title='Add to Cart' class='addtocart-button cart-click pull-right' onclick='add_to_cart($value[product_id])'>Add to&nbsp;&nbsp;<img src='img/2772.png' width='16' height'16'>&nbsp;&nbsp;<span>&nbsp;</span></button>

                                     </div>
                                </div>
                                </div></form>
                            ";
        $output .=    "</div>";


    }
?>
</div>
</div>
<?php
        $output .= "</div>";
        $k++;
    echo $output;
    }




if(isset($_POST['add_to_cart_detail_size'])){
    $product_id = $_POST['product_id'];

    $product_size = $_POST['product_size'];
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    if(!isset($_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['product_id'] = $product_details['product_id'];
    $order_items['product_price'] = $product_details['price'];
    $order_items['product_qty'] = '';
    $order_items['product_size'] = $product_size;
    array_push($_SESSION['shopping_cart'],$order_items);
    echo json_encode($_SESSION['shopping_cart']);

}
if(isset($_POST['add_to_cart_detail_qty'])){
    $product_id = $_POST['product_id'];
    $product_qty = $_POST['product_qty'];

    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    if(!isset($_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['product_id'] = $product_details['product_id'];
    $order_items['product_price'] = $product_details['price'];
    $order_items['product_qty'] = $product_qty;
    $order_items['product_size'] = '';
    array_push($_SESSION['shopping_cart'],$order_items);
    echo json_encode($_SESSION['shopping_cart']);

}

if(isset($_POST['get_customer_data'])){
    $customer_data = array();
    $customer_details = $_POST['customer_data_array'];
    $customer_data['customer_name'] = $customer_details['customer_name'];
    $customer_data['contact_no'] = $customer_details['contact_no'];
    $customer_data['email'] = $customer_details['email'];

    $customer_obj = new Customer();
    $id = $customer_obj->create_customer($customer_data);
    echo trim($id) ;
}

if(isset($_POST['add_to_cart_single'])){
    $product_id = $_POST['product_id'];
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    if(!isset($_SESSION['shopping_cart'])) {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['product_id'] = $product_details['product_id'];
    $order_items['product_price'] = $product_details['price'];
    $order_items['product_qty'] = 1;
    array_push($_SESSION['shopping_cart'],$order_items);
    echo json_encode($_SESSION['shopping_cart']);
}

if(isset($_POST['add_order_array'])){

    $order_data = $_POST['order_array'];
    $order_obj = new Order();
    $add_order = $order_obj->add_order($order_data);
    $order_id = $add_order ;
    echo $order_id;

}
if(isset($_POST['cart_items_array'])){
    $data =   $_POST['cart_items_json'] ;
    if(empty($data)){
        echo 'empty';
    }

}

if(isset($_POST['add_cash_feed'])){

    $cash_amount_array = $_POST['cash_feed_array'];
    $cash_obj = new CashFeed();
    $results = $cash_obj->save_cash_feed($cash_amount_array);
}


if(isset($_POST['get_product_details'])) {
    if($_POST['get_product_details'] == true) {

        $product_id = $_POST['product_id'];

        $pro_obj = new Product();
        $cat_obj = new Category();

        $is_prouct = $pro_obj->list_priduct_by_product_id($product_id);


        $get_category_name = $cat_obj->get_cat_name_by_cat_id($is_prouct['category_id']);

        $data_array = array_merge($is_prouct, $get_category_name);

        echo json_encode($data_array);


    }

}


if(isset($_POST['delete_product_by_id'])) {
    if($_POST['delete_product_by_id'] == true) {

        $pro_obj = new Product();
        $product_id = $_POST['product_id'];

        echo  $is_added_details =  $pro_obj->delete_product_by_id($product_id);

    }
}

if(isset($_POST['edit_product_details'])) {
    if($_POST['edit_product_details'] == true) {

        $pro_obj = new Product();
        $cat_obj = new Category();


        $product_id = $_POST['product_id'];

        $product_data = array();
        $product_data['price']                  = isset ( $_POST ['price'] ) ? $_POST ['price'] : '';

        $product_data['product_qty']            = isset ( $_POST ['product_qty'] ) ? $_POST ['product_qty'] : '';
        $product_data['add_new_prduct_quantity']            = isset ( $_POST ['add_new_prduct_quantity'] ) ? $_POST ['add_new_prduct_quantity'] : '';
        $product_data['cal_operator']            = isset ($_POST ['cal_operator'] ) ? $_POST ['cal_operator'] : '';
        $error_message                          = array();

        // cal_operator value 1  = +
        // cal_operator value 0  = -




        if($product_data['cal_operator']==1){
            $tot_qty = $product_data['product_qty'] + $product_data['add_new_prduct_quantity'];
            $operator = "+";
        }else if($product_data['cal_operator']==0){
            $operator = "-";
            $tot_qty = $product_data['product_qty'] - $product_data['add_new_prduct_quantity'];
        }



        if ($product_data['price']=="") {
            $error_message['price'] = "price error ";

        }

        if ($product_data['add_new_prduct_quantity']=="") {
            $error_message['add_new_prduct_quantity'] = "new product qty error ";
        }

        if(empty($error_message)){


            $product_details = array(
                'price'=>$product_data['price'],
                'qty'=>$tot_qty

            );

            echo  $is_added_details =  $pro_obj->update_new_product($product_details,$product_id);


            $tz_object = new DateTimeZone('Asia/Colombo');
            $datetime = new DateTime();
            $datetime->setTimezone($tz_object);


            $product_history = array(
                'user_id'=>$_SESSION['user_id'],
                'product_id'=>$product_id,
                'new_qty'=>$product_data['add_new_prduct_quantity'],
                'add_type'=>$operator,
                'added_date'=> $datetime->format('Y\-m\-d\ h:i:s')
            );

            echo  $is_added_history_details =  $pro_obj->add_product_history($product_history);
        }




    }

}

?>