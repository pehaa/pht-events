<?php
/**
 *
 * @link              https://github.com/pehaa/pht-events
 * @since             1.1.0
 * @package           PeHaa_Themes_Events
 *
 * @wordpress-plugin
 * Plugin Name:       PeHaa Themes Events
 * Plugin URI:        http://github.com/pehaa/pht-events/
 * Description:       Adds Event custom post type and Event Type taxonomy
 * Version:           1.0.0
 * Author:            PeHaa THEMES
 * Author URI:        https://github.com/pehaa/pht-events/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       pht-events
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pht-events-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pht-events-activator.php';
	PeHaa_Themes_Events_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pht-events-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pht-events-deactivator.php';
	PeHaa_Themes_Events_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pht-events.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_pht_events() {

	$plugin = new PeHaa_Themes_Events();
	$plugin->run();

}
run_pht_events();
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = PucFactory::buildUpdateChecker(
    'http://wp-plugins.pehaa.com/pht-events/metadata.json',
    __FILE__
);