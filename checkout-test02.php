<?php

require_once 'common_header.php';

//echo "<pre>";

//print_r($_SESSION);

//exit;

error_reporting(0);

$pro_obj = new Product();

$cat_obj = new Category();

$customer_obj = new Customer();

$get_customer = $customer_obj->existing_customers();



$list_cat = $cat_obj->list_category();

$cart_total = 0;

$cart_items = array();

$cart_temp_array = array();



//asort($_SESSION['shopping_cart']);

if(isset($_POST['Continue'])){

    header("Location:index.php");

}





// label Printing By Spenzer 2015.12.10 @ Hard Times



include 'WebClientPrint.php';

use Neodynamic\SDK\Web\WebClientPrint;

use Neodynamic\SDK\Web\Utils;

use Neodynamic\SDK\Web\DefaultPrinter;

use Neodynamic\SDK\Web\InstalledPrinter;

use Neodynamic\SDK\Web\ClientPrintJob;







// Ends Label Printing











// pending cart process starts by Spenzer 2015.11.04



if(isset($_POST['make_pending'])){



    if(!isset($_SESSION['pending_cart'])){



        $_SESSION['pending_cart'] = array();



    }

    if(isset($_SESSION['pending_cart'])){



        $curunt_shopping_cart = $_SESSION['shopping_cart'];

        array_push($_SESSION['pending_cart'],$curunt_shopping_cart);

        $_SESSION['shopping_cart'] = "";

        $_SESSION['session_cart_total'] ="00.00";

        header("Location:index.php");



    }





}







// pending cart process Ends by Spenzer 2015.11.04







