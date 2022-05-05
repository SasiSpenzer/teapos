
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

        $db_obj->query($sql);
        if($db_obj->rowCount() > 0) {

            $user_details_by_id = $db_obj->getResults($sql);
            $extra_obj = new Extra();
            $user_details = $extra_obj->objectToArray($user_details_by_id);
            return $user_details;
        } else {
            return false;
        }


    }
    public function existing_customers_by_id($id){

        $db_obj = new DB();
        $sql = "SELECT
                    *
                FROM
                    users WHERE id='".$id."'";
        $sql = $db_obj->query($sql);
        if($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $existing_customers = $extra_obj->objectToArray($sql);
            return $existing_customers;
        }

    }
    public function update_pass($customer_data,$user){

        $db_obj = new DB();
        $table = 'users';
        $data = $customer_data;
        $where = array("id =" . $user);
        $db_obj->update($table, $data, $where);

        return true;

    }
    public function update_user_status($customer_data,$user){

        $db_obj = new DB();
        $table = 'users';
        $data = $customer_data;

        $where = "id =".$user;
        $db_obj->update($table, $data, $where);
        header("Location:index.php");

    }

}

?>

