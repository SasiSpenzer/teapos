<?php


class Order {

    public  function add_order($order_data){
        $db_obj = new DB();
        $table = 'orders';
        $db_obj->insert($table, $order_data);
        $id = $db_obj->getLastInsertId();
        return $id ;
    }
    public function add_order_detail($order_details) {
        $db_obj = new DB();
        $table = 'order_details';
        $db_obj->insert($table, $order_details);

    }
    public function get_daily_sales($date) {

        $db_obj = new DB();
       $user_query = "SELECT SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                    DATE(order_date) = '".$date."'";

        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getRow($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }

    }
    public function getSalesSum($month,$year){

        $db_obj = new DB();
        $user_query = " SELECT orders.order_id,orders.order_date,orders.order_total,order_details.product_id,order_details.no_of_products,product.product_name
                        FROM orders INNER JOIN order_details ON
                        orders.order_id = order_details.order_id
                        INNER JOIN product ON
                        order_details.product_id = product.product_id
                        WHERE MONTH(orders.order_date) = '".$month."' AND YEAR(orders.order_date) = '".$year."' ORDER BY MONTH(orders.order_date) DESC";


        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {

            $sales_details_raw = $db_obj->getResults($user_query);
            $extra_obj = new Extra();
            $sales_details = $extra_obj->objectToArray($sales_details_raw);
            return $sales_details;
        } else {
            return false;
        }
    }

    public function get_sales_by_type($date){
        $db_obj = new DB();
        $receipts_query = "SELECT  (SELECT SUM(received_amount - balance_amount )FROM payments WHERE DATE(payment_date) = '".$date."' AND  payment_type = 'Cash') AS CashSales,
                           (SELECT SUM(received_amount - balance_amount )FROM payments WHERE DATE(payment_date) = '".$date."' AND  payment_type = 'CreditCard') AS CardSales,
                            FROM
                                    payments ";

        $db_obj->query($receipts_query);
        if($db_obj->rowCount() > 0) {
            $order_details_by_id = $db_obj->getRow($receipts_query);
            $extra_obj = new Extra();
            $order_details = $extra_obj->objectToArray($order_details_by_id);
            return $order_details;
        } else {
            return false;
        }
    }








    public function get_order_id_by_receipts($receiptsId){
        $db_obj = new DB();
        $receipts_query = "SELECT order_id
                                FROM
                                     payments
                                WHERE
                                    receipt_number = '".$receiptsId."'";


        $db_obj->query($receipts_query);
        if($db_obj->rowCount() > 0) {
            $order_details_by_id = $db_obj->getRow($receipts_query);
            $extra_obj = new Extra();
            $order_details = $extra_obj->objectToArray($order_details_by_id);
            return $order_details;
        } else {
            return false;
        }
    }

    public function get_order_summery_data($order_id){
        $db_obj = new DB();
        $order_query = "SELECT * FROM `orders`
            INNER JOIN order_details on
            orders.order_id = order_details.order_id
            WHERE orders.order_id = '".$order_id."'";

        $db_obj->query($order_query);
        if($db_obj->rowCount() > 0) {
            $order_details_by_id = $db_obj->getResults($order_query);
            $extra_obj = new Extra();
            $order_details = $extra_obj->objectToArray($order_details_by_id);
            return $order_details;
        } else {
            return false;
        }
    }

    public function record_cancellation($record_data){
        $db_obj = new DB();
        $table = 'order_cancellation_history';
        $db_obj->insert($table, $record_data);
    }

    public function delete_order_and_details($order_id){
        $db_obj = new DB();
        $table = 'orders';
        $where = array("order_id =" . $order_id);
        $db_obj->delete($table, $where);

        $table = 'order_details';
        $where = array("order_id =" . $order_id);
        $db_obj->delete($table, $where);

        return true;
    }
    public function get_duration_sales($date,$lastweek) {

        $db_obj = new DB();
        $user_query = "SELECT DATE(order_date) as order_date,SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                   ( DATE(order_date) BETWEEN '".$lastweek."'  AND '".$date."') GROUP BY DATE(order_date)";


        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getResults($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }

    }
    public function get_duration_sales_month($date, $year) {

        $db_obj = new DB();
        $user_query = "SELECT DATE(order_date) as order_date,SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                   MONTH(order_date) = '$date'
                                    AND YEAR(order_date) = '$year' GROUP BY DATE(order_date)";


        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getResults($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }

    }

