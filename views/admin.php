<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */
?>

<div class="wrap">

	<?php screen_icon( 'cm_icon' ); ?>
	<img class="cm-icon" src="<?php echo plugins_url( 'assets/cm-icon.png' , dirname(__FILE__) ); ?>" alt="<?php _e('Campaign Monitor Dashboard','CampaignMonitorDashboard'); ?>" />

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
		if(CMD_OLD_WRAPPER){
		?>

		<div class="old-wrapper">
			<h2>Hey!</h2>
			<p>Thanks for taking the time to install this plugin.</p>
			<p>Unfortunately there is another plugin installed which uses an <b>old</b> version of the Campaign Monitor API. This is causing some conflicting issues.</p>
			<p>To use this plugin you will need to un-install that plugin.</p>
			<p>If you would like more details on why this is the case, please don't hesitate to <a href="mailto:support@web-design-weekly.com?Subject=Campaign%20Monitor%20Plugin%20Questions">email</a> me.

		</div>

		<?php
		} else {
	?>


<div id="tabs">

	<ul class="nav-tab-wrapper">
		<li><h2><a class="nav-tab" href="#stats"><?php _e('Your Stats','campaign-monitor-dashboard'); ?></a></h2></li>
		<li><h2><a class="nav-tab" href="#shortcode"><?php _e('Shortcode','campaign-monitor-dashboard'); ?></a></h2></li>
		<li><h2><a class="nav-tab" href="#support"><?php _e('Support','campaign-monitor-dashboard'); ?></a></h2></li>
	</ul>


	<div id="stats">
		<div class="settings-field">
			<div class="inside">

			<form class="settings-form" method="post" action="options.php">
				<?php settings_fields( 'option-group' ); ?>

				<a href="#" class="edit-credentials">Edit Credentials</a>

				<span class="waiting"></span>

				<div class="major-settings">

					<p>API</p>
					<input type="text" class="cm_api_option" name="cm_api_option" size="34" value="<?php echo get_option('cm_api_option'); ?>" />

					<p>List ID</p>
					<input type="text" class="cm_list_id_option" name="cm_list_id_option" size="34" value="<?php echo get_option('cm_list_id_option'); ?>" />

					<p class="submit">
					<input type="submit" class="button-primary cm-settings-button" value="<?php _e('Save Changes') ?>" />
					</p>

					<p class="help">
						<a href="https://gist.github.com/jakebresnehan/5992863" target="_blank">Find API Key</a>
						<a href="https://gist.github.com/jakebresnehan/5992863" target="_blank">Find List ID</a>
					</p>

					<p class="cm_dashboard_widget_option-wrapper">
						<input type="checkbox" <?php checked( 'on', get_option('cm_dashboard_widget_option'), true ); ?> class="cm_dashboard_widget_option" id="cm_dashboard_widget_option" name="cm_dashboard_widget_option" /> <label for="cm_dashboard_widget_option">Show as Dashboard Widget</label>
					</p>

				</div>

			</form>

				<div id="cm-stats"></div>

			</div>
		</div>
	</div>

	<div id="shortcode">
		<div class="settings-field">
			<div class="inside">

				<h3>Adding Forms To Your Site</h3>

				<p>Once you have entered your correct Campaign Monitor credentials you can add sign up forms to any of your posts or pages with a simple shortcode.</p>

				<p><strong>Shortcode:</strong> <code>[emailform]</code></p>

				<h3>Shortcode Options</h3>

				<p>The shortcode come built with 4 options:</p>
					<ul>
						<li><strong>— Title</strong></li>
						<li><strong>— Subtitle</strong></li>
						<li><strong>— Thanks</strong></li>
						<li><strong>— Redirect</strong></li>
					</ul>

				<p>To custormise the output of you email sign up form all you need to do is give any of the options your desired value.</p>

				<p><small>Note: Your thank you message will only be displayed if you do not provide a redirect.</small></p>

				<p><code>[emailform title="<strong>YOUR TITLE</strong>" subtitle="<strong>YOUR SUBTITLE</strong>" redirect="<strong>http://yoursite.com</strong>" thanks="<strong>YOUR THANK YOU MESSAGE</strong>"]</code></p>

				<p>If you do not want the default output, just declare the option with a blank value.</p>

				<p>Like so: <code>[emailform <strong>OPTION</strong>=""]</code></p>

				<h3>Customising With CSS</h3>
				<p>The Campaign Monitor Dashboard shortcode form doesn't come with any styles. If you would like to custormise the look, you can simply just use the following CSS classes in your stylesheet.</p>

				<p><code>.cmpost-signup { /* Wraps entire form */ }</code></p>

				<p><code>.cmpost-signup h3 { /* Title styles */ }</code></p>

				<p><code>.cmpost-signup p { /* Subtitle styles */ }</code></p>

				<p><code>.cmpost-signup input[type="email"] { /* Email input styles */ }</code></p>

				<p><code>.cmpost-signup input[type="submit"] { /* Submit button styles */ }</code></p>

			</div>
		</div>
	</div>


	<div id="support">
		<div class="settings-field">
			<div class="inside">
				<h3>Need Help?</h3>
				<p>If for some reason you need help or have a feature request, please don't hesitate to submit a request on the dedicated support forum.</p>

				<p><a href="http://web-design-weekly.com/support/forums/forum/campaign-monitor-dashboard-plugin/" alt="Support Forum">Support Forum &rarr;</a></p>

			</div>
		</div>
	</div>

</div>




<?php
	}
?>