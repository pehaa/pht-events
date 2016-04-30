<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'pht-events',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
