<?php

defined('ABSPATH') || die();

// SAVE FIELDS
function save_api_fields($post_id)
{
	// CHECK SAVE STATUS
	$is_autosave = wp_is_post_autosave($post_id);
	$is_revision = wp_is_post_revision($post_id);
	$is_valid_nonce = (isset($_POST['api__plugin_nonce']) && wp_verify_nonce($_POST['api__plugin_nonce'], basename(__FILE__))) ? 'true' : 'false';

	// EXITS SCRIPT DEPENDING ON SAVE STATUS
	if ($is_autosave || $is_revision || !$is_valid_nonce) {
		return;
	}

	if (isset($_POST['api__url_field'])) {
		update_post_meta($post_id, 'api__url_field', esc_url_raw($_POST['api__url_field']));
	}
	if (isset($_POST['api__new_post_type_name'])) {
		update_post_meta($post_id, 'api__new_post_type_name', sanitize_text_field($_POST['api__new_post_type_name']));
	}
	if (isset($_POST['api__time_field'])) {
		update_post_meta($post_id, 'api__time_field', sanitize_text_field($_POST['api__time_field']));
	}
	if (isset($_POST['api__post_field'])) {
		update_post_meta($post_id, 'api__post_field', sanitize_text_field($_POST['api__post_field']));
	}
	if (isset($_POST['api__use_as_post_title'])) {
		update_post_meta($post_id, 'api__use_as_post_title', sanitize_text_field($_POST['api__use_as_post_title']));
	}
	if (isset($_POST['api__object_in_array_key'])) {
		update_post_meta($post_id, 'api__object_in_array_key', sanitize_text_field($_POST['api__object_in_array_key']));
	}
}
add_action('save_post', 'save_api_fields');
