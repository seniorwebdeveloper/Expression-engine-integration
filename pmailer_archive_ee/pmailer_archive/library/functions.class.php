<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Functions
{
	function functions()
	{
		$this->EE =& get_instance();
	}
	
	//functions
	function saveApiInfo($apiurl, $apikey, $pagination)
	{
		$this->EE->load->dbforge();
		$data = array(
			'apiurl' => $apiurl,
			'apikey' => $apikey,
            'pagination' => $pagination
		);
		
		$where = "settings_id = 1";
		$this->EE->db->update('exp_pmailer_archive_settings', $data, $where); 
	}
	
	function getApiKey()
	{
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_archive_settings");
		$row = $query->first_row();
		
		if($row->apikey == "")
		{
			$savedapikey = "";
			$savedurl = "";
            $savedpagination = $row->pagination;
		}
		else
		{
			$savedapikey = $row->apikey;
			$savedurl = $row->apiurl;
            $savedpagination = $row->pagination;
			
			$query = $this->EE->db->query("SELECT * FROM exp_members WHERE group_id=5");
			$members = $query->num_rows();
		}

		return array ('savedapi' => $savedapikey, 
						'savedurl' => $savedurl,
                        'savedpagination' => $savedpagination
						);
	}
}

?>