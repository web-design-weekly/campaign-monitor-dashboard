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
			$('.settings-form').addClass('successful-credentials-toggle');
		});

		// Graph 1 Ajax
		data = {
			action: 'aad_get_results'
		};

		$.post(ajaxurl, data, function (response) {
			console.log(response);
			//$('#subs-per-month').hide();
			$('#graph-1').html(response);
			$('.subs-per-month-waiting').hide()
		});

	} else {
		$('.major-settings').toggle();
		$('.waiting').hide();
		$('.subs-per-month-waiting').hide()

		$('#cm-stats').html('Please add your correct credentials <span>to get the ball rolling</span>.');
		console.log('no values');
	}


	$('.edit-credentials').click(function () {
		console.log("clicked settings");
		$('.major-settings').toggle();

	});

});


}(jQuery));
