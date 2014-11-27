<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
// Receiving messages boils down to reading values in the POST array
// This example will read in the values received and compose a response.

// 1.Import the helper Gateway class
require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/AfricasTalkingGateway.php';
class Lipasms extends REST_Controller {
	function __construct() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'curl' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Member_Model', 'members' );
	}
	function custSms_get() {
		                                   
		// Add Balance from the text
		if ($this->get ( "clCode" )) {
			
			$clCode = $this->get ("clCode");
			$custData = $this->members->getSingleCustomer ( 'clCode', $clCode );
			
			$response = $this->corescripts->getTotals($clCode );
			
			//print_r($response);
			if(empty($response)){
				$message = 'Dear Customer, you dont have any registered tills.'.
							'Kindly call branch or agent to get one';
				$this->corescripts->_send_sms2 ( $custData['mobileNo'], $message);
				return;
			}else if($response[0]['count'] == 0){
				$message = "Dear " . $custData ['firstName'] . 
				", There were no Lipa Na Mpesa transactions for your tills today.";
			}else{
			
			// //---------------Compose the SMS-----------------------------------
			//$tDate = date ( "d/m/Y" );
			$tTime = date ( "h:i A" );
			$message = "Dear " . $custData ['firstName'] . ", Your Lipa Na Mpesa Summary as at ".$tTime . ":";
			$counter = 1;
			foreach ( $response as $row ) {
				$message .= "<" . $counter ++ . "." . $row ['business_name'] . "- KES " .
				number_format ( $row ['totals'] ) . ">";
			}
			}
			//echo $message;
			$sms_feedback = $this->corescripts->_send_sms2 ( $custData['mobileNo'], $message );
			if ($sms_feedback) {
				echo "Success";
			} else {
				echo "Failed";
			}
		} else {
			echo 'No client Code sent!';
		}
	}
}