<?php
/**
 * Campaign Monitor Dashboard.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <jakebresnehan@gmail.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */

/**
 * Plugin class.
 *
 * @package CampaignMonitorDashboard
 * @author  Jake Bresnehan <jakebresnehan@gmail.com>
 */

class CampaignMonitorDashboard {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.1.5';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'campaign-monitor-dashboard';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Load admin Stylesheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Loads main Campaign Monitor settings panel
		add_action( 'wp_ajax_get_cm_settings', array( $this, 'process_cm_settings' ) );

		// Register widget
		add_action( 'widgets_init', create_function( '', 'register_widget( "campaign_monitor_widget" );' ) );

		// Loads Graph
		add_action( 'wp_ajax_get_month_graph', array( $this, 'process_graph_data' ) );


		if ( get_option('cm_dashboard_widget_option') == "on" ) {
			add_action('wp_dashboard_setup', array( $this, 'add_cm_dashboard_widget' ) );
		}

		// Adds settings link to plugins page
		$plugin_file = 'campaign-monitor-dashboard/campaign-monitor-dashboard.php';
		add_filter( 'plugin_action_links_' . $plugin_file, array( $this, 'my_plugin_action_links' ) );

		// Loads shortcode file
		add_action( 'after_setup_theme', array( $this, 'campaigntag_view' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}



	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );

