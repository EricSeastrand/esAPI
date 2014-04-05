<?php
session_start();

class esAPI {
	var $response;
	var $actions;
	var $routeClass = false;
	
	function __construct() {
		
	}
	
	function Listen() {
		if(! isset($_REQUEST['action']) )
			return; // Action wasn't even set at all. This is probably NOT an AJAX request.
		
		try{
			$this->_HANDLE_REQUEST( $_REQUEST['action'] );
			
		} catch( Exception $e ) {
			$this->_response_ERROR( $e );
		}
		
		$this->_response_SUCCESS();
	}
	
	function _HANDLE_REQUEST( $actionName ) {
		if( !$actionName )
			$this->_onBlankAction();
			
		$this->response = $this->_delegateToRouteClass( $actionName );
		
		if( $this->response !== null && $this->response !== true )
			return;
		
		$action = @$this->actions[ $actionName ];
		
		if( !$action )
			$this->_onUnknownAction();
		
		$this->response = $this->_REQUEST_HANDLER( $action, $_REQUEST );
		
	}
	
	function _delegateToRouteClass( $action ) {
		$handler = array( $this->routeClass, $action );
		if( is_callable($handler) )
			return call_user_func_array( $handler, array($this) );
		else
			return null;
	}
	
	function _REQUEST_HANDLER( $action ) {
		print_r( $action );
	}
		
	function _onUnknownAction(){
		throw new Exception('Unknown action name requested.');
	}
	
	function _onBlankAction() {
		throw new Exception('Action name was undefined.');
	}
	
	function _response_ERROR( $exception ) {
		error_log('Caught:'. print_r( $exception, true) );
		
		echo json_encode(array(
			'ok'     => false,
			'error'  => $exception->getMessage(),
			'result' => $this->response
		));
		exit;
	}
	
	function _response_SUCCESS() {
		if(@$_GET['return'])
			header('Location: '.$_SERVER['HTTP_REFERER']);
		else
			echo json_encode(array(
				'ok'     => true,
				'result' => $this->response
			));
		
		exit;
	}
	
}
?>
