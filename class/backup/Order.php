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

    public function create_payment($payment_data) {
        $db_obj = new DB();
        $table = 'payments';
        $db_obj->insert($table, $payment_data);
    }






}