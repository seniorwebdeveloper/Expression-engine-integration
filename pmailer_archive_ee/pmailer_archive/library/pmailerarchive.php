<script type="text/javascript">

	var totalpages = "";
	gettotalpages();
    
    
	
	$(document).ready(function(){
		setpages(1, 2, totalpages);
		submitapi(1);
		setbuttons();
		$("#next").attr("disabled", "true");
	});

	function gettotalpages()
	{
		httpObject2 = getHTTPObject();
	    if (httpObject2 != null) 
		{
			$("#pagenums").html("<img src='/pmailer_archive_root/preloader2.gif' alt='loading...'></img>");  // HTML for the Loading IMG.  
            httpObject2.abort;
	    	httpObject2.open("GET", "/pmailer_archive_root/pmailerarchiveprocess.php?pagenum=1&limit=" + limit);
		    httpObject2.send(null);
		    httpObject2.onreadystatechange = setoutputpages;
	    }
	}

	function setpages(currentpage, nextpage, totalpages)
	{
		$("#txtcurrentpage").val(currentpage);
		$("#txtnextpage").val(nextpage);
		$("#txttotalpages").val(totalpages);
	}
	
	function next()
	{
		changecurrentpage("next");
	}

	function prev()
	{
		changecurrentpage("prev");
	}

	function showpages(totpages)
	{
		$("#pagenums").html($("#txtcurrentpage").val() + " / " + totpages)
	}

	function setbuttons()
	{
		var currentpage = $("#txtcurrentpage").val();
		if(currentpage <= 1)
		{
			$("#prev").attr("disabled", "true");
		}
		else
		{
			$("#prev").removeAttr("disabled");
		}

		if(currentpage == totalpages)
		{
			$("#next").attr("disabled", "true");
		}
		else
		{
			$("#next").removeAttr("disabled");
		}
	}

	function changecurrentpage(direction)
	{
		var currentpage = $("#txtcurrentpage").val();
		var nextpage = $("#txtnextpage").val();
		
		if(direction == "next")
		{
			if((Number(nextpage) - Number(1)) != Number(totalpages))
			{
				currentpage = Number(currentpage) + Number(1);
				nextpage = Number(nextpage) + Number(1);
			}
			else
			{
				$("#next").attr("disabled", "true");
			}
		}
		else
		{
			if(currentpage <= 1 == false)
			{
				currentpage = Number(currentpage) - Number(1);
				nextpage = Number(nextpage) - Number(1);
			}
		}
		
		$("#txtcurrentpage").val(currentpage);
		$("#txtnextpage").val(nextpage);

		setbuttons();
		showpages(totalpages);
		submitapi(currentpage);
	}

	function submitapi(currentpage)
	{
		httpObject = getHTTPObject();
	    if (httpObject != null) 
		{
	    	$("#archivemsgs").html("<br /><img src='/pmailer_archive_root/preloader.gif' alt='loading...'></img>");  // HTML for the Loading IMG.  
            httpObject.abort;
		    httpObject.open("GET", "/pmailer_archive_root/pmailerarchiveprocess.php?page=" + currentpage + "&limit=" + limit);
		    httpObject.send(null);
		    httpObject.onreadystatechange = setoutput;
	    }
	}

	function setoutput()
	{
		if(httpObject.readyState == 4)
        {
            var currentpage = $("#currentpage").val();
            $("#archivemsgs").html(httpObject.responseText);
    	}
	}

	function setoutputpages()
	{
		if(httpObject.readyState == 4)
        {
            totalpages = httpObject2.responseText;
            checkPages();
    	}
	}
	
	function getHTTPObject()
	{
		if (window.ActiveXObject) 
		{
			return new ActiveXObject("Microsoft.XMLHTTP");
		}
		else if (window.XMLHttpRequest)
		{ 
			return new XMLHttpRequest();
		}
		else 
		{
			alert("Your browser does not support AJAX.");
			return null;
		}
	}
    
    function checkPages()
    {
        if(totalpages > 1)
        {
            showpages(totalpages);
            $("#pagenums").removeAttr("style");
            $("#next").removeAttr("style");
            $("#prev").removeAttr("style");
    		$("#next").removeAttr("disabled");
    	}
        else
        {
            $("#pagenums").css("display", "none");
            $("#next").css("display", "none");
            $("#prev").css("display", "none");
        }
    }
    

</script>

<input type="hidden" id="txttotalpages" name="txttotalpages" />
<input type="hidden" id="txtnextpage" name="txtnextpage" />
<input type="hidden" id="txtcurrentpage" name="txtcurrentpage" />
<button id="prev" name="prev" onclick="prev();"> < </button>
<span id="pagenums"> </span>
<button id="next" name="next" onclick="next();" disabled="disabled"> > </button>
<div id="archivemsgs" style="padding-left: 5px;"></div>