<?php

	error_reporting(E_ALL);
	
require_once(dirname(__FILE__) . '/esDB/esDB.inc.php');
require_once(dirname(__FILE__) . '/esAPI/esAPI_DB.inc.php');



$api = new esAPI_DB();
$api->routeClass = new routes();
$api->Listen();



class routes {
	function User_New( $api ) {
		$result = $api->ExecRoute( 'User_Create', array( 'username' => $_REQUEST['username'] ) );
		
		$_SESSION['userId'] = $result;
		$_SESSION['username'] = $_REQUEST['username'];
		
		return $_SESSION;
	}

	function LogOut( $api ) { $_SESSION = array(); return $_SESSION; }
	
	function WhoAmI( $api ) { return $_SESSION; }
	
	function Event_TimeSlot_SetAll( $api ) {
		$eventId = $_REQUEST['eventId'];
		$stageId = $_REQUEST['stageId'];
		$newTimeslots = json_decode($_REQUEST['timeslots'], true);
		
		$api->ExecRoute( 'Event_TimeSlot_DeleteAll', array('eventId' => $eventId, 'stageId' => $stageId) );
		
		foreach( $newTimeslots as $timeslot ) {
			$api->ExecRoute( 'Event_TimeSlot_Create', array(
				"performerId" => $timeslot['performerId'],
				"eventId"     => $eventId,
				"end"         => $timeslot['end'],
				"start"       => $timeslot['start'],
				"stageId"     => $timeslot['stageId']
			) );
		}
		
		return $api->ExecRoute( 'Event_TimeSlot_Get', array('eventId' => $eventId) );
	}
	
	function Performer_Create_WithNewPerson( $api ) {
		$details = array(
			'nameFirst'     => $_REQUEST['nameFirst'],
			'nameLast'      => $_REQUEST['nameLast'],
			'performerName' => $_REQUEST['performerName'],
			'facebookUrl'   => $_REQUEST['facebookUrl']
		);
		
		$newPersonId = $api->ExecRoute( 'Person_Create', $details );
		
		$details['personId'] = $newPersonId;
		
		$newPerformerId = $api->ExecRoute( 'Performer_Create', $details );
		
		return $newPerformerId;
	}
}
?>