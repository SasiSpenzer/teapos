<?php  
session_start();
ob_start();


function __autoload($class_name) {
    require_once 'class/'.$class_name . '.php';
}
?>
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="extra_css/asset/custom/css/custom.css" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="extra_css/asset/js/bootbox.min.js"></script>
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
          
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
  <div class="scan-add-callout">
  	<h3>Scan Add</h3>
    <p>
        <span class="col-sm-4">Product Quantity</span>
        <select class="col-sm-6">
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
            <option>7</option>
            <option>8</option>
            <option>9</option>
            <option>10</option>
            <option>11</option>
            <option>12</option>
            <option>13</option>
            <option>14</option>
            <option>15</option>
            <option>16</option>
            <option>17</option>
            <option>18</option>
            <option>19</option>
            <option>20</option>
            <option>21</option>
            <option>22</option>
            <option>23</option>
            <option>24</option>
            <option>25</option>
        </select>
    </p>
    <p>
        <span class="col-sm-4"> Product Barcode</span><input type="text" class="col-sm-6"></input></p>
        <button class="btn btn-primary btn-scan-add">Add</button>
  </div>
   </body>
