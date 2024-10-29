<?php

defined('ABSPATH') || die();

// CREATE CRON EVENTS AFTER POST TYPE HAS BEEN CREATED
function create_api_cron_events()
{

	$publishedEntries = array();
	// The Query
	global $wpdb;

	$prepared_query = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='api_calls' AND post_status = 'publish'");

	$publishedEntries = $prepared_query;

	foreach ($publishedEntries as $pubEntry) {

		do_action('api_plugin_before_add_event');

		$entryId = $pubEntry->ID;
		$allPostMeta = get_post_meta($entryId);
		$thisApiRequestTitle = sanitize_title($pubEntry->post_title);
		$thisApiRequestHook = 'api__' . $thisApiRequestTitle;
		add_action($thisApiRequestHook, ['ApiFetcher\Docron', 'doEvent']);

		$isExisting = wp_next_scheduled($thisApiRequestHook, array($entryId));
		if (!$isExisting) :
			$timeRecurrence = $allPostMeta['api__time_field'][0];
			$scheduleEvent = wp_schedule_event(time(), $timeRecurrence, $thisApiRequestHook, array($entryId));
			if ($scheduleEvent) :
			//echo '<pre>Scheduled event returned true, succes</pre>';
			else :
				echo '<pre>Something is wrong with your API Fetcher plugin';
				if (is_wp_error($scheduleEvent)) :
					print_r(' wp error ');
				endif;
				echo '</pre>';
			endif;
		else :
			continue;
		endif;

		do_action( 'api_plugin_after_add_event' );

	}
}
create_api_cron_events();
