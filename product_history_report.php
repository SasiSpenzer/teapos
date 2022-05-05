<?php
require_once 'common_header.php';
require_once 'inc/header.php';
// INCLUDE THE phpToPDF.php FILE
require_once 'api/phpToPDF/phpToPDF.php';

$pro_obj = new Product();
$cat_obj = new Category();

$list_cat = $cat_obj->list_category();
$cart_total = 0;
if(isset($_SESSION['shopping_cart'])) {
    if(!empty($_SESSION['shopping_cart'])) {
        foreach($_SESSION['shopping_cart'] as $each_item) {
            $cart_total += ($each_item['product_price'] * $each_item['product_qty']);
        }
    }
}

if(isset($_POST['find_product_history'])){
    
     $dateBegin = date('Y-m-d', strtotime($_POST['check_in']));
     $dateEnd = date('Y-m-d', strtotime($_POST['check_out']));

    if($dateBegin <= $dateEnd){
        
       $get_filtered_date = $pro_obj->get_product_history_by_date($dateBegin,$dateEnd);
       
       $_SESSION['dateBegin']=$dateBegin;
       $_SESSION['dateEnd']=$dateEnd;

    }else{
        $validation_error  = " Invalid date range";
    }
}
?>

        <link rel="stylesheet" href="asset/css/jquery-ui.css"> 
        <script src="asset/js/jquery-ui.js"></script>           
            <script type="text/javascript">
                $(function() {
                  $(".datepicker").datepicker({
                      dateFormat: "yy-mm-dd",
                      changeYear:true,
                      //minDate: 0,
                      showOn: "button",
                        buttonImage: "asset/images/btn_calendar.gif",
                        buttonImageOnly: true
                  });
                });
                
              
            </script>
            
            
    </head>
    <body>
       
        <?php require_once 'inc/navbar.php'; ?>
        
        <div class="container">
            <div class="row clearfix">
                <div class="col-md-12 column well">
                    
                    
                    <div class="container">
	<div class="row clearfix">
		<div class="col-md-12 column">
                    <?php if(isset($validation_error)){ ?>
                    <div class="product_message alert alert-danger alert-dismissible fade in" role="alert">
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                         <span aria-hidden="true">×</span>
                     </button>
                         <?php echo $validation_error ?>
                    </div>
                    <?php } ?>
                    
                    <h4>Quotes By Owner</h4>
                    <p>See how many quotes are owned by you for a period.</p>
                    
                    <div>
                      
                        <form role="form" action="" method="POST" class="form-inline">
				<div class="form-group">
					 <label for="exampleInputEmail1">Check In</label>
                                         <input type="text" id="from" name="check_in" placeholder="2014-07-02" class="dateselect datepicker" readonly="readonly" />
				</div>
				<div class="form-group">
					 <label for="exampleInputPassword1">Check Out</label>
                                         <input type="text" id="to" name="check_out" placeholder="2014-07-02" class="dateselect datepicker" readonly="readonly" />
				</div>
				 
                            <button type="submit" name="find_product_history" class="btn btn-primary">Find</button>
			</form>
                        
                          
                    </div>
                    
                    
		</div>
	</div>
</div>
                    
                    
                </div>
            </div>
        </div>
        <?php
          if(!empty($get_filtered_date)){
        ?>
        <div class="container">
                   <div class="row clearfix">
                       <div class="col-md-12 column well">
                           <form action="" method="POST">
                               <button type="submit" class="btn btn-info get_pdf_btn" name="get_pdf_btn" style="float: right">Get PDF</button>
                           
                           </form>
                                
                       </div>
                   </div>
        </div>
          <?php
          }
        ?>
      <?php  
      
      
      if(!empty($get_filtered_date)){

      
      $html = '
            <div class="container">
                   <div class="row clearfix">
                           <div class="col-md-12 column well">

                               <div class="col-md-12">
                                   <h3> Point of Sales</h3> 
                               </div>
 
                               <div class="col-md-9">
                                  <address> 
                                      <strong>Twitter, Inc.</strong><br />
                                      795 Folsom Ave, Suite 600<br />
                                      San Francisco, CA 94107<br /> 
                                      <abbr title="Phone">P:</abbr>
                                      (123) 456-7890
                                  </address>

                               </div>
                               <div class="col-md-3">';
                               $html .='  Date Range '.$dateBegin .' to '.$dateEnd;
                               $html .= '</div>


                               <div class="clearfix"></div>

                                   <div class="page-header">
                                       <h2 class="text-center">
                                                   Product History report   
                                           </h2>
                                   </div>
                                   <table class="table table-striped">
                                           <thead>
                                                   <tr>
                                                           <th>
                                                                   #
                                                           </th>
                                                           <th>
                                                                   Product name
                                                           </th>
                                                           <th>
                                                                   User name
                                                           </th>
                                                           <th>
                                                                   New quantity 
                                                           </th>
                                                           <th>
                                                                   Add type 
                                                           </th>
                                                           <th>
                                                                   Added date 
                                                           </th>
                                                   </tr>
                                           </thead>
                                           <tbody>';

                                                   $i=1;
                                                   foreach ($get_filtered_date as $filtered_date_values){ 
                                                    $date = date_create($filtered_date_values['added_date']);
                                                       
                                                   $html .= '<tr>
                                                           <td>'.$i++.'
                                                           </td>
                                                           <td>'.$filtered_date_values['product_name'].'
                                                           </td>
                                                           <td>'.$filtered_date_values['username'].'</td>
                                                           <td>'.$filtered_date_values['new_qty'].'</td>
                                                           <td>'.$filtered_date_values['add_type'].'</td>
                                                           <td>'.date_format($date,'Y-m-d').'</td>
                                                   </tr>';


                                                    } 



                                           $html .= '</tbody>
                                   </table>
                           </div>
                   </div>
           </div>  ';
           
     echo $html;
            
      }else{
         
      }
      
      
    
     
   
