<?php 
	
	$func = new Functions();
	
	if(isset($_GET['gettotalcontacts']))
	{
		$func->getNumContacts();
	}
	
	if(isset($_GET['batches']))
	{
		$func->splitContacts();
	}
	
	if(isset($_GET['processdata']))
	{
		$func->processContacts();
	}
	
?>

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

	
	function syncContacts()
	{
		//disable button
		$("#apifeedback").css("display", "block");
		$("#apifeedback").html("<strong><font size=4>Please do not navigate away from this page until all contacts have been exported</font></strong><br /><br />");
		$("#btnsynccontacts").attr("disabled", "disabled");
		$("#btnsynccontacts").css("background", "#999999");
		$("#btnsynccontacts").html("Processing...");
		
		//get total contacts
		var url = window.location;
		var params = "gettotalcontacts=true";
		$.get(url, params, function(data)
		{
			var response = JSON.parse(data);

			if(response > 0)
			{
				var totcontacts = response;
				$("#apifeedback").append("<strong>Exporting a total of " + totcontacts + " contacts...</strong><br />");
				//get contact batches
				params = "batches=true";
				$.get(url, params, function(data)
				{
					var response2 = JSON.parse(data);

					//process each batch
					if(response2 != "")
					{
						var newArray = new Array();
						var counter = 0;

						for(var i = 0; i < response2.length; i++)
						{
							counter = Number(counter) + 30;

							if(counter > totcontacts)
							{
								counter = totcontacts;
							}
							
							var JSONObject = new Object;
						    JSONObject = response2[i];
						    JSONstring = JSON.stringify(JSONObject);
							var params2 = "processdata=" + JSONstring;
							
							$.ajax({
							     async: false,
							     type: 'GET',
							     url: window.location,
							     data: params2,
							     success: function(data2) {
									var response3 = JSON.parse(data2);
									if(response3 != true)
									{
										$("#apifeedback").append("<span style='background:#FFC8C8; display:block; padding:5px;' >There was an error exporting your contacts. Please try again.</strong>");
									}
									else
									{
										$("#apifeedback").append("<br />" + counter + " of " + totcontacts + " successfully exported...");
									}
							     }
							});

							if(counter == totcontacts)
							{
								$("#apifeedback").append("<br /> <span style='background:#C8FFD6; display:block; padding:5px; margin:2px 0px; font-weight:bold; font-size:14px;' >All contacts successfully exported.</span>");
							}
						}
					}
				});
			}
		});
	}
</script>

<?php 
	
	//save api settings
	if(isset($_POST['apikey']))
	{
		$func->saveApiInfo($_POST['apiurl'], $_POST['apikey']);
	}
	
	//save selected lists
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
	
	if($currentApiInfo['syncdate'] != "")
	{
		print "Your last sync was on " . $currentApiInfo['syncdate'] . " with " . $currentApiInfo['syncamount'] . " contacts. <br / >";
		
		if($currentApiInfo['members'] > $currentApiInfo['syncamount'])
		{
			print "You now have " . $currentApiInfo['members'] . " contacts. Another sync is recommended. <br />";
		}
	}

?>

	<?php 
	$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_contacts';
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
		$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_contacts';
		$form_attributes = array('name' => 'setlists', 'id' => 'setlists');
		echo form_open($action_url, $form_attributes);
		$mailingLists = $func->getMailingLists(); 
	?>
		<div id="listerror" style="display: none; color: red; padding: 0 0 20px 3px;">
			*At least one list must be selected
		</div>
	<?=form_close();}

	//display sync button if mailing lists are saved
	$action_url = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=pmailer_contacts';
	$form_attributes = array('name' => 'synccontacts', 'id' => 'synccontacts');
	echo form_open($action_url, $form_attributes);
	$startsync = $func->displaySync();
	form_close();
	
	?>