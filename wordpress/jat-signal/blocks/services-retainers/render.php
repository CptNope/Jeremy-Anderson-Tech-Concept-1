<?php
/**
 * developer-portfolio/services-retainers — monthly care plans, as price rows with included hours.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) ( $attributes['heading'] ?? 'Ongoing Support & Maintenance' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$cat     = (string) ( $attributes['category'] ?? 'monthly-retainers' );
$posts   = jat_services_in( $cat, 10 );
$hid     = jat_id( 'care' );

echo jat_section_open( 'packages', $hid, 'section', array( 'id' => $cat ) ); // phpcs:ignore
?>
<div class="section-head">
	<div class="head-main"><h2 id="<?php echo esc_attr( $hid ); ?>"><?php echo esc_html( $heading ); ?></h2><div class="tag-list"><?php echo jat_ch_tag( 'ch4' ); // phpcs:ignore ?></div></div>
	<?php if ( $intro ) : ?><div class="section-head-side"><p class="lede"><?php echo esc_html( $intro ); ?></p></div><?php endif; ?>
</div>
<?php
if ( $posts ) {
	echo '<div class="price-table" aria-label="' . esc_attr( (string) ( $attributes['sectionTag'] ?? $heading ) ) . '">';
	foreach ( $posts as $p ) {
		echo jat_service_row( $p, array( 'tag' => false ) ); // phpcs:ignore
	}
	echo '</div>';
}
$tt = (string) ( $attributes['retainerTermsText'] ?? '' );
if ( $tt ) {
	echo '<div class="note-box"><p class="note-title">' . esc_html( (string) ( $attributes['retainerTermsTitle'] ?? __( 'Retainer Terms', 'jat-signal' ) ) ) . '</p><p>' . esc_html( $tt ) . '</p></div>';
}
echo jat_section_close(); // phpcs:ignore
