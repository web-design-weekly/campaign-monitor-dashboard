<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   CampaignMonitorDashboard
 * @author    Jake Bresnehan <hello@jakebresnehan.com>
 * @license   GPL-2.0+
 * @link      http://web-design-weekly.com
 * @copyright 2013 Jake Bresnehan
 */

// If uninstall, not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// TODO: Define uninstall functionality here