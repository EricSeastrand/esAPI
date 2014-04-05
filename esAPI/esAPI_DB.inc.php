<?php

require_once(dirname(__FILE__) . '/../esDB/esDB.inc.php');
require_once(dirname(__FILE__) . '/esAPI.inc.php');

class esAPI_DB extends esAPI {
	var $esDB;
	
	function __construct() {
		$this->esDB = new esDB();
		
		parent::__construct();

		$this->loadConfig_JSON(dirname(__FILE__) . '/../routes.json');
	}
	
	
	function loadConfig_JSON( $filePath ) {
		$newConfig = json_decode( file_get_contents($filePath), true );

		if(!$newConfig)
			throw new Exception('ERROR parsing JSON config.');
		
		$this->actions = $newConfig;
	}
	
	function ExecRoute( $actionName, $params=false ) {
		if($params === false) { error_log('ExecRoute called with no $params specified.'); $params = array(); }
		return $this->_REQUEST_HANDLER( $this->actions[ $actionName ], $params );
	}
	
	function _REQUEST_HANDLER( $route, $params ) {
		if( @$route['select'] )
			return $this->_handle_Select( $route, $params );
		if( @$route['insert'] )
			return $this->_handle_Insert( $route, $params );
		if( @$route['delete'] )
			return $this->_handle_Delete( $route, $params );
		if( @$route['update'] )
			return $this->_handle_Update( $route, $params );
	}
	
	function _handle_Delete( $route, $params ) {
		$where = $this->_substituteValues( $route['where'], $params, true );

		return $this->esDB->Delete( $route['delete'], $where );
	}
	
	function _handle_Insert( $route, $params ) {
		$toInsert = $this->_substituteValues( $route['insert'], $params, true );

		return $this->esDB->Insert( $toInsert );
	}
	
	function _handle_Select( $route, $params ) {
		if( $route['where'] ) {
			$filters = $this->_substituteValues( $route['where'], $params );
		} else
			$filters = array();

		return $this->esDB->Select( $route['select'], $route['fields'], $filters, @$route['join'] );
	}

	function _handle_Update( $route, $params ) {
		$where = $this->_substituteValues( $route['where'], $params, true );
		$updates = $this->_substituteValues( $route['set'], $params, true );


		return $this->esDB->Update( $route['update'], $where, $updates );
	}
	
	function _substituteValues( $substituteInto, $values, $requireAllFields=false ) {
		$output = array();

		foreach( $substituteInto as $key=>$val ) {
			$prefixChar = $val[0];
			$valueKey = substr($val, 1);
			
			switch( $prefixChar ) {
				case '_':
					$valueToSubstitute = @$values[ $valueKey ];
					if( $valueToSubstitute )
						$output[$key] = $valueToSubstitute;
					elseif ( $requireAllFields )
						throw new Exception('Field '.$valueKey.' is required.');						
				break;
				
				case '&':
					$output[$key] = @$values[ $valueKey ];
				break;
				
				case '+':
					$output[$key] = @$_SESSION[ $valueKey ];
				break;
				
				case ">":
					$output[$key] = array( 'cmp' => '>', 'val' => @$values[ $valueKey ] );
				break;
				case "<":
					$output[$key] = array( 'cmp' => '<', 'val' => @$values[ $valueKey ] );
				break;
				
				default:
					$output[$key] = $val;
				break;
			}
		}
		
		return $output;
	}
}
?>