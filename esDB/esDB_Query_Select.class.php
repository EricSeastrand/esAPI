<?php

class esDB_Query_Select extends esDB_Query {
	
	
	function __construct( $baseTable, $fields, $filter = array(), $joins = array() ) {
		$this->fields  = $fields;
		$this->filters = $filter;
		$this->joins   = $joins;
		$this->mainTable = $baseTable;
	}
	
	function buildQuery( ) {
		$this->_buildFields();
		$this->_buildTables();
		$this->_buildWhere();
		$this->_buildJoins();
		//$this->_buildGroupBy();
		
		$query = array(
			'SELECT',
				$this->queryFields,
			'FROM',
				$this->mainTable
		);
		if( $this->queryJoins )
			array_push( $query, 'LEFT JOIN', $this->queryJoins );
		if( $this->queryWhere )
			array_push( $query, 'WHERE', $this->queryWhere );
		/*
		if( $this->queryGroupBy )
			array_push( $query, 'GROUP BY', $this->queryGroupBy );
		*/
		return
			$this->query = join($query, ' ');
	}
	
	function _buildGroupBy() {
		$tablesUsed = $this->tables;
		foreach( $tablesUsed as $tableName ) {
			if($this->mainTable != $tableName)
				$groups[] = $tableName . '.ID';
		}
		
		$this->queryGroupBy = join( $groups, ',' );
	}
	
	
}

?>
