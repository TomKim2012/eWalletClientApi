<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Member_Model extends CI_Model {
	function __construct() {
		parent::__construct ();
		$this->db->query('Use naekana');
	}
	
	/*
	 * Repetition -Should find a solution to this immediately
	 */
	function getSingleMember($parameter, $value) {
		
		$this->db->where ( array (
				$parameter => $value 
		) );
		$query = $this->db->get ( 'MembersDetails' );
		
		// print_r($query->result());
		/* $fullNames = trim ( (isset ( $query->row ()->refno )) ? ($query->row ()->refno) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->clname )) ? ($query->row ()->clname) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->middlename )) ? ($query->row ()->middlename) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->clsurname )) ? ($query->row ()->clsurname) : "N/a" );
		 */
			
		$memberData = array (
				'firstName' => trim ( (isset ( $query->row ()->Firstname )) ? ($query->row ()->Firstname) : "N/a" ),
				'middleName' => trim ( (isset ( $query->row ()->Middlename )) ? ($query->row ()->Middlename) : "N/a" ),
				'lastName' => trim ( (isset ( $query->row ()->Othernames )) ? ($query->row ()->Othernames) : "N/a" ),
				'MemberNo' => trim ( (isset ( $query->row ()->MemberNo )) ? ($query->row ()->MemberNo) : "N/a" ) 
		);
		
		return $memberData;
	}
	
	function getVehicleNo_by_id($businessNo){
		$this->db->query('Use naekana');
		$this->db->where ( array (
				'businessNo' => $businessNo,
				'Blocked'=>0
		) );
		$query = $this->db->get('MemberVehicleNo');

		return $query->row();
	}
	
	function getVehicles($memberNo){
		$this->db->where ( array (
				'memberNo' => $memberNo,
				'Blocked'=>0
		) );
		$query = $this->db->get('MemberVehicleNo');
		
		//echo $this->db->last_query();
		//print_r($query->result_array());
		
		$vehicleList= $query->result_array();
		
		$businessNos=array();
		foreach ($vehicleList as $row) {
			$data=array('VehicleNo' => $row['VehicleNo'],
						'businessNo' => $row['businessNo'],
			);
			array_push($businessNos, $data);
		}
		return $businessNos;
	}
	
	function getTotals($businessNos){
		$this->db->query('Use mobileBanking');
		$response=array();
		foreach ($businessNos as $row) {
			$this->db->select_sum('mpesa_amt');
			$this->db->where ( array (
					'business_number' => $row['businessNo'],
			) );
			$query = $this->db->get('mTransportIPN');
			
			$amount= $query->row()->mpesa_amt;
			
			$data=array('VehicleNo' => $row['VehicleNo'],
					'totals' => $amount,
			);
			array_push($response, $data);
		}
		return $response;
	}
	
}