<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Contacts Class
*
* @package ExpressionEngine
* @category Module
* @author Prefix Technologies
* @link http://www.prefix.co.za/
* @copyright Copyright (c) 2011 Prefix Technologies
* @license http://creativecommons.org/licenses/MIT/ MIT License
*
*/

class Pmailer_contacts_mcp
{
	/**
	* Constructor
	*
	* @access public
	*/

	Function Pmailer_contacts_mcp()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
		// load model
		$this->EE->load->model('form_model','form');
		
		// settings
		$this->EE->cp->set_variable('cp_page_title', lang('pmailer_contacts_module_name') );
	}
	// --------------------------------------------------------------------

	/**
	* Main Page
	*
	* @access public
	*/
	
	function index()
	{
		// get form entries
		$entries = $this->EE->form->get_all();
		
		// view data
		$view_data['entries'] = $entries;
		
		return $this->EE->load->view('index', $view_data, TRUE);
	}
}

// END CLASS

/* End of file mcp.pmailer_contacts.php */
/* Location: ./system/expressionengine/third_party/modules/pmailer_contacts/mcp.pmailer_contacts.php */