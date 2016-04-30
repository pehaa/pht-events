<?php
/**
 * Core class used to implement the Calendar widget.
 *
 * @since 2.8.0
 *
 * @see WP_Widget
 */
class PeHaa_Themes_Events_Calendar_Widget extends WP_Widget {
	/**
	 *
	 * @since 1.0.0
	 *
	 * @static
	 * @access private
	 * @var int
	 */
	private static $instance = 0;

	/**
	 * Sets up a new Calendar widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'pht_events_calendar',
			'description' => __( 'An Events Calendar', 'pht-events' ),
		);
		parent::__construct( 'pht_events_calendar', __( 'Events Calendar', 'pht-events' ), $widget_ops );
	}

	/**
	 * Outputs the content for the current Events Calendar widget instance.
	 *
	 */
	public function widget( $args, $instance ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		$calendar = PeHaa_Themes_Events_Calendar::get_instance();
		echo $calendar->pht_get_calendar();
		echo $args['after_widget'];

		self::$instance++;
	}

	/**
	 * Handles updating settings for the current Calendar widget instance.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Outputs the settings form for the Calendar widget.
	 *
	 * @since 2.8.0
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title = sanitize_text_field( $instance['title'] );
		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<?php
	}
}