<?php
class Paybill_model extends CI_Model {
	/*PAYBILL Custom Function */
	function record_transaction($input){
		$query=$this->db->insert('mTransportIPN', $input);
		if($query){
		return "OK|Thankyou, IPN has been successfully been saved.";
		}
	}
}	
?>