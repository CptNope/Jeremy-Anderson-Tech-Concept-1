<?php
/**
 * Structured data, social meta, robots.txt and security headers.
 *
 * Same output as Developer Portfolio, with one change: when an SEO plugin is active
 * (All in One SEO, Yoast, Rank Math, SEOPress, The SEO Framework), the theme stops printing
 * what the plugin already prints (description, canonical, Open Graph, Twitter, WebSite,
 * Person, BreadcrumbList, Article) so pages don't carry duplicate canonicals or schema.
 * Schema the plugins don't produce — projects (SoftwareApplication), services (Service),
 * listings (ItemList) and FAQs (FAQPage) — is always printed.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

remove_action( 'wp_head', 'wp_generator' );

/** True when a dedicated SEO plugin is handling meta tags and core schema. */
function jat_seo_plugin_active(): bool {
	$active = defined( 'AIOSEO_VERSION' ) || function_exists( 'aioseo' )
		|| defined( 'WPSEO_VERSION' )
		|| class_exists( 'RankMath' ) || defined( 'RANK_MATH_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
	return (bool) apply_filters( 'jat_seo_plugin_active', $active );
}

function jat_owner_name(): string {
	$name = apply_filters( 'developer_portfolio_owner_name', '' );
	if ( $name ) {
		return $name;
	}
	$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'orderby' => 'ID', 'order' => 'ASC' ) );
	return $admins ? $admins[0]->display_name : get_bloginfo( 'name' );
}

function jat_ld( array $data ): void {
	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}

