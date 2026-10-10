<?php
/**
 * jat/channels — the four service channels as rows (promise, services, live starting price, link)
 * or as the compact strip used under the hero.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$chs = jat_channels();
if ( 'strip' === ( $attributes['layout'] ?? 'rows' ) ) {
	echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'strip-wrap' ) ) . '><div class="wrap">' . jat_channel_strip() . '</div></div>'; // phpcs:ignore
	return;
}
$items = jat_json( $attributes['items'] ?? '[]' );
$hid   = jat_id( 'channels' );
echo jat_section_open( '', $hid ); // phpcs:ignore
if ( '' !== trim( (string) ( $attributes['heading'] ?? '' ) ) ) {
	echo jat_section_head( (string) $attributes['heading'], (string) ( $attributes['lede'] ?? '' ), $hid ); // phpcs:ignore
} else {
	echo '<h2 class="vh" id="' . esc_attr( $hid ) . '">' . esc_html__( 'Service channels', 'jat-signal' ) . '</h2>';
}
echo '<div class="ch-rows">';
foreach ( $items as $item ) {
	$key = (string) ( $item['key'] ?? '' );
	if ( empty( $chs[ $key ] ) ) {
		continue;
	}
	$c    = $chs[ $key ];
	$from = jat_category_from_price( $c['category'] );
	?>
	<article class="ch-row" style="--c:var(<?php echo esc_attr( $c['var'] ); ?>)">
		<div class="id" aria-hidden="true"><i></i><?php echo esc_html( $c['id'] ); ?></div>
		<div>
			<h3><span class="id-inline"><?php echo esc_html( $c['id'] ); ?> </span><?php echo esc_html( $c['name'] ); ?></h3>
			<p><?php echo esc_html( (string) ( $item['promise'] ?? '' ) ); ?></p>
		</div>
		<ul aria-label="<?php echo esc_attr( sprintf( /* translators: %s channel */ __( '%s services', 'jat-signal' ), $c['name'] ) ); ?>">
			<?php foreach ( (array) ( $item['points'] ?? array() ) as $pt ) : ?>
				<li><?php echo esc_html( (string) $pt ); ?></li>
			<?php endforeach; ?>
		</ul>
		<div class="price">
			<?php if ( $from ) : ?><span class="amt"><?php echo esc_html( $from ); ?></span><span class="readout"><?php echo esc_html( $c['unit'] ); ?></span><?php endif; ?>
			<?php echo jat_link_arrow( jat_channel_url( $key ), sprintf( /* translators: %s channel */ __( 'Explore %s', 'jat-signal' ), $c['name'] ) ); // phpcs:ignore ?>
		</div>
	</article>
	<?php
}
echo '</div>';
echo jat_section_close(); // phpcs:ignore
