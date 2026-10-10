<?php
/**
 * developer-portfolio/services-addons — à la carte services as compact price rows in two columns.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) ( $attributes['heading'] ?? 'À La Carte Services' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$cat     = (string) ( $attributes['category'] ?? 'add-ons' );
$posts   = jat_services_in( $cat );
$hid     = jat_id( 'addons' );

echo jat_section_open( '', $hid, 'section', array( 'id' => $cat ) ); // phpcs:ignore
echo jat_section_head( $heading, $intro, $hid ); // phpcs:ignore
if ( $posts ) : ?>
	<ul class="addon-list" aria-label="<?php echo esc_attr( (string) ( $attributes['sectionTag'] ?? $heading ) ); ?>">
		<?php foreach ( $posts as $p ) : ?>
			<li>
				<div class="addon-head"><a class="t-item" href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a><span class="amt"><?php echo esc_html( get_post_meta( $p->ID, 'service_price', true ) ); ?></span></div>
				<?php $ex = jat_excerpt( $p, 26 ); ?>
				<?php if ( $ex ) : ?><p><?php echo esc_html( $ex ); ?></p><?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
endif;
echo jat_section_close(); // phpcs:ignore
