<?php
/**
 * Awards loader class.
 *
 * Handles Awards functionality.
 *
 * @package Spirit_Of_Football_Awards
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Awards class.
 *
 * A class that encapsulates Awards functionality.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Awards_Loader {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football
	 */
	public $plugin;

	/**
	 * Custom Post Type object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Awards_CPT
	 */
	public $cpt;

	/**
	 * ACF object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Awards_ACF
	 */
	public $acf;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->plugin = $parent;

		// Init when this plugin is loaded.
		add_action( 'sof_awards/loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Initialises this class.
	 *
	 * @since 1.0.0
	 */
	public function initialise() {

		// Only do this once.
		static $done;
		if ( isset( $done ) && true === $done ) {
			return;
		}

		// Bootstrap object.
		$this->include_files();
		$this->setup_objects();
		$this->register_hooks();

		/**
		 * Fires when this class is loaded.
		 *
		 * @since 1.0.0
		 */
		do_action( 'sof_awards/awards/loaded' );

		// We're done.
		$done = true;

	}

	/**
	 * Includes files.
	 *
	 * @since 1.0.0
	 */
	private function include_files() {

		// Include class files.
		require SOF_AWARDS_PATH . 'includes/class-awards-cpt.php';
		require SOF_AWARDS_PATH . 'includes/class-awards-acf.php';

	}

	/**
	 * Sets up this plugin's objects.
	 *
	 * @since 1.0.0
	 */
	private function setup_objects() {

		// Init objects.
		$this->cpt = new Spirit_Of_Football_Awards_CPT( $this );
		$this->acf = new Spirit_Of_Football_Awards_ACF( $this );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {

	}

}
