<?php
/*
 * Plugin Name: API Fetcher
 * Plugin URI: https://www.wesselhampsink.nl/api-fetcher-plugin
 * Description: This plugin manages cron API calls, can perform scheduled API requests and store the results in posts and custom fields. Ideal for developers that want to practice with API data.
 * Version: 1.0
 * Author: Wessel Hampsink
 * Author URI: https://www.wesselhampsink.nl
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined('ABSPATH') || die();

define('API_FETCHER_FILE', __FILE__);

define('API_FETCHER_DIR', untrailingslashit(dirname(API_FETCHER_FILE)));

require_once API_FETCHER_DIR . '/load.php';

/**
 * Deactivation hook.
 */
function api_plugin_deactivate()
{
    unregister_post_type('api__calls');
}
register_deactivation_hook(__FILE__, 'api_plugin_deactivate');
