<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/pehaa/pht-events
 * @since      1.0.0
 *
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    PeHaa_Themes_Events
 * @subpackage PeHaa_Themes_Events/includes
 * @author     PeHaa Themes <info@pehaa.com>
 */
class PeHaa_Themes_Events {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      PeHaa_Themes_Events_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	public $key;

	public $labels_base;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'pht-events';
		$this->version = '1.0.0';
		$this->key = 'pht_event';
		$this->labels_base = array(
			'name' => __( 'Events', 'pht-events'),
			'singular_name' => __( 'Event', 'pht-events')
		);
		$this->taxonomy_key = 'pht_event_type';
		$this->taxomomy_labels_base = array(
			'name' => __( 'Events', 'pht-events'),
			'singular_name' => __( 'Event', 'pht-events')
		);
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		add_action( 'init', array( $this, 'register_event_taxonomy' ), 0 );
		add_action( 'init', array( $this, 'register_event_post_type' ) );

		add_action( 'widgets_init', array( $this, 'register_widgets' ), 0 );

		add_filter( 'pht_event_pehaathemes_spt_post_type_args', array( $this, 'pht_event_pehaathemes_spt_post_type_args' ), 10, 3 );
		add_filter( 'pht_event_type_pehaathemes_spt_taxonomy_args', array( $this, 'pht_event_type_pehaathemes_spt_post_type_args' ), 10, 3 );

	}

	public function pht_event_pehaathemes_spt_post_type_args( $args, $key, $array ) {

		$args['supports'][] = 'comments';
		$args['rewrite']['slug'] = 'event';
		return $args;
		
	}

	public function pht_event_type_pehaathemes_spt_post_type_args( $args, $key, $array ) {

		$args['rewrite']['slug'] = 'event_type';
		return $args;
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - PeHaa_Themes_Events_Loader. Orchestrates the hooks of the plugin.
	 * - PeHaa_Themes_Events_i18n. Defines internationalization functionality.
	 * - PeHaa_Themes_Events_Admin. Defines all hooks for the admin area.
	 * - PeHaa_Themes_Events_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pht-events-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-pht-events-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-pht-events-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-pht-events-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-pht-events-calendar.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/class-pht-events-calendar-widget.php';
		$this->loader = new PeHaa_Themes_Events_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the PeHaa_Themes_Events_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new PeHaa_Themes_Events_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new PeHaa_Themes_Events_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'cmb2_render_coollab_date', $plugin_admin, 'render_callback_for_coollab_date', 10, 5 );
		$this->loader->add_action( 'cmb2_render_coollab_time', $plugin_admin, 'render_callback_for_coollab_time', 10, 5 );
		$this->loader->add_filter( 'cmb2_sanitize_coollab_date', $plugin_admin, 'sanitize_callback_for_coollab_date', 10, 2 );
		$this->loader->add_filter( 'cmb2_sanitize_coollab_time', $plugin_admin, 'sanitize_callback_for_coollab_time', 10, 2 );
		$this->loader->add_action( 'cmb2_init', $plugin_admin, 'register_metaboxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'delete_events_transient' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new PeHaa_Themes_Events_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'pre_get_posts', $plugin_public, 'events_archive_query' );
		$this->loader->add_action( 'wp_ajax_pehaathemes_events_calendar', $plugin_public, 'ajax_calendar' );
		$this->loader->add_action( 'wp_ajax_nopriv_pehaathemes_events_calendar', $plugin_public, 'ajax_calendar' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    PeHaa_Themes_Events_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register custom post types retrieved from the plugin options array
	 *
	 * @since     1.0.0
	 */

	public function register_event_post_type() {	
				
		if ( !class_exists( 'PeHaaThemes_Simple_Post_Types' ) ) {
			$args = $this->event_args();
			register_post_type( $this->key, $args );
		}

	}

	public function register_event_taxonomy() {	
				
		if ( !class_exists( 'PeHaaThemes_Simple_Post_Types' ) ) {
			$args = $this->event_type_args();
			register_taxonomy( $this->taxonomy_key, array( $this->key ), $args );
		}	

	}

	private function event_labels() {

		$array = $this->labels_base;

		$labels = array(
			'name'                => $array['name'],
			'singular_name'       => $array['singular_name'],
			'menu_name'           => $array['name'],
			'name_admin_bar'	  => $array['singular_name'],
			'parent_item_colon'   => sprintf( __('Parent %s:', 'pht-events' ),  $array['singular_name'] ),
			'all_items'           => sprintf( __( 'All %s', 'pht-events' ), $array['name'] ),
			'view_item'           => sprintf( __( 'View %s', 'pht-events' ),  $array['singular_name'] ),
			'add_new_item'        => sprintf( __( 'Add New %s', 'pht-events' ),  $array['singular_name'] ),
			'add_new'             => __( 'Add New', 'pht-events' ),
			'edit_item'           => sprintf( __( 'Edit %s', 'pht-events' ), $array['singular_name'] ),
			'new_item'						=> sprintf( __( 'New %s', 'pht-events' ),  $array['singular_name'] ),
			'search_items'        => sprintf( __( 'Search %s', 'pht-events' ),  $array['singular_name'] ),
			'not_found'           => __( 'Not found', 'pht-events' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'pht-events' ),
		);

		return apply_filters( $this->key .'_pehaathemes_spt_post_type_labels', $labels, $array );

	}

	private function event_args() {

		$array = $array = $this->labels_base;

		$args = array(
			'labels' => $this->event_labels(),
			'description'         => '',
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail' ),
			'hierarchical'        => false,
			'public'              => true,
			'menu_position'       => 5,
			'menu_icon'				=> NULL,
			'has_archive'         => true,
			'rewrite' => array( 
				'slug' => $this->key,
				'with_front' => true
			 ),
			'query_var' => true
		);

		return apply_filters( $this->key .'_pehaathemes_spt_post_type_args', $args, $this->key, $array );
		
	}

	private function event_type_labels() {

		$array = $this->taxomomy_labels_base;

		$name = ucfirst( isset( $array['name'] ) ? $array['name'] : 'taxonomies' );
		$singular_name = ucfirst( isset( $array['singular_name'] ) ? $array['singular_name'] : 'taxonomy' );

		$labels = array(
			'name'                => $name,
			'singular_name'       => $singular_name,
			'menu_name'           => $name,
			'parent_item'					=> sprintf( __( 'Parent %s', 'pht-events' ), $singular_name ),
			'parent_item_colon'   => sprintf( __( 'Parent %s:', 'pht-events' ), $singular_name ),
			'all_items'           => sprintf( __( 'All %s', 'pht-events' ), $name ),
			'view_item'           => sprintf( __( 'View %s', 'pht-events' ), $singular_name ),
			'add_new_item'        => sprintf( __( 'Add New %s', 'pht-events' ), $singular_name ),
			'add_new'             => __( 'Add New', 'pht-events' ),
			'edit_item'           => sprintf( __( 'Edit %s', 'pht-events' ), $singular_name ),
			'update_item'         => sprintf(  __( 'Update %s', 'pht-events' ), $singular_name ),
			'search_items'        => sprintf( __( 'Search %s', 'pht-events' ), $singular_name ),
			'not_found'           => __( 'Not found', 'pht-events' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'pht-events' ),
			'new_item_name'              => sprintf( __( 'New %s Name', 'pht-events' ), $name ),
			'separate_items_with_commas' => sprintf( __( 'Separate %s with commas', 'pht-events' ), $name ),
			'add_or_remove_items'        => sprintf( __( 'Add or remove %s', 'pht-events' ), $name ),
			'choose_from_most_used'      => sprintf( __( 'Choose from the most used %s', 'pht-events' ), $name )
			);

		return apply_filters( $this->taxonomy_key .'_pehaathemes_spt_taxonomy_labels', $labels, $array );

	}

	private function event_type_args() {

		$args = array(
				'labels' => $this->event_type_labels(),
				'hierarchical' => isset( $array['hierarchical'] ) && 'yes' === $array['hierarchical'] ? true : false,
				'public' => true,
				'show_ui'             => true,
				'show_in_nav_menus'   => true,
				'show_tagcloud' => true,
				'query_var' => true,
				'rewrite' => array( 
					'slug' => $this->taxonomy_key,
					'with_front' => true,
					'hierarchical' => false,
				 )
			);

		return apply_filters( $this->taxonomy_key .'_pehaathemes_spt_taxonomy_args', $args, $this->taxonomy_key, $this->taxomomy_labels_base );
		
	}

	public function register_widgets() {
		register_widget( 'PeHaa_Themes_Events_Calendar_Widget' );
	}

}
