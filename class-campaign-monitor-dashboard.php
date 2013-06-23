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

		add_action( 'wp_ajax_aad_get_results', array( $this, 'aad_process_ajax' ) );


		//add_action('wp_ajax_aad_get_results', 'aad_process_ajax');



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


	/* Dummy Functions  for dev*/

	/**
	 * NOTE:  Actions are points in the execution of a page or process
	 *        lifecycle that WordPress fires.
	 *
	 *        WordPress Actions: http://codex.wordpress.org/Plugin_API#Actions
	 *        Action Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// TODO: Define your action hook callback here
	}

	/**
	 * NOTE:  Filters are points of execution in which WordPress modifies data
	 *        before saving it or sending it to the browser.
	 *
	 *        WordPress Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *        Filter Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// TODO: Define your filter hook callback here
	}

	public function process_cm_settings() {
		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');

		$auth = array('api_key' => $cm_api);
		$wrap = new CS_REST_Lists($cm_list, $auth);

		$result = $wrap->get();
		$stats_result = $wrap->get_stats();
		// $segments_result = $wrap->get_segments();

		if($result->was_successful()) {
			$total_active_subscribers = $stats_result->response->TotalActiveSubscribers;
			echo "<div class=\"sub-stats\">";
			echo "<p>List - " .$result->response->Title. "</p>";
			echo "<p>Total Subscribers - " .$total_active_subscribers. "</p>";
			echo "</div>";
			?>
				<!-- Would like to have this in admin js... -->
				<script type="text/javascript">
					jQuery('.settings-form').addClass('successful-credentials');
					jQuery('.waiting').hide();
				</script>
			<?php

		} else {
			echo '<p>';
			echo 'Please add your correct credentials to get the ball rolling.';
			echo '</p>';
			?>
				<!-- Would like to have this in admin js... -->
				<script type="text/javascript">
					jQuery('.major-settings').toggle();
					jQuery('.waiting').hide();
				</script>
			<?php



		}

			die();
	}


	public function aad_process_ajax() {
		$cm_api = get_option('cm_api_option');
		$cm_list = get_option('cm_list_id_option');
		$auth = array('api_key' => $cm_api);

		$wrap = new CS_REST_Lists($cm_list, $auth);

		function get_actives($auth, $page_number)
		{
		    // last 365 days
		    $date_from = date('Y-m-d', strtotime('-365 days'));

		    // params: start date, page number, page size, order by, order direction
		    $result = $auth->get_active_subscribers($date_from, $page_number, 1000, 'date', 'asc');

		    if($result->was_successful()) {
		        echo "<p>Got subscribers</p>";
		        echo "<pre>";

		        echo("ResultsOrderedBy ".$result->response->ResultsOrderedBy."\n");
		        echo("OrderDirection ".$result->response->OrderDirection."\n");
		        echo("PageNumber ".$result->response->PageNumber."\n");
		        echo("PageSize ".$result->response->PageSize."\n");
		        echo("RecordsOnThisPage ".$result->response->RecordsOnThisPage."\n");
		        echo("TotalNumberOfRecords ".$result->response->TotalNumberOfRecords."\n");
		        echo("NumberOfPages ".$result->response->NumberOfPages."\n");
		        echo "</pre>";

		    } else {
		        echo "<pre>";
		        echo '<p>Failed with code '.$result->http_status_code."</p>";
		        var_dump($result->response);
		        echo "</pre>";
		    }

		    return $result;
		}

			function add_results_to_stack($stack, $result)
			{
			    foreach($result->response->Results as $list) {
			        $date = $list->Date;
			        $d = substr($date, 0, 7);   // just get the month part of the $list->Date
			        // could convert this to pretty text for month?
			        array_push($stack, $d);
			    }

			    print_r("Size of stack: ".sizeof($stack));

			    return $stack;
			}

    // **-**

    // start of API calls
    //

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

    //
    // end of API calls

    // **-**


    // hard-coded test data
    // to use comment out from **-** to **-**
    // $graph_data = array("2012-06"=>67, "2012-07"=>185, "2012-08"=>663, "2012-09"=>112, "2012-10"=>160, "2012-11"=>234, "2012-12"=>1569, "2013-01"=>2059, "2013-02"=>824, "2013-03"=>559, "2013-04"=>526, "2013-05"=>1600, "2013-06"=>442);

    $php_keys = array_keys($graph_data);
     $js_keys = json_encode($php_keys);

    $php_vals = array_values($graph_data);
     $js_vals = json_encode($php_vals);


    // create data for the running total graph

    $graph_data_running = $graph_data;
    $len = sizeof($graph_data_running)-1;

    $keys = array_keys($graph_data);

    // set last element of running total as total_active_subscribers
    // then calculate previous months by working backwords
    //$graph_data_running[$keys[$len]] = $total_active_subscribers;
    //$total_active_subscribers = $graph_data_running[$keys[$len]];

    for ($i=$len-1; $i >= 0 ; $i--) {
        $graph_data_running[$keys[$i]] = $graph_data_running[$keys[$i+1]] - $graph_data[$keys[$i+1]];
    }

    $php_keys_running = array_keys($graph_data_running);
     $js_keys_running = json_encode($php_keys_running);

    $php_vals_running = array_values($graph_data_running);
     $js_vals_running = json_encode($php_vals_running);


    echo '<pre>';
        print_r($graph_data);
        print_r($graph_data_running);
    echo '</pre>';

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

    var lineChartDataRunning = {
            labels : <?php echo $js_keys_running; ?>,
            datasets : [
                {
                    fillColor : "rgba(151,187,205,0.5)",
                    strokeColor : "rgba(151,187,205,1)",
                    pointColor : "rgba(151,187,205,1)",
                    pointStrokeColor : "#fff",
                    data : <?php echo $js_vals_running; ?>
                }
            ]
        }
    var optionsRunning = {bezierCurve : false};

    var myLine = new Chart(document.getElementById("canvas-graph-1").getContext("2d")).Line(lineChartDataMonthly);
    var myLine = new Chart(document.getElementById("canvas-graph-2").getContext("2d")).Line(lineChartDataRunning, optionsRunning);
});

</script>
<?php
		die();
	}



}