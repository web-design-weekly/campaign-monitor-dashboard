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
	<img class="cm-icon" src="<?php echo plugins_url( 'assets/cm-icon.png' , dirname(__FILE__) ); ?>" alt="<?php _e('Campaign Monitor Dashboard','campaign-monitor-dashboard'); ?>" />

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php
		if(CMD_OLD_WRAPPER){
		?>

		<div class="old-wrapper">
			<?php echo '<h2>' . __('Hey!','campaign-monitor-dashboard') . '</h2>' . '<p>' . __('Thanks for taking the time to install this plugin.','campaign-monitor-dashboard') . '</p>' . '<p>' . __('Unfortunately there is another plugin installed which uses an ','campaign-monitor-dashboard') . '<b>' . __('old ','campaign-monitor-dashboard') . '</b>' . __('version of the Campaign Monitor API. This is causing some conflicting issues.','campaign-monitor-dashboard') . '</p>' . '<p>' . __('To use this plugin you will need to un-install that plugin.','campaign-monitor-dashboard') . '</p>' . '<p>' . __('If you would like more details on why this is the case, please don\'t hesitate to use the dedicated ','campaign-monitor-dashboard') . '<a href="http://web-design-weekly.com/support/">' . __('Support Forum.','campaign-monitor-dashboard') . '</a></p>'; ?>
		</div>

		<?php
		} else {
	?>


<div id="tabs">

	<ul class="nav-tab-wrapper">
		<li><h2><a class="nav-tab" href="#stats"><?php _e('Your Stats','campaign-monitor-dashboard'); ?></a></h2></li>
		<li><h2><a class="nav-tab" href="#graph"><?php _e('Graph','campaign-monitor-dashboard'); ?></a></h2></li>
		<li><h2><a class="nav-tab" href="#shortcodes"><?php _e('Shortcodes','campaign-monitor-dashboard'); ?></a></h2></li>
		<li><h2><a class="nav-tab" href="#support"><?php _e('Support','campaign-monitor-dashboard'); ?></a></h2></li>
	</ul>

	<div id="stats">
		<div class="settings-field">
			<div class="inside">

			<form class="settings-form" method="post" action="options.php">
				<?php settings_fields( 'option-group' ); ?>

				<a href="#" class="edit-credentials"><?php _e('Edit Credentials','campaign-monitor-dashboard'); ?></a>

				<span class="waiting"></span>

				<div class="major-settings">

					<p><?php _e('API','campaign-monitor-dashboard'); ?></p>
					<input type="text" class="cm_api_option" name="cm_api_option" size="34" value="<?php echo get_option('cm_api_option'); ?>" />

					<p><?php _e('List ID','campaign-monitor-dashboard'); ?></p>
					<input type="text" class="cm_list_id_option" name="cm_list_id_option" size="34" value="<?php echo get_option('cm_list_id_option'); ?>" />

					<p class="submit">
					<input type="submit" class="button-primary cm-settings-button" value="<?php _e('Save Changes','campaign-monitor-dashboard'); ?>" />
					</p>

					<p class="help">
						<a href="https://gist.github.com/jakebresnehan/5992863" target="_blank"><?php _e('Find API Key','campaign-monitor-dashboard'); ?></a>
						<a href="https://gist.github.com/jakebresnehan/5992863" target="_blank"><?php _e('Find List ID','campaign-monitor-dashboard'); ?></a>
					</p>

					<p class="cm_dashboard_widget_option-wrapper">
						<input type="checkbox" <?php checked( 'on', get_option('cm_dashboard_widget_option'), true ); ?> class="cm_dashboard_widget_option" id="cm_dashboard_widget_option" name="cm_dashboard_widget_option" /> <label for="cm_dashboard_widget_option"><?php _e('Show as Dashboard Widget','campaign-monitor-dashboard'); ?></label>
					</p>

				</div>

			</form>

				<div id="cm-stats"></div>

			</div>
		</div>
	</div>

	<div id="graph">
		<div class="settings-field">
			<div class="inside">

					<div id="subs-per-month">
					<?php echo '<h3>' . __('Subscribers Per Month ','campaign-monitor-dashboard') . '<span>' . __('(Last 90 days)','campaign-monitor-dashboard') . '</span></h3>'; ?>
						<div id="graph-1">
							<img src="<?php echo plugins_url('/campaign-monitor-dashboard/assets/wpspin_light.gif'); ?>" class="subs-per-month-waiting"/>
						</div>

						<canvas id="canvas-graph-1" width="800" height="400"></canvas>
				</div>

			</div>
		</div>
	</div>

	<div id="shortcodes">
		<div class="settings-field">
			<div class="inside">

				<p><?php _e('The Campaign Monitor Dashboard plugin has a few easy shortcodes for your convenience.','campaign-monitor-dashboard'); ?></p>

				<?php echo '<p><strong>' . __('Email Form Shortcode: ', 'campaign-monitor-dashboard') . '</strong><code>[cm_email_form]</code></p>' ; ?>

				<?php echo '<p><strong>' . __('Total Subscribers Shortcode: ', 'campaign-monitor-dashboard') . '</strong><code>[cm_total_subscribers]</code></p>' ; ?>
				<hr>

				<h3><?php _e('Email Form Shortcode','campaign-monitor-dashboard'); ?></h3>

				<p><?php _e('Once you have entered your correct Campaign Monitor credentials you can add sign up forms to any of your posts or pages with a simple shortcode.', 'campaign-monitor-dashboard'); ?></p>

				<p><code>[cm_email_form]</code></p>

				<h4><?php _e('Shortcode Options','campaign-monitor-dashboard'); ?></h4>

				<p><?php _e('The shortcode come built with 4 options:','campaign-monitor-dashboard'); ?></p>
					<ul>
						<?php _e('','campaign-monitor-dashboard'); ?>
						<?php _e('','campaign-monitor-dashboard'); ?>
						<li><strong>— <?php _e('Title','campaign-monitor-dashboard'); ?></strong></li>
						<li><strong>— <?php _e('Subtitle','campaign-monitor-dashboard'); ?></strong></li>
						<li><strong>— <?php _e('Thanks','campaign-monitor-dashboard'); ?></strong></li>
						<li><strong>— <?php _e('Redirect','campaign-monitor-dashboard'); ?></strong></li>
					</ul>

				<p><?php _e('To custormise the output of you email sign up form all you need to do is give any of the options your desired value.','campaign-monitor-dashboard'); ?></p>

				<p><small><?php _e('Note: Your thank you message will only be displayed if you do not provide a redirect.','campaign-monitor-dashboard'); ?></small></p>


				<p><code>[cm_email_form title="<strong><?php _e('YOUR TITLE','campaign-monitor-dashboard'); ?></strong>" subtitle="<strong><?php _e('YOUR SUBTITLE','campaign-monitor-dashboard'); ?></strong>" redirect="<strong>http://yoursite.com</strong>" thanks="<strong><?php _e('YOUR THANK YOU MESSAGE','campaign-monitor-dashboard'); ?></strong>"]</code></p>

				<p><?php _e('If you do not want the default output, just declare the option with a blank value.','campaign-monitor-dashboard'); ?></p>

				<p><?php _e('Like so: ','campaign-monitor-dashboard'); ?> <code>[cm_email_form <strong><?php _e('OPTION','campaign-monitor-dashboard'); ?></strong>=""]</code></p>

				<h4><?php _e('Customising With CSS','campaign-monitor-dashboard'); ?></h4>
				<p><?php _e('The Campaign Monitor Dashboard shortcode form doesn\'t come with any styles. If you would like to custormise the look, you can simply just use the following CSS classes in your stylesheet.','campaign-monitor-dashboard'); ?></p>

				<p><code>.cm-signup { /* Wraps entire form */ }</code></p>

				<p><code>.cm-signup h3 { /* Title styles */ }</code></p>

				<p><code>.cm-signup p { /* Subtitle styles */ }</code></p>

				<p><code>.cm-signup input[type="email"] { /* Email input styles */ }</code></p>

				<p><code>.cm-signup input[type="submit"] { /* Submit button styles */ }</code></p>

				<hr>

				<h3><?php _e('Total Subscribers Shortcode','campaign-monitor-dashboard'); ?></h3>

				<p><?php _e('To output the total number of subscribers to your Campaign Monitor list, just use the following handy shortcode.','campaign-monitor-dashboard'); ?></p>

				<p><code>[cm_total_subscribers]</code></p>

			</div>
		</div>
	</div>


	<div id="support">
		<div class="settings-field">
			<div class="inside">
				<?php echo '<h3>' . __('Need Help? ','campaign-monitor-dashboard') . '</h3><p>' . __('If for some reason you need help or have a feature request, please don\'t hesitate to submit a request on the dedicated support forum.','campaign-monitor-dashboard') . '</p><p><a href="http://web-design-weekly.com/support/" alt="Support Forum">' . __('Support Forum &rarr;', 'campaign-monitor-dashboard') .'</a></p>'; ?>

			</div>
		</div>
	</div>

</div>


<?php
	}
?>