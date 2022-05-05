<?php

    if(empty($_SESSION['invo_date'])){ echo date('M,d,Y');} else { echo $_SESSION['invo_date'] ;}

    include_once("common_header.php");

    if(isset($_POST['skipReport'])){
        session_destroy();
        header('Location:login.php');
        exit;
    }

    $cashObj = new CashFeed();
    $GetCashSales = $cashObj->CasHsalesData();
    $cash_sales = $GetCashSales[0]['Sales'];
    if($GetCashSales[0]['Date'] != date('Y-m-d')){
        $cash_sales = '00.00';
    }

    $GetCardSales = $cashObj->CardsalesData();
    $card_sales = $GetCardSales[0]['Sales'];
    if($GetCardSales[0]['Date'] != date('Y-m-d')){
        $card_sales = '00.00';
    }


    if(isset($_POST['getReport'])){

        $product_obj = new Product();
        $Today_Sales = $product_obj->GetTodaySalesPosReport();
        $order_obj = new Order();
        $date_today = date('y-m-d');
        $daily_sales = $order_obj->get_daily_sales($date_today);
        $total_of_the_day =  $daily_sales['Total'];
//        echo "<pre>";
//        print_r($Today_Sales);
//        exit;
        $printer = new Escpos();
        $printer -> initialize();
        $printer->setJustification(1);
        $printer->setFont(10);
        $printer->setEmphasis(true);

        $printer -> text("   THE WITHERED LEAVES TEA AND SPICES COMPANY \n");
        $printer->setEmphasis(false);
        $printer -> text("   OLD DUTCH HOSPITAL\n");
        $printer -> text("   GALLE\n");
        $printer -> text("   +94 912231848\n");
        $printer->setJustification(0);
        $printer->feed(2);
        $printer->setFont(0);
        $date = date('y-m-d');
        $printer -> text("   DAILY SALES SUMMERY ");
        $printer->setJustification(2);
        $printer -> text("          DATE: $date ");
        $printer -> feed();

        $printer -> text("  ----------------------------------------------\n");
        $printer -> feed();
        $r_total = 0;

        $printer -> text("              Product      QTY   Value");
        $printer -> feed();
        foreach($Today_Sales as $each_sale) {
            $product_name = $each_sale['product_name'] ;
            $order_total = $each_sale['order_total'];
            $no_of_products = $each_sale['no_of_products'];
            $is_loos = $each_sale['is_loose'];
            //$total_sales = $each_sale['total_sales'];
            if($is_loos == 'F'){
                $type = '';
            }else{
                $type = 'Kg';
            }

            $printer -> text("      $product_name");
            $printer -> feed();
            $printer -> text("                            $no_of_products.$type   $order_total");
            $printer -> feed();

        }
        $printer -> text("  ----------------------------------------------\n");

        $printer -> text("   TOTAL                       $total_of_the_day\n");

        $printer->setEmphasis(false);

        $printer -> text("  ----------------------------------------------");
        $printer -> feed();
        date_default_timezone_set('Asia/Kolkata');
        $print_date = date('d-F-Y H:i:s');
        $printer -> text("   DATE :$print_date ");
        $printer -> feed();
        $printer -> text("  ----------------------------------------------\n");
        $printer->setJustification(1);
        $printer -> text("    WWW.WITHEREDLEAVES.COM \n");
        $printer -> cut();

        /**
         * Sending Email process begin
         */
        $LowProducts = $product_obj->getLowProducts();

        $lowProductArray = array();
        $totalBalance = 0;
        if(!empty($LowProducts)){
            $count = 0;
            foreach($LowProducts as $each_product){
                $lowProductArray[$count]['product_name'] = $each_product['product_name'];
                $lowProductArray[$count]['price'] = $each_product['price'];
                if($each_product['is_loose'] == 'T'){
                    $lowProductArray[$count]['RequestedQty'] = '1 KG';
                    $lowProductArray[$count]['Total'] = $each_product['price'] ;
                    $totalBalance +=  $lowProductArray[$count]['Total'];
                }else{
                    $lowProductArray[$count]['RequestedQty'] = '10 Items';
                    $lowProductArray[$count]['Total'] = $each_product['price'] * 10 ;
                    $totalBalance +=  $lowProductArray['Total'];
                }
                $count++;
            }

            require 'PHPMailer/PHPMailerAutoload.php';

            $mail = new PHPMailer;

            $mail->isSMTP();                                   // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com';                    // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                            // Enable SMTP authentication
            $mail->Username = 'galleteashop@gmail.com';          // SMTP username
            $mail->Password = 'richard@123'; // SMTP password
            $mail->SMTPSecure = 'tls';                         // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                 // TCP port to connect to

            $mail->setFrom('galleteashop@gmail.com', 'Galle Tea shop');
            $mail->addReplyTo('galleteashop@gmail.com', 'Galle Tea Shop');
            $mail->addAddress('dineshkarunaratne@hotmail.com');   // Add a recipient
            //$mail->addAddress('sasi.spenzer@gmail.com');   // Add a recipient
            //$mail->addCC('cc@example.com');
            $mail->addBCC('sasi.spenzer@gmail.com');

            $mail->isHTML(true);  // Set email format to HTML

            $bodyContent = '<h3>Hi Richard,Daily Product Quenity Summery Date :-'.date('Y-m-d').'</h3>';
            $bodyContent .= '<p>This is an auto-genarated email by the Galle Tea shop.</p>';
            $bodyContent .= '<p>Followig product/s  quentityes are running low.</p>';
            $bodyContent .= "<table border='1' cellpadding='4' cellspacing='3' align='center'>";
            $bodyContent .= "<tr>";
            $bodyContent .= "<td align='center'>Product Name</td>";
            $bodyContent .= "<td align='center'>Requested Quentity</td>";
            $bodyContent .= "<td align='center'>Value</td>";
            $bodyContent .= "</tr>";

            foreach($lowProductArray as $each_low_product){

                $bodyContent .= "<tr>";
                $bodyContent .= "<td align='center'>".$each_low_product['product_name']."</td>";
                $bodyContent .= "<td align='center'>".$each_low_product['RequestedQty']."</td>";
                $bodyContent .= "<td align='center'>".$each_low_product['Total']."</td>";

                $bodyContent .= "</tr>";
            }
            $bodyContent .= "<tr>";
            $bodyContent .= "<td colspan='2' align='center'>Total</td>";
            $bodyContent .= "<td align='center'>".$totalBalance."</td>";


            $bodyContent .= "</tr>";
            $bodyContent .= "</table>";
            $bodyContent .= '<p align="center">This is an auto-genarated email by the Galle Tea shop. Developed By <b>Verdict deal Developers 2016</b> &copy;</p>';


            $mail->Subject = 'Email from Galle Tea Shop';
            $mail->Body    = $bodyContent;

            if(!$mail->send()) {
                echo 'Message could not be sent.';
                echo 'Mailer Error: ' . $mail->ErrorInfo;
            } else {
                echo 'Message has been sent';
            }

        }
        else{

        }
        /**
         * Making an array for send to the API ( accounting API )
         */

        $data = array(
            'Amount' => $total_of_the_day,
            'SID'=>'67',
            'PTID'=>'1',
            'is_cleared'=>'1',
            'credit_debit'=>'1',
            'cash_online'=>'1',
            'is_po'=>'0',
            'Date'=>date('Y-m-d')

        );

        $data_string = json_encode($data);

        $curl = curl_init('http://accounting-api.wealthplus.lk/api/pos');

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'X-API-KEY: '.'e10adc3949ba59abbe56e057f20f883e',
                'Content-Length: ' . strlen($data_string))
        );

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  // Make it so the data coming back is put into a string
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);  // Insert the data

        // Send the request
        $result = curl_exec($curl);

        // Free up the resources $curl is using
        curl_close($curl);

        /**
         * Loging out Process
         */

        session_destroy();
        header("Location:login.php");
    }



