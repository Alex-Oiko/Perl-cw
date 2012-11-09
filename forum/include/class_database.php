<?php

/* Database Class */
class Database {
	/* Reference to core object */
	private $core = null;

	/* Most recent SQL query */
	private $sql = '';

	/* Number of queries */
	private $query_count = 0;

	/* Database connection */
	private $connection;

	/* Database prefix */
	public $db_prefix = "";

	/* Construct a new database object, take in reference to the core */
	public function Database(&$core) {
		if (!is_object($core)) { fatal_error("Attempting to initialise database without core object",1); }
		$this->core =& $core;
		$this->connect();
	}

	/* Connect to the database using DB configuration information */
	private function connect() {
		$core = $this->core;
		$dbserver = $core->config['Database']['db_server'];
		$dbname = $core->config['Database']['db_name'];
		$dbuser = $core->config['Database']['db_user'];
		$dbpassword = $core->config['Database']['db_password'];
		$dbprefix = $core->config['Database']['db_prefix'];
		
		//Set prefix from configuration
		$this->db_prefix = $dbprefix;

		//Set the database connection to the mysql connection
		$this->connection = new mysqli($dbserver,$dbuser,$dbpassword,$dbname);

		//Handle connection error
		if ($this->connection->connect_error) { 
			$dberror = $this->connection->connect_error;
			$this->db_error("Unable to connect to database: $dberror");  
		}
	}

	/* Execute a query on the database */
	public function &query($sql) {
		$this->sql =& $sql;

		//Attempt to execute the query, returning the result if succesful, otherwise halting with a DB error
		if ($result = $this->connection->query($this->sql)) { return $result; }
		else { $this->db_error($this->connection->error); }
	}

	/* Handle database errors */
	public function db_error($message = "") {
		//Only show admins useful database errors
		global $user;
			$outmessage = "Database error";
			if ($message != "") { $outmessage .= ": $message"; }
			if ($this->sql != "") { $outmessage .= "<br/><br/><strong>SQL</strong>:<br/>" . nl2br($this->sql); }
			if($user->is_admin()){
				fatal_error($outmessage);
			}
			else{
				fatal_error("Internal Error");
			}
	}

	/* Create and return a query object associated with the DB and specified tables */
	public function &make_query($tables,$type="SELECT") {
		$query = new Query($this,$tables,$type);
		return $query;
	}

	/* Rewrite key in MySQL form */
	public function rewrite_key($key) { 
		//Allow functions
		if (strpos($key,"(")) { return $key; }
		//Otherwise encompass in `'s
		$key = "`$key`"; return $key;
	}

	/* Rewrite and escape values in MySQL form, do any protection needed */
	public function rewrite_value($value) {
		if (!is_numeric($value)) { 
			$value = $this->connection->real_escape_string($value); 
			
			$value = "'$value'"; 
		}
		return $value; 
	}

	/* Return the auto-generated ID number */
	public function get_auto_id() { return $this->connection->insert_id; }
}

/* Database Query Class */
class Query {
	/* Reference to the database */
	private $db = null;

	/* Fields to use */
	private $fields = "*";

	/* Tables this query concerns */
	private $tables = "";

	/* Conditions where applicable */
	private $conditions = "";

	/* Type of SQL statement */
	private $type = "";

	/* Data to input */
	private $data = "";

	/* Limit */
	private $limit = "";

	/* Data order */
	private $order = "";

	/* Group data */
	private $group = "";

	/* Constructor */
	public function Query($db,$tables,$type) {
		$this->db =& $db;
		$db_prefix = $db->db_prefix;

		//Add database prefixes to tables
		$tables = preg_replace('/(^|,)([^,\n]*)/sim', '$1' . $db_prefix . '$2', $tables);

		$this->tables = "`" . str_replace(",","`,`",$tables) . "`";
		$this->type = $type;

		//Sanity check for tables
		if ($tables == "") { $db->db_error("Attempting to create query on no tables"); }
	}
	
