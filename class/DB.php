<?php
/**
 * Database specific class - mySQL
 *
 */
class DB {
	
 /*********************************************************************
     * Connection Parameters												  *
  **********************************************************************/

    private static $hostname	=	'localhost';
	private static $username	=	'root';
	private static $password	=	'';
	private static $dbname		=	'teapos_gallface';
	private static $instance = FALSE;
	private $db;
	public $fetch_mode = PDO::FETCH_OBJ;
	
	 /*********************************************************************
	 * Cached parameters												  *
	 **********************************************************************/	
	private $last_query	= NULL;
	private $last_statement = NULL;
	private $last_result	= NULL;
	private $row_count	= NULL;
	private $affected_row	= NULL;
		
	/**
	 * Constructor.
	 * Implements the Singleton design pattern.
	 * 
	 * @return object DB
	 * @access public
	 */
	public function __construct() {
        ini_set('track_errors',1);
        if (!self::$instance){
            $this->connect();
        }
        $this->db = self::$instance;
        return self::$instance;
	}
	
	/**
	 * Connect to the database and set the error mode to Exception.
	 * 
	 * @return void
	 * @access private
	 */
	private function connect(){
            $dns = 'mysql:host='.self::$hostname.';dbname='.self::$dbname;
	    self::$instance = new PDO($dns, self::$username, self::$password);
	    //self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $this->db = self::$instance;
	}
	
	/**
	 * Execute a query. 
	 * This function can be used from external. 
	 * The function separate the simple queryes and the INSERT, UPADTE, DELETE queries.
	 * 
	 * @param string $query
	 * @access public
	 * 
	 * @todo Validate $query
	 */
	public function query($query = NULL){
		$this->flush(); 
		$query = trim($query);
		$this->last_query = $query;
		// Query was an insert, delete, update, replace
		if ( preg_match("/^(insert|delete|update|replace|drop|create)\s+/i",$query) ){
			$this->affected_row = $this->db->exec($query);
			if ( $this->catch_error() ) return false;
			else return $this->affected_row;
		}
		else {
			//Query was an simple query.
			$stmt = $this->db->query($query);
			if ( $this->catch_error() ) return false;
			else {
				$stmt->setFetchMode($this->fetch_mode);
				$this->last_statement = $stmt;
				$this->last_result = $this->last_statement->fetchAll();
				return $this->last_result;
			}
		}
	}
	
	/**
	 * Execute a query.
	 * This function can be used from DB class methods.
	 * 
	 * @param string $query
	 * @return bool
	 * @access private
	 * 
	 * @todo Validate $query
	 */
	private function internalQuery($query = NULL){
		$this->flush();
		$query = trim($query);
		$this->last_query = $query;
		
		$stmt = $this->db->query($query);
		if ( $this->catch_error() ) return false;
		$stmt->setFetchMode($this->fetch_mode);
		$this->last_statement = $stmt;
		return TRUE;
	}

	/**
	 * Execute a query (INSERT, UPDATE, DELETE).
	 * 
	 * @param string $query
	 * @return int
	 * @access private
	 * 
	 * @todo Validate $query
	 */
	private function execute($query = NULL){
		$this->flush();
		$query = trim($query);
		$this->last_query = $query;
		$this->affected_row = $this->db->exec($query);
		if ( $this->catch_error() ) return false;
		return $this->affected_row;
	}	
	
	/**
	 * Return the the query as a result set.
	 *
	 * @param string $query
	 * @return result set
	 * @access public
	 * 
	 * @todo Validate $query.
	 */
	public function getResults($query = NULL){
		$this->internalQuery($query);
		$result = $this->last_statement->fetchAll();
		$this->last_result = $result;
		return $result;
	}
	
	/**
	 * Get one row from the DB.
	 *
	 * @param string $query
	 * @return reulst set
	 * @access public
	 * 
	 * @todo Validate $query.
	 */
	public function getRow($query = NULL){
		$this->internalQuery($query);
		$result = $this->last_statement->fetch();
		$this->last_result = $result;
		return $result;		
	}
	
	/**
	 * Helper function, walk the array, and modify the values.
	 *
	 * @param pointer $item
	 * @return void
	 * @access private 
	 */
	private static function prepareDbValues(&$item){
		$item = "'".self::escape($item)."'";
	}

