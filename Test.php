<?php
session_start();
date_default_timezone_set('Asia/Colombo');
 

require_once  '/class/PHPExcel.php';
require_once  '/class/Order.php';
require_once  '/class/DB.php';
require_once  '/class/Extra.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

$objPHPExcel = new PHPExcel();
$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);

if(isset($_GET['sale_rno_report_monthly']) && $_GET['sale_rno_report_monthly'] == 'true') {

    $print_year = $_GET['year'];
    $print_month = $_GET['month'];
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Monthly report : ".$print_year." - ".$print_month);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Payment ID");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "Order ID)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Receipt number");
    $objPHPExcel->getActiveSheet()->setCellValue('D3', "Payment Date");
    $objPHPExcel->getActiveSheet()->setCellValue('E3', "Order Total");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
     
    $order_obj = new Order();

	$month_sales_rno_sales_rep = $order_obj->month_sales_rno_report($print_month,$print_year);

    $report_detail =  $month_sales_rno_sales_rep ;
	
    foreach($report_detail as $each_month_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_month_record['payment_id'])
            ->setCellValue('B' . $z,  $each_month_record['order_id'])
            ->setCellValue('C' . $z,  $each_month_record['receipt_number'])
            ->setCellValue('D' . $z,  $each_month_record['payment_date'])
            ->setCellValue('E' . $z,  $each_month_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/sales_report_monthly_.xls');
    header("Location:sales_report/sales_report_monthly_.xls");
    //header("Location:index.php");
}

if(isset($_GET['sale_report_monthly']) && $_GET['sale_report_monthly'] == 'true') {

        $print_year = $_GET['year'];
        $print_month = $_GET['month'];
        $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->setCellValue('A1', "Monthly report : ".$print_year." - ".$print_month);
        $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
        $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
        $objPHPExcel->getActiveSheet()->setCellValue('C3', "Amount");

        $objPHPExcel->setActiveSheetIndex(0);
        // Add data
        $z = 4;
        $report_detail = $_SESSION['sales_data'];
        foreach($report_detail as $each_month_record) {
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_month_record['product_name'])
                ->setCellValue('B' . $z,  $each_month_record['qty'])
                ->setCellValue('C' . $z,  $each_month_record['order_total']);

            $z++;
        }

        $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('sales_report/sales_report_monthly_.xls');
        header("Location:sales_report/sales_report_monthly_.xls");
        //header("Location:index.php");
}

if(isset($_GET['product_inventory_live'])){

    $report_date =  date("F j, Y");

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 3;

    $filename = 'sales_report/Inventory_Report_Live.xls';
    $fh = fopen($filename, 'a');

    fclose($fh);
    if (file_exists($filename)) {
        unlink($filename);
    } else {

    }

    $products_report = $_SESSION['product_inventory_live'];
	$objPHPExcel->getActiveSheet()->getStyle("A1:G1")->getFont()->setSize(25);
    $objPHPExcel->getActiveSheet()->setCellValue('A1','Product Inventory Report-'.$report_date );
    $current_category_name = '';
    $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $products_report[0]['category_name']);
    $objPHPExcel->getActiveSheet()->getStyle("A".$z)->getFont()->setSize(16)
    ->setBold(true);
    $z++;
    $j = 1;

    $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, 'No')
        ->setCellValue('B' . $z,  'Tea Name')
        ->setCellValue('C' . $z,  'Stock In Hand');
    $objPHPExcel->getActiveSheet()->getStyle("A".$z)->getFont()->setSize(12)
        ->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("B".$z)->getFont()->setSize(12)
                ->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("C".$z)->getFont()->setSize(12)
                ->setBold(true);

    $z++;

    foreach($products_report as $each_product) {
        if($current_category_name != '' && $each_product['category_name'] != $current_category_name) {
            $j=1;
            $z++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_product['category_name']);
            $objPHPExcel->getActiveSheet()->getStyle("A".$z)->getFont()->setSize(16)
                ->setBold(true);
            $z++;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, 'No')
                ->setCellValue('B' . $z,  'Tea Name')
                ->setCellValue('C' . $z,  'Stock In Hand');

            $objPHPExcel->getActiveSheet()->getStyle("A".$z)->getFont()->setSize(12)
            ->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle("B".$z)->getFont()->setSize(12)
                ->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("C".$z)->getFont()->setSize(12)
                ->setBold(true);
            $z++;
        }
        $current_category_name = $each_product['category_name'];
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $j)
            ->setCellValue('B' . $z,  $each_product['product_name'])
            ->setCellValue('C' . $z,  $each_product['qty']);


        $j++;
        $z++;
    }

    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {

        $objPHPExcel->setActiveSheetIndex($objPHPExcel->getIndex($worksheet));

        $sheet = $objPHPExcel->getActiveSheet();
        $cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(true);
        /* @var PHPExcel_Cell $cell */
        foreach ($cellIterator as $cell) {
            $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
        }
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

   /* $reader = new PHPExcel_Reader_Excel2007;
    $workbook =  $reader->load("document.xlsx");
    $workbook->-getActiveSheet()->getSecurity()->setWorkbookPassword("your password");
*/

    $objWriter->save('sales_report/Inventory_Report_Live.xls');

    header("Location:sales_report/Inventory_Report_Live.xls");

    //header("Location:index.php");
}

