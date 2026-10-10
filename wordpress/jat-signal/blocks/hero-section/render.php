<?php
/**
 * developer-portfolio/hero-section — the oscilloscope hero.
 *
 * name → H1, description → lede, role → a mono line that cycles through "roles",
 * badgeText → the status readout (its dot turns green when the trace locks),
 * note → small print under the buttons, showChannels → the four-channel strip.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$name        = $attributes['name'] ?? 'Jeremy Anderson';
$role        = trim( wp_strip_all_tags( (string) ( $attributes['role'] ?? '{ Full-Stack Developer }' ) ) );
$description = $attributes['description'] ?? 'I craft performant, accessible, and beautifully designed web experiences. Specializing in React, Next.js, and modern web technologies.';
$badge       = trim( wp_strip_all_tags( (string) ( $attributes['badgeText'] ?? '✨ Available for freelance work' ) ) );
$badge       = trim( preg_replace( '/^[^\p{L}\p{N}]+/u', '', $badge ) ); // Drop leading emoji/decoration.
$p_text      = $attributes['primaryButtonText'] ?? 'View My Work';
$p_url       = trim( (string) ( $attributes['primaryButtonUrl'] ?? '/projects' ) );
$p_url       = '' === $p_url ? jat_contact_url() : $p_url;
$s_text      = $attributes['secondaryButtonText'] ?? 'About Me';
$s_url       = $attributes['secondaryButtonUrl'] ?? '/about';
$note        = trim( (string) ( $attributes['note'] ?? '' ) );
$roles       = array_values( array_filter( array_map( 'trim', explode( ',', (string) ( $attributes['roles'] ?? 'Full-Stack Developer, WordPress Engineer, AI Systems Builder, Cybersecurity Specialist, Cloud Architect' ) ) ) ) );
$hid         = jat_id( 'hero' );

// The role line keeps its braces style ("{ Software Architect }") and cycles through the roles list.
$role_inner = trim( $role, "{} \t" );
$wrap_role  = $role !== $role_inner;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'hero', 'aria-labelledby' => $hid ) ); ?>>
	<div class="graticule" aria-hidden="true"></div>
	<canvas class="hero-trace" aria-hidden="true"></canvas>
	<div class="hero-state readout<?php echo $badge ? ' has-badge' : ''; ?>" data-state="run">
		<span><span class="dot" aria-hidden="true"></span><span class="lbl"<?php echo $badge ? '' : ' aria-hidden="true"'; ?>><?php echo esc_html( $badge ? $badge : __( 'Acquiring signal', 'jat-signal' ) ); ?></span></span>
	</div>
	<div class="wrap">
		<div class="hero-copy">
			<h1 id="<?php echo esc_attr( $hid ); ?>"><?php echo wp_kses_post( $name ); ?></h1>
			<?php if ( '' !== $role_inner ) : ?>
				<p class="hero-role mono"<?php echo count( $roles ) > 1 ? ' data-roles="' . esc_attr( wp_json_encode( $roles ) ) . '" data-wrap="' . ( $wrap_role ? '1' : '0' ) . '"' : ''; ?>>
					<span class="role-text" aria-hidden="<?php echo count( $roles ) > 1 ? 'true' : 'false'; ?>"><?php echo esc_html( $role ); ?></span>
					<?php if ( count( $roles ) > 1 ) : ?><span class="vh"><?php echo esc_html( implode( ', ', $roles ) ); ?></span><?php endif; ?>
				</p>
			<?php endif; ?>
			<?php if ( $description ) : ?>
				<p class="lede"><?php echo wp_kses_post( $description ); ?></p>
			<?php endif; ?>
			<div class="cta-row">
				<?php
				echo jat_button( $p_url, $p_text, 'primary' ); // phpcs:ignore -- escaped in helper.
				echo jat_button( $s_url, $s_text, 'secondary' ); // phpcs:ignore
				?>
			</div>
			<?php if ( $note ) : ?>
				<p class="readout note"><?php echo esc_html( $note ); ?></p>
			<?php endif; ?>
		</div>
		<?php
		if ( ! empty( $attributes['showChannels'] ) ) {
			echo jat_channel_strip(); // phpcs:ignore -- escaped in helper.
		}
		?>
	</div>
</section>
