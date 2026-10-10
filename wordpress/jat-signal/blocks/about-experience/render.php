<?php
/**
 * developer-portfolio/about-experience — work history as a timeline, education and achievements as spec lists.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = jat_heading_text( $attributes['heading'] ?? "Where I've Worked", $attributes['headingHighlight'] ?? 'Worked' );
$jobs    = jat_json( $attributes['jobs'] ?? '[]' );
$edu     = jat_json( $attributes['eduItems'] ?? '[]' );
$ach     = jat_json( $attributes['achieveItems'] ?? '[]' );
$hid     = jat_id( 'exp' );

echo jat_section_open( 'experience', $hid ); // phpcs:ignore
echo jat_section_head( $heading, '', $hid ); // phpcs:ignore
if ( $jobs ) :
	?>
	<ol class="history">
		<?php foreach ( $jobs as $job ) : ?>
			<li>
				<span class="y readout"><?php echo esc_html( $job['period'] ?? '' ); ?></span>
				<div>
					<h3 class="t-item"><?php echo esc_html( $job['role'] ?? '' ); ?></h3>
					<?php if ( ! empty( $job['company'] ) ) : ?><p class="org"><?php echo esc_html( $job['company'] ); ?></p><?php endif; ?>
					<?php if ( ! empty( $job['description'] ) ) : ?><p class="muted"><?php echo esc_html( $job['description'] ); ?></p><?php endif; ?>
				</div>
			</li>
		<?php endforeach; ?>
	</ol>
	<?php
endif;
$lists = array_filter( array(
	array( $attributes['eduTitle'] ?? 'Education', $edu ),
	array( $attributes['achieveTitle'] ?? 'Achievements', $ach ),
), static fn( $l ) => ! empty( $l[1] ) );
if ( $lists ) :
	?>
	<div class="two-col cred">
		<?php foreach ( $lists as $l ) : ?>
			<section aria-label="<?php echo esc_attr( $l[0] ); ?>">
				<h3><?php echo esc_html( $l[0] ); ?></h3>
				<ul class="facts">
					<?php foreach ( $l[1] as $item ) : ?>
						<li><span><?php echo esc_html( $item['subtitle'] ?? '' ); ?></span><span><?php echo esc_html( $item['title'] ?? '' ); ?></span></li>
					<?php endforeach; ?>
				</ul>
			</section>
		<?php endforeach; ?>
	</div>
	<?php
endif;
echo jat_section_close(); // phpcs:ignore
