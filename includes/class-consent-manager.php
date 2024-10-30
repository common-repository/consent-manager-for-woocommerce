<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

final class Consent_Manager {

    public $version;

    /**
     * The single instance of the class.
     *
     *
     */
    protected static $_instance = null;

    /**
     * Main Consent Manager Instance.
     *
     * Ensures only one instance of Consent Manager is loaded or can be loaded.
     *
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Consent Manager Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action( 'user_details_loaded' );
    }

    /**
     * Hook into actions and filters.
     * @since  2.3
     */
    private function init_hooks() {

        register_activation_hook( CMW_PLUGIN_FILE, array( 'CMW_Install', 'install' ) );

        add_action('wp_enqueue_scripts', array($this, 'cmw_scripts'));

        add_action('wp_ajax_getBookingAddress', array($this, 'get_booking_address'));
        add_action('wp_ajax_nopriv_getBookingAddress', array($this, 'get_booking_address'));

        add_action('wp_ajax_getAdultsReport', array($this, 'getAdultsReport'));
        add_action('wp_ajax_nopriv_getAdultsReport', array($this, 'getAdultsReport'));

        add_action('wp_ajax_saveConsent', array($this, 'saveConsent'));
        add_action('wp_ajax_nopriv_saveConsent', array($this, 'saveConsent'));

        add_action('wp_ajax_getUserData', array($this, 'getUserData'));
        add_action('wp_ajax_nopriv_getUserData', array($this, 'getUserData'));

        add_action('wp_ajax_deleteUserData', array($this, 'deleteUserData'));
        add_action('wp_ajax_nopriv_deleteUserData', array($this, 'deleteUserData'));

        add_action('wp_ajax_getChildrenFieldsForCheckout', array($this, 'getChildrenFieldsForCheckout'));
        add_action('wp_ajax_nopriv_getChildrenFieldsForCheckout', array($this, 'getChildrenFieldsForCheckout'));


        add_action( 'wp', array($this, 'change_post_per_page_wpent'), 9 );

        // Adding link to Settings page
        add_filter( 'plugin_action_links_' . CMW_PLUGIN_BASENAME, array($this, 'cmw_action_links') );

        add_action( 'cmw_flush_rewrite_rules', array( $this, 'cmw_flush_rewrite_rules' ) );
    }

    public function cmw_action_links( $links ) {

        if (!$GLOBALS['consent_manager_pro']) {

            $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=consent_settings') ) .'">Settings</a>';

        }

        return $links;

    }

    public function change_post_per_page_wpent( $query ) {

        if (isset($_POST['route_name'])) {

            cmw_update_data($_POST);

//            exit;
        }

        return $query;
    }


    /* Proper way to enqueue scripts and styles */
    public function cmw_scripts()
    {

        wp_register_style( 'bootstrap_styles', plugin_dir_url(__FILE__) . '../assets/css/bootstrap.min.css');
        wp_enqueue_style( 'bootstrap_styles' );


        // Register and Enqueue a Stylesheet
        // get_template_directory_uri will look up parent theme location
        wp_register_style( 'cmw_style', plugin_dir_url(__FILE__) . '../assets/css/style_default.css');
        wp_enqueue_style( 'cmw_style' );

        wp_register_style( 'jquery_ui_style',  plugin_dir_url(__FILE__) . '../assets/css/jquery-ui.min.css');
        wp_enqueue_style( 'jquery_ui_style' );


        $this->check_theme();

        wp_register_style( 'jquery_modal', plugin_dir_url(__FILE__) . '../assets/css/jquery.modal.min.css');
        wp_enqueue_style( 'jquery_modal' );

        // Register and Enqueue a Script
        // get_stylesheet_directory_uri will look up child theme location
        wp_register_script( 'maskedinput', plugin_dir_url(__FILE__) . '../assets/js/jquery.maskedinput121.js', array('jquery'),null,true);
        wp_enqueue_script( 'maskedinput' );

        wp_register_script( 'jquery_modal', plugin_dir_url(__FILE__) . '../assets/js/jquery.modal.min.js', array('jquery'),null,true);
        wp_enqueue_script( 'jquery_modal' );

        wp_register_script( 'cmw-script', plugin_dir_url(__FILE__) . '../assets/js/main.js', array('jquery','jquery-ui-autocomplete', 'maskedinput'),null,true);
        $translation_array = array(
            'siteurl' => get_option('siteurl'),
            'content_dir' => WP_CONTENT_DIR,
            'content_url' => WP_CONTENT_URL,
            'admin_url'   => admin_url()
        );

        wp_localize_script('cmw-script', 'CMW_variables', $translation_array);
        wp_enqueue_script( 'cmw-script' );


    }

