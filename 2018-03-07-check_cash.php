<?php
session_start();
ob_start();
if(!isset($_SESSION['user_id'])){
    header('Location: login.php');
}
function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}
error_reporting(0);

$cash_obj = new CashFeed();
$cash_details = $cash_obj->report_cash_range();

$startCash_data = $cash_obj->getStartCashDay();
$endCash_data = $cash_obj->getEndCashDay();
$CashSales = $cash_obj->CasHsalesData();
$CardSales = $cash_obj->CardsalesData();
$BankDiposits = $cash_obj->GetBD();


$Mainarray = array();
    foreach ($startCash_data as $each_startCash_data){

        if (!array_key_exists($each_startCash_data['Date'],$Mainarray)) {
            $Mainarray[$each_startCash_data['Date']];
        }
        $tempArray = array();
        $tempArray['startCash'] = $each_startCash_data['amount'];
        $arrayend = array();
        foreach($endCash_data as $eachendCash_data){
            if($eachendCash_data['amount']){
                $arrayend[$eachendCash_data['Date']] = $eachendCash_data['amount'];
            }else{

            }
            $arrayend[$eachendCash_data['Date']] = $eachendCash_data['amount'];
        }
        if(array_key_exists($each_startCash_data['Date'],$arrayend)){
            $tempArray['endCash'] = $arrayend[$each_startCash_data['Date']];
        }else{
            $tempArray['endCash'] = 0;
        }

        $arrayCashSale = array();
        foreach($CashSales as $eachCashSales){
                $arrayCashSale[$eachCashSales['Date']] = $eachCashSales['Sales'];
        }

        if(array_key_exists($each_startCash_data['Date'],$arrayCashSale)){
            $tempArray['CashSales'] = $arrayCashSale[$each_startCash_data['Date']];
        }else{
            $tempArray['CashSales'] = 0;
        }
        $arrayCardSales = array();
        foreach($CardSales as $eachCardSales){
            $arrayCardSales[$eachCardSales['Date']] = $eachCardSales['Sales'];
        }
        if(array_key_exists($each_startCash_data['Date'],$arrayCardSales)){
            $tempArray['CardSales'] = $arrayCardSales[$each_startCash_data['Date']];
        }else{
            $tempArray['CardSales'] = 0;
        }
        $arrayBD = array();
        foreach($BankDiposits as $eachBD){
            $arrayBD[$eachBD['Date']] = $eachBD['amount'];
        }
        if(array_key_exists($each_startCash_data['Date'],$arrayBD)){
            $tempArray['BD'] = $arrayBD[$each_startCash_data['Date']];
        }else{
            $tempArray['BD'] = 0;
        }
        $tempArray['Date'] = $each_startCash_data['Date'];

        array_push($Mainarray,$tempArray);
    }



$rates_new = $cash_obj->get_rates();
$euro_rate =  $rates_new[0]['rate'];
$yen_rate =   $rates_new[1]['rate'];
$aud_rate =   $rates_new[2]['rate'];
$usd_rate =   $rates_new[3]['rate'];
$gbp_rate =   $rates_new[4]['rate'];

$pcData = $cash_obj->GetPCBackOneMonth();

$pcArray = array();
foreach($pcData as $each_data){

    $pc_coloms_data = json_decode($each_data['pc_amount']);

    if(is_array($pc_coloms_data)){
        $amount = 0;
        if(!empty($pc_coloms_data)){
            foreach($pc_coloms_data as $each_one){
                $each_one = (array) $each_one;

                if(!isset($pcArray[date('Y-m-d', strtotime($each_data['feed_time']))])){
                    $pcArray[date('Y-m-d', strtotime($each_data['feed_time']))] = array();
                }
                $tempArray = array();
                $tempArray['Amount'] = $each_one['amount'];
                $amount += $each_one['amount'];
                $tempArray['Description'] = $each_one['description'];
                array_push($pcArray[date('Y-m-d', strtotime($each_data['feed_time']))],$tempArray);

            }

        }
        else{

        }
    }
    else{

    }
}





?>


<?php

$user_obj = new User();


//Forms posted

