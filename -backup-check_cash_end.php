<?php
session_start();
ob_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}
function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}
?>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">
<style type="text/css">
    .selected_c_type{
        background-color: bisque;!important;


    }
    .selected_cn{
        background-color:lightblue;!important;
    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="extra_css/asset/js/bootbox.min.js"></script>
<script type="text/javascript" src="jquery.plugin.js"></script>
<script type="text/javascript" src="jquery.keypad.js"></script>
<link type="text/css" rel="stylesheet" href="css/jquery.keypad.css" />
<script src="vendor/js/modernizr.min.js"></script>

<link href="vendor/css/normalize.css" media="screen,projection" type="text/css" rel="stylesheet" />
<link href="vendor/css/bootstrap.min.css" media="screen,projection" type="text/css" rel="stylesheet" />
<link href="vendor/css/font-awesome.min.css" media="screen,projection" type="text/css" rel="stylesheet" />
<link href="css/main.css" media="screen,projection" type="text/css" rel="stylesheet" />

<!--[if lte IE 8]>
<link href="css/ie.css" media="screen, projection" type="text/css" rel="stylesheet" />
<![endif]-->

<link href="css/print.css" media="print" type="text/css" rel="stylesheet" />

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
</head>

<body>
<?php

$user_obj = new User();


//Forms posted

if(isset($_POST['done'])){
    if(!empty($_POST['continue']) AND ($_POST['continue'] != '00.00')){
        if (!empty($_POST)) {

            $cash_data = array();

            $cash_data['cash_amount'] = $_POST['continue'];
            $cash_data['end_or_start'] = 0;
            $cash_data['user_id'] = $_SESSION['user_id'];
            $cash_data['feed_time'] = date("Y-m-d");
            $cash_data['cash_or_card'] = 1;

            try {

                $_SESSION['end_cash_array'] = $cash_data ;
                header('Location:check_forign_end.php');
                exit;


            }
            catch (Exception $exc) {
                $error_message = $exc->getMessage();
            }

        }
        else{
            $error = 'Start Cash Did Not Filled Correctly !';
            $msg_out = "<h3 align='center'>'.$error.'</h3>";
            echo $msg_out;
        }
    }
}

?>
<form method="post" id="" >
    <div style="position: relative" class="container">
        <div class="row">

            <div class="col-md-12">
                <div id="wrapper">
                    <div class="dialpad compact">
                        <div  class="total_all">Total : <span ty="0" id="total_numbers">00.00</span></div>
                        <div class="number"></div>
                        <div class="dials">
                            <ol>

                                <li  value="5000" style="" id="c_type" class="digits selected_cn"><p align="center">Cash<input type="text" id="cash" class="form-control"> </p></li>
                                <li  value="5000" style="background-color: green" id="c_type" class="digits"><p align="center">Coins<input id="coins" value="00" type="text" class="form-control"> </p></li>
                                <li  value="5000" style="background-color: green" id="c_type" class="digits"><p align="center">Checks<input value="00" id="checks" type="text" class="form-control"></li>
                                <li  value="5000" style="background-color: green" id="c_type" class="digits"><p align="center">CC<input value="00" type="text" id="cc" class="form-control"></p></li>



                                <li value="1" class="digits"><p align="center"><strong>1</strong></p></li>
                                <li value="2" class="digits"><p align="center"><strong>2</strong></p></li>
                                <li value="3" class="digits"><p align="center"><strong>3</strong> </p></li>
                                <li  value="cash_dip" id="cash_dip" class="digits selected_c_type"><p align="center">Cash Deposits<input value="00" type="text" id="cash_dip_t" class="form-control"> </p></li>

                                <li value="4" class="digits"><p align="center"><strong>4</strong></p></li>
                                <li value="5" class="digits"><p align="center"><strong>5</strong></p></li>
                                <li value="6" class="digits"><p align="center"><strong>6</strong></p></li>
                                <li value="cash_paid"  id="cash_paid" style="background-color: green" class="digits"><p align="center">Cash Paid<input value="00" id="cash_paid_t" type="text" class="form-control"> </p></li>

                                <li value="7" class="digits"><p align="center"><strong>7</strong></p></li>
                                <li value="8" class="digits"><p align="center"><strong>8</strong></p></li>
                                <li value="9" class="digits"><p align="center"><strong>9</strong></p></li>
                                <li value="acc_dip" id="acc_dip" style="background-color: green" class="digits"><p align="center">Acc Diposit <input value="00" id="acc_dip_t" type="text" class="form-control"> </p></li>

                                <li value="0" class="digits"><p align="center"><strong>0</strong></p></li>
                                <li value="." class="digits"><p align="center"><strong>.</strong></p></li>
                                <li value="00" class="digits"><p align="center"><strong>00</strong></p></li> <li value="000" class="digits"><p align="center"><strong>000</strong></p></li>


                                <li id="delete" style="background-color: orangered" class="digits"><p><strong><i class="fa fa-times"></i></strong><sup>Clear last Entry</sup></p></li>
                                <li   id="clear" style="background-color: orangered" class="digits"><p><strong><i class="fa fa-refresh"></i></strong><sup>Clear All </sup></p></li>
                                <li value="report" id="report"  style="background-color: green" class="digits"><p align="center"><strong>Report</strong> </p></li>







                                <li id="add_count"    style="width:50%;background-color:cornflowerblue" class="digits"><p align="center"><strong>Add + </strong> </p></li>
                                <li id="clear_total"    style="width:50%;background-color:cornflowerblue" class="digits"><p align="center"><strong>Clear Total</strong> </p></li>

                            </ol>

                        </div>
                    </div>
                    <input style="width: 100%;margin-top: 30px;margin-bottom: 50px;" class="btn btn-primary" type="submit" name="done" id="done" value="Done">
                    <input type="hidden" name="continue" value="00.00" id="continue">
                </div>

            </div>

        </div>
    </div>
</form>
<script src="extra_css/asset/js/bootstrap.min.js"></script>

<script type="text/javascript">

    var windowHeight = $(window).height();
    var loginBoxHeight = $('.login-box').innerHeight();
    var welcomeTextHeight = $('.welcome-text').innerHeight();
    var loggedIn = $('.logged-in').innerHeight();

    var mathLogin = (windowHeight / 2) - (loginBoxHeight / 2);
    var mathWelcomeText = (windowHeight / 2) - (welcomeTextHeight / 2);
    var mathLoggedIn = (windowHeight / 2) - (loggedIn / 2);
    $('.login-box').css('margin-top', mathLogin);
    $('.welcome-text').css('margin-top', mathWelcomeText);
    $('.logged-in').css('margin-top', mathLoggedIn);


    function destroy_sesstion(){

        $.ajax({
            url: 'bootraping.php',
            type: 'post',
            dataType: 'json',
            data: {
                'destry_user_sesstion':true
            },
            success: function(data){
                if(data){
                    window.location = 'login.php';
                }

            }
        });
    }

</script>
<script src="vendor/js/respond.min.js" type="text/javascript"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="vendor/js/jquery.2.1.0.min.js"><\/script>')</script>
<script src="vendor/js/bootstrap.min.js"></script>
<script src="js/main.js"></script>