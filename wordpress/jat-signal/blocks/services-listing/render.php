<?php
/**
 * developer-portfolio/services-listing — every service as a price row, with pagination.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = jat_heading_text( $attributes['heading'] ?? 'What I Offer', $attributes['headingHighlight'] ?? 'Offer' );
$desc    = (string) ( $attributes['description'] ?? 'Professional development services tailored to your needs.' );
$paged   = jat_paged();
$q       = new WP_Query( array( 'post_type' => 'service', 'post_status' => 'publish', 'posts_per_page' => max( 1, (int) ( $attributes['perPage'] ?? 9 ) ), 'paged' => $paged ) );

echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'listing listing-services' ) ) . '>'; // phpcs:ignore
echo jat_page_head( array( 'title' => $heading, 'lede' => $desc, 'crumbs' => 'auto' ) ); // phpcs:ignore
echo '<section class="section" aria-label="' . esc_attr__( 'Services', 'jat-signal' ) . '"><div class="wrap">';
if ( $q->have_posts() ) {
	echo '<div class="price-table">';
	foreach ( $q->posts as $p ) {
		echo jat_service_row( $p, array( 'heading' => 'h2' ) ); // phpcs:ignore
	}
	echo '</div>' . jat_pagination( (int) $q->max_num_pages, $paged, __( 'Services pages', 'jat-signal' ) ); // phpcs:ignore
} else {
	echo '<p class="muted">' . esc_html__( 'No services yet. Check back soon.', 'jat-signal' ) . '</p>';
}
echo '</div></section></div>';
