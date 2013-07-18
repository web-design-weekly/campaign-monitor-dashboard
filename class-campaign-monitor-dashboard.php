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

		// Load admin Stylesheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Load plugin settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Loads main Campaign Monitor settings panel
		add_action( 'wp_ajax_get_cm_settings', array( $this, 'process_cm_settings' ) );
		
		
		if ( get_option('cm_dashboard_widget_option') == "on" ) {
			add_action('wp_dashboard_setup', array( $this, 'add_cm_dashboard_widget' ) );
		}

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

	}

	/**
	 * Register the administration menu into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {
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
		include_once( 'views/widget.php' );
	}

	/**
	 * Register the dashboard widget.
	 *
	 * @since    1.0.0
	 */
	public function add_cm_dashboard_widget() {
		wp_add_dashboard_widget(
			'cm_dashboard_widget', 
			'Campaign Monitor', 
			array( $this, 'dashboard_widget_view' )
		);	
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

}