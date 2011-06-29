<?php 
	$func = new Functions();

	if(isset($_POST['apikey']))
	{
		$func->saveApiInfo($_POST['apiurl'], $_POST['apikey']);
	}
	
	if(isset($_POST['savelistssubmitted']))
	{
		$func->saveListsToDB($_POST);
	}
	
	//check if api key & url is set
	$currentApiInfo = $func->getApiKey();
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
	$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_subscribe';
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
				<td style="width: 25%;">
					
				</td>
			</tr>
			<tr>
				<td style="width: 25%;"><label for="api_key">API Key</label></td>
				<td><input type="text" name="apikey" id="apikey" value="<?=$apiInput?>" /></td>
				<td style="width: 25%;">
					<button id="getlists" class="submit" type="button" name="getlists" onclick="getMailingListsJs();">Get Mailing Lists</button>
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

	<?=form_close()?>
	
	<?php 
	
	if($currentApiInfo['savedapi'] != "")
	{
		$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_subscribe';
		$form_attributes = array('name' => 'setlists', 'id' => 'setlists');
		echo form_open($action_url, $form_attributes);
		$mailingLists = $func->getMailingLists(); 
		
	?>
		<div id="listerror" style="display: none; color: red; padding: 0 0 20px 3px;">
			*At least one list must be selected
		</div>
	<?=form_close();}?>
	
<div class="dataTables_wrapper">
<table cellspacing="0" cellpadding="0" border="0" class="mainTable padTable">
<thead>
<tr>
<th>Entry ID</th>
<th>First Name</th>
<th>Last Name</th>
<th>Email Address</th>
</tr>
</thead>
<tbody>
<?php foreach($entries as $e){ ?>
<tr>
<td valign="top"><?= $e['entry_id']; ?></td>
<td valign="top"><?= $e['fname']; ?></td>
<td valign="top"><?= $e['lname']; ?></td>
<td valign="top"><?= $e['email']; ?></td>
</tr>
<? } ?>
</tbody>
</table>

</div>



<script type="text/javascript">

	//client-side validation of required api key
	function getMailingListsJs()
	{
		if(document.getElementById("apikey").value == "" || document.getElementById("apiurl").value == "")
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
		}
		else
		{
			document.getElementById("apikeyerror").style.display = "none";
			document.getElementById("apiurlerror").style.display = "none";
			document.setapikey.submit();
		}
	}

	//client-side validation of list selection
	function saveMailingListsJs()
	{
		var n = $("input:checked").length;

		if(n == 0)
		{
			document.getElementById("listerror").style.display = "block";
		}
		else
		{
			document.getElementById("listerror").style.display = "none";
			document.setlists.submit();
		}
	}

</script>