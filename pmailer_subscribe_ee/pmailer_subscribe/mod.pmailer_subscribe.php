<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Form Class
*
* @package ExpressionEngine
* @category Module
* @author Prefix Technologies
* @link http://www.prefix.co.za/
* @copyright Copyright (c) 2011 Prefix Technologies
* @license http://creativecommons.org/licenses/MIT/ MIT License
*
*/

require_once PATH_THIRD .'pmailer_subscribe/library/pmailer_subscription_api' .EXT;

class Pmailer_subscribe
{
	function Pmailer_subscribe()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
		// load CI libraries / helpers etc
		$this->EE->load->library('form_validation');
		$this->EE->load->helper('url');
		$this->EE->load->model('form_model');
		$this->EE->load->library('javascript');
		
		// settings
		$this->settings['post_url'] = $_SERVER['PHP_SELF'];
		
		// validation rules
		$this->validation_rules = array(
			array(
				'field' => 'fname',
				'label' => 'First Name'
			),
			array(
				'field' => 'lname',
				'label' => 'Last Name'
			),
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'required|valid_email'
			)
		);
		
	}
	
	/**
	* Create form - main function
	*
	*/
	function create_form()
	{
		//get saved lists for smart form
		$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
		$row = $query->row();
		$lists = unserialize($row->mailinglist);
		
		
		
		// fetch params
		$return_url = $this->EE->TMPL->fetch_param('return');

		// if no return url, we are done here - exit
		if($return_url == '')
		{
			return "You must include a return url.";
			die();
		}

		// validation rules
		$this->EE->form_validation->set_rules($this->validation_rules);
	
		// validation
		if($this->EE->form_validation->run() == FALSE)
		{
			// error or not posted
			
			// variables to parse
			$variable_row = array(
				'errors' => validation_errors()
			);
			
			//get saved smartform info
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");
			$row = $query->first_row();
			$numrows= $query->num_rows();
			
			if($numrows == 0)
			{
				$savedsmartform = "No";
				$savedpageviedelay = "";
			}
			else
			{
				$savedsmartform = $row->smartformenabled;
				$savedpageviewdelay = $row->pageviewdelay;
			}
		
			// output
			
			//include scripts and styles
			$output = '<script type="text/javascript">
							var savedsmartform = "' . $savedsmartform . '";
							var savedpageviewdelay = "' . $savedpageviewdelay . '";
					    </script>';
			$output .= '<script type="text/javascript">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/javascript/jquery-1.6.1.js').'</script>';
			$output .= '<script type="text/javascript">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/javascript/jquery.validate.min.js').'</script>';
			$output .= '<script type="text/javascript">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/javascript/smartform.js').'</script>';
			$output .= '<script type="text/javascript">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/javascript/jquery-ui.min.js').'</script>';
			
			
			
			$output .= '<style type="text/css">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/css/jquery-ui.css').'</style>';
			$output .= '<style type="text/css">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/css/styles.css').'</style>';

			$output .= '<form action="'.$this->settings['post_url'].'" method="post" id="newsletterform">';
			$output .= $this->EE->TMPL->parse_variables_row($this->EE->TMPL->tagdata, $variable_row);
			$output .= '</form>';
			$output .= 'Newsletters secured by <a href="http://www.pmailer.co.za/esubscribe.php" target="_blank">SafeSubscribe</a>';
			
			//smart form
			$output .= '<div id="pmailer_smart_form" style="display:none">
							<form id="pmailer_subscription_smart_form">
							
								<table width="100%">
									<tr>
										<td width="115"><span class="smartformlabel">First Name:</span></td>
										<td><input type="text" name="sffname" id="sfemail" /></td>
									</tr>
									<tr>
										<td width="115"><span class="smartformlabel">Last Name:</span></td>
										<td><input type="text" name="sflname" id="sfemail" /></td>
									</tr>
									<tr>
										<td width="115"><span class="smartformlabel">Email Address:</span></td>
										<td><input type="text" name="sfemail" id="sfemail" class="required email" /></td>
									</tr>
								</table>
						</div>';
			
			$output .= '<script type="text/javascript">'.file_get_contents(PATH_THIRD.'pmailer_subscribe/javascript/functions.js').'</script>';
			
			return $output;
		}
		else
		{
			// success
			
		
			// build data array of posted values
			$data = array(
				'email' => $this->EE->input->post('email'),
				'fname' => $this->EE->input->post('fname'),
				'lname' => $this->EE->input->post('lname')
			);

			//send to pmailer api
			//get saved api info
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");		
			$row = $query->row();
			$apikey = $row->apikey;
			$apiurl = $row->apiurl;
			$lists = unserialize($row->mailinglist);
			
			//send to pmailer api
			$contactdetails = array(
									'contact_name' => $this->EE->input->post('fname'),
	 								'contact_lastname' => $this->EE->input->post('lname'),
	 								'contact_email' => $this->EE->input->post('email')
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
			
			// redirect to thank you
			redirect($return_url);
		}
		
		
	}
	
	
}

	if(isset($_POST['sfemail']))
	{
		$sfemail = $_POST['sfemail'];
		$sffname = $_POST['sffname'];
		$sflname = $_POST['sflname'];
		$json = array();
		
		if($sfemail == "")
		{
			$json['status'] = "error";
			$json['message'] = "empty";
		}
		elseif(!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$sfemail))
		{
			$json['status'] = "error";
			$json['message'] = "invalid";
		}
		else 
		{	
			$query = $this->EE->db->query("SELECT * FROM exp_pmailer_subscription_settings");		
			$row = $query->row();
			$apikey = $row->apikey;
			$apiurl = $row->apiurl;
			$lists = unserialize($row->mailinglist);
			
			//send to pmailer api
			$contactdetails = array('contact_name' => $sffname,
									'contact_lastname' => $sflname,
	 								'contact_email' => $sfemail
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
			$data = array(
				'email' => $sfemail
			);
			
			$sql = $this->EE->db->insert_string('exp_pmailer_subscription_entries', $data);
			$this->EE->db->query($sql);
			
			$json['status'] = "success";
		}
		
		$url = "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		$json['redirect'] = $url;
		$encoded = json_encode($json);
		die($encoded);
	}

/* End of file mod.form.php */
/* Location: ./system/expressionengine/third_party/pmailer_subscribe/mod.pmailer_subscribe.php */