if(isset($_POST['done'])){

    if (!empty($_POST['total_display'] ) || $_POST['total_display'] == '00.00' ) {

        if (!empty($_POST)) {

            $cash_data = array();

            $cash_data['cash_amount'] = (int) filter_var($_POST['total_display'], FILTER_SANITIZE_NUMBER_INT); ;
            $cash_data['cc_amount'] = $_POST['cc_p_val'];
            $cash_data['tc_amount'] = $_POST['tc_p_val'];
            $cash_data['bd_amount'] = $_POST['bd_p_val'];

            $cash_data['end_or_start'] = 1;
            $cash_data['user_id'] = $_SESSION['user_id'];
            $cash_data['feed_time'] = date("Y-m-d");
            $cash_data['cash_or_card'] =1;
            $petty_cash_data = $_POST['petty_cash_records'];
            $cash_data['pc_amount'] = $petty_cash_data;


            try {

                $_SESSION['start_cash_array'] = $cash_data ;
                $euro_cash = $_POST['cash_euro'] * $_POST['euro_rate'] ;
                $yen_cash = $_POST['cash_yen'] * $_POST['yen_rate'];
                $aud_cash = $_POST['cash_aud'] * $_POST['aud_rate'] ;
                $usd_cash = $_POST['cash_usd'] * $_POST['usd_rate'] ;
                $gbp_cash = $_POST['cash_gbp'] * $_POST['gbp_rate'] ;

                $start_cash_from_befor_array = $_SESSION['start_cash_array'];

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
                $get_last_url = $cash_obj->get_last_page();
                $last_url = $get_last_url['url'];

                $today =  $date = date('Y-m-d');
                $end_cash = $cash_obj->end_cash_total($today);
                $really_end_cash = $end_cash['Total'];
                $_SESSION['start_cash_session'] = date('Y-m-d');

				if($really_end_cash == $final_start_cash || ($really_end_cash - $final_start_cash) <= 500 ){
                    //header('Location:'.$last_url);
                    if($final_start_cash == '' || $final_start_cash == 0){
                        $error = "Please Do not Start Without Counting the Cash";
                    }else{
                        header("Location:http://localhost/tea_showcase_ccc/index.php?cat_id=42");
                        exit;
                    }

				}
                else{

                    $error = "Last End Cash and Today's Start Cash Did not Match.Highly Recommend you to recount the money !";
                }


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
    else{
        $msg = 'please count the money';
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<script   src="https://code.jquery.com/jquery-2.2.3.js"   integrity="sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4="   crossorigin="anonymous"></script>


<!-- by Sasi Spenzer 2016.04.06  10.34 am @ CMS Office -->

<script type="text/javascript">

    var var_petty_cash_amount = 0;
    var var_petty_cash_entries = new Array();
    $(document).ready(function() {

        $('.keypad-color-disabled').click(function(){
            var type = $(this).val();
            $('#type_saver').val(type);
            $(this).addClass('keypad-color');
            $('.ff button').addClass('keypad-color-disabled');
            $(this).removeClass('keypad-color-disabled');
        });

        $("#open_cash").click(function(){
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {open_cash_reg:true},
                success: function(theResponse){

                }
            });
        });

        $('#open_cash').click(function(){
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                data: {open_cash_reg:true},
                success: function(theResponse){

                }
            });
        });

        $('.keypad-color').click(function(){

            var type = $(this).val();
            if(type == 'pc') {
                $('#pc').val('');
            }
        });


        $('#clear_total').click(function(){
            $('#total_display').val('');
        });

        $('#clear_by').click(function(){
            var selected_type = $('#type_saver').val();
            var total_in_display = $('#'+selected_type+'').val();
            var edited_total = total_in_display.slice(0,-1); // if you wanna delete one by one by spenzer
            $('#'+selected_type+'').val(''); // delete all data in selected field
        });

        $('.keypad-key').click(function(){
            var vale = $(this).val();
            var selected_type = $('#type_saver').val();
            if(selected_type == ''){
                if(vale != 'Open Cash'){
                    //alert('please select a type first !');
                }

            }else{

                var total_in_display = $('#'+selected_type+'').val();
                if(selected_type == 'pc') {

                    $('#petty_cash_entries .current_petty_cash_entry .petty_cash_single_entry_amount').html(total_in_display +vale);
                }

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

            // getting rates * values

            var usd = parseInt($("#cash_usd").val() * $("#usd_rate").val()) ;
            var euro = parseInt($("#cash_euro").val() * $("#euro_rate").val() ) ;
            var yen = parseInt($("#cash_yen").val() * $("#yen_rate").val() ) ;
            var gbp =  parseInt($("#cash_gbp").val() * $("#gbp_rate").val() ) ;
            var aud = parseInt($("#cash_aud").val() * $("#aud_rate").val() ) ;

            var total_foreign_currency  = usd + euro + yen + gbp +aud ;


            var selected_types_previous_value = $(''+'#'+selected_type+'_p_val'+'').val();
            var selected_type_current_value = 	$(''+'#'+selected_type+'').val();
            if(selected_type_current_value == ''){
                alert('please enter an amount before add !')
            }
            else{
                var now_total_by_section = parseInt(selected_types_previous_value) + parseInt(selected_type_current_value) + total_foreign_currency ;
                $(''+'#'+selected_type+'_p_val'+'').val(now_total_by_section);
                var make_super_total = parseInt($('#cash_p_val').val()) + parseInt($('#coins_p_val').val()) + parseInt($('#cc_p_val').val()) + parseInt($('#tc_p_val').val()) + parseInt($('#cd_p_val').val()) - (parseInt($('#pc_p_val').val()) + parseInt($('#bd_p_val').val()) ) ;

                $('#grand_total').val(make_super_total);
                $(''+'#'+selected_type+'').val('');
                $('#total_display').val(commaSeparateNumber(make_super_total));

                var now_usd_val = $('#current_usd').val();
                var now_euro_val = $('#current_euro').val();
                var now_aud_val = $('#current_aud').val();
                var now_gbp_val = $('#current_gbp').val();
                var now_yen_val = $('#current_yen').val();

                var new_usd_val =  parseInt($("#cash_usd").val()) + parseInt($('#current_usd').val());
                var new_euro_val = parseInt($("#cash_euro").val()) + parseInt($('#current_euro').val());
                var new_aud_val = parseInt($("#cash_aud").val()) +  parseInt($('#current_aud').val());
                var new_yen_val = parseInt($("#cash_yen").val()) +  parseInt($('#current_yen').val());
                var new_gbp_val = parseInt($("#cash_gbp").val()) +  parseInt($('#current_gbp').val());



                $('#current_usd').val(new_usd_val);
                $('#current_euro').val(new_euro_val);
                $('#current_aud').val(new_aud_val);
                $('#current_yen').val(new_yen_val);
                $('#current_gbp').val(new_gbp_val);


                $("#cash_usd").val('00');
                $("#cash_euro").val('00');
                $("#cash_yen").val('00');
                $("#cash_gbp").val('00');
                $("#cash_aud").val('00');
                $("#current_bd").val($("#bd_p_val").val());
                $("#current_cd").val($("#cd_p_val").val());



            }
        });


        $('#add_petty_cash').click(function(){
            $("#petty_cash_entries div").removeClass("current_petty_cash_entry");
            $('#petty_cash_entries').append('<div class="petty_cash_single_entry current_petty_cash_entry"><span class="petty_cash_single_entry_amount"></span><input type="text" class="petty_cash_single_entry_text" size="100" /><span data-value="" class="petty_cash_single_entry_button" onclick=remove_petty_cash(this)>Remove</span> </div>');
            $('#pc').val('');
        });
    });
    function remove_petty_cash(remove_item) {
        $(remove_item).parent().remove();
    }
    function set_petty_cash() {
        var_petty_cash_entries = new Array();
        var_petty_cash_amount = 0;

        $('div#petty_cash_entries>div').each(function(j,i){
            var_petty_cash_amount += parseInt($(i).find('.petty_cash_single_entry_amount').html());
            var temp_array = {};
            temp_array['amount'] = parseInt($(i).find('.petty_cash_single_entry_amount').html());
            temp_array['description'] = $(i).find('.petty_cash_single_entry_text').val();
            //var_petty_cash_entries[k] = $.parseJSON(temp_array);
            var_petty_cash_entries.push(temp_array);

        });

        $('#petty_cash_records').val(JSON.stringify(var_petty_cash_entries));
        $('#petty_cash_value').val(var_petty_cash_amount);
        return true;
    }
</script>

<!-- end by Sasi Spenzer -->



<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Tea POS New Millennium </title>
<style>
    body {
        font-family: 'Open Sans', sans-serif!important;
        -ms-touch-action: manipulation !important; /* Zoom in css */
    }
    .total{
        border:1px solid #848484;
        padding:10px 0;
        text-align:center;
        margin-bottom:20px;
        width:70%;
        font-weight:bold;
        text-transform:uppercase;
        font-size:21px;
		margin-left:auto;
		margin-right:auto;
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
    .keypad-key23{
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
        width: 259px;
        margin-right: -5px;
        height:56px;
    }
    .keypad-color{
        background: #61A07A;
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
        width: 263px;
        margin-right: -5px;
        color:#fff;
        padding:11px 0;
        font-size:16px;
    }
    .bottom-button{
        width:212px;
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
    .petty_cash_single_entry_amount {
        font-size: 20px;
        padding: 10px;
        width: 100px !important;

    }
    #table-wrapper {
        position:relative;
    }
    #table-scroll {
        height:150px;
        overflow:auto;
        margin-top:20px;
    }
    #table-wrapper table {
        width:100%;

    }
    #petty_cash_entries {
        margin: 10px 0 10px 70px;

    }
    .petty_cash_single_entry_button {
        background: red none repeat scroll 0 0;
        width: 100px;
        border: none;
        height: 30px;
        margin: 10px 0 10px 10px;
    }
    .login-box{
       margin-left: -2px;
    }
    .login-box table{
        background:#e5e5e5;
        border: 1px solid #848484;
		margin-top:21px;
    }
	.login-box table tr{
		background:#848484;
	    height: 68px;
		display:table;
		margin-left:-3px;
		margin-right:-2px;
		margin-top:-4px;
	}
	.login-box table tr td{
		width:100px;
	}
    .wrapper{
        float:left;
    }
    .login-wrapper{
        float:left;
    }
    .button-blue{
        width:50%;
        border:1px solid #000;
        color:#fff;
        font-weight:bold;
        text-transform:uppercase;
        float:left;
        padding:10px 0;
    }
    
    .form-control {
        display: block;
        padding: 6px 2px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
        -webkit-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
        width:45px;
    }
	.space{
		height: 61px;
		background: #e5e5e5;
		border: 1px solid #848484;
		border-top: none;
	}
	.main-wrapper{
		margin-left:auto;
		margin-right:auto;
		width:1100px;
	}
	.cash-counter{
		color:#fff;
	
	}
	.cash-counter ul{
		padding-left:0;
		background:#848484;
		display:inline-block;
		padding:10px 15px;
		border-right:1px solid #000;
		margin-bottom:0;
	}
	.cash-counter ul li{
		list-style-type:none;
		display:inline-block;
	}


