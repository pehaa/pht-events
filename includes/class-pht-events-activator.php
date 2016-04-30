<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events_Activator {

	/**
	 * Use PeHaaThemes_Simple_Post_Types class if available
	 *
	 * Adds the event post type via the PeHaaThemes_Simple_Post_Types if available
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( !class_exists( 'PeHaaThemes_Simple_Post_Types' ) ) {
			return;
		}

		$spt_option = get_option( 'pehaathemes_simple_post_types', array() );

		$spt_option['post_type']['pht_event'] = array(
			'name' => 'Events',
			'singular_name' => 'Event'
		);

		$spt_option['taxonomy']['pht_event_type'] = array(
			'name' => 'Event Types',
			'singular_name' => 'Event Type',
			'object_types' => array( 'pht_event' ),
			'hierarchical' => 'no'
		);

		update_option( 'pehaathemes_simple_post_types', $spt_option );

	}

}
