<?php

/**
 * Created by PhpStorm.
 * User: Win8.1-L
 * Date: 1/13/2017
 * Time: 11:53 AM
 */
class Printer
{
    public function updatePrinter($data){
        $db_obj = new DB();
        $data_send = array();
        $data_send['defaultPrinter'] = $data;
        $table = 'Printer';
        $where = array("printerId =" . 1);
        $db_obj->update($table,$data_send, $where);
    }

}