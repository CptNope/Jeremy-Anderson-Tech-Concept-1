<?php
/**
 * jat/channel-intro — channel page opener: outcome heading and lede, service areas with checklists,
 * and a sticky card showing the channel's first package (price straight from the Services post type).
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$chs = jat_channels();
$key = (string) ( $attributes['channel'] ?? 'ch1' );
if ( empty( $chs[ $key ] ) ) {
	return;
}
$c     = $chs[ $key ];
$items = jat_json( $attributes['items'] ?? '[]' );
$first = jat_services_in( $c['category'], 1 );
$first = $first ? $first[0] : null;
$hid   = jat_id( 'outcome' );
$cta   = trim( (string) ( $attributes['ctaUrl'] ?? '' ) );
$cta   = $cta ? $cta : jat_contact_url();
echo jat_section_open( '', $hid ); // phpcs:ignore
?>
<div class="aside-col">
	<div>
		<h2 id="<?php echo esc_attr( $hid ); ?>" class="intro-h"><?php echo esc_html( (string) ( $attributes['heading'] ?? '' ) ); ?></h2>
		<?php if ( ! empty( $attributes['lede'] ) ) : ?><p class="lede"><?php echo esc_html( $attributes['lede'] ); ?></p><?php endif; ?>
		<?php if ( $items ) : ?>
			<div class="svc-grid">
				<?php foreach ( $items as $i ) : $sid = sanitize_title( $i['id'] ?? ( $i['title'] ?? '' ) ); ?>
					<div id="<?php echo esc_attr( $sid ); ?>" class="svc-item">
						<h3 id="<?php echo esc_attr( $sid ); ?>-h"><?php echo esc_html( $i['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $i['outcome'] ) ) : ?><p class="outcome"><?php echo esc_html( $i['outcome'] ); ?></p><?php endif; ?>
						<?php if ( ! empty( $i['points'] ) ) : ?>
							<ul class="check-list" style="--c:var(<?php echo esc_attr( $c['var'] ); ?>)">
								<?php foreach ( (array) $i['points'] as $pt ) : ?><li><?php echo jat_icon( 'check' ); // phpcs:ignore ?><span><?php echo esc_html( (string) $pt ); ?></span></li><?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="sticky-card">
		<span class="readout" style="color:var(<?php echo esc_attr( $c['var'] ); ?>)"><?php echo esc_html( $c['id'] . ' · ' . $c['name'] ); ?></span>
		<?php if ( $first ) : ?>
			<span class="amt"><?php echo esc_html( (string) get_post_meta( $first->ID, 'service_price', true ) ); ?></span>
			<span class="muted small"><?php echo esc_html( sprintf( /* translators: %s package */ __( '%s, the usual starting point', 'jat-signal' ), get_the_title( $first ) ) ); ?></span>
		<?php endif; ?>
		<?php echo jat_button( $cta, (string) ( $attributes['ctaText'] ?? __( 'Book a discovery call', 'jat-signal' ) ), 'primary' ); // phpcs:ignore ?>
		<a class="btn btn-secondary" href="#<?php echo esc_attr( $c['category'] ); ?>"><?php esc_html_e( 'Compare packages', 'jat-signal' ); ?></a>
		<p class="readout long"><?php esc_html_e( 'Free 30-minute call · Proposal in 3 business days', 'jat-signal' ); ?></p>
	</div>
</div>
<?php
echo jat_section_close(); // phpcs:ignore
