<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CMW_Admin_Adults_Report' ) ) {

    /**
     * CMW_Admin_Children_list Class.
     */
    class CMW_Admin_Adults_Report
    {

        static $limit = 20;

        /**
         * Settings page.
         *
         * Handles the display of the main woocommerce settings page in admin.
         */
        public static function output() {

            if ($_GET['adult_id']) {

                $adult_info = self::get_adult_info(esc_attr(wp_unslash($_GET['adult_id'])));
                $adult_info->years = self::get_child_years($adult_info->dob);

                include 'templates/adult-info.php';
            } else {

                $children = self::get_adults_list(esc_attr(wp_unslash($_GET['paged'])));

                $pagination = self::get_pagination();

                include 'templates/search-and-report.php';
            }
        }


        private function get_adults_list($pageNum = 1)
        {


            global $wpdb;
            $result = [];
            $offset = self::$limit * ($pageNum - 1);

            $adults = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cmw_parent_info");

            if ($adults) {

                foreach ($adults as $adult) {
                    $result[] = [
                        'adult_id'      => $adult->user_id,
                        'years'         => cmw_get_child_years($adult->dob),
                        'name'          => get_user_meta($adult->user_id, 'first_name', true) . ' ' . get_user_meta($adult->user_id, 'last_name', true),
                        'medical_agree' => $adult->medical_agree,
                        'share_agree'   => $adult->share_agree,
                        'adult'         => 1
                    ];
                }

            }

            if (isset($_GET['s']) && !empty($_GET['s'])) {
                foreach ($result as $key => $res) {
                    if (strpos($res['name'], esc_attr(wp_unslash($_GET['s']))) !== false) {

                    } else {
                        unset($result[$key]);
                    }
                }
            }


            return array_slice($result, $offset, self::$limit);
        }

        private function get_child_years($dob)
        {
            $birthday_timestamp = strtotime($dob);
            $age = date('Y') - date('Y', $birthday_timestamp);
            if (date('md', $birthday_timestamp) > date('md')) {
                $age--;
            }
            return $age;
        }

        private function get_parent_count()
        {
            global $wpdb;

            $where = isset($_GET['s'])
                ? " WHERE name LIKE '%" . esc_sql($_GET['s']) . "%'"
                : '';

            return $wpdb->query("SELECT * FROM {$wpdb->prefix}cmw_parent_info" . $where);
        }


        private function get_pagination()
        {
            $parent_count = self::get_parent_count();

            return [
                'current_page' => $_GET['paged'] ? esc_attr(wp_unslash($_GET['paged'])) : 1,
                'count'        => $parent_count,
                'pages_count'  => ceil($parent_count / self::$limit)
            ];
        }


        private function get_adult_info($adult_id)
        {
            global $wpdb;

            return $wpdb->get_row("SELECT * FROM {$wpdb->prefix}cmw_parent_info WHERE user_id = {$adult_id}");
        }

    }
}