if(isset($_SESSION['shopping_cart'])) {



    if(!empty($_SESSION['shopping_cart'])) {

        foreach($_SESSION['shopping_cart'] as $each_item) {

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



$_SESSION['shopping_cart_final'] = $cart_items;







if(isset($_POST['complete_process'])){



    $proceed_further = 1;

    $discount_amount = $_POST['discount_amount'];



    if($_POST['received_amount'] == "" || $_POST['received_amount'] == 0) {

        $proceed_further = 0;

        $msg_emp = "Please enter received amount";
        header("Location:checkout.php?error=true");
        exit;

    } else if($_POST['balance_amount'] < 0) {

        $proceed_further = 0;

        $msg = "<span style='color:red;'>Received amount less than sale value</span>";

    }

    else if($_POST['discount_amount'] >= $_POST['received_amount'] ) {



        $proceed_further = 0;

        $msg = "<span style='color:red;'>Discount amount less than received amount</span>";

    }

    if($proceed_further == 1) {

        if($_POST['customer_select'] == 'selected'){

               $new_customer_name = $_POST['customer_name'] ;

               $new_customer_number = $_POST['contact_no'] ;

               $new_customer_email = $_POST['email'] ;



               if(empty($new_customer_name) || empty($new_customer_number) || empty($new_customer_email)){

                    $msg = "<span style='color:red;'>Please Fill all Fields !<span>";

                    $proceed_further = 0;

               }

               else{

                   $customer_data = array();



                   $customer_data['customer_name'] = $new_customer_name;

                   $customer_data['contact_no'] = $new_customer_number;

                   $customer_data['email'] = $new_customer_email ;

                   $customer_data['customer_status'] ='1';



                   //sending new customers Data to Db



                   $customer_obj = new Customer();

                   $id = $customer_obj->create_customer($customer_data);



                   $customer_id = trim($id) ;



               }

         }

        else{

            $customer_id = $_POST['customer_select'] ;



        }



        $cart_total_send = $_POST['hid_val_total'];

        $current_user_id = $_POST['hid_user_id'];





        // data array



        $order_data = array();



        $order_data['customer_id'] = $customer_id;

        $order_data['order_date'] = date('Y-m-d H:i:s');

        $order_data['order_total'] = $cart_total_send;

        $order_data['user_id'] = $current_user_id;

        $order_data['discount_amount'] = $discount_amount;



        //sending details to mother order table



        $order_obj = new Order();

        $add_order = $order_obj->add_order($order_data);

        $order_id = $add_order;



        $cart_data = unserialize($_POST['hidden_cart_items']);

        if(!empty($cart_data)) {

            foreach($cart_data as $each_item) {

                $cart_data_array = array();



                $cart_data_array['order_id'] = $order_id ;

                $cart_data_array['product_id'] = $each_item['product_id'];

                if($cart_data_array['order_type'] == 'tea_pot'){

                    $each_item['product_qty'] = $cart_data_array['no_of_products'] *0.005;

                }

                else{

                    $cart_data_array['no_of_products'] = $each_item['product_qty'];

                }
                // sending data to table

                $order_obj = new Order();

                $results = $order_obj->add_order_detail($cart_data_array);

                $detail_of_each_product = $pro_obj->list_priduct_by_product_id($each_item['product_id']);

                    $original_qty = $detail_of_each_product['qty'];
                    $available_qty = $original_qty - $each_item['product_qty'];
                    $detail_array = array();
                    $detail_array['qty'] = $available_qty ;
                    $reduce = $pro_obj->reduce_qty($detail_array,$each_item['product_id']);
            }

        }


        $payments_data = array();

        if($_POST['payment_type'] == 'cash') {

            $payments_data['payment_type'] = 'Cash';

        } else if($_POST['payment_type'] == 'credit') {

            $payments_data['payment_type'] = 'CreditCard';

        }

        $payments_data['received_amount'] = $_POST['received_amount'];

        $payments_data['balance_amount'] = $_POST['balance_amount'];

        $payments_data['customer_id'] = $customer_id;

        $payments_data['payment_date'] = date('Y-m-d H:i:s');

        $payments_data['order_id'] = $order_id;



        $extra_obj = new Extra();

        $reciept_number = str_pad($extra_obj->get_next_receipt_number(),7,"0",STR_PAD_LEFT);

        $extra_obj->update_receipt_number();

        $payments_data['receipt_number'] = $order_id;

        $order_obj->create_payment($payments_data);

        $printer = new Escpos();



        $printer -> initialize();



        $printer->setJustification(1);

        $printer->setFont(10);

        $printer->setEmphasis(true);

        if($_POST['payment_type'] == 'cash'){

            $printer ->pulse();

        }



        $printer -> text("   THE WITHERED LEAVES TEA AND SPICES COMPANY \n");

        $printer->setEmphasis(false);

        $printer -> text("   OLD DUTCH HOSPITAL\n");

        $printer -> text("   GALLE\n");

        $printer -> text("   +94 912231848\n");

        $printer->setJustification(0);

        $printer->feed(2);

        $printer->setFont(0);

        $logged_user = $_SESSION['username'];

        $printer -> text("   CASHIER: $logged_user ");

        $printer->setJustification(2);

        $printer -> text("          RECEIPT: $reciept_number ");

        $printer -> feed();







        $printer -> text("  ----------------------------------------------\n");



        $printer -> text("   PRODUCT         PRICE        QTY      AMOUNT\n");



        $printer -> text("  ----------------------------------------------");

        $printer -> feed();

        $r_total = 0;

        foreach($cart_items as $each_cart_item) {

            $product_details = $pro_obj->list_priduct_by_product_id($each_cart_item['product_id']);

            $r_product_name = $product_details['product_name'];

            $printer -> text("   $r_product_name ");

            $product_unit_price = number_format($each_cart_item['product_price'],2);





            $r_product_qty = $each_cart_item['product_qty'];



            $unit_price_total = $each_cart_item['product_price']* $r_product_qty;

            $product_unit_price = str_pad($product_unit_price,9," ",STR_PAD_LEFT);

            if($each_cart_item['type'] == 'Kg') {

                $r_product_qty_p = $each_cart_item['product_qty'].$each_cart_item['type'];

            } else {

                $r_product_qty_p = $each_cart_item['product_qty'];

            }

            $r_product_qty_p = str_pad($r_product_qty_p,6," ",STR_PAD_LEFT);

            $r_total += $unit_price_total;

            $unit_price_total = number_format($unit_price_total,2);

           // $r_total += $unit_price_total;

            $unit_price_total = str_pad($unit_price_total,10," ",STR_PAD_LEFT);

            $printer -> feed();

            $printer -> text("              $product_unit_price      $r_product_qty_p   $unit_price_total");

            $printer -> feed();





        }







        $printer -> text("  ----------------------------------------------\n");



        $printer->setEmphasis(true);

        $discount = $_POST['discount_amount'];

        $sub_total = $r_total;

        $total_after_discount = $sub_total-$discount ;

        $sub_total = number_format($sub_total,2);

        $sub_total = str_pad($sub_total,12," ",STR_PAD_LEFT);

        $r_cash = $_POST['received_amount'];

        $r_cash = number_format($r_cash,2);

        $r_cash = str_pad($r_cash,12," ",STR_PAD_LEFT);

        $r_balance = $_POST['balance_amount']+$discount;



        $_SESSION['balance'] = $r_balance ;



        $r_balance = number_format($r_balance,2);



        if(!empty($_POST['discount_amount'])){

            $printer -> text("   SUB TOTAL                       $sub_total\n");

        }

        else{

            $printer -> text("   TOTAL                       $sub_total\n");

        }



        $printer->setEmphasis(false);

        if($_POST['payment_type'] == 'cash') {

            $r_payment_type = 'CASH';

        } else if($_POST['payment_type'] == 'credit') {

            $r_payment_type = 'CREDIT';

        }



        if(!empty($_POST['discount_amount'])){

            $discount = number_format($discount,2);

            $discount = "-".$discount;

            $discount = str_pad($discount,12," ",STR_PAD_LEFT);



            $total_after_discount = number_format($total_after_discount,2);

            $total_after_discount = str_pad($total_after_discount,12," ",STR_PAD_LEFT);

            $printer -> text("   DISCOUNT                        $discount\n");

            $printer -> text("   TOTAL                           $total_after_discount\n");

        }

        $printer -> text("  ----------------------------------------------");

        $printer -> feed();

        $r_balance = str_pad($r_balance,10," ",STR_PAD_LEFT);

        $printer -> text("   $r_payment_type                            $r_cash\n");

        $printer -> text("   BALANCE                           $r_balance\n");

        $printer -> feed();

        date_default_timezone_set('Asia/Kolkata');

        $print_date = date('d-F-Y H:i:s');

        $printer -> text("   DATE :$print_date ");

        $printer -> feed();

        $printer -> text("  ----------------------------------------------\n");

        $printer->setJustification(1);

        $printer -> text("    WWW.WITHEREDLEAVES.COM \n");



        $printer -> cut();



        $printer->setJustification(1);

        $printer->setFont(10);

        $printer->setEmphasis(true);

        $printer -> text("   THE WITHERED LEAVES TEA AND SPICES COMPANY \n");

        $printer->setEmphasis(false);

        $printer -> text("   OLD DUTCH HOSPITAL\n");

        $printer -> text("   GALLE\n");

        $printer -> text("   +94 912231848\n");

        $printer->setJustification(0);

        $printer->feed(2);

        $printer->setFont(0);

        $logged_user = $_SESSION['username'];

        $printer -> text("   CASHIER: $logged_user ");

        $printer->setJustification(2);

        $printer -> text("          RECEIPT: $reciept_number ");

        $printer -> feed();







        $printer -> text("  ----------------------------------------------\n");



        $printer -> text("   PRODUCT         PRICE        QTY      AMOUNT\n");



        $printer -> text("  ----------------------------------------------");

        $printer -> feed();

        $r_total = 0;

        foreach($cart_items as $each_cart_item) {

            $product_details = $pro_obj->list_priduct_by_product_id($each_cart_item['product_id']);

            $r_product_name = $product_details['product_name'];

            $printer -> text("   $r_product_name ");

            $product_unit_price = number_format($each_cart_item['product_price'],2);





            $r_product_qty = $each_cart_item['product_qty'];



            $unit_price_total = $each_cart_item['product_price']* $r_product_qty;

            $product_unit_price = str_pad($product_unit_price,9," ",STR_PAD_LEFT);

            if($each_cart_item['type'] == 'Kg') {

                $r_product_qty_p = $each_cart_item['product_qty'].$each_cart_item['type'];

            } else {

                $r_product_qty_p = $each_cart_item['product_qty'];

            }

            $r_product_qty_p = str_pad($r_product_qty_p,6," ",STR_PAD_LEFT);

            $r_total += $unit_price_total;

            $unit_price_total = number_format($unit_price_total,2);

            // $r_total += $unit_price_total;

            $unit_price_total = str_pad($unit_price_total,10," ",STR_PAD_LEFT);

            $printer -> feed();

            $printer -> text("              $product_unit_price      $r_product_qty_p   $unit_price_total");

            $printer -> feed();





        }



        $printer -> text("  ----------------------------------------------\n");



        $printer->setEmphasis(true);

        $sub_total = $r_total;



        $sub_total = number_format($sub_total,2);

        $sub_total = str_pad($sub_total,12," ",STR_PAD_LEFT);

        $r_cash = $_POST['received_amount'];

        $r_cash = number_format($r_cash,2);

        $r_cash = str_pad($r_cash,12," ",STR_PAD_LEFT);



      

        if(!empty($_POST['discount_amount'])){

            $printer -> text("   SUB TOTAL                       $sub_total\n");

        }

        else{

            $printer -> text("   TOTAL                       $sub_total\n");

        }

        $printer->setEmphasis(false);

        if($_POST['payment_type'] == 'cash') {

            $r_payment_type = 'CASH';

        } else if($_POST['payment_type'] == 'credit') {

            $r_payment_type = 'CREDIT';

        }





        if(!empty($_POST['discount_amount'])){

            $printer -> text("   DISCOUNT                        $discount\n");

            $printer -> text("   TOTAL                           $total_after_discount\n");

        }

        $printer -> text("  ----------------------------------------------");

        $printer -> feed();



        $printer -> text("   $r_payment_type                            $r_cash\n");

        $printer -> text("   BALANCE                           $r_balance\n");

        $printer -> feed();

        date_default_timezone_set('Asia/Kolkata');

        $print_date = date('d-F-Y H:i:s');

        $printer -> text("   DATE :$print_date ");

        $printer -> feed();

        $printer -> text("  ----------------------------------------------\n");

        $printer->setJustification(1);

        $printer -> text("    WWW.WITHEREDLEAVES.COM \n");



        $printer -> cut();





        $msg = "Order Successfully Added !";

        $_SESSION['balance'] = $r_balance ;



    }

    header("Location:index.php?show_balance=true");



}



?>

<link type="text/css" rel="stylesheet" href="css/bootstrap_old.css" />



<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script type="text/javascript" src="jquery.plugin.js"></script>

<script type="text/javascript" src="jquery.keypad.js"></script>

<link type="text/css" rel="stylesheet" href="css/jquery.keypad.css" />

<script type="text/javascript" src="js/html2canvas.js"></script>

<script type="text/javascript" src="js/jquery.plugin.html2canvas.js"></script>




<script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<!-- label Printing API -->

<script src="http://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js">
</script>

<script type="text/javascript">

    $(document).ready(function(){
        $('.keypad-key').click(function(){
             var value = $(this).val();

                $('#received_amount').val($('#received_amount').val() + value);




        });

        $('#done_btn').click(function(){
            var r_amount = $('#received_amount').val();
            var cart_total = $('#hid_val_total').val();
            var discount = $('#discount_amount').val();
            if(discount == ''){
                var discount = 0
            }
            var new_r_amount = parseInt(r_amount) + parseInt(discount);

            var balance = new_r_amount - cart_total ;

            $('#balance_amount').attr('value',balance);


        });

        $('#clear_btn').click(function(){
            $('#received_amount').val('');
        });

        $('#back_btn').click(function(){
            var total = $('#received_amount').val();

            var total = total.slice(0,-1);
            $('#received_amount').empty().val(total);
        });

    });

</script>

<!-- License:  LGPL 2.1 or QZ INDUSTRIES SOURCE CODE LICENSE -->



<style>

    .item h4 {

        width:100px;

        height:100px;

        background-color:red;

    }

    .has-error{



    }



    #success_massage{

        display: none;

        text-align: center;

    }



    .modal.in .modal-dialog {

        z-index: 10000 !important;

    }
    .place-order-lg{
        padding:20px 31px;
    }

    .panel-body{
        border-right:1px solid #ccc;
        padding-right:0;
        padding-top:0;
    }

</style>

        <script>

        function check_val(){
            var amount  = $('#received_amount').val();
            if(amount =='' || amount == 0){
                alert('Please Fill the Received Amount !')
            }

        }
        $(document).ready(function(){



            var barcode="";

            $(document).keydown(function(e) {



                var code = (e.keyCode ? e.keyCode : e.which);



                if(code==13){ // Enter key hit



                    $.ajax({

                        url: 'extra_function.php',

                        data: {'barcode':true,'barcode_data':barcode,"starStatus":false},

                        type: 'post',

                        success: function(data)

                        {

                            if(data =='This product is not intended'){

                                alert('This product is not intended');

                            }

                            else{

                                var product_id = data ;

                                add_to_cart_from_barcode(product_id);

                            }

                        }

                    });

                }

                else {

                    barcode=barcode+String.fromCharCode(code);

                }

            });



            // model datepickers for label printing







            $('#print_lable').click(function(){

               var ex_date = $('#ex_date').val();

               var mn_date = $('#mn_date').val();

               var count = $('#labl_count').val();



                $.ajax({

                    url: "extra_function.php",

                    type: "POST",

                    cache: false,

                    async:true,

                    data: {add_dates:true,exdate:ex_date,mndate:mn_date,count:count},

                    success: function(theResponse) {



                        $('#print_lable_last1').prop('disabled', false);
                        $('#print_lable_last2').prop('disabled', false);
                        $('#print_lable_last3').prop('disabled', false);

                        $('#labl_count').val('1');

                    }
                });
            });

           $('#hidden_text').change(function(){

                   var amount = $('#hidden_text').val();

                   $('#received_amount').val(amount);

               var received_amount = $('#received_amount').val();

               var cart_total = $('#hid_val_total').val();

               var balance = received_amount - cart_total ;

               $('#balance_amount').attr('value',balance);

            });



            $("#test_max").click(function(){

                   var val = $("#test_div").val();

                   alert(val);

            });

            $('#Create_Product').click(function(){
                 $('#product_add').modal('show');

            });



       $('#hidden_text').keyup(function(){

       });
           ;

        $("#cash_feed_save").on('click',function(e) {

            var cash_amount_value = $('#cash_amount').val();
            if(cash_amount_value =='' ) {                      //if it is blank.
                alert("Please enter value!");
            }else{

                $.ajax({
                    url: "extra_function.php",
                    type: "POST",
                    cache: false,
                    async:false,
                    data: {add_cash_feed:true,cash_amount:cash_amount_value},

                    success: function(theResponse){
                        $('#cash_feed_box').modal('toggle');
                        $( "#success_massage" ).show();
                        $( "#success_massage" ).fadeOut(6000 );                            }
                });

                e.preventDefault();

            }
        });

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

                        $('#added_cart').modal('show');



                    }

                });

            }

        // toggle new custtomer form

        $('#select_customer').change(function(){

            if(this.checked){

                $('#customer_selection').hide('slow');



                $('#new_customer_form2').show('slow');

            }else{

                $('#customer_selection').show('slow');

                $('#new_customer_form2').hide('slow');

            }

        });



        $('#payment_type,#payment_type2').change(function(){



            var type = this.value ;

            if(type =='credit'){

                var total_balance = $('#hid_val_total').val();

                $("#received_amount").val(total_balance);

                //$("#received_amount").attr("disabled", "disabled");

            }

            else{

                $("#received_amount").val('');

                $("#received_amount").removeAttr("disabled");

                $( "#hidden_text" ).focus();

                $("#balance_amount").val('00.00');

            }

        });





        // insert customer data

