<?php
class Paybill_model extends CI_Model {
	/*PAYBILL Custom Function */
	function record_transaction($input){
		$this->db->insert('transactions', $input);
		return "OK|Thankyou.We have received your payment. Pioneer FSA.";
	}
}	
?>