<?php
/**
 * developer-portfolio/projects-listing — the Projects page: header plus work cards.
 *
 * With splitProprietary on (default), projects with a public repository or live link come first,
 * then projects without links under their own heading (as on the live site).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = jat_heading_text( $attributes['heading'] ?? 'All Projects', $attributes['headingHighlight'] ?? 'Projects' );
$desc    = (string) ( $attributes['description'] ?? "A collection of applications, tools, and experiments I've built." );
$cols    = max( 1, min( 4, (int) ( $attributes['columns'] ?? 3 ) ) );
$split   = ! isset( $attributes['splitProprietary'] ) || $attributes['splitProprietary'];

echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'listing listing-projects' ) ) . '>'; // phpcs:ignore
echo jat_page_head( array( 'title' => $heading, 'lede' => $desc, 'crumbs' => is_front_page() ? array() : 'auto' ) ); // phpcs:ignore

if ( $split ) {
	$all  = get_posts( array( 'post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => 100, 'orderby' => 'date', 'order' => 'DESC' ) );
	$open = array_values( array_filter( $all, static fn( $p ) => jat_project_is_open( $p->ID ) ) );
	$priv = array_values( array_filter( $all, static fn( $p ) => ! jat_project_is_open( $p->ID ) ) );
	if ( ! $all ) {
		echo '<section class="section"><div class="wrap"><p class="muted">' . esc_html__( 'No projects yet. Check back soon.', 'jat-signal' ) . '</p></div></section>';
	}
	if ( $open ) {
		echo '<section class="section" aria-label="' . esc_attr__( 'Public projects', 'jat-signal' ) . '"><div class="wrap"><div class="work-grid" style="--cols:' . (int) $cols . '">';
		foreach ( $open as $p ) {
			echo jat_project_card( $p, 2 ); // phpcs:ignore
		}
		echo '</div></div></section>';
	}
	if ( $priv ) {
		$pid = jat_id( 'private' );
		echo '<section class="section band" aria-labelledby="' . esc_attr( $pid ) . '"><div class="wrap">';
		echo jat_section_head( (string) ( $attributes['proprietaryHeading'] ?? 'Proprietary systems' ), (string) ( $attributes['proprietaryDescription'] ?? '' ), $pid ); // phpcs:ignore
		echo '<div class="work-grid compact" style="--cols:' . (int) $cols . '">';
		foreach ( $priv as $p ) {
			echo jat_project_card( $p, 3 ); // phpcs:ignore
		}
		echo '</div></div></section>';
	}
} else {
	$paged = jat_paged();
	$q     = new WP_Query( array( 'post_type' => 'project', 'post_status' => 'publish', 'posts_per_page' => max( 1, (int) ( $attributes['perPage'] ?? 9 ) ), 'paged' => $paged ) );
	echo '<section class="section" aria-label="' . esc_attr__( 'Projects', 'jat-signal' ) . '"><div class="wrap">';
	if ( $q->have_posts() ) {
		echo '<div class="work-grid" style="--cols:' . (int) $cols . '">';
		foreach ( $q->posts as $p ) {
			echo jat_project_card( $p, 2 ); // phpcs:ignore
		}
		echo '</div>' . jat_pagination( (int) $q->max_num_pages, $paged, __( 'Projects pages', 'jat-signal' ) ); // phpcs:ignore
	} else {
		echo '<p class="muted">' . esc_html__( 'No projects yet. Check back soon.', 'jat-signal' ) . '</p>';
	}
	echo '</div></section>';
}
echo '</div>';
