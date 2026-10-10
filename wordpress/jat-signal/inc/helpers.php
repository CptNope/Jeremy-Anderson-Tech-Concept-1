<?php
/**
 * Shared markup helpers.
 *
 * Blocks and templates render through these so every surface uses the same
 * vocabulary: channel identifiers, readouts, price rows, work cards, post rows.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline icons. All are decorative (aria-hidden); the surrounding text carries the meaning.
 */
function jat_icon( string $name ): string {
	$p = array(
		'arrow' => '<svg class="arrow" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M3 8h10M9 4l4 4-4 4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'ext'   => '<svg class="ext" viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M9 3h4v4M13 3 7 9M11 9v3.5a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7a.5.5 0 0 1 .5-.5H7" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'check' => '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M3 8.5 6.5 12 13 4.5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>',
		'cross' => '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4.5 4.5l7 7M11.5 4.5l-7 7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'part'  => '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M4 8h8" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'plus'  => '<svg viewBox="0 0 18 18" aria-hidden="true" focusable="false"><path d="M9 3v12M3 9h12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'menu'  => '<svg viewBox="0 0 18 18" aria-hidden="true" focusable="false"><path d="M2 5h14M2 9h14M2 13h14" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>',
		'pin'   => '<svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M8 14.5s4.5-4.2 4.5-7.6A4.5 4.5 0 0 0 3.5 6.9c0 3.4 4.5 7.6 4.5 7.6Z" fill="none" stroke="currentColor" stroke-width="1.4"/><circle cx="8" cy="6.8" r="1.6" fill="currentColor"/></svg>',
		'github'   => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.49.5.09.68-.22.68-.48v-1.7c-2.78.6-3.37-1.34-3.37-1.34-.45-1.16-1.11-1.46-1.11-1.46-.91-.62.07-.61.07-.61 1 .07 1.53 1.03 1.53 1.03.89 1.53 2.34 1.09 2.91.83.09-.65.35-1.09.64-1.34-2.22-.25-4.56-1.11-4.56-4.94 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.03A9.56 9.56 0 0 1 12 6.84c.85 0 1.71.11 2.5.34 1.91-1.3 2.75-1.03 2.75-1.03.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.84-2.34 4.69-4.57 4.94.36.31.68.92.68 1.85v2.74c0 .27.18.58.69.48A10.01 10.01 0 0 0 22 12c0-5.52-4.48-10-10-10z"/></svg>',
		'linkedin' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M19.7 3H4.3A1.3 1.3 0 0 0 3 4.3v15.4A1.3 1.3 0 0 0 4.3 21h15.4a1.3 1.3 0 0 0 1.3-1.3V4.3A1.3 1.3 0 0 0 19.7 3zM8.34 18.34H5.67V9.75h2.67v8.59zM7 8.57a1.55 1.55 0 1 1 0-3.1 1.55 1.55 0 0 1 0 3.1zm11.34 9.77h-2.67v-4.18c0-1-.02-2.28-1.39-2.28-1.39 0-1.6 1.09-1.6 2.21v4.25h-2.67V9.75h2.56v1.17h.04a2.8 2.8 0 0 1 2.52-1.39c2.7 0 3.2 1.78 3.2 4.1v4.71z"/></svg>',
		'x'        => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.98 10.62 20.54 3h-1.55l-5.7 6.62L8.75 3H3.5l6.88 10.01L3.5 21h1.55l6.01-6.99L15.87 21h5.25l-7.14-10.38zm-2.13 2.48-.7-1L5.65 4.16h2.39l4.47 6.4.7 1 5.82 8.32h-2.39l-4.75-6.79z"/></svg>',
		'email'    => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="none" stroke="currentColor" stroke-width="1.6" d="M3.5 6.5h17v11h-17z"/><path fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round" d="m3.5 7 8.5 6.5L20.5 7"/></svg>',
	);
	return $p[ $name ] ?? '';
}

/**
 * The four service channels. Channel colors identify a channel and nothing else.
 * Each channel maps to the service category and badge type already used by the Services post type.
 */
