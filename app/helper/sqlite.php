<?php
class Helper_Sqlite {
	private $_dbname = '';
	private $_agent  = '';
	
	function __construct ( $dbname='' ) {
		$this->_dbname = $dbname;
		$this->_agent  = new SQLiteDatabase($dbname);

		defined('SQLITE_TYPE_PRIMARY')  OR define('SQLITE_TYPE_PRIMARY',  ' INTEGER(19) PRIMARY KEY');
		defined('SQLITE_TYPE_TINYINT')  OR define('SQLITE_TYPE_TINYINT',  ' INTEGER(2) ' );
		defined('SQLITE_TYPE_SMALLINT') OR define('SQLITE_TYPE_SMALLINT', ' INTEGER(8) ' );
	}
	
	function __destruct () {}
	
	function create ( $table, $attrs ) {
		$sql  = "CREATE TABLE IF NOT EXISTS {$table} (";
		foreach ( $attrs as $col => $attr ) {
			$sql .= "`{$col}` {$attr['type']}";
		}
		$sql .= ")";
		$this->_agent->queryExec($sql);
	}
	
	function execute ( $sql ) {
		return $this->_agent->query($sql);
	}
	function insert () {}
	function update () {}
	function delete () {}
	function read () {}
	
	function check () {
		$this->_agent->queryExec("CREATE TABLE sys_group (uuid INTEGER PRIMARY KEY, puuid INTEGER, gname TEXT)");
		
		$this->_agent->query('INSERT INTO sys_group VALUES ('.time().', 0, "'.time().'");');
		
		$return = array();
		$query = $this->_agent->query("SELECT * FROM sys_group WHERE 1 ", SQLITE_ASSOC, $query_error);
		if ($query_error) die("Error: $query_error");
		if (!$query) die("Impossible to execute query.");
		while ( $row=$query->fetch() ) $return[] = $row;
		return $return;
	}
}