/** Structured data. */
add_action( 'wp_head', static function () {
	$site  = home_url( '/' );
	$owner = jat_owner_name();
	$seo   = jat_seo_plugin_active();

	if ( ! $seo ) {
		jat_ld( array(
			'@context'        => 'https://schema.org',
			'@type'           => 'WebSite',
			'name'            => get_bloginfo( 'name' ),
			'url'             => $site,
			'description'     => get_bloginfo( 'description' ),
			'publisher'       => array( '@type' => 'Person', 'name' => $owner ),
			'potentialAction' => array( '@type' => 'SearchAction', 'target' => $site . '?s={search_term_string}', 'query-input' => 'required name=search_term_string' ),
		) );

		$about = get_page_by_path( 'about' );
		if ( is_front_page() || ( $about && is_page( $about->ID ) ) ) {
			$person = array( '@context' => 'https://schema.org', '@type' => 'Person', 'name' => $owner, 'url' => $site, 'description' => get_bloginfo( 'description' ) );
			foreach ( array( 'email' => 'developer_portfolio_public_email', 'jobTitle' => 'developer_portfolio_job_title', 'sameAs' => 'developer_portfolio_social_links', 'knowsAbout' => 'developer_portfolio_knows_about' ) as $k => $filter ) {
				$v = apply_filters( $filter, 'sameAs' === $k || 'knowsAbout' === $k ? array() : '' );
				if ( ! empty( $v ) ) {
					$person[ $k ] = $v;
				}
			}
			jat_ld( $person );
		}
	}

	if ( is_singular( 'project' ) ) {
		$id     = get_the_ID();
		$schema = array(
			'@context'            => 'https://schema.org',
			'@type'               => 'SoftwareApplication',
			'name'                => get_the_title( $id ),
			'description'         => get_post_meta( $id, 'project_subtitle', true ) ?: jat_excerpt( $id, 30 ),
			'url'                 => get_permalink( $id ),
			'author'              => array( '@type' => 'Person', 'name' => $owner ),
			'applicationCategory' => 'WebApplication',
		);
		if ( has_post_thumbnail( $id ) ) {
			$schema['image'] = get_the_post_thumbnail_url( $id, 'full' );
		}
		if ( $live = get_post_meta( $id, 'project_live_url', true ) ) {
			$schema['installUrl'] = $live;
		}
		if ( $gh = get_post_meta( $id, 'project_github_url', true ) ) {
			$schema['codeRepository'] = $gh;
		}
		jat_ld( $schema );
	}

	if ( is_singular( 'service' ) ) {
		$id     = get_the_ID();
		$schema = array(
			'@context'    => 'https://schema.org',
			'@type'       => 'Service',
			'name'        => get_the_title( $id ),
			'description' => get_post_meta( $id, 'service_subtitle', true ) ?: jat_excerpt( $id, 30 ),
			'url'         => get_permalink( $id ),
			'provider'    => array( '@type' => 'Person', 'name' => $owner, 'url' => $site ),
		);
		$price = (string) get_post_meta( $id, 'service_price', true );
		$num   = jat_price_value( $price );
		if ( $price ) {
			$offer = array( '@type' => 'Offer', 'description' => trim( $price . ' ' . get_post_meta( $id, 'service_price_note', true ) ), 'availability' => 'https://schema.org/InStock', 'priceCurrency' => 'USD' );
			if ( null !== $num ) {
				$offer['price'] = $num;
			}
			$schema['offers'] = $offer;
		}
		$cats = get_the_terms( $id, 'service_category' );
		if ( $cats && ! is_wp_error( $cats ) ) {
			$schema['serviceType'] = wp_list_pluck( $cats, 'name' );
		}
		jat_ld( $schema );
	}

	if ( ! $seo && is_singular( 'post' ) ) {
		$id     = get_the_ID();
		$schema = array(
			'@context'      => 'https://schema.org',
			'@type'         => 'Article',
			'headline'      => get_the_title( $id ),
			'description'   => jat_excerpt( $id, 30 ),
			'url'           => get_permalink( $id ),
			'datePublished' => get_the_date( 'c', $id ),
			'dateModified'  => get_the_modified_date( 'c', $id ),
			'author'        => array( '@type' => 'Person', 'name' => $owner ),
			'publisher'     => array( '@type' => 'Person', 'name' => $owner ),
		);
		if ( has_post_thumbnail( $id ) ) {
			$schema['image'] = get_the_post_thumbnail_url( $id, 'full' );
		}
		jat_ld( $schema );
	}

	if ( ! $seo && ! is_front_page() ) {
		$items = array( array( '@type' => 'ListItem', 'position' => 1, 'name' => __( 'Home', 'jat-signal' ), 'item' => $site ) );
		foreach ( jat_breadcrumbs() as $i => $crumb ) {
			$item = array( '@type' => 'ListItem', 'position' => $i + 2, 'name' => $crumb['label'] );
			if ( $crumb['url'] ) {
				$item['item'] = $crumb['url'];
			}
			$items[] = $item;
		}
		jat_ld( array( '@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items ) );
	}

	// ItemList on the Projects and Services listings.
	$list_type = '';
	$projects  = get_page_by_path( 'projects' );
	$services  = get_page_by_path( 'services' );
	if ( ( $projects && is_page( $projects->ID ) ) || is_post_type_archive( 'project' ) ) {
		$list_type = 'project';
	} elseif ( ( $services && is_page( $services->ID ) ) || is_post_type_archive( 'service' ) ) {
		$list_type = 'service';
	}
	if ( $list_type ) {
		$posts = get_posts( array( 'post_type' => $list_type, 'posts_per_page' => 50, 'orderby' => 'date', 'order' => 'DESC' ) );
		if ( $posts ) {
			$items = array();
			foreach ( $posts as $i => $p ) {
				$items[] = array( '@type' => 'ListItem', 'position' => $i + 1, 'name' => get_the_title( $p ), 'url' => get_permalink( $p ) );
			}
			jat_ld( array( '@context' => 'https://schema.org', '@type' => 'ItemList', 'name' => 'project' === $list_type ? __( 'Projects', 'jat-signal' ) : __( 'Services', 'jat-signal' ), 'numberOfItems' => count( $items ), 'itemListElement' => $items ) );
		}
	}
}, 5 );

/** FAQPage schema for content using the [faq] shortcode. */
add_action( 'wp_footer', static function () {
	$post = get_post();
	if ( ! is_singular() || ! $post || ! has_shortcode( $post->post_content, 'faq' ) ) {
		return;
	}
	preg_match_all( '/\[faq\s+question="([^"]+)"\](.*?)\[\/faq\]/s', $post->post_content, $m, PREG_SET_ORDER );
	$items = array();
	foreach ( $m as $row ) {
		$items[] = array( '@type' => 'Question', 'name' => $row[1], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $row[2] ) ) );
	}
	if ( $items ) {
		jat_ld( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ) );
	}
}, 99 );

/** Description, canonical, Open Graph and Twitter tags (only without an SEO plugin). */
add_action( 'wp_head', static function () {
	if ( jat_seo_plugin_active() ) {
		return;
	}
	$type  = 'website';
	$image = '';
	if ( is_singular() ) {
		$id    = get_queried_object_id();
		$title = get_the_title( $id );
		$url   = get_permalink( $id );
		$image = (string) get_the_post_thumbnail_url( $id, 'full' );
		$type  = is_singular( 'post' ) ? 'article' : 'website';
		$desc  = jat_excerpt( $id, 30 );
		if ( ! $desc ) {
			$desc = get_bloginfo( 'description' );
		}
	} elseif ( is_post_type_archive() ) {
		$obj   = get_queried_object();
		$title = $obj->label ?? get_bloginfo( 'name' );
		$url   = get_post_type_archive_link( $obj->name ?? '' );
		$desc  = $obj->description ?? get_bloginfo( 'description' );
	} elseif ( is_front_page() || is_home() ) {
		$title = get_bloginfo( 'name' );
		$url   = is_home() && get_option( 'page_for_posts' ) ? get_permalink( (int) get_option( 'page_for_posts' ) ) : home_url( '/' );
		$desc  = get_bloginfo( 'description' );
	} else {
		$title = wp_get_document_title();
		$url   = '';
		$desc  = get_bloginfo( 'description' );
		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			$url  = get_term_link( $term );
			$desc = term_description( $term ) ? wp_strip_all_tags( term_description( $term ) ) : $desc;
		}
	}
	$title = wp_strip_all_tags( (string) $title );
	$desc  = wp_strip_all_tags( (string) $desc );
	if ( $desc ) {
		printf( '<meta name="description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url && ! is_wp_error( $url ) && ! is_singular() ) { // Core prints rel=canonical on singular views.
		printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
	}
	printf( '<meta property="og:type" content="%s">' . "\n", esc_attr( $type ) );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $url && ! is_wp_error( $url ) ) {
		printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	}
	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );
	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
		printf( '<meta property="og:image:alt" content="%s">' . "\n", esc_attr( $title ) );
	}
	printf( '<meta name="twitter:card" content="%s">' . "\n", $image ? 'summary_large_image' : 'summary' );
	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	if ( $desc ) {
		printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $desc ) );
	}
	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}
	if ( is_singular( 'post' ) ) {
		$id = get_queried_object_id();
		printf( '<meta property="article:published_time" content="%s">' . "\n", esc_attr( get_the_date( 'c', $id ) ) );
		printf( '<meta property="article:modified_time" content="%s">' . "\n", esc_attr( get_the_modified_date( 'c', $id ) ) );
	}
}, 2 );

