<?php  
session_start();
ob_start();
error_reporting(0);


function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js" type="text/javascript"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" type="text/javascript"></script>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">

<script type="text/javascript" src="js/modal_settings.js"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $("#comparesalesbtn").click(function(){



            var salesyear1 = $("#salesyear1").val();
            var salesyear2 = $("#salesyear2").val();
            $.ajax({
                url: "testajax.php",
                type: "POST",
                cache: false,
                dataType : 'json',
                async:false,
                data: {get_sales_compare:true,salesyear1:salesyear1,salesyear2:salesyear2},
                success: function(data){
                    if(data['one'] != ''){
                        $('#monthsalesval1').html(data['one']);
                    }
                    else{
                        $('#monthsalesval1').html('00.00');
                    }

                    if(data['two'] != ''){
                        $('#monthsalesval2').html(data['two']);
                    }
                    else{
                        $('#monthsalesval2').html('00.00');
                    }

                }
            });



        });

        $("#comparesalesbtn2").click(function(){

            var salesmonth1 = $('#salesmonth1-2').val();
            var salesmonth2 = $("#salesmonth2-2").val();

            var salesyear1 = $("#salesyear1-2").val();
            var salesyear2 = $("#salesyear2-2").val();


            $.ajax({
                url: "testajax.php",
                type: "POST",
                cache: false,
                dataType : 'json',
                async:false,
                data: {get_sales_compare2:true,salesmonth1:salesmonth1,salesmonth2:salesmonth2,salesyear1:salesyear1,salesyear2:salesyear2},
                success: function(data){
                    if(data['one'] != ''){
                        $('#monthsalesval1-2').html(data['one']);
                    }
                    else{
                        $('#monthsalesval1-2').html('00.00');
                    }

                    if(data['two'] != ''){
                        $('#monthsalesval2-2').html(data['two']);
                    }
                    else{
                        $('#monthsalesval2-2').html('00.00');
                    }


                }
            });





        });



    });


</script>
<style>
    .modal-backdrop{
        z-index:-999!important;
    }
    .modal-body{
        text-align:center;
        color:#3276b1;
    }
    .modal-body h3{
        line-height:31px;
    }
</style>
 <style>
 body{
	 background:#e5e5e5;
 }
 .well{
	 background:grey;
	 color:#fff;
	 padding:50px !important;
	  position: fixed;
	  top: 50%;
	  left: 50%;
	  /* bring your own prefixes */
	  transform: translate(-50%, -50%);
	  margin-top:0 !important;
 }
 .well.login-box legend{
	 color:#fff;
 }
 .login-box .form-group input{
     width: 100%;
 }
 .form-group{
	 margin-bottom:24px;
 }
 .btn-group{
	 position:fixed;
	 top:50%;
    background: #316896;
    text-transform: uppercase;
	padding:10px;
	font-size:12px;
	margin-top:346px;
	display:inline;
 }
 .btn-group a{
	 text-decoration:none;
	 color:#fff !important;
 }
 .dropdown-menu a{
	 color:#262626 !important;
 }
 .button-inline{
	 width:640px;
	 margin:0 auto;
 }
 .btn-login-submit{
	 padding-left:40px;
	 padding-right:40px;
 }
 .btn-cancel-action{
	 padding-left:40px;
	 padding-right:40px;
 }
 .modal-backdrop{
     z-index:-999!important;
 }
 </style>
 
