<?php
/**
 * jat/site-footer — lockup, studio line, email, link columns and profiles.
 * Links only appear for pages that exist and are published.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$col = static function ( array $links ): string {
	$out = '';
	foreach ( $links as $l ) {
		if ( ! empty( $l[1] ) ) {
			$out .= '<li><a href="' . esc_url( $l[1] ) . '"' . ( jat_is_external( $l[1] ) ? ' rel="me noopener"' : '' ) . '>' . esc_html( $l[0] ) . '</a></li>';
		}
	}
	return $out;
};
$services = array();
foreach ( jat_channels() as $key => $c ) {
	$services[] = array( $c['id'] . ' ' . $c['name'], jat_channel_url( $key ) );
}
$services[] = array( __( 'Pricing', 'jat-signal' ), jat_page_url( 'services' ) );
$blog       = (int) get_option( 'page_for_posts' );
$studio     = array(
	array( __( 'Projects', 'jat-signal' ), jat_page_url( 'projects' ) ),
	array( __( 'Approach', 'jat-signal' ), jat_page_url( 'approach' ) ),
	array( __( 'About', 'jat-signal' ), jat_page_url( 'about' ) ),
	array( $blog ? get_the_title( $blog ) : __( 'Blog', 'jat-signal' ), $blog && 'publish' === get_post_status( $blog ) ? get_permalink( $blog ) : '' ),
	array( __( 'Contact', 'jat-signal' ), jat_page_url( 'contact' ) ),
);
$elsewhere  = array(
	array( 'GitHub', (string) ( $attributes['githubUrl'] ?? '' ) ),
	array( 'LinkedIn', (string) ( $attributes['linkedinUrl'] ?? '' ) ),
	array( 'X (Twitter)', (string) ( $attributes['xUrl'] ?? '' ) ),
	array( __( 'Privacy policy', 'jat-signal' ), get_privacy_policy_url() ? get_privacy_policy_url() : jat_page_url( 'privacy-policy' ) ),
	array( __( 'Terms of service', 'jat-signal' ), jat_page_url( 'terms-of-service' ) ),
	array( __( 'Accessibility', 'jat-signal' ), jat_page_url( 'accessibility' ) ),
);
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'site-footer' ) ); ?>>
	<div class="wrap">
		<div class="foot-grid">
			<div>
				<a class="foot-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s site */ __( '%s, home', 'jat-signal' ), get_bloginfo( 'name' ) ) ); ?>"><?php echo jat_editor_sprite(); // phpcs:ignore ?><svg viewBox="1 6 468 68" width="220" height="32" aria-hidden="true" focusable="false"><use href="#lk-h"/></svg></a>
				<?php if ( ! empty( $attributes['blurb'] ) ) : ?><p class="muted"><?php echo esc_html( $attributes['blurb'] ); ?></p><?php endif; ?>
				<?php echo jat_copy_line(); // phpcs:ignore ?>
			</div>
			<div><h2><?php esc_html_e( 'Services', 'jat-signal' ); ?></h2><ul><?php echo $col( $services ); // phpcs:ignore ?></ul></div>
			<div><h2><?php esc_html_e( 'Studio', 'jat-signal' ); ?></h2><ul><?php echo $col( $studio ); // phpcs:ignore ?></ul></div>
			<div><h2><?php esc_html_e( 'Elsewhere', 'jat-signal' ); ?></h2><ul><?php echo $col( $elsewhere ); // phpcs:ignore ?></ul></div>
		</div>
		<div class="foot-base">
			<span class="readout long">© <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?> · Shrewsbury,&nbsp;MA</span>
			<?php if ( ! empty( $attributes['note'] ) ) : ?><span class="concept-note"><?php echo esc_html( $attributes['note'] ); ?></span><?php endif; ?>
		</div>
	</div>
</div>
