<?php
/**
 * jat/service-summary — sticky price card on a single service, from the service's own meta.
 *
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$pid = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $pid || 'service' !== get_post_type( $pid ) ) {
	echo jat_editor_note( __( 'The price card appears on single service pages.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$m        = static fn( $k ) => trim( (string) get_post_meta( $pid, $k, true ) );
$price    = $m( 'service_price' ) ? $m( 'service_price' ) : $m( 'service_price_range' );
$features = jat_json( get_post_meta( $pid, 'service_features', true ) );
$cta_url  = $m( 'service_cta_url' ) ? $m( 'service_cta_url' ) : jat_contact_url();
$cta_text = $m( 'service_cta_text' ) ? $m( 'service_cta_text' ) : __( 'Book a discovery call', 'jat-signal' );
$ch       = jat_service_channel( $pid );
?>
<aside <?php echo get_block_wrapper_attributes( array( 'class' => 'sticky-card', 'aria-label' => __( 'Price and booking', 'jat-signal' ) ) ); ?>>
	<?php if ( $ch ) : ?><div class="tag-list"><?php echo jat_ch_tag( $ch ); // phpcs:ignore ?></div><?php endif; ?>
	<?php if ( $m( 'service_tier' ) ) : ?><span class="readout"><?php echo esc_html( $m( 'service_tier' ) ); ?></span><?php endif; ?>
	<?php if ( $price ) : ?><span class="amt"><?php echo esc_html( $price ); ?></span><?php endif; ?>
	<?php if ( $m( 'service_price_note' ) ) : ?><span class="muted small"><?php echo esc_html( $m( 'service_price_note' ) ); ?></span><?php endif; ?>
	<?php if ( $m( 'service_hours_included' ) ) : ?><span class="small"><?php echo esc_html( $m( 'service_hours_included' ) ); ?></span><?php endif; ?>
	<?php if ( $m( 'service_min_hours' ) ) : ?><span class="readout"><?php echo esc_html( sprintf( /* translators: %s minimum */ __( 'Minimum %s', 'jat-signal' ), $m( 'service_min_hours' ) ) ); ?></span><?php endif; ?>
	<?php if ( $features ) : ?>
		<ul class="check-list"<?php echo $ch ? ' style="--c:var(' . esc_attr( jat_channels()[ $ch ]['var'] ) . ')"' : ''; ?>>
			<?php foreach ( $features as $f ) : ?><li><?php echo jat_icon( 'check' ); // phpcs:ignore ?><span><?php echo esc_html( (string) $f ); ?></span></li><?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<?php if ( $m( 'service_typical_uses' ) ) : ?><p class="small muted"><?php echo esc_html( $m( 'service_typical_uses' ) ); ?></p><?php endif; ?>
	<?php echo jat_button( $cta_url, $cta_text, 'primary' ); // phpcs:ignore ?>
	<?php echo jat_button( jat_page_url( 'services' ), __( 'All prices', 'jat-signal' ), 'secondary' ); // phpcs:ignore ?>
</aside>
