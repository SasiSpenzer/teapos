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

    if (!empty($_POST['total_display_final']) || $_POST['total_display_final'] == '00' ) {

        if (!empty($_POST)) {

            $cash_data = array();

             $total_display = isset($_POST ['total_display_final']) ? trim($_POST ['total_display_final']) : '';
             $lkr_display = isset($_POST ['LKR_display_final']) ? trim($_POST ['LKR_display_final']) : '';
             $usd_display = isset($_POST ['USD_display_final']) ? trim($_POST ['USD_display_final']) : '';
             $euro_display = isset($_POST ['Euro_display_final']) ? trim($_POST ['Euro_display_final']) : '';
             $pound_display = isset($_POST ['Pound_display_final']) ? trim($_POST ['Pound_display_final']) : '';
             $bank_d_display = isset($_POST ['bd']) ? trim($_POST ['bd']) : '';
             $pc_display = isset($_POST ['petty_cash_records']) ? trim($_POST ['petty_cash_records']) : '';

             $cash_feed_array = array();
             $cash_feed_array['end_or_start'] = 1;
             $cash_feed_array['cash_amount'] = $total_display;
             $cash_feed_array['bd_amount'] = $bank_d_display;
             $cash_feed_array['pc_amount'] = json_encode($pc_display);
             $cash_feed_array['user_id'] = $_SESSION['user_id'];
             $cash_feed_array['feed_time'] = date("Y-m-d");
             $cash_feed_array['cash_or_card'] = 1;


            try {
                $cash_obj = new CashFeed();
                $save_cash = $cash_obj->save_cash_feed($cash_feed_array);

                if(!empty($_POST['euro_rate'])){

                    $id= 1;
                    $data = array(
                        'rate'=>$_POST['euro_rate']
                    );
                    $cash_obj->update_rate($data,$id) ;

                    $id= 5;
                    $data = array(
                        'rate'=>$_POST['pound_rate']
                    );
                    $cash_obj->update_rate($data,$id) ;
                    $id= 4;
                    $data = array(
                        'rate'=>$_POST['usd_rate']
                    );
                    $cash_obj->update_rate($data,$id) ;
                }

                $get_last_url = $cash_obj->get_last_page();
                $last_url = $get_last_url['url'];

                $today =  $date = date('Y-m-d');
                $end_cash = $cash_obj->end_cash_total($today);
                $really_end_cash = $end_cash['Total'];
                $_SESSION['start_cash_session'] = date('Y-m-d');

				if($really_end_cash == $total_display || ($really_end_cash - $total_display) <= 500 ){
                    //header('Location:'.$last_url);
                    if($total_display == '' || $total_display == 0){
                        $error = "Please Do not Start Without Counting the Cash";
                    } else {
                        header("Location:http://localhost/tea_showcase_gf/index.php?cat_id=50");
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
<script src="https://code.jquery.com/jquery-2.2.3.js" integrity="sha256-laXWtGydpwqJ8JA+X9x2miwmaiKhn8tVmOVEigRNtP4=" crossorigin="anonymous"></script>


<!-- by Sasi Spenzer 2016.04.06  10.34 am @ CMS Office -->

<script type="text/javascript">

    var var_petty_cash_amount = 0;
    var var_petty_cash_entries = new Array();
    $(document).ready(function() {

        // commented by Sasi Spenzer
//        $('.keypad-color-disabled').click(function(){
//            var type = $(this).val();
//            $('#type_saver').val(type);
//            $(this).addClass('keypad-color');
//            $('.ff button').addClass('keypad-color-disabled');
//            $(this).removeClass('keypad-color-disabled');
//        });

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
            $('#total_display_final').val('00');
            $('#USD_display_final').val('00');
            $('#Euro_display_final').val('00');
            $('#Pound_display_final').val('00');
            $('#LKR_display_final').val('00');
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
            var current_value = $('#'+selected_type+'').val();
            //alert(current_value);
            var final_adding_type = '';
            var total_adding = 'total_display_final';

            if(selected_type == 'LKR_display'){

                final_adding_type = 'LKR_display_final';

            } else if(selected_type == 'USD_display'){

                final_adding_type = 'USD_display_final';

            } else if(selected_type == 'Euro_display'){

                final_adding_type = 'Euro_display_final';

            } else if(selected_type == 'Pound_display'){

                final_adding_type = 'Pound_display_final';

            }
            else if(selected_type == 'pc'){

                final_adding_type = 'pc';
            }
            else{
                final_adding_type = 'bd';
            }

            var current_total = $('#'+total_adding+'').val();

            if(current_total != '00' && current_total != ''){

                if(final_adding_type == 'pc' || final_adding_type == 'bd' ){

                    var make_total = parseInt(current_total) - parseInt(current_value);
                } else {

                    var make_total = parseInt(current_total) + parseInt(current_value);
                }

                var usd_rate = $("#usd_rate").val();
                var euro_rate = $("#euro_rate").val();
                var pound_rate = $("#pound_rate").val();

                var make_total_usd = parseInt(current_total) + parseInt(current_value * usd_rate);
                var make_total_euro = parseInt(current_total) + parseInt(current_value * euro_rate);
                var make_total_pound = parseInt(current_total) + parseInt(current_value * pound_rate);



                $('#'+total_adding+'').attr('value', ''); // removing the value attribute for a better future
                if(selected_type == 'LKR_display' || selected_type == 'pc' || selected_type == 'bank2'){

                    $('#'+total_adding+'').val(make_total);

                } else if(selected_type == 'USD_display'){

                    $('#'+total_adding+'').val(make_total_usd);

                } else if(selected_type == 'Euro_display'){

                    $('#'+total_adding+'').val(make_total_euro);

                } else if(selected_type == 'Pound_display'){

                    $('#'+total_adding+'').val(make_total_pound);

                }

                if($('#'+final_adding_type+'').val() == '00'){
                    $('#'+final_adding_type+'').val(current_value);

                }else{
                    var adding_type_current_value = $('#'+final_adding_type+'').val();
                    var make_adding_type_total = parseInt(adding_type_current_value) + parseInt(current_value);
                    $('#'+final_adding_type+'').val(make_adding_type_total);

                }

            }else{

                $('#'+total_adding+'').attr('value', ''); // removing the value attribute for a better future

                var usd_rate = $("#usd_rate").val();
                var euro_rate = $("#euro_rate").val();
                var pound_rate = $("#pound_rate").val();

                if(selected_type == 'LKR_display'){

                    $('#'+total_adding+'').val(current_value);

                } else if(selected_type == 'USD_display'){

                    $('#'+total_adding+'').val(current_value * usd_rate);

                } else if(selected_type == 'Euro_display'){

                    $('#'+total_adding+'').val(current_value * euro_rate);

                } else if(selected_type == 'Pound_display'){

                    $('#'+total_adding+'').val(current_value * pound_rate);

                }

                if($('#'+final_adding_type+'').val() == '00'){
                    $('#'+final_adding_type+'').val(current_value);
                }else{
                    var adding_type_current_value = $('#'+final_adding_type+'').val();
                    var make_adding_type_total = parseInt(adding_type_current_value) + parseInt(current_value);
                    $('#'+final_adding_type+'').val(make_adding_type_total);

                }

            }
            $('#'+selected_type+'').val('00');


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

    function  make_selected(id,input_id) {
         var selected_id = id;
        $('#type_saver').val(input_id);

        $(".total").attr('style','');
        $(".additional-button-set").attr('style','');

        $('#'+selected_id+'').css("background", "#66cc66");
        $('#'+input_id+'').prop('disabled', false);
        if($('#'+input_id+'').val() == '00'){
            $('#'+input_id+'').val('');
        }

    }
</script>

<!-- end by Sasi Spenzer -->



<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Tea POS New Millennium </title>
<link rel="stylesheet" type="text/css" href="css/cash-registry.css"/>
</head>
<form method="post" onsubmit=" return set_petty_cash()">

	<body>
		<div class="main-wrapper">

			<div class="total-calculations">

				<!-- Start duplicated top by  Sasitha Perera -->
				<div class="total"><input id="total_display_final" name="total_display_final" placeholder="00" type="text" value=""/>
					<span>Total</span>
				</div>

				<div class="total"><input id="LKR_display_final" name="LKR_display_final" type="text" placeholder="00" value="00"/>
					<span>LKR</span>
				</div>

				<div class="total"><input id="USD_display_final" name="USD_display_final" type="text" placeholder="00" value="00"/>
					<span> &#36; </span>
				</div>

				<div class="total"><input id="Euro_display_final" name="Euro_display_final" type="text" placeholder="00" value="00"/>
					<span> &euro; </span>
				</div>

				<div class="total"><input  id="Pound_display_final" name="Pound_display_final" type="text" placeholder="00" value="00"/>
					<span> &pound; </span>
				</div>

				<!-- end -->


                <div style="margin-right: -157px;" align="center">
				<div onclick="make_selected(this.id,'LKR_display')" id="LKR_display_div" class="total"><input disabled id="LKR_display" name="LKR_display" type="text" value="00"/>
					<span>LKR</span>
				</div>

				<div onclick="make_selected(this.id,'USD_display')" id="USD_display_div" class="total"><input disabled id="USD_display" name="USD_display" type="text" value="00"/>
					<span> &#36; </span>
				</div>

				<div onclick="make_selected(this.id,'Euro_display')" id="Euro_display_div" class="total"><input disabled id="Euro_display" name="Euro_display" type="text" value="00"/>
					<span> &euro; </span>
				</div>

				<div onclick="make_selected(this.id,'Pound_display')" id="Pound_display_div" class="total"><input disabled id="Pound_display" name="Pound_display" type="text" value="00"/>
					<span> &pound; </span>
				</div>
                </div>



				<div class="clear"></div>
			</div>

<!--            <div align="center" class="additional-button-set">-->
<!--                <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!--                <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!--                <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!---->
<!--                <div class="clear"></div>-->
<!---->
<!---->
<!--            </div>-->


			<?php if(isset($error)){ ?>
                <div class="total">
                    <font color="red"> Error:
                        <?php echo $error ; ?>
                    </font>
                </div>
			<?php  } ?>

			<div class="wrapper">

				<?php /*
	   
	   <!-- Commented by Dilantha -->
        <div class="keypad-popup">
            <div class="keypad-row ff">
                <button value="cash" type="button" class="keypad-color-disabled">Cash</button>
                <button value="aud" type="button" class="keypad-color-disabled">AUD</button>
                <button value="euro" type="button" class="keypad-color-disabled">Euro</button>
               <button value="usd" type="button" class="keypad-color-disabled">USD</button>
            </div>

            <div class="keypad-row margin-bottom row-upper">
                <input id="cash" disabled style="text-align: center;font-size: large" type="text" value="" class="text-area"/>
                <input id="aud" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
                <input id="euro" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
                <input id="usd" disabled style="text-align: center;font-size: large" type="text" class="text-area"/>
            </div>
			
		*/	?>




				<div class="calculator-wrap">
					<div class="keypad-row">

                        <button id="key_pad_btn" value="0" type="button" class="keypad-key">0</button>
                        <button id="key_pad_btn" value="1" type="button" class="keypad-key">1</button>
                        <button id="key_pad_btn" value="2" type="button" class="keypad-key">2</button>
                        <button id="key_pad_btn" value="3" type="button" class="keypad-key">3</button>
                        <button id="key_pad_btn" value="4" type="button" class="keypad-key">4</button>
                        <div class="clear"></div>

                    </div>
                    <div class="keypad-row">

                        <button id="key_pad_btn" value="5" type="button" class="keypad-key">5</button>
                        <button id="key_pad_btn" value="6" type="button" class="keypad-key">6</button>
                        <button id="key_pad_btn" value="7" type="button" class="keypad-key">7</button>
                        <button id="key_pad_btn" value="8" type="button" class="keypad-key">8</button>
                        <button id="key_pad_btn" value="9" type="button" class="keypad-key">9</button>
                        <div class="clear"></div>

                    </div>
					<div class="keypad-row">
						<button id="add_by_section" type="button" class="keypad-key bottom-button-function">Add</button>
						<button id="clear_by" type="button" class="keypad-key bottom-button-function">Clear</button>
						<button id="clear_total" type="button" class="keypad-key bottom-button-function">Clear Total</button>
						<button id="key_pad_btn" value="." type="button" class="keypad-key  bottom-button">.</button>
						<!-- <button id="key_pad_btn" value="00" type="button" class="keypad-key  bottom-button">00</button>
                
                <button   <?php if($_SESSION['is_super_admin'] != 1){ ?> disabled="disabled" <?php } ?> value="Open_Cash" id="open_cash" type="button" class="keypad-key bottom-button">Open Cash</button> -->
						<!--<div class="keypad-button"><button type="button" class="keypad-color">Text</button><br />-->
						<!--<input style="text-align: center;font-size: large" type="text" value="" class="text-area"/></div>-->
						<div class="clear"></div>
					</div>

					<div class="clear"></div>
				</div>


			</div>


			<input value="" id="type_saver" name="type_saver" type="hidden"/>

			<input value="0" id="cash_p_val" name="cash_p_val" type="hidden"/>
			<input value="0" id="coins_p_val" name="coins_p_val" type="hidden"/>
			<input value="0" id="cc_p_val" name="cc_p_val" type="hidden"/>
			<input value="0" id="tc_p_val" name="tc_p_val" type="hidden"/>
			<input value="0" id="pc_p_val" name="pc_p_val" type="hidden"/>
			<input value="0" id="bd" name="bd" type="hidden"/>
			<input value="0" id="cd_p_val" name="cd_p_val" type="hidden"/>
			<input value="" id="petty_cash_records" name="petty_cash_records" type="hidden"/>
			<input value="0" id="grand_total" name="grand_total" type="hidden"/>
			<input value="" id="petty_cash_value" name="petty_cash_value" type="hidden"/>

			<input value="" id="LKR_display_div" name="LKR_display_div" type="hidden"/>
			<input value="" id="USD_display_div" name="USD_display_div" type="hidden"/>
			<input value="" id="Euro_display_div" name="Euro_display_div" type="hidden"/>
			<input value="" id="Pound_display_final" name="Pound_display_final" type="hidden"/>



			<?php if(isset($msg)){ echo $msg ;} ?>
			<div id="petty_cash_entries"></div>
			
			
			<div align="center" class="additional-button-set">
                    <button onclick="make_selected(this.id,'pc')" class="add-value-button" id="add_petty_cash" value="pc" type="button">Petty cash</button>
					<input id="pc" type="text" value="" />
				    <button onclick="make_selected(this.id,'bank2')" id="bd" class="add-value-button"  value="bank" type="button" >Bank Deposits</button>
				    <input id="bank2" type="text" />

				<div class="clear"></div>


			</div>





			<div id="table-wrapper">
				<div id="table-scroll" class="drop-down-utility drop-down-utility-one">
					<span class="drop-titile drop-titile-one">Petty Cash Drop Down</span>
					<ul class="list-types">
						<?php  foreach($Mainarray as $each_data){?>
						<li>
							<ul style="list-style-type: none">
								<li style="float: left;padding: 0 1em;">Date
									<?php  echo $each_data ['Date'] ; ?>
								</li>
								<li style="float: left;padding: 0 1em;">Start Cash
									<?php  echo $each_data ['startCash']  ; ?>
								</li>
								<li style="float: left;padding: 0 1em;"> End Cash
									<?php  echo $each_data ['endCash'] ; ?>
								</li>
								<li style="float: left;padding: 0 1em;">Cash Sales
									<?php  echo $each_data ['CashSales'] ; ?>
								</li>
								<li style="float: left;padding: 0 1em;">Card Sales
									<?php  echo $each_data ['CardSales'] ; ?>
								</li>
								<li style="float: left;padding: 0 1em;">Bank Deposits
									<?php  echo $each_data ['BD'] ; ?>
								</li>
							</ul>

						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
			
			
			
						<div id="table-wrapper">
				<div id="table-scroll" class="drop-down-utility drop-down-utility-two">
					<span class="drop-titile drop-titile-two"> Bank Deposit Drop Down</span>
					<ul class="list-types">
						<?php  foreach($Mainarray as $each_data){ ?>
						<li>
							<ul>
								<li>Date
									<?php  echo $each_data ['Date'] ; ?>
								</li>
								<li>Start Cash
									<?php  echo $each_data ['startCash']  ; ?>
								</li>
								<li> End Cash
									<?php  echo $each_data ['endCash'] ; ?>
								</li>
								<li>Cash Sales
									<?php  echo $each_data ['CashSales'] ; ?>
								</li>
								<li>Card Sales
									<?php  echo $each_data ['CardSales'] ; ?>
								</li>
								<li>Bank Deposits
									<?php  echo $each_data ['BD'] ; ?>
								</li>
							</ul>

						</li>
						<?php } ?>
					</ul>
				</div>
			</div>
            <table>

                <tr>
                    <td>
                        USD
                    </td>
                    <td>
                        Euro
                    </td>
                    <td>
                        POUND
                    </td>

                </tr>


                <tr>

                    <td><input name="usd_rate" value="<?php echo $usd_rate; ?>" id="usd_rate" type="text"/></td>
                    <td><input name="euro_rate" value="<?php echo $euro_rate ; ?>" id="euro_rate" type="text"/></td>
                    <td><input name="pound_rate" value="<?php echo $gbp_rate ; ?>" id="pound_rate" type="text"/></td>

                </tr>

            </table>


            <br>
            <div align="center">

                <input align="center" style="background-color: #4CAF50;
    border: none;
    color: white;
    padding: 15px 32px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    margin: 4px 2px;
    cursor: pointer;" type="submit" value="Proceed Now" name="done" class="btn btn-primary"/>

            </div>


<!--			<div align="center" class="additional-button-set">-->
<!--                    <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!--				    <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!--				    <button class="add-value-button" value="pc" type="button">Petty cash</button>-->
<!---->
<!--				    <div class="clear"></div>-->
<!---->
<!---->
<!--			</div>-->
			
			

			<!-- By Sasi Spenzer -->
			<!--        <div id="table-wrapper">-->
			<!--            <div id="table-scroll">-->
			<!--                <table border="2">-->
			<!--                    -->
			<?php //foreach($pcArray as $eachDay=>$eachData){ ?>
			<!--                    <thead>-->
			<!--                    <tr>-->
			<!--                        <th><span class="text"> Date:</span></th>-->
			<!--                        <th><span class="text">-->
			<?php //echo $eachDay; ?>
			<!--</span></th>-->
			<!---->
			<!--                    </tr>-->
			<!--                    <tr>-->
			<!--                        <td>-->
			<!--                            Description-->
			<!--                        </td>-->
			<!--                        <td>-->
			<!--                            Amount-->
			<!--                        </td>-->
			<!--                    </tr>-->
			<!--                    -->
			<?php //foreach($eachData as $eachRec){ ?>
			<!--                        <tr>-->
			<!--                            <td>-->
			<!--                                -->
			<?php //echo $eachRec['Description'] ;  ?>
			<!--                            </td>-->
			<!--                            <td>-->
			<!--                                -->
			<?php //echo $eachRec['Amount'] ;  ?>
			<!--                            </td>-->
			<!--                        </tr>-->
			<!---->
			<!--                    -->
			<?php //} ?>
			<!--                    <tr bgcolor="black">-->
			<!--                        <td colspan="2">-->
			<!--                        </td>-->
			<!--                    </tr>-->
			<!--                    </thead>-->
			<!--                    -->
			<?php //}?>
			<!---->
			<!--                </table>-->
			<!--            </div>-->
			<!--        </div>-->






		</div>
		</div>
		<script>
			$( '.drop-titile-one' ).click( function () {
				$( '.drop-down-utility-one' ).toggleClass( 'active' );
			} );
			
				$( '.drop-titile-two' ).click( function () {
				$( '.drop-down-utility-two' ).toggleClass( 'active' );
			} );
		</script>

	</body>




</html>