function jat_channels(): array {
	return apply_filters( 'jat_channels', array(
		'ch1' => array( 'id' => 'CH1', 'name' => __( 'Platforms', 'jat-signal' ), 'var' => '--ch1', 'wave' => 'step', 'slug' => 'platforms', 'category' => 'wordpress-packages', 'badge' => 'wp', 'unit' => __( 'fixed fee', 'jat-signal' ), 'summary' => __( 'Sites & apps', 'jat-signal' ) ),
		'ch2' => array( 'id' => 'CH2', 'name' => __( 'Intelligence', 'jat-signal' ), 'var' => '--ch2', 'wave' => 'burst', 'slug' => 'intelligence', 'category' => 'ai-packages', 'badge' => 'ai', 'unit' => __( 'fixed fee', 'jat-signal' ), 'summary' => __( 'AI & automation', 'jat-signal' ) ),
		'ch3' => array( 'id' => 'CH3', 'name' => __( 'Security', 'jat-signal' ), 'var' => '--ch3', 'wave' => 'pulse', 'slug' => 'security', 'category' => 'security-packages', 'badge' => 'sec', 'unit' => __( 'fixed fee', 'jat-signal' ), 'summary' => __( 'Audit & harden', 'jat-signal' ) ),
		'ch4' => array( 'id' => 'CH4', 'name' => __( 'Operations', 'jat-signal' ), 'var' => '--ch4', 'wave' => 'sine', 'slug' => 'operations', 'category' => 'monthly-retainers', 'badge' => 'maint', 'unit' => __( 'per month', 'jat-signal' ), 'summary' => __( 'Hosting & care', 'jat-signal' ) ),
	) );
}

/** Channel key for a legacy badge type (wp, ai, sec, maint) or a service category slug. */
function jat_channel_key( string $badge_or_category ): string {
	foreach ( jat_channels() as $key => $ch ) {
		if ( $badge_or_category === $ch['badge'] || $badge_or_category === $ch['category'] || $badge_or_category === $key ) {
			return $key;
		}
	}
	return '';
}

/** Channel key for a service post: its badge type first, then its category. */
function jat_service_channel( int $post_id ): string {
	$key = jat_channel_key( (string) get_post_meta( $post_id, 'service_badge_type', true ) );
	if ( $key ) {
		return $key;
	}
	$terms = get_the_terms( $post_id, 'service_category' );
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$key = jat_channel_key( $t->slug );
			if ( $key ) {
				return $key;
			}
		}
	}
	return '';
}

/** Channel identifier tag: dot, CH number, name. */
function jat_ch_tag( string $key ): string {
	$chs = jat_channels();
	if ( empty( $chs[ $key ] ) ) {
		return '';
	}
	$c = $chs[ $key ];
	return sprintf( '<span class="ch-tag" style="--c:var(%1$s)"><i></i><b>%2$s</b> %3$s</span>', esc_attr( $c['var'] ), esc_html( $c['id'] ), esc_html( $c['name'] ) );
}

/** Resolve a stored URL: site-relative paths become absolute, everything else is escaped as-is. */
function jat_url( $url ): string {
	$url = trim( (string) $url );
	if ( '' === $url ) {
		return '';
	}
	if ( str_starts_with( $url, '/' ) && ! str_starts_with( $url, '//' ) ) {
		return esc_url( home_url( $url ) );
	}
	if ( str_starts_with( $url, '#' ) ) {
		return esc_attr( $url );
	}
	return esc_url( $url );
}

/** True for links that leave the site (gets an external icon and rel=noopener). */
function jat_is_external( $url ): bool {
	$url = (string) $url;
	if ( ! preg_match( '#^https?://#i', $url ) ) {
		return false;
	}
	return wp_parse_url( $url, PHP_URL_HOST ) !== wp_parse_url( home_url(), PHP_URL_HOST );
}

/** Permalink of a published page by path, or '' when it does not exist or is not public yet. */
function jat_page_url( string $path ): string {
	$page = get_page_by_path( $path );
	if ( ! $page || 'publish' !== $page->post_status ) {
		return '';
	}
	return get_permalink( $page );
}

/** Public contact address (JAT Signal screen › Public email). */
function jat_email(): string {
	$email = get_option( 'jat_public_email', 'info@jeremyanderson.tech' );
	return sanitize_email( apply_filters( 'jat_public_email', $email ) );
}

