<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Desktop
 * Date: 1/12/15
 * Time: 4:16 PM
 * To change this template use File | Settings | File Templates.
 */

class CashFeed {

    public function save_cash_feed($cash_feed_data) {
        $db_obj = new DB();
        $table = 'cash_counter_feed';
        $db_obj->insert($table, $cash_feed_data);
        return true;
    }

    public function today_cash($date) {
        $db_obj = new DB();
        $c_sql = "SELECT * FROM cash_counter_feed WHERE  end_or_start ='1' AND feed_time = '".$date."' ORDER BY cash_counter_feed_id DESC LIMIT 1";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }
    }
    public function end_cash_total($date) {
        $db_obj = new DB();
        $c_sql = "SELECT SUM(cash_amount) AS Total FROM cash_counter_feed WHERE (cash_or_card = '1' or cash_or_card = '0') AND end_or_start ='0' AND feed_time = '".$date."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }
    }

    public function report_today_start_cash($date) {
        $db_obj = new DB();
        $c_sql = "SELECT SUM(cash_amount) AS Total FROM cash_counter_feed WHERE cash_or_card = '1' AND end_or_start ='1' AND feed_time = '".$date."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }
    }

    public function report_today_end_cash($date) {
        $db_obj = new DB();
        $c_sql = "SELECT SUM(cash_amount) AS Total FROM cash_counter_feed WHERE (cash_or_card = '1' OR cash_or_card = '0')  AND end_or_start ='0' AND feed_time = '".$date."'";

        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {
            $c_sql_details = $db_obj->getRow($c_sql);
            $extra_obj = new Extra();

            return $cat_details = $extra_obj->objectToArray($c_sql_details);

        } else {
            return false;
        }
    }


    public  function week_cash($date,$date_before_week){
        $db_obj = new DB();
        $c_sql = "SELECT DATE(feed_time) as feed_time,cash_amount FROM cash_counter_feed WHERE  end_or_start ='1' AND feed_time BETWEEN '".$date_before_week."' AND '".$date."'";


        $db_obj->query($c_sql);

        if ($db_obj->rowCount() > 0) {

            $extra_obj = new Extra();
            $results = $db_obj->getResults($c_sql);

            $results_need = $extra_obj->objectToArray($results);
            return $results_need;


        } else {
            return false;
        }
    }

    public  function report_week_end_cash($date,$befordate){
        $db_obj = new DB();
        $c_sql = "SELECT DATE(feed_time) as feed_time,SUM(cash_amount) AS Total FROM cash_counter_feed WHERE (cash_or_card = '1' OR cash_or_card = '0')  AND end_or_start ='0' AND feed_time BETWEEN '".$befordate."' AND '".$date."' GROUP BY feed_time";

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

    public function month_start_cash_report($month_name){
        $db_obj = new DB();
        $c_sql = "SELECT * FROM cash_counter_feed WHERE end_or_start ='1' AND month(feed_time) = '".$month_name."'";

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

    public function month_end_cash_report($month){
        $db_obj = new DB();
        $c_sql = "SELECT * FROM cash_counter_feed WHERE end_or_start ='0' AND (cash_or_card = '1' OR cash_or_card = '0') AND month(feed_time) = '".$month   ."'";

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

}