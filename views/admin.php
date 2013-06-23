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
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<!-- <p>Your Campaign Monitor settings.</p> -->

	<form class="settings-form" method="post" action="options.php">
		<?php settings_fields( 'option-group' ); ?>

		<a href="#" class="edit-credentials">Edit Credentials</a>

		<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting"/>

		<div class="major-settings">
			<p>API</p>
		<input type="text" class="cm_api_option" name="cm_api_option" size="35" value="<?php echo get_option('cm_api_option'); ?>" />

		<p>List ID</p>
		<input type="text" class="cm_list_id_option" name="cm_list_id_option" size="35" value="<?php echo get_option('cm_list_id_option'); ?>" />

		<p class="submit">
		<input type="submit" class="button-primary cm-settings-button" value="<?php _e('Save Changes') ?>" />
		</p>
		</div>

	</form>

<div id="cm-stats"></div>

<div id="subs-per-month">
	<h2>Subscribers Per Month (Last 365 days)</h2>
	<div id="graph-1">
	<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="subs-per-month-waiting"/>
	</div>
	<canvas id="canvas-graph-1" width="700" height="400"></canvas>
</div>

<!--
<h2>Subscribers Over Time</h2>
<canvas id="canvas-graph-2" width="700" height="400"></canvas>
<div id="graphs"></div>
 -->

