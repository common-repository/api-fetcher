<?php

defined('ABSPATH') || die();

function api_test_ajax()
{
    check_ajax_referer('api_ajax_tester_nonce');

    $testUrl =  esc_url($_POST['testurl']);

    $results = wp_remote_retrieve_body(wp_remote_get($testUrl));
    if ($results) {
        $response['succes'] = true;
    }
    $response = $results;
    echo $response;
    die();
}

add_action('wp_ajax_api_test_ajax', 'api_test_ajax');
