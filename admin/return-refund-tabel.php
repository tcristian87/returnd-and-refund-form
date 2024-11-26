<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

include_once(ABSPATH . 'wp-admin/includes/plugin.php');


class Rarf_Settings_Panel {

    public function __construct()
    {
    add_action('admin_menu', [$this, 'rarf_admin_menu']);
    add_action('admin_notices', array('Rarf_Ceck_Woo', 'rarf_display_status'));
        
    }

    public function rarf_admin_menu() {
        // Main menu page
        add_menu_page('Returns/Refunds', 'Returns/Refunds', 'manage_options', 'return-form', [$this, 'rarf_admin_page'], 'dashicons-admin-page');
    
    
    }
    

    public function rarf_admin_page() {
         // Check nonce for security
         if (isset($_POST['return_form_nonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_POST['return_form_nonce']));
            if (!wp_verify_nonce($nonce, 'return_form_action')) {
                wp_die('Security check failed.');
            }
        }
            $status = Rarf_Ceck_Woo::rarf_check_status();

            if ($status !== true) {
                    echo esc_html($status[1]);
            }else{
                global $wpdb;
                $table_name = $wpdb->prefix . 'return_form';
                $rows_per_page = 20;
                $current_page = isset($_GET['paged']) ? max((int) sanitize_text_field(wp_unslash($_GET['paged'])), 1) : 1;
                $offset = ($current_page - 1) * $rows_per_page;
                // Ensure the table name is safe
                $sanitized_table_name = esc_sql($table_name);

                // Try to get the total number of rows from cache
                $cache_key_total = 'total_rows_' . $sanitized_table_name;
                $total = wp_cache_get($cache_key_total);
            
                if ($total === false) {
                    // Cache miss, query the database
                    $total_query = "SELECT COUNT(1) FROM {$sanitized_table_name}";
                    //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared
                    $total = $wpdb->get_var($total_query);

                    wp_cache_set($cache_key_total, $total, '', 3600); 
                }
                $total_pages = ceil($total / $rows_per_page);

                // Try to get the entries from cache
                $cache_key_entries = 'entries_' . $sanitized_table_name . '_page_' . $current_page;
                $entries = wp_cache_get($cache_key_entries);
                if ($entries === false) {
                    // Cache miss, query the database
                    /** the sanitize table name is made before the prepare and here is passed the dynamic value of the table name as placeholder */
                    //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
                    $entries = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$sanitized_table_name} ORDER BY id DESC LIMIT %d, %d",
                        $offset, $rows_per_page
                    ));
                    wp_cache_set($cache_key_entries, $entries, '', 3600); 
                }

                echo '<div class="wrap"><h1>' . esc_html__('Requests', 'return-and-refund-form') . '</h1>';
                        if ($entries) {
                            echo '<table class="wp-list-table widefat fixed striped">';
                            // Translatable table headers
                            echo '<thead><tr><th>' . esc_html__('ID', 'return-and-refund-form') . '</th><th>' . esc_html__('Order Number', 'return-and-refund-form') . '</th><th>' . esc_html__('Status', 'return-and-refund-form') . '</th><th>' . esc_html__('Full Name', 'return-and-refund-form') . '</th><th>' . esc_html__('E-mail', 'return-and-refund-form') . '</th><th>' . esc_html__('Phone', 'return-and-refund-form') . '</th><th>' . esc_html__('Products', 'return-and-refund-form') . '</th><th>' . esc_html__('Return Reason', 'return-and-refund-form') . '</th><th>' . esc_html__('Date', 'return-and-refund-form') . '</th></tr></thead>';
                            echo '<tbody>';

                            foreach ($entries as $entry) {
                                $order_number_link = $entry->order_number ? sprintf('<a href="%s">%s</a>', esc_url(admin_url('post.php?post=' . $entry->order_number . '&action=edit')), esc_html($entry->order_number)) : esc_html__('N/A', 'return-and-refund-form');
                                // Check order status
                                $order = wc_get_order($entry->order_number);
                                $order_solution = '';

                                if ($order) {
                                    $order_status = $order->get_status();
                                    if ($order_status == 'refunded') {
                                        $order_solution = esc_html__('Completed', 'return-and-refund-form');
                                    } else {
                                        $order_solution = esc_html__('Pending Solution', 'return-and-refund-form');
                                    }
                                } else {
                                    $order_solution = esc_html__('Order not found', 'return-and-refund-form');
                                }
                                /** if we escaping the $oreder_numnber_link this link wil not work,
                                 * and the sanitize is made before when the value of the variable is set
                                 */

                                echo "<tr>
                                        <td>" . esc_html($entry->id) . "</td>" .
                                        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        "<td>" . $order_number_link . "</td>
                                        <td>" . esc_html($order_solution) . "</td>
                                        <td>" . esc_html($entry->full_name) . "</td>
                                        <td>" . esc_html($entry->email) . "</td>
                                        <td>" . esc_html($entry->phone) . "</td>
                                        <td>" . esc_html($entry->products) . "</td>
                                        <td>" . esc_html($entry->return_reason) . "</td>
                                        <td>" . esc_html($entry->submission_time) . "</td>
                                    </tr>";
                            }

                            echo '</tbody></table>';

                            // Pagination links
                            $page_links = paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '?paged=%#%',
                                'prev_text' => esc_html__('&laquo;', 'return-and-refund-form'),
                                'next_text' => esc_html__('&raquo;', 'return-and-refund-form'),
                                'total' => $total_pages,
                                'current' => $current_page
                            ));
                            /** the escaping is made before when the value of the variable is set  */
                            //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo '<div class="pagination">' . $page_links . '</div>';
                        } else {
                            echo '<p>' . esc_html__('No entries found.', 'return-and-refund-form') . '</p>';
                        }

                echo '</div>';
                            
                    }
                }


            }

new Rarf_Settings_Panel();

class Rarf_Ceck_Woo extends Rarf_Settings_Panel{

    public static function rarf_check_status() {
        // Define the plugin path
        $woocommerce_plugin_path = 'woocommerce/woocommerce.php';

        // Check if WooCommerce is installed
        $is_installed = file_exists(WP_PLUGIN_DIR . '/' . $woocommerce_plugin_path);

        // Check if WooCommerce is active
        $is_active = is_plugin_active($woocommerce_plugin_path);

        if ($is_installed && !$is_active) {
            // The WooCommerce is installed but not active, prompting for activation with a translatable string.
            return array(
                $is_installed, 
                '<div class="notice notice-warning is-dismissible"><p>' . 
                esc_html__('You must activate WooCommerce.', 'return-and-refund-form') . 
                '</p></div>'
            );
        } elseif (!$is_installed) {
            // WooCommerce is not installed, prompting for installation with a translatable string.
            return array(
                $is_installed, 
                '<div class="notice notice-error is-dismissible"><p>' . 
                esc_html__('You must install WooCommerce.', 'return-and-refund-form') . 
                '</p></div>'
            );
        } else {
            return true;
        }
    }

    public static function rarf_display_status() {
        $status = self::rarf_check_status();
        if (is_array($status)) {
            return $status[1];
        }
    }


}