//     function save_order(all_cart_items) {

//         console.log(all_cart_items);

//

//     }

//            $('#complete_process').on('click', function(){

//

//                if($("#select_customer").prop('checked') == true){

//

//                    var customer_name = $('#customer_name').val();

//                    var contact_no = $('#contact_no').val();

//                    var email = $('#email').val();

//

//                    var customer_data_array = {

//                        customer_name: customer_name,

//                        contact_no:contact_no,

//                        email:email,

//                        customer_status:1

//                    }

//

//                    if(customer_data_array['customer_name'] =='' || customer_data_array['contact_no'] =='' || customer_data_array['email'] =='') {                      //if it is blank.

//                        if(customer_data_array['customer_name'] ==''){

//                            $( '#customer_name_div' ).addClass("has-error");

//                        } else if(customer_data_array['contact_no'] ==''){

//                            $( '#customer_name_div' ).removeClass("has-error");

//                            $( '#contact_no_div' ).addClass("has-error");

//                        }else if(customer_data_array['email'] ==''){

//                            $( '#contact_no_div' ).removeClass("has-error");

//                            $( '#email_div' ).addClass("has-error");

//                        }

//                    } else {

//                        $( '#email_div' ).removeClass("has-error");

//

//                        $.ajax({

//                            url: "extra_function.php",

