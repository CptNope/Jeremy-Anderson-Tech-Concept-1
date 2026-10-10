<?php
/**
 * Behaviour carried over from Developer Portfolio, re-expressed in the new markup.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/** Meta keys the single project / service templates already show in their header or summary card. */
function jat_template_meta_keys(): array {
	return array(
		'project' => array( 'project_subtitle', 'project_github_url', 'project_live_url' ),
		'service' => array( 'service_subtitle', 'service_tier', 'service_price', 'service_price_note', 'service_cta_text', 'service_cta_url', 'service_price_range' ),
	);
}

/** Meta key a block is bound to through the Block Bindings API, if any. */
function jat_bound_meta_key( array $block ): string {
	$bindings = $block['attrs']['metadata']['bindings'] ?? array();
	foreach ( $bindings as $binding ) {
		if ( ( $binding['source'] ?? '' ) === 'core/post-meta' && ! empty( $binding['args']['key'] ) ) {
			return (string) $binding['args']['key'];
		}
	}
	return '';
}

add_filter( 'render_block', static function ( $content, array $block ) {
	$name = $block['blockName'] ?? '';
	if ( ! $name ) {
		return $content;
	}
	$key = jat_bound_meta_key( $block );

	// On a single project or service, the template already shows the bound fields; don't repeat them in the body.
	if ( $key && is_singular( array( 'project', 'service' ) ) && get_the_ID() === get_queried_object_id() ) {
		$type = get_post_type();
		if ( in_array( $key, jat_template_meta_keys()[ $type ] ?? array(), true ) ) {
			return '';
		}
	}

	// Buttons bound to an empty meta URL are hidden; bound links open in a new tab (as before).
	if ( 'core/button' === $name && isset( $block['attrs']['metadata']['bindings']['url'] ) ) {
		$url_key = $block['attrs']['metadata']['bindings']['url']['args']['key'] ?? '';
		if ( $url_key && '' === trim( (string) get_post_meta( get_the_ID(), $url_key, true ) ) ) {
			return '';
		}
		if ( false === strpos( $content, 'target=' ) && false === strpos( $content, 'href="mailto:' ) ) {
			$content = str_replace( '<a ', '<a target="_blank" rel="noopener noreferrer" ', $content );
		}
	}

	// Project buttons groups with no links left show the proprietary note instead.
	if ( 'core/buttons' === $name && 'project' === get_post_type() ) {
		$bound = false;
		foreach ( $block['innerBlocks'] ?? array() as $inner ) {
			if ( isset( $inner['attrs']['metadata']['bindings']['url'] ) ) {
				$bound = true;
				break;
			}
		}
		if ( $bound && ! jat_project_is_open( get_the_ID() ) ) {
			return '<p class="readout">' . esc_html__( 'Proprietary software', 'jat-signal' ) . '</p>';
		}
	}

	// Old template groups that expected links to be injected (project-card, single-project-links).
	if ( 'core/group' === $name && 'project' === get_post_type() ) {
		$class = $block['attrs']['className'] ?? '';
		if ( false !== strpos( $class, 'single-project-links' ) || ( false !== strpos( $class, 'project-card' ) && false === strpos( $content, 'wp-block-buttons' ) ) ) {
			$links = jat_project_links( get_the_ID() );
			if ( false !== strpos( $class, 'single-project-links' ) ) {
				return $links;
			}
			$content = preg_replace( '/<\/div>\s*$/', $links . '</div>', $content, 1 );
		}
	}

	// Tech stack placeholder group (pre-block migration markup).
	if ( 'core/group' === $name && false !== strpos( $block['attrs']['className'] ?? '', 'tech-stack-cards' ) ) {
		$html = jat_tech_stack_grid( get_the_ID() );
		if ( $html ) {
			return $html;
		}
	}

	return $content;
}, 10, 2 );

/** {year} in any block becomes the current year. */
add_filter( 'render_block', static function ( $content ) {
	return false !== strpos( (string) $content, '{year}' ) ? str_replace( '{year}', gmdate( 'Y' ), $content ) : $content;
} );

