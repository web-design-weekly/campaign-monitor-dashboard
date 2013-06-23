(function ($) {

 $(window).bind("load", function() {

	var cm_api_settings = $('.cm_api_option').val(),
		cm_list_settings = $('.cm_list_id_option').val();

	console.log(cm_api_settings);
	console.log(cm_list_settings);

	if (cm_api_settings || cm_list_settings) {
		// CM Settings Ajax
		data = {
			action: 'get_cm_settings'
		};

		$.post(ajaxurl, data, function (response) {
			$('#cm-stats').html(response);
		});

		// Graph Ajax
		data = {
			action: 'aad_get_results'
		};

		$.post(ajaxurl, data, function (response) {
			console.log('boom');
			console.log(response);
			$('#graphs').html(response);
			$('.subs-per-month-waiting').hide();
		});

	} else {
		$('.major-settings').toggle();
		$('.waiting').hide();
		$('.subs-per-month-waiting').hide();
		$('#cm-stats').html('Please add your correct credentials to get the ball rolling.');
		console.log('no values');
	}


	$('.edit-credentials').click(function () {
		console.log("clicked settings");
		$('.major-settings').toggle();
	});

});


}(jQuery));
