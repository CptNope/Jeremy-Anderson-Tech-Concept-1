<?php
/**
 * jat/founder — portrait, introduction and a short timeline.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$hid   = jat_id( 'founder' );
$items = jat_json( $attributes['items'] ?? '[]' );
$img   = '';
if ( ! empty( $attributes['imageId'] ) ) {
	$img = wp_get_attachment_image( (int) $attributes['imageId'], 'large', false, array( 'alt' => (string) ( $attributes['imageAlt'] ?? '' ), 'loading' => 'lazy' ) );
} elseif ( ! empty( $attributes['imageUrl'] ) ) {
	$img = '<img src="' . esc_url( $attributes['imageUrl'] ) . '" alt="' . esc_attr( (string) ( $attributes['imageAlt'] ?? '' ) ) . '" loading="lazy" decoding="async">';
}
echo jat_section_open( '', $hid ); // phpcs:ignore
?>
<div class="founder">
	<figure class="portrait<?php echo $img ? '' : ' graticule'; ?>">
		<?php echo $img ? $img : jat_portrait_placeholder(); // phpcs:ignore ?>
		<?php if ( ! empty( $attributes['caption'] ) ) : ?><figcaption class="readout"><?php echo jat_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $attributes['caption'] ); ?></figcaption><?php endif; ?>
	</figure>
	<div class="founder-text">
		<h2 id="<?php echo esc_attr( $hid ); ?>"><?php echo esc_html( $attributes['heading'] ?? '' ); ?></h2>
		<?php if ( ! empty( $attributes['text'] ) ) : ?><p class="lede"><?php echo esc_html( $attributes['text'] ); ?></p><?php endif; ?>
		<?php if ( $items ) : ?>
			<ul class="timeline">
				<?php foreach ( $items as $i ) : ?>
					<li><span class="y"><?php echo esc_html( $i['y'] ?? '' ); ?></span><span><?php echo esc_html( $i['text'] ?? '' ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
		<?php
		$u = (string) ( $attributes['linkUrl'] ?? '' );
		if ( $u && ( '/about/' !== $u || jat_page_url( 'about' ) ) ) {
			echo '<p class="after-table">' . jat_link_arrow( $u, $attributes['linkText'] ?? '' ) . '</p>'; // phpcs:ignore
		}
		?>
	</div>
</div>
<?php
echo jat_section_close(); // phpcs:ignore
