<?php
/**
 * developer-portfolio/blog-listing — the Blog page: header plus post rows with pagination.
 * On the posts page it lists the main query (category, tag and date archives use their own template).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading  = jat_heading_text( $attributes['heading'] ?? 'Thoughts & Insights', $attributes['headingHighlight'] ?? 'Insights' );
$desc     = (string) ( $attributes['description'] ?? 'Writing about web development, software architecture, and the tools that make developers more productive.' );
$per_page = max( 1, (int) ( $attributes['perPage'] ?? 10 ) );
$paged    = jat_paged();
$q        = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => $per_page, 'paged' => $paged, 'ignore_sticky_posts' => false ) );

echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'listing listing-posts' ) ) . '>'; // phpcs:ignore
echo jat_page_head( array( 'title' => $heading, 'lede' => $desc, 'crumbs' => 'auto' ) ); // phpcs:ignore
echo jat_category_filter(); // phpcs:ignore
echo '<section class="section" aria-label="' . esc_attr__( 'Posts', 'jat-signal' ) . '"><div class="wrap">';
if ( $q->have_posts() ) {
	echo '<div class="post-list">';
	foreach ( $q->posts as $p ) {
		echo jat_post_row( $p ); // phpcs:ignore
	}
	echo '</div>' . jat_pagination( (int) $q->max_num_pages, $paged, __( 'Blog pages', 'jat-signal' ) ); // phpcs:ignore
} else {
	echo '<p class="muted">' . esc_html__( 'No posts found.', 'jat-signal' ) . '</p>';
}
echo '</div></section></div>';
