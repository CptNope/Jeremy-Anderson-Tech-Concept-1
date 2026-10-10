<?php
/**
 * Content model, carried over unchanged from Developer Portfolio so existing content keeps working:
 * post types `project` and `service`, taxonomies `project_tag`, `project_category`, `service_category`,
 * their rewrite slugs, editor templates (with meta bindings) and every meta key with its sanitizer.
 *
 * Do not rename anything in this file: URLs, saved meta and the editor sidebars depend on these keys.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Projects custom post type.
 */
function jat_register_post_types() {
	register_post_type( 'project', array(
		'labels'       => array(
			'name'               => __( 'Projects', 'jat-signal' ),
			'singular_name'      => __( 'Project', 'jat-signal' ),
			'add_new'            => __( 'Add New Project', 'jat-signal' ),
			'add_new_item'       => __( 'Add New Project', 'jat-signal' ),
			'edit_item'          => __( 'Edit Project', 'jat-signal' ),
			'new_item'           => __( 'New Project', 'jat-signal' ),
			'view_item'          => __( 'View Project', 'jat-signal' ),
			'search_items'       => __( 'Search Projects', 'jat-signal' ),
			'not_found'          => __( 'No projects found.', 'jat-signal' ),
			'not_found_in_trash' => __( 'No projects found in Trash.', 'jat-signal' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-portfolio',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'      => array( 'slug' => 'project' ),
		'template'     => array(
			array( 'core/paragraph', array(
				'placeholder' => 'Project subtitle (e.g. AI-Assisted Training Platform)',
				'style'       => array(
					'typography' => array( 'fontSize' => '1.1rem' ),
					'color'      => array( 'text' => 'var:preset|color|violet-400' ),
				),
				'metadata'    => array(
					'bindings' => array(
						'content' => array(
							'source' => 'core/post-meta',
							'args'   => array( 'key' => 'project_subtitle' ),
						),
					),
				),
			) ),
			array( 'core/separator', array() ),
			array( 'core/paragraph', array(
				'placeholder' => 'Write the project description...',
			) ),
		),
	) );

	register_taxonomy( 'project_tag', 'project', array(
		'labels'       => array(
			'name'          => __( 'Project Tags', 'jat-signal' ),
			'singular_name' => __( 'Project Tag', 'jat-signal' ),
		),
		'public'       => true,
		'hierarchical' => false,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-tag' ),
	) );

	register_taxonomy( 'project_category', 'project', array(
		'labels'       => array(
			'name'          => __( 'Project Categories', 'jat-signal' ),
			'singular_name' => __( 'Project Category', 'jat-signal' ),
		),
		'public'       => true,
		'hierarchical' => true,
		'show_in_rest' => true,
		'rewrite'      => array( 'slug' => 'project-category' ),
	) );
	register_post_type( 'service', array(
		'labels'       => array(
			'name'               => __( 'Services', 'jat-signal' ),
			'singular_name'      => __( 'Service', 'jat-signal' ),
			'add_new'            => __( 'Add New Service', 'jat-signal' ),
			'add_new_item'       => __( 'Add New Service', 'jat-signal' ),
			'edit_item'          => __( 'Edit Service', 'jat-signal' ),
			'new_item'           => __( 'New Service', 'jat-signal' ),
			'view_item'          => __( 'View Service', 'jat-signal' ),
			'search_items'       => __( 'Search Services', 'jat-signal' ),
			'not_found'          => __( 'No services found.', 'jat-signal' ),
			'not_found_in_trash' => __( 'No services found in Trash.', 'jat-signal' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-admin-tools',
		'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
		'rewrite'      => array( 'slug' => 'service' ),
		'template'     => array(
			array( 'core/group', array(
				'style'  => array( 'spacing' => array( 'blockGap' => '0.25rem' ) ),
				'layout' => array( 'type' => 'flex', 'orientation' => 'vertical' ),
			), array(
				array( 'core/paragraph', array(
					'placeholder' => 'Subtitle (e.g. Custom WordPress Development)',
					'style'       => array( 'typography' => array( 'fontSize' => '0.85rem' ) ),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_subtitle' ),
							),
						),
					),
				) ),
				array( 'core/paragraph', array(
					'placeholder' => 'Tier (e.g. STARTER, PROFESSIONAL)',
					'style'       => array(
						'typography' => array( 'fontSize' => '0.75rem', 'textTransform' => 'uppercase', 'letterSpacing' => '0.06em' ),
						'color'      => array( 'text' => '#64748b' ),
					),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_tier' ),
							),
						),
					),
				) ),
			) ),
			array( 'core/group', array(
				'style'  => array( 'spacing' => array( 'blockGap' => '0.15rem', 'margin' => array( 'top' => '1rem' ) ) ),
				'layout' => array( 'type' => 'flex', 'orientation' => 'vertical' ),
			), array(
				array( 'core/heading', array(
					'level'       => 3,
					'placeholder' => 'Price (e.g. $115/hr or $3,500)',
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_price' ),
							),
						),
					),
				) ),
				array( 'core/paragraph', array(
					'placeholder' => 'Price note (e.g. per hour, flat fee · 3–4 weeks)',
					'style'       => array( 'color' => array( 'text' => '#64748b' ), 'typography' => array( 'fontSize' => '0.8rem' ) ),
					'metadata'    => array(
						'bindings' => array(
							'content' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_price_note' ),
							),
						),
					),
				) ),
			) ),
			array( 'core/separator', array() ),
			array( 'core/paragraph', array(
				'placeholder' => 'Service description — visible in the excerpt and package cards...',
			) ),
			array( 'core/buttons', array(
				'style' => array( 'spacing' => array( 'margin' => array( 'top' => '1rem' ) ) ),
			), array(
				array( 'core/button', array(
					'placeholder' => 'CTA Button (e.g. Get a Quote →)',
					'metadata'    => array(
						'bindings' => array(
							'text' => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_cta_text' ),
							),
							'url'  => array(
								'source' => 'core/post-meta',
								'args'   => array( 'key' => 'service_cta_url' ),
							),
						),
					),
				) ),
			) ),
		),
	) );

	register_taxonomy( 'service_category', 'service', array(
		'labels'            => array(
			'name'          => __( 'Service Categories', 'jat-signal' ),
			'singular_name' => __( 'Service Category', 'jat-signal' ),
		),
		'public'            => true,
		'hierarchical'      => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'rewrite'           => array( 'slug' => 'service-category' ),
	) );
}
add_action( 'init', 'jat_register_post_types' );