    public function CompareSales($date, $year){
        $db_obj = new DB();
        $user_query = "SELECT SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                   MONTH(order_date) = '$date'
                                    AND YEAR(order_date) = '$year'";


        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getResults($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }
    }
    public function CompareSalesYear($year){
        $db_obj = new DB();
        $user_query = "SELECT SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                   YEAR(order_date) = '$year'";


        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getResults($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }
    }

    public function create_payment($payment_data) {
        $db_obj = new DB();
        $table = 'payments';
        $db_obj->insert($table, $payment_data);
    }




    public function get_today_orders($date){
        $db_obj = new DB();
        $o_sql = "SELECT * FROM orders WHERE DATE(order_date) = '".$date."'";

        $db_obj->query($o_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($o_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }

    public function order_inside_details($order_id){
        $db_obj = new DB();
        $o_sql = "SELECT * FROM order_details WHERE  order_id = '".$order_id."'";

        $db_obj->query($o_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($o_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }

    public function month_sales_report($month_name, $selected_year){
        $db_obj = new DB();
        $c_sql = "SELECT * FROM orders WHERE MONTH(order_date) =  '".$month_name."' AND YEAR(order_date) =  '".$selected_year."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($c_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }
    public function month_sales_rno_report($month_name, $selected_year){
        $db_obj = new DB();
        $sql = "
SELECT payments.payment_id,payments.order_id , payments.receipt_number,payments.payment_date,orders.order_total 
  FROM `payments` 
  INNER JOIN orders ON
orders.order_id = payments.order_id
WHERE  
YEAR(payments.payment_date) = '".$selected_year."' AND
  MONTH(payments.payment_date) = '".$month_name."'";


        $db_obj->query($sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }

    public function month_sales_report_per_user($month_name,$user){
        $db_obj = new DB();
        $c_sql = "SELECT * FROM orders WHERE MONTH(order_date) =  ".$month_name." AND user_id = '".$user."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($c_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }



    public function month_sales_count($month_name, $selected_month_year){
        $db_obj = new DB();
        $c_sql = "SELECT SUM(order_total) AS TM FROM orders WHERE MONTH(order_date) ='".$month_name."' AND  YEAR(order_date) ='".$selected_month_year."' ";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getRow($c_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }
    public function month_sales_count_per_user($month_name,$user){
        $db_obj = new DB();
        $c_sql = "SELECT SUM(order_total) AS Total FROM orders WHERE MONTH(order_date) ='".$month_name."' AND user_id='".$user."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($c_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }

    public function get_week_sales($date,$last_week_date) {

        $db_obj = new DB();
        $user_query = "SELECT SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                    DATE(order_date) BETWEEN '".$last_week_date."' AND '".$date."'";
        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getRow($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }

    }

    public function get_week_orders($date,$last_week,$user_id){
        $db_obj = new DB();
        $o_sql = "SELECT * FROM orders WHERE DATE(order_date) BETWEEN '".$last_week."' AND '".$date."'";

        $db_obj->query($o_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($o_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }
    public function get_daily_sales_users($date,$user) {

        $db_obj = new DB();
        $user_query = "SELECT SUM(order_total) AS Total
                                FROM
                                    orders
                                WHERE
                                    DATE(order_date) = '".$date."' AND user_id = '".$user."' ";

        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $user_details_by_id = $db_obj->getRow($user_query);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }

    }
    public function get_today_orders_users($date,$user){
        $db_obj = new DB();
        $o_sql = "SELECT * FROM orders WHERE DATE(order_date) = '".$date."' AND  user_id = '".$user."'";

        $db_obj->query($o_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($o_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }

    public function get_top_five(){
        $db_obj = new DB();
        $o_sql = "SELECT `product_id`, COUNT(*) AS magnitude FROM order_details GROUP BY `product_id` ORDER BY magnitude DESC LIMIT 5";

        $db_obj->query($o_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($o_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        }
        else {
            return false;
        }
    }



}