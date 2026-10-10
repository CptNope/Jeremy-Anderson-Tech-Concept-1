<?php
/**
 * developer-portfolio/featured-projects — selected projects.
 *
 * layout "grid" (default): a row of work cards. layout "feature": the first project as a large
 * screen with its facts, the next ones as a pair of cards (the concept's "Engineering you can inspect").
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading  = jat_heading_text( $attributes['heading'] ?? 'Recent Projects', $attributes['headingHighlight'] ?? 'Projects' );
$desc     = (string) ( $attributes['description'] ?? '' );
$link     = (string) ( $attributes['linkUrl'] ?? '/projects' );
$link_txt = (string) ( $attributes['linkText'] ?? 'View all projects →' );
$layout   = $attributes['layout'] ?? 'grid';
$posts    = jat_pick_posts( 'project', $attributes['postIds'] ?? '[]', (int) ( $attributes['count'] ?? 3 ) );
$hid      = jat_id( 'work' );

if ( ! $posts ) {
	echo jat_editor_note( __( 'No published projects yet. Add one under Projects.', 'jat-signal' ) ); // phpcs:ignore
	return;
}

echo jat_section_open( '', $hid ); // phpcs:ignore -- markup from helper.
echo jat_section_head( $heading, $desc, $hid, $link, $link_txt ); // phpcs:ignore

if ( 'feature' === $layout ) {
	$first = array_shift( $posts );
	$sub   = (string) get_post_meta( $first->ID, 'project_subtitle', true );
	$tags  = get_the_terms( $first->ID, 'project_tag' );
	$cats  = get_the_terms( $first->ID, 'project_category' );
	?>
	<div class="work-feature">
		<a class="screen plate-link" href="<?php echo esc_url( get_permalink( $first ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s project */ __( '%s case study', 'jat-signal' ), get_the_title( $first ) ) ); ?>"><?php echo jat_project_plate( $first, 'large' ); // phpcs:ignore ?></a>
		<div class="work-meta">
			<h3><a href="<?php echo esc_url( get_permalink( $first ) ); ?>"><?php echo esc_html( get_the_title( $first ) ); ?></a></h3>
			<?php if ( $sub ) : ?><p class="sub"><?php echo esc_html( $sub ); ?></p><?php endif; ?>
			<p class="muted"><?php echo esc_html( jat_excerpt( $first, 40 ) ); ?></p>
			<ul class="facts">
				<?php if ( $cats && ! is_wp_error( $cats ) ) : ?>
					<li><span><?php esc_html_e( 'Type', 'jat-signal' ); ?></span><span><?php echo esc_html( implode( ', ', wp_list_pluck( $cats, 'name' ) ) ); ?></span></li>
				<?php endif; ?>
				<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
					<li><span><?php esc_html_e( 'Built with', 'jat-signal' ); ?></span><span><?php echo esc_html( implode( ', ', array_slice( wp_list_pluck( $tags, 'name' ), 0, 6 ) ) ); ?></span></li>
				<?php endif; ?>
				<li><span><?php esc_html_e( 'Status', 'jat-signal' ); ?></span><span><?php echo jat_project_is_open( $first->ID ) ? esc_html__( 'Public: live and readable', 'jat-signal' ) : esc_html__( 'Private: available on request', 'jat-signal' ); ?></span></li>
			</ul>
			<div class="links-row">
				<?php echo jat_link_arrow( get_permalink( $first ), __( 'Read the case study', 'jat-signal' ) ); // phpcs:ignore ?>
				<?php
				$live = (string) get_post_meta( $first->ID, 'project_live_url', true );
				if ( $live ) {
					echo jat_link_arrow( $live, __( 'Open live app', 'jat-signal' ) ); // phpcs:ignore
				}
				?>
			</div>
		</div>
	</div>
	<?php if ( $posts ) : ?>
		<div class="work-pair">
			<?php foreach ( $posts as $p ) { echo jat_project_card( $p ); } // phpcs:ignore ?>
		</div>
	<?php endif; ?>
	<?php
} else {
	echo '<div class="work-grid">';
	foreach ( $posts as $p ) {
		echo jat_project_card( $p ); // phpcs:ignore
	}
	echo '</div>';
}

echo jat_section_close(); // phpcs:ignore
