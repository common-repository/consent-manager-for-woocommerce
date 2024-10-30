<?php
/**
 * Plugin Name: Consent Manager for Woocommerce
 * Description: Easily manage consent for children and adults
 * Version:     1.0
 * Author:      Arqino Digital Limited
 * Author URI:  http://arqino.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: consent-manager-for-woocommerce
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the main Consent Manager class.
if ( ! class_exists( 'Consent_Manager' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-consent-manager.php';
}

// Define WC_PLUGIN_FILE.
if ( ! defined( 'CMW_PLUGIN_FILE' ) ) {
    define( 'CMW_PLUGIN_FILE', __FILE__ );
}

/**
 * Main instance of Consent Manager.
 *
 * Returns the main instance of CM to prevent the need to use globals.
 *
 * @return Consent_Manager
 */
function CMW() {
    return Consent_Manager::instance();
}

// Global for backwards compatibility.
$GLOBALS['consent_manager'] = CMW();