</head>
<body>
    <?php


    $user_obj = new User();
    if(isset($_POST['backup_data'])){

        $location = $_POST['backup_location'];
        if(empty($location)){
            $location = "C:/Users/Win8.1-L/Desktop/zip_tea.zip";
        }

        $r = $user_obj->update_location_backup($location);

        $_SESSION['back_location'] = $location ;
        header("Location:zip-test.php");

    }

    $user_obj = new User();
    $user_location_data = $user_obj->get_backup_location();
    $user_location = $user_location_data['data'] ;


    // get top sales of the shop
    /*$order_obj = new Order();
    $get_top_5 = $order_obj->get_top_five();
    $product_obj = new Product();
    // get details for each product
    $first_product_details = $product_obj->list_priduct_by_product_id($get_top_5['0']['product_id']);
    $secound_product_details = $product_obj->list_priduct_by_product_id($get_top_5['1']['product_id']);
    $third_product_details = $product_obj->list_priduct_by_product_id($get_top_5['2']['product_id']);
    $fourth_product_details = $product_obj->list_priduct_by_product_id($get_top_5['3']['product_id']);
    $fifth_product_details = $product_obj->list_priduct_by_product_id($get_top_5['4']['product_id']);

    // get each product name and sale count
    $first_product_name = $first_product_details['product_name'];
    $first_product_sales = $get_top_5['0']['magnitude'];

    $secound_product_name = $secound_product_details['product_name'];
    $secound_product_sales = $get_top_5['1']['magnitude'];

    $third_product_name = $third_product_details['product_name'];
    $third_product_sales = $get_top_5['2']['magnitude'];

    $fourth_product_name = $fourth_product_details['product_name'];
    $fourth_product_sales = $get_top_5['3']['magnitude'];

    $fourth_product_name = $fourth_product_details['product_name'];
    $fourth_product_sales = $get_top_5['3']['magnitude'];

    $fifth_product_name = $fourth_product_details['product_name'];
    $fifth_product_sales = $get_top_5['4']['magnitude'];

    $user_obj = new User();
    //Prevent the user visiting the logged in page if he/she is already logged in
    /*if (isUserLoggedIn()) {
        header("Location: index.php");
        die();
    }
    
    // sales details getting from here

   $date_today = date("Y-m-d");
    $last_day_1 = date('Y-m-d',strtotime("-1 days"));
    $last_day_2 = date('Y-m-d',strtotime("-2 days"));
    $last_day_3 = date('Y-m-d',strtotime("-3 days"));
    $last_day_4 = date('Y-m-d',strtotime("-4 days"));
    $last_day_5 = date('Y-m-d',strtotime("-5 days"));
    $last_day_6 = date('Y-m-d',strtotime("-6 days"));
    $date_today_sale = date("Y-m-d");
    $sales_history = array();
    $va = 0;
    for($k=0;$k < 30;$k++) {
        $date_today_sale = date('Y-m-d',strtotime("-$k days"));
        $sales_history[$k]['sale_amount'] = $order_obj->get_daily_sales($date_today_sale);
        $va +=  $sales_history[$k]['sale_amount']['Total'];


        $sales_history[$k]['date'] = $date_today_sale;



    }

  /*  $daily_sales_6 = $order_obj->get_daily_sales($last_day_6);
    $total_of_the_day_6 = $daily_sales_6['Total'];



    $daily_sales_5 = $order_obj->get_daily_sales($last_day_5);
    $total_of_the_day_5 = $daily_sales_5['Total'];


    $daily_sales_4 = $order_obj->get_daily_sales($last_day_4);
    $total_of_the_day_4 = $daily_sales_4['Total'];

    $daily_sales_3 = $order_obj->get_daily_sales($last_day_3);
    $total_of_the_day_3 = $daily_sales_3['Total'];

    $daily_sales_2 = $order_obj->get_daily_sales($last_day_2);
    $total_of_the_day_2 = $daily_sales_2['Total'];

    $daily_sales_1 = $order_obj->get_daily_sales($last_day_1);
    $total_of_the_day_1 = $daily_sales_1['Total'];

    $daily_sales = $order_obj->get_daily_sales($date_today);
    $total_of_the_day =  $daily_sales['Total'];

*/


    //Forms posted
    if(isset($_POST['login_user'])){
        if (!empty($_POST)) {
            
            $login_data = array();
            $login_data['user_name'] = isset($_POST ['username']) ? trim($_POST ['username']) : '';
            $login_data['password'] = isset($_POST ['password']) ? trim($_POST ['password']) : '';

            try {

                if($login_data['user_name']=='')throw new Exception("Please Enter a Valid User name");
                if($login_data['password'] =='')throw new Exception("Please Enter a Valid password");        
        
                $is_user = $user_obj->login_user($login_data);
              
              if(!empty($is_user)){

                    $is_logged = $user_obj->uppdate_last_login($is_user['id']);
                    $user_details_by_id = $user_obj->getUserByID($is_user['id']);
                    $user_login_history = array();
                    $user_login_history['user_id'] = $user_details_by_id['id'];
                    $user_login_history['user_login_time'] = date("Y-m-d h:i:s");
                    $user_login_history['login_type'] = "LOG";
                    $user_obj->insert_user_log($user_login_history);
                    
                    $_SESSION['user_id'] = $user_details_by_id['id'];
                    $_SESSION['is_super_admin'] = $user_details_by_id['is_super_admin'];
                    $_SESSION['user_level'] = $user_details_by_id['is_admin'];
                    $_SESSION['logged_time'] = $user_details_by_id['last_login'];
                    $_SESSION['username'] = $user_details_by_id['username'];
                    $_SESSION['user_level'] = $user_details_by_id['is_admin'];
                    $_SESSION['user_type_level'] = $user_details_by_id['is_admin'];

                    $cash_obj = new CashFeed();
                    $get_last_url = $cash_obj->get_last_page();
                    $last_url = $get_last_url['url'];
                    $date = date("Y-m-d");
                    $type = '1';

//                    $check_results = $cash_obj->check_cash_already($date,$type);
//                    if($check_results == true){

                    //}
//                    else{
                        $_SESSION['defaultPrinter'] =  $defaultPrinter['defaultPrinter'];
                        header('Location: check_cash.php');
                        exit;
//                    }


              }else{
                  
                 $error_message = "User name or Password are wrong ";
                 //header("Location: login.php");
                  
              }
          
              //exit;
                
                
            } catch (Exception $exc) {
                $error_message = $exc->getMessage();
            }

        }
    }
    ?>

    <div class="container">
        <div class="row">

            <div class="col-md-12">
          <div class="well login-box">
                    
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <legend>Login</legend>
                        
                        
                            <?php  if (isset($error_message)) { ?>
                               <p class="text-danger">  <?php   echo $error_message; ?> </p>
                            <?php  }  ?>  
                       
                        
                        <div class="form-group">
                            <label for="username">User Name</label>
                            <input value='' id="username" name="username"  type="text" class="form-control" />
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input id="password" value='' name="password"   type="password" class="form-control" />
                        </div>
                        <div class="text-center">
                            <input type="submit" name="login_user" class="btn btn-success btn-login-submit" value="Login" />
                            <button class="btn btn-danger btn-cancel-action">Cancel</button>
                        </div>



        </div>




          </div>

          <div style="overflow-y: auto;" class="col-md-12">
            <ul class="button-inline">
              <li class="btn-group dropup" style="margin-left:-68px;padding-right: 20px;">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:black">Quick Sales Summary : <?php echo   number_format($va,2); ?> </a>

        <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >
