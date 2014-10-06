<?php
class Paybill_model extends CI_Model {
	
	function record_transaction($input){
		$this->db->query('Use mobileBanking');
		$query=$this->db->insert('LipaNaMpesaIPN', $input);
		if($query){
		return "OK|Thankyou, IPN has been successfully been saved.";
		}
	}
	
}	
?>