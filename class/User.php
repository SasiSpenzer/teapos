<?php

class User {


    
    public function getUserByID($id) {
    
        $db_obj = new DB();
        $user_query = "SELECT *                                       
                                FROM
                                    users
                                WHERE
                                    id = '".$id."'";
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
    public function list_users() {

        $db_obj = new DB();
        $user_query = "SELECT * FROM users ";

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

    public function get_backup_location(){
        $db_obj = new DB();
        $user_query = "SELECT * FROM backup_location_data ";

        $db_obj->query($user_query);
        if($db_obj->rowCount() > 0) {
            $location_details_array = $db_obj->getRow($user_query);
            $extra_obj = new Extra();
            $location_details = $extra_obj->objectToArray($location_details_array);
            return $location_details;
        } else {
            return false;
        }
    }

    public function update_location_backup($data){


        $db_obj = new DB();
        $table = 'backup_location_data';
        $sql = "UPDATE backup_location_data SET data = '$data' where bld_id = '1'";
        $db_obj->query($sql);
    }
    
    
    
    public function login_user($user_details) {
        
        $user_name =  $user_details['user_name'];
        $password  =  md5($user_details['password']);
        
        $db_obj = new DB();
        $user_details_sql = "SELECT
                                    id,
                                    username,
                                    password,
                                    status,
                                    is_admin                                        
                                FROM
                                    users
                                WHERE
                                    username = '".$user_name."' 
                                AND 
                                    password = '".$password."'";
        
        $db_obj->query($user_details_sql);
        
        if($db_obj->rowCount() > 0) {
        
            $user_details = $db_obj->getRow($user_details_sql);
            $extra_obj = new Extra();
            $logged_user_details = $extra_obj->objectToArray($user_details);
            return $logged_user_details;
            
        } else {
            return false;
        }
        
    
    }
    
  
    public function uppdate_last_login($user_id) {
        
        $db_obj = new DB();
        $update_last_login_sql = "UPDATE
                                users
                                SET
                                 last_login = NOW()
                                WHERE
                                    id = ".$user_id ;
        
        $db_obj->query($update_last_login_sql);
        
        
    }
    public function insert_user_log($user_data) {
        $db_obj = new DB();
        $table = 'user_login_history';
        $db_obj->insert($table, $user_data);

    }

    public function get_user_availability($user_name) {
        $db_obj = new DB();
        $sql = "SELECT  username
                FROM
                        users
                WHERE
                        username='$user_name'";
        $db_obj->query($sql);
        if($db_obj->rowCount() > 0) {
            return 'ture';
        }
    
    }


    public function add_new_user($data){
        $db_obj = new DB();
        $table ='users';
        $results = $db_obj->insert($table,$data);
        return $results;
    }
    public function tytyt() {
        die('test');
    }

}