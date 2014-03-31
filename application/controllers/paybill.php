<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
require APPPATH . '/libraries/AfricasTalkingGateway.php';

class Paybill extends CI_Controller {
	function Paybill() {
		parent::__construct ();
		date_default_timezone_set ( 'Africa/Nairobi' );
		$this->load->library ( 'CoreScripts' );
		$this->load->model ( 'Paybill_model', 'ezauth' );
		$this->load->model ( 'Member_model','members' );
		$this->load->helper ( 'file' );
	}
	function index() {
		// Log the details
		$myFile = "application/controllers/mpesalog.txt";
		$input = $this->input->get ( NULL, TRUE );
		write_file ( $myFile, "=============================\n", 'a+' );
		foreach ( $input as $var => $value ) {
			if (! write_file ( $myFile, "$var = $value\n", 'a+' )) {
				echo "Unable to write to file!";
			}
		}
		
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
				'mpesa_sender' => $this->input->get ( 'mpesa_sender' ) 
		);
		$user = $this->input->get ( 'user' );
		$pass = $this->input->get ( 'pass' );
		
		if ($user == 'mTransport' && $pass == 'transport@2014') {
			if ($inp ['id']) {
				$transaction_registration = $this->ezauth->record_transaction ( $inp );
				echo $transaction_registration;
				
				//Send SMS to Client
				$tDate = date ("d/m/Y");
				$tTime = date("h:i A");
				$vehicleNo = $this->members->getVehicleNo_by_id($inp['business_number']);
				$message =  "Dear ".$inp['mpesa_sender'].
							",Your fare of Kshs. ".$inp['mpesa_amt'].
							" has been received on ".$tDate." at ".$tTime.
							".Thank-you for travelling with saccoName ( ".$vehicleNo.
							" ). Customer care no. 0729472421";

				$sms_feedback = $this->corescripts->_send_sms2 (substr( $inp['mpesa_msisdn'], 2 ), $message );

				if($sms_feedback){
					echo ".Sms sent to customer";
				}else{
					echo ".SMS not sent to customer";
				}
				
			} else {
				echo "FAIL|No transaction details were sent";
			}
		} else {
			echo "FAIL|The payment could not be completed at this time.Incorrect username / password combination. Pioneer FSA";
		}
	}
}

?>