//                            type: "POST",

//                            cache: false,

//                            async:false,

//                            data: {get_customer_data:true,customer_data_array:customer_data_array},

//                            success: function(theResponse){

//

//                                var customer = theResponse ;

//

//

//                                var d  = new Date();

//

//                                var month = d.getMonth()+1;

//                                var day = d.getDate();

//

//                                var output = d.getFullYear() + '/' +

//                                    (month<10 ? '0' : '') + month + '/' +

//                                    (day<10 ? '0' : '') + day;

//                                var date_today = output ;

//                                var order_total = $('#hid_val_total').val();

//                                var user_id = $('#hid_user_id').val();

//

//                                var order_array =  {

//                                    customer_id:customer,

//                                    order_date:date_today,

//                                    order_total:order_total,

//                                    user_id:user_id

//                                }

//

//                                $.ajax({

//                                    url: "extra_function.php",

//                                    type: "POST",

//                                    cache: false,

//                                    async:false,

//                                    data: {add_order_array:true,order_array:order_array},

//                                    success: function(theResponse){

//

//                                        var order_id = theResponse ;

//                                        var cart_items =  $('#hidden_cart_items').val();

//                                        $.ajax({

//                                            url: "extra_function.php",

//                                            type: "POST",

//                                            cache: false,

//                                            async:false,

//                                            data: {cart_items_array:true,cart_items_json:cart_items},

//                                            success: function(event2){

//                                                alert(event2);

//                                            }

//                                        });

//                                    }

//                                });

//

//                                $( '#customer_form' ).each(function(){

//                                    this.reset();

//                                });

//                            }

//                        });

//                        e.preventDefault();

//                    }

//

//                }else{

//                    var cus_id = $('#customer_id').val();

//                    var user_id = $('#hid_user_id').val();

//                    var d  = new Date();

//

//                    var month = d.getMonth()+1;

//                    var day = d.getDate();

//

//                    var output = d.getFullYear() + '/' +

//                        (month<10 ? '0' : '') + month + '/' +

//                        (day<10 ? '0' : '') + day;

//                    var date_today = output ;

//                    var order_total = $('#hid_val_total').val();

//                    var order_array =  {

//                        customer_id:cus_id,

//                        order_date:date_today,

//                        order_total:order_total,

//                        user_id:user_id

//                    }

//                    $.ajax({

//                        url: "extra_function.php",

//                        type: "POST",

//                        cache: false,

//                        async:false,

//                        data: {add_order_array:true,order_array:order_array},

//                        success: function(theResponse){

