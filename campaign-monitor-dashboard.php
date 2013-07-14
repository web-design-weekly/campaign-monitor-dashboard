<?php
/**
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 *
 * @wordpress-plugin
 * Plugin Name: Campaign Monitor Dashboard
 * Plugin URI:  http://web-design-weekly.com
 * Description: Display stats from you Campaign Monitor email list.
 * Version:     1.0.0
 * Author:      Jake Bresnehan
 * Author URI:  http://web-design-weekly.com
 * Text Domain: plugin-name-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists ( 'CS_REST_Subscribers' ) )
    include_once ( plugin_dir_path( __FILE__ ) . 'cm-php/csrest_subscribers.php' );

if ( ! class_exists ( 'CS_REST_Lists' ) )
    include_once ( plugin_dir_path( __FILE__ ) . 'cm-php/csrest_lists.php' );

include_once( plugin_dir_path( __FILE__ ) . 'class-campaign-monitor-dashboard.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
register_activation_hook( __FILE__, array( 'CampaignMonitorDashboard', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'CampaignMonitorDashboard', 'deactivate' ) );

CampaignMonitorDashboard::get_instance();