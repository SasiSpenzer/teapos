<?php
session_start();
ob_start();

function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}

if(isset($_POST['product_add'])){

    $ProductName = trim($_POST['product_name']);
    $product_qty = $_POST['product_qty'];

    $name_parts = explode("-", $ProductName);
    if(!empty($name_parts)){
        if (is_numeric($name_parts[0])) {
            $product_name = $name_parts[1];
            $product_id = $name_parts[0];
        }
    }



    $product_obj = new Product();


    $product_data = $product_obj->list_priduct_by_product_id($product_id);
    $current_product_qty = $product_data['qty'] ;

    $new_qty = $product_qty + $current_product_qty ;
    $data_array = array();
    $data_array['qty'] = $new_qty ;

    $product_obj->update_new_product($data_array,$product_id);
    $product_history = array(
        'user_id'=>$_SESSION['user_id'],
        'product_id'=>$product_id,
        'new_qty'=>$product_qty,
        'add_type'=>'+',
        'added_date'=> Date('Y-m-d h:i:s')
    );
    if($product_obj->add_product_history($product_history)){
        $success_msg = "Product Added Successfully !";
    }


}


?>
<link href="css/bootstrap.css" rel="stylesheet">
<link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="extra_css/asset/js/bootbox.min.js"></script>
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script type="text/javascript">





        $( function() {
            var availableTags = '';
            $.ajax({
                url: "extra_function.php",
                type: "POST",
                cache: false,
                async:false,
                dataType: 'json',
                data: {get_products:true},
                success: function(product_data){
                    availableTags = product_data ;
                }
            });
            $("#tags").autocomplete({
                source: availableTags
            });
        } );







</script>

<style>





    body{
        background:#e5e5e5;
    }
    .scan-add-callout{
        background:grey;
        color:#fff;
        padding:30px !important;
        position: fixed;
        top: 50%;
        left: 50%;
        /* bring your own prefixes */
        transform: translate(-50%, -50%);
        margin-top:0 !important;
    }
    .scan-add-callout p{
        padding:20px 0;
        font-size:15px;
    }
    .scan-add-callout input, .scan-add-callout select{
        color:#000;
    }
    .scan-add-callout h3{
        text-align:center;
        border-bottom:1px solid #fff;
        padding-bottom:10px;
    }
    .btn-scan-add{
        width:90%;
        margin-top:20px;
        margin-left:12px;
    }

</style>
</head>
<body>
<form method="post" action="">
    <div class="scan-add-callout">
        <h3>Products Add</h3>
        <?php if(isset($success_msg)) { ?><p><?php echo $success_msg ; ?></p> <?php  } ?>
        <p>
            <span class="col-sm-4">Product Quantity</span>
            <select name="product_qty" class="col-sm-6">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
                <option value="7">7</option>
                <option value="8">8</option>
                <option value="9">9</option>
                <option value="10">10</option>
                <option value="11">11</option>
                <option value="12">12</option>
                <option value="13">13</option>
                <option value="14">14</option>
                <option value="15">15</option>
                <option value="16">16</option>
                <option value="17">17</option>
                <option value="18">18</option>
                <option value="19">19</option>
                <option value="20">20</option>
                <option value="21">21</option>
                <option value="22">22</option>
                <option value="23">23</option>
                <option value="24">24</option>
                <option value="25">25</option>
            </select>
        </p>
        <p>
            <span class="col-sm-4"> Product Name</span><input id="tags" name="product_name" type="text" class="col-sm-6"></p>
        <input  value="Add" type="submit" name="product_add" class="btn btn-primary btn-scan-add">
        <a href="index.php"> <input value="Back" type="button" name="back" class="btn btn-primary btn-scan-add"></a>
    </div>
</form>
</body>
