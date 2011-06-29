<?php 

	include 'pmailer_subscription_api.php';
	
	$apikey = 'HXrFFruIK8IJb3sGF4WgUaX3rJQVEFLR';
	$apiurl = 'qa.pmailer.net';	
	$api = new PMailerSubscriptionApiV1_0($apiurl, $apikey);
	
	$filters = array( 'message_type' => "email",
					  'message_status' => "sent"
				     );
	$order = array( 'message_send_date' => "DESC");
	//$limit = 3;
    $limit = $_GET['limit'];
	
	if(isset($_GET['pagenum']))
	{
		$totalmessages = $api->getBatch($filters, $order, 1, 1000000);
		$pages = intval($totalmessages['total']) / intval($limit);
		print ceil($pages);
	}
	
	if(isset($_GET['page']))
	{
		$page = intval($_GET['page']);
		$apidata = $api->getBatch($filters, $order, $page, intval($limit));
		displayMessages($apidata, $apiurl);
	}
	
	function displayMessages($apidata, $apiurl)
	{
		print '<ul>';
		foreach($apidata['data'] as $key)
		{
			print '<li>' . date('d F Y', $key['message_send_date']) . ': <a href="http://' . $apiurl . '/public/webversion/' . $key['message_id'] . '" target="_blank">' . $key['message_subject'] . '</a></li>';
		}
		print '</ul>';
	}

?>