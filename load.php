<?php

defined('ABSPATH') || die();

function api_plugin_init()
{
	require_once API_FETCHER_DIR . '/Classes/Docron.php';
	require_once API_FETCHER_DIR . '/Classes/Croncreate.php';
	require_once API_FETCHER_DIR . '/controller/register-type.php';
	require_once API_FETCHER_DIR . '/controller/register-fields.php';
	require_once API_FETCHER_DIR . '/controller/create-requested-post-type.php';
	require_once API_FETCHER_DIR . '/controller/add-cron-events.php';
	if (is_admin()) {
		require_once API_FETCHER_DIR . '/view/admin-fields.php';
		require_once API_FETCHER_DIR . '/ajax/test-url.php';
		require_once API_FETCHER_DIR . '/view/admin-apiresult-fields.php';
		require_once API_FETCHER_DIR . '/controller/save-fields.php';
	}
}
add_action('plugins_loaded', 'api_plugin_init');

function api_plugin_admin_styles_scripts($hook)
{
	global $post_type;
	if ('post-new.php' != $hook && 'post.php' != $hook) {
		return;
	} elseif ($post_type == 'api_calls') {
		wp_enqueue_script('api_plugin_script', plugins_url('/api-fetcher/assets/adminscript.js'));
		wp_enqueue_style('api_plugin_style', plugins_url('/api-fetcher/assets/adminstyle.css'));
		wp_localize_script('api_plugin_script', 'api_ajax_tester', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('api_ajax_tester_nonce')
		]);
	}
}
add_action('admin_enqueue_scripts', 'api_plugin_admin_styles_scripts');
