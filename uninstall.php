<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb, $wp_version;


// Delete user privacy and consent meta
$users = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}cmw_parent_info");

if ($users) {

    foreach ($users as $user) {
        delete_user_meta($user->user_id, 'emergency_contact_name');
        delete_user_meta($user->user_id, 'emergency_relationship');
        delete_user_meta($user->user_id, 'emergency_phone');
        delete_user_meta($user->user_id, 'emergency_mobile');
        delete_user_meta($user->user_id, 'emergency_medical_notes');
    }

}


// Tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_parent_info" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_children_info" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_child_to_order" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}cmw_adult_to_order" );


// Deleting options
// TODO getting options array from the Install class
$options = [
    'cmw_terms_and_conditions',
    'cmw_booking_terms',
    'cmw_privacy_policy',
    'consent_manager_version'
];

foreach ($options as $option) {
    delete_option($option);
}

// Delete adults photos
$dirAdult = WP_CONTENT_DIR . '/uploads/adult_photo';
if (is_dir($dirAdult)) {
    $it = new RecursiveDirectoryIterator($dirAdult, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it,
        RecursiveIteratorIterator::CHILD_FIRST);
    foreach($files as $file) {
        if ($file->isDir()){
            rmdir($file->getRealPath());
        } else {
            unlink($file->getRealPath());
        }
    }
    rmdir($dirAdult);
}

//Delete all exports
array_map('unlink', glob(WP_CONTENT_DIR . '/uploads/personal_data*.csv')); // Personal data
array_map('unlink', glob(WP_CONTENT_DIR . '/uploads/children_list*.csv')); // children list export from admin panel


// Clear any cached data that has been removed
wp_cache_flush();
