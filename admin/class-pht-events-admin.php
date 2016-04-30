<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/admin
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/pht-events-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/pht-events-admin.min.js', array( 'jquery' ), $this->version, false );

	}

	public function register_metaboxes() {

		$prefix = 'pht_events_';

		$cmb_post = new_cmb2_box( array(
			'id'            => $prefix . '_settings',
			'title'         => esc_html__( 'Event Settings', 'pht-events' ),
			'object_types'  => array( 'pht_event' ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true,
		) );

		$date = array(
			'name' => __( 'Start Date', 'pht-events' ),
			'desc' => __( 'field description (optional)', 'pht-events' ),
			'id'   => $prefix . 'startdate',
			'type' => 'coollab_date',
			'date_format' => 'Y-m-d'
		);

		$time = array(
			'name' => __( 'Start Time', 'pht-events' ),
			'desc' => __( 'field description (optional)', 'pht-events' ),
			'id'   => $prefix . 'starttime',
			'type' => 'coollab_time',
			'time_format' => 'H:i'
		);

		$date_end = array(
			'name' => __( 'End Date', 'pht-events' ),
			'desc' => __( 'field description (optional)', 'pht-events' ),
			'id'   => $prefix . 'enddate',
			'type' => 'coolab_date',
			'date_format' => 'Y-m-d'
		);

		$time_end = array(
			'name' => __( 'End Time', 'pht-events' ),
			'desc' => __( 'field description (optional)', 'pht-events' ),
			'id'   => $prefix . 'endtime',
			'type' => 'coollab_time',
			'time_format' => 'H:i'
		);

		$cmb_post->add_field( $date );
		$cmb_post->add_field( $time );
		$cmb_post->add_field( $date_end );
		$cmb_post->add_field( $time_end );
	}

	public function render_callback_for_coollab_date( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		echo $field_type_object->input( array( 'type' => 'text', 'class' => 'js-pht-event-date' ) );
	}

	public function render_callback_for_coollab_time( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		echo $field_type_object->input( array( 'type' => 'text', 'class' => 'js-pht-event-time' ) );
	}

	public function sanitize_callback_for_coollab_date( $override_value, $value ) {

		$value = trim( $value );
		$regex = '/^\\d{4,4}\\-(0{0,1}[1-9]|1[012])\\-(0{0,1}[1-9]|[12][0-9]|3[01])$/';

		preg_match( $regex, $value, $matches);

		if ( empty( $matches ) ) {
			return '';
		}
		return $value;

	}


	public function sanitize_callback_for_coollab_time( $override_value, $value ) {

		$value = trim( $value );
		$regex = '/^([0-9]|0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/';
		preg_match( $regex, $value, $matches);

		if ( empty( $matches ) ) {
			return '';
		}
		return $value;

	}

	public function delete_events_transient( $post_id ) {
		if ( 'pht_event' === get_post_type( $post_id ) ) {
			delete_transient( 'pht-events-calendar' );
		}
		
	}

}
