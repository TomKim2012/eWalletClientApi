<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
require APPPATH . '/libraries/AfricasTalkingGateway.php';
class Paybill extends CI_Controller {
	function Paybill() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Paybill_model', 'transaction' );
		$this->load->helper ( 'file' );
	}
	function index() {
		
		/**
		 * Extract IPN Parameters
		 */
		$parameters = array (
				'id' => $this->input->get ( 'id' ),
				'business_number' => $this->input->get ( 'business_number' ),
				'orig' => $this->input->get ( 'orig' ),
				'dest' => $this->input->get ( 'dest' ),
				'tstamp' => $this->input->get ( 'tstamp' ),
				'mpesa_code' => $this->input->get ( 'mpesa_code' ),
				'mpesa_acc' => $this->input->get ( 'mpesa_acc' ),
				'mpesa_msisdn' => $this->input->get ( 'mpesa_msisdn' ),
				'mpesa_trx_date' => $this->input->get ( 'mpesa_trx_date' ),
				'mpesa_trx_time' => $this->input->get ( 'mpesa_trx_time' ),
				'mpesa_amt' => $this->input->get ( 'mpesa_amt' ),
				'mpesa_sender' => $this->input->get ( 'mpesa_sender' ),
				'ipAddress' => $this->input->ip_address () 
		);
		
		$user = $this->input->get ( 'user' );
		$pass = $this->input->get ( 'pass' );
		
		/**
		 * **********************************
		 */
		/**
		 * Differentiation between Buy Goods and Paybill
		 * Buy Goods - has no account Number
		 * Paybill - always has an account Number
		 * *
		 */
		
		if (isset ( $parameters ['mpesa_acc'] )) {
			$firstName = $this->getFirstName ( $parameters ['mpesa_sender'] ); // JOASH NYADUNDO
			$phoneNumber = $this->format_number ( $parameters ['mpesa_msisdn'] );
			
			// Send message to customer who deposited.
			$message = "Dear " . $firstName . ", MPESA deposit of " . $parameters ['mpesa_amt'] . " confirmed.
					 Invest as low as Ksh 5000 in our fixed deposit " . "or real estate fund and get upto 18% guaranteed returns.";
			$this->sendSMS ( $phoneNumber, $message, $parameters ['mpesa_code'] );
		} else {
			/*
			 * Should be sorted asap
			 * we are making account number to be the same as business number because Pioneer's integration does not take
			 * into consideration empty account Number;
			 */
			$parameters ['mpesa_acc'] = $parameters ['business_number'];
		}
		
		/**
		 * Saving Parameters on successful Authentication
		 * Client should set the correct Parameters here
		 */
		
		if (($user == 'pioneerfsa' && $pass == 'financial@2013')) {
			if ($parameters ['id']) {
				$transaction_registration = $this->transaction->record_transaction ( $parameters );
				echo $transaction_registration;
			} else {
				echo "FAIL|No transaction details were sent";
			}
		} else {
			echo "FAIL|The payment could not be completed at this time.
					Incorrect username / password combination. Pioneer FSA";
		}
	}
	function getFirstName($names) {
		$fullNames = explode ( " ", $names );
		$firstName = $fullNames [0];
		$customString = substr ( $firstName, 0, 1 ) . strtolower ( substr ( $firstName, 1 ) );
		return $customString;
	}
	function sendSMS($phoneNo, $message, $mpesaCode) {
		$smsInput = $this->corescripts->_send_sms2 ( $phoneNo, $message );
		
		// Save SMS Log
		$smsInput ['transactionId'] = $mpesaCode;
		$smsInput ['tstamp'] = date ( "Y-m-d G:i" );
		$smsInput ['message'] = $message;
		$smsInput ['destination'] = $phoneNo;
		
		$this->transaction->insertSmsLog ( $smsInput );
		
		if ($smsInput ['status']) {
			echo " and sms sent to customer";
		} else {
			echo " sms not sent to customer";
		}
	}
	function truncateString($content) {
		$truncated = "";
		if (strlen ( $content ) > 15) {
			$truncated = substr ( $content, 0, 15 ) . "** ";
		} else {
			$truncated = $content;
		}
		return $truncated;
	}
	function format_Number($phoneNumber) {
		$formatedNumber = "0" . substr ( $phoneNumber, 3 );
		return $formatedNumber;
	}
	function deliveryCallBack() {
		$messageId = $this->input->post ( 'id' );
		$status = $this->input->post ( 'status' );
		$this->transaction->updateLog ( $messageId, $status );
	}
}

?>