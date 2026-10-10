<?php
/**
 * developer-portfolio/contact-cta — the closing call to action on a graticule ground.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = jat_heading_text( $attributes['heading'] ?? "Let's Build Something", $attributes['headingHighlight'] ?? 'Amazing', true );
$desc    = (string) ( $attributes['description'] ?? "Got a project in mind? I'm always open to discussing new opportunities, creative ideas, or ways to help bring your vision to life." );
$b_text  = (string) ( $attributes['buttonText'] ?? 'Get In Touch ↗' );
$b_url   = trim( (string) ( $attributes['buttonUrl'] ?? 'mailto:info@jeremyanderson.tech' ) );
$b_url   = '' === $b_url ? jat_contact_url() : $b_url;
$s_text  = (string) ( $attributes['secondaryButtonText'] ?? '' );
$s_url   = (string) ( $attributes['secondaryButtonUrl'] ?? '' );
$email   = ! isset( $attributes['showEmail'] ) || $attributes['showEmail'];
$hid     = jat_id( 'close' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'close', 'aria-labelledby' => $hid ) ); ?>>
	<div class="graticule" aria-hidden="true"></div>
	<div class="wrap">
		<div><h2 id="<?php echo esc_attr( $hid ); ?>"><?php echo esc_html( $heading ); ?></h2></div>
		<div class="contact-lines">
			<?php if ( $desc ) : ?><p class="lede"><?php echo wp_kses_post( $desc ); ?></p><?php endif; ?>
			<div class="cta-row">
				<?php
				echo jat_button( $b_url, $b_text, 'primary' ); // phpcs:ignore
				echo jat_button( $s_url, $s_text, 'secondary' ); // phpcs:ignore
				?>
			</div>
			<?php
			if ( $email ) {
				echo jat_copy_line( '', __( 'Or write', 'jat-signal' ) ); // phpcs:ignore
			}
			?>
		</div>
	</div>
</section>