/** Where "Book a call" goes: the Contact page once it is published, otherwise email. */
function jat_contact_url(): string {
	$url = jat_page_url( 'contact' );
	return $url ? $url : 'mailto:' . jat_email();
}

/** Plain heading text. The old gradient "highlight" span is dropped: emphasis comes from the type role. */
function jat_heading_text( $heading, $highlight = '', bool $append = false ): string {
	$heading   = trim( wp_strip_all_tags( (string) $heading ) );
	$highlight = trim( wp_strip_all_tags( (string) $highlight ) );
	if ( $append && '' !== $highlight && false === stripos( $heading, $highlight ) ) {
		$heading = trim( $heading . ' ' . $highlight );
	}
	return $heading;
}

/** Numeric value of a stored price string ("$3,500", "from $8,500", "$350/mo", "$15,000+"). */
function jat_price_value( $price ): ?float {
	if ( ! preg_match( '/([0-9][0-9,]*(?:\.[0-9]+)?)/', (string) $price, $m ) ) {
		return null;
	}
	return (float) str_replace( ',', '', $m[1] );
}

/** Format a number as a price ("$3,500"). */
function jat_price_format( float $value ): string {
	return '$' . number_format_i18n( $value, floor( $value ) == $value ? 0 : 2 );
}

/**
 * Lowest published price in a service category, formatted for the channel strip ("from $3,500").
 * Read live from the Services post type, so editing a service updates every channel summary.
 */
function jat_category_from_price( string $category ): string {
	$cache = get_transient( 'jat_from_prices' );
	if ( ! is_array( $cache ) ) {
		$cache = array();
	}
	if ( isset( $cache[ $category ] ) ) {
		return $cache[ $category ];
	}
	$ids = get_posts( array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => 50,
		'fields'         => 'ids',
		'no_found_rows'  => true,
		'tax_query'      => array( array( 'taxonomy' => 'service_category', 'field' => 'slug', 'terms' => $category ) ),
	) );
	$min = null;
	$monthly = false;
	foreach ( $ids as $id ) {
		$raw = (string) get_post_meta( $id, 'service_price', true );
		$v   = jat_price_value( $raw );
		if ( null !== $v && ( null === $min || $v < $min ) ) {
			$min     = $v;
			$monthly = (bool) preg_match( '#/\s*mo#i', $raw );
		}
	}
	$out = null === $min ? '' : sprintf( /* translators: %s: price */ __( 'from %s', 'jat-signal' ), jat_price_format( $min ) . ( $monthly ? '/mo' : '' ) );
	$cache[ $category ] = $out;
	set_transient( 'jat_from_prices', $cache, DAY_IN_SECONDS );
	return $out;
}
add_action( 'save_post_service', static fn() => delete_transient( 'jat_from_prices' ) );
add_action( 'set_object_terms', static fn() => delete_transient( 'jat_from_prices' ) );

/** Readout: mono, uppercase label. Long strings drop the caps so they read as text. */
function jat_readout( $text, string $extra_class = '', string $tag = 'span' ): string {
	$text = (string) $text;
	if ( '' === trim( $text ) ) {
		return '';
	}
	$long = mb_strlen( wp_strip_all_tags( $text ) ) > 34 ? ' long' : '';
	return sprintf( '<%1$s class="readout%2$s%3$s">%4$s</%1$s>', tag_escape( $tag ), $long, $extra_class ? ' ' . esc_attr( $extra_class ) : '', esc_html( $text ) );
}

/** Section head: heading on the left, lede on the right (stacks on narrow screens). */
function jat_section_head( string $heading, string $lede = '', string $id = '', string $link = '', string $link_text = '' ): string {
	if ( '' === $heading && '' === $lede ) {
		return '';
	}
	$out  = '<div class="section-head">';
	$out .= '<h2' . ( $id ? ' id="' . esc_attr( $id ) . '"' : '' ) . '>' . esc_html( $heading ) . '</h2>';
	if ( $lede || $link ) {
		$out .= '<div class="section-head-side">';
		if ( $lede ) {
			$out .= '<p class="lede">' . esc_html( $lede ) . '</p>';
		}
		if ( $link && $link_text ) {
			$out .= jat_link_arrow( $link, $link_text );
		}
		$out .= '</div>';
	}
	return $out . '</div>';
}

