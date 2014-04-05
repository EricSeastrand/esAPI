<?php

define("ESDB_LIBPATH", dirname(__FILE__) );
require_once(ESDB_LIBPATH . '/db.conf.php');
require_once(ESDB_LIBPATH . '/esDB_CORE.class.php');
require_once(ESDB_LIBPATH . '/esDB_Query.class.php');
require_once(ESDB_LIBPATH . '/esDB_Query_Select.class.php');
require_once(ESDB_LIBPATH . '/esDB_Query_Insert.class.php');
require_once(ESDB_LIBPATH . '/esDB_Query_Delete.class.php');

class esDB {
	const DEBUG = true;
	var $core;
	var $routes;
	
	function __construct(  ){
		$this->core = new esDB_CORE();
	}
	
	function Select( $baseTable, $fields, $filter, $joins ) {
		$this->queryObject = new esDB_Query_Select( $baseTable, $fields, $filter, $joins );
		$this->queryObject->buildQuery();
		
		$results = $this->core->Execute( $this->queryObject, true );
		
		return $results;
	}
	
	function Insert( $colValues ) {
		$this->queryObject = new esDB_Query_Insert( $colValues );
		$this->queryObject->buildQuery();
		
		$result = $this->core->Execute( $this->queryObject );
		
		return $result;
	}
	
	function Delete( $table, $where ) {
		$this->queryObject = new esDB_Query_Delete( $table, $where );
		$this->queryObject->buildQuery();
		
		$result = $this->core->Execute( $this->queryObject );
		
		return $result;
	}
	
}

?>