/** Security headers (unchanged). */
add_action( 'send_headers', static function () {
	if ( is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()' );
} );

/** robots.txt (unchanged), unless an SEO plugin manages it. */
add_filter( 'robots_txt', static function ( $output, $public ) {
	if ( '0' === (string) $public || jat_seo_plugin_active() ) {
		return $output;
	}
	$site = home_url( '/' );
	return "User-agent: *\nAllow: /\nDisallow: /wp-admin/\nDisallow: /wp-includes/\nDisallow: /wp-content/plugins/\nDisallow: /wp-content/cache/\nDisallow: /wp-json/\nDisallow: /?s=\nDisallow: /search/\n\n# Sitemaps\nSitemap: {$site}sitemap.xml\nSitemap: {$site}wp-sitemap.xml\n";
}, 10, 2 );

/**
 * Breadcrumb trail for the current view (after "Home"). Used by the page head and by BreadcrumbList.
 *
 * @return array<int, array{label:string,url:string}>
 */
function jat_breadcrumbs(): array {
	$crumbs = array();
	$page   = static function ( string $path, string $fallback ) {
		$p = get_page_by_path( $path );
		return $p && 'publish' === $p->post_status ? array( 'label' => get_the_title( $p ), 'url' => get_permalink( $p ) ) : array( 'label' => $fallback, 'url' => '' );
	};
	if ( is_singular( 'project' ) ) {
		$crumbs[] = $page( 'projects', __( 'Projects', 'jat-signal' ) );
		$crumbs[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_singular( 'service' ) ) {
		$crumbs[] = $page( 'services', __( 'Services', 'jat-signal' ) );
		$crumbs[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_singular( 'post' ) ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = array( 'label' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
		}
		$crumbs[] = array( 'label' => get_the_title(), 'url' => '' );
	} elseif ( is_page() ) {
		$id = get_queried_object_id();
		foreach ( array_reverse( get_post_ancestors( $id ) ) as $anc ) {
			$crumbs[] = array( 'label' => get_the_title( $anc ), 'url' => get_permalink( $anc ) );
		}
		$crumbs[] = array( 'label' => get_the_title( $id ), 'url' => '' );
	} elseif ( is_home() ) {
		$blog     = (int) get_option( 'page_for_posts' );
		$crumbs[] = array( 'label' => $blog ? get_the_title( $blog ) : __( 'Blog', 'jat-signal' ), 'url' => '' );
	} elseif ( is_tax( array( 'project_category', 'project_tag' ) ) || is_post_type_archive( 'project' ) ) {
		$crumbs[] = $page( 'projects', __( 'Projects', 'jat-signal' ) );
		if ( ! is_post_type_archive() ) {
			$crumbs[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
		}
	} elseif ( is_tax( 'service_category' ) || is_post_type_archive( 'service' ) ) {
		$crumbs[] = $page( 'services', __( 'Services', 'jat-signal' ) );
		if ( ! is_post_type_archive() ) {
			$crumbs[] = array( 'label' => single_term_title( '', false ), 'url' => '' );
		}
	} elseif ( is_category() || is_tag() || is_date() || is_author() ) {
		$blog = (int) get_option( 'page_for_posts' );
		if ( $blog ) {
			$crumbs[] = array( 'label' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
		}
		$crumbs[] = array( 'label' => wp_strip_all_tags( get_the_archive_title() ), 'url' => '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( 'label' => __( 'Search', 'jat-signal' ), 'url' => '' );
	} elseif ( is_404() ) {
		$crumbs[] = array( 'label' => __( 'Not found', 'jat-signal' ), 'url' => '' );
	}
	return $crumbs;
}
