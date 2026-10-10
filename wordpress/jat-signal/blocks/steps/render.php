<?php
/**
 * jat/steps — numbered process.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$items = jat_json( $attributes['items'] ?? '[]' );
if ( ! $items ) {
	return;
}
$hid = jat_id( 'steps' );
echo jat_section_open( '', $hid ); // phpcs:ignore
if ( '' !== trim( (string) ( $attributes['heading'] ?? '' ) ) ) {
	echo jat_section_head( (string) $attributes['heading'], (string) ( $attributes['lede'] ?? '' ), $hid ); // phpcs:ignore
} else {
	echo '<h2 class="vh" id="' . esc_attr( $hid ) . '">' . esc_html__( 'Process', 'jat-signal' ) . '</h2>';
}
echo '<ol class="steps" style="--n:' . (int) min( 6, count( $items ) ) . '">';
foreach ( $items as $s ) {
	echo '<li><h3>' . esc_html( $s['title'] ?? '' ) . '</h3>';
	if ( ! empty( $s['meta'] ) ) {
		echo '<span class="n">' . esc_html( $s['meta'] ) . '</span>';
	}
	echo '<p>' . esc_html( $s['text'] ?? '' ) . '</p>';
	if ( ! empty( $s['note'] ) ) {
		echo '<span class="readout dt">' . esc_html( $s['note'] ) . '</span>';
	}
	echo '</li>';
}
echo '</ol>';
$link = jat_link_arrow( $attributes['linkUrl'] ?? '', $attributes['linkText'] ?? '' );
if ( $link && ( '/approach/' !== ( $attributes['linkUrl'] ?? '' ) || jat_page_url( 'approach' ) ) ) {
	echo '<p class="after-table">' . $link . '</p>'; // phpcs:ignore
}
echo jat_section_close(); // phpcs:ignore