/** Live / GitHub links for a project, or the proprietary note. */
function jat_project_links( int $post_id ): string {
	$live = trim( (string) get_post_meta( $post_id, 'project_live_url', true ) );
	$gh   = trim( (string) get_post_meta( $post_id, 'project_github_url', true ) );
	if ( ! $live && ! $gh ) {
		return '<p class="readout">' . esc_html__( 'Proprietary software', 'jat-signal' ) . '</p>';
	}
	$out = '<nav class="links-row" aria-label="' . esc_attr__( 'Project links', 'jat-signal' ) . '">';
	if ( $live ) {
		$out .= jat_link_arrow( $live, __( 'Open live app', 'jat-signal' ) );
	}
	if ( $gh ) {
		$out .= jat_link_arrow( $gh, __( 'Read the source', 'jat-signal' ) );
	}
	return $out . '</nav>';
}

/** Tech stack categories from page meta (tech_stack_data), as instrument rows. */
function jat_tech_stack_grid( int $post_id ): string {
	$cats = jat_json( get_post_meta( $post_id, 'tech_stack_data', true ) );
	if ( ! $cats ) {
		return '';
	}
	// Old color keys map onto the channel palette; rose maps to the danger red.
	$map = array( 'violet' => '--ch2', 'cyan' => '--ch1', 'emerald' => '--ch4', 'amber' => '--ch3', 'rose' => '--danger' );
	$out = '<div class="stack-grid">';
	foreach ( $cats as $cat ) {
		$name   = (string) ( $cat['name'] ?? '' );
		$var    = $map[ $cat['color'] ?? '' ] ?? '--ch1';
		$badges = array_filter( array_map( 'strval', (array) ( $cat['badges'] ?? array() ) ) );
		$out   .= '<section class="stack-row" style="--c:var(' . esc_attr( $var ) . ')" aria-label="' . esc_attr( $name ) . '">';
		$out   .= '<h3 class="stack-name"><i aria-hidden="true"></i>' . esc_html( $name ) . '</h3>';
		$out   .= '<ul class="chips">';
		foreach ( $badges as $b ) {
			$out .= '<li>' . esc_html( $b ) . '</li>';
		}
		$out .= '</ul></section>';
	}
	return $out . '</div>';
}

/** Archive titles without the "Category:" prefix. */
add_filter( 'get_the_archive_title', static function ( $title ) {
	if ( is_category() ) {
		return single_cat_title( '', false );
	}
	if ( is_tag() ) {
		return single_tag_title( '', false );
	}
	if ( is_tax() ) {
		return single_term_title( '', false );
	}
	if ( is_author() ) {
		return get_the_author();
	}
	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}
	return $title;
} );

/**
 * [faq question="…"]Answer[/faq] — accessible disclosure, same shortcode as Developer Portfolio.
 * FAQPage structured data for it is printed by inc/seo.php.
 */
add_shortcode( 'faq', static function ( $atts, $content = '' ) {
	$atts = shortcode_atts( array( 'question' => '' ), $atts, 'faq' );
	if ( '' === $atts['question'] || '' === trim( (string) $content ) ) {
		return '';
	}
	$id = 'faq-' . sanitize_title( $atts['question'] );
	return '<div class="faq faq-inline"><details class="faq-item" id="' . esc_attr( $id ) . '"><summary>' . esc_html( $atts['question'] ) . jat_icon( 'plus' ) . '</summary><div class="faq-a">' . wp_kses_post( wpautop( $content ) ) . '</div></details></div>';
} );

/** Shortcodes wrapped in a paragraph would nest block elements in <p>; unwrap the FAQ. */
add_filter( 'the_content', static function ( $content ) {
	return preg_replace( '#<p>\s*(<div class="faq faq-inline">.*?</div></details></div>)\s*</p>#s', '$1', $content );
}, 12 );
