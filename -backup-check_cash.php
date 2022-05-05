<?php

session_start();

if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

?>
<html>
<head>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">
    <style>
        .keypad-popup{
            margin-left:auto;
            margin-right:auto;
            width:1048px;
        }
        .total{
            border:1px solid #848484;
            padding:10px 0;
            text-align:center;
            margin-bottom:20px;
            width:1038px;
            font-weight:bold;
            text-transform:uppercase;
            font-size:21px;
        }
        .total input{
            margin-left:10px;
        }
        .keypad-key{
            background:#fff;
            border:1px solid #000;
            margin-right: -5px;
            width:200px;
            height:104px;
            float:left;
            border-top:none;
            font-size:20px;
            padding:0;
        }
        .keypad-key:active{
            margin-left:0px;
        }
        .text-area{
            width: 255px;
            margin-right: -5px;
            height:56px;
        }
        .keypad-color{
            background: #e84e40;
            border:1px solid #000;
            width: 261px;
            margin-right: -5px;
            color:#fff;
            padding:11px 0;
            font-size:16px;
        }
        .keypad-color-disabled{
            background:grey;
            border:1px solid #000;
            width: 261px;
            margin-right: -5px;
            color:#fff;
            padding:11px 0;
            font-size:16px;
        }
        .bottom-button{
            width:265px;
            background:#316896;
            border:1px solid #000;
            color:#fff;
            font-weight:bold;
            text-transform:uppercase;
        }
        .margin-bottom{
            margin-bottom:20px;
        }
        .top-border{
            border-top:1px solid #000;
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

<!-- by Sasi Spenzer 2016.04.06  10.34 am @ CMS Office -->

<script type="text/javascript">
    $(document).ready(function() {

        $('.keypad-color-disabled').click(function(){
            var type = $(this).val();
            $('#type_saver').val(type);
            $(this).addClass('keypad-color');
            $('.ff button').addClass('keypad-color-disabled');
            $(this).removeClass('keypad-color-disabled');
        });


        $('#clear_total').click(function(){
            $('#total_display').val('');
        });

        $('#clear_by').click(function(){
            var selected_type = $('#type_saver').val();
            var total_in_display = $('#'+selected_type+'').val();
            var edited_total = total_in_display.slice(0,-1);
            $('#'+selected_type+'').val(edited_total);
        });

        $('.keypad-key').click(function(){
            var vale = $(this).val();
            var selected_type = $('#type_saver').val();
            if(selected_type == ''){
                alert('please select a type first !');
            }else{
                var total_in_display = $('#'+selected_type+'').val();
                $('#'+selected_type+'').val(total_in_display +vale);
            }
        });

        // Add by Section Calculations
        function commaSeparateNumber(val){
            while (/(\d+)(\d{3})/.test(val.toString())){
                val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
            }
            return val;
        }
        $('#add_by_section').click(function(){

            var selected_type = $('#type_saver').val();

            var selected_types_previous_value = $(''+'#'+selected_type+'_p_val'+'').val();
            var selected_type_current_value = 	$(''+'#'+selected_type+'').val();
            if(selected_type_current_value == ''){
                alert('please enter an amount before add !')
            }
            else{
                var now_total_by_section = parseInt(selected_types_previous_value) + parseInt(selected_type_current_value) ;



                $(''+'#'+selected_type+'_p_val'+'').val(now_total_by_section);
                var make_super_total = parseInt($('#cash_p_val').val()) + parseInt($('#coins_p_val').val()) + parseInt($('#cc_p_val').val()) + parseInt($('#tc_p_val').val()) + parseInt($('#cd_p_val').val()) - (parseInt($('#pc_p_val').val()) + parseInt($('#bd_p_val').val()) ) ;

                $('#grand_total').val(make_super_total);
                $(''+'#'+selected_type+'').val('');
                $('#total_display').val(commaSeparateNumber(make_super_total));
            }
        });

    });
</script>

<!-- end by Sasi Spenzer -->

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
            $cash_data['end_or_start'] = 1;
            $cash_data['user_id'] = $_SESSION['user_id'];
            $cash_data['feed_time'] = date("Y-m-d");
            $cash_data['cash_or_card'] = 1;

            try {

                $_SESSION['start_cash_array'] = $cash_data ;
                header('Location:check_forign.php');
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
} ?>


    <div class="keypad-popup">
        <div class="total">Total<input style="text-align: center;font-size: large" id="total_display" type="text" value="00.00" /></div>
        <div class="keypad-row ff">
            <button value="cash" type="button" class="keypad-color-disabled">Cash</button>
            <button value="coins" type="button" class="keypad-color-disabled">Coins</button>
            <button value="cc" type="button" class="keypad-color-disabled">CC</button>
            <button value="tc" type="button" class="keypad-color-disabled">TC</button>
        </div>
        <div class="keypad-row margin-bottom">
            <input id="cash" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/>
            <input id="coins" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
            <input id="cc" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
            <input id="tc" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
        </div>
        <div class="keypad-row">
            <button id="key_pad_btn" value="1" type="button" class="keypad-key top-border">1</button>
            <button id="key_pad_btn" value="2" type="button" class="keypad-key top-border">2</button>
            <button id="key_pad_btn" value="3" type="button" class="keypad-key top-border">3</button>
            <button id="key_pad_btn" value="4" type="button" class="keypad-key float-key top-border">4</button>
            <div  class="keypad-button ff"><button value="pc" type="button" class="keypad-color-disabled">Petty cash</button><br />
                <input id="pc" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>
        </div>
        <div class="keypad-row">
            <button id="key_pad_btn" value="5" type="button" class="keypad-key">5</button>
            <button id="key_pad_btn" value="6" type="button" class="keypad-key">6</button>
            <button id="key_pad_btn" value="7" type="button" class="keypad-key">7</button>
            <button id="key_pad_btn" value="8" type="button" class="keypad-key">8</button>
            <div class="keypad-button ff"><button value="cd" type="button" class="keypad-color-disabled">Cash deposit</button><br />
                <input id="cd" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>
        </div>
        <div class="keypad-row">
            <button id="key_pad_btn" value="9" type="button" class="keypad-key">9</button>
            <button id="key_pad_btn" value="0" type="button" class="keypad-key">0</button>
            <button id="key_pad_btn" value="." type="button" class="keypad-key">.</button>
            <button id="key_pad_btn" value="00" type="button" class="keypad-key">00</button>
            <div class="keypad-button ff"><button value="bd" type="button" class="keypad-color-disabled">Bank Deposit</button><br />
                <input id="bd" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>
        </div>
        <div class="keypad-row">
            <button id="add_by_section" type="button" class="keypad-key bottom-button">Add</button>
            <button id="clear_by" type="button" class="keypad-key bottom-button">Clear</button>
            <button id="clear_total" type="button" class="keypad-key bottom-button">Clear Total</button>
            <!--<div class="keypad-button"><button type="button" class="keypad-color">Text</button><br />-->
            <!--<input style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>-->
        </div>
    </div>
    <input value="" id="type_saver" type="hidden"/>
    <input value="0" id="cash_p_val"  type="hidden"/>
    <input value="0" id="coins_p_val" type="hidden"/>
    <input value="0" id="cc_p_val" type="hidden"/>
    <input value="0" id="tc_p_val" type="hidden"/>
    <input value="0" id="pc_p_val" type="hidden"/>
    <input value="0" id="bd_p_val" type="hidden"/>
    <input value="0" id="cd_p_val" type="hidden"/>
    <input value="0" id="grand_total" type="hidden"/>


<script src="extra_css/asset/js/bootstrap.min.js"></script>


<script src="vendor/js/respond.min.js" type="text/javascript"></script>

<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="vendor/js/jquery.2.1.0.min.js"><\/script>')</script>
<script src="vendor/js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</html>


