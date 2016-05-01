<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/public
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PeHaa_Themes_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PeHaa_Themes_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pht-events-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in PeHaa_Themes_Events_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The PeHaa_Themes_Events_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pht-events-public.min.js', array( 'jquery' ), $this->version, false );
		$myparams = array(
			'nonce' => wp_create_nonce( $this->plugin_name . '_calendar' ),
			'ajaxURL' => esc_url( admin_url( 'admin-ajax.php' ) ),
		);

		wp_localize_script( $this->plugin_name, 'pht_events_scriptparams', $myparams );

	}

	public function events_archive_query( $query ) {

		if ( is_admin() ) {
			return;
		}

		$args = array();
		
		if ( $query->is_post_type_archive( 'pht_event' ) || $query->is_tax( 'pht_event_type' ) ) {

			if ( is_tax( 'pht_event_type' ) ) {

				$args = self::pht_se_modify_events_query_basic();

			}	elseif ( is_year() ) {
				$year = $query->get( 'year' );

				$args = self::pht_se_modify_year_query( $year );
								
			} elseif ( is_month() ) {
				$year = $query->get( 'year' );
				$month = $query->get( 'monthnum' );

				$args = self::pht_se_modify_month_query( $year, $month );
				
			} elseif ( is_day() ) {
				
				$year = $query->get( 'year' );
				$month = $query->get( 'monthnum' );
				$day = $query->get( 'day' );

				$args = self::pht_se_modify_day_query( $year, $month, $day );
			}

			if ( !empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					$query->set( $key, $value );
				}
			}

		}

	}

	public function ajax_calendar() {
		check_ajax_referer( $this->plugin_name . '_calendar' , 'nonce' );
		if ( !is_numeric( $_POST['year'] ) || !$_POST['month'] ) {
			die( 'Invalid data' );
		}
		$calendar = PeHaa_Themes_Events_Calendar::get_instance();
		echo $calendar->pht_get_calendar( $_POST['month'], $_POST['year'], false );
		exit;
	}

	public static function pht_se_modify_day_query( $year, $month, $day ) {

		$args = self::pht_se_modify_events_query_basic();
		$args['day'] = 0;
		$args['monthnum'] = 0;
		$args['year'] = 0;
		
		$args['meta_query'] = array(
			'relation' => 'OR',
				array(
					'key'     => 'pht_events_startdate',
					'value'   => "$year-$month-$day",
					'compare' => '=',
					'type' => 'DATE'
				),
				array(
					'relation' => 'AND',
					array(
						'key' => 'pht_events_enddate',
						'value' => "$year-$month-$day",
						'compare' => '>=',
						'type' => 'DATE'
					),
					array(
						'key' => 'pht_events_startdate',
						'value' => "$year-$month-$day",
						'compare' => '<=',
						'type' => 'DATE'
					)
										
				)
			);

		return $args;			

	}

	public static function pht_se_modify_month_query( $year, $month ) {

		$args = self::pht_se_modify_events_query_basic();
		$args['monthnum'] = 0;
		$args['year'] = 0;
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => 'pht_events_enddate',
				'value' => array( "$year-$month-01",  "$year-$month-31" ),
				'compare' => 'BETWEEN',
				'type' => 'DATE'
			),
			array(
				'key' => 'pht_events_startdate',
				'value' => array( "$year-$month-01",  "$year-$month-31" ),
				'compare' => 'BETWEEN',
				'type' => 'DATE'
			)
		);

		return $args;			

	}

	public static function pht_se_modify_year_query( $year ) {

		$args = self::pht_se_modify_events_query_basic();
		$args['year'] = 0;
		$args['meta_query'] = array(
			'relation' => 'OR',
			array(
				'key' => 'pht_events_startdate',
				'value' => array( "$year-01-01",  "$year-12-31" ),
				'compare' => 'BETWEEN',
				'type' => 'DATE'
			),
			array(
				'key' => 'pht_events_enddate',
				'value' => array( "$year-01-01", "$year-12-31" ),
				'compare' => 'BETWEEN',
				'type' => 'DATE'
			),
		);

		return $args;			

	}

	public static function pht_se_modify_events_query_basic() {

		$args = array();
		$args['meta_key'] = 'pht_events_startdate';
		$args['order'] = 'ASC';
		$args['orderby'] = 'meta_value';

		return $args;

	}

	public static function pht_se_modify_events_query( $status ) {

		$args = array();

		if ( in_array( $status, array( 'upcoming', 'past' ) ) ) {

			$year = gmdate( 'Y', current_time( 'timestamp' ) );
			$month = gmdate( 'm', current_time( 'timestamp' ) );
			$day = gmdate( 'j', current_time( 'timestamp' ) );
				
			$args = self::pht_se_modify_events_query_basic();

			if ( 'upcoming' === $status ) {
					$args = wp_parse_args( 
						array(
							'meta_query' => array(
								'relation' => 'OR',
								array(
									'key' => 'pht_events_enddate',
									'value' => "$year-$month-$day",
									'compare' => '>=',
									'type' => 'DATE'
								),
								array(
									'key' => 'pht_events_startdate',
									'value' => "$year-$month-$day",
									'compare' => '>=',
									'type' => 'DATE'
								)
							)
						),
						$args
					);
			} else {
				$args = wp_parse_args( 
					array(
						'order' => 'DESC',
						'meta_query' => array(
							'key' => 'pht_events_startdate',
							'value' => "$year-$month-$day",
							'compare' => '<',
							'type' => 'DATE'
						),
					),
					$args
				);
			}
			
		}

		return $args;

	} 

}