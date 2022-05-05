//   $(document).ready(function(){
//
//
//       $('#received_amount').keypad();
//       $('#balance_amount').keypad();
//
//
//
//
//       // toggle new custtomer form
//       $('#select_customer').change(function(){
//
//           if(this.checked){
//               $('#customer_selection').hide('slow');
//               $('#new_customer_form').show('slow');
//           }else{
//               $('#new_customer_form').hide('slow');
//               $('#customer_selection').show('slow');
//           }
//       });
//
//
//       // insert customer data
//       $('#complete_process').on('click', function(){
//
//           if($("#select_customer").prop('checked') == true){
//
//               var customer_name = $('#customer_name').val();
//               var contact_no = $('#contact_no').val();
//               var email = $('#email').val();
//
//               var customer_data_array = {
//                   customer_name: customer_name,
//                   contact_no:contact_no,
//                   email:email
//               }
//
//               if(customer_data_array['customer_name'] =='' || customer_data_array['contact_no'] =='' || customer_data_array['email'] =='') {                      //if it is blank.
//                   if(customer_data_array['customer_name'] ==''){
//                       $( '#customer_name_div' ).addClass("has-error");
//                   } else if(customer_data_array['contact_no'] ==''){
//                       $( '#customer_name_div' ).removeClass("has-error");
//                       $( '#contact_no_div' ).addClass("has-error");
//                   }else if(customer_data_array['email'] ==''){
//                       $( '#contact_no_div' ).removeClass("has-error");
//                       $( '#email_div' ).addClass("has-error");
//                   }
//               } else {
//                   $( '#email_div' ).removeClass("has-error");
//
//                   $.ajax({
//                       url: "extra_functions.php",
//                       type: "POST",
//                       cache: false,
//                       async:false,
//                       data: {get_customer_data:true,customer_data_array:customer_data_array},
//                       success: function(theResponse){
//                           $( '#customer_form' ).each(function(){
//                               this.reset();
//                           });
//                       }
//                   });
//                   e.preventDefault();
//               }
//
//           }else{
//               $.ajax({
//                   url: "extra_functions.php",
//                   type: "POST",
//                   cache: false,
//                   async:false,
//                   data: {add_to_cart_single:true,product_id:cart_product_id},
//                   success: function(theResponse){
//
//
//                   }
//               });
//           }
//       });
//       function addtoCartSingle(product_id) {
//           var cart_product_id = product_id;
//           $.ajax({
//               url: "extra_functions.php",
//               type: "POST",
//               cache: false,
//               async:false,
//               data: {add_to_cart_single:true,product_id:cart_product_id},
//               success: function(theResponse){
//
//               }
//           });
//       }
//   });
//
//
//
//
//
//
