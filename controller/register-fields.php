<?php

defined('ABSPATH') || die();

// CREATE FIELDS FOR THE API CALL POST TYPE
add_action('admin_init', 'register_meta_boxes');
function register_meta_boxes()
{
	add_meta_box(
		'meta-box-id',
		'API Call settings',		// Title of custom fields
		'register_fields_callback',	// Custom fields function
		'api_calls', 				// Custom post type
		'normal',
		'high' 						// Position edit page
	);
}