/** Text link with a trailing arrow (or external icon). */
function jat_link_arrow( $url, $text, string $class = 'link-arrow' ): string {
	$text = trim( preg_replace( '/\s*[→↗›»]+\s*$/u', '', wp_strip_all_tags( (string) $text ) ) );
	if ( '' === $text || '' === trim( (string) $url ) ) {
		return '';
	}
	$ext = jat_is_external( $url );
	return sprintf(
		'<a class="%1$s" href="%2$s"%3$s>%4$s%5$s%6$s</a>',
		esc_attr( $class ),
		jat_url( $url ),
		$ext ? ' rel="noopener"' : '',
		esc_html( $text ),
		$ext ? '<span class="vh"> ' . esc_html__( '(opens external site)', 'jat-signal' ) . '</span>' : '',
		jat_icon( $ext ? 'ext' : 'arrow' )
	);
}

/** Button. Old content often ends labels with arrows ("Get In Touch ↗"); the icon replaces them. */
function jat_button( $url, $text, string $variant = 'primary', string $extra = '' ): string {
	$text = trim( preg_replace( '/\s*[→↗›»]+\s*$/u', '', wp_strip_all_tags( (string) $text ) ) );
	if ( '' === $text || '' === trim( (string) $url ) ) {
		return '';
	}
	$ext  = jat_is_external( $url );
	$icon = 'primary' === $variant ? jat_icon( $ext ? 'ext' : 'arrow' ) : '';
	return sprintf( '<a class="btn btn-%1$s%2$s" href="%3$s"%4$s>%5$s%6$s</a>', esc_attr( $variant ), $extra ? ' ' . esc_attr( $extra ) : '', jat_url( $url ), $ext ? ' rel="noopener"' : '', esc_html( $text ), $icon );
}

/** Email line with a copy button. */
function jat_copy_line( string $email = '', string $label = '' ): string {
	$email = $email ? $email : jat_email();
	if ( ! $email ) {
		return '';
	}
	return '<div class="copy-line">' . ( $label ? jat_readout( $label ) : '' ) . '<a class="mono" href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a><button class="btn-copy" type="button" data-copy-text="' . esc_attr( antispambot( $email ) ) . '">' . esc_html__( 'Copy', 'jat-signal' ) . '</button></div>';
}

/** Unique, stable-enough id for aria-labelledby inside a request. */
function jat_id( string $prefix = 'jat' ): string {
	return wp_unique_id( $prefix . '-' );
}

/** Decode a JSON string attribute into an array. */
function jat_json( $raw ): array {
	if ( is_array( $raw ) ) {
		return $raw;
	}
	$v = json_decode( (string) $raw, true );
	return is_array( $v ) ? $v : array();
}

/** Excerpt for cards: the excerpt field, else trimmed content. */
function jat_excerpt( $post, int $words = 24 ): string {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$text = has_excerpt( $post ) ? $post->post_excerpt : wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( $post->post_content ) ) );
	return wp_trim_words( $text, $words, '…' );
}

/**
 * Schematic plate used when a project has no featured image: a graticule screen
 * with a trace in the given color. Purely decorative.
 */
function jat_schematic( string $seed, string $color_var = '--ch1' ): string {
	$h   = crc32( $seed );
	$pts = array();
	for ( $x = 0; $x <= 320; $x += 8 ) {
		$y     = 90 + 38 * sin( $x / ( 28 + ( $h % 17 ) ) + ( $h % 7 ) ) + 14 * sin( $x / 9.5 + ( $h % 5 ) );
		$pts[] = $x . ',' . round( $y, 1 );
	}
	return '<svg class="schematic" viewBox="0 0 320 180" preserveAspectRatio="none" aria-hidden="true" focusable="false"><polyline points="' . esc_attr( implode( ' ', $pts ) ) . '" fill="none" stroke="var(' . esc_attr( $color_var ) . ')" stroke-width="1.6" vector-effect="non-scaling-stroke" stroke-linejoin="round"/></svg>';
}

/** Project plate: featured image, or the schematic fallback. */
function jat_project_plate( WP_Post $post, string $size = 'large', string $loading = 'lazy' ): string {
	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, array(
			'loading' => $loading,
			'alt'     => trim( (string) get_post_meta( get_post_thumbnail_id( $post ), '_wp_attachment_image_alt', true ) ) ?: sprintf( /* translators: %s project title */ __( '%s interface', 'jat-signal' ), get_the_title( $post ) ),
		) );
	}
	return '<div class="plate-schematic graticule">' . jat_schematic( $post->post_name ) . '</div>';
}

