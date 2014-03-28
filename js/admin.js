(function (jQuery) {

	jQuery(document).ready(function( $ ) {

		jQuery("#tabs").tabs();

		// To handle slow loading of JS :D
		jQuery(".settings-field").css('display','block');

	});


jQuery(window).bind("load", function() {

	var cm_api_settings = jQuery('.cm_api_option').val(),
		cm_list_settings = jQuery('.cm_list_id_option').val();

	if (cm_api_settings || cm_list_settings) {
		// CM Settings Ajax
		data = {
			action: 'get_cm_settings'
		};

		jQuery.post(ajaxurl, data, function (response) {
			jQuery('#cm-stats').html(response);
			jQuery('.settings-form').addClass('successful-credentials-toggle');
			jQuery('#subs-per-month').show();
		});

		//Subs Per Month Graph
		data = {
			action: 'get_month_graph'
		};

		jQuery.post(ajaxurl, data, function (response) {
			jQuery('#graph-1').html(response);
			jQuery('.subs-per-month-waiting').hide();
			jQuery('#subs-per-month').show();
		});

	} else {

		jQuery('.major-settings').toggle();
		jQuery('.waiting').hide();
		jQuery('.subs-per-month-waiting').hide();
		jQuery('#cm-stats').html('<p class="cm-error">Please add your correct credentials <span>to get the ball rolling.</span></p>');
	}

	jQuery('.edit-credentials').click(function () {
		jQuery('.major-settings').toggle();
		jQuery('.edit-credentials').toggleClass('on');
		toggleText();
	});

});


function toggleText () {

	if (jQuery('.edit-credentials').hasClass('on')) {
		jQuery('.edit-credentials').text('Hide Credentials');
	} else {
		jQuery('.edit-credentials').text('Edit Credentials');
	}

}

}(jQuery));