	/**
	 * Insert a value into a table.
	 *
	 * @param string $table
	 * @param array $data
	 * @return void
	 * @access public
	 * 
	 * @todo Validate if $table or $data is null.
	 */
	public function insert($table = NULL, $data = NULL){
		array_walk($data,'DB::prepareDbValues');
		
		$query = "INSERT INTO `".$table."` 
				 (`".implode('`, `', array_keys($data))."`) 
				 VALUES ( ".implode(', ', $data).")";
		$this->execute($query);
	}
	
	/**
	 * Update a value(s) in a table
	 * Ex: 
	 * $table = 'tableName';
	 * $data = array('text'=> 'value', 'date'=> '2009-12-01');
	 * $where = array('id=12','AND name="John"'); OR $where = 'id = 12';
	 *
	 * @param string $table
	 * @param array $data
	 * @param array/string $where
	 * @return void
	 * @access public
	 * 
	 * @todo Validate the $table, $data, $where variables.
	 */
	public function update($table = NULL, $data = NULL, $where = NULL){
		array_walk($data,'DB::prepareDbValues');
		foreach ($data as $key => $val){
			$valstr[]= $key.' = '.$val;
		}

		$query = "UPDATE ".$table." SET ".implode(', ', $valstr);

		if (is_array($where)){
			$query.= " WHERE ".implode(" ",$where);
		}
		else {
			$query.= " WHERE ".$where;
		}

		$this->execute($query);
	}
	
	/**
	 * Delete a record from a table.
	 * Ex.
	 * $table = 'tableName';
	 * $where = array('id = 12','AND name = "John"'); OR $where = 'id = 12';
	 *
	 * @param string $table
	 * @param array/string $where
	 * @return void
	 * @access public
	 * 
	 * @todo Validate the $table, $where variables.
	 */
	public function delete($table = NULL, $where = NULL){
		$query = "DELETE FROM ".$table." WHERE ";
		if (is_array($where)){
			$query.= implode(" ",$where);
		}
		else{
			$query.= $where;
		}
		$this->execute($query);
	}
	
	/**
	 * Return the last insert id.
	 *
	 * @return integer
	 * @access public
	 */
	public function getLastInsertId(){
		return $this->db->lastInsertId();
	}
	
	/**
	 * Return the last executed query.
	 *
	 * @return string
	 * @access public
	 */
	public function getLastQuery(){
		return $this->last_query;
	}
	
	/**
	 * Returns the number of rows affected by the last SQL statement.
	 *
	 * @return int
	 * @access public
	 */
	public function rowCount(){
		if (!is_null($this->last_statement)){
			return $this->last_statement->rowCount();
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Set the PDO fetch mode.
	 *
	 * @param string $fetch_mode
	 * @return void
	 * @access public
	 */
	public function setFetchMode($fetch_mode){
		$this->fetch_mode = $fetch_mode;
	}
	
	/**
	 * Kill cached data.
	 * 
	 * @return void
	 * @access private
	 */
	private function flush(){
		$this->last_query		= NULL;
		$this->last_statement 	= NULL;
		$this->last_result		= NULL;
		$this->row_count		= NULL;
		$this->affected_row		= NULL;
	}
	
        public function begin_transaction() {
            $this->db->beginTransaction();
        }
        
        public function commit_transaction() {
            $this->db->commit();
        }

        /**
	*  Format a mySQL string correctly for safe mySQL insert
	*  (no mater if magic quotes are on or not)
	*
	* @param string $str
	* @return string
	* @access public
	*/
	public static function escape($str)
	{
		return  stripslashes($str);
	}
	
	function catch_error()
    {
        $err_array = $this->db->errorInfo();
        // Note: Ignoring error - bind or column index out of range
        if ( isset($err_array[1]) && $err_array[1] != 25)
        {
            try {
                throw new Exception();
            }
            catch (Exception  $e){
                print "<div style='background-color:#D8D8D8; color:#000000; padding:10px;
                border:2px red solid;>";
                print "<p style='font-size:25px; color:#7F0000'>DATABASE ERROR</p>";
                print "<p style='font-size:20px; color:#7F0000'>Query:<br />
                <span style='font-size:15px; color:#000000;'>{$this->getLastQuery()}</span></p>";
                print "<p style='font-size:20px; color:#7F0000'>Message:<br />
                <span style='font-size:15px; color:#000000;'>{$err_array[2]}</span></p>";
                print "</div>";
                die();
            }
        }
    }

}
?>