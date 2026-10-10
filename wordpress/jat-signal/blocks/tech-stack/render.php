<?php
/**
 * developer-portfolio/tech-stack — technology categories from the page's tech_stack_data meta
 * (edited from the block's settings panel). Old color keys map onto the channel palette.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$post_id = (int) ( $block->context['postId'] ?? get_the_ID() );
$grid    = jat_tech_stack_grid( $post_id );
if ( ! $grid ) {
	echo jat_editor_note( __( 'Tech stack: add categories in the block settings (they are stored on this page).', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$hid = jat_id( 'stack' );
echo jat_section_open( 'stack-section', $hid ); // phpcs:ignore
echo jat_section_head( (string) ( $attributes['heading'] ?? 'Technologies I Work With' ), (string) ( $attributes['description'] ?? '' ), $hid ); // phpcs:ignore
echo $grid; // phpcs:ignore -- escaped in jat_tech_stack_grid().
echo jat_section_close(); // phpcs:ignore
