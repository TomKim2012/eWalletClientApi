<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
require APPPATH . '/libraries/AfricasTalkingGateway.php';
class Paybill extends CI_Controller {
	function Paybill() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Paybill_model', 'transaction' );
		$this->load->model ( 'Member_model', 'members' );
		$this->load->helper ( 'file' );
	}
	function index() {
		// Get the input details
		$inp = array (
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
		 * Hard-Code For Paybill:510513() and 510512(Pioneer Shares)
		 * Paybill Transactions
		 */
		if ($inp ['business_number'] == '510513' || $inp ['business_number'] == '510512') {
			$firstName = $this->getFirstName ( $inp ['mpesa_sender'] ); // JOASH NYADUNDO
			$phoneNumber = $this->format_number ( $inp ['mpesa_msisdn'] );

			//Send message to customer who deposited.
			$message ="Dear ". $firstName .", MPESA deposit of ". $inp['mpesa_amt'].
					" confirmed. Invest as low as Ksh 5000 in our fixed deposit ".
					"or real estate fund and get upto 18% guaranteed returns.";
			$sms_feedback = $this->corescripts->_send_sms2 ($phoneNumber, $message);

		} else {
			/*Should be sorted asap
			 we are making account number to be the same as business number;

			*/

			$inp['mpesa_acc']=$inp['business_number'];
			//$inp ['mpesa_acc'] = 'N/A';
		}
		
		if (($user == 'pioneerfsa' && $pass == 'financial@2013') || ($user = 'mTransport' && $pass = 'transport@2014')) {
			if ($inp ['id']) {
				$transaction_registration = $this->transaction->record_transaction ( $inp );
				echo $transaction_registration;
				
				// Send SMS to Client
				$tDate = date ( "d/m/Y" );
				$tTime = date ( "h:i A" );
				$till = $this->members->getOwner_by_id ( $inp ['business_number'] );
				$balance = $this->members->getTillTotal ( $inp ['business_number'] );
				
				$message = "Dear " . $this->truncateString( $till ['businessName'] ) . ", transaction " . $inp ['mpesa_code'] . " of Kshs. " 
						. number_format ( $inp ['mpesa_amt'] ) . " received from " . $this->truncateString($inp ['mpesa_sender']) 
						. " on " . $tDate . " at " . $tTime . ". New Till balance is Ksh " . $balance;
				
				// echo $message;
				if ($till ['phoneNo']) {
					$smsInput = $this->corescripts->_send_sms2 ( $till ['phoneNo'], $message );
					
					// Persist sms Log
					$smsInput ['transactionId'] = $inp ['mpesa_code'];
					$smsInput ['tstamp'] = date ( "Y-m-d G:i" );
					$smsInput ['message'] = $message;
					$smsInput ['destination'] = $till ['phoneNo'];
					
					$this->transaction->insertSmsLog ( $smsInput );
					
					if ($smsInput ['status']) {
						echo " and sms sent to customer";
					} else {
						echo " sms not sent to customer";
					}
				} else {
					echo "The Till Phone details are not saved";
				}
			} else {
				echo "FAIL|No transaction details were sent";
			}
		} else {
			echo "FAIL|The payment could not be completed at this time.Incorrect username / password combination. Pioneer FSA";
		}
	}
	function getFirstName($names) {
		$fullNames = explode ( " ", $names );
		$firstName = $fullNames [0];
		$customString = substr ( $firstName, 0, 1 ) . strtolower ( substr ( $firstName, 1 ) );
		return $customString;
	}
	
	function truncateString($content){
		$truncated="";
		if (strlen ( $content ) > 15) {
			$truncated = substr ( $content, 0, 15 ) . "** ";
		}else{
			$truncated=$content;
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