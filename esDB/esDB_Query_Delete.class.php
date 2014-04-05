<?php

class esDB_Query_Delete extends esDB_Query {
	var $queryValues;
	var $values = array();
	
	function __construct( $table, $where ) {
		$this->_tables[$table] = true;
		$this->filters  = $where;
	}
	
	function buildQuery() {
		$this->_buildTables();
		$this->_buildWhere();
		
		$query = array(
			'DELETE FROM',
				$this->queryTables,
			'WHERE',
				$this->queryWhere,
		);
		
		return
			$this->query = join($query, ' ');
	}
	
	
}

?>
