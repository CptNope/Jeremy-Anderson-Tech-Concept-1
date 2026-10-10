<?php
/**
 * jat/versus — two trace screens side by side.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$a   = $attributes;
$hid = jat_id( 'why' );
echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( (string) ( $a['heading'] ?? '' ), (string) ( $a['lede'] ?? '' ), $hid ); // phpcs:ignore
?>
<div class="versus">
	<div class="screen graticule">
		<div class="screen-bar"><span class="readout"><?php echo esc_html( $a['leftLabel'] ?? '' ); ?></span><span class="readout"><?php echo esc_html( $a['leftState'] ?? '' ); ?></span></div>
		<canvas data-versus="many" aria-hidden="true"></canvas>
		<div class="labels"><h3><?php echo esc_html( $a['leftTitle'] ?? '' ); ?></h3><p><?php echo esc_html( $a['leftText'] ?? '' ); ?></p></div>
	</div>
	<div class="screen graticule">
		<div class="screen-bar"><span class="readout"><?php echo esc_html( $a['rightLabel'] ?? '' ); ?></span><span class="readout locked"><?php echo esc_html( $a['rightState'] ?? '' ); ?></span></div>
		<canvas data-versus="one" aria-hidden="true"></canvas>
		<div class="labels"><h3><?php echo esc_html( $a['rightTitle'] ?? '' ); ?></h3><p><?php echo esc_html( $a['rightText'] ?? '' ); ?></p></div>
	</div>
</div>
<?php
echo jat_section_close(); // phpcs:ignore