<?php
            $sales_history = array_reverse($sales_history);
            foreach($sales_history as $each_day_sale) { ?>
            <li>
                <a href="#" id="" data-toggle="modal" data-target="#cash_reports"><?php echo $each_day_sale['date']; ?>: <?php echo $each_day_sale['sale_amount']['Total']; ?></a>
            </li>
            <?php

            } ?>
        </ul>
    </li>
      <li class="btn-group dropup" style="margin-left:160px;">
        <a id="backup_files" href="#">Backup System Files : </a>
        <ul class="dropdown-menu" >
        <div class="loader">
            <h2>Backup System Files</h2>
	<div class="loader-inner">
    <img src="img/preloader.gif">
		</div>
	</div>
        </ul>
       
    </li>
             <li class="btn-group dropup" style="margin-left:320px;">
        <a data-toggle="modal" data-target="#mymodel" href="db-backup-test.php" >Backup Stystem Database :  </a>
        <ul class="dropdown-menu">
        <div class="loader">
            <h2>Backup System Database</h2>
        
        </ul>


    </li>
    <li class="btn-group dropup" style="margin-left:517px;">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:black">Top 5 Products : </a>
        <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >
<?php
            $sales_history = array_reverse($sales_history);
            foreach($sales_history as $each_day_sale) { ?>
            <li>
                <a href="#" id="" data-toggle="modal" data-target="#cash_reports"><?php echo $each_day_sale['date']; ?>: <?php echo $each_day_sale['sale_amount']['Total']; ?></a>
            </li>
            <?php

            } ?>
        </ul>
    </li>
    </ul>
            </div>
          
        </div>
    </div>

    <div class="modal fade" id="model_location_getter" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <div class="form-group">
                        <label>System Backup</label>

                        <br/><br/>
                        <label>System Backup Location</label>
                        <input type="text" name="backup_location" id="backup_location" value="<?php if(!empty($user_location)) { echo $user_location ; } ?>" class="form-control">
                        <br/>



                            <a href=""> <button data-toggle="modal" data-target="#mymodel" name="backup_data" type="submit" class="btn btn-primary" id="month_start_cash">Backup Files</button></a>
                            <br><br>
                        </form>

                    </div>

                </div>
                <div class="modal-footer">
                    <!--                <button type="button" class="btn btn-primary" id="cash_feed_save_end">Add value</button>-->
                </div>
            </div>
        </div>
    </div>
    <button style="vertical-align: bottom;margin-top: 700px;margin-left: 900px;" type="button" class="btn btn-primary" data-toggle="modal" data-target=".bs-example-modal-lg">Compare Sales</button>

    <div id="mymodel" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <h3>please wait while system is processing <br>your request....</h3>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>





    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Compare Sales By Year</h4>
                </div>
                <div class="modal-body">

                    <div class="col-sm-12">

                        <!--Start Form-->

                        <div class="row">
                            <div id="message"></div>
                            <div></div>

                            <form method="post" action="">
                                <div col-lg-6 class="form-group">


                                    <label for="">Year</label>
                                    <select  id="salesyear1" name="salesyear1" class="form-control">
                                        <option value="2014">2014</option>
                                        <option value="2015">2015</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                    </select>


                                </div>

                                <div align="center">
                                    <h1 id="monthsalesval1">00.00 LKR</h1>
                                </div>

                                <div col-lg-6 class="form-group">


                                    <label for="product_description">Year</label>
                                    <select  id="salesyear2" name="salesyear2"  class="form-control">
                                        <option value="2014">2014</option>
                                        <option value="2015">2015</option>
                                        <option value="2016">2016</option>
                                        <option value="2017">2017</option>
                                        <option value="2018">2018</option>
                                        <option value="2019">2019</option>
                                        <option value="2020">2020</option>
                                    </select>


                                </div>

                                <div align="center">
                                    <h1 id="monthsalesval2">00.00 LKR</h1>
                                </div>

                                <div class="form-group text-center">


                                    <input  id="comparesalesbtn"
                                            name="comparesalesbtn" type="button" class="product_create btn btn-success" value="Compare" />
                                </div>

                                <div style="clear: both"></div>
                                <hr><br>
                                <h4 class="modal-title" id="myModalLabel">Compare Sales By Year and month</h4>
                                <form method="post" action="">
                                    <div col-lg-6 class="form-group">
                                        <label for="month">Month</label>
                                        <select  id="salesmonth1-2" name="salesmonth1-2" class="form-control">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>

                                        <label for="">Year</label>
                                        <select  id="salesyear1-2" name="salesyear1-2" class="form-control">
                                            <option value="2014">2014</option>
                                            <option value="2015">2015</option>
                                            <option value="2016">2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                        </select>


                                    </div>

                                    <div align="center">
                                        <h1 id="monthsalesval1-2">00.00 LKR</h1>
                                    </div>

                                    <div col-lg-6 class="form-group">
                                        <label for="">Month</label>
                                        <select  id="salesmonth2-2" name="salesmonth2-2" class="form-control">
                                            <option value="01">January</option>
                                            <option value="02">February</option>
                                            <option value="03">March</option>
                                            <option value="04">April</option>
                                            <option value="05">May</option>
                                            <option value="06">June</option>
                                            <option value="07">July</option>
                                            <option value="08">August</option>
                                            <option value="09">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>

                                        <label for="product_description">Year</label>
                                        <select  id="salesyear2-2" name="salesyear2-2"  class="form-control">
                                            <option value="2014">2014</option>
                                            <option value="2015">2015</option>
                                            <option value="2016">2016</option>
                                            <option value="2017">2017</option>
                                            <option value="2018">2018</option>
                                            <option value="2019">2019</option>
                                            <option value="2020">2020</option>
                                        </select>


                                    </div>

                                    <div align="center">
                                        <h1 id="monthsalesval2-2">00.00 LKR</h1>
                                    </div>

                                    <div class="form-group text-center">


                                        <input  id="comparesalesbtn2"
                                                name="comparesalesbtn2" type="button" class="product_create btn btn-success" value="Compare" />
                                    </div>

                                    <div style="clear: both"></div>
                                </form>

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

    <!-- <li class="btn-group dropup">
         <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:black">Quick Sales Summary<?php echo $total_of_the_day ; ?></a>
         <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >

             <li>
                 <a href="#" id="" data-toggle="modal" data-target="#cash_reports"><?php echo $last_day_1 ; ?>:<?php echo $total_of_the_day_1 ; ?></a>
             </li>
             <li>
                 <a href="#" id="" data-toggle="modal" data-target="#sale_reports"><?php echo $last_day_2 ; ?>:<?php echo $total_of_the_day_2 ; ?></a>
             </li>
             <li>
                 <a href="#" id="" data-toggle="modal" data-target="#sale_reports"><?php echo $last_day_3 ; ?>:<?php echo $total_of_the_day_3 ; ?></a>
             </li>
             <li>
                 <a href="#" id="" data-toggle="modal" data-target="#sale_reports"><?php echo $last_day_4 ; ?>:<?php echo $total_of_the_day_4 ; ?></a>
             </li>
             <li>
                 <a href="" id="" ><?php echo $last_day_5 ; ?>:<?php echo $total_of_the_day_5 ; ?></a>
             </li>
             <li>
                 <a hFref="#" id="" ><?php echo $last_day_6 ; ?>:<?php echo $total_of_the_day_6 ; ?></a>
             </li>


         </ul>
     </li> -->

    
    <!--
    <li style="margin-right: 15px"  class="btn-group dropup pull-right">
        <a href="#" class="dropdown-toggle"  data-toggle="dropdown" style="color:black"> Top 5 Selling Products</a>
        <ul class="dropdown-menu" style="padding: 15px;min-width: 250px;" >

            <li>
                <a href="#" id="" data-toggle="modal" data-target="#cash_reports">1st Place = <?php echo $first_product_name ; ?> -><?php echo $first_product_sales ; ?> Sales</a>
            </li>
            <li>
                <a href="#" id="" data-toggle="modal" data-target="#sale_reports">2nd Place = <?php echo $secound_product_name ; ?> -><?php echo $secound_product_sales ; ?> Sales</a>
            </li>
            <li>
                <a href="#" id="" data-toggle="modal" data-target="#sale_reports">3rd Place = <?php echo $third_product_name ; ?> -><?php echo $third_product_sales ; ?> Sales</a>
            </li>
            <li>
                <a href="#" id="" data-toggle="modal" data-target="#sale_reports">4th Place = <?php echo $fourth_product_name ; ?> -><?php echo $fourth_product_sales ; ?> Sales</a>
            </li>
            <li>
                <a href="" id="" >5th Place = <?php echo $fifth_product_name ; ?> -><?php echo $fifth_product_sales ; ?> Sales</a>
            </li>



        </ul>
    </li>
    -->