/** True when a project has a public repository or live link. */
function jat_project_is_open( int $post_id ): bool {
	return '' !== trim( (string) get_post_meta( $post_id, 'project_github_url', true ) ) || '' !== trim( (string) get_post_meta( $post_id, 'project_live_url', true ) );
}

/** Term chips (mono, small) for a post, linking to the archive. */
function jat_term_chips( int $post_id, string $taxonomy, int $limit = 6, bool $link = true ): string {
	$terms = get_the_terms( $post_id, $taxonomy );
	if ( ! $terms || is_wp_error( $terms ) ) {
		return '';
	}
	$out   = '<ul class="chips" aria-label="' . esc_attr( get_taxonomy( $taxonomy )->labels->name ?? '' ) . '">';
	$shown = array_slice( $terms, 0, $limit );
	foreach ( $shown as $t ) {
		$out .= '<li>' . ( $link ? '<a href="' . esc_url( get_term_link( $t ) ) . '">' . esc_html( $t->name ) . '</a>' : esc_html( $t->name ) ) . '</li>';
	}
	if ( count( $terms ) > $limit ) {
		$out .= '<li class="more">+' . ( count( $terms ) - $limit ) . '</li>';
	}
	return $out . '</ul>';
}

/** Work card for a project (used by listings, featured projects and related work). */
function jat_project_card( WP_Post $post, int $heading_level = 3 ): string {
	$sub   = (string) get_post_meta( $post->ID, 'project_subtitle', true );
	$h     = 'h' . max( 2, min( 4, $heading_level ) );
	$out   = '<article class="work-card">';
	$out  .= '<div class="plate">' . jat_project_plate( $post, 'medium_large' ) . '</div>';
	$out  .= '<div class="body">';
	$out  .= '<' . $h . '><a class="card-link" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></' . $h . '>';
	if ( $sub ) {
		$out .= '<p class="sub">' . esc_html( $sub ) . '</p>';
	}
	$ex = jat_excerpt( $post, 22 );
	if ( $ex ) {
		$out .= '<p>' . esc_html( $ex ) . '</p>';
	}
	$out .= jat_term_chips( $post->ID, 'project_tag', 4, false );
	$out .= '<span class="more">' . ( jat_project_is_open( $post->ID ) ? esc_html__( 'Case study', 'jat-signal' ) : esc_html__( 'Overview', 'jat-signal' ) ) . jat_icon( 'arrow' ) . '</span>';
	$out .= '</div></article>';
	return $out;
}

/** Post row for listings ("Field notes" style). */
function jat_post_card( WP_Post $post, int $heading_level = 3 ): string {
	$h    = 'h' . max( 2, min( 4, $heading_level ) );
	$cats = get_the_category( $post->ID );
	$out  = '<article class="post">';
	$out .= '<' . $h . '><a class="card-link" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></' . $h . '>';
	$ex   = (string) get_post_meta( $post->ID, 'post_subtitle', true );
	$ex   = $ex ? $ex : jat_excerpt( $post, 24 );
	if ( $ex ) {
		$out .= '<p>' . esc_html( $ex ) . '</p>';
	}
	$out .= '<div class="meta">';
	if ( $cats ) {
		$out .= jat_readout( $cats[0]->name );
	}
	$out .= '<time class="readout" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( 'M j, Y', $post ) ) . '</time>';
	$out .= '</div><span class="post-more" aria-hidden="true">' . esc_html__( 'Read', 'jat-signal' ) . jat_icon( 'arrow' ) . '</span></article>';
	return $out;
}

/**
 * Price row for a service post. Data comes straight from the existing service meta.
 *
 * @param array $opts features (bool) — add an expandable "What's included" list; tag (bool) — channel tag.
 */