//    
 
    if(isset($_POST['get_pdf_btn'])){
        
         
       
        $dateBegin = $_SESSION['dateBegin'];
        $dateEnd  = $_SESSION['dateEnd'];
        $get_filtered_date = $pro_obj->get_product_history_by_date($dateBegin,$dateEnd);
        
        
        
        $html = '
            <div class="container">
                   <div class="row clearfix">
                           <div class="col-md-12 column well">

                               <div class="col-md-12">
                                   <h3> Point of Sales</h3> 
                               </div>
 
                               <div class="col-md-9">
                                  <address> 
                                      <strong>Twitter, Inc.</strong><br />
                                      795 Folsom Ave, Suite 600<br />
                                      San Francisco, CA 94107<br /> 
                                      <abbr title="Phone">P:</abbr>
                                      (123) 456-7890
                                  </address>

                               </div>
                                <div class="col-md-3">';
                               $html .='  Date Range '.$dateBegin .' to '.$dateEnd;
                               $html .= '</div>


                               <div class="clearfix"></div>

                                   <div class="page-header">
                                       <h2 class="text-center">
                                                   Product History report   
                                           </h2>
                                   </div>
                                   <table class="table table-striped">
                                           <thead>
                                                   <tr>
                                                           <th>
                                                                   #
                                                           </th>
                                                           <th>
                                                                   Product name
                                                           </th>
                                                           <th>
                                                                   User name
                                                           </th>
                                                           <th>
                                                                   New quantity 
                                                           </th>
                                                           <th>
                                                                   Add type 
                                                           </th>
                                                           <th>
                                                                   Added date 
                                                           </th>
                                                   </tr>
                                           </thead>
                                           <tbody>';

                                                   $i=1;
                                                   foreach ($get_filtered_date as $filtered_date_values){ 
                                                    $date = date_create($filtered_date_values['added_date']);
                                                       
                                                   $html .= '<tr>
                                                           <td>'.$i++.'
                                                           </td>
                                                           <td>'.$filtered_date_values['product_name'].'
                                                           </td>
                                                           <td>'.$filtered_date_values['username'].'</td>
                                                           <td>'.$filtered_date_values['new_qty'].'</td>
                                                           <td>'.$filtered_date_values['add_type'].'</td>
                                                           <td>'.date_format($date,'Y-m-d').'</td>
                                                   </tr>';


                                                    } 



                                           $html .= '</tbody>
                                   </table>
                           </div>
                   </div>
           </div>  ';
        
        $tz_object = new DateTimeZone('Asia/Colombo'); 
        $datetime = new DateTime();
        $datetime->setTimezone($tz_object);

        $current_date = $datetime->format('Y\-m\-d\ h:i:s');

        $file_name = 'product_history_'.$current_date.'.pdf';

        $pdf_options = array(
            "source_type" => 'html',
            "source" => $html,
            "action" => 'download',
            "page_size" => 'A5',
            //"save_directory" => 'uploads/pdf/product_history', 
            "file_name" =>$file_name );

        phptopdf($pdf_options);
        //remove file from local server
        unlink($file_name);
    }
    
 ?>

        
 
   <?php require_once 'inc/footer.php'; ?>