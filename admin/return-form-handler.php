<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

class Rarf_Handler
{


    public function __construct()
    {
        add_action('wp_ajax_nopriv_return_form_handler', [$this, 'rarf_form_handler']);
        add_action('wp_ajax_return_form_handler', [$this, 'rarf_form_handler']);
    }

    function rarf_form_handler()
    {
          //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && sanitize_text_field(wp_unslash($_POST['action'])) === 'return_form_handler') {


            if (!isset($_POST['return_form_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash(($_POST['return_form_nonce']))), 'return_form_nonce')) {
                wp_send_json_error('Security check failed.');
                return;
            }

            /**  $formData is just storing the array of what $_POST['formData'] have */
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $formData = isset($_POST['formData']) && is_array($_POST['formData']) ? wp_unslash($_POST['formData']) : array();
            $data = array();
            
            foreach ($formData as $field) {
                $data[$field['name']] = $field['value'];
            }
            
 
            $order_number = isset($data['order_number']) ? sanitize_text_field($data['order_number']) : '';
            $full_name = isset($data['full_name']) ? sanitize_text_field($data['full_name']) : '';
            $email = isset($data['email']) ? sanitize_email($data['email']) : '';
            $phone = isset($data['phone']) ? sanitize_text_field($data['phone']) : '';
            $products = isset($data['products']) ? sanitize_textarea_field($data['products']) : '';
            $return_reason = isset($data['return_reason']) ? sanitize_textarea_field($data['return_reason']) : '';
            $privacy_policy = isset($data['privacy_policy']) ? 1 : 0;

            global $wpdb;
            $table_name = $wpdb->prefix . 'return_form';
            /** the sanitize and the checking of the data is made before on each input */
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery
            $result = $wpdb->insert($table_name, array(
                'order_number' => $order_number,
                'full_name' => $full_name,
                'email' => $email,
                'phone' => $phone,
                'products' => $products,
                'return_reason' => $return_reason,
                'privacy_policy' => $privacy_policy
            ));

            if ($result === false) {
                wp_send_json_error('Database insert failed.');
            }

            // Prepare and send admin email
            /* translators: %s: Name of the client in the email subject */
            $subject = sprintf(esc_html__('New return request from %s', 'return-and-refund-form'), esc_html($full_name));
            $message = esc_html__("A new return request has been submitted.\n\n", 'return-and-refund-form');
            /* translators: %s: Order number */
            $message .= sprintf(esc_html__("Order number: %s\n", 'return-and-refund-form'),  esc_html($order_number));
            /* translators: %s: Full name of the client */
            $message .= sprintf(esc_html__("Full name: %s\n", 'return-and-refund-form'),  esc_html($full_name));
            /* translators: %s: Client email address */
            $message .= sprintf(esc_html__("Email: %s\n", 'return-and-refund-form'),  esc_html($email));
            /* translators: %s: Client Phone number */
            $message .= sprintf(esc_html__("Phone: %s\n", 'return-and-refund-form'),  esc_html($phone));
            /* translators: %s: Products */
            $message .= sprintf(esc_html__("Products: %s\n", 'return-and-refund-form'),  esc_html($products));
            /* translators: %s: Return Reason */
            $message .= sprintf(esc_html__("Reason for return: %s\n", 'return-and-refund-form'),  esc_html($return_reason));
            $headers = array('Content-Type: text/plain; charset=UTF-8');

            wp_mail(get_option('admin_email'), $subject, $message, $headers);


            // Prepare and send user email
            /* translators: %s: Return Request from translate */
            $user_subject = sprintf(esc_html__("Your return request to %s", 'return-and-refund-form'), get_bloginfo('name'));

            $user_message = '
                <html>
                <head>
                <style>
                    body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
                    .email-container { background-color: #f7f7f7; padding: 20px; }
                    .email-content { background-color: #ffffff; padding: 20px; border: 1px solid #ddd; }
                    .email-footer { margin-top: 20px; font-size: 12px; color: #777; text-align: center;}
                    .email-header { margin: 20px; font-size: 20px; text-align: center; color: black; }
                </style>
                </head>
                <body>
                    <div class="email-container">
                        <div class="email-header">
                            ' . esc_html($user_subject) . '
                        </div>
                        <div class="email-content">
                            <p>' . sprintf(
                            /* translators: %s: Client Full Name */
                            esc_html__('Dear %s,', 'return-and-refund-form'),
                            esc_html($full_name)
                        ) . '</p>
                            <p>' . esc_html__('We have received your return request. Our team will contact you soon.', 'return-and-refund-form') . '</p>
                            <p>' . sprintf(
                            /* translators: %s: Greattings email part */
                            esc_html__('Sincerely, %s', 'return-and-refund-form'),
                            esc_html(get_bloginfo('name'))
                        ) . '</p>
                            </div>
                            <div class="email-footer">					
                            <p>' . sprintf(
                            /* translators: %s: Year and rights reserved */
                            esc_html__('© %1$s %2$s. All rights reserved.', 'return-and-refund-form'),
                            gmdate("Y"),
                            esc_html(get_bloginfo('name'))
                        ) . '</p>
                        </div>
                    </div>
                </body>
                </html>
            ';

            $headers = array('Content-Type: text/html; charset=UTF-8');

            wp_mail($email, $user_subject, $user_message, $headers);

            // Return success response
            wp_send_json_success('Form submitted successfully.');
        } else {
            wp_send_json_error('Invalid request.');
        }
    }
}

new Rarf_Handler();
