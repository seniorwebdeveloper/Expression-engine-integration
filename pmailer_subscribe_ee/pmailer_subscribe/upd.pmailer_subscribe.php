<?php //if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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

class Pmailer_subscribe_upd
{
	var $version = '1.0';
	
	function Pmailer_subscribe_upd()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	
	/**
	* Module Installer
	*
	* @access public
	* @return bool
	*/
	function install()
	{
		//add module to expressionengine
		$this->EE->load->dbforge();
		
		$data = array(
			'module_name' => 'Pmailer_subscribe',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
		
		$this->EE->db->insert('modules', $data);
		
		$data = array(
			'class' => 'Form' ,
			'method' => 'index'
		);
		
		$this->EE->db->insert('actions', $data);
		
		//Create subscriptions table
		$fields = array(
			'entry_id' => array('type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE),
			'fname' => array('type' => 'varchar', 'constraint' => '250'),
			'lname' => array('type' => 'varchar', 'constraint' => '250'),
			'email' => array('type' => 'varchar', 'constraint' => '250'),
			'mailinglist' => array('type' => 'varchar', 'constraint' => '250')
		);
		
		
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('entry_id', TRUE);
		$this->EE->dbforge->create_table('pmailer_subscription_entries', TRUE);
		
		
		//create settings table
		$fields = array(
			'settings_id' => array('type' => 'int',
				'constraint' => '10',
				'unsigned' => TRUE,
				'auto_increment' => TRUE),
			'apikey' => array('type' => 'varchar', 'constraint' => '250'),
			'apiurl' => array('type' => 'varchar', 'constraint' => '250'),
			'mailinglist' => array('type' => 'varchar', 'constraint' => '250'),
			'smartformenabled' => array('type' => 'varchar', 'constraint' => '4'),
			'pageviewdelay' => array('type' => 'varchar', 'constraint' => '4')
		);
		
		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('settings_id', TRUE);
		$this->EE->dbforge->create_table('pmailer_subscription_settings', TRUE);
		
		//add a row to the settings table
		$data = array(
			'settings_id' => 1,
			'apikey' => "",
			'mailinglist' => "",
			'smartformenabled' => "",
			'pageviewdelay' => ""
		);
		$this->EE->db->insert('pmailer_subscription_settings', $data);
		
		return TRUE;
	}
	
	/**
	* Module Uninstaller
	*
	* @access public
	* @return bool
	*/
	function uninstall()
	{
		$this->EE->load->dbforge();
	
		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => 'Pmailer_subscribe'));
		
		//$this->EE->db->where('module_id', $query->row('module_id'));
		//$this->EE->db->delete('module_member_groups');
		
		$this->EE->db->where('module_name', 'Pmailer_subscribe');
		$this->EE->db->delete('modules');
		
		$this->EE->db->where('class', 'Pmailer_subscribe');
		$this->EE->db->delete('actions');
		
		// delete form submissions
		// note that by default this will DESTROY all of your form submission data
		$this->EE->dbforge->drop_table('pmailer_subscription_entries');
		$this->EE->dbforge->drop_table('pmailer_subscription_settings');
		
		return TRUE;
	}
	
	/**
	* Module Updater
	*
	* @access public
	* @return bool
	*/
	
	function update($current='')
	{
		return TRUE;
	}
}

/* END Class */

/* End of file upd.form.php */
/* Location: ./system/expressionengine/third_party/modules/pmailer_subscribe/upd.pmailer_subscribe.php */