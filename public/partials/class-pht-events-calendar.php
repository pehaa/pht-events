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
class PeHaa_Themes_Events_Calendar {

	protected static $instance = NULL;

	public $no_events;

	private function __construct() {

		$this->check_if_any_events();
		if ( !$this->no_events ) {
			$this->get_now();
			$this->get_week_begins();
			$this->get_week();
		}
		
	}

	public static function get_instance() {

		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;

	}

	private function get_now() {

		$this->ts = current_time( 'timestamp' );
		$this->month = gmdate( 'm', $this->ts ); //03
		$this->year = gmdate( 'Y', $this->ts ); //2016

	}

	private function get_week_begins() {

		// week_begins = 0 stands for Sunday
		$this->week_begins = (int) get_option( 'start_of_week' );
	}

	private function get_week() {

		global $wp_locale;

		$this->week = $this->week_abbr = $this->week_raw = array();
		for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
			$wd = $wp_locale->get_weekday( ( $wdcount + $this->week_begins ) % 7 );
			$this->week_raw[] = ( $wdcount + $this->week_begins ) % 7;
			$this->week[] = $wd;
			$this->week_abbr[] = $wp_locale->get_weekday_abbrev( $wd );
		}
	}

	protected function display_week_labels() {

		$output = '';

		$i = 0;

		foreach ( $this->week_abbr as $day_name ) {
		
			$output .= sprintf( '<div class="%1$s">%3$s</div>',
				apply_filters( 'pht-events-calendar__day_class', 'pht-events-calendar__day pht-events-calendar__label-' . $this->week_raw[ $i ], $this->week_raw[ $i ] ),
				esc_attr( $day_name ),
				esc_html( $day_name )
			);
			$i++;
		}

		return $output;

	}

	public function days_with_events( $Month, $Y, $last_day ) {

		global $wpdb;

		if ( $this->no_events ) {
			return array();
		}

		$months_of_daywithevent = get_transient( 'pht-events-calendar' );

		if ( false === ( $months_of_daywithevent ) || !isset( $months_of_daywithevent[ $Month.$Y ] ) ) {
			$start_month = "{$Y}-{$Month}-{$last_day}";
			$end_month = '{$Y}-{$Month}-01';
			$events_request = $wpdb->prepare(
				"SELECT event_start.meta_value as EventStartDate, 
						event_start.post_id as ID,
						event_end.meta_value as EventEndDate
				FROM $wpdb->postmeta AS event_start
				LEFT JOIN $wpdb->posts ON event_start.post_id = $wpdb->posts.ID
				LEFT JOIN $wpdb->postmeta as event_end ON ( event_start.post_id = event_end.post_id AND event_end.meta_key = 'pht_events_enddate' )
				WHERE event_start.meta_key = 'pht_events_startdate'
				AND ( ( event_start.meta_value <= %s AND event_start.meta_value >= %s ) OR ( event_end.meta_value <= %s AND event_end.meta_value >= %s ) )
				AND $wpdb->posts.post_status = 'publish';", $start_month, $end_month,$start_month, $end_month
			);

			$dayswithevents = $wpdb->get_results( $events_request );

			$months_of_daywithevent[ $Month.$Y ] = $dayswithevents;
			
			set_transient( 'pht-events-calendar', $months_of_daywithevent, HOUR_IN_SECONDS );
		}

 		return $months_of_daywithevent[ $Month.$Y ];

	}

	private function is_current_year( $Y ) {
		$ts = current_time( 'timestamp' );
		return $Y === gmdate( 'Y', $ts );
	}

	private function is_current_month( $Month, $Y ) {

		if ( ! $this->is_current_year( $Y ) ) {
			return;
		}
		$ts = current_time( 'timestamp' );
		return $Month === gmdate( 'm', $ts );
	}

	private function is_today( $day, $Month, $Y ) {

		if ( ! $this->is_current_month( $Month, $Y ) ) {
			return;
		}
		$ts = current_time( 'timestamp' );
		return $day === (int) gmdate( 'j', $ts );
	}

	public function day_link( $day, $Month, $Y, $last_day ) {	

		if ( empty( $this->days_with_events ) ) {
			return false;
		}

		$date = date( 'Y-m-d', mktime( 0, 0 , 0, $Month, $day, $Y ) );

		$link = false;

		foreach( $this->days_with_events as $day_with_event ) {
			if ( !isset( $day_with_event->EventEndDate ) ) {
				$day_with_event->EventEndDate = $day_with_event->EventStartDate;
			}
			
			if ( $date >= $day_with_event->EventStartDate && $date <= $day_with_event->EventEndDate ) {
				if ( $link ) {
					$link = $this->pht_events_get_day_link( $Y, $Month, $day );
				} else {
					$link = get_permalink( $day_with_event->ID );
				}
			}			
		}
		return $link;
	}


	public function pht_get_calendar( $Month = NULL, $Y = NULL, $no_ajax = true ) {

		if ( $this->no_events ) {
			$output = esc_html__( 'No events found', 'pht-events' );

			return $output;
		}
	
		global $wpdb, $wp_locale;

		if ( NULL === $Month ) {
			$Month = $this->month;
			$Y = $this->year;
		}

		$key = md5( $Month . $Y );
	
		$cache = wp_cache_get( 'pht-events_get_calendar', 'pht-events_calendar' );

		if ( $cache && is_array( $cache ) && isset( $cache[ $key ] ) ) {
			
			$output = apply_filters( 'pht-events_get_calendar', $cache[ $key ] );

			$output .= 'here';

			return $output;
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}		

		$unixmonth = mktime( 0, 0 , 0, $Month, 1, $Y );
	
		$last_day = date( 't', $unixmonth );

		$base_date = strtotime("$Y-$Month");

		$previous = array( 'month' => date( 'm', strtotime("-1 month", $base_date) ), 'year' => date( 'Y', strtotime("-1 month", $base_date) ) ); 
		$next = array( 'month' => date( 'm', strtotime("+1 month", $base_date) ), 'year' => date( 'Y', strtotime("+1 month", $base_date) ) );

	
		$calendar_output = '';
		if ( $no_ajax ) {
			$calendar_output = '<div class="js-calendar-container pht-events-calendar__container">';
		}
		$calendar_output .= sprintf( '<div class="js-calendar %s">', apply_filters( 'pht-events-calendar__table_classes', 'pht-events-calendar__table' ) );

		$pad = calendar_week_mod( date( 'w', $unixmonth ) - $this->week_begins );

		$calendar_output .= "<div class='pht-events-calendar__blocks pht-events-calendar-pad-$pad'>";
	
		$calendar_output .= $this->display_week_labels();

		$pad_class = "pht-events-calendar--pad-$pad";

		$look_for_today = $this->is_current_month( $Month, $Y );

		$this->days_with_events = $this->days_with_events( $Month, $Y, $last_day );

		for ( $day = 1; $day <= (int) $last_day; ++$day ) {
		
			if ( $look_for_today ) {
				$is_today = $this->is_today( $day, $Month, $Y );
				$look_for_today = !$is_today;	
			} else {
				$is_today = false;
			}
		
			$day_link = $this->day_link( $day, $Month, $Y, $last_day );
		
			$calendar_output .= sprintf( '<div class="pht-events-calendar__day_td %1$s %2$s %3$s %4$s %5$s">',
				esc_attr( $pad_class ),
				$is_today ? 'pht-events-calendar__day_td--today' : '',
				'pht-events-calendar__day-' . $this->week_raw[ ( $day +  $pad - 1 ) % 7 ],
				$day_link ? 'pht-events-calendar__day_td--link' : '',
				apply_filters( 'pht-events-calendar__day_td_classes', '', $day_link, $is_today )
			);

			if ( $day_link ) {

				$date_format = date( _x( 'F j, Y', 'daily archives date format' ), strtotime( "{$Y}-{$Month}-{$day}" ) );
				$label = sprintf( __( 'Events scheduled on %s', 'pht-events' ), $date_format );
				$calendar_output .= sprintf( '<a href="%s" aria-label="%s" class="pht-events-calendar__day_span %s">%s</a>',
					esc_url( $day_link ),
					esc_attr( $label ),
					$is_today ? 'pht-events-calendar__day_span--today' : '',
					esc_html( $day )
				);

			} else {
				$calendar_output .= sprintf( '<span class="pht-events-calendar__day_span %s">%s</span>',
					$is_today ? 'pht-events-calendar__day_span--today' : '',
					esc_html( $day )
				);

			}

			$calendar_output .= '</div>';		

			$pad_class = '';
		
		}
	
		$calendar_output .= '</div>';
		$calendar_output .= '</div>';
		$calendar_output .= '<div class="pht-events-calendar__nav">';
		$calendar_output .= sprintf( '<a class="js-pht-events-calendar-link pht-events-calendar__link pht-events-calendar__link--prev %1$s" href="#" data-calendar-link-month="%2$s" data-calendar-link-year="%3$s">%4$s</a>',
			apply_filters( 'pht-events-calendar__link--prev_classes', '' ),
			$previous['month'],
			$previous['year'],
			$wp_locale->get_month_abbrev( $wp_locale->get_month( $previous['month'] ) )
		);

		$calendar_output .= '<span class="pht-events-calendar__caption">';
		$calendar_output .= sprintf( _x('%1$s %2$s', 'calendar caption'),
			$wp_locale->get_month( $Month ),
			date( 'Y', $unixmonth )
		);
		$calendar_output .= '</span>';
		$calendar_output .= sprintf( '<a class="js-pht-events-calendar-link pht-events-calendar__link pht-events-calendar__link--next %1$s" href="#" data-calendar-link-month="%2$s" data-calendar-link-year="%3$s">%4$s</a>',
			apply_filters( 'pht-events-calendar__link--next_classes', '' ),
			$next['month'],
			$next['year'],
			$wp_locale->get_month_abbrev( $wp_locale->get_month( $next['month'] ) )
		);
		
		$calendar_output .= '</div>';

		if ( $no_ajax ) {
			$calendar_output .= '</div>';
		}

		$cache[ $key ] = $calendar_output;
		wp_cache_set( 'pht-events_get_calendar', $cache, 'pht-events_calendar' );
	
		return $calendar_output;
	}


	private function check_if_any_events() {
		global $wpdb;
		$gotsome = $wpdb->get_var("SELECT 1 as test FROM $wpdb->posts WHERE post_type = 'pht_event' AND post_status = 'publish' LIMIT 1");
		$this->no_events = ! $gotsome;	
	}


	private function pht_events_get_day_link( $year, $month, $day ) {
		
		global $wp_rewrite;
		
		if ( ! $year ) {
			$year = gmdate( 'Y', current_time( 'timestamp' ) );
		}
			
		if ( ! $month ) {
			$month = gmdate( 'm', current_time( 'timestamp' ) );
		}
			
		if ( ! $day ) {
			$day = gmdate( 'j', current_time( 'timestamp' ) );
		}
			
		$daylink = $wp_rewrite->get_day_permastruct();

		if ( ! empty( $daylink ) ) {
			$daylink = str_replace( '%year%', $year, $daylink );
			$daylink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $daylink );
			$daylink = str_replace( '%day%', zeroise( intval( $day ), 2 ), $daylink );
			$daylink = esc_url( home_url( user_trailingslashit( $daylink, 'day' ) .'?post_type=pht_event' ) );
		} else {
			$daylink = esc_url( home_url( '?m=' . $year . zeroise( $month, 2 ) . zeroise( $day, 2 ) . '&post_type=pht_event' ) );
		}


		/**
		 * Filter the day archive permalink.
		 *
		 * @since 1.5.0
		 *
		 * @param string $daylink Permalink for the day archive.
		 * @param int    $year    Year for the archive.
		 * @param int    $month   Month for the archive.
		 * @param int    $day     The day for the archive.
		 */
		return apply_filters( 'day_link', $daylink, $year, $month, $day );
	}

}