	/* Add protected condition */
	public function add_condition($key,$type,$value,$condition_type="AND") {
		//Prepare value and key
		$conditions =& $this->conditions;
		$key = $this->db->rewrite_key($key);
		$value = $this->db->rewrite_value($value);

		//No conditions yet so start adding conditions
		if ($conditions == "") {
			$conditions = "$key $type $value";
		}
		//Otherwise append
		else {
			$conditions .= " \n$condition_type $key $type $value";
		}
	}

	/* Directly add condition */
	public function add_condition_direct($condition,$condition_type="AND") {
		//Add DB prefix if necessary
		$dbprefix = $this->db->db_prefix;
		$condition = preg_replace("/`([^`]*)`[.]/","`$dbprefix\\1`.",$condition); 

		//Prepare value and key
		$conditions =& $this->conditions;
		//No conditions yet so start adding conditions
		if ($conditions == "") {
			$conditions = "$condition";
		}
		//Otherwise append
		else {
			$conditions .= " \n$condition_type $condition";
		}
	}

	/* Set grouping */
	public function set_group($fields) {
		$fields = str_replace(",","`,`",$fields);
		$fields = str_replace(".","`.`",$fields);
		$this->group = "`" . $fields . "`";
	}

	/* Set fields */
	public function set_fields($fields) {
		$fields = str_replace(",","`,`",$fields);
		$fields = str_replace(".","`.`",$fields);
		$this->fields = "`" . $fields . "`"; 		
	}

	/* Directly set fields */
	public function set_fields_statement($statement) { $this->fields = $statement; }

	/* Set query limit to limit results */
	public function set_limit($limit) { $this->limit = $limit; }

	/* Set a specified order */
	public function set_order($order) { 
		$dbprefix = $this->db->db_prefix;
		$order = preg_replace('/([^.]+)[.]([^. ]+)/sim', "`$dbprefix\\1`.`\\2`", $order); 
		$this->order = $order; 
	}

	/* Set a random order */
	public function set_random_order() { $this->set_order("RAND()"); }

	/* Add data to query */
	public function add_data($key,$value) {
		//Prepare value and key
		$data =& $this->data;
		$key = $this->db->rewrite_key($key);
		$value = $this->db->rewrite_value($value);
		
		//No data yet so start adding
		if ($data == "") { 
			$data = "$key = $value";
		}
		else {
			$data .= ", \n$key = $value";
		}
	}

	/* Output SQL query, overriding PHP default */
	public function __toString() { return $this->to_sql(); }

	/* Execute the query */
	public function execute() { return $this->db->query($this->to_sql()); }

	/* Convert query to SQL */
	private function &to_sql() {
		$sql = "";
		$fields = $this->fields;
		$tables = $this->tables;
		$data = $this->data;

		switch($this->type) {
			case "SELECT":
				$sql = "SELECT $fields FROM $tables \n "; 
				$sql .= $this->add_conditions() . $this->add_group() . $this->add_order() . $this->add_limit();
				break;
			case "INSERT":
				if ($data == "") { $this->db->database_error("Attempting to insert nothing into table $tables"); }
				$sql = "INSERT INTO $tables \n";
				$sql .= "SET $data \n";
				break;				
			case "UPDATE":
				if ($data == "") { $this->db->database_error("Attempting to update nothing in table $tables"); }
				$sql = "UPDATE $tables \n";
				$sql .= "SET $data \n";
				$sql .= $this->add_conditions() . $this->add_order() . $this->add_limit();	
				break;	
			case "DELETE":
				$sql = "DELETE FROM $tables \n";
				$sql .= $this->add_conditions() . $this->add_order() . $this->add_limit();	
				break;
			default:
				$this->db->db_error("Invalid database query type: $this->type");
				break;
		}
		return $sql;
	}

	/* Helper functions */
	private function add_conditions() { $conditions = $this->conditions; return ($conditions != "") ? " WHERE $conditions \n " : ""; }
	private function add_order() { $order = $this->order; return ($order != "") ? " ORDER BY $order \n" : ""; }
	private function add_limit() { $limit = $this->limit; return ($limit != "") ? " LIMIT $limit \n" : ""; }
	private function add_group() { $group = $this->group; return ($group != "") ? " GROUP BY $group \n" : ""; }

}

?>
