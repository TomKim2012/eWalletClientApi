<?php
defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );
class Member_Model extends CI_Model {
	function __construct() {
		parent::__construct ();
	}
	
	function getSingleCustomer($parameter, $value) {
		$this->db->where ( array (
				$parameter => $value 
		) );
		$query = $this->db->get ( 'Client' );
		
		// print_r($query->result());
		$fullNames = trim ( (isset ( $query->row ()->refno )) ? ($query->row ()->refno) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->clname )) ? ($query->row ()->clname) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->middlename )) ? ($query->row ()->middlename) : "N/a" ) . " " .
				trim ( (isset ( $query->row ()->clsurname )) ? ($query->row ()->clsurname) : "N/a" );
			
		$custData = array (
				'firstName' => trim ( (isset ( $query->row ()->clname )) ? ($query->row ()->clname) : "N/a" ),
				'middleName' => trim ( (isset ( $query->row ()->middlename )) ? ($query->row ()->middlename) : "N/a" ),
				'lastName' => trim ( (isset ( $query->row ()->clsurname )) ? ($query->row ()->clsurname) : "N/a" ),
				'fullNames' => $fullNames,
				'refNo' => trim ( (isset ( $query->row ()->refno )) ? ($query->row ()->refno) : "N/a" ),
				'mobileNo' => trim ( (isset ( $query->row ()->phone )) ? ($query->row ()->phone) : "N/a" ),
				'customerId' => trim ( (isset ( $query->row ()->clcode )) ? ($query->row ()->clcode) : "N/a" ) 
		);
		return $custData;
	}
	
	function getTills($custId){
		$this->db->query('Use MergeFinals');
		$query = $this->db->query("select docnum from clientdoc where clientcode='".$custId.
								   "' AND priority>0");
	
		$tillList= $query->result_array();
	
		$businessNos=array();
		foreach ($tillList as $row) {
			$data=array(
					'businessNo' => $row['docnum']
			);
			array_push($businessNos, $data);
		}
		return $businessNos;
	}
	
	function getOwner_by_id($businessNo) {
		$query = $this->db->query ( "select businessName,phoneNo from LipaNaMpesaTills".
									" where tillNo='".$businessNo."'" );
		
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
			
			return $amount;
	}
	
	function getTotals($businessNos) {
		$response = array ();
		$this->db->query('Use mobileBanking');
		
		foreach ( $businessNos as $row ) {
			$this->db->select('businessName');
			$this->db->select_sum ('mpesa_amt');
			$this->db->from('LipaNaMpesaIPN');
			$this->db-> join('LipaNaMpesaTills',
							 'LipaNaMpesaIPN.business_number=LipaNaMpesaTills.tillNo');
			$this->db->where ( array (
					'business_number' => trim($row ['businessNo'])
			) );
			$this->db->group_by("businessName");
			
			$query = $this->db->get();
			//echo $this->db->last_query();
			
			$results = $query->row_array();
			
			$data = array (
					'business_name' => $results ['businessName'],
					'totals' => $results['mpesa_amt'] 
			);
			array_push ( $response, $data );
		}
		return $response;
	}
}