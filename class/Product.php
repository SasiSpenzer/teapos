<?php


class Product
{

    public function select_product_by_cat_id($cat_id)
    {
        $db_obj = new DB();
        $sql = "SELECT *
                FROM `product`
                JOIN category ON category.category_id = product.category_id
                WHERE product.category_id='" . $cat_id . "'
                  AND product.status=1
                ORDER BY product.Order_id ASC";
        $db_obj->query($sql);
        if ($db_obj->rowCount() > 0) {
            $extra_obj = new Extra();
            $parent_results = $db_obj->getResults($sql);
            $slider_details = $extra_obj->objectToArray($parent_results);
            return $slider_details;
        }
    }
    public function product_value()
    {

        $db_obj = new DB();
        $p_sql = "SELECT SUM(qty * price) AS grand_total FROM product";
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }
    public function getLowProducts(){

        $db_obj = new DB();
        $p_sql = "SELECT * FROM `product` WHERE `qty` > '0' AND `qty` < '0.5'";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }


    }



    public function list_product_inventory_live()
    {

        $db_obj = new DB();
        $p_sql = "SELECT product_id,price,product_name,qty,category_name  FROM product left join category on product.category_id=category.category_id order by category_name DESC";
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function GetTodaySalesPosReport()
    {

        $db_obj = new DB();
        $date = date('y-m-d');
        $p_sql = "SELECT SUM(order_details.no_of_products) as no_of_products,order_details.product_id,product.product_name,product.is_loose,orders.order_id,orders.order_date,product.is_loose,
                          (product.price * order_details.no_of_products) as order_total
                  FROM orders JOIN order_details
                  ON orders.order_id = order_details.order_id
                  JOIN product ON product.product_id = order_details.product_id
                  WHERE DATE(order_date) = '$date'
                  GROUP BY order_details.product_id";
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {

            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $sales_details = $extra_obj->objectToArray($p_sql_details);

        } else {

            return false;
        }
    }

    public function GetTodaySalesPosReportBYDate($date)
    {

        $db_obj = new DB();

        $p_sql = "SELECT SUM(order_details.no_of_products) as no_of_products,order_details.product_id,product.product_name,product.is_loose,orders.order_id,orders.order_date,product.is_loose,
                          (product.price * order_details.no_of_products) as order_total
                  FROM orders JOIN order_details
                  ON orders.order_id = order_details.order_id
                  JOIN product ON product.product_id = order_details.product_id
                  WHERE DATE(order_date) = '$date'
                  GROUP BY order_details.product_id";
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {

            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $sales_details = $extra_obj->objectToArray($p_sql_details);

        } else {

            return false;
        }
    }






    public function update_order_p($data, $id)
    {
        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $id);
        $db_obj->update($table, $data, $where);
    }

    public function list_priduct_by_product_id($p_id)
    {

        $db_obj = new DB();
        $p_sql = "SELECT *  FROM product WHERE product_id = " . $p_id;

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function list_priduct_by_product_id_by_bar($p_id, $field)
    {

        $db_obj = new DB();
        $p_sql = "SELECT * FROM product WHERE product_id = " . $p_id;
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function list_priduct_inventory()
    {

        $db_obj = new DB();
        $p_sql = "SELECT product.product_id,product.qty,product.price,product.product_name,category.category_name,product.category_id  FROM product
                    INNER JOIN category ON
                    category.category_id = product.category_id";
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function add_new_product($product_details)
    {

        $db_obj = new DB();
        $table = 'product';
        $db_obj->insert($table, $product_details);
        return $db_obj->getLastInsertId();


    }

    public function  get_product_data()
    {

        $db_obj = new DB();
        $p_sql = "SELECT *  FROM temp_lable_details";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($sql_details);

        } else {
            return false;
        }

    }

    public function get_dates()
    {
        $db_obj = new DB();
        $p_sql = "SELECT *  FROM product_dates";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $date_details = $extra_obj->objectToArray($sql_details);

        } else {
            return false;
        }

    }


    public function add_dates_lable($data)
    {

        $db_obj = new DB();
        $query = "TRUNCATE TABLE product_dates";
        $db_obj->query($query);

        $table = 'product_dates';
        $db_obj->insert($table, $data);
    }


    public function add_product_for_barcode_lable($data)
    {
        $db_obj = new DB();
        $query = "TRUNCATE TABLE temp_lable_details";
        $db_obj->query($query);

        $table = 'temp_lable_details';
        $db_obj->insert($table, $data);
    }


    public function reduce_qty($data, $id)
    {

        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $id);
        $db_obj->update($table, $data, $where);


    }

    public function delete_product_by_id($p_id)
    {

        $db_obj = new DB();
        $table = 'product';
        $where = array("product_id =" . $p_id);
        $db_obj->delete($table, $where);

        return true;

    }


    public function add_product_history($product_history_details)
    {

        $db_obj = new DB();
        $table = 'product_history';
        $db_obj->insert($table, $product_history_details);
        return $db_obj->getLastInsertId();


    }

    public function update_new_product($product_details, $product_id)
    {

        $db_obj = new DB();
        $table = 'product';
        $data = $product_details;
        $where = array("product_id =" . $product_id);
        $db_obj->update($table, $data, $where);

        return true;

    }

    public function search_product($keyword)
    {

        $db_obj = new DB();
        $search_sql = "SELECT category.category_name,product.*
                       FROM product inner join category
                       WHERE
                       product.product_name LIKE '%" . $keyword . "%'
                       AND
                       product.category_id = category.category_id
                       AND
                       product.status = '1'";

        $db_obj->query($search_sql);

        if ($db_obj->rowCount() > 0) {
            $search_details = $db_obj->getResults($search_sql);
            $extra_obj = new Extra();

            return $search_details = $extra_obj->objectToArray($search_details);

        } else {
            return false;
        }
    }

    public function get_product_by_barcode($barcode)
    {

        $db_obj = new DB();
        $p_sql = "SELECT product_id  FROM product WHERE barcode = " . $barcode;
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function get_product_by_barcode_add($barcode)
    {

        $db_obj = new DB();
        $p_sql = "SELECT product_id  FROM product WHERE barcode = " . $barcode . " OR bar25 = " . $barcode . " OR  bar50 = " . $barcode . " OR bar80 = " . $barcode . " OR bar100 = " . $barcode . " OR bar250 = " . $barcode . " OR bar500 = " . $barcode . " OR bar1000 = " . $barcode;

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function get_product_by_barcode_grames($barcode)
    {

        $db_obj = new DB();
        $p_sql = "SELECT  product_id,bar25,bar50,bar80,bar100,bar250,bar500,bar1000 FROM product WHERE bar25 = " . $barcode . " OR bar50 = " . $barcode . " OR bar80 = " . $barcode . " OR bar100 = " . $barcode . " OR bar250 = " . $barcode . " OR bar500 = " . $barcode . " OR bar1000 = " . $barcode;
        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getRow($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }


    public function get_each__product_sales_today($product_id, $date)
    {

        $db_obj = new DB();
        $p_sql = " SELECT order_details.*,orders.order_id,orders.order_date
                   FROM order_details INNER JOIN orders
                   ON order_details.product_id = '" . $product_id . "'
                   AND orders.order_id = order_details.order_id
                   AND DATE(orders.order_date) = '" . $date . "'";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }

    }

    public function get_each_product_week_sales($product_id, $date_start, $date_end)
    {
        $db_obj = new DB();
        $p_sql = "SELECT order_details.*,orders.order_id,orders.order_date FROM order_details INNER JOIN
                  orders ON
                  order_details.product_id = '" . $product_id . "' AND
                  orders.order_id = order_details.order_id AND
                  (DATE(orders.order_date) BETWEEN '" . $date_end . "' AND '" . $date_start . "')";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }
    }

    public function get_each_product_month_sales($product_id, $month)
    {
        $db_obj = new DB();
        $p_sql = "SELECT order_details.*,orders.order_id,orders.order_date FROM order_details INNER JOIN
                  orders ON
                  order_details.product_id = '" . $product_id . "' AND
                  orders.order_id = order_details.order_id AND
                  MONTH(orders.order_date) = '" . $month . "'";

        $db_obj->query($p_sql);

        if ($db_obj->rowCount() > 0) {
            $p_sql_details = $db_obj->getResults($p_sql);
            $extra_obj = new Extra();

            return $product_details = $extra_obj->objectToArray($p_sql_details);

        } else {
            return false;
        }
    }

    public function get_all_loose_tea()
    {
        $db_obj = new DB();
        $p_sql = "SELECT product_id from product where is_loose='T'";


        $p_sql_details = $db_obj->getResults($p_sql);
        $extra_obj = new Extra();

        return $product_details = $extra_obj->objectToArray($p_sql_details);
    }
    public function get_all_nonLoos_tea()
    {
        $db_obj = new DB();
        $p_sql = "SELECT * from product where is_loose='F'";


        $p_sql_details = $db_obj->getResults($p_sql);
        $extra_obj = new Extra();

        return $product_details = $extra_obj->objectToArray($p_sql_details);
    }

} 