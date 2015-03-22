<?php
class Paybill_model extends CI_Model {
	function record_transaction($input) {
		$this->db->query ( 'Use mobileBanking' );
		
		// Validate IP:
		if ($this->validateIP ( $input ['ipAddress'] )) {
			$input ['isApproved'] = true;
		} else {
			$input ['isApproved'] = false;
		}
		

		//Temporary Code for my clients before getting server
		if(($input['business_number']=='885850')||($input['business_number']=='354750')){
			$query = $this->db->insert ( 'TemporaryIPN', $input );
		}else{
			$query = $this->db->insert ( 'LipaNaMpesaIPN', $input );
		}
		
		if ($query) {
			return "OK|Thankyou, IPN has been successfully been saved.";
		} else {
			return "Fail | Something went wrong while performing the query";
		}
	}
	function validateIP($ipAddress) {
		$this->db->where ( array (
				'SettingKey' => 'IPNServer' 
		) );
		$query = $this->db->get ( 'SettingModel' );
		
		if ($query->num_rows () > 0) {
			if ($query->row ()->SettingValue == $ipAddress) {
				return true;
			}
		} else {
			echo "No Valide IPN serverURL set in the database";
			return false;
		}
	}
	function insertSmsLog($input) {
		$query = $this->db->insert ( 'smsModel', $input );
		$smsLogId = $this->db->insert_id();
		$this->updateTransactionRecord($smsLogId, $input['transactionId']);

		if ($query) {
			return "Success|Sms Logged into database";
		} else {
			return "Fail | Fail to Log sms into db";
		}
	}
	
	function updateTransactionRecord($smsLogId,$transactionId){
		$updates = array (
				'smsStatus_FK' => $smsLogId
		);
		$this->db->where ( 'mpesa_code', $transactionId );
		$query = $this->db->update ( 'LipaNaMpesaIPN', $updates );
		echo "records updated";
	}
	
	function updateLog($messageId, $status) {
		$updates = array (
				'status' => $status 
		);
		$this->db->where ( 'messageId', $messageId );
		$query = $this->db->update ( 'smsModel', $updates );
		if ($query) {
			echo "updated sms Log";
		} else {
			echo "Failed to update sms Log";
		}
	}

	function getipnaddress($business_number) {
		$this->db->query ( 'Use mobileBanking' );

		$this->db->select('ipn_address,tillModel_id,username,password');
		$this->db->from('IPN_details');
		$this->db->join('TillModel','TillModel.id = IPN_details.tillModel_id','INNER');
		$this->db->where('business_number', $business_number);
		$query = $this->db->get();
		
		if ($query->num_rows () > 0) {
			return $query->row();

		} else {
			
			return false;
		}

	}

	function inseripnlog($ipnlog) {
		$this->db->query ( 'Use mobileBanking' );

		$query = $this->db->insert ( 'IPN_logs', $ipnlog );
	

		if ($query) {
			return true;
		} else {
			return false;
		}

	}
}
?>