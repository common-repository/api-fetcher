<?php

defined('ABSPATH') || die();

// CREATE THE POST TYPE IF NEEDED, AFTER API POST
function api_plugin_createPostType()
{

	$apiQuery = array(
		'post_type' => 'api_calls',
		'post_status' => 'publish',
		'posts_per_page' => -1
	);

	$publishedEntries = array();
	// The Query
	$apiPostsQuery = new WP_Query($apiQuery);
	// The Loop
	while ($apiPostsQuery->have_posts()) : $apiPostsQuery->the_post();
		$queryPostId = get_the_id();
		$shouldCreateNew = get_post_meta($queryPostId, 'api__post_field');
		if ($shouldCreateNew[0] == 'api__create_new') {
			$shouldUseThisTitle = get_post_meta($queryPostId, 'api__new_post_type_name');
			if ($shouldUseThisTitle[0] != '') {
				array_push($publishedEntries, $shouldUseThisTitle[0]);
			}
		}
	endwhile;
	// Reset Query
	wp_reset_postdata();

	foreach ($publishedEntries as $entry) :
		$typeName = $entry;
		$typeSlug = sanitize_title($entry);
		// Set UI labels for Custom Post Type
		$labels = array(
			'name'                => $typeName,
			'singular_name'       => $typeName,
			'menu_name'           => $typeName,
			'parent_item_colon'   => 'Parent' . $typeName,
			'all_items'           => 'All ' . $typeName,
			'view_item'           => 'View ' . $typeName,
			'add_new_item'        => 'Add New ' . $typeName,
			'add_new'             => 'Add New ',
			'edit_item'           => 'Edit ' . $typeName,
			'update_item'         => 'Update ' . $typeName,
			'search_items'        => 'Search ' . $typeName,
			'not_found'           => 'Not Found',
			'not_found_in_trash'  => 'Not found in Trash',
		);
		// Set other options for Custom Post Type     
		$args = array(
			'label'               => $typeSlug,
			'description'         => 'Created by the API plugin',
			'labels'              => $labels,
			// Features this CPT supports in Post Editor
			'supports'            => array('title'),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			/* A hierarchical CPT is like Pages and can have
	        * Parent and child items. A non-hierarchical CPT
	        * is like Posts.
	        */
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 40,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			'show_in_rest' 		  => true,
		);
		// Registering your Custom Post Type
		$registerThis = register_post_type($typeSlug, apply_filters( 'api_plugin_register_custom_post_type', $args ));
	endforeach;
}
add_action('init', 'api_plugin_createPostType', 3);