?>


<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tea Shop Galle </title>
</head>

<body>

<style>
    body {
        font-family: 'Open Sans', sans-serif!important;
    }
    .alert-box{
        width:600px;
        top:50%;
        left: 50%;
        transform: translate3d(-50%,-50%, 0);
        position: absolute;

    }
    .content{
        border:1px solid #848484;
        padding:20px 50px 65px 50px;
        border-top:none;
    }
    .head-title{
        border:1px  solid #848484;
        width:100%;
        background:#ebebe4;
        padding:10px 0;
        font-size: 20px;
        text-align: center;
        font-weight: 300;
        background: grey;
        color:#fff;

    }
    .btn{
        width:30%;
        border:1px solid #000;
        color:#fff;
        font-weight:bold;
        text-transform:uppercase;
        float:left;
        padding:10px 0;
        background:#316896;
        margin-right:20px;
    }
</style>
<form method="post" action="">
<div class="alert-box">
    <div class="head-title">Is This is the End of the Day Buddy ?</div>
    <div class="content">
        <p>It was a busy day for Sure.Don't you like to have a look on today's sales Report ?
        </p>
        <button name="getReport" type="submit" class="btn">Print Report</button>
        <button name="skipReport" type="submit" class="btn">Skip the  Report</button>
        <br><br><br>
        <table class="table">
            <tr>
                <th width="200">Sales Type</th>
                <th width="200">Amount (LKR)</th>
            </tr>
            <tr>
                <td align="center" width="200">Cash Sales</td>
                <td width="200"><?php echo $cash_sales; ?></td>
            </tr>
            <tr>
                <td align="center" width="200">Card Sales</td>
                <td width="200"><?php echo $card_sales ?></td>
            </tr>
        </table>

    </div>

</form>




</div>
</body>
</html>
