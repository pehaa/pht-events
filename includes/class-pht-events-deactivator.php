<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_transient( 'pht-events-calendar' );

	}

}