//

//                        }

//                    });

//                }

//            });







        });



        function get_barcaode_model(p_id,qty){

            $('#barcode_creator').modal('show');

            $('.modal-backdrop').hide();





            $.ajax({

                url: "extra_function.php",

                type: "POST",

                cache: false,

                async:false,

                data: {get_pro_details:true,product_id:p_id,qty:qty},

                success: function(theResponse){



                    var product_array = jQuery.parseJSON(theResponse);



                    $('#product_name_barcode').val(product_array['name']);

                    $('#product_qty_barcode').val(product_array['qty']);

                    var val_qty = $('#product_qty_barcode').val();

                    $('#product_price_barcode').val(product_array['price']*product_array['qty']);

                    $('#product_qty_barcode').append(product_array['type']);

                    $('#product_qty_barcode').val(val_qty +' '+ product_array['type']);



                    $.ajax({

                        url: "extra_function.php",

                        type: "POST",

                        cache: false,

                        async:false,

                        data: {label_details_add_table:true,name:product_array['name'],qty:product_array['qty'],price:product_array['price']*product_array['qty'],type:product_array['type'],barcode:product_array['barcode']},

                        success: function(theResponse){







                        }

                    });





                    $.ajax({

                        url: "extra_function.php",

                        type: "POST",

                        cache: false,

                        async:false,

                        data: {get_bar_code:true,id:p_id,qty:qty},

                        success: function(theResponse){



                            var image_data = jQuery.parseJSON(theResponse);

                            var image_path = image_data['image'];



                            $('#image_here_modal_dr').html(image_path);



                        }

                    });





                }

            });









        }


     function AllSetGoPrintLable(){

         $.ajax({
             url: "extra_function.php",
             type: "POST",
             cache: false,
             async:false,
             data: {make_label_sessions:true},
             success: function(theResponse){

                 $.get("./LabelCore.php", function (labelXml) {
                     var label = dymo.label.framework.openLabelXml(labelXml);
                     // open label
                     // set label text


                     // select printer to print on
                     // for simplicity sake just use the first LabelWriter printer
                     var printers = dymo.label.framework.getPrinters();

                     if (printers.length == 0) throw "No DYMO printers are installed. Install DYMO printers.";
                     var printerName = "";

                     for (var i = 0; i < printers.length; ++i) {
                         var printer = printers[i];

                         if (printer.printerType == "TapePrinter") {

                             printerName = printer.name;
                             break;
                         }
                     }


                     if (printerName == "") throw "No LabelWriter printers found. Install LabelWriter printer";
                     // finally print the label
                     label.print(printerName);

                 }, "text");
             }

         });

     }

     function AllSetGoPrintName(){
         $.ajax({
             url: "extra_function.php",
             type: "POST",
             cache: false,
             async:false,
             data: {make_label_sessions:true},
             success: function(theResponse){

                 $.get("./LabelCoreName.php", function (labelXml) {
                     var label = dymo.label.framework.openLabelXml(labelXml);
                     // open label
                     // set label text


                     // select printer to print on
                     // for simplicity sake just use the first LabelWriter printer
                     var printers = dymo.label.framework.getPrinters();

                     if (printers.length == 0) throw "No DYMO printers are installed. Install DYMO printers.";
                     var printerName = "";

                     for (var i = 0; i < printers.length; ++i) {
                         var printer = printers[i];

                         if (printer.printerType == "TapePrinter") {

                             printerName = printer.name;
                             break;
                         }
                     }


                     if (printerName == "") throw "No LabelWriter printers found. Install LabelWriter printer";
                     // finally print the label
                     label.print(printerName);

                 }, "text");
             }

         });
     }

     function AllSetGoPrintDetails(){
         $.ajax({
             url: "extra_function.php",
             type: "POST",
             cache: false,
             async:false,
             data: {make_label_sessions:true},
             success: function(theResponse){

                 $.get("./LabelCoreDetails.php", function (labelXml) {
                     var label = dymo.label.framework.openLabelXml(labelXml);
                     // open label
                     // set label text


                     // select printer to print on
                     // for simplicity sake just use the first LabelWriter printer
                     var printers = dymo.label.framework.getPrinters();

                     if (printers.length == 0) throw "No DYMO printers are installed. Install DYMO printers.";
                     var printerName = "";

                     for (var i = 0; i < printers.length; ++i) {
                         var printer = printers[i];

                         if (printer.printerType == "TapePrinter") {

                             printerName = printer.name;
                             break;
                         }
                     }


                     if (printerName == "") throw "No LabelWriter printers found. Install LabelWriter printer";
                     // finally print the label
                     label.print(printerName);

                 }, "text");
             }

         });
     }

    function addtoCartSingle(product_id) {

        var cart_product_id = product_id;

        $.ajax({

            url: "extra_functions.php",

            type: "POST",

            cache: false,

            async:false,

            data: {add_to_cart_single:true,product_id:cart_product_id},

            success: function(theResponse){

            }

        });

    }









</script>









    <!doctype html>

<html lang="en">

    <head>

        <meta charset="UTF-8">

        <title>Tea Shop Checkout</title>



    <body>





        <div style="margin-top: 0px;padding-top: 0px;vertical-align: top;" class="container">



            <div class="row-fluid">



                <div class="col-md-12">

                    <div class="panel panel-default">

                        <div class="panel-heading">

                            Shopping cart

                        </div>

                        <div class="panel-body">

                            <div class="table-responsive">

                                <table class="table table-striped table-bordered table-hover">

                                    <?php if(isset($_SESSION['shopping_cart']) AND !empty($_SESSION['shopping_cart'])){ ?><thead>

                                    <tr>

                                        <td align="center" colspan="4">Do you want to make this Cart Pending ?</td>

                                        <td align="center" colspan="2"><form method="post" action=""><input class="btn btn-primary" type="submit" name="make_pending" id="make_pending" value="Make Pending" ></form></td>

                                    </tr>



                                    </thead>

                                    <?php } ?>

                                    <thead>

                                    <tr>

                                        <th>#</th>

                                        <th></th>

                                        <th>Product Name</th>

                                        <th>Barcode</th>

                                        <th>Unit Price</th>

                                        <th>Qty</th>

                                        <th>Subtotal</th>

                                    </tr>

                                    </thead>

                                    <tbody>

