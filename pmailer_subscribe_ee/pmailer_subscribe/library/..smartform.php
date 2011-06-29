<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	
	require '../../../../codeigniter/system/core/common.php';
	require_once 'pmailer_subscription_api.php';
	
	class Smartform
	{
		/**
		* Constructor
		*
		*/
		function Smartform()
		{
			$this->EE =& get_instance();
			$this->EE->load->dbforge();
		
			//$this->EE =& get_instance();
			//parent::__construct();
			
			echo "test";
			$email = "test123@mail.com";
			$json = array();
			
			if($email == "")
			{
				$json['status'] = "error";
				$json['message'] = "empty";
			}
			elseif(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email))
			{
				$json['status'] = "error";
				$json['message'] = "invalid";
			}
			else 
			{	
				saveSmartForm($email);
				$json['status'] = "success";
			}
			
			$encoded = json_encode($json);
			die($encoded);
		}
		
		function saveSmartForm($postedData)
		{
			$fname = "";
			$lname = "";
			
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");		
			$row = $query->row();
			$apikey = $row->apikey;
			$apiurl = $row->apiurl;
			$lists = unserialize($row->mailinglist);
			
			//send to pmailer api
			$contactdetails = array(
									'contact_name' => $this->EE->input->post('fname'),
	 								'contact_lastname' => $this->EE->input->post('lname'),
	 								'contact_email' => $this->EE->input->$postedData
								);
								
			$api = new PMailerSubscriptionApiV1_0($apiurl, $apikey);
			
			
			try
			{
	 			$lists = $api->subscribe($contactdetails, $lists); // returns an array of lists
			}
			catch(Exception $e)
			{
				echo '<span style="color:red; font-weight:bold;">Error: ',  $e->getMessage(), "</span><br />";
				$apierror = true;
			}
			
			// insert data into db
			$this->EE->form_model->insert($data);
		}
	}
?>