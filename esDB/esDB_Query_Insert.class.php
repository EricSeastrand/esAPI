<?php

class esDB_Query_Insert extends esDB_Query {
	var $queryValues;
	var $values = array();
	
	function __construct( $values ) {
		$this->values = $values;
		
		$this->fields   = array_keys($values);
	}
	
	function buildQuery() {
		$this->_buildFields( true );
		$this->_buildValues();
		$this->_buildTables();
		
		$query = array(
			'INSERT INTO',
				$this->queryTables,
			'(',
				$this->queryFields,
			')',
			'VALUES',
			'(',
				$this->queryValues,
			')'
		);
		
		return
			$this->query = join($query, ' ');
	}
	
	function _buildValues() {
		$values = array();
		
		foreach( $this->values as $key=>$val ) {
			$values[] = '?';
			$this->placeholderTypes[]  = 's';
			$this->placeholderValues[] = $val;
		}
		
		return
			$this->queryValues =  join($values, ', ');
	}
	
	function asd() {}
}

?>