<?php

$k = 1;

foreach($cart_items as $each_cart_item) {

   $product_details = $pro_obj->list_priduct_by_product_id($each_cart_item['product_id']);



?>

                                    <tr>

                                        <td><a href="delete_cart_item.php?pid=<?php echo $product_details['product_id']; ?>" /><img src="img/images.png" height="50px" /></a></td>

                                        <td style="width: 100px; height: 100px;>

                                                        <span class=" fileinput-new="" thumbnail"="">

                                        <img width="50" height="50" alt="50%x50%" src="uploads/<?php echo $product_details['feature_image']; ?>" style="height: 100%; width: 100%; display: block;">



                                        <!--  Hidden Fields Begin -->

                                                <input type="hidden" name="hidden_p_name" id="hidden_p_name" value="<?php echo $product_details['product_name']; ?>">

                                                <input type="hidden" name="hidden_p_qty" id="hidden_p_qty" value="<?php echo $each_cart_item['product_qty'];?>">

                                                <input type="hidden" name="hidden_p_price" id="hidden_p_price" value="<?php echo number_format(($each_cart_item['product_qty']*$each_cart_item['product_price']),2);?>">

                                        <!-- Hidden Fields Ends -->

                                        </td>



                                        <td><?php echo $product_details['product_name'];

                                            if($each_cart_item['order_type']== 'tea_pot'){?>

                                                (Tea Pot)

                                           <?php }



                                            ?></td>

                                        <td>

                                            <a id="barcode_get_mod" onClick="get_barcaode_model(<?php echo $each_cart_item['product_id'] ; ?>,<?php echo $each_cart_item['product_qty'] ; ?>)" class="btn-primary btn">Barcode</a>

                                        </td>

                                        <td>

                                           <?php if($each_cart_item['order_type']== 'tea_pot'){ echo $product_details['pot_price']; } else {  echo number_format($each_cart_item['product_price'],2); } ?>



                                        <td><?php echo $each_cart_item['product_qty']; if($each_cart_item['order_type'] != 'tea_pot') { $each_cart_item['type']; } ?></td>

                                        <td>

                                            <?php if($each_cart_item['order_type']== 'tea_pot'){ echo number_format(($each_cart_item['product_qty']*$product_details['pot_price']),2);} else {  echo number_format(($each_cart_item['product_qty']*$each_cart_item['product_price']),2); } ?>

                                           </td>

                                    </tr>

<?php

$k++;

}

?>





                                    </tbody>

                                </table>

                            </div>







                            <div style="margin-bottom: 50px; clear: both"></div>

                            <div class="row">



                                <?php

                                if(isset($msg)){?>

                                   <div class="active alert alert-warning"><?php  echo $msg ; ?></div>



                               <?php  }  ?>
                                <?php
                                if(isset($_GET['error'])){?>

                                <div class="active alert alert-warning"><font color="red">Please Fill The Received Amount</font></div>



                                <?php  }  ?>



                                <div class="col-md-12">

                                    <div class="checkbox">

                                        <label style="font-weight: bold;">

                                            <form id="place_order" action="" method="post">

                                            <input name="select_customer" type="checkbox" value="selected" id="select_customer"/>New customer

                                        </label>



                                    </div>



									<div class="radio">

                                        <label style="font-weight: bold;">



                                        </label>

                                    </div>

									<div class="radio">

                                        <label style="font-weight: bold;">



                                            <input value="<?php echo $cart_total ; ?> " type="hidden" name="hid_val_total" id="hid_val_total">

                                            <input value="<?php echo $_SESSION['user_id'] ; ?> " type="hidden" name="hid_user_id" id="hid_user_id">

                                        </label>

                                    </div>



									 <div style="padding:10px;clear: both;"></div>

                                    <div class="panel-body col-md-6">



                                        <div class="col-md-4 pull-left" id="new_customer_form" style="border-right:1px solid #ccc;" >

                                            <div class="form-group" id="customer_name_div">
                                                <div class="payment-method-col" style="margin-top:10px;padding-bottom:20px;"><strong>Payment Method</strong></div>


                                                <label class="control-label">Received Amount</label>

                                                <input name="received_amount" id="received_amount" class="form-control" <?php if(isset($_POST['received_amount'])) { ?> value="<?php echo $_POST['received_amount'] ?>" <?php } ?> />

                                                <input style=" border-style: solid;

    border-width: 5px;margin-left: 228px;

border-color:white;color:white;

