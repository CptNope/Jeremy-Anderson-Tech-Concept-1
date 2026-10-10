<?php
/**
 * jat/post-nav — previous / next entry of the same type, as two cards.
 *
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_singular() ) {
	echo jat_editor_note( __( 'Previous and next links appear on single entries.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$prev = get_adjacent_post( false, '', true );
$next = get_adjacent_post( false, '', false );
if ( ! $prev && ! $next ) {
	return;
}
$card = static function ( $p, string $label, string $rel ): string {
	if ( ! $p ) {
		return '<span></span>';
	}
	return '<a class="pn pn-' . esc_attr( $rel ) . '" rel="' . esc_attr( $rel ) . '" href="' . esc_url( get_permalink( $p ) ) . '"><span class="readout">' . esc_html( $label ) . '</span><span class="t-item">' . esc_html( get_the_title( $p ) ) . '</span></a>';
};
?>
<nav <?php echo get_block_wrapper_attributes( array( 'class' => 'post-nav', 'aria-label' => __( 'More entries', 'jat-signal' ) ) ); ?>>
	<div class="wrap">
		<?php echo $card( $prev, __( '← Previous', 'jat-signal' ), 'prev' ) . $card( $next, __( 'Next →', 'jat-signal' ), 'next' ); // phpcs:ignore ?>
	</div>
</nav>
