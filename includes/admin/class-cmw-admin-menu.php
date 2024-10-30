<?php
/**
 * Setup menus in WP admin.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CMW_Admin_Menu' ) ) :

    /**
     * CMW_Admin_Menus Class.
     */
    class CMW_Admin_Menu {

        /**
         * Hook in tabs.
         */
        public function __construct() {
            // Add menus
            add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );

        }

        /**
         * Add menu items.
         */
        public function admin_menu() {
           // global $menu;
            global $submenu;

            add_menu_page( 'Settings', 'Consent Manager', 'administrator', 'cmw_plugin', array($this, 'consent_settings'), '' );
            add_submenu_page( 'cmw_plugin', 'Settings', 'Settings', 'administrator', 'consent_settings', array( $this, 'consent_settings' ) );

            add_submenu_page( 'cmw_plugin', 'Search & Reports', 'Search & Reports', 'read', 'search_and_reports', array( $this, 'adults_report' ) );

            // Remove 'Parent's Consent' sub menu item
            unset($submenu['cmw_plugin'][0]);

            add_action( 'admin_init', array($this, 'register_cmw_gdpr_policy_urls') );
        }



        public function register_cmw_gdpr_policy_urls()
        {
            register_setting( 'cmw-gdpr-policy-url-settings-group', 'cmw_terms_and_conditions' );
            register_setting( 'cmw-gdpr-policy-url-settings-group', 'cmw_booking_terms' );
            register_setting( 'cmw-gdpr-policy-url-settings-group', 'cmw_privacy_policy' );
        }


        /**
         * Init settings page
         */
        public function consent_settings()
        {

            CMW_Organisation::output();

        }

        /**
         * Init the search page (children list page)
         */
        public function adults_report()
        {

            CMW_Admin_Adults_Report::output();

        }


    }

endif;

return new CMW_Admin_Menu();
