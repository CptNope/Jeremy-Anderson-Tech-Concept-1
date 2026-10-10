<?php
/**
 * jat/price-list — chosen services (or one category) as price rows, read from the Services post type.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$cat   = trim( (string) ( $attributes['category'] ?? '' ) );
$posts = $cat ? jat_services_in( $cat ) : array();
if ( ! $posts ) {
	$posts = jat_pick_posts( 'service', $attributes['postIds'] ?? '[]', (int) ( $attributes['count'] ?? 5 ) );
}
if ( ! $posts ) {
	echo jat_editor_note( __( 'No published services yet.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$hid = jat_id( 'prices' );
echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( (string) ( $attributes['heading'] ?? '' ), (string) ( $attributes['lede'] ?? '' ), $hid ); // phpcs:ignore
echo '<div class="price-table">';
foreach ( $posts as $p ) {
	echo jat_service_row( $p, array( 'features' => ! empty( $attributes['showFeatures'] ) ) ); // phpcs:ignore
}
echo '</div>';
$link = jat_link_arrow( $attributes['linkUrl'] ?? '', $attributes['linkText'] ?? '' );
if ( $link ) {
	echo '<p class="after-table">' . $link . '</p>'; // phpcs:ignore
}
echo jat_section_close(); // phpcs:ignore
