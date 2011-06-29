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
		$this->EE->db->update('exp_pmailer_contacts_settings', $data, $where); 
	}
	
	function getApiKey()
	{
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
		$row = $query->first_row();
		
		if($row->apikey == "")
		{
			$savedapikey = "";
			$savedurl = "";
			$syncdate = "";
			$syncamount = "";
			$members = "";
		}
		else
		{
			$savedapikey = $row->apikey;
			$savedurl = $row->apiurl;
			$syncdate = $row->lastsyncdate;
			$syncamount = $row->lastsyncamount;
			
			$query = $this->EE->db->query("SELECT * FROM exp_members WHERE group_id=5");
			$members = $query->num_rows();
		}

		return array ('savedapi' => $savedapikey, 
						'savedurl' => $savedurl,
						'syncdate' => $syncdate, 
						'syncamount' => $syncamount, 
						'members' => $members, 
						);
	}
	
	function getMailingLists()
	{
		$apierror = false;
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
		
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
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
			$row = $query->first_row();
			$numrows= $query->num_rows();
			
			if($numrows == 0)
			{
				$savedlists = "";
			}
			else
			{
				$savedlists = unserialize($row->mailinglist);
				$doubleopinselected = $row->doubleoptin;
			}
			
			$count = count($savedlists);
		
			$numcols = 3; // how many columns to display
			$numcolsprinted = 0; // no of columns so far
			
			print "<table width='100%' class='mainTable padTable'>
					<thead><tr><th>Select Mailing Lists</th></tr></thead>
					<tbody><tr><td>
					<table width='100%'><tr>";
			
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
				echo "<td style='border:none !important; padding-left: 0px !important;'>$output</td>\n";
				$numcolsprinted++;
			}
			echo "	</tr>
					</table></td></tr>
					<tr>
						<td>
							Enable Double Opt-In: &nbsp;";
							
							if(isset($doubleopinselected))
							{
								if($doubleopinselected == "Yes")
								{
									echo "<select name='doubleoptin' id='doubleoptin'>
												<option value='No'>No</option>
												<option value='Yes' selected='selected'>Yes</option>
											</select>";
								}
								else
								{
									echo "<select name='doubleoptin' id='doubleoptin'>
											<option value='No' selected='selected'>No</option>
											<option value='Yes'>Yes</option>
										</select>";
								}
							}
							else
							{
								echo "<select name='doubleoptin' id='doubleoptin'>
										<option value='No' selected='selected'>No</option>
										<option value='Yes'>Yes</option>
									</select>";
							}
			echo "</td>
					</tr>
					<tr>
						<td>
							<button type='button' id='savelists' class='submit' name='savelists' onclick='saveMailingListsJs();'>Save Selected Lists and Settings</button>
							<input type='hidden' name='savelistssubmitted' value='savelistssubmitted' />
						</td>
					</tr>
					</tbody></table>";
		}
	}
	
	function saveListsToDB($postedData)
	{
		$doubleoptin = $postedData['doubleoptin'];
		unset($postedData['savelistssubmitted']); 
		unset($postedData['doubleoptin']);
		
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
		
		$row = $query->row(); 
		
		//reset array indexes
		$resetPostedData=array_merge(array(),$postedData);
		
		$this->EE->load->dbforge();
		$data = array(
			'mailinglist' => serialize($resetPostedData),
			'doubleoptin' => $doubleoptin
		);
		
		$where = "settings_id = 1";
		$this->EE->db->update('exp_pmailer_contacts_settings', $data, $where); 
	}
	
	function displaySync()
	{
		$showingSync = "";
		//check for saved lists
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
		
		$row = $query->row(); 
		$mailinglists = $row->mailinglist;
		
		if($mailinglists != "")
		{
			print "<table width='100%' class='mainTable padTable'>
					<thead><tr><th>Export Contacts</th></tr></thead>
					<tbody><tr><td>
					<p>Please note that depending on the speed of your internet connection and the number of members you currently have registered, exporting contacts can take a few minutes.<br /></p>
					<br />
					<!-- <input type='submit' id='synccontacts' class='submit' name='synccontacts' value='Sync Contacts' /> -->
					<button type='button' id='btnsynccontacts' class='submit' name='synccontacts' onclick='syncContacts();'>Export Contacts</button>
					<br /><br />
					<div id='apifeedback' style='display:none; border:1px solid #999999; padding:10px; '> </div>
					</td></tr></tbody>
					</table>";
		}
	}
	
	function getNumContacts()
	{
		$query = $this->EE->db->query("SELECT screen_name as contact_name, email as contact_email FROM exp_members WHERE group_id=5");
		$contacts = $query->result_array();
		$numcontacts = $query->num_rows();
		
		$encoded = json_encode($numcontacts);
		die($encoded);
	}
	
	function splitContacts()
	{
		$query = $this->EE->db->query("SELECT screen_name as contact_name, email as contact_email FROM exp_members WHERE group_id=5");
		$contacts = $query->result_array();
		$contacts_split = array_chunk($contacts, 30);
		
		$encoded = json_encode($contacts_split);
		die($encoded);
	}
	
	function processContacts()
	{
		$contacts = json_decode($_GET['processdata']);
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_contacts_settings");
		$row = $query->row(); 
		$doubleoptin = $row->doubleoptin;
		$list_ids = unserialize($row->mailinglist);
		$apiurl = $row->apiurl;
		$apikey = $row->apikey;
		
		if($doubleoptin == "No")
		{
			$status = "subscribed";
		}
		else
		{
			$status = "unconfirmed";
		}
		
		$api = new PMailerSubscriptionApiV1_0($apiurl, $apikey);
		$apiresponse = $api->createBatch($contacts, $list_ids, $status);
		
		if($apiresponse['status'] == "success")
		{
			$apisuccess = true;
		}
		else
		{
			$apisuccess = false;
			$apimessage = $apiresponse['message'];
		}
		
		$encoded = json_encode($apisuccess);
		die($encoded);
		
	}
}

?>