<?php
/**
 * jat/page-head — breadcrumbs, H1, lede and a channel waveform.
 * With no title set it reads the current view: page, post, project, service, posts page,
 * archive, search or 404 — so one block serves every template.
 *
 * @var array    $attributes
 * @var WP_Block $block
 */

defined( 'ABSPATH' ) || exit;

$title   = trim( (string) ( $attributes['title'] ?? '' ) );
$lede    = trim( (string) ( $attributes['lede'] ?? '' ) );
$channel = (string) ( $attributes['channel'] ?? '' );
$extra   = '';
$pid     = (int) ( $block->context['postId'] ?? 0 );
if ( ! $pid && is_singular() ) {
	$pid = get_queried_object_id();
}

if ( '' === $title ) {
	if ( $pid && ( is_singular() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) ) {
		$post  = get_post( $pid );
		$type  = $post ? $post->post_type : '';
		$title = get_the_title( $pid );
		if ( '' === $lede ) {
			$sub  = array( 'post' => 'post_subtitle', 'project' => 'project_subtitle', 'service' => 'service_subtitle' )[ $type ] ?? '';
			$lede = $sub ? (string) get_post_meta( $pid, $sub, true ) : '';
			if ( '' === $lede && $post && has_excerpt( $post ) ) {
				$lede = $post->post_excerpt;
			}
		}
		if ( 'service' === $type && ! $channel ) {
			$channel = jat_service_channel( $pid );
		}
		if ( ! empty( $attributes['showMeta'] ) || ! isset( $attributes['showMeta'] ) ) {
			if ( 'post' === $type ) {
				$words = str_word_count( wp_strip_all_tags( $post->post_content ) );
				$cats  = get_the_category( $pid );
				$extra = '<div class="head-meta">'
					. '<time class="readout" datetime="' . esc_attr( get_the_date( 'c', $pid ) ) . '">' . esc_html( get_the_date( 'M j, Y', $pid ) ) . '</time>'
					. jat_readout( sprintf( /* translators: %d minutes */ _n( '%d min read', '%d min read', max( 1, (int) round( $words / 220 ) ), 'jat-signal' ), max( 1, (int) round( $words / 220 ) ) ) )
					. ( $cats ? '<a class="readout" href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>' : '' )
					. '</div>';
			} elseif ( 'project' === $type ) {
				$terms = get_the_terms( $pid, 'project_category' );
				if ( $terms && ! is_wp_error( $terms ) ) {
					$extra = '<div class="head-meta">';
					foreach ( $terms as $t ) {
						$extra .= '<a class="readout" href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a>';
					}
					$extra .= '</div>';
				}
			}
		}
	} elseif ( is_home() ) {
		$blog  = (int) get_option( 'page_for_posts' );
		$title = $blog ? get_the_title( $blog ) : __( 'Blog', 'jat-signal' );
		if ( '' === $lede && $blog && has_excerpt( $blog ) ) {
			$lede = get_post_field( 'post_excerpt', $blog );
		}
	} elseif ( is_search() ) {
		$title = __( 'Search', 'jat-signal' );
		global $wp_query;
		/* translators: 1: number of results, 2: search terms */
		$lede = sprintf( _n( '%1$d result for “%2$s”.', '%1$d results for “%2$s”.', (int) $wp_query->found_posts, 'jat-signal' ), (int) $wp_query->found_posts, get_search_query() );
	} elseif ( is_404() ) {
		$title = __( 'Page not found', 'jat-signal' );
		$lede  = '' === $lede ? __( 'The page you’re looking for doesn’t exist or has moved. Try the search below, or start from one of these pages.', 'jat-signal' ) : $lede;
	} elseif ( is_archive() ) {
		$title = wp_strip_all_tags( get_the_archive_title() );
		if ( '' === $lede ) {
			$lede = wp_strip_all_tags( get_the_archive_description() );
		}
		if ( '' === $lede && is_post_type_archive() ) {
			$obj  = get_queried_object();
			$lede = $obj->description ?? '';
		}
		if ( is_tax( 'service_category' ) && ! $channel ) {
			$channel = jat_channel_key( (string) get_queried_object()->slug );
		}
	} else {
		$title = __( 'Page title', 'jat-signal' );
	}
}

$crumbs = ( ! isset( $attributes['showCrumbs'] ) || $attributes['showCrumbs'] ) && ! is_front_page() ? 'auto' : array();
echo jat_page_head( array( // phpcs:ignore -- escaped in helper.
	'title'   => $title,
	'lede'    => $lede,
	'crumbs'  => $crumbs,
	'channel' => $channel,
	'wave'    => (string) ( $attributes['wave'] ?? '' ),
	'extra'   => $extra . ( $channel && $extra ? '<div class="tag-list">' . jat_ch_tag( $channel ) . '</div>' : '' ),
	'wrapper' => get_block_wrapper_attributes( array( 'class' => 'page-head' ) ),
) );
