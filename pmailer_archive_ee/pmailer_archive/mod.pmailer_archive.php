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

require_once PATH_THIRD .'pmailer_archive/library/pmailer_subscription_api' .EXT;

class Pmailer_archive
{
	function Pmailer_archive()
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
        //get page limit
        $query = $this->EE->db->query("SELECT * FROM exp_pmailer_archive_settings");
		$row = $query->first_row();
        
        if($row->pagination == 0)
		{
			$limit = 3;
		}
		else
		{
			$limit = $row->pagination;
			$savedurl = $row->apiurl;
		}
        
        $output = '<script type="text/javascript">' . file_get_contents(PATH_THIRD.'pmailer_archive/javascript/jquery-1.6.1.js') . '</script>';
        $output .= '<script type="text/javascript">var limit=' . $limit . ';</script>';
		$output .= file_get_contents(PATH_THIRD.'pmailer_archive/library/pmailerarchive.php');
		
		return $output;
	}
}

/* End of file mod.pmailer_archive.php */
/* Location: ./system/expressionengine/third_party/pmailer_archive/mod.pmailer_archive.php */