if(isset($_GET['today_cash_start'])){
    $start_cash = $_GET['today_cash_start'];
    $end_cash = $_GET['today_end_cash'];
    $date = date('Y/m/d');
    $report_date    = date('l jS \of F Y h:i:s A');
    $sale = $_GET['total_sale'];
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Daily Cash Report of". $report_date);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Date");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "Start Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "End Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('D3', "Total Sales");

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A4', $date);
    $objPHPExcel->getActiveSheet()->setCellValue('B4', $start_cash);
    $objPHPExcel->getActiveSheet()->setCellValue('C4', $end_cash);
    $objPHPExcel->getActiveSheet()->setCellValue('D4', $sale);

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/Daily Cash Report of.xls');
    header("Location:sales_report/Daily Cash Report of.xls");
    //header("Location:index.php");
}


if(isset($_GET['week_rep'])){

    $report_date    = date('l jS \of F Y h:i:s A');

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Weekly Cash Report of". $report_date);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Date");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "Start Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "End Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('D3', "Total Sales");
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $report_detail = $_SESSION['week_start_cash'];

    foreach($report_detail as $each_week_record_key=>$each_week_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_week_record_key)
            ->setCellValue('B' . $z,  $each_week_record[0]['amount'])
            ->setCellValue('C' . $z,  $each_week_record[1]['amount'])
            ->setCellValue('D' . $z,  $each_week_record[2]['amount']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/weekly_cash_report_.xls');
    header("Location:sales_report/weekly_cash_report.xls");
    // header("Location:index.php");
}

if(isset($_GET['month_report'])){

     $report_date    = date('l jS \of F Y h:i:s A');

    $report_year = $_GET['year'];
    $report_month = $_GET['month'];
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Monthly Cash Report of ". $report_year." - ".$report_month);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Date");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "Start Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "End Cash");
    $objPHPExcel->getActiveSheet()->setCellValue('D3', "Total Sales");
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $report_detail = $_SESSION['m_cash'];

    foreach($report_detail as $each_week_record_key=>$each_week_record) {
        $start_cash = 0;
        $end_cash = 0;
        $total_sales = 0;
        if(array_key_exists('0',$each_week_record)) {
            if(array_key_exists('amount',$each_week_record[0])) {
                $start_cash = $each_week_record[0]['amount'];
            }
        }
        if(array_key_exists('1',$each_week_record)) {
            if(array_key_exists('amount',$each_week_record[1])) {
                $end_cash = $each_week_record[1]['amount'];
            }
        }
        if(array_key_exists('2',$each_week_record)) {
            if(array_key_exists('amount',$each_week_record[2])) {
                $total_sales = $each_week_record[2]['amount'];
            }
        }
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_week_record_key)
            ->setCellValue('B' . $z,  $start_cash)
            ->setCellValue('C' . $z,  $end_cash)
            ->setCellValue('D' . $z,  $total_sales);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/monthly_cash_report_.xls');
    header("Location:sales_report/monthly_cash_report_.xls");
    //header("Location:index.php");

}
if(isset($_GET['sale_report_today'])){


    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Daily report : ".date('Y/m/d'));
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Amount");
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $report_detail = $_SESSION['sales_data'];
    foreach($report_detail as $each_month_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_month_record['product_name'])
            ->setCellValue('B' . $z,  $each_month_record['qty'])
            ->setCellValue('C' . $z,  $each_month_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/sales_report_.xls');
    header("Location:sales_report/sales_report_.xls");
    //header("Location:index.php");


}

if(isset($_GET['week_sale_report'])){

    $report_date    = date('l jS \of F Y h:i:s A');
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Weekly Sale report : ".$report_date);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Amount");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $report_detail = $_SESSION['weekly_sale_data'];
    foreach($report_detail as $each_month_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_month_record['product_name'])
            ->setCellValue('B' . $z,  $each_month_record['qty'])
            ->setCellValue('C' . $z,  $each_month_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/sales_report_weekly_.xls');
    header("Location:sales_report/sales_report_weekly_.xls");
    //header("Location:index.php");

}

if(isset($_GET['product_inventory'])){

    $report_date =  date("F j, Y, g:i a");

    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Product Inventory Report : ".$report_date);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product ID");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "QTY(items/Kg)");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $products_report = $_SESSION['product_inventory'];
    foreach($products_report as $each_product) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_product['product_id'])
            ->setCellValue('B' . $z,  $each_product['product_name'])
            ->setCellValue('C' . $z,  $each_product['qty']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/Inventory_Report.xls');
    header("Location:sales_report/Inventory_Report.xls");
    //header("Location:index.php");
}

