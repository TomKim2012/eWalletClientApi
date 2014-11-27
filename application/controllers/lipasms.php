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
		if ($this->get ( "phoneNumber" )) {
			
			$phone = $this->get ( "phoneNumber" );
			$custData = $this->members->getSingleCustomer ( 'phone', $phone );
			

			if (empty($custData ['userId'])) {
				$message = 'Dear Customer, your phoneNumber is not registered.' . 'Kindly contact your nearest branch';
				echo $message;
				$this->corescripts->_send_sms2 ( $phone, $message );
				return;
			}
			
			$response = $this->corescripts->getTotals ( $custData ['userId'] );
			
			 // print_r($response);
			 // return;
			 
			if (empty ( $response )) {
				$message = 'Dear Customer, you dont have any registered tills.' . 'Kindly call branch to be assigned a Till';
				echo $message;
				$this->corescripts->_send_sms2 ( $custData ['mobileNo'], $message );
				return;
			} else if ($response [0] ['count'] == 0) {
				$message = "Dear " . $custData ['firstName'] . ", There were no Lipa Na Mpesa transactions for your tills today.";
			} else {
				
				// //---------------Compose the SMS-----------------------------------
				// $tDate = date ( "d/m/Y" );
				$tTime = date ( "h:i A" );
				$message = "Dear " . $custData ['firstName'] . ", Today's Lipa Na Mpesa Summary as at " . $tTime . " is as follows:";
				$counter = 1;
				foreach ( $response as $row ) {
					$message .= "<" . $counter ++ . "." . $row ['business_name'] . "- KES " . number_format ( $row ['totals'] ) . ">";
				}
			}
			echo $message;
			$sms_feedback = $this->corescripts->_send_sms2 ( $custData['mobileNo'], $message );
			if ($sms_feedback) {
			echo "Success";
			} else {
			echo "Failed";
			}
		} else {
			echo 'No phone Number sent';
		}
	}
}