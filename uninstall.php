<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
	exit();
}

function api_plugin_delete_plugin()
{
	global $wpdb;

	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'api_calls',
			'post_status' => 'any',
		)
	);
	foreach ($posts as $post) {
		wp_delete_post($post->ID, true);
	}

	$apiQuery = array(
		'post_type' => 'api_calls',
		'post_status' => 'publish',
		'posts_per_page' => -1
	);
	// The Query
	query_posts($apiQuery);
	// The Loop
	while (have_posts()) : the_post();
		$post = get_post();
		$theTitle = sanitize_title($post->title);
		wp_clear_scheduled_hook('api__' . $theTitle);
	endwhile;
	// Reset Query
	unregister_post_type('api_calls');

	wp_reset_query();
}

api_plugin_delete_plugin();
