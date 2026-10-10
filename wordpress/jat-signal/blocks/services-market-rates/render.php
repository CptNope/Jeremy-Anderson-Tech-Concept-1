<?php
/**
 * developer-portfolio/services-market-rates — market context as an instrument readout strip.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$cards = jat_json( $attributes['cards'] ?? '[]' );
if ( ! $cards ) {
	return;
}
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'section tight', 'aria-label' => __( 'Market rates for comparison', 'jat-signal' ) ) ); ?>>
	<div class="wrap">
		<dl class="stat-strip">
			<?php foreach ( $cards as $card ) : ?>
				<div>
					<dt class="readout"><?php echo esc_html( $card['label'] ?? '' ); ?></dt>
					<dd class="v mono"><?php echo esc_html( $card['range'] ?? '' ); ?></dd>
					<?php if ( ! empty( $card['note'] ) ) : ?><dd class="k"><?php echo esc_html( $card['note'] ); ?></dd><?php endif; ?>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
