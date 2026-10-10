<?php
/**
 * developer-portfolio/services-packages — fixed-fee packages in one service category, as price rows.
 * sectionTagType (wp, ai, sec, maint) picks the channel identifier.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) ( $attributes['heading'] ?? 'WordPress Project Packages' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$cat     = (string) ( $attributes['category'] ?? 'wordpress-packages' );
$ch      = jat_channel_key( (string) ( $attributes['sectionTagType'] ?? 'wp' ) );
$posts   = jat_services_in( $cat, 10 );
$hid     = jat_id( 'pkg' );

echo jat_section_open( 'packages', $hid, 'section', array( 'id' => $cat ) ); // phpcs:ignore
?>
<div class="section-head">
	<div class="head-main">
		<h2 id="<?php echo esc_attr( $hid ); ?>"><?php echo esc_html( $heading ); ?></h2>
		<?php if ( $ch ) : ?><div class="tag-list"><?php echo jat_ch_tag( $ch ); // phpcs:ignore ?></div><?php endif; ?>
	</div>
	<?php if ( $intro ) : ?><div class="section-head-side"><p class="lede"><?php echo esc_html( $intro ); ?></p></div><?php endif; ?>
</div>
<?php
if ( $posts ) {
	echo '<div class="price-table" aria-label="' . esc_attr( (string) ( $attributes['sectionTag'] ?? $heading ) ) . '">';
	foreach ( $posts as $p ) {
		echo jat_service_row( $p, array( 'tag' => false ) ); // phpcs:ignore
	}
	echo '</div>';
} else {
	echo jat_editor_note( sprintf( /* translators: %s category slug */ __( 'No services in the "%s" category.', 'jat-signal' ), $cat ) ); // phpcs:ignore
}
echo jat_section_close(); // phpcs:ignore
