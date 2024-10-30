<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CMW_Organisation' ) ) {

    /**
     * CM Admin Settings page.
     */
    class CMW_Organisation
    {

        static $limit = 20;
        static $classes_count = 0;

        /**
         * Settings page.
         *
         * Handles the display of the main consent manager settings page in admin.
         */
        public static function output() {

            include 'templates/settings.php';

        }


    }
}
