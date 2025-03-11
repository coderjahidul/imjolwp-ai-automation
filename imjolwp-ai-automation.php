<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/coderjahidul/
 * @since             1.0.0
 * @package           Imjolwp_Ai_Automation_For_Wordpress
 *
 * @wordpress-plugin
 * Plugin Name:       ImjolWP - AI Automation
 * Plugin URI:        https://github.com/coderjahidul/
 * Description:       ImjolWP is an AI-powered automation plugin that generates post titles, descriptions, images, summaries, audio, and videos using AI. It supports Elementor, automated scheduling, and a queue system to streamline content creation effortlessly.
 * Version:           1.0.0
 * Author:            Jahidul islam Sabuz
 * Author URI:        https://github.com/coderjahidul//
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       imjolwp-ai-automation
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'IMJOLWP_AI_AUTOMATION_FOR_WORDPRESS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 */
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
use Imjolwp\Imjolwp_Ai_Automation_For_Wordpress;
use Imjolwp\Imjolwp_Ai_Automation_For_Wordpress_Activator;
use Imjolwp\Imjolwp_Ai_Automation_For_Wordpress_Deactivator;


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-imjolwp-ai-automation-for-wordpress-activator.php
 */
function activate_imjolwp_ai_automation_for_wordpress() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-imjolwp-ai-automation-for-wordpress-activator.php';
	Imjolwp_Ai_Automation_For_Wordpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-imjolwp-ai-automation-for-wordpress-deactivator.php
 */
function deactivate_imjolwp_ai_automation_for_wordpress() {
	// require_once plugin_dir_path( __FILE__ ) . 'includes/class-imjolwp-ai-automation-for-wordpress-deactivator.php';
	Imjolwp_Ai_Automation_For_Wordpress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_imjolwp_ai_automation_for_wordpress' );
register_deactivation_hook( __FILE__, 'deactivate_imjolwp_ai_automation_for_wordpress' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_imjolwp_ai_automation_for_wordpress() {

	$plugin = new Imjolwp_Ai_Automation_For_Wordpress();
	$plugin->run();

}
run_imjolwp_ai_automation_for_wordpress();

// Function to append data to a log file
function put_program_logs( $data ) {

    // Ensure the directory for logs exists
    $directory = __DIR__ . '/program_logs/';
    if ( ! file_exists( $directory ) ) {
        // Use wp_mkdir_p instead of mkdir
        if ( ! wp_mkdir_p( $directory ) ) {
            return "Failed to create directory.";
        }
    }

    // Construct the log file path
    $file_name = $directory . 'program_logs.log';

    // Append the current datetime to the log entry
    $current_datetime = gmdate( 'Y-m-d H:i:s' ); // Use gmdate instead of date
    $data             = $data . ' - ' . $current_datetime;

    // Write the log entry to the file
    if ( file_put_contents( $file_name, $data . "\n\n", FILE_APPEND | LOCK_EX ) !== false ) {
        return "Data appended to file successfully.";
    } else {
        return "Failed to append data to file.";
    }
}

// Generate focus keywords function
function generate_focus_keywords($text) {
    $text = strtolower(strip_tags($text)); // Convert to lowercase & remove HTML tags
    $text = preg_replace('/[^a-z0-9\s]/', '', $text); // Remove special characters
    $words = explode(' ', $text); // Split into words

    // Common words to ignore
    $stop_words = ['i', 'the', 'and', 'for', 'with', 'a', 'to', 'is', 'on', 'by', 'at', 'it', 'in', 'of', 'as', 'this', 'that'];

    // Filter out common words and select unique keywords
    $keywords = array_diff($words, $stop_words);
    $keywords = array_unique($keywords);

    return implode(' ', array_slice($keywords, 0, 5)); // Limit to 5 keywords
}


add_action('plugins_loaded', function() {
    new \Imjolwp\Automation\Imjolwp_Ai_Automation_For_Wordpress_Automation();
});



// if (!class_exists('Imjolwp\Admin\Partials\Imjolwp_Ai_Automation_For_Wordpress_Admin_Display')) {
//     require_once __DIR__ . '/admin/partials/Imjolwp_Ai_Automation_For_Wordpress_Admin_Display.php';
// }

// if(!class_exists('Imjolwp\Settings\Imjolwp_Ai_Automation_For_Wordpress_Dashboard')) {
//     require_once __DIR__ . '/admin/settings/Imjolwp_Ai_Automation_For_Wordpress_Dashboard.php';
// }

// if(!class_exists('Imjolwp\Settings\Imjolwp_Ai_Automation_For_Wordpress_Scheduled_Post_list')) {
//     require_once __DIR__ . '/admin/settings/Imjolwp_Ai_Automation_For_Wordpress_Scheduled_Post_list.php';
// }

// if(!class_exists('Imjolwp\Settings\Imjolwp_Ai_Automation_For_Wordpress_Settings')) {
//     require_once __DIR__ . '/admin/settings/Imjolwp_Ai_Automation_For_Wordpress_Settings.php';
// }

// if(!class_exists('Imjolwp\Admin\Imjolwp_Ai_Automation_For_Wordpress_Admin')) {
//     require_once __DIR__ . '/admin/Imjolwp_Ai_Automation_For_Wordpress_Admin.php';
// }

// if(!class_exists('Imjolwp\Automation\Imjolwp_Ai_Automation_For_Wordpress_Automation')) {
//     require_once __DIR__ . '/includes/automation/Imjolwp_Ai_Automation_For_Wordpress_Automation.php';
// }

// if(!class_exists('Imjolwp\AAutomation\Imjolwp_Ai_Automation_For_Wordpress_Queue')) {
//     require_once __DIR__ . '/includes/automation/Imjolwp_Ai_Automation_For_Wordpress_Queue.php';
// }

// if(!class_exists('Imjolwp\Ai\Imjolwp_Ai_Automation_For_Wordpress_Ai_Description')) {
//     require_once __DIR__ . '/includes/ai/Imjolwp_Ai_Automation_For_Wordpress_Ai_Description.php';
// }