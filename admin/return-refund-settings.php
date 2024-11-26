<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Rarf_Backend_Panels{

    public function __construct(){
         add_action('admin_menu', [$this, 'rarf_adm_settings']);
         add_action('admin_init', [$this, 'rarf_register_settings']);


    }

   public function rarf_adm_settings() {
        add_submenu_page('return-form', 'Settings', 'Settings', 'manage_options', 'return-form-settings', [$this, 'rarf_settings_page']);
    }
   public function rarf_register_settings() {
        register_setting('return_form_settings', 'return_form_refund_policy_page');
    }

    // plugin setting page 
    public function rarf_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Settings', 'return-and-refund-form'); ?></h1>
    
            <form action="options.php" method="post">
                <?php settings_fields('return_form_settings'); ?>
    
                <h2><?php esc_html_e('Select the Terms of Return page', 'return-and-refund-form'); ?></h2>  
                <?php
                // Descriptive text about selecting the refund return page
                esc_html_e('Select the Return Refund Policy page so it can be implemented the check mark for accepting the terms at Return Refund Policy', 'return-and-refund-form');
              
                // Fetch the selected page from options
                $selected_page = get_option('return_form_refund_policy_page');
    
                // Dropdown for selecting the page
                echo '</br><select name="return_form_refund_policy_page">';
                echo '<option value="">' . esc_html__('Select a page', 'return-and-refund-form') . '</option>';
    
                // Fetch and list all WordPress pages
                $pages = get_pages();
                foreach ($pages as $page) {
                    $selected_attr = selected($selected_page, $page->ID, false);
                    echo '<option value="' . esc_attr($page->ID) . '"' . esc_attr($selected_attr) . '>' . esc_html($page->post_title) . '</option>';
                }
                echo '</select>';
    
                // Instructions for using the return form shortcode
                echo '<p>' . sprintf(esc_html__('The form ca be used with shortcode, copy and place the shortcode where you need it', 'return-and-refund-form'), '<strong style="color:red">[return_form]</strong>') . '</p>';
                ?>
    
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
    


    public function rarf_settings_section_cb() {
        echo '<p>Settings for the Return Form.</p>';
    }

    public function rarf_field_cb() {
        // Render settings field
        $option = get_option('return_form_option');
        echo '<input type="text" name="return_form_option" value="' . esc_attr($option) . '"/>';
    }
    


}

new Rarf_Backend_Panels();







