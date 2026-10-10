<?php
/**
 * jat/archive-list — the main query of an archive, search or posts index, with pagination.
 * Projects show as work cards, services as price rows, everything else as post rows.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;
$query = $wp_query;
if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) { // Editor preview: show recent posts.
	$query = new WP_Query( array( 'post_type' => 'post', 'posts_per_page' => 4 ) );
}
$layout = (string) ( $attributes['layout'] ?? 'auto' );
if ( 'auto' === $layout ) {
	if ( is_post_type_archive( 'project' ) || is_tax( array( 'project_category', 'project_tag' ) ) ) {
		$layout = 'projects';
	} elseif ( is_post_type_archive( 'service' ) || is_tax( 'service_category' ) ) {
		$layout = 'services';
	} else {
		$layout = 'posts';
	}
}
echo '<section ' . get_block_wrapper_attributes( array( 'class' => 'section', 'aria-label' => __( 'Results', 'jat-signal' ) ) ) . '><div class="wrap">'; // phpcs:ignore
if ( $query->have_posts() ) {
	$open  = array( 'projects' => '<div class="work-grid">', 'services' => '<div class="price-table">', 'posts' => '<div class="post-list">' );
	echo $open[ $layout ] ?? $open['posts']; // phpcs:ignore
	foreach ( $query->posts as $p ) {
		if ( 'projects' === $layout ) {
			echo jat_project_card( $p, 2 ); // phpcs:ignore
		} elseif ( 'services' === $layout ) {
			echo jat_service_row( $p, array( 'heading' => 'h2' ) ); // phpcs:ignore
		} else {
			echo jat_post_row( $p ); // phpcs:ignore
		}
	}
	echo '</div>';
	echo jat_pagination( (int) $query->max_num_pages, max( 1, (int) get_query_var( 'paged' ) ), __( 'More results', 'jat-signal' ) ); // phpcs:ignore
} else {
	echo '<div class="empty"><p class="lede">' . esc_html( (string) ( $attributes['emptyText'] ?? __( 'Nothing here yet.', 'jat-signal' ) ) ) . '</p>' . get_search_form( array( 'echo' => false ) ) . '</div>'; // phpcs:ignore
}
echo '</div></section>';
