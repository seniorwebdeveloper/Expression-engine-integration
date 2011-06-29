/* cookie plugin */
/*!
* jQuery CooQuery Plugin v2
* http://cooquery.lenonmarcel.com.br/
*
* Copyright 2009, 2010 Lenon Marcel
* Dual licensed under the MIT and GPL licenses.
* http://www.opensource.org/licenses/mit-license.php
* http://www.gnu.org/licenses/gpl.html
*
* Date: 2010-01-24 (Sun, 24 January 2010)
*/

(function($){
	$.setCookie = function( name, value, options ){
		if( typeof name === 'undefined' || typeof value === 'undefined' )
			return false;
	
		var str = name + '=' + encodeURIComponent(value);
	
		if( options.domain ) str += '; domain=' + options.domain;
		if( options.path ) str += '; path=' + options.path;
		if( options.duration ){
			var date = new Date();
			date.setTime( date.getTime() + options.duration * 24 * 60 * 60 * 1000 );
			str += '; expires=' + date.toGMTString();
		}
	
		if( options.secure ) str += '; secure';
			return document.cookie = str;
		
	};
	
	$.delCookie = function( name ){
		return $.setCookie( name, '', { duration: -1 } );
	};

	// Based on Mootools Cookie.read function (http://mootools.net/docs/core/Utilities/Cookie#Cookie:read)
	$.readCookie = function( name ){
		var value = document.cookie.match('(?:^|;)\\s*' + name.replace(/([-.*+?^${}()|[\]\/\\])/g, '\\$1') + '=([^;]*)');
		return (value) ? decodeURIComponent(value[1]) : null;
	};

	$.CooQueryVersion = 'v 2.0';
})($);

/* pmailer submission form logic */

(function($)
{
	$(document).ready(function() 
	{
		var enabled = savedsmartform.toUpperCase()
        // check if smart forms is enabled  	
        if( enabled != 'YES' )
        {
              return;
        }

        // create cookie if it does not exist
        if ( $.readCookie('pmailer_page_views') == null )
        {
              $.setCookie( 'pmailer_page_views', '0', {
                  duration : 365 // In days
              });
        }

        else // increment page view 
        {
        	
              var page_views = parseInt($.readCookie('pmailer_page_views')) + 1;
              $.setCookie('pmailer_page_views', page_views, 
              {
                  duration : 365 // In days
              });
              
              // display smart form if page views is over 5
              var show_smart_form = $.readCookie('pmailer_show_smart_form');
              if ( show_smart_form != 'false' && page_views >= savedpageviewdelay )
              {
                    var pmailer_dialogue_title = "Subscribe To Our Newsletter";
                    $('#pmailer_smart_form').show();
                    $('#pmailer_smart_form').dialog(
                    {
                    	width: 400,
                    	minHeight: 220,
                        title: pmailer_dialogue_title,
                        modal: true,
                        resizable: false,
                        beforeClose: function(event, ui)
                        { 
	            		  	SetCookie('pmailer_page_views', '0', '365')
	            		  	window.location = window.location.href;
                    	},
                        buttons:
                        [
                            {
                                text: "Do not show again",
                                click: PmailerUtilities.Dialog.close
                            },
                            {
                                text: 'Subscribe',
                                click: PmailerUtilities.Dialog.subscribe,
                                id: 'pmailer_sub_smart_subscribe'
                            }
                        ]
                    });
              }
        }
    });
})($);

 

var PmailerUtilities = {};
PmailerUtilities.Dialog =
{
	close: function(event)
	{
		SetCookie('pmailer_show_smart_form', 'false', '60')
		window.location = window.location.href;
	},

	subscribe: function()
    {
		// disable subscribe btn
		$('#pmailer_sub_smart_subscribe').attr('disabled', 'disabled');
		var pmailer_sub_url = "";
		var params = $('#pmailer_subscription_smart_form').serialize();
		$.post(pmailer_sub_url, params, function(data)
		{
			// check if subscribe was succesfull
			var response = JSON.parse(data);
			
			if ( response.status == 'success' )
			{
				$('#pmailer_subscription_smart_form').html('<span class="pmailer_subscription_success">You have been successfully subscribed.</span>');

				window.setTimeout("$('.ui-dialog').hide()", 3000);
				window.setTimeout("$('.ui-widget-overlay').hide()", 3000);
				SetCookie('pmailer_show_smart_form', 'false', '60')
				window.location = response.redirect;
			}
		});
    }
}

function SetCookie(cookieName,cookieValue,nDays) 
{
	var today = new Date();
	var expire = new Date();
	if (nDays==null || nDays==0) nDays=1;
	expire.setTime(today.getTime() + 3600000*24*nDays);
	document.cookie = cookieName+"="+escape(cookieValue)
                 + ";expires="+expire.toGMTString();
}