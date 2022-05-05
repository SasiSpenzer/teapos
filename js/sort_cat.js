$(document).ready(function(){
    $('#select_cat').change(function(){
        $( "#sortable" ).sortable();
        $( "#sortable" ).disableSelection();
        var cat_id = $('#select_cat').val();
        //alert(cat_id);
        $.ajax({
            url: 'extra_function.php',

            data: {'Cat_droup_down':true,'Cat_id': cat_id},
            type: 'post',
            success: function(data)
            {
                    $('#cat_results').html(data);
                $( "#sortable" ).sortable();
                $( "#sortable" ).disableSelection();
            }
        });

    });


    $('#save_order').click(function(){
        var idsInOrder = $("#sortable").sortable("toArray");
        var order_id = 1;
        jQuery.each( idsInOrder, function( i, val ) {
            var product_id = val;

            $.ajax({
                url: 'extra_function.php',
                data: {'sort_product':true,'product_id': product_id,'order_id':order_id},
                type: 'post',
                success: function(data)
                {
                    $('#msg_suc').show('slow');

                }
            });
             order_id++;

        });



    });

    function submit(){

    }



});

