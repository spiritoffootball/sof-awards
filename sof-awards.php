<?php
/**
 * SOF Awards
 *
 * Plugin Name:       SOF Awards
 * Description:       Provides "Awards" functionality for the Spirit of Football website.
 * Plugin URI:        https://github.com/spiritoffootball/sof-awards
 * GitHub Plugin URI: https://github.com/spiritoffootball/sof-awards
 * Version:           1.0.0a
 * Author:            Christian Wach
 * Author URI:        https://haystack.co.uk
 * Text Domain:       sof-awards
 * Domain Path:       /languages
 *
 * @package Spirit_Of_Football_Awards
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Set our version here.
define( 'SOF_AWARDS_VERSION', '1.0.0a' );

// Store reference to this file.
if ( ! defined( 'SOF_AWARDS_FILE' ) ) {
	define( 'SOF_AWARDS_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'SOF_AWARDS_URL' ) ) {
	define( 'SOF_AWARDS_URL', plugin_dir_url( SOF_AWARDS_FILE ) );
}

// Store PATH to this plugin's directory.
if ( ! defined( 'SOF_AWARDS_PATH' ) ) {
	define( 'SOF_AWARDS_PATH', plugin_dir_path( SOF_AWARDS_FILE ) );
}

/**
 * Plugin Class.
 *
 * A class that encapsulates plugin functionality.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Awards {

	/**
	 * Awards loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Awards_Loader
	 */
	public $awards;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Initialise when all plugins are loaded.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this plugin.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap plugin.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this plugin is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_awards/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes plugin files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {

		// Include class files.
		require SOF_AWARDS_PATH . 'includes/class-awards.php';

	}

	/**
	 * Sets up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	private function setup_objects() {

		// Init objects.
		$this->awards = new Spirit_Of_Football_Awards_Loader( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {

		// Use translation.
		add_action( 'init', [ $this, 'translation' ] );

	}

	/**
	 * Enables translation.
	 *
	 * @since 1.0.0
	 */
	public function translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'sof-awards', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( SOF_AWARDS_FILE ) ) . '/languages/' // Relative path to files.
		);

	}

	/**
	 * Performs plugin activation tasks.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin activation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_awards/activate' );

	}

	/**
	 * Performs plugin deactivation tasks.
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Maybe init.
		$this->initialise();

		/**
		 * Broadcast plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_awards/deactivate' );

	}

}

/**
 * Gets a reference to this plugin.
 *
 * @since 1.0.0
 *
 * @return Spirit_Of_Football_Awards $plugin The plugin reference.
 */
function sof_awards() {

	// Store instance in static variable.
	static $plugin = false;

	// Maybe return instance.
	if ( false === $plugin ) {
		$plugin = new Spirit_Of_Football_Awards();
	}

	// --<
	return $plugin;

}

// Initialise plugin now.
sof_awards();

// Activation.
register_activation_hook( __FILE__, [ sof_awards(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ sof_awards(), 'deactivate' ] );

/*
 * Uninstall uses the 'uninstall.php' method.
 *
 * @see https://codex.wordawards.org/Function_Reference/register_uninstall_hook
 */
