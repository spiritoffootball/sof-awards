<?php
/**
 * Awards Custom Post Type Class.
 *
 * Handles providing an "Awards" Custom Post Type.
 *
 * @package Spirit_Of_Football_Awards
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Custom Post Type Class.
 *
 * A class that encapsulates an "Awards" Custom Post Type.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Awards_CPT {

	/**
	 * Plugin object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Awards
	 */
	public $plugin;

	/**
	 * Awards loader.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Spirit_Of_Football_Awards_Loader
	 */
	public $coverage;

	/**
	 * Custom Post Type name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_name = 'award';

	/**
	 * Custom Post Type REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $post_type_rest_base = 'award';

	/**
	 * Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_name = 'award-type';

	/**
	 * Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_rest_base = 'award-types';

	/**
	 * Alternative Taxonomy name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_name = 'award-tag';

	/**
	 * Alternative Taxonomy REST base.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $taxonomy_alt_rest_base = 'award-tags';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_Awards_Loader $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->coverage = $parent;
		$this->plugin   = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_awards/awards/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Activation and deactivation.
		add_action( 'sof_awards/activate', [ $this, 'activate' ] );
		add_action( 'sof_awards/deactivate', [ $this, 'deactivate' ] );

		// Always create post type.
		add_action( 'init', [ $this, 'post_type_create' ] );

		// Make sure our feedback is appropriate.
		add_filter( 'post_updated_messages', [ $this, 'post_type_messages' ] );

		// Make sure our UI text is appropriate.
		add_filter( 'enter_title_here', [ $this, 'post_type_title' ] );

		// Create primary taxonomy.
		add_action( 'init', [ $this, 'taxonomy_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_filter_post_type' ] );

		// Create alternative taxonomy.
		add_action( 'init', [ $this, 'taxonomy_alt_create' ] );
		add_filter( 'wp_terms_checklist_args', [ $this, 'taxonomy_alt_fix_metabox' ], 10, 2 );
		add_action( 'restrict_manage_posts', [ $this, 'taxonomy_alt_filter_post_type' ] );

	}

	/**
	 * Actions to perform on plugin activation.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// Pass through.
		$this->post_type_create();
		$this->taxonomy_create();
		$this->taxonomy_alt_create();

		// Go ahead and flush.
		flush_rewrite_rules();

	}

	/**
	 * Actions to perform on plugin deactivation (NOT deletion).
	 *
	 * @since 1.0.0
	 */
	public function deactivate() {

		// Flush rules to reset.
		flush_rewrite_rules();

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates our Custom Post Type.
	 *
	 * @since 1.0.0
	 */
	public function post_type_create() {

		// Only call this once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Define labels.
		$labels = [
			'name'               => __( 'Awards', 'sof-awards' ),
			'singular_name'      => __( 'Award', 'sof-awards' ),
			'add_new'            => __( 'Add New', 'sof-awards' ),
			'add_new_item'       => __( 'Add New Award', 'sof-awards' ),
			'edit_item'          => __( 'Edit Award', 'sof-awards' ),
			'new_item'           => __( 'New Award', 'sof-awards' ),
			'all_items'          => __( 'All Awards', 'sof-awards' ),
			'view_item'          => __( 'View Award', 'sof-awards' ),
			'search_items'       => __( 'Search Awards', 'sof-awards' ),
			'not_found'          => __( 'No matching Award found', 'sof-awards' ),
			'not_found_in_trash' => __( 'No Awards found in Trash', 'sof-awards' ),
			'menu_name'          => __( 'Awards', 'sof-awards' ),
		];

		// Build args.
		$args = [

			'labels'              => $labels,

			// Defaults.
			'menu_icon'           => 'dashicons-awards',
			'description'         => __( 'Spirit of Football Awards', 'sof-awards' ),
			'public'              => true,
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => false,
			'query_var'           => true,
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'menu_position'       => 50,
			'map_meta_cap'        => true,
			'pages'               => false,

			// Rewrite.
			'rewrite'             => [
				'slug'       => 'awards',
				'with_front' => false,
			],

			// Supports.
			'supports'            => [
				'title',
				'thumbnail',
			],

			// REST setup.
			'show_in_rest'        => true,
			'rest_base'           => $this->post_type_rest_base,

		];

		// Set up the Custom Post Type called "Award".
		register_post_type( $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Overrides messages for a Custom Post Type.
	 *
	 * @since 1.0.0
	 *
	 * @param array $messages The existing messages.
	 * @return array $messages The modified messages.
	 */
	public function post_type_messages( $messages ) {

		// Access relevant globals.
		global $post, $post_ID;

		// Define custom messages for our Custom Post Type.
		$messages[ $this->post_type_name ] = [

			// Unused - messages start at index 1.
			0  => '',

			// Item updated.
			1  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Award updated. <a href="%s">View Award</a>', 'sof-awards' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Custom fields.
			2  => __( 'Custom field updated.', 'sof-awards' ),
			3  => __( 'Custom field deleted.', 'sof-awards' ),
			4  => __( 'Award updated.', 'sof-awards' ),

			// Item restored to a revision.
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			5  => isset( $_GET['revision'] ) ?

				// Revision text.
				sprintf(
					/* translators: %s: The date and time of the revision. */
					__( 'Award restored to revision from %s', 'sof-awards' ),
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					wp_post_revision_title( (int) $_GET['revision'], false )
				) :

				// No revision.
				false,

			// Item published.
			6  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Award published. <a href="%s">View Award</a>', 'sof-awards' ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Item saved.
			7  => __( 'Award saved.', 'sof-awards' ),

			// Item submitted.
			8  => sprintf(
				/* translators: %s: The permalink. */
				__( 'Award submitted. <a target="_blank" href="%s">Preview Award</a>', 'sof-awards' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

			// Item scheduled.
			9  => sprintf(
				/* translators: 1: The date, 2: The permalink. */
				__( 'Award scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Award</a>', 'sof-awards' ),
				/* translators: Publish box date format - see https://php.net/date */
				date_i18n( __( 'M j, Y @ G:i', 'sof-awards' ), strtotime( $post->post_date ) ),
				esc_url( get_permalink( $post_ID ) )
			),

			// Draft updated.
			10 => sprintf(
				/* translators: %s: The permalink. */
				__( 'Award draft updated. <a target="_blank" href="%s">Preview Award</a>', 'sof-awards' ),
				esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) )
			),

		];

		// --<
		return $messages;

	}

	/**
	 * Overrides the "Add title" label.
	 *
	 * @since 1.0.0
	 *
	 * @param string $title The existing title - usually "Add title".
	 * @return string $title The modified title.
	 */
	public function post_type_title( $title ) {

		// Bail if not our post type.
		if ( get_post_type() !== $this->post_type_name ) {
			return $title;
		}

		// Overwrite with our string.
		$title = __( 'Add an identifying name for the Award', 'sof-awards' );

		// --<
		return $title;

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates our Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical'      => true,

			// Labels.
			'labels'            => [
				'name'              => _x( 'Award Types', 'taxonomy general name', 'sof-awards' ),
				'singular_name'     => _x( 'Award Type', 'taxonomy singular name', 'sof-awards' ),
				'search_items'      => __( 'Search Award Types', 'sof-awards' ),
				'all_items'         => __( 'All Award Types', 'sof-awards' ),
				'parent_item'       => __( 'Parent Award Type', 'sof-awards' ),
				'parent_item_colon' => __( 'Parent Award Type:', 'sof-awards' ),
				'edit_item'         => __( 'Edit Award Type', 'sof-awards' ),
				'update_item'       => __( 'Update Award Type', 'sof-awards' ),
				'add_new_item'      => __( 'Add New Award Type', 'sof-awards' ),
				'new_item_name'     => __( 'New Award Type Name', 'sof-awards' ),
				'menu_name'         => __( 'Award Types', 'sof-awards' ),
				'not_found'         => __( 'No Award Types found', 'sof-awards' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'award-types',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fixes the Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress Post ID.
	 */
	public function taxonomy_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for this Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-awards' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_name,
			'name'            => $this->taxonomy_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Creates our alternative Custom Taxonomy.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_alt_create() {

		// Only register once.
		static $registered;
		if ( $registered ) {
			return;
		}

		// Arguments.
		$args = [

			// Same as "category".
			'hierarchical'      => true,

			// Labels.
			'labels'            => [
				'name'              => _x( 'Award Tags', 'taxonomy general name', 'sof-awards' ),
				'singular_name'     => _x( 'Award Tag', 'taxonomy singular name', 'sof-awards' ),
				'search_items'      => __( 'Search Award Tags', 'sof-awards' ),
				'all_items'         => __( 'All Award Tags', 'sof-awards' ),
				'parent_item'       => __( 'Parent Award Tag', 'sof-awards' ),
				'parent_item_colon' => __( 'Parent Award Tag:', 'sof-awards' ),
				'edit_item'         => __( 'Edit Award Tag', 'sof-awards' ),
				'update_item'       => __( 'Update Award Tag', 'sof-awards' ),
				'add_new_item'      => __( 'Add New Award Tag', 'sof-awards' ),
				'new_item_name'     => __( 'New Award Tag Name', 'sof-awards' ),
				'menu_name'         => __( 'Award Tags', 'sof-awards' ),
				'not_found'         => __( 'No Award Tags found', 'sof-awards' ),
			],

			// Rewrite rules.
			'rewrite'           => [
				'slug' => 'award-tags',
			],

			// Show column in wp-admin.
			'show_admin_column' => true,
			'show_ui'           => true,

			// REST setup.
			'show_in_rest'      => true,
			'rest_base'         => $this->taxonomy_alt_rest_base,

		];

		// Register a taxonomy for this CPT.
		register_taxonomy( $this->taxonomy_alt_name, $this->post_type_name, $args );

		// Flag done.
		$registered = true;

	}

	/**
	 * Fixes the alternative Custom Taxonomy metabox.
	 *
	 * @see https://core.trac.wordpress.org/ticket/10982
	 *
	 * @since 1.0.0
	 *
	 * @param array $args The existing arguments.
	 * @param int   $post_id The WordPress Post ID.
	 */
	public function taxonomy_alt_fix_metabox( $args, $post_id ) {

		// If rendering metabox for our taxonomy.
		if ( isset( $args['taxonomy'] ) && $args['taxonomy'] === $this->taxonomy_alt_name ) {

			// Setting 'checked_ontop' to false seems to fix this.
			$args['checked_ontop'] = false;

		}

		// --<
		return $args;

	}

	/**
	 * Adds a filter for the alternative Custom Taxonomy to the Custom Post Type listing.
	 *
	 * @since 1.0.0
	 */
	public function taxonomy_alt_filter_post_type() {

		// Access current post type.
		global $typenow;

		// Bail if not our post type.
		if ( $typenow !== $this->post_type_name ) {
			return;
		}

		// Get tax object.
		$taxonomy = get_taxonomy( $this->taxonomy_alt_name );

		// Build args.
		$args = [
			/* translators: %s: The plural name of the taxonomy terms. */
			'show_option_all' => sprintf( __( 'Show All %s', 'sof-awards' ), $taxonomy->label ),
			'taxonomy'        => $this->taxonomy_alt_name,
			'name'            => $this->taxonomy_alt_name,
			'orderby'         => 'name',
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
			'selected'        => isset( $_GET[ $this->taxonomy_alt_name ] ) ? wp_unslash( $_GET[ $this->taxonomy_alt_name ] ) : '',
			'show_count'      => true,
			'hide_empty'      => true,
			'value_field'     => 'slug',
			'hierarchical'    => 1,
		];

		// Show a dropdown.
		wp_dropdown_categories( $args );

	}

}
