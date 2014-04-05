<?php

class esDB_CORE {
	var $dbh;
	var $dbPass;
	var $dbUser;
	var $dbHost;
	var $dbName;
	
	function __construct() {
		$this->_loadConfig();
		
		$this->_initDbh();
		
	}
	
	function Execute( $queryObject, $fetchResult = false ) {
		if(esDB::DEBUG)
			error_log( "\n\nExecuting This Query:\n" . print_r($queryObject, true) );

		$stmt = $this->dbh->prepare( $queryObject->query );
		
		if(!$stmt)
			throw new Exception('Error preparing statement:'.$this->dbh->error);
		
		$this->_stmt_bindParams( $queryObject, $stmt );
		
		$stmt->execute();
		
		if($fetchResult)
			$queryResult = $this->_stmt_fetchResult( $queryObject, $stmt );
		elseif( $this->dbh->insert_id )
			$queryResult = $this->dbh->insert_id;
		else
			$queryResult = $stmt->affected_rows;
		
		if(esDB::DEBUG)
			error_log( "\n\nQuery Results:\n" . print_r($queryResult, true) );
		
		return $queryResult;
	}
	
	function _stmt_fetchResult( $queryObject, $stmt ) {
		$dataSet   = array(); // Array of all rows found
		$rowBuffer = array(); // Assoc. array of fields in a row. Gets populated by mysqli->bind_param
		
		foreach( $queryObject->fields as $field ) {
			$bind_results[] = &$rowBuffer[$field];
		}
		call_user_func_array(array($stmt, 'bind_result'), $bind_results);
		
		while( $stmt->fetch() ) {
			foreach( $queryObject->_fields as $field ) {
				$row[$field['as']] = $rowBuffer[$field['native']];
				
			}
			error_log(print_r($rowBuffer, 1));
			$dataSet[] = $row;
		}
		
		return $dataSet;
	}
	
	function _stmt_bindParams( &$queryObject, &$stmt ) {
		if( !count( $queryObject->placeholderValues ) )
			return;
		
		// First argument to bind_param is the list of types (s=string, i=int, etc...)
		$bind_param_args[] = join( $queryObject->placeholderTypes, '');
		$bind_param_args = array_merge($bind_param_args, $queryObject->placeholderValues);

		call_user_func_array(array($stmt, 'bind_param'), $bind_param_args );
	}
	
	function _initDbh() {
		$this->dbh = new mysqli(
			$this->dbHost,
			$this->dbUser,
			$this->dbPass,
			$this->dbName
		);
		
		if ($this->dbh->connect_errno) {
			throw new Exception( "MySQL Connection ERROR: " . $this->dbh->connect_error );
		}
	}
	
	function _loadConfig() {
		$this->dbHost = esDB_CONFIG::HOST     ;
		$this->dbUser = esDB_CONFIG::USER     ;
		$this->dbPass = esDB_CONFIG::PASS     ;
		$this->dbName = esDB_CONFIG::DATABASE ;
	}
}

?>
