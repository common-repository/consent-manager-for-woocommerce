<?php

/**
 * Wrapper for get_posts specific to adult.
 *
 */

function cmw_insert_adult_and_update_order($request, $order_id)
{
    global $wpdb;

    foreach ($request['add_adult_f_name'] as $key => $adult) {

        $wpdb->insert(
            $wpdb->prefix . 'cmw_adult_to_order',
            [
                'user_id' => get_current_user_id(),
                'first_name' => sanitize_text_field($adult),
                'last_name' => sanitize_text_field($request['add_adult_l_name'][$key]),
                'email' => sanitize_email($request['add_adult_email'][$key]),
                'order_id' => $order_id
            ]
        );
    }
}
