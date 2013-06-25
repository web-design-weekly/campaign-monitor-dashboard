<?php
/**
 * Campaign Monitor Dashboard.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */

/**
 * Plugin class.
 *
 * @package CampaignMonitorDashboard
 * @author  Jake Bresnehan <hello@jakebresnehan.com>
 */

class CampaignMonitorDashboard {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '1.0.0';

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

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register widget
		add_action( 'widgets_init', create_function( '', 'register_widget( "campaign_monitor_widget" );' ) );


		// Define custom functionality. Read more about actions and filters: http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		//add_action( 'TODO', array( $this, 'action_method_name' ) );
		//add_filter( 'TODO', array( $this, 'filter_method_name' ) );

		// Load plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// loads JavaScript Ajax

		add_action( 'wp_ajax_get_cm_settings', array( $this, 'process_cm_settings' ) );
		add_action( 'wp_ajax_get_month_graph', array( $this, 'process_graph_data' ) );



		//add_action('widgets_init', array( $this, 'plugin_widget') );
		// Load the widget for plugin
		//add_action( 'wp_register_sidebar_widget', array( $this, 'plugin_widget' ) );


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
		}

		// Maybe loading this can be done better?
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script-plugins', plugins_url( 'js/admin-plugins.js', __FILE__ ), array( 'jquery' ), $this->version );
		}

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * TODO:
		 *
		 * Change 'Page Title' to the title of your plugin admin page
		 * Change 'Menu Text' to the text for menu item for the plugin settings page
		 * Change 'plugin-name' to the name of your plugin
		 */
		$this->plugin_screen_hook_suffix = add_plugins_page(
			__( 'Campaign Monitor Dashboard', $this->plugin_slug ),
			__( 'Campaign Monitor', $this->plugin_slug ),
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Registers the settings for this plugin.
	 *
	 * @since    1.0.0
	 */

	public function register_settings() {
		//register our settings
		register_setting( 'option-group', 'cm_api_option' );
		register_setting( 'option-group', 'cm_list_id_option' );
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
	 * Ajax main Campaign Monitor settings panel
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

			echo "<div class=\"current-list\">";
			echo "<h3>" .$result->response->Title. "</h3>";
			echo "<p>Total Subscribers: " .$total_active_subscribers. "</p>";
			echo "<p class=\"unsub\">Total Unsubscribers: " .$total_unsubscribers. "</p>";
			echo "</div>";

			echo "<div class=\"stats\">";
			echo "<div class=\"sub-stats\">";
			echo "<h4>Subscribers</h4>";
			echo "<p><span>Today</span> " .$new_sub_today. "</p>";
			echo "<p><span>Yesterday</span> " .$new_sub_yesterday. "</p>";
			echo "<p><span>This Week</span> " .$new_sub_this_week. "</p>";
			echo "<p><span>This Month</span> " .$new_sub_this_month. "</p>";
			echo "<p><span>This Year</span> " .$new_sub_this_year. "</p>";
			echo "</div>";

			echo "<div class=\"sub-stats\">";
			echo "<h4>Unsubscribers</h4>";
			echo "<p><span>Today</span> " .$un_sub_today. "</p>";
			echo "<p><span>Yesterday</span> " .$un_sub_yesterday. "</p>";
			echo "<p><span>This Week</span> " .$un_sub_this_week. "</p>";
			echo "<p><span>This Month</span> " .$un_sub_this_month. "</p>";
			echo "<p><span>This Year</span> " .$un_sub_this_year. "</p>";
			echo "</div>";
			echo "</div>";

			?>
				<!-- Would like to have this in admin js... -->
				<script type="text/javascript">
					jQuery('.settings-form').addClass('successful-credentials');
					jQuery('.waiting').hide();
				</script>
			<?php

		} else {
			echo '<p class="cm-error">';
			echo 'Please add your correct credentials <span>to get the ball rolling.</span>';
			echo '</p>';
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
	 * Ajax subscribers per month graph data
	 *
	 * @since    1.0.0
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

				echo '<p>Failed with code '.$result->http_status_code."</p>";
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
		die();
	}

}