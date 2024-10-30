<?php
add_action('woocommerce_email_customer_details', 'cmw_show_customer_order_details', 0);

function cmw_show_customer_order_details($order, $sent_to_admin, $plain_text, $email)
{

    cmw_get_customer_details($order, $sent_to_admin);

    cmw_get_children_details($order);
}


function cmw_get_customer_details($order, $sent_to_admin)
{

    $fields = array();

    if ($order->customer_note) {
        $fields['customer_note'] = array(
            'label' => __('Note', 'woocommerce'),
            'value' => wptexturize($order->customer_note)
        );
    }

    if ($order->billing_email) {
        $fields['billing_email'] = array(
            'label' => __('Email', 'woocommerce'),
            'value' => wptexturize($order->billing_email)
        );
    }

    if ($order->billing_phone) {
        $fields['billing_phone'] = array(
            'label' => __('Tel', 'woocommerce'),
            'value' => wptexturize($order->billing_phone)
        );
    }

    $customer_id = get_post_meta( $order->id, '_customer_user', true );

    $customer_info = cmw_get_parent_info($customer_id);

    if ($customer_info->mobile) {
        $fields['billing_phone'] = array(
            'label' => __('Tel', 'woocommerce'),
            'value' => wptexturize($customer_info->mobile)
        );
    }


    wc_get_template('emails/email-customer-details.php', array('fields' => $fields));

    return;
}

function cmw_get_children_details($order)
{
    global $wpdb;

    $children = $wpdb->get_results("SELECT child_id, name, DOB FROM {$wpdb->prefix}cmw_child_to_order cto
                                    LEFT JOIN {$wpdb->prefix}cmw_children_info ci ON (cto.child_id = ci.`id`)
                                    WHERE order_id = {$order->id}");

    cmw_get_template('emails/children_order_details.php', compact('children'));
}
