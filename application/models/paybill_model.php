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
		$query = $this->db->insert ( 'LipaNaMpesaIPN', $input );
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

	function getipnaddress($tillno) {
		$this->db->query ( 'Use mobileBanking' );

		$this->db->select('ipn_address,till_model_id,username,password');
		$this->db->from('IPN_details');
		$this->db->join('TillModel','TillModel.id = IPN_details.till_model_id','INNER');
		$this->db->where('tillNo', $tillno);
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