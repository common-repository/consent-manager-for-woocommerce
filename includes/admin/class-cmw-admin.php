<?php
/**
 * Consent Manager Admin
 *
 * @class    CMW_Admin
 * @author   Arqino Digital Limited
 * @category Admin
 * @version  2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * WC_Admin class.
 */
class CMW_Admin {

    /**
     * Constructor.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );
        add_action( 'init', array( $this, 'init_script' ) );
        add_action( 'admin_init', array( $this, 'buffer' ), 1 );
    }

    /**
     * Output buffering allows admin screens to make redirects later on.
     */
    public function buffer() {
        ob_start();
    }

    /**
     * Include any classes we need within admin.
     */
    public function includes() {

        include_once('cmw-admin-functions.php' );
        include_once('class-cmw-admin-menu.php' );
        include_once('class-cmw-organisation.php');
        include_once('class-cmw-admin-adults-report.php');
    }

    public function init_script()
    {

        wp_register_style( 'cmw_admin_style', dirname(dirname(plugin_dir_url(__FILE__))) . '/assets/css/admin/style.css');
        wp_enqueue_style( 'cmw_admin_style' );

        wp_register_style( 'jquery_tiptip_style',  plugin_dir_url(__FILE__) . '../../assets/css/tipTip.min.css');
        wp_enqueue_style( 'jquery_tiptip_style' );

        wp_register_style( 'jquery_modal', plugin_dir_url(__FILE__) . '../../assets/css/jquery.modal.min.css');
        wp_enqueue_style( 'jquery_modal' );

        wp_register_script( 'jquery_modal', plugin_dir_url(__FILE__) . '../../assets/js/jquery.modal.min.js', array('jquery'),null,true);
        wp_enqueue_script( 'jquery_modal' );

        wp_register_script( 'jquery_tiptip', plugin_dir_url(__FILE__) . '../../assets/js/jquery.tipTip.js', array('jquery'),null,true);
        wp_enqueue_script( 'jquery_tiptip' );

        wp_register_script( 'cmw-admin-script', dirname(dirname(plugin_dir_url(__FILE__))) . '/assets/js/admin/script.js', array('jquery','jquery-ui-datepicker'),null,true);
        $translation_array = array(
            'siteurl' => get_option('siteurl'),
            'content_dir' => WP_CONTENT_DIR,
            'content_url' => WP_CONTENT_URL,
            'admin_url'   => admin_url()
        );

        wp_localize_script('cmw-admin-script', 'CMW_variables', $translation_array);
        wp_enqueue_script( 'cmw-admin-script' );

    }
    
}

return new CMW_Admin();
