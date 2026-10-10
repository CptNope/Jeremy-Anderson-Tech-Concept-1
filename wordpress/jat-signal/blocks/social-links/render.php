<?php
/**
 * developer-portfolio/social-links — profile links as labelled icon buttons.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$links = array_filter( array(
	'github'   => array( $attributes['githubUrl'] ?? '', 'GitHub' ),
	'linkedin' => array( $attributes['linkedinUrl'] ?? '', 'LinkedIn' ),
	'x'        => array( $attributes['xUrl'] ?? '', 'X (Twitter)' ),
	'email'    => array( $attributes['emailUrl'] ?? '', __( 'Email', 'jat-signal' ) ),
), static fn( $l ) => '' !== trim( (string) $l[0] ) );
if ( ! $links ) {
	return;
}
?>
<ul <?php echo get_block_wrapper_attributes( array( 'class' => 'social' ) ); ?>>
	<?php foreach ( $links as $icon => $l ) : ?>
		<li><a href="<?php echo esc_url( $l[0] ); ?>"<?php echo 'email' === $icon ? '' : ' rel="me noopener"'; ?> aria-label="<?php echo esc_attr( $l[1] ); ?>"><?php echo jat_icon( $icon ); // phpcs:ignore ?></a></li>
	<?php endforeach; ?>
</ul>