    public function check_theme()
    {
        $theme = wp_get_theme();

        switch ($theme->Name) {
            case 'Avada' :
                wp_register_style( 'cmw_style_avada', plugin_dir_url(__FILE__) . '../assets/css/style_avada.css');
                wp_enqueue_style( 'cmw_style_avada' );
                break;
            case 'X' :
                wp_register_style( 'cmw_style_x', plugin_dir_url(__FILE__) . '../assets/css/style_x_theme.css');
                wp_enqueue_style( 'cmw_style_x' );
                break;
            case 'Twenty Seventeen':
                wp_register_style( 'cmw_style_twenty_seventeen', plugin_dir_url(__FILE__) . '../assets/css/style_twenty_seventeen.css');
                wp_enqueue_style( 'cmw_style_twenty_seventeen' );
                break;
            case 'Betheme':
                wp_register_style( 'cmw_style_betheme', plugin_dir_url(__FILE__) . '../assets/css/style_betheme.css');
                wp_enqueue_style( 'cmw_style_betheme' );
                break;
            case "Oshin":
                wp_register_style( 'cmw_style_oshin', plugin_dir_url(__FILE__) . '../assets/css/style_oshin.css');
                wp_enqueue_style( 'cmw_style_oshin' );
                break;

        }
    }



    public function get_booking_address()
    {
        $parent = new CMW_ParentInfo();
        $result = $parent->get_billing_address();

        echo json_encode($result);
        die;
    }


    public function getAdultsReport()
    {
        global $wpdb;

        $adults = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cmw_parent_info");

        if (!empty($adults)) {

            $fp = fopen(WP_CONTENT_DIR . '/uploads/search_and_reports.csv', 'w');

            fputcsv($fp, [
                'Name',
                'Years',
                'Emergency Info',
                'Has medical consent',
                'Has sharing consent'
            ]);

            foreach ($adults as $adult) {


                fputcsv($fp, [
                    get_user_meta($adult->user_id, 'first_name', true) . ' ' . get_user_meta($adult->user_id, 'last_name', true),
                    cmw_get_child_years($adult->DOB) . ' years',
                    get_user_meta($adult->user_id, 'emergency_contact_name', true) . ' ' . get_user_meta($adult->user_id, 'emergency_mobile', true),
                    $adult->medical_agree ? 'Yes' : 'No',
                    $adult->share_agree ? 'Yes' : 'No'
                ]);

            }

            fclose($fp);
        }

        die;

    }


    /**
     * Define CMW Constants.
     */
    private function define_constants() {
        $upload_dir = wp_upload_dir(null, false);

        $this->define( 'CMW_ABSPATH', dirname( CMW_PLUGIN_FILE ) . '/' );
        $this->define( 'CMW_PLUGIN_FILE', __FILE__ );
        $this->define( 'CMW_PLUGIN_BASENAME', plugin_basename( CMW_PLUGIN_FILE ) );
        $this->define( 'CMW_PLUGIN_DIR', plugin_dir_path(__FILE__));
        $this->define( 'CMW_VERSION', $this->version );
        $this->define( 'CMW_LOG_DIR', $upload_dir['basedir'] . '/logs/' );
    }

