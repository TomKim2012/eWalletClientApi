<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Member_Model extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	/*
	 * Repetition -Should find a solution to this immediately
	 */
	function getSingleMember($parameter, $value) {
		$this->db->where ( array (
				$parameter => $value 
		) );
		$query = $this->db->get ( 'MembersDetails' );
		
		$memberData = array (
				'firstName' => trim ( (isset ( $query->row ()->Firstname )) ? ($query->row ()->Firstname) : "N/a" ),
				'middleName' => trim ( (isset ( $query->row ()->Middlename )) ? ($query->row ()->Middlename) : "N/a" ),
				'lastName' => trim ( (isset ( $query->row ()->Othernames )) ? ($query->row ()->Othernames) : "N/a" ),
				'MemberNo' => trim ( (isset ( $query->row ()->MemberNo )) ? ($query->row ()->MemberNo) : "N/a" ) 
		);
		
		return $memberData;
	}
	function getVehicleNo_by_id($businessNo) {
		$this->db->query ( 'Use naekana' );
		$this->db->where ( array (
				'businessNo' => $businessNo,
				'Blocked' => 0 
		) );
		$query = $this->db->get ( 'MemberVehicleNo' );
		
		return $query->row ();
	}
	function getOwner_by_id($businessNo) {
		$query = $this->db->query ( "select businessName,phoneNo from LipaNaMpesaTills where tillNo='".$businessNo."'" );
		
		if ($query->num_rows () > 0) {
			return $query->row_array ();
		} else {
			return false;
		}
	}
	/*
	 * Total for a single Till
	 */
	function getTillTotal($businessNo){
		$this->db->select_sum ( 'mpesa_amt' );
			$this->db->where ( array (
					'business_number' => $businessNo,
					'tstamp'=>date ( "Y-m-d" )
			) );
			$query = $this->db->get ( 'LipaNaMpesaIPN' );
			
			$amount = $query->row()->mpesa_amt;
			
			echo $this->db->last_query();
			return $amount;
	}
	
	function getTotals($businessNos) {
		$response = array ();
		foreach ( $businessNos as $row ) {
			$this->db->select_sum ( 'mpesa_amt' );
			$this->db->where ( array (
					'business_number' => $row ['businessNo'] 
			) );
			$query = $this->db->get ( 'LipaNaMpesaIPN' );
			
			$amount = $query->row()->mpesa_amt;
			
			$data = array (
					'VehicleNo' => $row ['VehicleNo'],
					'totals' => $amount 
			);
			array_push ( $response, $data );
		}
		return $response;
	}
}