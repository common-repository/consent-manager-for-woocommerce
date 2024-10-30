<?php
/**
 * Custom Children Details
 *
 * This is extra customer data which can be filtered by plugins. It outputs below the order item table.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-customer-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.

 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}



?>
<?php if ($children) :?>
<h2><?php _e( 'Children details', 'woocommerce' ); ?></h2>
<ul>
    <?php foreach ( $children as $child ) : ?>
        <li><strong><?php echo wp_kses_post( $child->name ); ?>:</strong> <span class="text"><?php echo wp_kses_post( cmw_get_child_years($child->DOB) . ' years' ); ?></span></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
