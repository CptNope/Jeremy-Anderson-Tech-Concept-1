<?php
/**
 * developer-portfolio/latest-posts — recent (or chosen, or related) blog posts.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$heading = jat_heading_text( $attributes['heading'] ?? 'From the Blog', $attributes['headingHighlight'] ?? 'Blog' );
$desc    = (string) ( $attributes['description'] ?? '' );
$count   = max( 1, (int) ( $attributes['count'] ?? 3 ) );

if ( ! empty( $attributes['related'] ) ) {
	$current = (int) ( $block->context['postId'] ?? get_the_ID() );
	$cats    = wp_get_post_categories( $current );
	$posts   = get_posts( array( 'post_type' => 'post', 'posts_per_page' => $count, 'post__not_in' => array( $current ), 'category__in' => $cats, 'ignore_sticky_posts' => true ) );
	if ( count( $posts ) < $count ) {
		$posts = array_merge( $posts, get_posts( array( 'post_type' => 'post', 'posts_per_page' => $count - count( $posts ), 'post__not_in' => array_merge( array( $current ), wp_list_pluck( $posts, 'ID' ) ), 'ignore_sticky_posts' => true ) ) );
	}
} else {
	$posts = jat_pick_posts( 'post', $attributes['postIds'] ?? '[]', $count );
}

if ( ! $posts ) {
	echo jat_editor_note( __( 'No published posts yet.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$hid = jat_id( 'posts' );
echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( $heading, $desc, $hid, $attributes['linkUrl'] ?? '/blog', $attributes['linkText'] ?? 'Read all posts →' ); // phpcs:ignore
echo '<div class="posts">';
foreach ( $posts as $p ) {
	echo jat_post_card( $p ); // phpcs:ignore
}
echo '</div>';
echo jat_section_close(); // phpcs:ignore
