<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Rarf_ShortCode{

    
    public function __construct(){
        add_shortcode('return_form', [$this, 'rarf_render_shortcode']);
    }
   
    public function rarf_render() {
     $nonce = wp_create_nonce('return_form_nonce'); 
            ?>
            <section class="refund-form-section">
            <div class="refund-form-container container">
               
                <form  method="post" id="refund-return-form">
                    <input type="text" id="order_number" name="order_number" placeholder=" <?php esc_html_e('Order Number:', 'return-and-refund-form'); ?>" require>

                    <input type="text" id="full_name" name="full_name" placeholder=" <?php esc_html_e('Name and Last Name:', 'return-and-refund-form'); ?>" require>

                    <input type="email" id="email" name="email" placeholder="<?php esc_html_e('E-mail:', 'return-and-refund-form'); ?>" require>

                    <input type="tel" id="phone" name="phone" placeholder="<?php esc_html_e('Phone:', 'return-and-refund-form'); ?>" require>

                    <textarea cols="5" id="products" name="products" placeholder= "<?php esc_html_e('Products that are returned:', 'return-and-refund-form'); ?>" require></textarea>

                    <textarea cols="5" id="return_reason" name="return_reason" placeholder="<?php esc_html_e('Return reason:', 'return-and-refund-form'); ?>"></textarea>

                    <div class="terms-and-conditions-form">
                        
                        
                        <?php
                        $refund_policy_page_id = get_option('return_form_refund_policy_page');

                        if (!empty($refund_policy_page_id)) {
                            $refund_policy_page_url = get_permalink($refund_policy_page_id); 
                            ?>
                             <label for="privacy_policy">
                                <input type="checkbox" id="privacy_policy" name="privacy_policy">
                                <?php esc_html_e("I have read and agree to the &nbsp;", 'return-and-refund-form'); ?> 
                                <a target="blank" href="<?php echo esc_url($refund_policy_page_url); ?>  "> 
                                    <?php esc_html_e('terms and conditions regarding returns*', 'return-and-refund-form'); ?></a></label>
                        <?php }
                            ;?>
                    </div>
                    <input type="hidden" name="return_form_nonce" value="<?php  echo esc_attr($nonce); ?>">
                    <input type="hidden" name="action" value="return_form_handler">
                    <input type="hidden" name="is_ajax" id="is_ajax" value="0">
                    <input id="return-submit-button" type="submit" value="<?php esc_html_e('Send', 'return-and-refund-form'); ?>">
                </form>
            </div>

            </section>
            <?php


    }

        // Define shortcode function
        public function rarf_render_shortcode() {
            // Start output buffer
            ob_start();

            $this->rarf_render();
             $output = ob_get_clean();

            // Return the output
            return $output;
        }

  


}



new Rarf_ShortCode();