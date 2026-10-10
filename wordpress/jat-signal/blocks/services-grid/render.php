<?php
/**
 * developer-portfolio/services-grid — selected services as price rows (name, channel, price, timing).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading  = jat_heading_text( $attributes['heading'] ?? 'What I Offer', $attributes['headingHighlight'] ?? 'Offer' );
$desc     = (string) ( $attributes['description'] ?? 'Professional development services tailored to your needs.' );
$posts    = jat_pick_posts( 'service', $attributes['postIds'] ?? '[]', (int) ( $attributes['count'] ?? 3 ) );
$hid      = jat_id( 'services' );

if ( ! $posts ) {
	echo jat_editor_note( __( 'No published services yet.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( $heading, $desc, $hid ); // phpcs:ignore
echo '<div class="price-table">';
foreach ( $posts as $p ) {
	echo jat_service_row( $p, array( 'features' => false ) ); // phpcs:ignore
}
echo '</div>';
$link = jat_link_arrow( $attributes['linkUrl'] ?? '/services', $attributes['linkText'] ?? 'View all services →' );
if ( $link ) {
	echo '<p class="after-table">' . $link . '</p>'; // phpcs:ignore
}
echo jat_section_close(); // phpcs:ignore
