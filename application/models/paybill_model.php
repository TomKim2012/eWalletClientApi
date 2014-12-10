<?php
class Paybill_model extends CI_Model {
	function record_transaction($input) {
		$this->db->query ( 'Use mobileBanking' );
		
		// Validate IP:
		if ($this->validateIP ( $input ['ipAddress'] )) {
			$input['isApproved'] = true;
		}else{
			$input['isApproved']=false;
		}
		$query = $this->db->insert ( 'LipaNaMpesaIPN', $input );
		if ($query) {
			return "OK|Thankyou, IPN has been successfully been saved.";
		}else{
			return "Fail | Something went wrong while performing the query";
		}
	}
	function validateIP($ipAddress) {
		$this->db->where ( array (
				'SettingKey' => 'IPNServer' 
		) );
		$query = $this->db->get ('SettingModel' );
		
		if ($query->num_rows () > 0) {
			if ($query->row ()->SettingValue == $ipAddress) {
				return true;
			}
		} else {
			echo "No Valide IPNserverURL set in the database";
			return false;
		}
	}
}
?>