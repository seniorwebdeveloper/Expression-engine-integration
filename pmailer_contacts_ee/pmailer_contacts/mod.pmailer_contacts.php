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

require_once PATH_THIRD .'pmailer_contacts/library/pmailer_subscription_api' .EXT;

class Pmailer_contacts
{
	function Pmailer_contacts()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	
	/**
	* Create form - main function
	*
	*/
	function create_form()
	{

	}
}

/* End of file mod.pmailer_contacts.php */
/* Location: ./system/expressionengine/third_party/pmailer_contacts/mod.pmailer_contacts.php */