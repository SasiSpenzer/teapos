<?php

class Extra {

    public function objectToArray( $object )
    {
        if( !is_object( $object ) && !is_array( $object ) )
        {
            return $object;
        }
        if( is_object( $object ) )
        {
            $object = get_object_vars( $object );
        }
        //return array_map( $this->objectToArray, $object );
        return array_map(array($this, 'objectToArray'), $object);
    }
    public function fileUpload($file_array){
        $target_path = $target_path . basename( $_FILES['uploadedfile']['name']);

        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
          return true;
        } else{
           return false;
        }

    }

    public function get_next_receipt_number() {
       $db_obj = new DB();
       $get_next_ref_sql = 'SELECT reciept_id
                              FROM increment_data';
       $extra_obj = new Extra();
       $ref_number = $extra_obj->objectToArray($db_obj->getRow($get_next_ref_sql));
       $ref_number = $ref_number['reciept_id'];
       return $ref_number;
    }

    public function update_receipt_number() {
       $db_obj = new DB();
       $update_ref_sql = "UPDATE increment_data
                             SET reciept_id= (reciept_id+1)";
       $db_obj->query($update_ref_sql);


    }

}
?>
