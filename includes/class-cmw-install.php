<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CMW_Install Class.
 */
class CMW_Install {

    public static $CMW_db_version = '1.0';

    /**
     * Install WC.
     */
    public static function install() {

        global $wpdb;

        if ( ! defined( 'CMW_INSTALLING' ) ) {
            define( 'CMW_INSTALLING', true );
        }

        // Ensure needed classes are loaded
        //include_once( 'admin/class-wc-admin-notices.php' );

        self::create_tables();

        self::cmw_flush_rewrite_rules();

        self::create_tags();

        self::update_cmw_version();


        do_action( 'cmw_flush_rewrite_rules' );

        /*
         * Deletes all expired transients. The multi-table delete syntax is used
         * to delete the transient record from table a, and the corresponding
         * transient_timeout record from table b.
         *
         * Based on code inside core's upgrade_network() function.
         */
        $sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d";
        $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );

        add_option( 'CMW_db_version', self::$CMW_db_version );


    }

    /**
     * Hook in tabs.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
    }

    /**
     * Update WC version to current.
     */
    private static function update_cmw_version() {
        delete_option( 'consent_manager_version' );
        add_option( 'consent_manager_version', CMW()->version );
    }

    /**
     * Check WooCommerce version and run the updater is required.
     *
     * This check is done on all requests and runs if the versions do not match.
     */
    public static function check_version() {
        if ( get_option( 'consent_manager_version' ) !== CMW()->version ) {
            self::install();
        }
    }

    /**
     * Creating new terms (product tags) for the Checkout page functionality
     */
    private static function create_tags() {

        $terms = [
            'Adults' => [
                'taxonomy' => 'product_tag',
                'slug' => 'adults'
            ],
            'Children' => [
                'taxonomy' => 'product_tag',
                'slug' => 'children'
            ]

        ];

        foreach ($terms as $key => $term) {
            wp_insert_term( $key, $term['taxonomy'], $args = array('slug' => $term['slug']) );
        }

    }

    /**
     * Set up the database tables which the plugin needs to function.
     *
     * Tables:
     *		woocommerce_attribute_taxonomies - Table for storing attribute taxonomies - these are user defined
     *		woocommerce_termmeta - Term meta table - sadly WordPress does not have termmeta so we need our own
     *		woocommerce_downloadable_product_permissions - Table for storing user and guest download permissions.
     *			KEY(order_id, product_id, download_id) used for organizing downloads on the My Account page
     *		woocommerce_order_items - Order line items are stored in a table to make them easily queryable for reports
     *		woocommerce_order_itemmeta - Order line item meta is stored in a table for storing extra data.
     *		woocommerce_tax_rates - Tax Rates are stored inside 2 tables making tax queries simple and efficient.
     *		woocommerce_tax_rate_locations - Each rate can be applied to more than one postcode/city hence the second table.
     */
    private static function create_tables() {
        global $wpdb;

        $wpdb->hide_errors();

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        dbDelta(self::get_schema());
    }


    private static function get_schema() {
        global $wpdb;

        $collate = '';

        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        }

        $installed_ver = get_option( "CMW_db_version" );

        /*
         * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
         * As of WordPress 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
         * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
         *
         * This may cause duplicate index notices in logs due to https://core.trac.wordpress.org/ticket/34870 but dropping
         * indexes first causes too much load on some servers/larger DB.
         */

        if ( $installed_ver != self::$CMW_db_version ) {} // for updates

        $tables = "
CREATE TABLE {$wpdb->prefix}cmw_parent_info (
  user_id bigint(20) NOT NULL,
  dob DATE NOT NULL,
  gender CHAR(1) NOT NULL,
  address longtext NOT NULL,
  postcode VARCHAR(32) NOT NULL,
  phone varchar(64) NULL,
  mobile varchar(64) NOT NULL,
  subscription char(1) NOT NULL DEFAULT 0,
  medical_agree char(1) NOT NULL DEFAULT 0,
  share_agree char(1) NOT NULL DEFAULT 0
) $collate;
CREATE TABLE {$wpdb->prefix}cmw_children_info (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) unsigned NOT NULL DEFAULT '0',
  name varchar(255) NOT NULL,
  gender CHAR(1) NOT NULL,
  DOB DATE NOT NULL,
  school VARCHAR(128) NULL,
  address longtext NULL,
  postcode VARCHAR(32) NULL,
  medical_agree char(1) NOT NULL DEFAULT 0,
  share_agree char(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (id),
  KEY `user_id` (`user_id`)
) $collate;
CREATE TABLE {$wpdb->prefix}cmw_child_to_order (
  child_id bigint(20) NOT NULL,
  order_id bigint(20) NOT NULL
) $collate;
CREATE TABLE {$wpdb->prefix}cmw_adult_to_order (
  id bigint(20) NOT NULL auto_increment,
  user_id bigint(20) unsigned NOT NULL,
  first_name varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  email varchar(128) NOT NULL,
  order_id bigint(20) NOT NULL,
  PRIMARY KEY  (id)
) $collate;
		";
        return $tables;
    }

    /**
     * Flush rewrite rules on plugin activation.
     */
    private static function cmw_flush_rewrite_rules() {
        flush_rewrite_rules(true);
    }

    private static function drop_tables()
    {
        global $wpdb;

        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_parent_info" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_children_info" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_child_to_order" );
        $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_adult_to_order" );
    }


}

CMW_Install::init();