/**
 * Register custom meta fields for Services.
 */
function jat_register_service_meta() {
	$meta_fields = array(
		'service_subtitle'       => 'string',
		'service_price_range'    => 'string',
		'service_cta_url'        => 'string',
		'service_price'          => 'string',
		'service_price_note'     => 'string',
		'service_tier'           => 'string',
		'service_features'       => 'string',
		'service_icon'           => 'string',
		'service_badge_type'     => 'string',
		'service_featured'       => 'string',
		'service_cta_text'       => 'string',
		'service_typical_uses'   => 'string',
		'service_min_hours'      => 'string',
		'service_hours_included' => 'string',
		'service_color_bar'      => 'string',
		'service_sort_order'     => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'service', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => function ( $value ) use ( $key ) {
				$value = wp_strip_all_tags( $value );
				if ( strpos( $key, '_url' ) !== false && $value ) {
					$value = esc_url_raw( $value );
				}
				return $value;
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'jat_register_service_meta' );

/**
 * Register custom meta fields for Projects.
 */
function jat_register_project_meta() {
	$meta_fields = array(
		'project_subtitle'  => 'string',
		'project_github_url' => 'string',
		'project_live_url'   => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'project', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => function ( $value ) use ( $key ) {
				$value = wp_strip_all_tags( $value );
				if ( strpos( $key, '_url' ) !== false && $value ) {
					$value = esc_url_raw( $value );
				}
				return $value;
			},
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'jat_register_project_meta' );

/**
 * Register custom meta fields for Blog Posts.
 */
function jat_register_post_meta_fields() {
	$meta_fields = array(
		'post_subtitle' => 'string',
	);

	foreach ( $meta_fields as $key => $type ) {
		register_post_meta( 'post', $key, array(
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => $type,
			'default'           => '',
			'sanitize_callback' => 'wp_strip_all_tags',
			'auth_callback'     => function () {
				return current_user_can( 'edit_posts' );
			},
		) );
	}
}
add_action( 'init', 'jat_register_post_meta_fields' );

/**
 * Register tech stack meta field for pages.
 */
function jat_register_page_meta() {
	register_post_meta( 'page', 'tech_stack_data', array(
		'show_in_rest'      => true,
		'single'            => true,
		'type'              => 'string',
		'default'           => '',
		'sanitize_callback' => function ( $value ) {
			$decoded = json_decode( $value, true );
			if ( ! is_array( $decoded ) ) {
				return '';
			}
			return wp_json_encode( $decoded );
		},
		'auth_callback'     => function () {
			return current_user_can( 'edit_pages' );
		},
	) );
}
add_action( 'init', 'jat_register_page_meta' );

/** Flush rewrite rules once after switching to this theme so /project/ and /service/ resolve. */
add_action( 'after_switch_theme', static function () {
	jat_register_post_types();
	flush_rewrite_rules();
} );