    /**
     * Define constant if not already set.
     *
     * @param  string $name
     * @param  string|bool $value
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {

        // models
        include_once(CMW_ABSPATH . 'includes/models/children.php');
        include_once(CMW_ABSPATH . 'includes/models/child_order.php');
        include_once(CMW_ABSPATH . 'includes/models/parent.php');

        include_once(CMW_ABSPATH . 'includes/cmw-child-functions.php');
        include_once(CMW_ABSPATH . 'includes/cmw-parent-functions.php');
        include_once(CMW_ABSPATH . 'includes/cmw-adult-functions.php');

        // functions
        include_once(CMW_ABSPATH . 'includes/class-cmw-install.php');
        include_once(CMW_ABSPATH . 'includes/class-cmw-custom-endpoint.php');
        include_once(CMW_ABSPATH . 'includes/cmw-template-functions.php');
        include_once(CMW_ABSPATH . 'includes/cmw-template-hooks.php');
        include_once(CMW_ABSPATH . 'includes/class-cmw-validate.php');
        include_once(CMW_ABSPATH . 'includes/cmw-email-functions.php');

        include_once(CMW_ABSPATH . 'includes/admin/class-cmw-admin.php');

        include_once(CMW_ABSPATH . 'functions.php');
    }


    public function saveConsent()
    {

        if (isset($_POST['terms'])) {
            update_user_meta(get_current_user_id(), 'gdpr_terms', sanitize_text_field($_POST['terms']));
        }

        if (isset($_POST['policy'])) {
            update_user_meta(get_current_user_id(), 'gdpr_policy', sanitize_text_field($_POST['policy']));
        }

        if (isset($_POST['booking_terms'])) {
            update_user_meta(get_current_user_id(), 'booking_terms', sanitize_text_field($_POST['booking_terms']));
        }

        if (isset($_POST['subscr'])) {
            // subscribe user to newsletter
            update_user_meta(get_current_user_id(), 'subscribe_to_news', sanitize_text_field($_POST['subscr']));
        }

        die;


    }

    public function getUserData()
    {

        $current_user = wp_get_current_user();

        $fp = fopen(WP_CONTENT_DIR . '/uploads/personal_data_' . $current_user->user_login . '.csv', 'w');

        fputcsv($fp, [
            'Name, Surname',
            'Email',
            'Physical Address',
            'Home phone',
            'Mobile Phone',
            'Children Names',
            'Relationship',
            'Medical emergency details',
            'Website usage terms and conditions',
            'Bookings and Purchase terms and conditions',
            'Privacy policy',
            'Mailing list, offers and services'
        ]);

        $user_info = cmw_get_parent_info($current_user->ID);

        $children = cmw_get_children([
            'parent_id' => $current_user->ID
        ]);

        $childrenNames = [];
        $relations = [];
        $doctors = [];

        foreach ($children as $child) {

            $emergency = cmw_get_child_emergency_info($child->id);

            if (!empty($emergency)) {
                $relations[] = $emergency->relationship;
                $doctor_phone = $emergency->doctor_phone ? '(' . $emergency->doctor_phone . ')' : '';
                $doctor_address = $emergency->doctor_address ? $emergency->doctor_address . ' ' . $emergency->doctor_postcode : '';
                $doctors[] = $emergency->doctor_name . ' ' . $doctor_phone . ' ' . $doctor_address;
            }

            $childrenNames[] = $child->name;
        }

        fputcsv($fp, [
            $current_user->user_firstname . ' ' . $current_user->user_lastname,
            $current_user->user_email,
            $user_info->address ? $user_info->address . ' ' . $user_info->postcode : '',
            $user_info->phone,
            $user_info->mobile,
            implode(', ', $childrenNames),
            implode('; ', $relations),
            implode('; ', $doctors),
            get_user_meta(get_current_user_id(), 'gdpr_terms', true) ? 'Yes' : 'No',
            get_user_meta(get_current_user_id(), 'booking_terms', true) ? 'Yes' : 'No',
            get_user_meta(get_current_user_id(), 'gdpr_policy', true) ? 'Yes' : 'No',
            get_user_meta(get_current_user_id(), 'subscribe_to_news', true) ? 'Yes' : 'No'
        ]);

        fclose($fp);

        echo json_encode(array('user_login' => $current_user->user_login));
        die;
    }

    public function deleteUserData()
    {

        $userId = get_current_user_id();

        $children = cmw_get_children([
            'parent_id' => $userId
        ]);

        if (!empty($children)) { // deleting all users children

            foreach ($children as $child) {

                $childrenObj = new CMW_Children();

                $childrenObj->deleteChild($child->id); // delete child details

            }

        }

        $parent = new CMW_ParentInfo();

        $parent->deleteParentDetails($userId); // deleting details created by CM

        wp_delete_user( $userId );

        die;
    }


    public function getChildrenFieldsForCheckout()
    {
        $result = [
            'child_rows' => []
        ];

        if (is_user_logged_in()) {

            $children = cmw_get_children([
                'parent_id'     => get_current_user_id(),
                'has_emergency' => true
            ]);

            if (!empty($children)) {


                foreach ($children as $child) {
                    $result['child_rows'][] = [
                        'child_id' => $child->id,
                        'name' => $child->name
                    ];
                }


            }

        }

        echo json_encode($result);
        die;
    }


    public function cmw_flush_rewrite_rules()
    {
        flush_rewrite_rules();
    }

}