" type="text" id="hidden_text">

                                            </div>

                                            <div class="form-group" id="customer_name_div">

                                                <label class="control-label" style="margin-top:-25px;">Discount Amount</label>

                                                <input name="discount_amount" id="discount_amount" class="form-control" <?php if(isset($_POST['discount_amount'])) { ?> value="<?php echo $_POST['discount_amount'] ?>" <?php } ?> />

                                            </div>

                                            <div class="form-group" id="contact_no_div">

                                                <label>Balance Amount</label>

                                                <input readonly="readonly" name="balance_amount" value="00.00" id="balance_amount" type="text" class="form-control" <?php if(isset($_POST['received_amount'])) { ?> value="<?php echo $_POST['balance_amount'] ?>" <?php } ?>  />

                                            </div>

                                            <input id="complete_process" name="complete_process" class="btn btn-primary place-order-lg" onclick="check_val(); findPrinter()" type="submit" value="Place Order">



                                        </div>
                                        <div class="number-pad-col">

                                            <div class="panel-heading" style="font-size: 16px">

                                                <div class="radio pull-left" style="margin-top:-10px;margin-left:20px;">

                                                    <label style="font-weight: bold; padding-right:50px;margin-top:10px;">

                                                        <input type="radio" name="payment_type" id="payment_type" value="cash" checked>Cash

                                                    </label>

                                                </div>

                                                <div class="radio" style="margin-top:-11px;">

                                                    <label style="font-weight: bold;margin-top:10px;">

                                                        <input type="radio" name="payment_type" id="payment_type2" value="credit">Credit Card

                                                        <input value="<?php echo $cart_total ; ?> " type="hidden" name="hid_val_total" id="hid_val_total">

                                                        <input value="<?php echo $_SESSION['user_id'] ; ?> " type="hidden" name="hid_user_id" id="hid_user_id">
                                                    </label>

                                                </div>

                                            </div>


                                            <div class="col-md-3 keypad-popup" style="top: 50px;left:30px; display: block; width: 296.5px;"><div class="keypad-row"><button value="7" type="button" class="keypad-key">7</button><button value="8" type="button" class="keypad-key">8</button><button value="9" type="button" class="keypad-key">9</button><button id="done_btn" type="button" class="keypad-special keypad-close" title="Close the keypad">Done</button></div><div class="keypad-row"><button value="4" type="button" class="keypad-key">4</button><button value="5" type="button" class="keypad-key">5</button><button value="6" type="button" class="keypad-key">6</button><button id="clear_btn" type="button" class="keypad-special keypad-clear" title="Erase all the text">Clear</button></div><div class="keypad-row"><button value="1" type="button" class="keypad-key">1</button><button value="2" type="button" class="keypad-key">2</button><button value="3" type="button" class="keypad-key">3</button><button id="back_btn" type="button" class="keypad-special keypad-back" title="Erase the previous character">Back</button></div><div class="keypad-row"><button type="button" class="keypad-key">.</button><button value="0" type="button" class="keypad-key">0</button><button value="00" type="button" class="keypad-key">00</button></div></div>

                                        </div>
                                        <div>

                                        </div></div>



                                    <div>



                                    </div>

                                    <div class="panel panel-default">

                                        <div class="panel-heading" style="font-size: 16px">

                                            Customer Details

                                        </div>

                                        <div class="panel-body">


                                                <div class="col-md-6" id="new_customer_form2"  style="display: none">

                                                    <div class="form-group" id="customer_name_div">

                                                        <label class="control-label">Customer name</label>

                                                        <input name="customer_name" id="customer_name" class="form-control" />

                                                    </div>

                                                    <div class="form-group" id="contact_no_div">

                                                        <label>Contact no</label>

                                                        <input name="contact_no" id="contact_no" type="address" class="form-control" />

                                                    </div>

                                                    <div class="form-group" id="email_div">

                                                        <label>Email</label>

                                                        <input name="email" id="email" class="form-control" />

                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <div class="form-group" id="customer_selection">

                                                        <label>Customer</label>

                                                        <select name="customer_select" class="form-control" id="customer_id">

                                                            <?php foreach($get_customer as $customer) { ?>

                                                            <option value="<?php echo $customer['customer_id'] ; ?>"><?php echo $customer['customer_name'] ; ?></option>

<!--                                                            --><?php }

//                                                            foreach($customer_details as $record){

//                                                                echo "<option >".$record['customer_name']."</option>";

//                                                            }

//                                                            ?>

                                                        </select>

                                                    </div>

                                                </div>

                                                <div class="col-md-6">

                                                    <div style="padding:50px;clear: both;"></div>



                                                    <div class="table-responsive" style="float: right">

                                                        <div>

                                                            <div style="clear: both;"></div>

                                                            <table class="table">

                                                                <tbody>

                                                                <tr style="font-weight: bold; font-size: 18px">

                                                                    <td><div>Total: </div></td>

                                                                    <td><div>Rs. <?php echo number_format($cart_total,2); ?></div></td>

                                                                </tr>

                                                                <tr>

                                                                    <td width="320px"><div></div></td>

                                                                    <td><div></div></td>

                                                                </tr>

                                                                </tbody>

                                                            </table>



                                                            <div style="float: right">



                                                                <a href="index.php">Continue Shopping</a>

                                                                <input id="complete_process" name="complete_process"  class="btn btn-primary"  onClick="findPrinter()" type="submit" value="Place Order" />



                                                                <?php

                                                                   $data=serialize($_SESSION['shopping_cart_final']);

                                                                   $encoded=htmlentities($data);

                                                                   ?>

                                                                    <input type="hidden" value="<?php echo $encoded ; ?>" id="hidden_cart_items" name="hidden_cart_items">

                                                                    <input type="hidden" value="1" id="hidden_cart_items" name="test">

                                                               </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>



                                        </div>

                                    </div>

                                </div>

                            </div>









                        </div>

                    </div>

                </div>

            </div>

        </div>





        <nav class="navbar navbar-default" role="navigation">

            <!-- Brand and toggle get grouped for better mobile display -->

            <div class="navbar-header">

                <a id="asd" href="#menu"></a>



