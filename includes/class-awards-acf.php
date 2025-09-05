<?php
/**
 * Awards ACF Class.
 *
 * Handles ACF functionality for Awards.
 *
 * @package Spirit_Of_Football_Awards
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ACF Class.
 *
 * A class that encapsulates ACF functionality for Awards.
 *
 * @since 1.0.0
 */
class Spirit_Of_Football_Awards_ACF {

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
	public $loader;

	/**
	 * ACF Field Group prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $group_key = 'group_sof_awards_';

	/**
	 * Award ACF Field prefix.
	 *
	 * @since 1.0.0
	 * @access public
	 * @var string
	 */
	public $field_key = 'field_sof_awards_';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param Spirit_Of_Football_Awards_Loader $parent The parent object.
	 */
	public function __construct( $parent ) {

		// Store references.
		$this->loader = $parent;
		$this->plugin = $parent->plugin;

		// Init when this plugin is loaded.
		add_action( 'sof_awards/awards/loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Registers hook callbacks.
	 *
	 * @since 1.0.0
	 */
	public function register_hooks() {

		// Add Field Group and Fields.
		add_action( 'acf/init', [ $this, 'field_groups_add' ] );
		add_action( 'acf/init', [ $this, 'fields_add' ] );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds ACF Field Groups.
	 *
	 * @since 1.0.0
	 */
	public function field_groups_add() {

		// Add our ACF Fields.
		$this->field_group_awards_item_add();

	}

	/**
	 * Adds Awards Field Group.
	 *
	 * @since 1.0.0
	 */
	private function field_group_awards_item_add() {

		// Attach the Field Group to our CPT.
		$field_group_location = [
			[
				[
					'param'    => 'post_type',
					'operator' => '==',
					'value'    => $this->loader->cpt->post_type_name,
				],
			],
		];

		// Hide UI elements on our CPT edit page.
		$field_group_hide_elements = [
			'the_content',
			'excerpt',
			'discussion',
			'comments',
			// 'revisions',
			'author',
			'format',
			'page_attributes',
			// 'featured_image',
			'tags',
			'send-trackbacks',
		];

		// Define Field Group.
		$field_group = [
			'key'                   => $this->group_key . 'item',
			'title'                 => __( 'Award Details', 'sof-awards' ),
			'fields'                => [],
			'location'              => $field_group_location,
			'hide_on_screen'        => $field_group_hide_elements,
			'label_placement'       => 'left',
			'instruction_placement' => 'field',
		];

		// Now add the Field Group.
		acf_add_local_field_group( $field_group );

	}

	// -----------------------------------------------------------------------------------

	/**
	 * Adds ACF Fields.
	 *
	 * @since 1.0.0
	 */
	public function fields_add() {

		// Add our ACF Fields.
		$this->fields_item_add();

	}

	/**
	 * Adds "Award" Fields.
	 *
	 * @since 1.0.0
	 */
	private function fields_item_add() {

		// Define Field.
		$field = [
			'key'               => $this->field_key . 'image',
			'parent'            => $this->group_key . 'item',
			'label'             => __( 'Award Badge', 'sof-awards' ),
			'name'              => 'image',
			'type'              => 'image',
			'instructions'      => __( 'The "Badge" of the Award.', 'sof-awards' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'preview_size'      => 'medium',
			'acfe_thumbnail'    => 0,
			'library'           => 'all',
			'return_format'     => 'array',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'key'           => $this->field_key . 'awarded_by',
			'parent'        => $this->group_key . 'item',
			'label'         => __( 'Awarded By', 'sof-awards' ),
			'name'          => 'awarded_by',
			'type'          => 'text',
			'instructions'  => __( 'Who gave this Award? Examples: DFB, etc.', 'sof-awards' ),
			'default_value' => '',
			'placeholder'   => '',
			'required'      => 1,
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'key'               => $this->field_key . 'logo',
			'parent'            => $this->group_key . 'item',
			'label'             => __( 'Organisation Logo', 'sof-awards' ),
			'name'              => 'logo',
			'type'              => 'image',
			'instructions'      => __( 'Optional Logo of the Awarding Organisation.', 'sof-awards' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'preview_size'      => 'medium',
			'acfe_thumbnail'    => 0,
			'library'           => 'all',
			'return_format'     => 'array',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Get the current year.
		$current_year = gmdate( 'Y' );

		// Build choices.
		$choices = [];
		foreach ( range( $current_year - 20, $current_year + 1 ) as $year ) {
			$choices[ $year ] = $year;
		}

		// Define Field.
		$field = [
			'key'           => $this->field_key . 'year',
			'parent'        => $this->group_key . 'item',
			'label'         => __( 'Year', 'sof-awards' ),
			'name'          => 'year',
			'type'          => 'select',
			'instructions'  => __( 'In which year was the Award given?', 'sof-awards' ),
			'required'      => 0,
			'placeholder'   => '',
			'allow_null'    => 0,
			'multiple'      => 0,
			'ui'            => 0,
			'return_format' => 'value',
			'choices'       => $choices,
			'default_value' => $current_year,
			'wrapper'       => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'key'           => $this->field_key . 'about',
			'parent'        => $this->group_key . 'item',
			'label'         => __( 'About this Award', 'sof-awards' ),
			'name'          => 'about',
			'type'          => 'wysiwyg',
			'instructions'  => __( 'Use this field to describe the Award.', 'sof-awards' ),
			'default_value' => '',
			'placeholder'   => '',
			'wrapper'       => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'key'               => $this->field_key . 'picture',
			'parent'            => $this->group_key . 'item',
			'label'             => __( 'Award Image', 'sof-awards' ),
			'name'              => 'picture',
			'type'              => 'image',
			'instructions'      => __( 'A representative Image for the Award, e.g. receiving the Award.', 'sof-awards' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'preview_size'      => 'medium',
			'acfe_thumbnail'    => 0,
			'library'           => 'all',
			'return_format'     => 'array',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define Field.
		$field = [
			'key'               => $this->field_key . 'file',
			'parent'            => $this->group_key . 'item',
			'label'             => __( 'Award File', 'sof-awards' ),
			'name'              => 'file',
			'type'              => 'file',
			'instructions'      => __( 'Downloadable File for the Award.', 'sof-awards' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'acfe_thumbnail'    => 0,
			'return_format'     => 'array',
			'mime_types'        => '',
			'library'           => 'all',
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

		// Define "Image source" Repeater.
		$field = [
			'key'               => $this->field_key . 'file',
			'parent'            => $this->group_key . 'item',
			'label'             => __( 'Links', 'sof-awards' ),
			'name'              => 'links',
			'type'              => 'repeater',
			'instructions'      => __( 'Add any links that are relevant to this Award', 'sof-awards' ),
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'collapsed'         => '',
			'min'               => 0,
			'max'               => 0,
			'layout'            => 'table',
			'button_label'      => __( 'Add link', 'sof-awards' ),
			'sub_fields'        => [
				[
					'key'               => $this->field_key . 'link_title',
					'label'             => __( 'Link Label', 'sof-awards' ),
					'name'              => 'link_label',
					'type'              => 'text',
					'instructions'      => '',
					'required'          => 0,
					'placeholder'       => '',
					'conditional_logic' => 0,
				],
				[
					'key'               => $this->field_key . 'link',
					'label'             => __( 'Link', 'sof-awards' ),
					'name'              => 'link',
					'type'              => 'url',
					'instructions'      => '',
					'required'          => 0,
					'allow_null'        => 1,
					'placeholder'       => '',
					'conditional_logic' => 0,
				],
			],
		];

		// Now add Field.
		acf_add_local_field( $field );

	}

}
