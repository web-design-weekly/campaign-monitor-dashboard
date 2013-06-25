(function ($) {

$(window).bind("load", function() {

	var cm_api_settings = $('.cm_api_option').val(),
		cm_list_settings = $('.cm_list_id_option').val();

	if (cm_api_settings || cm_list_settings) {
		// CM Settings Ajax
		data = {
			action: 'get_cm_settings'
		};

		$.post(ajaxurl, data, function (response) {
			$('#cm-stats').html(response);
			$('.settings-form').addClass('successful-credentials-toggle');
			$('#subs-per-month').show();
		});

		//Subs Per Month Graph
		data = {
			action: 'get_month_graph'
		};

		$.post(ajaxurl, data, function (response) {
			$('#graph-1').html(response);
			$('.subs-per-month-waiting').hide();
			$('#subs-per-month').show();
		});

	} else {

		$('.major-settings').toggle();
		$('.waiting').hide();
		$('.subs-per-month-waiting').hide();
		$('#cm-stats').html('<p class="cm-error">Please add your correct credentials <span>to get the ball rolling.</span></p>');
	}

	$('.edit-credentials').click(function () {
		$('.major-settings').toggle();
	});

});

}(jQuery));
