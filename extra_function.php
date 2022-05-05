<?php
include_once("common_header.php");



if(isset($_POST['get_location_backup'])){


    $user_obj = new User();
    $user_location_data = $user_obj->get_backup_location();
    $user_location = $user_location_data['data'] ;
    echo json_encode($user_location);

}



if(isset($_POST['get_bar_code'])){
    $product_obj = new Product();
    $product_id = $_POST['id'];
    $qty = $_POST['qty'] ;

    $check_tea = $product_obj->list_priduct_by_product_id($product_id);

    if($check_tea['is_loose'] == 'T'){
        if($qty =='0.025'){
            $field = 'bar25';
        }
        else if($qty =='0.05'){
            $field = 'bar50';
        }
        else if($qty =='0.08'){
            $field = 'bar80';
        }
        else if($qty =='0.1'){
            $field = 'bar100';
        }
        else if($qty =='0.25'){
            $field = 'bar250';
        }
        else if($qty =='0.5'){
            $field = 'bar500';
        }
        else if($qty =='1'){
            $field = 'bar1000';
        } else {
            $field = 'barcode';
        }
    }
    else{
        $field ='barcode';
    }


    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id_by_bar($product_id,$field);
    // Including all required classes
    require_once('class/barcode/BCGFontFile.php');
    require_once('class/barcode/BCGColor.php');
    require_once('class/barcode/BCGDrawing.php');

// Including the barcode technology
    require_once('class/barcode/BCGcode39.barcode.php');

// Loading Font
    $font = new BCGFontFile('class/font/Arial.ttf', 18);

// Don't forget to sanitize user inputs
    $text =  $product_details[$field];

// The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);

    $drawException = null;
    try {
        $code = new BCGcode39();
        $code->setScale(2); // Resolution
        $code->setThickness(30); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch(Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
    1 - Filename (empty : display on screen)
    2 - Background color */
    $path = 'barcode_images/'.$product_details['product_name'].'.png';
    $drawing = new BCGDrawing($path, $color_white);
    if($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

// Header that says it is an image (remove it if you save the barcode to a file)
    header('Content-Type: image/png');
    header('Content-Disposition: inline; filename="barcode.png"');

// Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);

    // image creation down here
    $array = "";
    $array['image'] = "<img src='$path'>";
    $array['path'] = $path;
    $array['link'] = "<a class='btn btn-primary' href='$path' download>Save</a>";
    echo json_encode($array) ;



}

if(isset($_POST['check_number'])){

    $receipts_number = $_POST['receipts_number'];
    $receipts_number = str_pad($receipts_number,7,"0",STR_PAD_LEFT);
    if(!empty($receipts_number)){

        if (!is_numeric($receipts_number)) {
            echo "Number You Enterd is Invalid !";
        }
        $order_obj = new Order();
        $order_data = $order_obj->get_order_id_by_receipts($receipts_number);
        if(empty($order_data)){
            echo "Number You Enterd is Invalid !";
            exit;
        }
        $order_id = $order_data['order_id'];
        $order_id;
        try{
            $order_data = $order_obj->get_order_summery_data($order_id);
            if(empty($order_data)){
                echo "Number You Enterd is Invalid !";
                exit;
            }
            $order_data_array = array();
            $temp_array = array();

            $order_data_array['date'] = date('Y-m-d');


            foreach($order_data as $each_data){
                $order_data_array['order_total'] = $each_data['order_total'];
                $order_data_array['order_id'] = $each_data['order_id'];
                array_push($temp_array,$each_data['product_id']);

            }

            $order_data_array['oder_products'] = json_encode($temp_array);
            $order_data_array['cancelled_by'] =  $_SESSION['user_id'];


            $order_obj->record_cancellation($order_data_array);
            $order_obj->delete_order_and_details($order_id);
            echo "Sales has been Deleted !";

        }catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }


    }
    else{
        echo "Number You Enterd is Invalid !";
    }


}

if(isset($_POST['update_excel'])){
    $product_obj = new Product();
    $product_inventory = $product_obj->list_priduct_inventory();

    $_SESSION['product_inventory_live'] = $product_inventory;

    header("Location:Test.php?product_inventory_live=true");
}


if(isset($_POST['get_pro_details'])){

    $product_id = $_POST['product_id'];
    $qty = $_POST['qty'];

    if($qty =='0.025'){
        $field = 'bar25';
    }
    else if($qty =='0.05'){
        $field = 'bar50';
    }
    else if($qty =='0.08'){
        $field = 'bar80';
    }
    else if($qty =='0.1'){
        $field = 'bar100';
    }
    else if($qty =='0.25'){
        $field = 'bar250';
    }
    else if($qty =='0.5'){
        $field = 'bar500';
    }
    else if($qty =='1'){
        $field = 'bar1000';
    } else {
        $field = 'barcode';
    }



    $_SESSION['shopping_cart_final'][$product_id];

    $product_session_array = $_SESSION['shopping_cart_final'][$product_id];

    $product_session_array['product_id'];

    $product_obj=  new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);

    $product_name = $product_details['product_name'];
    $product_price = $product_details['sales_price'];
    $product_barcode = $product_details['barcode'];
    $product_type2 =  $product_session_array['type'];
    $barcode = $product_details[$field];

    $product_array = "";
    $product_array['name'] = $product_name ;
    $product_array['price'] = $product_price ;
    $product_array['qty'] = $qty ;
    $product_array['type'] = $product_type2 ;
    $product_array['barcode'] = $barcode ;
    echo json_encode($product_array);

}


if(isset($_POST['label_details_add_table'])){

    $name = $_POST['name'];
    $price = $_POST['price'];
    $barcode = $_POST['barcode'];
    $qty = $_POST['qty'];
    $type = $_POST['type'];

    $lable_temp_array = "";
    $lable_temp_array['name'] = $name;
    $lable_temp_array['qty'] = $qty;
    $lable_temp_array['type'] = $type;
    $lable_temp_array['barcode'] = $barcode;
    $lable_temp_array['price'] = $price;

    $product_obj = new Product();
    $product_obj->add_product_for_barcode_lable($lable_temp_array);




}

if(isset($_POST['add_dates'])){

    $exdate = $_POST['exdate'];
    $mndate = $_POST['mndate'];
    $count = $_POST['count'] ;

    $dates_array = "";
    $dates_array['exdate'] = $exdate;
    $dates_array['mndate'] = $mndate;
    $dates_array['count'] = $count;

    $product_obj = new Product();
    $product_obj->add_dates_lable($dates_array);
}






if(isset($_POST['cat_details'])){
    $category_id = $_POST['cat_id'];
    $product = new Product();
    $product_result = $product->select_product_by_cat_id($category_id);

    $output = '';
    $x = 0;
    foreach($product_result as $value){
        $product_qty = $value['qty'];
        $is_loos = $value['is_loose'];
        $x++;
        $output .= "<div sl_id=\"".$x."\" id=\"".$value['product_id']."\" class=\"swiper-slide blue-slide sl-".$x." \" style=\"width: 600px; height: 600px;\">";

        $output .=  "<div id='image_".$value['product_id']."' class='image_class' style='padding-top:0px; border:2px solid cdcdcd#;'><img style='padding: 1px;-webkit-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    -moz-box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);
    box-shadow: 0px 4px 5px 5px rgba(0,0,0,0.60);'  src=\"uploads/".$value['feature_image']."\">";
        if($_SESSION['user_level'] == 1) {
            $output .=   "<div>";

                $output .=  "<a href='#' id='".$value['product_id']."' style='position: absolute; right: 48px; top: 5px;' class='edit_product' data-toggle='modal' data-target='#added_car'>
                    <span class='glyphicon glyphicon-pencil btn btn-warning'></span>
                </a>
                <a href='#' id='".$value['product_id']."' class='edit_photo' onclick='edit_photo(".$value['product_id'].")' style='position: absolute; right: -41px; top: 5px;'>
                    <span class='glyphicon glyphicon-remove btn btn-danger'></span>
                </a>
                <a href='#' id='".$value['product_id']."'  style='position: absolute; right: 5px; top: 5px;' class='delete_product' onclick='delete_product(".$value['product_id'].")'>
                    <span class='glyphicon glyphicon-remove btn btn-danger'></span>
                </a>
              


            </div>";
          }

        $output .=   "</div>";

        $output .=   "<div style='padding-top: 0px;'>
                                <div  style='display:none; background-color: #938a6a;
                                     width: 606px; height: 80px;padding-top:20px;margin-left:0px;' class='details_div' id='product_detail_".$value['product_id']."'>
                                    <form id = 'product_form_$value[product_id]'  method ='post' action = ''>";


        if($is_loos == 'T'){
            $output .=   "<div class=\"col-md-3\">
                                                <span style='color:blue;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_size\" id=\"product_size\">
                                                                <option id='".$value['pot_price']."' value='tea_pot'>Pot - Rs :".number_format(($value['pot_price']),2)."</option>
                                                                <option value='0.025'>25g - Rs :".(0.025)*($value['price']).".00</option>
                                                                <option value='0.05'>50g - Rs :".(0.05)*($value['price']).".00</option>
                                                                <option value='0.08'>80g - Rs :".(0.08)*($value['price']).".00</option>
                                                                <option selected='selected' value='0.1'>100g - Rs :".(0.1)*($value['price']).".00</option>
                                                                <option value='0.25'>250g- Rs :".(0.25)*($value['price']).".00</option>
                                                                <option value='0.5'>500g- Rs :".(0.5)*($value['price']).".00</option>
                                                                <option value='1'>1Kg- Rs :".$value['price'].".00</option>

                                                           </select>
                                                   </div>
                                        </div>";
            $output .=   " <div class=\"col-md-2\">
                                                <span style='color:blue;font-weight:bold;'>Description</span>
                                                   <div>".$value['product_description']."
                                                   </div>
                                        </div>";
        } else {
            $output .=   "<div class=\"col-md-2\">

                                                            <span style='color:blue;font-weight:bold;'>Price</span>

                                                            <div style='font-size:18px;color:blue;font-weight:bold;'>Rs :".$value['price']." </div>

                                                   </div>";
            $output .=   " <div class=\"col-md-2\">
                                                <span style='color:blue;font-weight:bold;'>Quantity</span>
                                                   <div>
                                                         <select  style='color:red;font-size:25px;width:85px;' name=\"product_qty\" id=\"product_qty\">";

            for($a=1;$a<=$product_qty;$a++){
                $output .=   "<option value=".$a.">".$a."</option>";
                if($a == 5)
                    break;
            }



            $output .=   "</select>
                                                   </div>
                                        </div>";
            $output .=   "<div class=\"col-md-2\">
                                                <span style='color:blue;font-weight:bold;'>Description</span>
                                                   <div>".$value['product_name']."
                                                   </div>
                                        </div>";
        }
        $output .=   "<div class='price col-md-3 pull-right'>

                                                <button style='margin-left:20%; margin-top: 0px;  padding-top: 0px;'
   type='button' value='' title='Add to Cart' class='addtocart-button cart-click pull-right' onclick='add_to_cart($value[product_id])'>Add to&nbsp;&nbsp;<img src='img/2772.png' width='16' height'16'> <span>&nbsp;</span></button>

                                     </div>
                                </div>
                                </div>


                        </form>
                            ";
        $output .=   "</div>";


    }

    $output .=   "</div></div>";

        $output .= "</div>";

    echo $output;
    }

if(isset($_POST['add_to_cart_detail_size'])){
    $product_id = $_POST['product_id'];
    $onsale = $_POST['onsale'];
    $_SESSION['onsale'] = $onsale;

    $product_size = $_POST['product_size'];
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);

    $cashObj = new CashFeed();
    $rateData = $cashObj->get_rates();
    $usdRate = $rateData[3]['rate'];

    if(!isset($_SESSION['shopping_cart']) || $_SESSION['shopping_cart'] == "") {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['price_using_is'] = '';
    $order_items['product_id'] = $product_details['product_id'];
    if($onsale ==1){
        $order_items['product_price'] = $product_details['sales_price'];
        $order_items['price_using_is'] =$product_details['sales_price'];
        $order_items['price_usd'] = number_format(($product_details['sales_price']/$usdRate),2);
        $order_items['onsale'] = 1;

    }else{
        $order_items['product_price'] = $product_details['price'];
        $order_items['price_using_is'] =$product_details['price'];
        $order_items['price_usd'] = number_format(($product_details['price']/$usdRate),2);
        $order_items['onsale'] = 0;

    }

    $order_items['product_qty'] = '';
    $order_items['product_size'] = $product_size;
    array_push($_SESSION['shopping_cart'],$order_items);
    echo json_encode($_SESSION['shopping_cart']);

}
if(isset($_POST['update_amount'])){

    $amount = $_POST['amount'];
    $id = $_POST['product_id'];
    $data = array();
    $data['qty'] = $amount;
    $payment_obj = new Product();
    $payment_obj->update_new_product($data,$id);

}

if (isset($_POST['get_products'])) {
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_inventory();
    $return_array = array();
    foreach ($product_details as $each_product) {
        $make_product_name = $each_product['product_id'] . '-' . $each_product['product_name'];
        array_push($return_array, $make_product_name);
    }
    echo json_encode($return_array);
}


if(isset($_POST['convert_numbers'])){
    $number1 = $_POST['old_v'];
    $number2 = $_POST['val'];
    echo $number1.$number2;
}




if(isset($_POST['tea_pot'])){
    $product_id = $_POST['product_id'];

    $product_size = 1;
    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    if(!isset($_SESSION['shopping_cart']) || $_SESSION['shopping_cart'] == "") {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['product_id'] = $product_details['product_id'];
    $order_items['order_type'] = 'tea_pot';
    $order_items['product_price'] = $product_details['pot_price'];
    $order_items['product_qty'] = '';
    $order_items['product_size'] = 1;
    array_push($_SESSION['shopping_cart'],$order_items);
    echo json_encode($_SESSION['shopping_cart']);

}
if(isset($_POST['add_to_cart_detail_qty'])){
    $product_id = $_POST['product_id'];
    $product_qty = $_POST['product_qty'];
    $onsale = $_POST['onsale'];


    $product_obj = new Product();
    $product_details = $product_obj->list_priduct_by_product_id($product_id);
    if(!isset($_SESSION['shopping_cart']) ||  $_SESSION['shopping_cart'] == '') {
        $_SESSION['shopping_cart'] = array();
    }
    $order_items = array();
    $order_items['price_using_is'] = '';
    $order_items['product_id'] = $product_details['product_id'];
    if($onsale ==1){
        $order_items['product_price'] = $product_details['sales_price'];
        $order_items['price_using_is'] =$product_details['sales_price'];
        $order_items['onsale'] = 1;
    }else{
        $order_items['product_price'] = $product_details['price'];
        $order_items['price_using_is'] =$product_details['price'];
        $order_items['onsale'] = 0;
    }
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
    $order_items['product_price'] = $product_details['sales_price'];
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
if(isset($_POST['product_keyword'])){

    $keyword = $_POST['keyword'];
    $product_obj = new Product();

    $results = $product_obj->search_product($keyword);

    if(empty($results)){

        $output ='';
        $output .="<td valign='top' colspan='5' class='dataTables_empty'>No matching Product found for product name = <b>".$keyword."</b></td>";
        echo $output;
    }
    else{
         
        foreach($results as $key=>$value){

            $output = "";
            $output .= "";
            $output .= "<tr class='ui-selectable row bgcolor'>";
            $output .= "<td align='center' onclick='go_to_product($value[product_id],$value[category_id])'>".$value['product_id']."</td>";
            $output .="<td align='center' onclick='go_to_product($value[product_id],$value[category_id])'>".$value['product_name']."</td>";
            $output .="<td align='center' onclick='go_to_product($value[product_id],$value[category_id])'>".$value['price']."</a></td>";
            $output .="<td align='center' onclick='go_to_product($value[product_id],$value[category_id])'>".$value['category_name']."</a></td>";
            $output .="<td onclick='go_to_product($value[product_id],$value[category_id])' align='center'>";
            $output .=" <img src='uploads/".$value['feature_image']."' width='25' height='25'>";
            $output .=" </td>";
            $output .= "</tr>";
            echo $output;
        }
    }

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

if(isset($_POST['url'])){
    $url = $_POST['site_url'];
    session_start();
    $_SESSION['sales_url'] = $url;
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
        $product_data['sales_price']                  = isset ( $_POST ['sales_price'] ) ? $_POST ['sales_price'] : '';
        $product_data['product_description']                  = isset ( $_POST ['product_des'] ) ? $_POST ['product_des'] : '';
        $product_data['name']                  = isset ( $_POST ['name'] ) ? $_POST ['name'] : '';
        $product_data['product_qty']            = isset ( $_POST ['product_qty'] ) ? $_POST ['product_qty'] : '';
        $product_data['add_new_prduct_quantity']            = isset ( $_POST ['add_new_prduct_quantity'] ) ? $_POST ['add_new_prduct_quantity'] : '';
        $product_data['pot_price']    = isset ( $_POST ['pot_price'] ) ? $_POST ['pot_price'] : '';
        $product_data['cal_operator']            = isset ($_POST ['cal_operator'] ) ? $_POST ['cal_operator'] : '';
        $product_data['barcode'] = isset ($_POST ['barcode'] ) ? $_POST ['barcode'] : '';
        $product_data['category_id'] = isset ($_POST ['product_cat'] ) ? $_POST ['product_cat'] : '';

        $product_data['25'] = isset ($_POST ['var_25'] ) ? $_POST ['var_25'] : '';
        $product_data['50'] = isset ($_POST ['var_50'] ) ? $_POST ['var_50'] : '';
        $product_data['80'] = isset ($_POST ['var_80'] ) ? $_POST ['var_80'] : '';
        $product_data['100'] = isset ($_POST ['var_100'] ) ? $_POST ['var_100'] : '';
        $product_data['250'] = isset ($_POST ['var_250'] ) ? $_POST ['var_250'] : '';
        $product_data['500'] = isset ($_POST ['var_500'] ) ? $_POST ['var_500'] : '';
        $product_data['1000'] = isset ($_POST ['var_1000'] ) ? $_POST ['var_1000'] : '';






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
                'sales_price'=>$product_data['sales_price'],
                'qty'=>$tot_qty,
                'category_id'=>$product_data['category_id'],
                'product_name'=> $product_data['name'],
                'product_description'=>$product_data['product_description'],
                'pot_price'=>$product_data['pot_price'],
                'barcode'=>$product_data['barcode'],
                'bar25'=>$product_data['25'],
                'bar50'=>$product_data['50'],
                'bar80'=>$product_data['80'],
                'bar100'=>$product_data['100'],
                'bar250'=>$product_data['250'],
                'bar500'=>$product_data['500'],
                'bar1000'=>$product_data['1000']
            );

            $is_added_details =  $pro_obj->update_new_product($product_details,$product_id);

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
            echo true;
        }




    }

}

if(isset($_POST['change_to_pos'])){

    $_SESSION['user_level'] = 0;
    $_SESSION['session_cart_total'] = $_POST['cart'];
    echo '1';
   // header("Location:index.php");


}

if(isset($_POST['change_to_cust'])){

    $_SESSION['user_type_level'] = '0';

    header("Location:index.php");

}
if(isset($_POST['change_printer'])){

    $printerdata = $_POST['printerdata'];
    $_SESSION['defaultPrinter']  = $printerdata;
    $printer_obj  = new Printer();
    $printer_obj->updatePrinter($printerdata);

}

if(isset($_POST['change_to_admin'])){

    $_SESSION['user_level'] = 1;
    $_SESSION['user_type_level'] = 1;
    header("Location:index.php");
}


if(isset($_POST['get_cat_id'])){

    $cat_obj = new Category();
    $catergoryes = $cat_obj->list_category();
    $id_array = array();
    foreach($catergoryes as $each_one){
        array_push($id_array,$each_one['category_id']);
    }
    $id_array = json_encode($id_array);
    $cat_obj->UpdateAllCat();
    echo $id_array;

}

if(isset($_POST['get_cat_cust_id'])){
    $cat_obj = new Category();
    $categorys = $cat_obj->selectCustomerCategories();
    $id_array = array();
    foreach($categorys as $each_one){
        array_push($id_array,$each_one['category_id']);
    }
    $id_array = json_encode($id_array);

    echo $id_array;
}


if(isset($_POST['update_grams_db'])){

    $temp_array = json_encode($_POST['temp_array']);
    $updateArray = array();
    $updateArray['grams'] = $temp_array;
    $cat_id = $_POST['cat_id'];
    $cat_obj = new Category();
    $cat_obj->update_cat($updateArray,$cat_id);


}




if(isset($_POST['update_cust_mode'])){
    $cat_id = $_POST['cat_id'];
    $cat_obj = new Category();
    $data = array();
    $data['customer_mode'] = '0';
    $cat_obj->update_cat($data,$cat_id);

}

// open cash register

if(isset($_POST['open_cash_reg'])){
    $printer = new Escpos();
    $printer -> initialize();
    $printer ->pulse();
}


if(isset($_POST['get_sales_compare'])){

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
if(isset($_POST['get_sales_compare_test'])){
    echo 'teytetyteyt';
}
if(isset($_POST['get_sales_compare_year'])){

    die('vvvvvvvvv');
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
// clear carts by time

if(isset($_POST['clear_cart_by_time'])){
    unset($_SESSION['shopping_cart']);
}

if(isset($_POST['Cat_droup_down'])){
    $Cat_id = $_POST['Cat_id'];
    $product_obj_new = new Product();
    $my_results = $product_obj_new->select_product_by_cat_id($Cat_id);

    $output = "";
    $output .= "<ul id='sortable'>";
    foreach($my_results as $each_product){
    $output .="<li id='". $each_product['product_id'] ."' style='font-size:16px;height:75px;' class='ui-state-default'><span class='ui-icon ui-icon-arrowthick-2-n-s'></span><img width='70px' height='70px' src='uploads/". $each_product['feature_image'] ."'>&nbsp;". $each_product['product_name'] ."</li>";
    }

    $output .= "</ul>";

    echo $output;

}

// reports here

if(isset($_POST['today_start_cash'])){

    $date = date('Y/m/d');

    $cash_obj = new CashFeed();
    $get_cash = $cash_obj->report_today_start_cash($date);
    $today_start_cash = $get_cash['Total'];
    echo $today_start_cash ;


}

if(isset($_POST['make_label_sessions'])){

    $product_obj = new Product();
    $data_products = $product_obj->get_product_data();
    $product_dates = $product_obj->get_dates();

    $_SESSION['product_name_label'] = $data_products['name'];
    $_SESSION['product_type_label'] = $data_products['type'];
    $_SESSION['product_exdate_label'] = $product_dates['exdate'];
    $_SESSION['product_barcode_label'] = $data_products['barcode'];
    $_SESSION['product_qty_label'] = $data_products['qty'];


}



if(isset($_POST['sort_product'])){

   $product_id = $_POST['product_id'];
   $order_id = $_POST['order_id'];
   $data = array(
        'Order_id'=>$order_id
   );

   $product_obj = new Product();
   $product_obj->update_order_p($data,$product_id);

}

if(isset($_POST['master_code'])){

    $master_code = new Category();
    $results = $master_code->get_master_code();
    $code = $results;
    echo $code[0]['code'] ;

}

if(isset($_POST['barcode'])){

    if(!empty($_POST['barcode_data'])){

        $barcode = $_POST['barcode_data'];
        $product_obj = new Product();
        $product_data = $product_obj->get_product_by_barcode($barcode);
        if(empty($product_data)){

            $gramage_data = $product_obj->get_product_by_barcode_grames($barcode);

            if(!empty($gramage_data)){

                $data_array = '';




                    if($gramage_data['bar25'] == $barcode){
                            $data_array['grame'] = '0.025' ;
                            $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar50'] == $barcode){
                        $data_array['grame'] = '0.05' ;
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar80'] == $barcode){
                        $data_array['grame'] = '0.08' ;
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar100'] == $barcode){
                        $data_array['grame'] = '0.1' ;
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar250'] == $barcode){
                        $data_array['grame'] = '0.25' ;
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar500'] == $barcode){
                        $data_array['grame'] = '0.5' ;
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else if($gramage_data['bar1000'] == $barcode){
                        $data_array['grame'] = '1';
                        $data_array['pid'] = $gramage_data['product_id'] ;
                    }
                    else {
                        $data_array = '';
                        $data_array['no'] = 'This product is not intended';
                        echo  json_encode($data_array);
                    }
                $data_array['yes']  = 'null';

                $encoded_data_array = json_encode($data_array);
                echo $encoded_data_array ;
            }
            else{
                $data_array = '';
                $data_array['no'] = 'This product is not intended';
                echo  json_encode($data_array);
            }


        }
        else{
            $data_array = '';

            $data_array['yes'] = $product_data['product_id'];
            echo  json_encode($data_array);

        }


    }

}
if(isset($_POST['make_cart_value'])){

    $cart_total = $_POST['total'];
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

   // $_SESSION['session_cart_total'] = 0;
    $_SESSION['session_cart_total'] =   $cart_total;
}

if(isset($_POST['balance_show'])){
    $balance = 0 ;

    if($_SESSION['balance'] == '' || $_SESSION['balance'] == 0 || !isset($_SESSION['balance'])){
        $balance = 0 ;
    }else{
        $balance = $_SESSION['balance'];
    }


    $_SESSION['shopping_cart_final'] = "";
    $_SESSION['shopping_cart'] = "";
    $_SESSION['session_cart_total'] = "";
//    $url = $_SESSION['sales_url'] ;
//    header("Location:".$url."");
    echo $balance ;
}

if(isset($_POST['done_balance'])){
    $_SESSION['shopping_cart_final'] = "";
    $_SESSION['shopping_cart'] = "";
    $_SESSION['session_cart_total'] = "";
    $url = $_SESSION['sales_url'] ;
    echo $url;
}

if(isset($_POST['make_page_cat_session'])){
        $url = $_POST['url'];
        $url_array = array(
            'url'=>$url
        );
        $db_obj = new DB();
        $table = 'last_page';
        $data = $url_array;
        $where = array("id =1");
        $db_obj->update($table, $data, $where);

}

if(isset($_POST['make_this_disappear'])){

    $pending_cart_id = $_POST['pending_cart_id'] ;
    unset($_SESSION['pending_cart'][$pending_cart_id]);

}

if(isset($_POST['make_this_active'])){

    $pending_cart_id = $_POST['pending_cart_id'] ;
    $pending_cart_total = $_POST['pending_cart_total'] ;

    if(empty($_SESSION['shopping_cart'])){


        $_SESSION['shopping_cart'] = $_SESSION['pending_cart'][$pending_cart_id];
        unset($_SESSION['pending_cart'][$pending_cart_id]);
        $_SESSION['session_cart_total'] = $pending_cart_total ;
    }
    else{
        $current_cart = $_SESSION['shopping_cart'] ;
        $current_pending_cart =  $_SESSION['pending_cart'][$pending_cart_id];
        $_SESSION['shopping_cart'] = $current_pending_cart ;
        $_SESSION['pending_cart'][$pending_cart_id] = $current_cart;
        $_SESSION['session_cart_total'] = $pending_cart_total ;
    }

}






?>