<?php
if (!class_exists('CMW_Custom_Endpoints')) {

    class CMW_Custom_Endpoints
    {

        /**
         * Custom endpoint name.
         *
         * @var string
         */
        public static $endpoint = 'my-company';

        protected $endpoints = ['adult-details', 'parent-details'];

        /**
         * Plugin actions.
         */
        public function __construct()
        {
            // Actions used to insert a new endpoint in the WordPress.
            add_action('init', array($this, 'add_endpoints'));
            add_filter('query_vars', array($this, 'add_query_vars'), 0);


            // Insering your new tab/page into the My Account page.
            add_filter('woocommerce_account_menu_items', array($this, 'my_custom_my_account_menu_items'));

            add_action( 'after_switch_theme', array($this, 'cmw_custom_flush_rewrite_rules') ); // TODO check if I can remove this line and function
            do_action( 'cmw_flush_rewrite_rules' );
        }

        /**
         * Register new endpoint to use inside My Account page.
         *
         * @see https://developer.wordpress.org/reference/functions/add_rewrite_endpoint/
         */
        public function add_endpoints()
        {
            foreach ($this->endpoints as $endpoint) {
                add_rewrite_endpoint($endpoint, EP_ROOT | EP_PAGES);
            }

        }

        /**
         * Add new query var.
         *
         * @param array $vars
         * @return array
         */
        public function add_query_vars($vars)
        {
            array_merge($vars, $this->endpoints);

            //$vars[] = self::$endpoint;

            return $vars;
        }

        /**
         * Set endpoint title.
         *
         * @param string $title
         * @return string
         */
        public function endpoint_title($title)
        {
            global $wp_query;

            $is_endpoint = isset($wp_query->query_vars[self::$endpoint]);

            if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
// New page title.
                $title = __('My Company', 'domain');

                remove_filter('the_title', array($this, 'endpoint_title'));
            }

            return $title;
        }


        private function my_custom_insert_after_helper($items, $new_items, $after)
        {
            // Search for the item position and +1 since is after the selected item key.
            $position = array_search($after, array_keys($items)) + 1;

            // Insert the new item.
            $array = array_slice($items, 0, $position, true);
            $array += $new_items;
            $array += array_slice($items, $position, count($items) - $position, true);

            return $array;
        }

        /**
         * Insert the new endpoint into the My Account menu.
         *
         * @param array $items
         * @return array
         */
        public function my_custom_my_account_menu_items($items)
        {

            $new_items = [];
            $new_items['adult-details'] = __('Adult Details', 'woocommerce');

            // Add the new item after `orders`.
            return $this->my_custom_insert_after_helper($items, $new_items, 'orders');
        }


        public function cmw_custom_flush_rewrite_rules()
        {
            flush_rewrite_rules();
        }

    }
}


add_action('init', '_action_cmw_user_family_details_init', 0);

if (!(function_exists('_action_cmw_user_family_details_init'))) {
    function _action_cmw_user_family_details_init()
    {
        new CMW_Custom_Endpoints();
    }
}
