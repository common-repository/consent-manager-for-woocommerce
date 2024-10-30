<?php
/**
 * Wrapper for get_posts specific to children.
 *
 * This function should be used for order retrieval so that when we move to
 * custom tables, functions still work.
 *
 * Args:
 * 		status array|string List of order statuses to find
 * 		type array|string Order type, e.g. shop_order or shop_order_refund
 * 		parent int post/order parent
 * 		customer int|string|array User ID or billing email to limit orders to a
 * 			particular user. Accepts array of values. Array of values is OR'ed. If array of array is passed, each array will be AND'ed.
 * 			e.g. test@test.com, 1, array( 1, 2, 3 ), array( array( 1, 'test@test.com' ), 2, 3 )
 * 		limit int Maximum of orders to retrieve.
 * 		offset int Offset of orders to retrieve.
 * 		page int Page of orders to retrieve. Ignored when using the 'offset' arg.
 * 		exclude array Order IDs to exclude from the query.
 * 		orderby string Order by date, title, id, modified, rand etc
 * 		order string ASC or DESC
 * 		return string Type of data to return. Allowed values:
 * 			ids array of order ids
 * 			objects array of order objects (default)
 * 		paginate bool If true, the return value will be an array with values:
 * 			'orders'        => array of data (return value above),
 * 			'total'         => total number of orders matching the query
 * 			'max_num_pages' => max number of pages found
 *
 * @since  2.6.0
 * @param  array $args Array of args (above)
 * @return array|stdClass Number of pages and an array of order objects if
 *                             paginate is true, or just an array of values.
 */

function cmw_get_children( $args )
{

    $children = new CMW_Children();

    return $children->getChildren($args);

}

function cmw_get_child_info($child_id)
{
    $children = new CMW_Children();

    return $children->getChild($child_id);
}

function cmw_get_child_years($dob)
{
    $birthday_timestamp = strtotime($dob);
    $age = date('Y') - date('Y', $birthday_timestamp);
    if (date('md', $birthday_timestamp) > date('md')) {
        $age--;
    }
    return $age;
}

function cmw_has_required_info($child_id)
{

    $children = new CMW_Children();

    return $children->hasRequired($child_id);
}

function cmw_has_consent($child_id)
{
    $children = new CMW_Children();

    return $children->hasConsent($child_id);
}

function cmw_update_child_agree_info($request)
{
    $children = new CMW_Children();

    $children->updateAgreeInfo($request);

    wc_add_notice( __( 'Child agree info changed successfully', 'woocommerce' ) );

    wp_safe_redirect( home_url(add_query_arg( NULL, NULL )) );

    return;
}

function cmw_validate_info($request)
{
    $rules = [
        'name'   => 'required|max:255',
        'dob'    => 'required|validDate',
        'school' => 'required'
    ];

    $flag = true;

    foreach ($rules as $key => $rule) {
        $rulesArr = explode("|", $rule);

        foreach ($rulesArr as $item) {

            $itemLength = preg_match('/max/', $rule) ? str_replace('max:', '', $item) : 0;

            if (preg_match('/max/', $item)) {
                if (! CMW_Validate::MaxLength($request[$key], $itemLength)) {
                    wc_add_notice( sprintf('Field %s must be %d characters length', $key, $itemLength), 'error' );
                    $flag = false;
                }
            }

            switch ($item) {
                case 'required' :
                    if (! CMW_Validate::requiredVal($request[$key]) ) {
                        wc_add_notice( sprintf('Field %s is required', $key), 'error' );
                        $flag = false;
                    }

                    break;
                case 'numeric' :

                    if (! CMW_Validate::isNumeric($request[$key])) {
                        wc_add_notice( sprintf('Field %s must contain numeric value', $key), 'error' );
                        $flag = false;
                    }


                    break;

                case 'validDate' :

                    if (! CMW_Validate::checkDateOfBirth($request[$key])) {
                        wc_add_notice( sprintf('Field %s must be not more than ' . date('d.m.Y'), $key), 'error' );
                        $flag = false;
                    }

                    break;
            }
        }
    }

    return $flag;
}

function cmw_update_data($request)
{

    switch ($request['route_name'])  {
        case 'agree':
            return cmw_update_child_agree_info($request);
            break;
        case 'parent_info':
            return cmw_update_parent_info($request);
            break;
        case 'privacy':
            return cmw_update_parent_privacy($request);
            break;
        case 'adult_info' :
            return update_adult_info($request);
            break;
        case 'delete_adult':
            return delete_adult($request);
            break;
        default:
            //throw new Exception('Can not find update path');
            break;
    }
    return;
}

function cmw_update_child_order_info($child_ids, $order_id)
{
    global $wpdb;

    foreach ($child_ids as $child_id) {

        $child_id_sanitized = intval(sanitize_text_field($child_id));

        if ($child_id_sanitized > 0 && is_numeric($child_id_sanitized)) {

            $wpdb->insert(
                $wpdb->prefix . 'cmw_child_to_order',
                [
                    'child_id' => $child_id,
                    'order_id' => $order_id
                ]
            );
        }

    }

}

function cmw_insert_child_and_update_order($request, $order_id)
{
    global $wpdb;

    foreach ($request['add_child_name'] as $key => $child) {
        $wpdb->insert(
            $wpdb->prefix . 'cmw_children_info',
            [
                'user_id' => get_current_user_id(),
                'name'    => sanitize_text_field($child),
                'DOB'     => date('Y-m-d', strtotime($request['add_child_years'][$key]))
            ]
        );

        $wpdb->insert(
            $wpdb->prefix . 'cmw_child_to_order',
            [
                'child_id' => $wpdb->insert_id,
                'order_id' => $order_id
            ]
        );
    }
}

function cmw_get_child_classes($child_id)
{
    $orders = CMW_ChildOrder::getChildOrders($child_id);
    $items = [];

    foreach ($orders as $order) {
        $orderObj = new WC_Order( $order->order_id );

        foreach ($orderObj->get_items() as $item) {
            $items[] = [
                'id'       => $item['item_meta']['_product_id'][0],
                'name'     => $item['name'],
                'date'     => $item['item_meta']['Booking Date'][0],
                'time'     => $item['item_meta']['Booking Time'][0],
                'duration' => $item['item_meta']['Duration'][0],
                'ts'       => strtotime($item['item_meta']['Booking Date'][0] . ' ' . $item['item_meta']['Booking Time'][0])
            ];
        }
    }

    usort($items, function ($a, $b) {
        return ($a['ts'] <= $b['ts'])
            ? -1
            : 1;
    });

    return $items;

}

function cmw_count_children_to_order($order_id)
{
    $children = new CMW_Children();

    return count($children->getChildrenToOrder($order_id));
}


function cmw_get_children_names_to_order($order_id)
{
    $children = new CMW_Children();
    $childrenInfo = $children->getChildrenToOrder($order_id);
    $names = [];

    if ($childrenInfo) {
        foreach ($childrenInfo as $child) {
            $childInfo = cmw_get_child_info($child->child_id);

            $names[] = $childInfo->name;
        }
    }

    return $names;
}
