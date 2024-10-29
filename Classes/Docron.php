<?php
/**
 * Class to execute cron
 */

namespace ApiFetcher;

class Docron
{

    public static function executeEvent($argPostId)
    {

        $cronPostMeta = get_post_meta($argPostId);

        $apiUrl = $cronPostMeta['api__url_field'][0];

        $apiUrl = wp_remote_get(esc_url_raw( $apiUrl )); // no need to escape entities

        $results = wp_remote_retrieve_body($apiUrl);

        // CREATE ACTION AFTER API IS CALLED
        do_action('api_plugin_after_api_called');

        if ($cronPostMeta['api__object_in_array_key'][0]) :
            // turn it into a PHP array from JSON string
            $results = json_decode($results);
            $offsetPoint = $cronPostMeta['api__object_in_array_key'][0];
            $results = $results->$offsetPoint;
            $newResults = json_decode(json_encode($results), true);
        else :
            $results = json_decode($results);
            $newResults = json_decode(json_encode($results), true);
            $newResults = array($newResults);
        endif;

        foreach ($newResults as $post) :
            $titleFieldkey = $cronPostMeta['api__use_as_post_title'][0];
            if ($cronPostMeta['api__post_field'][0] != 'api__create_new') :
                $targetPostType = $cronPostMeta['api__post_field'][0];
            else :
                $targetPostType = sanitize_title($cronPostMeta['api__new_post_type_name'][0]);
            endif;
            $post_slug = sanitize_title($post[$titleFieldkey]);

            $existing_post = get_page_by_title($post_slug, 'OBJECT', $targetPostType);

            if ($existing_post === null) {
                $inserted_post = wp_insert_post([
                    'post_name' => $post_slug,
                    'post_title' => $post_slug,
                    'post_type' => $targetPostType,
                    'post_status' => 'publish'
                ], true);

                // CREATE POST META TO DEFINE IT'S A API PLUGIN POST
                update_post_meta($inserted_post, 'is__api__post', 1);

                // CREATE ACTION AFTER POST IS INSERTED BEFORE API RESPONSE VALUES ARE SAVED
                do_action('api_plugin_before_response_saved');

                // SET META VALUE OF POST WITH RECEIVED VALUES FROM THE API
                apply_filters('api_plugin_before_saving_value', $post);
                
                foreach ($post as $key => $value) {
                    if (is_array($value)) :
                        $encodeArray = json_encode($value);
                        update_post_meta($inserted_post, 'api__field__' . $key, $encodeArray);
                    else :
                        update_post_meta($inserted_post, 'api__field__' . $key, sanitize_textarea_field($value));
                    endif;
                }

                if (is_wp_error($inserted_post)) :
                    continue;
                endif;
            }

        endforeach;
    }

    public static function doEvent($args)
    {
        $argPostId = intval($args);
        Docron::executeEvent($argPostId);
    }
}
