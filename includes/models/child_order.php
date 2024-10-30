<?php

class CMW_ChildOrder {

    public static function insertChildOrders($child_id, $order_id)
    {

        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'cmw_child_to_order',
            [
                'child_id' => $child_id,
                'order_id' => $order_id
            ]
        );

    }

    public function updateChildOrders($child_ids, $order_id)
    {
        global $wpdb;

        foreach ($child_ids as $child_id) {
            $wpdb->insert(
                $wpdb->prefix . 'cmw_child_to_order',
                [
                    'child_id' => $child_id,
                    'order_id' => $order_id
                ]
            );
        }

    }

    public static function getChildOrders($child_id)
    {
        global $wpdb;

        return $wpdb->get_results("SELECT order_id FROM {$wpdb->prefix}cmw_child_to_order WHERE child_id = {$child_id}");
    }
}
