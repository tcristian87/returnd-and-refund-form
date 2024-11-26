=== Return Refund Form ===

Contributors: LeraTech Solutions 
Tags: Return Refund  Form, 
Requires at least: 5.1.0
Tested up to: 6.6
Requires PHP: 7.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
The Return Form plugin for WordPress enables customers to request returns easily with a customizable form.

== Description ==

The Return Form Handler Plugin for WordPress provides a comprehensive solution for managing product return requests. This plugin allows site administrators to easily create and manage a return form for customers.  Upon activation, it automatically creates a custom database table to store return form submissions. The plugin includes features for form submission handling, nonce verification for security,
email notifications to administrators and customers, and a user-friendly admin interface for
reviewing and managing return requests. Additionally, it leverages caching mechanisms to optimize 
database queries and enhance performance. Perfect for eCommerce sites using WooCommerce, this plugin 
ensures a streamlined and secure process for handling returns.

== Features ==

- Simple integration with any WordPress site
- Customisable form fields
- Form validation to ensure all necessary information is provided
- Email notifications upon form submission
- GDPR compliant with optional privacy policy agreement checkbox

== Installation ==

1. Install the plugin through the WordPress plugins screen directly or upload the plugin files to the `/wp-content/plugins/plugin-name` directory,
2. Activate the plugin through the 'Plugins' screen in WordPress.

== Requirements and Usage  ==

- SMTP Plugin: An SMTP plugin must be installed and configured to handle outgoing emails for notifications.
- After activation, navigate to the plugin settings page in the WordPress admin panel to select the privacy policy agreement page. 
- Use the shortcode [return_form] to display the form on any page or post.
- Admin panel Returns/Refunds submenu show a tabel of the requests history, with the details for each request
- Order number if clicked redirects to the specific order

== Uninstallation ==

To uninstall:
1. Deactivate the plugin through the 'Plugins' screen in WordPress.
2. Delete the plugin through the 'Plugins' screen in WordPress.

IMPORTANT NOTICE: Upon deletion, the plugin will remove all return form data from the database.
 Ensure you have backed up any necessary data before proceeding with plugin deletion.

== Screenshots ==


== Changelog ==
= 1.0.0 =
* Initial release.
