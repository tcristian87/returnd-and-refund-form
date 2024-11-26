<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Rarf_Assets {

    public function __construct(){
        add_action('wp_enqueue_scripts', [$this, 'rarf_enqueue_scripts']);
    }

    // Scripts and styles register
    public function rarf_enqueue_scripts() {
        // Enqueue jQuery
        wp_enqueue_script('jquery');

        // Enqueue the custom script
        wp_enqueue_script('custom-return-refund-script', plugin_dir_url(__DIR__) . 'assets/js/custom.js', array('jquery'), '1.0.0', true);

        // Enqueue the custom stylesheet
        wp_enqueue_style('return-refund-style', plugin_dir_url(__DIR__) . 'assets/css/style.css', array(), '1.0.0', 'all');

        // Localize the script with data
        wp_localize_script('custom-return-refund-script', 'rarf_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}

new Rarf_Assets();