/*  product sales reports starts here ( spenzer )*/



if(isset($_GET['product_sale_report_week'])){


    $product_array = json_decode($_SESSION['product_sale_report_week'],true);
    $name = $_GET['name'];
    $report_date =  date('Y-m-d');

    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Product Sales Report - .$name. : ".date('Y/m/d'));
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Order ID");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Date");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;

    foreach($product_array as $each_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_record['order_id'])
            ->setCellValue('B' . $z,  $each_record['no_of_products'])
            ->setCellValue('C' . $z,  $each_record['order_date']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/product_sales_report_.xls');
    header("Location:sales_report/product_sales_report_.xls");
    //header("Location:index.php");







}

if(isset($_GET['product_sale_report_month'])){


    $product_array = json_decode($_SESSION['product_sale_report_month'],true);
    $name = $_GET['name'];
    $report_date =  date('Y-m-d');
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Product Sales Report - .$name. : ".date('Y/m/d'));
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Order ID");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Date");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;

    foreach($product_array as $each_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_record['order_id'])
            ->setCellValue('B' . $z,  $each_record['no_of_products'])
            ->setCellValue('C' . $z,  $each_record['order_date']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/product_sales_report_.xls');
    header("Location:sales_report/product_sales_report_.xls");
    //header("Location:index.php");

}

if(isset($_GET['per_user_week_sale_report'])){


    $product_array = json_decode($_SESSION['per_user_week_sales'],true);

    $Total = $_GET['Total'];
    $name = $_GET['name'];

    $report_date =  date('Y-m-d');
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Product Sales Report By User - .$name. : ".date('Y/m/d'));
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Order Total");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;

    foreach($product_array as $each_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_record['product_name'])
            ->setCellValue('B' . $z,  $each_record['qty'])
            ->setCellValue('C' . $z,  $each_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/product_user_sales_report_.xls');
    header("Location:sales_report/product_user_sales_report_.xls");
    //header("Location:index.php");


}
if(isset($_GET['per_user_month_sale_report'])){

    session_start();
    $product_array = json_decode($_SESSION['per_user_month_sales'],true);
    $Total = $_GET['Total'];
    $name = $_GET['name'];

    $report_date =  date('Y-m-d');
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Product Sales Report By User - .$name. : ".date('Y/m/d'));
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Order Total");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;

    foreach($product_array as $each_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_record['product_name'])
            ->setCellValue('B' . $z,  $each_record['qty'])
            ->setCellValue('C' . $z,  $each_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/product_user_month__sales_report_.xls');
    header("Location:sales_report/product_user_month__sales_report_.xls");
    //header("Location:index.php");



}




if(isset($_GET['product_sale_report_today'])){

    $name = $_GET['name'];
    $report_date =  date('Y-m-d');


    $report_date    = date('l jS \of F Y h:i:s A');
    $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()
        ->setWidth("30");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setCellValue('A1', "Sales  report of ".$name." ".$report_date);
    $objPHPExcel->getActiveSheet()->setCellValue('A3', "Product Name");
    $objPHPExcel->getActiveSheet()->setCellValue('B3', "QTY(items/Kg)");
    $objPHPExcel->getActiveSheet()->setCellValue('C3', "Amount");

    $objPHPExcel->setActiveSheetIndex(0);
    // Add data
    $z = 4;
    $report_detail = $_SESSION['weekly_sale_data'];
    foreach($report_detail as $each_month_record) {
        $objPHPExcel->getActiveSheet()->setCellValue('A' . $z, $each_month_record['product_name'])
            ->setCellValue('B' . $z,  $each_month_record['qty'])
            ->setCellValue('C' . $z,  $each_month_record['order_total']);

        $z++;
    }

    $date_string = date('Y').'_'.date('m').'_'.date('d').'_'.date('h').'_'.date('i');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('sales_report/sales_report_today_.xls');
    header("Location:sales_report/sales_report_today_.xls");
    //header("Location:index.php");



}