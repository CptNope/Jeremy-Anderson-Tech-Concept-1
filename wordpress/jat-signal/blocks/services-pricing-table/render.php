<?php
/**
 * developer-portfolio/services-pricing-table — hourly rates from the Services post type (by category).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$tag     = (string) ( $attributes['sectionTag'] ?? 'Hourly Rates' );
$heading = (string) ( $attributes['heading'] ?? 'Consulting & Development Hours' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$cat     = (string) ( $attributes['category'] ?? 'hourly-rates' );
$posts   = jat_services_in( $cat );
$hid     = jat_id( 'rates' );

echo jat_section_open( '', $hid, 'section', array( 'id' => $cat ) ); // phpcs:ignore
echo jat_section_head( $heading, $intro, $hid ); // phpcs:ignore
if ( $posts ) : ?>
	<div class="table-wrap">
		<table class="spec stack" role="table">
			<caption class="readout"><?php echo esc_html( $tag ); ?></caption>
			<thead><tr role="row"><th scope="col" role="columnheader"><?php esc_html_e( 'Service area', 'jat-signal' ); ?></th><th scope="col" role="columnheader"><?php esc_html_e( 'Rate', 'jat-signal' ); ?></th><th scope="col" role="columnheader"><?php esc_html_e( 'Typical use cases', 'jat-signal' ); ?></th><th scope="col" role="columnheader"><?php esc_html_e( 'Minimum', 'jat-signal' ); ?></th></tr></thead>
			<tbody>
				<?php
				foreach ( $posts as $p ) :
					$ch = jat_service_channel( $p->ID );
					?>
					<tr role="row">
						<td data-label="<?php esc_attr_e( 'Service area', 'jat-signal' ); ?>" role="cell"><strong class="t-item"><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></strong><?php echo $ch ? jat_ch_tag( $ch ) : jat_readout( get_post_meta( $p->ID, 'service_subtitle', true ) ); // phpcs:ignore ?></td>
						<td data-label="<?php esc_attr_e( 'Rate', 'jat-signal' ); ?>" role="cell"><span class="amt"><?php echo esc_html( get_post_meta( $p->ID, 'service_price', true ) ); ?></span><span class="k"><?php echo esc_html( get_post_meta( $p->ID, 'service_price_note', true ) ); ?></span></td>
						<td data-label="<?php esc_attr_e( 'Typical use cases', 'jat-signal' ); ?>" role="cell" class="muted"><?php echo esc_html( get_post_meta( $p->ID, 'service_typical_uses', true ) ); ?></td>
						<td data-label="<?php esc_attr_e( 'Minimum', 'jat-signal' ); ?>" role="cell" class="mono"><?php echo esc_html( get_post_meta( $p->ID, 'service_min_hours', true ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
else :
	echo jat_editor_note( sprintf( /* translators: %s category slug */ __( 'No services in the "%s" category.', 'jat-signal' ), $cat ) ); // phpcs:ignore
endif;
$vt = (string) ( $attributes['volumeDiscountText'] ?? '' );
if ( $vt ) :
	?>
	<div class="note-box">
		<p class="note-title"><?php echo esc_html( trim( preg_replace( '/^[^\p{L}\p{N}]+/u', '', (string) ( $attributes['volumeDiscountTitle'] ?? '' ) ) ) ); ?></p>
		<p><?php echo wp_kses( $vt, array( 'strong' => array(), 'em' => array(), 'br' => array() ) ); ?></p>
	</div>
	<?php
endif;
echo jat_section_close(); // phpcs:ignore
