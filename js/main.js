

var main_total = 0;
var old_v = '';
var pure_total_old = 0;
$(document).ready(function(){
    var id;

    $(":input").focus(function () {
        id = this.id;
         old_v = '';
    });
    $('.digits').click(function(){
        var val = $(this).val();
        var is_clear = $(this).attr('id');

        if(is_clear  == 'clear'){
            $('#'+id+'').val("");
            return false;
        }
        else if(is_clear == 'delete'){

             var total =  $('#'+id+'').val();

            var jio = total.slice(0,-1);

            $('#'+id+'').empty().val(jio);
            return false;
        } else if(is_clear == 'clear_total'){

        }
        else if(is_clear == 'add_count'){

        }
        if (is_clear == 'c_type'){
            $('.selected_cn').removeClass('selected_cn');
            $(this).removeAttr('style');

            $(this).addClass('selected_cn');
        }

        else if(val < 10){

         if(is_clear == 'acc_dip'){
             $('.selected_c_type').removeClass('selected_c_type');
             $(this).removeAttr('style');

             $(this).addClass('selected_c_type');
         }
         else if(is_clear == 'cash_paid'){
             $('.selected_c_type').removeClass('selected_c_type');
             $(this).removeAttr('style');

             $(this).addClass('selected_c_type');
         }
         else if(is_clear == 'report'){
             $('.selected_c_type').removeClass('selected_c_type');
             $(this).removeAttr('style');

             $(this).addClass('selected_c_type');
         }
         else if(is_clear == 'cash_dip'){
             $('.selected_c_type').removeClass('selected_c_type');
             $(this).removeAttr('style');

             $(this).addClass('selected_c_type');
         }


         else{
             $.ajax({
                 url: 'extra_function.php',

                 data: {'convert_numbers':true,'old_v': old_v,'val':val},
                 type: 'post',
                 success: function(data)
                 {
                     old_v = data;
                     $('#'+id+'').val(data);
                 }
             });
         }

        }
    });
    function commaSeparateNumber(val){
        while (/(\d+)(\d{3})/.test(val.toString())){
            val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
        }
        return val;
    }




    $('#add_count').click(function(){

        var cash = $('#cash').val();

        var checks = $('#coins').val();


        var coins = $('#checks').val();
        var cc = $('#cc').val();
        var cash_deposits = $('#cash_dip_t').val();

        var cash_paid = $('#cash_paid_t').val();
        var acc_deposits = $('#acc_dip_t').val();



        if(!cash){

            cash = 0 ;

        }
        if(!checks){

            checks = 0 ;
        }
        if(!coins){
            coins = 0 ;
        }
        if(!cc){
            cc = 0 ;
        }
        if(!cash_deposits){
            cash_deposits = 0 ;
        }
        if(!cash_paid){
            cash_paid = 0 ;
        }
        if(!acc_deposits){
            acc_deposits = 0 ;
        }

        var added = parseInt(cash) + parseInt(checks) + parseInt(coins) + parseInt(cc) + parseInt(cash_deposits);

        var reduce = parseInt(cash_paid) + parseInt(acc_deposits) ;
       

        var pure_total = (added) - (reduce) ;

        pure_total_old = pure_total + pure_total_old ;

        $('#total_numbers').html(commaSeparateNumber(pure_total_old));
        $('#continue').val(pure_total_old);
        $('#total_numbers').val(pure_total_old);
        $('#cash').val(cash);

        $('#coins').val(checks);


        $('#checks').val(coins);
        $('#cc').val(cc);
        $('#cash_dip_t').val(cash_deposits);

        $('#cash_paid_t').val(cash_paid);
        $('#acc_dip_t').val(acc_deposits);
        $('.number').text('');
        return false;

    });

    $('#clear_total').click(function(){
        $('#total_numbers').text('00.00') ;
    });



});


