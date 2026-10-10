<?php
/**
 * Block registration.
 *
 * /blocks holds the 22 developer-portfolio/* blocks (same names, attributes and defaults as
 * Developer Portfolio, so saved page content renders unchanged) and the jat/* blocks used by
 * the concept layouts. Every block is server-rendered; one shared editor script gives each
 * block its settings panel and a live server-side preview.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', static function () {
	wp_register_script(
		'jat-blocks-editor',
		JAT_URI . '/assets/js/editor.js',
		array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'wp-core-data', 'wp-i18n', 'wp-compose' ),
		JAT_VERSION,
		true
	);
	$names = array();
	foreach ( glob( JAT_DIR . '/blocks/*/block.json' ) as $file ) {
		$type = register_block_type( dirname( $file ) );
		if ( $type ) {
			$names[] = $type->name;
		}
	}
	wp_localize_script( 'jat-blocks-editor', 'JAT_EDITOR', array(
		'channels' => array_map( static fn( $c ) => array( 'id' => $c['id'], 'name' => $c['name'] ), jat_channels() ),
		'blocks'   => $names,
	) );

	// A light "band" style for section blocks, so pages can alternate ground colors.
	foreach ( array( 'jat/channels', 'jat/versus', 'jat/price-list', 'jat/steps', 'jat/founder', 'jat/check-grid', 'developer-portfolio/featured-projects', 'developer-portfolio/latest-posts', 'developer-portfolio/tech-stack', 'developer-portfolio/services-grid', 'developer-portfolio/services-pricing-table', 'developer-portfolio/services-packages', 'developer-portfolio/services-retainers', 'developer-portfolio/services-addons', 'developer-portfolio/services-comparison', 'developer-portfolio/services-faq', 'developer-portfolio/about-experience' ) as $name ) {
		register_block_style( $name, array( 'name' => 'band', 'label' => __( 'Band (ink ground)', 'jat-signal' ) ) );
	}
} );

/** Put the theme's blocks in their own inserter category. */
add_filter( 'block_categories_all', static function ( array $cats ): array {
	array_unshift( $cats, array( 'slug' => 'jat', 'title' => __( 'JAT Signal', 'jat-signal' ), 'icon' => null ) );
	return $cats;
} );

/**
 * Section wrapper used by section blocks: <section class="section …"><div class="wrap">…</div></section>.
 * Alignment, custom classes and anchors from the editor are kept via get_block_wrapper_attributes().
 */
function jat_section_open( string $class = '', string $labelledby = '', string $tag = 'section', array $extra = array() ): string {
	$attrs = array( 'class' => trim( 'section ' . $class ) );
	if ( $labelledby ) {
		$attrs['aria-labelledby'] = $labelledby;
	}
	$attrs = array_merge( $attrs, $extra );
	return '<' . tag_escape( $tag ) . ' ' . get_block_wrapper_attributes( $attrs ) . '><div class="wrap">';
}
function jat_section_close( string $tag = 'section' ): string {
	return '</div></' . tag_escape( $tag ) . '>';
}

/** Placeholder shown only in the editor when a block has nothing to show yet. */
function jat_editor_note( string $text ): string {
	if ( ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return '';
	}
	return '<div class="jat-editor-note">' . esc_html( $text ) . '</div>';
}