			//UI Tabs
			wp_enqueue_script( 'jquery-ui-tabs' );

		}

		// Maybe loading this can be done better?
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script-plugins', plugins_url( 'js/admin-plugins.js', __FILE__ ), array( 'jquery' ), $this->version );
		}


	}

	/**
	 * Register the administration menu into the WordPress Dashboard menu only for users that can manage options.
	 *
	 * @since    1.1.2
	 */
	public function add_plugin_admin_menu() {
		if (current_user_can( 'manage_options' )) {
			$this->plugin_screen_hook_suffix = add_options_page(
				__( 'Campaign Monitor Dashboard', $this->plugin_slug ),
				__( 'Campaign Monitor', $this->plugin_slug ),
				'read',
				$this->plugin_slug,
				array( $this, 'display_plugin_admin_page' )
			);
		}

	}

	/**
	 * Add settings link on plugins.php
	 *
	 * @since    1.0.9
	 */

	public function my_plugin_action_links( $links ) {
		$links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=campaign-monitor-dashboard') .'">Settings</a>';
		$links[] = '<a href="http://web-design-weekly.com/support/" target="_blank">Support</a>';

		return $links;
	}


	/**
	 * Registers the settings for this plugin.
	 *
	 * @since    1.0.0
	 */

	public function register_settings() {
		register_setting( 'option-group', 'cm_api_option' );
		register_setting( 'option-group', 'cm_list_id_option' );
		register_setting( 'option-group', 'cm_dashboard_widget_option' );
	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}


	/**
	 * Render the dashboard widget view.
	 *
	 * @since    1.0.0
	 */
	public function dashboard_widget_view() {
		include_once( 'views/dashboard-widget.php' );
	}

	/**
	 * Register the dashboard widget only for users that can manage options.
	 *
	 * @since    1.1.2
	 */
	public function add_cm_dashboard_widget() {
		if (current_user_can( 'manage_options' )) {
			wp_add_dashboard_widget(
				'cm_dashboard_widget',
				'Campaign Monitor',
				array( $this, 'dashboard_widget_view' )
			);

			wp_register_style( 'dashboard-widget-style', plugins_url('css/dashboard-widget.css', __FILE__) );
			wp_enqueue_style( 'dashboard-widget-style' );
		}

	}


	/**
	 * Main Campaign Monitor settings panel
	 *
	 * @since    1.0.0
	 */
	public function process_cm_settings() {
		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');

		$auth = array('api_key' => $cm_api);
		$wrap = new CS_REST_Lists($cm_list, $auth);

		$result = $wrap->get();
		$stats_result = $wrap->get_stats();

		if($result->was_successful()) {

			$total_active_subscribers = $stats_result->response->TotalActiveSubscribers;
			$new_sub_today = $stats_result->response->NewActiveSubscribersToday;
			$new_sub_yesterday = $stats_result->response->NewActiveSubscribersYesterday;
			$new_sub_this_week = $stats_result->response->NewActiveSubscribersThisWeek;
			$new_sub_this_month = $stats_result->response->NewActiveSubscribersThisMonth;
			$new_sub_this_year = $stats_result->response->NewActiveSubscribersThisYear;

			$total_unsubscribers = $stats_result->response->TotalUnsubscribes;
			$un_sub_today = $stats_result->response->UnsubscribesToday;
			$un_sub_yesterday = $stats_result->response->UnsubscribesYesterday;
			$un_sub_this_week = $stats_result->response->UnsubscribesThisWeek;
			$un_sub_this_month = $stats_result->response->UnsubscribesThisMonth;
			$un_sub_this_year = $stats_result->response->UnsubscribesThisYear;

			echo '<div class="current-list">';
			echo '<h3>' . $result->response->Title . '</h3>';
			echo '<p>' . __('Total Subscribers: ', 'campaign-monitor-dashboard') . $total_active_subscribers . '</p>';
			echo '<p class="unsub">' . __('Total Unsubscribers: ','campaign-monitor-dashboard') . $total_unsubscribers . '</p>';
			echo '</div>';

			echo '<div class="stats">';
			echo '<div class="sub-stats">';
			echo '<h4>' . __('Subscribers','campaign-monitor-dashboard') . '</h4>';
			echo '<p><span>' . __('Today ','campaign-monitor-dashboard') . '</span>' . $new_sub_today . '</p>';
			echo '<p><span>' . __('Yesterday ','campaign-monitor-dashboard') . '</span>' . $new_sub_yesterday . '</p>';
			echo '<p><span>' . __('This Week ','campaign-monitor-dashboard') . '</span>' . $new_sub_this_week . '</p>';
			echo '<p><span>' . __('This Month' ,'campaign-monitor-dashboard') .'</span>' . $new_sub_this_month . '</p>';
			echo '<p><span>' . __('This Year ','campaign-monitor-dashboard') .'</span>' . $new_sub_this_year . '</p>';
			echo '</div>';

			echo '<div class="sub-stats">';
			echo '<h4>' . __('Unsubscribers','campaign-monitor-dashboard') . '</h4>';
			echo '<p><span>' . __('Today ','campaign-monitor-dashboard') .'</span>' . $un_sub_today . '</p>';
			echo '<p><span>' . __('Yesterday ','campaign-monitor-dashboard') . '</span>' . $un_sub_yesterday . '</p>';
			echo '<p><span>' . __('This Week ','campaign-monitor-dashboard') . '</span>' . $un_sub_this_week . '</p>';
			echo '<p><span>' . __('This Month ','campaign-monitor-dashboard') .'</span>' . $un_sub_this_month . '</p>';
			echo '<p><span>' . __('This Year ','campaign-monitor-dashboard') . '</span>' . $un_sub_this_year . '</p>';
			echo '</div>';
			echo '</div>';

			?>
				<!-- Would like to have this in admin js... -->
				<script type="text/javascript">
					jQuery('.settings-form').addClass('successful-credentials');
					jQuery('.waiting').hide();
				</script>
			<?php

		} else {
			echo '<p class="cm-error">' . __('Please add your correct credentials ','campaign-monitor-dashboard') . '<span>' . __('to get the ball rolling.','campaign-monitor-dashboard') . '</span></p>';
			?>
				<!-- Would like to have this in admin js... -->
				<script type="text/javascript">
					jQuery('.major-settings').toggle();
					jQuery('.waiting').hide();
					jQuery('#subs-per-month').hide();
				</script>
			<?php

		}

		die();
	}

	/**
	 * Render the shortcode
	 *
	 * @since    1.0.9
	 */
	public function campaigntag_view() {
		include_once( 'views/shortcode.php' );
	}

	/**
	 * Ajax subscribers per month graph data
	 *
	 * @since    1.1.2
	 */
	public function process_graph_data() {
		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');
		$auth = array('api_key' => $cm_api);

		$wrap = new CS_REST_Lists($cm_list, $auth);

		function get_actives($auth, $page_number)
		{
			// last 90 days
			$date_from = date('Y-m-d', strtotime('-90 days'));

			// params: start date, page number, page size, order by, order direction
			$result = $auth->get_active_subscribers($date_from, $page_number, 1000, 'date', 'asc');

			if($result->was_successful()) {

			} else {

				echo '<p>Failed to get results, please refresh the page.</p>';
			}

			return $result;
		}


			function add_results_to_stack($stack, $result) {
				foreach($result->response->Results as $list) {
					$date = $list->Date;
					$d = substr($date, 0, 7);   // just get the month part of the $list->Date
					$niceDate = date("M-y", strtotime($d));
					array_push($stack, $niceDate);
				}

				return $stack;
			}

		// start of API calls

		// get the first page of results
		$result = get_actives($wrap, 1);
		$stack = add_results_to_stack(array(), $result);


		// check if we need to get more pages
		// we've already got page 1; loop from 1 until < num_pages, and get additional pages if necessary
		$num_pages = $result->response->NumberOfPages;
		for($i = 1; $i < $num_pages; $i++) {
			$next_page = $i+1;
			$result = get_actives($wrap, $next_page);
			$stack = add_results_to_stack($stack, $result);
		}

		// $stack now contains the results for all _active_ subscribers
		// get data for graph
		$graph_data = array_count_values($stack);

		// end of API calls

		$php_keys = array_keys($graph_data);
		$js_keys = json_encode($php_keys);
		$php_vals = array_values($graph_data);
		$js_vals = json_encode($php_vals);


		// just make sure we have the data to pump into the map
		if (count($php_keys) > 3) {

			?>

			<script type="text/javascript">
			jQuery(function () {
				var lineChartDataMonthly = {
						labels : <?php echo $js_keys; ?>,
						datasets : [
							{
								fillColor : "rgba(151,187,205,0.5)",
								strokeColor : "rgba(151,187,205,1)",
								pointColor : "rgba(151,187,205,1)",
								pointStrokeColor : "#fff",
								data : <?php echo $js_vals; ?>
							}
						]
					}

				var myLine = new Chart(document.getElementById("canvas-graph-1").getContext("2d")).Line(lineChartDataMonthly);
			});
			</script>

			<?php

		} else {

			?>

			<script type="text/javascript">
				jQuery(function () {

				jQuery("#subs-per-month h3").remove();

				jQuery("#subs-per-month").before("<p class=\"cm-error\">Unfortunately your list doesn't have have enough data yet.<br /><span>Give it time and a nice graph will display.</span></p>");

				});

			</script>

			<?php
		}

		die();
	}


}