<!--                             <a class="navbar-brand" href="index.php">Point of Sales</a>-->

            </div>



            <!-- Collect the nav links, forms, and other content for toggling -->

            <div class="collapse navbar-collapse collapse_cus" id="bs-example-navbar-collapse-1">

                <ul class="nav navbar-nav navbar-right">

                    <li>

                        <a href="clear_cart.php"   id=""  class="pDetails">Complete Sale</a>

                    </li>



                    <li>

                        <a href="#"   id=""  class="pDetails" data-toggle="modal" data-target="#product_add">Create Product</a>

                    </li>

                    <li class="btn-group dropup">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" > <span id="cart_total_display">Cart Total : Rs.<?php echo $cart_total; ?></span> <b class="caret"></b></a>

                        <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;">

                            <li>

                                <a class="" href="checkout.php">Checkout</a>

                            </li>



                        </ul>

                    </li>

                    <li><a class="cash_feed_class" data-toggle="modal" data-target="#cash_feed_box" href="#">

                            Cash Feed

                        </a>

                    </li>

                    <!--reports

                    <li class="btn-group dropup">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> Reports  </a>

                        <ul class="dropdown-menu" >

                            <li>

                                <a href="" id="" >Product History</a>

                            </li>

                            <li>

                                <a href="#" id="" >Product List</a>

                            </li>

                        </ul>

                    </li>

                    <li class="btn-group dropup">

                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"> User :  <b class="caret"></b></a>

                        <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;">

                            <li>

                                <a href="#"   id="" data-toggle="modal" data-target="#add_user">Add User</a>

                            </li>

                            <li>

                                <?php if(isset($_SESSION['user_id'])){    ?>

                                    <a class=""  href="logout.php"> <span class="glyphicon glyphicon-user"></span>  Sign out</a>

                                    <a class=""  href="logout_session.php"> <span class="glyphicon glyphicon-user"></span>  Sign out keep work</a>



                                <?php  } ?>



                                <input class="btn btn-success btn-block" type="button" id="sign-in-google" value="Sign In with Google">

                                <input class="btn btn-success btn-block" type="button" id="sign-in-twitter" value="Sign In with Twitter">

                            </li>

                        </ul>

                    </li>

                </ul>

            </div>

            <!-- /.navbar-collapse -->

        </nav>





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



                                <form enctype="multipart/form-data" data-toggle="validator" id="product-submit-form" method="post" action="">



                                    <div class="form-group">

                                        <label for="product_name">Product Name</label>

                                        <input  id="product_name" name="product_name" type="text" class="form-control" />



                                    </div>



                                    <?php if(isset($error_message['product_name'])){   ?>

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

                                        foreach($list_cat as $cat_val){

                                            echo "<option id='cat_".$cat_val['category_id']."' value='".$cat_val['category_id']."' >"

                                                .$cat_val['category_name']."</option>";

                                        }

                                        echo "</select>";

                                        ?>

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



        <div class="modal fade" id="cash_feed_box" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

                        <h4 class="modal-title" id="myModalLabel">Enter Cash Value</h4>

                    </div>

                    <div class="modal-body">

                        <div class="form-group">

                            <label>Cash amount</label>

                            <input name="cash_amount" id="cash_amount" class="form-control" />

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button type="button" class="btn btn-primary" id="cash_feed_save">Add value</button>

                    </div>

                </div>

            </div>

        </div>



        <div id="barcode_creator" role="dialog" class="modal fade lol-bn-ane-manda">

            <div class="modal-dialog">

                <div class="modal-content">

                    <div class="modal-header">

                        <button type="button" data-dismiss="modal" class="close">&times;</button>

                        <h4 class="modal-title">Generate Barcode</h4>

                    </div>

                    <div class="modal-body">

                        <form>

                            <table class="table">

                                <tr>

                                    <td align="center">

                                        Product Name

                                    </td>

                                    <td align="center">

                                        Product Price / Qty

                                    </td>

                                </tr>

                                <tr>

                                    <td height="75">

                                          <input disabled="disabled" id="product_name_barcode" name="product_name_barcode" type="text" class="form-control">

                                    </td>

                                    <td height="75">

                                        <input disabled="disabled" placeholder="Price" id="product_qty_barcode" name="product_qty_barcode" type="text" class="form-control input-sm" style="

                                            display: inline-block;

                                            width: 100px;

                                        ">

                                        <input disabled="disabled" placeholder="Qty" id="product_price_barcode" name="product_price_barcode" type="text" class="form-control input-sm" style="

                                        display: inline-block;

                                        width: 100px;

                                    ">

                                    </td>

                                </tr>

                                <tr>

                                    <td height="150" align="center" colspan="2">

                                    Product Barcode

                                    </td>



                                </tr>

                                <tr>

                                    <td align="center" valign="middle" id="image_here_modal_dr" colspan="2">



                                    </td>

                                </tr>

                                <tr>

                                    <td height="75" align="center">

                                        Expire Date

                                    </td>

                                    <td height="75" align="center">

                                        Manufactured Date

                                    </td>

                                </tr>

                                <tr>

                                    <td>

                                        <input type="text" class="form-control" value="<?php echo date('Y-m-d', strtotime('+3 years')); ?>" name="ex_date" id="ex_date">

                                    </td>

                                    <td>

                                        <input type="text" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="mn_date" id="mn_date">

                                    </td>



                                </tr>

                                <tr>

                                    <td align="center" colspan="2">

                                        <select class="form-control" name="labl_count" id="labl_count">

                                            <option>1</option>

                                            <option>2</option>

                                            <option>3</option>

                                            <option>4</option>

                                            <option>5</option>

                                            <option>6</option>

                                            <option>7</option>

                                            <option>8</option>

                                            <option>9</option>

                                            <option>10</option>



                                        </select>

                                    </td>

                                </tr>

                                <tr>

                                    <td align="center" colspan="2">

                                        <input class="btn-warning btn" type="button" id="print_lable" value="Confirm Details">

                                    </td>

                                </tr>

                            </table>

                        </form>

                    </div>

                    <div id="installedPrinters" style="visibility:hidden">

                        <br />



                        <select name="installedPrinterName" id="installedPrinterName"></select>

                    </div>

                    <div class="modal-footer">





                        <input class="btn btn-success" disabled="disabled" id="print_lable_last1" type="button" onClick="AllSetGoPrintLable()" value="Print Label" />
                        <input class="btn btn-success" disabled="disabled" id="print_lable_last2" type="button" onClick="AllSetGoPrintName()" value="Print Name only" />
                        <input class="btn btn-success" disabled="disabled" id="print_lable_last3" type="button" onClick="AllSetGoPrintDetails()" value="Print Barcode" />
                        <button type="button" data-dismiss="modal" class="btn btn-default">Close</button>
                        <?php

                        //Specify the ABSOLUTE URL to the php file that will create the ClientPrintJob object

                        //In this case, this same page

                        //echo WebClientPrint::createScript(Utils::getRoot().'/PrintZPLSample/PrintLabel.php')

                        ?>

                    </div>

                </div>

            </div>

        </div>

                    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js">





                    </script>
        <link type="text/css" rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">







    </body>

</html>

