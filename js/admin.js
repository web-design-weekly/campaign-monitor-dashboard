(function (jQuery) {

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
		});

	} else {

		jQuery('.major-settings').toggle();
		jQuery('.waiting').hide();
		jQuery('#cm-stats').html('<p class="cm-error">Please add your correct credentials <span>to get the ball rolling.</span></p>');
	}

	jQuery('.edit-credentials').click(function () {
		jQuery('.major-settings').toggle();
	});

});

}(jQuery));
