<?php
session_start();
ob_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}
function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

$cash_obj = new CashFeed();
$rates_new = $cash_obj->get_rates();
$euro_rate =  $rates_new[0]['rate'];
$yen_rate =   $rates_new[1]['rate'];
$aud_rate =   $rates_new[2]['rate'];
$usd_rate =   $rates_new[3]['rate'];
$gbp_rate =   $rates_new[4]['rate'];



?>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="extra_css/asset/js/bootbox.min.js"></script>
<script type="text/javascript" src="jquery.plugin.js"></script>
<script type="text/javascript" src="jquery.keypad.js"></script>
<link type="text/css" rel="stylesheet" href="css/jquery.keypad.css" />
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
</head>
<script type="text/javascript">
    $(document).ready(function(){

        $("#today_start_5000").keypad();
        $("#today_start_2000").keypad();
        $("#today_start_1000").keypad();
        $("#today_start_500").keypad();
        $("#today_start_100").keypad();
        $("#today_start_50").keypad();
        $("#today_start_20").keypad();
        $("#today_start_10").keypad();
        $("#today_start_coins").keypad();
        $("#today_start_check").keypad();
    });
</script>
<body>
<?php

$user_obj = new User();
//Prevent the user visiting the logged in page if he/she is already logged in
/*if (isUserLoggedIn()) {
    header("Location: index.php");
    die();
}
*/

//Forms posted
if(isset($_POST['continue'])){
    if (!empty($_POST)) {



        $euro_cash = $_POST['cash_euro'] * $_POST['euro_rate'] ;
        $yen_cash = $_POST['cash_yen'] * $_POST['yen_rate'];
        $aud_cash = $_POST['cash_aud'] * $_POST['aud_rate'] ;
        $usd_cash = $_POST['cash_usd'] * $_POST['usd_rate'] ;
        $gbp_cash = $_POST['cash_gbp'] * $_POST['gbp_rate'] ;

        $start_cash_from_befor_array = $_SESSION['end_cash_array'];
        $real_start_cash = $start_cash_from_befor_array['cash_amount'] ;

        $final_start_cash = $real_start_cash + $euro_cash + $yen_cash + $aud_cash + $usd_cash + $gbp_cash ;
        $start_cash_from_befor_array['cash_amount'] = $final_start_cash ;
        $cash_obj = new CashFeed();
        $save_cash = $cash_obj->save_cash_feed($start_cash_from_befor_array);

        if(!empty($_POST['euro_rate'])){
            $id= 1;
            $data = array(
                'rate'=>$_POST['euro_rate']
            );
            $cash_obj->update_rate($data,$id) ;
        }
        if(!empty($_POST['euro_rate'])){
            $id= 2;
            $data = array(
                'rate'=>$_POST['yen_rate']
            );
            $cash_obj->update_rate($data,$id);
        }
        if(!empty($_POST['euro_rate'])){
            $id= 3;
            $data = array(
                'rate'=>$_POST['aud_rate']
            );
            $cash_obj->update_rate($data,$id);
        }
        if(!empty($_POST['euro_rate'])){
            $id= 4;
            $data = array(
                'rate'=>$_POST['usd_rate']
            );
            $cash_obj->update_rate($data,$id);
        }
        if(!empty($_POST['euro_rate'])){
            $id= 5;
            $data = array(
                'rate'=>$_POST['gbp_rate']
            );
            $cash_obj->update_rate($data,$id);
        }
        session_destroy();
        header('Location:login.php');
        exit;

    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="well login-box">

                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                    <legend style="color: white">End Cash Counter (foreign currency)</legend>

                    <?php  if (isset($error_message)) { ?>
                        <p class="text-danger">  <?php   echo $error_message; ?> </p>
                    <?php  }  ?>
                    <table>


                        <tr>
                            <td>
                                <label style="color: white" for="start_cash">Cash in Euro</label>

                            </td>
                            <td>
                                <input value='' id="cash_euro" name="cash_euro"  type="text" class="form-control" />
                            </td>
                            <td>
                                <input value='<?php echo $euro_rate ; ?>' id="euro_rate" name="euro_rate"   type="text" class="form-control" />
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <label style="color: white" for="start_cash">Cash in Yen</label>

                            </td>
                            <td>
                                <input value='' id="yen_cash" name="yen_cash"  type="text" class="form-control" />
                            </td>
                            <td>
                                <input value='<?php echo $yen_rate ; ?>' id="yen_rate" name="yen_rate"  type="text" class="form-control" />
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <label style="color: white" for="start_cash">Cash in AUD</label>

                            </td>
                            <td>
                                <input value='' id="cash_aud" name="cash_aud"  type="text" class="form-control" />
                            </td>
                            <td>
                                <input value='<?php echo $aud_rate ; ?>' id="aud_rate" name="aud_rate"  type="text" class="form-control" />
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <label style="color: white" for="start_cash">Cash in USD</label>

                            </td>
                            <td>
                                <input value='' id="cash_usd" name="cash_usd"  type="text" class="form-control" />
                            </td>
                            <td>
                                <input value='<?php echo $usd_rate ; ?>' id="usd_rate" name="usd_rate"  type="text" class="form-control" />
                            </td>

                        </tr>
                        <tr>
                            <td>
                                <label style="color: white" for="start_cash">Cash in GBP</label>

                            </td>
                            <td>
                                <input value='' id="cash_gbp" name="cash_gbp"  type="text" class="form-control" />
                            </td>
                            <td>
                                <input value='<?php echo $gbp_rate ; ?>' id="gbp_rate" name="gbp_rate"  type="text" class="form-control" />
                            </td>

                        </tr>


                    </table>
                    <div class="form-group text-center">
                        <button class="btn btn-danger btn-cancel-action">Clear</button>
                        <input type="submit" name="continue" class="btn btn-success btn-login-submit" value="Continue" />
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

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