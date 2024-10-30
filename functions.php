<?php

/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param string $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 */
function cmw_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    if ( ! empty( $args ) && is_array( $args ) ) {
        extract( $args );
    }

    $located = cmw_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '2.1' );
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters( 'cmw_get_template', $located, $template_name, $args, $template_path, $default_path );

    do_action( 'cmw_before_template_part', $template_name, $template_path, $located, $args );

    include( $located );

    do_action( 'cmw_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like wc_get_template, but returns the HTML instead of outputting.
 * @see wc_get_template
 * @since 2.5.0
 * @param string $template_name
 */
function cmw_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
    ob_start();
    cmw_get_template( $template_name, $args, $template_path, $default_path );
    return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param string $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function cmw_locate_template( $template_name, $template_path = '', $default_path = '' ) {
    if ( ! $template_path ) {
        $template_path = 'consent-manager-for-woocommerce/';
    }

    if ( ! $default_path ) {
        $default_path = CMW_PLUGIN_DIR . '../templates/';
    }

    // Look within passed path within the theme - this is priority.
    $template = locate_template(
        array(
            trailingslashit( $template_path ) . $template_name,
            $template_name
        )
    );

    // Get default template/
    if ( ! $template ) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters( 'cmw__locate_template', $template, $template_name, $template_path );
}

/**
 * @deprecated
 */
function cmw__locate_template( $template_name, $template_path = '', $default_path = '' ) {
    return cmw_locate_template( $template_name, $template_path, $default_path );
}


add_filter( 'woocommerce_checkout_fields' , 'cmw_override_default_checkout_fields' );

/**
 * Unset default fields for checkout page
 *
 * @param $fields
 * @return mixed
 */
function cmw_override_default_checkout_fields( $fields ) {

    unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_phone']);
    unset($fields['account']['account_username']);

    $fields['billing']['billing_email']['class'] = array('form-row-first');

    $fields['order']	= array(
        'order_comments' => array(
            'type' => 'textarea',
            'class' => array('notes'),
            'label' => __( 'Booking Notes', 'woocommerce' ),
            'placeholder' => _x('Anything else we need to know before you arrive for the class?', 'placeholder', 'woocommerce')
        )
    );

    return $fields;
}

add_action('woocommerce_after_checkout_billing_form', 'cmw_custom_mobile_field', 0);

function cmw_custom_mobile_field($checkout)
{
    woocommerce_form_field( 'mobile', array(
        'type'         => 'text',
        'class'        => array('form-row-last', 'validate-phone'),
        'id'           => 'mobile',
        'label'        => __('Mobile'),
        'required'     => true,
        'placeholder'  => '0XXXX-XXX-XXX',
        'autocomplete' => 'tel',
        'default'      => is_user_logged_in() ? cmw_get_phone() : ''
    ), $checkout->get_value( 'mobile' ));
}

add_action('woocommerce_checkout_process', 'cmw_mobile_checkout_field_process');

function cmw_mobile_checkout_field_process() {

    if ( ! $_POST['mobile'] )
        wc_add_notice( __( 'Mobile is a required field.' ), 'error' );

    if ($_POST['add_child_name']) {
        foreach ( $_POST['add_child_name'] as $name ) {
            if ( ! $name ) {
                wc_add_notice( __( 'Child name is a required field.' ), 'error' );
            }
        }
    }

    if ($_POST['add_child_years']) {
        foreach ( $_POST['add_child_years'] as $dob ) {
            if (!CMW_Validate::checkDateOfBirth($dob))
                wc_add_notice( 'Field Date of Birth must be not more than ' . date('d.m.Y'), 'error' );

            if (!CMW_Validate::checkDateOfBirthNoMoreThat18Years($dob))
                wc_add_notice( 'Only persons under the age of 18 may be added to orders as children.',  'error' );
        }
    }

}

function cmw_get_phone()
{
    global $wpdb;

    $user_id = get_current_user_id();

    return $wpdb->get_row("SELECT mobile FROM {$wpdb->prefix}cmw_parent_info WHERE user_id = {$user_id}")->mobile;

}

add_action('woocommerce_after_checkout_billing_form', 'cmw_custom_child_field', 1);

function cmw_custom_child_field($checkout)
{
    $personsCount = 1;
    $postTags = [];

    // TODO if there are 2 classes with different persons count
    foreach (WC()->cart->get_cart() as $item) {

        $tags = wp_get_post_terms($item['product_id'], 'product_tag');

        foreach ($tags as $tag) {

            $postTags[] = $tag->name;
        }

        $personsCount = $item['booking']['Persons'] ? : $item['quantity'];
    }

    if (array_search('Children', $postTags) !== false || array_search('Adults', $postTags) !== false) {
        echo '<div class="adults_and_children">';
        cmw_add_children_counter($personsCount, $checkout);
        echo '</div>';

        echo '<div class="children_info"></div>';

        echo '<hr/>';

        if (array_search('Adults', $postTags) !== false) {

            echo '<div class="adults_counter_input">';
            cmw_add_adults_counter($personsCount, $checkout);
            echo '</div>';

            echo '<div class="adults_info"></div>';
        }


    }

}

function cmw_draw_children_select($checkout, $options, $count)
{
    for ($i = 0; $i < $count; $i++) {
        echo '<div class="child-row">';
        woocommerce_form_field( 'choose_child[]', array(
            'type'        => 'select',
            'class'       => array('my-field-class form-row-wide'),
            'label'       => __('Choose child (only children with completed emergency and consent details are shown)'),
            'options'     => $options,
            'required'    => true,
        ), $checkout->get_value( 'choose_child' ));
        echo '</div>';
    }
}

function cmw_draw_add_child_form($checkout, $count)
{
    for ($i = 0; $i < $count; $i++) {
        echo '<div class="child-row">';
        woocommerce_form_field( 'add_child_name[]', array(
            'type'        => 'text',
            'class'       => array('my-field-class', 'form-row-wide'),
            'label'       => __('Child name'),
            'required'    => true,
        ), $checkout->get_value( 'choose_child' ));

        woocommerce_form_field( 'add_child_years[]', array(
            'type'        => 'text',
            'id'          => 'datepicker_' . $i,
            'input_class' => array('my-field-class', 'form-row-wide', 'datepicker'),
            'placeholder' => 'DD.MM.YYYY',
            'label'       => __('Date of birth'),
            'required'    => true,
        ), $checkout->get_value( 'choose_child' ));
        echo '</div>';
    }
}

function cmw_add_children_counter($count, $checkout) {
    echo '<p class="form-row my-field-class form-row-wide validate-required" id="children_count_field">
    <label>Number of children attending</label>';
    echo '<div class="input-group number-spinner">
				<span class="input-group-btn">
					<a class="btn btn-default" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></a>
				</span>';
    echo '<input type="text" name="children_count" min="0" max="' . $count . '" class="input-text form-control text-center" value="0" />';

    echo '<span class="input-group-btn">
					<a class="btn btn-default" data-dir="up"><span class="glyphicon glyphicon-plus"></span></a>
				</span>
			</div></p>';
}

function cmw_add_adults_counter($count, $checkout) {


    echo '<p class="form-row my-field-class form-row-wide validate-required" id="adults_count_field">
    <label>Number of adults (excluding you)</label>';
    echo '<div class="input-group number-spinner">
				<span class="input-group-btn">
					<a class="btn btn-default" data-dir="dwn"><span class="glyphicon glyphicon-minus"></span></a>
				</span>';
    echo '<input type="text" id="adults_count" name="adults_count" min="0" max="' . $count . '" class="input-text form-control text-center" value="0" />';

    echo '<span class="input-group-btn">
					<a class="btn btn-default" data-dir="up"><span class="glyphicon glyphicon-plus"></span></a>
				</span>
			</div></p>';

}

/**
 * Update the order meta with field value
 **/
add_action('woocommerce_checkout_update_order_meta', 'cmw_custom_checkout_child_field_update_order_meta');

function cmw_custom_checkout_child_field_update_order_meta( $order_id )
{

    if ($_POST['mobile']) {

        $_POST['mobile'] = sanitize_text_field($_POST['mobile']);

        cmw_update_parent_mobile($_POST);
    }

    if ($_POST['choose_child']) {

        cmw_update_child_order_info($_POST['choose_child'], $order_id);

    }

    if ($_POST['add_child_name']) {

        cmw_insert_child_and_update_order($_POST, $order_id);

    }


    if ($_POST['add_adult_f_name']) {
        cmw_insert_adult_and_update_order($_POST, $order_id);
    }

    $parent = new CMW_ParentInfo();
    $parent->updateParentMobile(['mobile' => sanitize_text_field($_POST['billing_phone'])]);

    if ($_POST['terms']) {
        update_user_meta(get_current_user_id(), 'booking_terms', sanitize_text_field($_POST['terms']));
    }

    if ($_POST['social']) {

        global $wpdb;

        $wpdb->update(
            $wpdb->prefix . 'cmw_parent_info',
            [
                'share_agree' => 1
            ],
            [
                'user_id' => get_current_user_id()
            ]
        );

    }

    if ($_POST['subscribe_to_news']) {

        if (!get_user_meta(get_current_user_id(), 'subscribe_to_news', true)) {
            update_user_meta(get_current_user_id(), 'subscribe_to_news', [0 => 'email']);
        }

    }

}

function cmw_update_child_to_order($child_id, $order_id)
{
    global $wpdb;

    $data = [
        'child_id' => $child_id,
        'order_id' => $order_id
    ];

    $wpdb->insert(
        $wpdb->prefix . 'cmw_child_to_order',
        $data
    );

}

add_action('woocommerce_order_details_after_order_table', 'cmw_custom_child_info', 20);

function cmw_custom_child_info($order)
{
    global $wpdb;

    $children = $wpdb->get_results("SELECT ci.name, ci.DOB FROM {$wpdb->prefix}cmw_child_to_order co
                                    LEFT JOIN {$wpdb->prefix}cmw_children_info ci
                                    ON (co.child_id = ci.id)
                                    WHERE order_id = {$order->id}");

    echo '<table><tr><td colspan="2"><strong>Children Info</strong></td></tr>';

    foreach($children as $child) {
        echo '<tr>
			<td>' . $child->name . '</td>
			<td>' . cmw_get_child_years($child->DOB) . '</td>
		</tr>';
    }

    echo '</table>';
}

add_action('woocommerce_order_details_after_order_table', 'cmw_custom_adult_info', 21);

function cmw_custom_adult_info($order)
{
    global $wpdb;

    $adults = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cmw_adult_to_order co
                                    WHERE order_id = {$order->id}");

    echo '<table><tr><td colspan="2"><strong>Adults Info</strong></td></tr>';


    foreach($adults as $adult) {
        echo '<tr>
			<td>' . $adult->first_name . ' ' . $adult->last_name . '</td>
			<td>' . $adult->email . '</td>
		</tr>';
    }

    echo '</table>';
}


add_action('add_checkbox', 'cmw_add_checkbox_for_address_duplicate');

function cmw_add_checkbox_for_address_duplicate()
{

    $user_id = get_current_user_id();
    $parent = new CMW_ParentInfo();
    $address = $parent->getParentInfo($user_id)->address;

    $checked = $parent->get_billing_address()['address'] == $address ? 'checked' : '';

    // TODO remove style to styles.css
    echo '<div id="billing_checkbox" style="float: right; margin-top: 10px;">
            <label>
              <input type="checkbox" name="same_to_mydetails" class="same_to_mydetails" ' .
        $checked . '/>
                Use this address in My Details
            </label>
          </div>';
}


add_action('woocommerce_customer_save_address', 'cmw_check_if_checkbox_is_checked', 10, 2);

function cmw_check_if_checkbox_is_checked($user_id)
{

    if ($_POST['same_to_mydetails']) {

        $address = '';
        $parent = new CMW_ParentInfo();

        if (!empty($_POST['billing_address_2'])) $address .= sanitize_text_field($_POST['billing_address_2']) . ' ';
        if (!empty($_POST['billing_address_1'])) $address .= sanitize_text_field($_POST['billing_address_1']) . ' ';
        if (!empty($_POST['billing_city'])) $address .= sanitize_text_field($_POST['billing_city']) . ' ';
        if (!empty($_POST['billing_country'])) $address .= sanitize_text_field($_POST['billing_country']) . ' ';

        $parent->updateParent([
            "address"  => $address,
            "postcode" => sanitize_text_field($_POST['billing_postcode']),
            "user_id"  => $user_id
        ]);

    }
}
