<?php
/**
 * developer-portfolio/about-intro — About page head plus the founder introduction:
 * portrait (or a graticule placeholder), greeting, bio, and the stats as a readout strip.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$title    = jat_heading_text( $attributes['heading'] ?? 'The Developer Behind the Code', $attributes['headingHighlight'] ?? 'Developer' );
$img      = trim( (string) ( $attributes['imageUrl'] ?? '' ) );
$alt      = (string) ( $attributes['imageAlt'] ?? '' );
$location = trim( (string) ( $attributes['location'] ?? '' ) );
$greeting = trim( (string) ( $attributes['greeting'] ?? '' ) );
$bios     = array_filter( array( $attributes['bio1'] ?? '', $attributes['bio2'] ?? '', $attributes['bio3'] ?? '' ), static fn( $b ) => '' !== trim( (string) $b ) );
$stats    = array();
for ( $i = 1; $i <= 4; $i++ ) {
	$num = trim( (string) ( $attributes[ 'stat' . $i . 'Number' ] ?? '' ) );
	$lab = trim( (string) ( $attributes[ 'stat' . $i . 'Label' ] ?? '' ) );
	if ( '' !== $num || '' !== $lab ) {
		$stats[] = array( $num, $lab );
	}
}
$gid = jat_id( 'meet' );
echo '<div ' . get_block_wrapper_attributes( array( 'class' => 'about-intro' ) ) . '>'; // phpcs:ignore
echo jat_page_head( array( 'title' => $title, 'crumbs' => 'auto' ) ); // phpcs:ignore
?>
<section class="section" aria-labelledby="<?php echo esc_attr( $gid ); ?>">
	<div class="wrap founder">
		<figure class="portrait<?php echo $img ? '' : ' graticule'; ?>">
			<?php if ( $img ) : ?>
				<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( $alt ); ?>" loading="eager" decoding="async" fetchpriority="high">
			<?php else : ?>
				<?php echo jat_portrait_placeholder(); // phpcs:ignore ?>
			<?php endif; ?>
			<?php if ( $location ) : ?>
				<figcaption class="readout"><?php echo jat_icon( 'pin' ); // phpcs:ignore ?><?php echo esc_html( $location ); ?></figcaption>
			<?php endif; ?>
		</figure>
		<div class="founder-text">
			<h2 id="<?php echo esc_attr( $gid ); ?>"><?php echo esc_html( $greeting ? $greeting : __( 'Meet the founder', 'jat-signal' ) ); ?></h2>
			<?php foreach ( $bios as $k => $bio ) : ?>
				<p class="<?php echo 0 === $k ? 'lede' : ''; ?>"><?php echo esc_html( $bio ); ?></p>
			<?php endforeach; ?>
		</div>
	</div>
	<?php if ( $stats ) : ?>
		<div class="wrap">
			<dl class="stat-strip stats-big">
				<?php foreach ( $stats as $s ) : ?>
					<div><dt class="readout"><?php echo esc_html( $s[1] ); ?></dt><dd class="v mono"><?php echo esc_html( $s[0] ); ?></dd></div>
				<?php endforeach; ?>
			</dl>
		</div>
	<?php endif; ?>
</section>
</div>
