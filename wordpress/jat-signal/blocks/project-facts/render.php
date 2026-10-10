<?php
/**
 * jat/project-facts — sticky facts card on a single project.
 *
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$pid = (int) ( $block->context['postId'] ?? get_the_ID() );
if ( ! $pid || 'project' !== get_post_type( $pid ) ) {
	echo jat_editor_note( __( 'Project facts appear on single project pages.', 'jat-signal' ) ); // phpcs:ignore
	return;
}
$cats = get_the_terms( $pid, 'project_category' );
$tags = get_the_terms( $pid, 'project_tag' );
?>
<aside <?php echo get_block_wrapper_attributes( array( 'class' => 'sticky-card', 'aria-label' => __( 'Project facts', 'jat-signal' ) ) ); ?>>
	<ul class="facts">
		<?php if ( $cats && ! is_wp_error( $cats ) ) : ?><li><span><?php esc_html_e( 'Type', 'jat-signal' ); ?></span><span><?php echo esc_html( implode( ', ', wp_list_pluck( $cats, 'name' ) ) ); ?></span></li><?php endif; ?>
		<li><span><?php esc_html_e( 'Status', 'jat-signal' ); ?></span><span><?php echo jat_project_is_open( $pid ) ? esc_html__( 'Public', 'jat-signal' ) : esc_html__( 'Private', 'jat-signal' ); ?></span></li>
	</ul>
	<?php if ( $tags && ! is_wp_error( $tags ) ) : ?>
		<div class="stack-chips"><span class="readout"><?php esc_html_e( 'Built with', 'jat-signal' ); ?></span><?php echo jat_term_chips( $pid, 'project_tag', 24 ); // phpcs:ignore ?></div>
	<?php endif; ?>
	<?php echo jat_project_links( $pid ); // phpcs:ignore ?>
	<?php echo jat_button( jat_contact_url(), __( 'Build something like this', 'jat-signal' ), 'primary' ); // phpcs:ignore ?>
</aside>