</style>



</head>
<form method="post" onsubmit=" return set_petty_cash()">
    <body>
    <div class="main-wrapper">
                <div class="total">Total<input style="text-align: center;font-size: large" id="total_display" name="total_display" type="text" value="00.00" /></div>
                <?php if(isset($error)){ ?><div class="total"><font color="red"> Error:<?php echo $error ; ?></font></div> <?php  } ?>

    <div class="wrapper">
        <div class="keypad-popup">
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
            <div id="petty_cash_entries">


            </div>
            <div class="cash-counter">
                    <ul>
                        <li>Cash in <br />Euro</li>
                        <li><input value='00' id="cash_euro" name="cash_euro"  type="text" class="form-control" /></li>
                        <li><input value='<?php echo $euro_rate ; ?>' id="euro_rate" name="euro_rate"   type="text" class="form-control" /></li>

                    </ul>
                     <ul style="margin-left:-5px;">
                        <li>Cash in<br /> Yen</li>
                        <li><input value='00' id="cash_yen" name="cash_yen"  type="text" class="form-control" /></li>
                        <li><input value='<?php echo $yen_rate ; ?>' id="yen_rate" name="yen_rate"  type="text" class="form-control" /></li>
                	</ul>
                    <ul style="margin-left:-4px;">
                        <li>Cash in <br />AUD</li>
                        <li><input value='00' id="cash_aud" name="cash_aud"  type="text" class="form-control" /></li>
                        <li><input value='<?php echo $aud_rate ; ?>' id="aud_rate" name="aud_rate"  type="text" class="form-control" /></li>
                	</ul>
                      <ul style="margin-left:-5px;">
                        <li>Cash in<br /> USD</li>
                        <li><input value='00' id="cash_usd" name="cash_usd"  type="text" class="form-control" /></li>
                        <li><input value='<?php echo $usd_rate ; ?>' id="usd_rate" name="usd_rate"  type="text" class="form-control" /></li>
                	</ul>
                     <ul style="margin-left:-4px; padding:10px 46px;">
                        <li>Cash in <br />GBP</li>
                        <li><input value='00' id="cash_gbp" name="cash_gbp"  type="text" class="form-control" /></li>
                        <li><input value='<?php echo $gbp_rate ; ?>' id="gbp_rate" name="gbp_rate"  type="text" class="form-control" /></li>
               		 </ul>
                  
           </div>



            <div class="keypad-row">
                <button id="key_pad_btn" value="1" type="button" class="keypad-key top-border">1</button>
                <button id="key_pad_btn" value="2" type="button" class="keypad-key top-border">2</button>
                <button id="key_pad_btn" value="3" type="button" class="keypad-key top-border">3</button>
                <button id="key_pad_btn" value="." type="button" class="keypad-key float-key top-border">.</button>
                <div id="add_petty_cash"  class="keypad-button"><button value="pc" type="button" class="keypad-color-disabled">Petty cash</button><br />
                    <input id="pc" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>


            </div>
            <div class="keypad-row">
                <button id="key_pad_btn" value="4" type="button" class="keypad-key">4</button>
                <button id="key_pad_btn" value="5" type="button" class="keypad-key">5</button>
                <button id="key_pad_btn" value="6" type="button" class="keypad-key">6</button>
                <button id="key_pad_btn" value="0" type="button" class="keypad-key">0</button>
                <div class="keypad-button ff"><button value="cd" type="button" class="keypad-color-disabled">Cash deposit</button><br />
                    <input id="cd" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>
            </div>
            <div class="keypad-row">
                <button id="key_pad_btn" value="7" type="button" class="keypad-key">7</button>
                <button id="key_pad_btn" value="8" type="button" class="keypad-key">8</button>
                <button id="key_pad_btn" value="9" type="button" class="keypad-key">9</button>
                <button id="key_pad_btn" value="00" type="button" class="keypad-key">00</button>
                <div class="keypad-button ff"><button value="bd" type="button" class="keypad-color-disabled">Bank Deposit</button><br />
                    <input id="bd" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>
            </div>
            <div class="keypad-row">
                <button id="add_by_section" type="button" class="keypad-key bottom-button">Add</button>
                <button id="clear_by" type="button" class="keypad-key bottom-button">Clear</button>
                <button id="clear_total" type="button" class="keypad-key bottom-button">Clear Total</button>
                <button   <?php if($_SESSION['is_super_admin'] != 1){ ?> disabled="disabled" <?php } ?> value="Open_Cash" id="open_cash" type="button" class="keypad-key bottom-button">Open Cash</button>
                <!--<div class="keypad-button"><button type="button" class="keypad-color">Text</button><br />-->
                <!--<input style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>-->
            </div>
        </div>

        <input value="" id="type_saver" name="type_saver" type="hidden"/>


        <input value="0" id="cash_p_val" name="cash_p_val"  type="hidden"/>
        <input value="0" id="coins_p_val" name="coins_p_val" type="hidden"/>
        <input value="0" id="cc_p_val" name="cc_p_val" type="hidden"/>
        <input value="0" id="tc_p_val" name="tc_p_val" type="hidden"/>
        <input value="0" id="pc_p_val" name="pc_p_val" type="hidden"/>
        <input value="0" id="bd_p_val" name="bd_p_val" type="hidden"/>
        <input value="0" id="cd_p_val" name="cd_p_val" type="hidden"/>

        <input value="" id="petty_cash_records" name="petty_cash_records" type="hidden"/>
        <input value="0" id="grand_total" name="grand_total" type="hidden"/>
        <input value="" id="petty_cash_value" name="petty_cash_value" type="hidden"/>

        <input type="submit" value="Finish" name="done" class="keypad-key23 bottom-button"/>

        <?php if(isset($msg)){ echo $msg ;} ?>

        <br><br><br><br><br><br>

        <table align="center" width="80%">
            <tr align="center">
                <td>USD</td>
                <td>EURO</td>
                <td>YEN</td>
                <td>GBP</td>
                <td>AUD</td>

                <td>Bank Deposits</td>
                <td>Cash Deposits </td>

            </tr>
            <tr align="center">
                <td><input value="00" style="width: 130px;"  type="text" id="current_usd" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_euro" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_yen" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_gbp" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_aud" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_bd" readonly/></td>
                <td><input value="00" style="width: 130px;"  type="text" id="current_cd" readonly/></td>


            </tr>

        </table>
        <div id="table-wrapper">
            <div id="table-scroll">
                <table border="1">
                    <thead>
                    <tr>
                        <th><span class="text">Date</span></th>
                        <th><span class="text">Start Cash</span></th>
                        <th><span class="text">End Cash</span></th>
                        <th><span class="text">Cash Sales</span></th>
                        <th><span class="text">Card Sales</span></th>
                        <th><span class="text">Bank Deposits</span></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php  foreach($Mainarray as $each_data){?>
                        <tr>
                            <td><?php  echo $each_data ['Date'] ; ?></td>
                            <td><?php  echo $each_data ['startCash']  ; ?></td>
                            <td><?php  echo $each_data ['endCash'] ; ?></td>
                            <td><?php  echo $each_data ['CashSales'] ; ?></td>
                            <td><?php  echo $each_data ['CardSales'] ; ?></td>
                            <td><?php  echo $each_data ['BD'] ; ?></td>


                        </tr>
                    <?php } ?>

                    </tbody>
                </table>
            </div>
        </div>

