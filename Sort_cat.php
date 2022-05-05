<?php
require_once 'common_header.php';
error_reporting(0);
$pro_obj = new Product();
$cat_obj = new Category();
$customer_obj = new Customer();
$get_customer = $customer_obj->existing_customers();

$list_cat = $cat_obj->list_category();
?>
<link type="text/css" rel="stylesheet" href="css/bootstrap_old.css" />
<link type="text/css" rel="stylesheet" href="css/jquery.keypad.css" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript" src="jquery.plugin.js"></script>
<script type="text/javascript" src="jquery.keypad.js"></script>

<script type="text/javascript" src="js/html2canvas.js"></script>
<script type="text/javascript" src="js/jquery.plugin.html2canvas.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<link rel="stylesheet" href="/resources/demos/style.css">
<style>
    #sortable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
    #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; height: 18px; }
    #sortable li span { position: absolute; margin-left: -1.3em; }

</style>

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
    body {
        font-family: "Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";

    }
	</style>
        <script>

        $(document).ready(function(){
            $('#Create_Product').click(function(){
                 $('#product_add').modal('show');
            });


       $('#received_amount').keyup(function(){
           var received_amount = $('#received_amount').val();
           var cart_total = $('#hid_val_total').val();
            var balance = received_amount - cart_total ;
           $('#balance_amount').attr('value',balance);
       });



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
        <title>Demo</title>



    <body>
        <div style="margin-top: 0px;padding-top: 0px;vertical-align: top;" class="container">
            <div class="row-fluid">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Sort Category
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                     <tr>
                                        <th>Image</th>
                                        <th>Product Name  Name</th>
                                        <th>Product Description</th>
                                        <th>Total Products In this Category </th>
                                    </tr>
                                </table>
                                <div class="alert alert-success" style="display: none" id="msg_suc">
                                            Product Order Saved !!
                                </div>
                                <form id="cat">
                                    <label for="select_cat">Please Select a Category </label>
                                    <select class="form-control" id="select_cat">
                                        <option>Select a Category</option>
                                        <?php
                                        foreach($list_cat as $each_cat){?>
                                            <option value="<?php echo $each_cat['category_id'] ; ?>"><?php echo $each_cat['category_name'] ; ?></option>
                                        <?php }

                                        ?>
                                    </select>

                                </form>
                                <div id="cat_results">

                                </div>
                                <br>
                                <br>
                                <input type="button" class="btn btn bg-primary" value="Save Order" id="save_order">

                            </div>
                            <div style="margin-bottom: 50px; clear: both"></div>
                            <div class="row">
                                <?php
                                if(isset($msg)){?>
                                   <div class="active alert alert-warning"><?php  echo $msg ; ?></div>
                               <?php  }  ?>
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
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse collapse_cus" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="index.php"   id=""  class="pDetails">Go Back</a>
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
        <script type="text/javascript" src="js/sort_cat.js"></script>
        <script>
            $(function() {
                $( "#sortable" ).sortable();
                $( "#sortable" ).disableSelection();
            });
        </script>

    </body>
</html>
