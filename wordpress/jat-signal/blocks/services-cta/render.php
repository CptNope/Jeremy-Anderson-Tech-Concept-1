<?php
/**
 * developer-portfolio/services-cta — closing call to action (Services variant).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$hid = jat_id( 'close' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'close', 'aria-labelledby' => $hid ) ); ?>>
	<div class="graticule" aria-hidden="true"></div>
	<div class="wrap">
		<div><h2 id="<?php echo esc_attr( $hid ); ?>"><?php echo esc_html( (string) ( $attributes['heading'] ?? 'Ready to Talk?' ) ); ?></h2></div>
		<div class="contact-lines">
			<?php if ( ! empty( $attributes['description'] ) ) : ?><p class="lede"><?php echo esc_html( $attributes['description'] ); ?></p><?php endif; ?>
			<div class="cta-row">
				<?php
				echo jat_button( $attributes['primaryButtonUrl'] ?? 'mailto:info@jeremyanderson.tech', $attributes['primaryButtonText'] ?? 'Email Jeremy →', 'primary' ); // phpcs:ignore
				echo jat_button( $attributes['secondaryButtonUrl'] ?? '/about', $attributes['secondaryButtonText'] ?? 'Learn More About Jeremy', 'secondary' ); // phpcs:ignore
				?>
			</div>
			<?php echo jat_copy_line( '', __( 'Or write', 'jat-signal' ) ); // phpcs:ignore ?>
		</div>
	</div>
</section>
