<?php
/**
 * developer-portfolio/services-hero — page head for the Services page.
 * heading + headingHighlight → H1, subtitle → lede, chips → facts row, eyebrow → readout under the lede.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$title = jat_heading_text( $attributes['heading'] ?? 'Services &', $attributes['headingHighlight'] ?? 'Pricing Guide', true );
$chips = array_filter( array_map( 'strval', jat_json( $attributes['chips'] ?? '[]' ) ) );
$extra = '';
if ( $chips ) {
	$extra .= '<ul class="chips chips-lg" aria-label="' . esc_attr__( 'At a glance', 'jat-signal' ) . '">';
	foreach ( $chips as $c ) {
		$extra .= '<li>' . esc_html( $c ) . '</li>';
	}
	$extra .= '</ul>';
}
$eyebrow = trim( (string) ( $attributes['eyebrow'] ?? '' ) );
if ( $eyebrow ) {
	$extra .= jat_readout( $eyebrow, '', 'p' );
}
echo jat_page_head( array( // phpcs:ignore
	'title'   => $title,
	'lede'    => (string) ( $attributes['subtitle'] ?? '' ),
	'crumbs'  => 'auto',
	'extra'   => $extra,
	'wrapper' => get_block_wrapper_attributes( array( 'class' => 'page-head' ) ),
) );
