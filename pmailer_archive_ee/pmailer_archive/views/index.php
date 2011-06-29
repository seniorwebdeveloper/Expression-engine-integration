<?php 
	$func = new Functions();

	//save api settings
	if(isset($_POST['apikey']))
	{
		$func->saveApiInfo($_POST['apiurl'], $_POST['apikey'], $_POST['pagination']);
	}

	//check if api key & url is set
	$currentApiInfo = $func->getApiKey();
    $paginationInput=$currentApiInfo['savedpagination'];
	if($currentApiInfo['savedapi'] == "")
	{
		$apiInput="";
		$urlInput="";
	}
	else 
	{
		//populate text field with api key
		$apiInput=$currentApiInfo['savedapi'];
		$urlInput=$currentApiInfo['savedurl'];
	}
?>

	<?php 
		$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_archive';
		$form_attributes = array('name' => 'setapikey', 'id' => 'setapikey');
		echo form_open($action_url, $form_attributes);
	?>
	<table cellspacing="0" cellpadding="0" border="0" class="mainTable padTable">
		<thead>
			<tr>
				<th colspan="3">API Settings</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 25%;"><label for="api_key">API URL</label></td>
				<td><input type="text" name="apiurl" id="apiurl" value="<?=$urlInput?>" /></td>
				<td style="width: 25%;"> </td>
			</tr>
			<tr>
				<td style="width: 25%;"><label for="api_key">API Key</label></td>
				<td><input type="text" name="apikey" id="apikey" value="<?=$apiInput?>" /></td>
				<td style="width: 25%;"> </td>
			</tr>
            <tr>
				<td style="width: 25%;"><label for="pagination">Items Per Page</label></td>
				<td><input type="text" name="pagination" id="pagination" style="width:100px;" value="<?=$paginationInput?>" /></td>
				<td style="width: 25%;">
					<button id="getlists" class="submit" type="button" name="getlists" onclick="saveApi();">Save</button>
				</td>
			</tr>
		</tbody>
	</table>
	
	<div id="apiurlerror" style="display: none; color: red; padding: 0 0 20px 3px;">
		*Please enter your API URL
	</div>
	<div id="apikeyerror" style="display: none; color: red; padding: 0 0 20px 3px;">
		*Please enter your API Key
	</div>
    <div id="paginationerror" style="display: none; color: red; padding: 0 0 20px 3px;">
		*Please enter the number of pages you want to display per screen
	</div>

	<?=form_close()?>

<script type="text/javascript">

	//client-side validation of required api key
	function saveApi()
	{
		if(document.getElementById("apikey").value == "" || document.getElementById("apiurl").value == "" || document.getElementById("pagination").value == "")
		{
			if(document.getElementById("apikey").value == "")
			{
				document.getElementById("apikeyerror").style.display = "block";
			}
			else
			{
				document.getElementById("apikeyerror").style.display = "none";
			}
            
			if(document.getElementById("apiurl").value == "")
			{
				document.getElementById("apiurlerror").style.display = "block";
			}
			else
			{
				document.getElementById("apiurlerror").style.display = "none";
			}
            
            if(document.getElementById("pagination").value == "")
			{
				document.getElementById("paginationerror").style.display = "block";
			}
			else
			{
				document.getElementById("paginationerror").style.display = "none";
			}
		}
		else
		{
			document.getElementById("apikeyerror").style.display = "none";
			document.getElementById("apiurlerror").style.display = "none";
            document.getElementById("paginationerror").style.display = "none";
			document.setapikey.submit();
		}
	}
	
</script>