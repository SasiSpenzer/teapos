
<?php


class Customer {

    public function create_customer($customer_data){

        $db_obj = new DB();
        $table = 'customer_details';
        $db_obj->insert($table, $customer_data);
        $id = $db_obj->getLastInsertId();
        return $id ;

    }

    public function existing_customers(){

        $db_obj = new DB();
        $sql = "SELECT
                    *
                FROM
                    customer_details";
        $sql = $db_obj->query($sql);
        if($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $existing_results = $db_obj->getResults($sql);
            $existing_customers = $extra_obj->objectToArray($existing_results);
            return $existing_customers;
        }

    }

}

?>

