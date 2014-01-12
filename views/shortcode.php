<?php


// displays the campaign_monitor signup form
 function campaign_monitor_form($redirect, $title, $subtitle, $thanks) {
 	if(strlen(trim($thanks)) <= 0) {
		$subtitle = __('You have been successfully subscribed', 'campaign-monitor-dashboard');
	}
	if(strlen(trim($redirect)) <= 0) {
		if (is_singular()) :
			$redirect =  get_permalink($post->ID);
		else :
			$redirect = 'http';
			if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") $redirect .= "s";
			$redirect .= "://";
			if ($_SERVER["SERVER_PORT"] != "80") $redirect .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
			else $redirect .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		endif;
	}
	ob_start();
		if(isset($_GET['submitted']) && $_GET['submitted'] == '1') {
			echo '<div class="cm-signup"><h3>' . $thanks . '</h3></div>';
		} else {
			if(strlen(trim(get_option('cm_api_option'))) > 0 ) { ?>
			<div class="cm-signup">
				<h3><?php echo($title); ?></h3>
				<p><?php echo($subtitle); ?></p>
			<form action="" method="post">

					<input name="campaign-monitor-email" class="campaign-monitor-email" type="email" placeholder="Email Address"/>

					<input type="hidden" name="action" value="signup"/>
					<input type="hidden" name="redirect" value="<?php echo $redirect; ?>">

					<input type="submit" value="Sign Up" />

			</form>
			</div>


			<?php
		}
	}
	return ob_get_clean();

}


//fixes the redirect issue
add_action('init', 'do_output_buffer');
function do_output_buffer() {
	ob_start();
}


	/**
	 * Register the shortcode
	 * [cmemailform redirect="foo-value" title="foo-value" subtitle="foo-value" thanks="foo-value"]
	 * @since    1.0.0
	 */
	 function campaigntag_code( $atts, $content = null ) {

		extract( shortcode_atts( array(
			'redirect' => '',
			'title' => 'Sign up to our newsletter',
			'subtitle' => 'Receive updates on a regular basis.',
			'thanks' => 'Thanks for subscribing!'
		), $atts ) );

		if($redirect == '') {
			$redirect = add_query_arg('submitted', 'yes', get_permalink());
		}

		return campaign_monitor_form($redirect, $title, $subtitle, $thanks);
	}

add_shortcode( 'cm_email_form', 'campaigntag_code' );




// process the subscribe to list form
function check_for_email_signup() {
	// only proceed with this function if we are posting from our email subscribe form
	if(isset($_POST['action']) && $_POST['action'] == 'signup') {

		// setup the email varaible
		$email = strip_tags($_POST['campaign-monitor-email']);

		 echo "$email";

		// check for a valid email
		 if(!is_email($email)) {
		 	wp_die(__('Your email address is invalid. Click back and enter a valid email address.', 'pcm'), __('Invalid Email', 'pcm'));
		 }

		// send this email to campaign_monitor
		subscribe_email($email);

		 // send user to the confirmation page
		wp_redirect(add_query_arg('submitted', '1', $_POST['redirect'])); exit;

		//wp_redirect( home_url() ); exit;

	}
}
add_action('init', 'check_for_email_signup');




	/**
	 * Subscribe someone to a list
	 *
	 */
	function subscribe_email($email) {

		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');

		$auth = array('api_key' => $cm_api);
		$wrap = new CS_REST_Subscribers($cm_list, $auth);

		//$settings = get_option ( $this->option_name );

		$wrap = new CS_REST_Subscribers($cm_list, $cm_api);

		$result = $wrap->add(array(
			'EmailAddress' => $email,
			'Resubscribe' => true
		));

		if($result->was_successful()) {
			return true;
		}

		return false;
	}





	/**
	 * Register the shortcode
	 * [cmtotalsubscribers]
	 * @since    1.0.0
	 */

function campaigntag_totalsubs( $atts ){

	$cm_api = get_option('cm_api_option');
	$cm_list = get_option('cm_list_id_option');

	$auth = array('api_key' => $cm_api);
	$wrap = new CS_REST_Lists($cm_list, $auth);

	$result = $wrap->get();
	$stats_result = $wrap->get_stats();

	if($result->was_successful()) {

		$total_active_subscribers = $stats_result->response->TotalActiveSubscribers;
	}

	return $total_active_subscribers;
}
add_shortcode( 'cm_total_subscribers', 'campaigntag_totalsubs' );





?>