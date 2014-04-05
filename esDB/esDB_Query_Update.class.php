<?php

class esDB_Query_Update extends esDB_Query {
	var $queryValues;
	var $values = array();
	
	function __construct( $tableName, $filter, $values ) {
		$this->values = $values;
		$this->filters = $filter;
		$this->mainTable = $tableName;
		
		$this->fields   = array_keys($values);
	}
	
	function buildQuery() {
		$this->_buildFields( true );
		$this->_buildValues();
		$this->_buildTables();
		$this->_buildWhere();

		
		$query = array(
			'UPDATE ' , $this->queryTables,
			'SET '    , $this->queryValues, 
			'WHERE '  , $this->queryWhere,
		);
		
		return
			$this->query = join($query, ' ');
	}
	
	function _buildValues() {
		$values = array();
		
		foreach( $this->values as $key=>$val ) {
			$values[] = $key.'=?';
			$this->placeholderTypes[]  = 's';
			$this->placeholderValues[] = $val;
		}
		
		return
			$this->queryValues =  join($values, ', ');
	}
	
	function asd() {}
}

?>
