<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'return_form';

// Properly sanitize the table name (if needed)
$sanitized_table_name = esc_sql($table_name);

// Drop the table if it exists
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
$wpdb->query("DROP TABLE IF EXISTS {$sanitized_table_name}");

// Delete the plugin option
delete_option('return_form_refund_policy_page');