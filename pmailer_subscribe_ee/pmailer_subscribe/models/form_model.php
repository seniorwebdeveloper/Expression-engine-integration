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

require_once PATH_THIRD .'pmailer_subscribe/library/pmailer_subscription_api' .EXT;
require_once PATH_THIRD .'pmailer_subscribe/library/functions.class' .EXT;

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
    	$query = $this->db->get('exp_pmailer_subscription_entries');
		return $query->result_array();
    }
    
	/**
	* Insert from form
	*
	*/
	function insert($data=false)
	{
		if(is_array($data))
		{
			$this->db->insert('exp_pmailer_subscription_entries', $data);
			return true;
		}
	}
	// ------------------------------------------------------------------------
}
?>