<?php


include_once("common_header.php");
 if($_SESSION['user_level'] != 1){
     header('Location: index.php');
}
$product_obj = new Product();
$product_list = $product_obj->list_product_inventory_live();
$product_value = $product_obj->product_value();
//
//echo "<pre>";
//print_r($product_value);
//exit;
$mainArray = array();
foreach ($product_list as $each){

    if(!isset($mainArray[$each['category_name']])){

        $mainArray[$each['category_name']] =  array() ;
    }

    $tempArray = array();
    $tempArray['product_id'] = $each['product_id'];
    $tempArray['product_name'] = $each['product_name'];
    $tempArray['qty'] = $each['qty'];
    $tempArray['price'] = $each['price'];
    array_push($mainArray[$each['category_name']],$tempArray);

}

//echo "<pre>";
//print_r($mainArray);
//exit;

?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body {font-family: Arial, Helvetica, sans-serif;}

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<link href="css/bootstrap.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-2.2.3.min.js"></script>
<link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">
<!--<script src="https://code.jquery.com/jquery-1.12.4.js"></script>-->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>-->
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>-->
<!--<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>-->
<!--<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>-->


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css">
<script>


</script>
<script type="text/javascript">
    function ActiveText(id){
        var id = id;
        $("#amount_"+id).attr("readonly", false);
        $("#done_"+id).show();
    }

    function SaveNewAmount(id){
        var id = id;
        var amount = $("#amount_"+id).val();

        $.ajax({
            url: "extra_function.php",
            type: "POST",
            cache: false,
            async: false,
            data: {update_amount: true,product_id:id,amount:amount},
            success: function (data) {

                location.reload();
            }
        });

    }
    function makeEdit(id){

        $("#exampleModal").modal('show');
    }
</script>
 <body>
 <div>
     <h1 style="color: deepskyblue" align="center">Central Repository !</h1><br><br>
     <h3 style="margin-left: 20px;">Total Value of the stock is : <?php echo number_format($product_value['grand_total']) ; ?></h3>
    <div align="center"><button class="btn btn-primary" style="padding: 15px 32px;text-align: center;text-decoration: none;display: inline-block;font-size: 16px;" onclick="javascript:window.print()">Print Inventory</button></div> <br>
     <form id="hidden_form" enctype="multipart/form-data" method="post" action="">
         <table width="300px;!important"  class="table table-bordered table-payment">
             <thead>
                     <tr>
                         <th>NO</th>
                         <th>Category Name</th>
                         <th>Product name</th>
                         <th>Quantity</th>
                         <th>Price ( 1KG / Item )</th>
                         <th>Value</th>
                         <th>Action</th>
                         <th></th>
                     </tr>
                     </thead>
                     <tbody>
                    <?php
                    $num = 1; $cat = 1;
                    foreach ($mainArray as $key=>$each_product){ ?>
                     <tr>

                         <th style="background: lightcoral" colspan="8"><h1 align="center"><?php echo $cat .'-'. $key; ?></h1></th>

                     </tr>

                        <?php foreach ($each_product as $each_array){ ?>
                            <tr>
                                <th style="background: seashell;opacity: 0.4;"><?php echo $num ; ?></th>
                                <th style="background: seashell;opacity: 0.4;"><?php echo $key ; ?></th>
                                <th style="color:sienna"><?php echo $each_array['product_name'] ; ?></th>
                                <th style="background: seashell;opacity: 0.4;">
                                    <input ondblclick="ActiveText(<?php echo $each_array['product_id']; ?>)" id="amount_<?php echo $each_array['product_id']; ?>" type="text" readonly value="<?php echo $each_array['qty']; ?>">
                                    <input onclick="SaveNewAmount(<?php echo $each_array['product_id']; ?>)" id="done_<?php echo $each_array['product_id']; ?>" style="display: none" class="btn-primary form-control" type="button" value="Done">
                                </th>
                                <th style="background: seashell;opacity: 0.4;"><?php echo $each_array['price'] ; ?></th>
                                <th style="background: seashell;opacity: 0.4;"><?php echo number_format($each_array['price'] * $each_array['qty'] ) ; ?></th>
                                <th style="background: seashell;opacity: 0.4;"><a onclick="ActiveText(<?php echo $each_array['product_id']; ?>)" class="btn btn-primary" href="#">Edit</a> </th>
                                <th style="background: seashell;opacity: 0.4;"></th>
                            </tr>

                        <?php $num++; } ?>

                    <?php $cat++; } ?>
             </tbody>
         </table>
 </div>



 <div id="myModal" class="modal">

     <!-- Modal content -->
     <div class="modal-content">
         <span class="close">&times;</span>
         <p>Some text in the Modal..</p>
     </div>

 </div>
 </body>