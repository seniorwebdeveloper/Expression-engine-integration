<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Archive Class
*
* @package ExpressionEngine
* @category Module
* @author Prefix Technologies
* @link http://www.prefix.co.za/
* @copyright Copyright (c) 2011 Prefix Technologies
* @license http://creativecommons.org/licenses/MIT/ MIT License
*
*/

class Pmailer_archive_mcp
{
	/**
	* Constructor
	*
	* @access public
	*/

	Function Pmailer_archive_mcp()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
		
		// load model
		$this->EE->load->model('form_model','form');
		
		// settings
		$this->EE->cp->set_variable('cp_page_title', lang('pmailer_archive_module_name') );
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

/* End of file mcp.pmailer_archive.php */
/* Location: ./system/expressionengine/third_party/modules/pmailer_archive/mcp.pmailer_archive.php */