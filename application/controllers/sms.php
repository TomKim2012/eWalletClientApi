<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
// Receiving messages boils down to reading values in the POST array
// This example will read in the values received and compose a response.

// 1.Import the helper Gateway class
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/AfricasTalkingGateway.php';
class Sms extends REST_Controller {
	function __construct() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'curl' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Member_Model', 'members' );
		// $this->load->model ( 'Users_Model', 'users' );
	}
	function custSms_post() {
		// 2.Read in the received values
		$phoneNumber = $this->post ( "from" ); // sender's Phone Number
		$shortCode = $this->post ( "to" ); // The short code that received the message
		$text = $this->post ( "text" ); // Message text
		$linkId = $this->post ( "linkId" ); // Used To bill the user for the response
		$date = $this->post ( "date" ); // The time we received the message
		$id = $this->post ( "id" ); // A unique id for this message
		                            
		// Add Balance from the text
		
		if ($phoneNumber) {
			// 1. Use phoneNumber to get Client Code
			if ($phoneNumber) {
				$phoneNumber = "0" . substr ( $phoneNumber, 4 );
				$custData = $this->members->getSingleMember ( 'Mobile', $phoneNumber );
				
				if ($custData ['MemberNo'] == "N/a") {
					$message = "The phoneNumber you sent is not registered with the system. Kindly contact the Sacco for more details.";
					$myresponse = $this->corescripts->_send_sms2 ( $phoneNumber, $message );
					return;
				}
			}
			
			$response = $this->corescripts->getTotals ( $custData ['MemberNo'] );
			
			// //---------------Compose the SMS-----------------------------------
			$tDate = date ("d/m/Y");
			$tTime = date("h:i A");
			$message = "Dear " . $custData ['firstName'] . ", Your fare collections as at " . $tDate . " at " . $tTime
					  .":";
			$counter = 1;
			foreach ( $response as $row ) {
				$message .= "<". $counter ++ . ".".$row ['VehicleNo'] . " - Kshs " .number_format ( $row ['totals'] ).">";
			}
			
			//echo $message;
			$sms_feedback = $this->corescripts->_send_sms2 ( $phoneNumber, $message );
			if ($sms_feedback) {
				echo "Success";
			} else {
				echo "Failed";
			}
			
		} else {
			$message = 'Incorrect Format sent.Please add "Balance" to the Message text, then send again';
			$this->corescripts->_send_sms2 ( $phoneNumber, $message );
		}
	}
	
}