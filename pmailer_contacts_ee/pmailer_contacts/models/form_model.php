<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Form Module
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
require_once PATH_THIRD .'pmailer_contacts/library/functions.class' .EXT;

class Form_model extends CI_Model
{
	/**
	* Constructor
	*
	*/
	function Form_model()
	{
		//parent::CI_Model();
		parent::__construct();
    }
    
	/**
	* Get All Entries
	*
	*/
    function get_all()
    {

    }
    
	/**
	* Insert from form
	*
	*/
	function insert($data=false)
	{
		if(is_array($data))
		{
			return true;
		}
	}
	// ------------------------------------------------------------------------
}
?>