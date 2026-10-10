<?php
/**
 * jat/check-grid — areas or standards in a ruled two-column grid; each has an outcome line,
 * optional text and an optional checklist. layout "list" renders a single checklist instead.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$items = jat_json( $attributes['items'] ?? '[]' );
if ( ! $items ) {
	return;
}
$chs = jat_channels();
$var = isset( $chs[ $attributes['channel'] ?? '' ] ) ? $chs[ $attributes['channel'] ]['var'] : '--ch1';
$hid = jat_id( 'grid' );
echo jat_section_open( '', $hid ); // phpcs:ignore

if ( 'list' === ( $attributes['layout'] ?? 'grid' ) ) {
	echo '<div class="two-col"><div><h2 id="' . esc_attr( $hid ) . '">' . esc_html( (string) ( $attributes['heading'] ?? '' ) ) . '</h2>';
	if ( ! empty( $attributes['lede'] ) ) {
		echo '<p class="lede">' . esc_html( $attributes['lede'] ) . '</p>';
	}
	echo '</div><ul class="check-list" style="--c:var(' . esc_attr( $var ) . ')">';
	foreach ( $items as $i ) {
		$text = trim( ( $i['title'] ?? '' ) . ( ! empty( $i['text'] ) ? ' ' . $i['text'] : '' ) );
		echo '<li>' . jat_icon( 'check' ) . '<span>' . esc_html( $text ) . '</span></li>'; // phpcs:ignore
	}
	echo '</ul></div>';
} else {
	echo jat_section_head( (string) ( $attributes['heading'] ?? '' ), (string) ( $attributes['lede'] ?? '' ), $hid ); // phpcs:ignore
	echo '<div class="svc-grid">';
	foreach ( $items as $i ) {
		$sid = ! empty( $i['id'] ) ? sanitize_title( $i['id'] ) : sanitize_title( $i['title'] ?? '' );
		echo '<section id="' . esc_attr( $sid ) . '" aria-labelledby="' . esc_attr( $sid ) . '-h"><h3 id="' . esc_attr( $sid ) . '-h">' . esc_html( $i['title'] ?? '' ) . '</h3>';
		if ( ! empty( $i['outcome'] ) ) {
			echo '<p class="outcome">' . esc_html( $i['outcome'] ) . '</p>';
		}
		if ( ! empty( $i['text'] ) ) {
			echo '<p>' . esc_html( $i['text'] ) . '</p>';
		}
		if ( ! empty( $i['points'] ) ) {
			echo '<ul class="check-list" style="--c:var(' . esc_attr( $var ) . ')">';
			foreach ( (array) $i['points'] as $pt ) {
				echo '<li>' . jat_icon( 'check' ) . '<span>' . esc_html( (string) $pt ) . '</span></li>'; // phpcs:ignore
			}
			echo '</ul>';
		}
		echo '</section>';
	}
	echo '</div>';
}
echo jat_section_close(); // phpcs:ignore
