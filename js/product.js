$(document).ready(function(){


    function delete_product(product_id) {



        bootbox.confirm("Are you sure want to delete ?", function(result) {

            if(result) {

                $.ajax({
                    url: 'extra_function.php',
                    dataType: 'json',
                    data: {'delete_product_by_id':true,'product_id': product_id},
                    type: 'post',
                    success: function(data)
                    {
                        $('.delete_product_success_message').show();
                        window.location.href='index.php';
                    }
                });

            }

        });




    }



//    $(".product-edit-submit-form_awesome").on('click',function(e) {
//
//        alert('ccc');
//        var price           = $('.edit_prduct_price').val();
//        var name  =             $('.edit_prduct_name').val();
//        var name =          $('.edit_prduct_name').val();
//        var product_qty     = parseInt($('.edit_prduct_quantity').val());
//        var product_id     = $('.edit_product_id').val();
//
//        var add_new_prduct_quantity = parseInt($('.add_new_prduct_quantity').val());
//        var cal_operator =   $('input:radio[name=cal_operator]').filter(":checked").val()
//
//        if (typeof cal_operator === "undefined") {
//            var cal_operator = "1";
//        }
//        var error_messages = [];
//
//
//        if(add_new_prduct_quantity == ""){
//            error_messages[4]=true;
//            $('.add_new_prduct_quantity').addClass('error_msg');
//        }else if(!$.isNumeric($('.add_new_prduct_quantity').val())){
//            error_messages[4]=true;
//            $('.add_new_prduct_quantity').addClass('error_msg');
//        }else{
//            $('.add_new_prduct_quantity').removeClass('error_msg');
//        }
//
//        if(cal_operator==0){
//
//            if(add_new_prduct_quantity > product_qty){
//                error_messages[5]=true;
//                $('.add_new_prduct_quantity').addClass('error_msg');
//            }
//
//        }
//
//        if(price == ""){
//            error_messages[2]=true;
//            $('.edit_prduct_price').addClass('error_msg');
//        }else if(!$.isNumeric($('.edit_prduct_price').val())){
//            error_messages[2]=true;
//            $('.edit_prduct_price').addClass('error_msg');
//        }else{
//            $('.edit_prduct_price').removeClass('error_msg');
//        }
//
//        if(error_messages.length === 0){
//
//
//            $.ajax({
//                url: 'extra_function.php',
//                dataType: 'json',
//                data: {'edit_product_details':true,'product_id':product_id,'price': price,'name':name,'product_qty':product_qty,'add_new_prduct_quantity':add_new_prduct_quantity,'cal_operator':cal_operator},
//                type: 'post',		// To send DOMDocument or non processed data file it is set to false (i.e. data should not be in the form of string)
//                success: function(data)  		// A function to be called if request succeeds
//                {
//                    $('.edit_success_message').show();
//                    window.location.href='index.php';
//
//                }
//            });
//
//        }
//
//    });

    $('#search_model').click(function(){
       $('#search_product').modal('show');
    });


    //Product seach starts here
    $('#search_keyword').keyup(function(){

        var keyword = $('#search_keyword').val();
        //alert(keyword);

        $.ajax({
           url: "extra_function.php",
           type: "POST",
           cache: false,
           async:false,
           data: {product_keyword:true,keyword:keyword},
           success: function(theResponse){
               $('#result_tred').html(theResponse);
          }
       });



    });

    $('#ok').click(function(){
        $('#added_cart').modal('hide');
    });
//    $('#menu ul li').on('click', function(e){
//
//        var cat_id = (this.id);
//
//        $.ajax({
//            type: 'post',
//            url: 'extra_function.php',
//            cache: false,
//            //dataType: "json",
//            async: true,
//            data: {'cat_details': true, 'cat_id': cat_id},
//            success: function (theResponse) {
//                $('#slider_divs').html(theResponse);
//                reinitSwiper(mySwiper);
//
//                return false;
//            }
//
//        });
//
//        return false;
//    });


//    function add_to_cart(product_id) {
//        var item_data = $('#product_form_'+product_id).serializeArray();
//        console.log(item_data);
//        var product_qty = "";
//        var product_size = "";
//        $.each( item_data, function( key, value ) {
//            if(value.name == 'product_size') {
//                product_size = value.value;
//            }
//            if(value.name == 'product_qty') {
//                product_qty = value.value;
//            }
//        });
//        var cart_product_id = product_id;
//        $.ajax({
//            url: "extra_functions.php",
//            type: "POST",
//            cache: false,
//            async:false,
//            data: {add_to_cart_detail:true,product_id:cart_product_id,product_size:product_size,product_qty:product_qty},
//            success: function(theResponse){
//                var theResponse = $.parseJSON(theResponse);
//                var cart_total = 0;
//                $.each(theResponse, function(index, value) {
//
//                    cart_total += (value.product_price*value.product_qty);
//                });
//                $("#cart_total_display").html("Cart Total : Rs."+cart_total.toFixed(2));
//                bootbox.alert("Product added to the cart");
//
//
//            }
//        });
//        $("#customer_top_bar").show();
//    }


});