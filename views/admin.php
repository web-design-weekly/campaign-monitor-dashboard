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
<?php
}
?>