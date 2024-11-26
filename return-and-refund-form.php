<?php
/*
Plugin Name: Return and Refund Form
Description: A plugin to manage return requests.
Text Domain: return-and-refund-form 
Version: 1.0.0
Author: LERATECH
Author URI: https://leratech.ro/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}


if(is_admin()){
    require_once plugin_dir_path(__FILE__)  . 'admin/return-refund-tabel.php'; 
    require_once plugin_dir_path(__FILE__)  . 'admin/return-refund-settings.php';
}

require_once plugin_dir_path(__FILE__)  . 'admin/return-form-handler.php'; 
require_once plugin_dir_path(__FILE__) . 'includes/assets.php';
require_once plugin_dir_path(__FILE__) . 'public/short-code.php';


register_activation_hook(__FILE__, 'rarf_table');

function rarf_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'return_form';

    // Set a unique cache key
    $cache_key = 'rarf_table_exists';


    // Attempt to get the cached value
    $table_exists = wp_cache_get($cache_key);

    // Check if the cache returned a valid value
    if ($table_exists === false) {
        // Cache miss - check the database
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
        $table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table_name)) === $table_name;

        // Set the cache with the result of the database query
        wp_cache_set($cache_key, $table_exists, '', 3600); // Cache for 1 hour
    }

    // If the table doesn't exist, create it
    if (!$table_exists) {
        // SQL to create your table
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            order_number varchar(255) NOT NULL,
            full_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            products text NOT NULL,
            return_reason text NOT NULL,
            privacy_policy tinyint(1) DEFAULT 0 NOT NULL,
            submission_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Update the cache after creating the table
        wp_cache_set($cache_key, true, '', 3600); 
    }
}
