<?php
require_once 'common_header.php';
require_once 'inc/header.php';

$pro_obj = new Product();
$cat_obj = new Category();

if($_GET['pid']){
        
        $pid = $_GET['pid'];    
        $get_product_details = $pro_obj->list_priduct_by_product_id($pid);
     
    if(isset($get_product_details['category_id'])){
        $get_category_name = $cat_obj->get_cat_name_by_cat_id($get_product_details['category_id']);
    }
    
}else{
    header('Location: index.php');
}

 
?>

    </head>
    <body>


        <?php require_once 'inc/navbar.php'; ?>
 
         
        
        <div class="container">
            
<!--            <div class="row">

            </div>-->
            
            <div class="row">
                
                <div class="col-md-12">
                    <div class="well product-box">
                        
                         <?php if(!empty($get_product_details)){ ?>
                        <div class="row"> 
                        
                            <div class="col-sm-3 pro_cat_list">
                                            
                                                <div class="photo">
                         <img src="asset/images/product/<?php echo $get_product_details['feature_image']; ?>" class="img-responsive" alt="a">
                                                </div>
                                
                                                
                                             
                                        </div>
                            
                            <div class="col-sm-9 pro_cat_list">
                            
                                <div class="fright">
                                    <h1 class="title"><?php echo $get_product_details['product_name']; ?></h1>
                                
                                    <div class="manufacturer">
                                            <span class="bold">Category : </span><span class="bold">   <?php echo $get_product_details['category_id']; ?>  </span>
                                    </div>
                                      
                                    <div class="stock">
                                        <span class="bold">Availability:</span><i class="green">in stock</i>&nbsp;<b><?php echo $get_product_details['qty']; ?> &nbsp;item(s)</b>
                                    </div>
                                      
                                        
                                          
                                   
                                    <div class="stock">
                                        <span class="bold">Quantity:</span> 
                                        <select name="Quantity">
                                            <?php  for($i=1;$i <= $get_product_details['qty'];$i++){ ?>
                                            <option value="<?php   echo $i; ?>"><?php   echo $i; ?></option>
                                             <?php   } ?>
                                             
                                        </select>
                                    </div>
                                    
                                    <div class="price">
                                        <div class="product-price" id="productPrice9">
                                        <div class="PricevariantModification" style="display : none;">
                                            <span class="PricevariantModification"></span>
                                        </div>
                                            <div class="PricesalesPrice" style="display : block;">
                                                <span class="PricesalesPrice">$ <?php echo $get_product_details['price']; ?></span>
                                            </div>
                                        </div>
                                        
                                     </div>
                                    
                                    <div class="short_desc">
                                        <p>
                                            <?php echo $get_product_details['product_description']; ?>
                                        </p>
                                            
                                        
                                        
                                    </div>
                                    
                                    
                                    <div class="product-box2">
				
                                        <div class="addtocart-area2 proddet">

                                        <span class="addtocart_button2">
                                            <button type="submit" value="" title="Add to Cart" class="addtocart-button cart-click">Add to Cart<span>&nbsp;</span></button>
                                        </span>
                                            
                                        <div class="clear"></div>
                                        </div>

                                        <div class="clear"></div>

                                    </div>
                                    
                                    
                                    
                                    
                                </div>
                                
                                
<!--                                <div class="panel panel-default">
                                <div class="panel-heading">Panel heading without title</div>
                                <div class="panel-body">
                                  
                                    <ul>
                                        <li>Test Name : </li>
                                    </ul>
                                    
                                </div>
                              </div>-->
                                
                                
<!--                                <div class="info">
                                                    <div class="row">
                                                        <div class="price col-md-6">
                                                            <h5>Lorem Ipsum test product details <?php //echo $list_catergory_details_value['product_name'];  ?> </h5>
                                                            <h5 class="price-text-color">
                                                                $ 100.43<?php //echo $list_catergory_details_value['price'];  ?> </h5>
                                                        </div>
                                                        <div class="rating hidden-sm col-md-6">
                                                            <i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                            </i><i class="price-text-color fa fa-star"></i><i class="price-text-color fa fa-star">
                                                            </i><i class="fa fa-star"></i>
                                                        </div>
                                                    </div>
                                                    <div class="separator clear-left">
                                                        <p class="btn-add">
                                                            <i class="fa fa-shopping-cart"></i><a href="http://www.jquery2dotnet.com" class="hidden-sm">Add to cart</a></p>
                                                        <p class="btn-details">
                                                            <i class="fa fa-list"></i><a href="http://www.jquery2dotnet.com" class="hidden-sm">More details</a></p>
                                                    </div>
                                                    <div class="clearfix">
                                                    </div>
                                                </div>-->
                                
                            </div>
                            
                            
                        </div>
                         <?php }else{  ?>
                             
                             <div class="alert alert-danger alert-dismissible fade in" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <strong>Opss..!</strong>No product Avaliable ...
                              </div>
                           
                       <?php  } ?>
                    </div>
                     
                </div>
            </div>
        </div>

   
        
        <?php require_once 'inc/footer.php'; ?>