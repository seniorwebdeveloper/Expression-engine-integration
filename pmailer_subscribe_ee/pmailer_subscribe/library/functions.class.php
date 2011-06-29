<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Functions
{
	function functions()
	{
		$this->EE =& get_instance();
	}
	
	//functions
	function saveApiInfo($apiurl, $apikey)
	{
		$this->EE->load->dbforge();
		$data = array(
			'apiurl' => $apiurl,
			'apikey' => $apikey
		);
		
		$where = "settings_id = 1";
		$this->EE->db->update('exp_pmailer_subscription_settings', $data, $where); 
	}
	
	function getApiKey()
	{
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
		$row = $query->first_row();
		
		if($row->apikey == "")
		{
			$savedapikey = "";
			$savedurl = "";
		}
		else
		{
			$savedapikey = $row->apikey;
			$savedurl = $row->apiurl;
		}

		return array ('savedapi' => $savedapikey, 'savedurl' => $savedurl);
	}
	
	function getMailingLists()
	{
		$apierror = false;
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
		
		$row = $query->row(); 
		$apikey = $row->apikey;
		$apiurl = $row->apiurl;
		
		$api = new PMailerSubscriptionApiV1_0($apiurl, $apikey);
		try
		{
 			$lists = $api->getLists(); // returns an array of lists
		}
		catch(Exception $e)
		{
			echo '<span style="color:red; font-weight:bold;">Error: ',  $e->getMessage(), "</span><br />";
			$apierror = true;
		}
 		
		if($apierror != true)
		{
			//check for saved lists
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
			$row = $query->first_row();
			$numrows= $query->num_rows();
			
			if($numrows == 0)
			{
				$savedlists = "";
				$savedsmartform = "";
				$savedpageviedelay = "";
			}
			else
			{
				$savedlists = unserialize($row->mailinglist);
				$savedsmartform = $row->smartformenabled;
				$savedpageviewdelay = $row->pageviewdelay;
			}
			
			$count = count($savedlists);
		
			$numcols = 3; // how many columns to display
			$numcolsprinted = 0; // no of columns so far
			
			print "<table width='100%' class='mainTable padTable'>
					<thead><tr><th>Mailing Lists and Smart Forms</th></tr></thead>
					<tbody><tr><td>
					<table width='100%'><tr><td colspan='3' style='border:none !important;'>
					<strong>Select the mailing list(s) you want your entries to be subscribed to:</strong></td></tr><tr>";
			
			foreach($lists['data'] as $key => $lists) 
			{
				$checked = "";	
				for($i = 0; $i < $count; $i++)
				{
					if($lists['list_id'] == $savedlists[$i])
					{
						$checked = "checked = 'checked'";
					}
				}
				
				$output = '<input type="checkbox"'.$checked.' name="'.$lists['list_id'].'" value="'.$lists['list_id'].'" />&nbsp;'.$lists['list_name'].'&nbsp;&nbsp;';
				
				if ($numcolsprinted == $numcols) 
				{
					print "</tr>\n<tr>\n";
					$numcolsprinted = 0;
				}
				// output row from database
				echo "<td style='border:none !important;'>$output</td>\n";
				$numcolsprinted++;
			}
			echo "</tr></tbody></table></td></tr>
				<tr><td>
					<table width='100%'>
					<tr><td colspan='2' style='border:none !important;'><strong>Smart Forms:</strong></td></tr>
					<tr>
					<td style='border:none !important; width:50%;'>Enable Smart Form: <select name='enablesmartform'>";
					
					if($savedsmartform == "No")
					{
						echo "<option value='Yes'>Yes</option><option value='No' selected='selected'>No</option>";
					}
					else
					{
						echo "<option value='Yes'>Yes</option><option value='No'>No</option>";
					}
						
						
						
			echo "</select></td>
					<td style='border:none !important; width:50%;'>Page-View Delay: <input type='text' name='pageviewdelay'  style='width:80px' value='" . $savedpageviewdelay . "' /></td>
					</tr>
					</table>
				</td></tr>
				<tr>
					<td>
						<button type='button' id='savelists' class='submit' name='savelists' onclick='saveMailingListsJs();'>Save Lists and Smart Form Settings</button>
						<input type='hidden' name='savelistssubmitted' value='savelistssubmitted' />
					</td></tr>";
			echo "</table>";
		}
	}
	
	function saveListsToDB($postedData)
	{
		unset($postedData['savelistssubmitted']); 
		
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
		
		$row = $query->row(); 
		
		//reset array indexes
		$smartformenabled =  $postedData['enablesmartform'];
		$pageviewdelay =  $postedData['pageviewdelay'];
		
		unset($postedData['enablesmartform']); 
		unset($postedData['pageviewdelay']);
		$resetPostedData=array_merge(array(),$postedData);
		
		$this->EE->load->dbforge();
		$data = array(
			'mailinglist' => serialize($resetPostedData),
			'smartformenabled' => $smartformenabled,
			'pageviewdelay' => $pageviewdelay
		);
		
		$where = "settings_id = 1";
		$this->EE->db->update('exp_pmailer_subscription_settings', $data, $where);
	}
}

?>