<!--        <table width="100%" border="3">-->
<!--            <tr>-->
<!--                <td>Date</td>-->
<!--                <td>Type</td>-->
<!--                <td>Cash</td>-->
<!--                <td>CC</td>-->
<!--                <td>TC</td>-->
<!---->
<!--                <td>Bank Deposits</td>-->
<!--                <td>User ID</td>-->
<!---->
<!--            </tr>-->
<!--            --><?php // foreach($cash_details as $each_data){?>
<!--                <tr>-->
<!--                    <td>--><?php // echo $each_data ['feed_time'] ; ?><!--</td>-->
<!--                    <td>--><?php // if ( $each_data ['end_or_start'] == 1) echo 'Start'; elseif($each_data ['end_or_start'] == 0) echo 'End' ; ?><!--</td>-->
<!--                    <td>--><?php // echo $each_data ['cash_amount'] ; ?><!--</td>-->
<!--                    <td>--><?php // echo $each_data ['cc_amount'] ; ?><!--</td>-->
<!--                    <td>--><?php // echo $each_data ['tc_amount'] ; ?><!--</td>-->
<!--                    <td>--><?php // echo $each_data ['bd_amount'] ; ?><!--</td>-->
<!--                    <td>--><?php // echo $each_data ['username'] ; ?><!--</td>-->
<!---->
<!--                </tr>-->
<!--            --><?php //} ?>
<!--        </table>-->



        <div id="table-wrapper">
            <div id="table-scroll">
                <table border="2">
                    <?php foreach($pcArray as $eachDay=>$eachData){ ?>
                    <thead>
                    <tr>
                        <th><span class="text"> Date:</span></th>
                        <th><span class="text"><?php echo $eachDay; ?></span></th>

                    </tr>
                    <tr>
                        <td>
                            Description
                        </td>
                        <td>
                            Amount
                        </td>
                    </tr>
                    <?php foreach($eachData as $eachRec){ ?>
                        <tr>
                            <td>
                                <?php echo $eachRec['Description'] ;  ?>
                            </td>
                            <td>
                                <?php echo $eachRec['Amount'] ;  ?>
                            </td>
                        </tr>

                    <?php } ?>
                    <tr bgcolor="black">
                        <td colspan="2">
                        </td>
                    </tr>
                    </thead>
                    <?php }?>

                </table>
            </div>
        </div>