function jat_service_row( WP_Post $post, array $opts = array() ): string {
	$opts     = wp_parse_args( $opts, array( 'features' => true, 'tag' => true, 'desc' => true, 'link' => true, 'heading' => 'h3' ) );
	$id       = $post->ID;
	$price    = (string) get_post_meta( $id, 'service_price', true );
	$price    = $price ? $price : (string) get_post_meta( $id, 'service_price_range', true );
	$note     = (string) get_post_meta( $id, 'service_price_note', true );
	$tier     = (string) get_post_meta( $id, 'service_tier', true );
	$featured = (string) get_post_meta( $id, 'service_featured', true );
	$hours    = (string) get_post_meta( $id, 'service_hours_included', true );
	$features = jat_json( get_post_meta( $id, 'service_features', true ) );
	$ch       = jat_service_channel( $id );
	$popular  = '';
	if ( $featured ) {
		$popular = ( $tier && preg_match( '/most|popular|request/i', $tier ) ) ? $tier : __( 'Most popular', 'jat-signal' );
	}
	$h    = in_array( $opts['heading'], array( 'h2', 'h3', 'h4' ), true ) ? $opts['heading'] : 'h3';
	$out  = '<div class="price-row">';
	$out .= '<div class="name"><' . $h . ' class="price-name">' . ( $opts['link'] ? '<a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>' : esc_html( get_the_title( $post ) ) ) . '</' . $h . '>';
	if ( $popular ) {
		$out .= '<span class="popular">' . esc_html( $popular ) . '</span>';
	}
	if ( $opts['tag'] && $ch ) {
		$out .= jat_ch_tag( $ch );
	}
	$out .= '</div>';
	$desc = $opts['desc'] ? jat_excerpt( $post, 40 ) : '';
	$out .= '<div class="desc">';
	if ( $desc ) {
		$out .= '<p>' . esc_html( $desc ) . '</p>';
	}
	if ( $hours ) {
		$out .= '<p class="hours">' . esc_html( $hours ) . '</p>';
	}
	if ( $opts['features'] && $features ) {
		$out .= '<details class="incl"><summary>' . esc_html__( 'What’s included', 'jat-signal' ) . jat_icon( 'plus' ) . '</summary><ul class="check-list">';
		foreach ( $features as $f ) {
			$out .= '<li>' . jat_icon( 'check' ) . '<span>' . esc_html( $f ) . '</span></li>';
		}
		$out .= '</ul></details>';
	}
	$out .= '</div>';
	$out .= '<span class="amt">' . esc_html( $price ) . '</span>';
	$out .= '<span class="time">' . esc_html( $note ) . '</span>';
	$out .= '</div>';
	return $out;
}

/** Services in a category, ordered by the existing service_sort_order meta. */
function jat_services_in( string $category, int $limit = 20 ): array {
	if ( '' === $category ) {
		return array();
	}
	$q = new WP_Query( array(
		'post_type'      => 'service',
		'post_status'    => 'publish',
		'posts_per_page' => $limit,
		'no_found_rows'  => true,
		'meta_key'       => 'service_sort_order',
		'orderby'        => array( 'meta_value_num' => 'ASC', 'date' => 'ASC' ),
		'tax_query'      => array( array( 'taxonomy' => 'service_category', 'field' => 'slug', 'terms' => $category ) ),
	) );
	$posts = $q->posts;
	if ( ! $posts ) {
		// Services without a sort order are excluded by meta_key ordering; fall back to date.
		$posts = get_posts( array( 'post_type' => 'service', 'posts_per_page' => $limit, 'tax_query' => array( array( 'taxonomy' => 'service_category', 'field' => 'slug', 'terms' => $category ) ) ) );
	}
	return $posts;
}

/** Posts by id list (keeps the given order), or the latest N of a type. */
function jat_pick_posts( string $type, $ids_json, int $count ): array {
	$ids = array_filter( array_map( 'intval', jat_json( $ids_json ) ) );
	$args = array( 'post_type' => $type, 'post_status' => 'publish', 'no_found_rows' => true, 'ignore_sticky_posts' => true );
	if ( $ids ) {
		$args += array( 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count( $ids ) );
	} else {
		$args += array( 'posts_per_page' => max( 1, $count ), 'orderby' => 'date', 'order' => 'DESC' );
		if ( is_singular( $type ) ) { // "More work" on a project page never lists the project itself.
			$args['post__not_in'] = array( get_queried_object_id() );
		}
	}
	return get_posts( $args );
}

