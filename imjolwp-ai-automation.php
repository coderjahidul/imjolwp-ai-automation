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
 * Plugin URI:        https://github.com/coderjahidul/imjolwp-ai-automation
 * Description:       ImjolWP is an AI-powered automation plugin that generates post titles, descriptions, images, summaries, audio, and videos using AI. It supports Elementor, automated scheduling, and a queue system to streamline content creation effortlessly.
 * Version:           1.0.1
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
define( 'IMJOLWP_AI_AUTOMATION_VERSION', '1.0.0' );

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
 * This action is documented in includes/class-imjolwp-ai-automation-activator.php
 */
function imjolwp_ai_automation_for_wordpress_activate() {
	Imjolwp_Ai_Automation_For_Wordpress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-imjolwp-ai-automation-deactivator.php
 */
function imjolwp_ai_automation_for_wordpress_deactivate() {
	Imjolwp_Ai_Automation_For_Wordpress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'imjolwp_ai_automation_for_wordpress_activate' );
register_deactivation_hook( __FILE__, 'imjolwp_ai_automation_for_wordpress_deactivate' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function imjolwp_ai_automation_run() {

	$plugin = new Imjolwp_Ai_Automation_For_Wordpress();
	$plugin->run();

}
imjolwp_ai_automation_run();

// Generate focus keywords function
function imjolwp_generate_focus_keywords($text) {
	$text = strtolower(wp_strip_all_tags($text)); // Convert to lowercase & remove HTML tags
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
