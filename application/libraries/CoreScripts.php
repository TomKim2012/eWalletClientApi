<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
	// require APPPATH . '/libraries/AfricasTalkingGateway.php';
class CoreScripts {
	public function __construct() {
		$this->CI ()->load->library ( 'curl' );
		$this->CI ()->load->model ( 'Paybill_Model', 'transactions' );
		$this->CI ()->load->model ( 'Member_Model', 'members' );
	}
	public function CI() {
		$CI = & get_instance ();
		return $CI;
	}
	function updateCustomer($newMobile) {
		// updating customer Record
		$cust = array (
				'newMobile' => $newMobile 
		);
		// updating customer
		if (strlen ( $cust ['newMobile'] ) == 10) { // 0729472421
			$newInput = array (
					'phone' => $cust ['newMobile'] 
			);
			$this->CI ()->customers->UpdateCustomer ( $inp ['clCode'], $newInput );
		}
	}
	function getTotals($userId) {
		if ($userId == "") {
			return;
		}
		
		$businessNos = $this->CI ()->members->getTills ( $userId );
		$response = $this->CI ()->members->getTotals ( $businessNos );
		
		if ($response) {
			return $response;
		}
	}
	
	// ----------Function to send sms-------------------
	function _send_sms($recipient, $message) {
		$serverUrl = "http://api.smartsms.co.ke/api/sendsms/plain";
		
		if ($recipient == "") {
			return array (
					'error' => "Message not sent, No phoneNumber passed" 
			);
		}
		
		$recipient = "+254" . substr ( $recipient, 1 );
		
		$parameters = array (
				'user' => 'megarider',
				'password' => 'ZpmXSCdd',
				'sender' => 'PioneerFSA',
				'GSM' => $recipient,
				'SMSText' => $message 
		);
		
		$response = $this->CI ()->curl->simple_get ( $serverUrl, $parameters );
		
		// Validate Response
		// Ascertain -- the necessary return response is sent
		
		return true;
	}
	
	/* Africa Is Talking SMS-Sending */
	function _send_sms2($phoneNumber, $message) {
		if ($phoneNumber == "") {
			return array (
					'error' => "Message not sent, No phoneNumber passed" 
			);
		}
		$recipient = "+254" . substr ( $phoneNumber, 1 );
		
		// Create an instance of the gateway class
		$username = "TomKim";
		$shortCode = "PioneerFSA";
		$apiKey = "1473c117e56c4f2df393c36dda15138a57b277f5683943288c189b966aae83b4";
		$gateway = new AfricasTalkingGateway ( $username, $apiKey );
		
		try {
			// Send a response originating from the short code that received the message
			/*
			 * Bug:: If you put shortcode - It fails completely.
			 */
			
			$results = $gateway->sendMessage ( $recipient, $message, $shortCode );
			
			// print_r($results);
			
			// Read in the gateway response and persist if necessary
			$response = $results [0];
			$status = $response->status;
			$cost = $response->cost;
			$messageId = $response->messageId;
			
			$input = array (
					'status' => isset($status)?$status:"Failed",
					'cost' => substr ( $cost, 3 ),
					'destination' => $recipient,
					'message' => $message,
					'messageId' => $messageId 
			);
			return $input;
		} catch ( AfricasTalkingGatewayException $e ) {
			// Log the error
			$errorMessage = $e->getMessage ();
			echo $errorMessage;
			return false;
		}
	}
	function saveMiniStatement($clCode, $transactionType, $transactionAmount) {
		$inp = array (
				'clCode' => $clCode,
				'transaction_amount' => $transactionAmount,
				'transaction_type' => $transactionType 
		);
		$response = $this->CI ()->transactions->createTransaction ( $inp );
		return $response;
	}
}

/* End of file CoreScripts.php */