/** Pagination markup for a custom query. */
function jat_pagination( int $total, int $current, string $label ): string {
	if ( $total < 2 ) {
		return '';
	}
	$links = paginate_links( array(
		'total'     => $total,
		'current'   => $current,
		'type'      => 'array',
		'prev_text' => '<span class="vh">' . esc_html__( 'Previous page', 'jat-signal' ) . '</span><span aria-hidden="true">←</span>',
		'next_text' => '<span class="vh">' . esc_html__( 'Next page', 'jat-signal' ) . '</span><span aria-hidden="true">→</span>',
	) );
	if ( ! $links ) {
		return '';
	}
	return '<nav class="pager" aria-label="' . esc_attr( $label ) . '"><ul><li>' . implode( '</li><li>', $links ) . '</li></ul></nav>';
}

/** Current page number for blocks that paginate inside a page. */
function jat_paged(): int {
	return max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
}

/**
 * Page head: breadcrumbs, H1, lede, optional extra markup, and a channel waveform on wide screens.
 *
 * @param array $a title, lede, crumbs ('auto' | array of [label,url]), channel (ch1–ch4), wave, extra, id, class.
 */
function jat_page_head( array $a ): string {
	$a = wp_parse_args( $a, array( 'title' => '', 'lede' => '', 'crumbs' => 'auto', 'channel' => '', 'wave' => '', 'extra' => '', 'id' => '', 'class' => '', 'wrapper' => '' ) );
	$chs  = jat_channels();
	$ch   = $chs[ $a['channel'] ] ?? null;
	$var  = $ch ? $ch['var'] : '--ch1';
	$wave = $a['wave'] ? $a['wave'] : ( $ch ? $ch['wave'] : 'step' );
	$out  = '<header ' . ( $a['wrapper'] ? $a['wrapper'] : 'class="page-head' . ( $a['class'] ? ' ' . esc_attr( $a['class'] ) : '' ) . '"' ) . '>';
	$out .= '<div class="graticule" aria-hidden="true"></div>';
	$out .= '<canvas class="head-trace" data-wave="' . esc_attr( $wave ) . '" data-color="' . esc_attr( $var ) . '" aria-hidden="true"></canvas>';
	$out .= '<div class="wrap">';
	$crumbs = 'auto' === $a['crumbs'] ? jat_breadcrumbs() : (array) $a['crumbs'];
	if ( $crumbs ) {
		$out .= '<ol class="crumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'jat-signal' ) . '"><li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'jat-signal' ) . '</a></li>';
		$last = count( $crumbs ) - 1;
		foreach ( array_values( $crumbs ) as $i => $c ) {
			$label = is_array( $c ) ? ( $c['label'] ?? $c[0] ?? '' ) : (string) $c;
			$url   = is_array( $c ) ? ( $c['url'] ?? $c[1] ?? '' ) : '';
			$out  .= ( $i === $last || ! $url ) ? '<li aria-current="page">' . esc_html( $label ) . '</li>' : '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		}
		$out .= '</ol>';
	}
	$out .= '<h1' . ( $a['id'] ? ' id="' . esc_attr( $a['id'] ) . '"' : '' ) . '>' . esc_html( wp_strip_all_tags( (string) $a['title'] ) ) . '</h1>';
	if ( '' !== trim( (string) $a['lede'] ) ) {
		$out .= '<p class="lede">' . esc_html( wp_strip_all_tags( (string) $a['lede'] ) ) . '</p>';
	}
	if ( $ch && '' === $a['extra'] ) {
		$out .= '<div class="tag-list">' . jat_ch_tag( $a['channel'] ) . '</div>';
	}
	$out .= $a['extra'];
	return $out . '</div></header>';
}

/** Where a channel links: its own page once published (Services › Platforms…), else the Services page. */
function jat_channel_url( string $key ): string {
	$chs = jat_channels();
	if ( empty( $chs[ $key ] ) ) {
		return '';
	}
	$url = jat_page_url( 'services/' . $chs[ $key ]['slug'] );
	if ( ! $url ) {
		$url = jat_page_url( $chs[ $key ]['slug'] );
	}
	if ( ! $url ) {
		$services = jat_page_url( 'services' );
		$url      = $services ? $services . '#' . $chs[ $key ]['category'] : home_url( '/' );
	}
	return $url;
}

/** The four-channel strip under the hero, with live "from" prices. */
function jat_channel_strip(): string {
	$out = '<nav class="strip" aria-label="' . esc_attr__( 'Service channels', 'jat-signal' ) . '">';
	foreach ( jat_channels() as $key => $c ) {
		$from = jat_category_from_price( $c['category'] );
		$out .= sprintf(
			'<a href="%1$s" style="--c:var(%2$s)"><span class="v"><span class="id">%3$s</span> %4$s</span><span class="p">%5$s</span></a>',
			esc_url( jat_channel_url( $key ) ),
			esc_attr( $c['var'] ),
			esc_html( $c['id'] ),
			esc_html( $c['name'] ),
			esc_html( trim( $c['summary'] . ( $from ? ' · ' . $from : '' ) ) )
		);
	}
	return $out . '</nav>';
}

/** Post as a full-width row: date, then title, subtitle or excerpt, category (blog listing, archives, search). */
function jat_post_row( WP_Post $post, int $heading_level = 2 ): string {
	$h    = 'h' . max( 2, min( 4, $heading_level ) );
	$type = get_post_type( $post );
	$out  = '<article class="post-row">';
	$out .= '<time class="readout" datetime="' . esc_attr( get_the_date( 'c', $post ) ) . '">' . esc_html( get_the_date( 'M j, Y', $post ) ) . '</time>';
	$out .= '<div class="post-row-body"><' . $h . '><a class="card-link" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></' . $h . '>';
	$sub  = 'post' === $type ? (string) get_post_meta( $post->ID, 'post_subtitle', true ) : ( 'project' === $type ? (string) get_post_meta( $post->ID, 'project_subtitle', true ) : '' );
	$ex   = $sub ? $sub : jat_excerpt( $post, 28 );
	if ( $ex ) {
		$out .= '<p>' . esc_html( $ex ) . '</p>';
	}
	$out .= '</div><div class="post-row-meta">';
	if ( 'post' === $type ) {
		$cats = get_the_category( $post->ID );
		if ( $cats ) {
			$out .= jat_readout( $cats[0]->name );
		}
	} else {
		$obj  = get_post_type_object( $type );
		$out .= jat_readout( $obj ? $obj->labels->singular_name : $type );
	}
	$out .= jat_icon( 'arrow' ) . '</div></article>';
	return $out;
}

/** Category links for the blog (only categories that have posts). */
function jat_category_filter(): string {
	$cats = get_categories( array( 'hide_empty' => true, 'orderby' => 'count', 'order' => 'DESC', 'number' => 8 ) );
	if ( count( $cats ) < 2 ) {
		return '';
	}
	$current = is_category() ? get_queried_object_id() : 0;
	$blog    = (int) get_option( 'page_for_posts' );
	$out     = '<nav class="filter" aria-label="' . esc_attr__( 'Categories', 'jat-signal' ) . '"><div class="wrap"><ul>';
	$out    .= '<li><a href="' . esc_url( $blog ? get_permalink( $blog ) : home_url( '/' ) ) . '"' . ( $current ? '' : ' aria-current="page"' ) . '>' . esc_html__( 'All', 'jat-signal' ) . '</a></li>';
	foreach ( $cats as $c ) {
		$out .= '<li><a href="' . esc_url( get_category_link( $c ) ) . '"' . ( $current === $c->term_id ? ' aria-current="page"' : '' ) . '>' . esc_html( $c->name ) . ' <span class="n">' . (int) $c->count . '</span></a></li>';
	}
	return $out . '</ul></div></nav>';
}

/**
 * The brand sprite is printed once at wp_body_open on the front end. Editor previews render
 * through the REST API and have no body, so blocks that use the sprite print it themselves there.
 */
function jat_editor_sprite(): string {
	static $done = false;
	if ( $done || ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return '';
	}
	$done = true;
	ob_start();
	include JAT_DIR . '/assets/images/sprite.svg.php';
	return (string) ob_get_clean();
}

/** Stand-in for a missing portrait: the JAT mark on the graticule. */
function jat_portrait_placeholder(): string {
	return '<div class="ph" aria-hidden="true">' . jat_editor_sprite() . '<svg viewBox="1 6 108 68" focusable="false"><use href="#mk"/></svg></div>';
}
