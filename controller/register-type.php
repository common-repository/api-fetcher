<?php

defined('ABSPATH') || die();

/*
* Creating a function to create our CPT
*/

function create_api_post_type()
{
    // Set UI labels for Custom Post Type
    $labels = array(
        'name'                => 'API Calls',
        'singular_name'       => 'Calls',
        'menu_name'           => 'API Calls',
        'parent_item_colon'   => 'Parent Call',
        'all_items'           => 'All API Calls',
        'view_item'           => 'View API Call',
        'add_new_item'        => 'Add New API Call',
        'add_new'             => 'Add New',
        'edit_item'           => 'Edit API Call',
        'update_item'         => 'Update API Call',
        'search_items'        => 'Search API Call',
        'not_found'           => 'Not Found',
        'not_found_in_trash'  => 'Not found in Trash',
    );
    // Set other options for Custom Post Type     
    $args = array(
        'label'               => 'api_call',
        'description'         => 'API Calls fields',
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array('title'),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 40,
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
        'show_in_rest'           => true,
        'menu_icon'   => 'dashicons-sort',
    );

    // Registering your Custom Post Type
    register_post_type('api_calls', $args);
}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action('init', 'create_api_post_type', 0);
