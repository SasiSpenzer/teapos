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
$cash_details = $cash_obj->report_cash_range();

?>


<?php

$user_obj = new User();


//Forms posted

if(isset($_POST['done'])){

    if (!empty($_POST['total_display'] ) || $_POST['total_display'] == '00.00' ) {

        if (!empty($_POST)) {

            $cash_data = array();

            $cash_data['cash_amount'] = $_POST['cash_p_val'] + $_POST['coins_p_val'];
            $cash_data['cc_amount'] = $_POST['cc_p_val'];
            $cash_data['tc_amount'] = $_POST['tc_p_val'];
            $cash_data['bd_amount'] = $_POST['bd_p_val'];

            $cash_data['end_or_start'] = 1;
            $cash_data['user_id'] = $_SESSION['user_id'];
            $cash_data['feed_time'] = date("Y-m-d");
            $cash_data['cash_or_card'] =1;
            $petty_cash_data = json_encode($_POST['petty_cash_records']);
            $cash_data['pc_amount'] = $petty_cash_data;


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
    else{
        $msg = 'please count the money';
    }
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
        width: 255px;
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
    .petty_cash_single_entry_amount {
        font-size: 20px;
        padding: 10px;
        width: 100px !important;

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

</style>



</head>
<form method="post" onsubmit=" return set_petty_cash()">
    <body>


    <div class="keypad-popup">
        <div class="total">Total<input style="text-align: center;font-size: large" id="total_display" name="total_display" type="text" value="00.00" /></div>
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
        <div class="keypad-row">
            <button id="key_pad_btn" value="1" type="button" class="keypad-key top-border">1</button>
            <button id="key_pad_btn" value="2" type="button" class="keypad-key top-border">2</button>
            <button id="key_pad_btn" value="3" type="button" class="keypad-key top-border">3</button>
            <button id="key_pad_btn" value="4" type="button" class="keypad-key float-key top-border">4</button>
            <div id="add_petty_cash"  class="keypad-button"><button value="pc" type="button" class="keypad-color-disabled">Petty cash</button><br />
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

    <input type="submit" value="Finish" name="done" class="keypad-key23 bottom-button">

    <?php if(isset($msg)){ echo $msg ;} ?>
    </body>
</form>
<br><br><br><br><br><br><br>
<table width="55%" border="5" align="center">
    <tr>
        <td>Date</td>
        <td>Type</td>
        <td>Cash</td>
        <td>CC</td>
        <td>TC</td>

        <td>Bank Deposits</td>
        <td>User ID</td>

    </tr>
    <?php  foreach($cash_details as $each_data){?>
    <tr>
        <td><?php  echo $each_data ['feed_time'] ; ?></td>
        <td><?php  if ( $each_data ['end_or_start'] == 1) echo 'Start'; elseif($each_data ['end_or_start'] == 0) echo 'End' ; ?></td>
        <td><?php  echo $each_data ['cash_amount'] ; ?></td>
        <td><?php  echo $each_data ['cc_amount'] ; ?></td>
        <td><?php  echo $each_data ['tc_amount'] ; ?></td>
        <td><?php  echo $each_data ['bd_amount'] ; ?></td>
        <td><?php  echo $each_data ['user_id'] ; ?></td>

    </tr>
    <?php } ?>
</table>
</html>