<!--                    <table width="95%" border="2">-->
<!--                        --><?php //foreach($pcArray as $eachDay=>$eachData){ ?>
<!--                            <thead style="overflow: scroll;">-->
<!--                            <tr bgcolor="#5f9ea0">-->
<!--                                <td>-->
<!--                                    Date:-->
<!--                                </td>-->
<!--                                <td>-->
<!--                                    --><?php //echo $eachDay; ?>
<!--                                </td>-->
<!--                            </tr>-->
<!--                            <tr>-->
<!--                                <td>-->
<!--                                    Description-->
<!--                                </td>-->
<!--                                <td>-->
<!--                                    Amount-->
<!--                                </td>-->
<!--                            </tr>-->
<!--                            --><?php //foreach($eachData as $eachRec){ ?>
<!--                                <tr>-->
<!--                                    <td>-->
<!--                                        --><?php //echo $eachRec['Description'] ;  ?>
<!--                                    </td>-->
<!--                                    <td>-->
<!--                                        --><?php //echo $eachRec['Amount'] ;  ?>
<!--                                    </td>-->
<!--                                </tr>-->
<!---->
<!--                            --><?php //} ?>
<!--                            <tr bgcolor="black">-->
<!--                                <td colspan="2">-->
<!--                                </td>-->
<!--                            </tr>-->
<!--                            </thead>-->
<!--                        --><?php //}?>
<!---->
<!--                    </table>-->
<!--                </li>-->




    </div>
</div>
</body>




</html>
