<?php
/**
 * Starter content for a brand-new WordPress install.
 *
 * Runs once, on activation, only when the site is fresh: no static front page, no published
 * pages other than WordPress's "Sample Page", and no projects or services. An existing site
 * (including jeremyanderson.tech) is never touched by this file.
 *
 * It installs what Developer Portfolio used to seed (service categories, the 27 services and
 * their prices, the legal pages, the tech stack), builds Home, Projects, Services, Blog and
 * About in their Developer Portfolio layouts, applies the concept layouts on top (so "Restore
 * previous" works exactly as on a migrated site), and publishes the concept pages.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/** True on a fresh install that hasn't had starter content yet. */
function jat_is_fresh_site(): bool {
	if ( get_option( 'jat_starter_done' ) ) {
		return false;
	}
	if ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) ) {
		return false;
	}
	$sample = get_page_by_path( 'sample-page' );
	$pages  = get_posts( array(
		'post_type'      => 'page',
		'post_status'    => array( 'publish', 'future', 'private', 'pending' ),
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'post__not_in'   => $sample ? array( $sample->ID ) : array(),
	) );
	if ( $pages ) {
		return false;
	}
	$work = get_posts( array( 'post_type' => array( 'project', 'service' ), 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids' ) );
	return ! $work;
}

/** Read one JSON file from inc/starter/. */
function jat_starter_json( string $file ) {
	$path = JAT_DIR . '/inc/starter/' . $file;
	if ( ! is_readable( $path ) ) {
		return array();
	}
	$data = json_decode( (string) file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions -- local theme file.
	return is_array( $data ) ? $data : array();
}

/** Service categories and services, with their prices and features. Skips any that exist. */
function jat_starter_services(): int {
	$data = jat_starter_json( 'services.json' );
	foreach ( $data['categories'] ?? array() as $c ) {
		if ( ! term_exists( $c['slug'], 'service_category' ) ) {
			wp_insert_term( $c['name'], 'service_category', array( 'slug' => $c['slug'], 'description' => $c['description'] ?? '' ) );
		}
	}
	$made = 0;
	foreach ( $data['services'] ?? array() as $s ) {
		if ( get_page_by_path( $s['slug'], OBJECT, 'service' ) ) {
			continue;
		}
		$content = '' !== trim( (string) $s['excerpt'] ) ? "<!-- wp:paragraph -->\n<p>" . esc_html( $s['excerpt'] ) . "</p>\n<!-- /wp:paragraph -->" : '';
		$id      = wp_insert_post( array(
			'post_type'    => 'service',
			'post_status'  => 'publish',
			'post_title'   => $s['title'],
			'post_name'    => $s['slug'],
			'post_excerpt' => $s['excerpt'],
			'post_content' => wp_slash( $content ),
			'menu_order'   => (int) ( $s['menu_order'] ?? 0 ),
		), true );
		if ( is_wp_error( $id ) ) {
			continue;
		}
		wp_set_object_terms( $id, (array) $s['categories'], 'service_category' );
		foreach ( (array) $s['meta'] as $k => $v ) {
			update_post_meta( $id, $k, wp_slash( $v ) );
		}
		++$made;
	}
	delete_transient( 'jat_from_prices' );
	return $made;
}

/** The Developer Portfolio page layouts, filled with Jeremy Anderson Tech's own copy. */
function jat_starter_pages(): array {
	$cta = jat_b( 'developer-portfolio/contact-cta', array( 'align' => 'full', 'buttonUrl' => '' ) );
	return array(
		'home'     => array(
			'title'    => 'Home',
			'template' => '',
			'content'  => implode( "\n", array(
				jat_b( 'developer-portfolio/hero-section', array(
					'align'       => 'full',
					'badgeText'   => 'Available for new projects',
					'role'        => '{ Software Architect }',
					'description' => 'I design, build and secure web platforms, AI systems and custom software for businesses and research teams across New England.',
					'roles'       => 'Software Architect, WordPress Engineer, AI Systems Builder, Cybersecurity Specialist',
				) ),
				jat_b( 'developer-portfolio/featured-projects', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/tech-stack', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-grid', array( 'align' => 'full' ) ),
				$cta,
				jat_b( 'developer-portfolio/latest-posts', array( 'align' => 'full' ) ),
			) ),
		),
		'projects' => array(
			'title'    => 'Projects',
			'template' => 'page-projects',
			'content'  => jat_b( 'developer-portfolio/projects-listing', array( 'align' => 'full' ) ) . "\n" . $cta,
		),
		'services' => array(
			'title'    => 'Services',
			'template' => 'page-services',
			'excerpt'  => 'Published prices for WordPress development, AI consulting, cybersecurity and ongoing support.',
			'content'  => implode( "\n\n", array(
				jat_b( 'developer-portfolio/services-hero', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-market-rates', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-pricing-table', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-packages', array( 'align' => 'full', 'sectionTag' => 'WordPress Development', 'sectionTagType' => 'wp', 'heading' => 'WordPress Project Packages', 'category' => 'wordpress-packages' ) ),
				jat_b( 'developer-portfolio/services-packages', array( 'align' => 'full', 'sectionTag' => 'AI Consulting', 'sectionTagType' => 'ai', 'heading' => 'AI Consulting Packages', 'intro' => 'From a 90-minute discovery session to full AI system architecture, every engagement is scoped to your business and delivered with clear documentation.', 'category' => 'ai-packages' ) ),
				jat_b( 'developer-portfolio/services-packages', array( 'align' => 'full', 'sectionTag' => 'Cybersecurity', 'sectionTagType' => 'sec', 'heading' => 'Cybersecurity Packages', 'intro' => 'WordPress-focused security services, from one-time audits to full infrastructure monitoring, sized for small businesses and startups.', 'category' => 'security-packages' ) ),
				jat_b( 'developer-portfolio/services-retainers', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-addons', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-comparison', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-faq', array( 'align' => 'full' ) ),
				jat_b( 'developer-portfolio/services-cta', array( 'align' => 'full' ) ),
			) ),
		),
		'blog'     => array(
			'title'    => 'Blog',
			'template' => 'page-blog',
			'excerpt'  => 'Build logs, security notes and what we learn shipping real systems.',
			'content'  => '',
		),
		'about'    => array(
			'title'    => 'About',
			'template' => 'page-about',
			'content'  => implode( "\n", array(
				jat_b( 'developer-portfolio/about-intro', array(
					'align'            => 'wide',
					'heading'          => 'The Engineer Behind the Systems',
					'headingHighlight' => 'Engineer',
					'imageUrl'         => '',
					'imageAlt'         => '',
					'location'         => 'Shrewsbury, MA',
					'greeting'         => 'Hi, I’m Jeremy.',
					'bio1'             => 'I have worked with computers and the web since 1998. I build web platforms, applied AI systems and security programs, and I believe the person who builds a system should be the one who answers for it.',
					'bio2'             => 'I co-founded AndersonGraf Technologies in San Antonio, an IT and web company that served local businesses for a decade. Since 2016 I have worked independently as a full-stack developer.',
					'bio3'             => 'I study computer science at Harvard and attend OWASP security events such as BASC Boston.',
					'stat1Number'      => '25+',
					'stat1Label'       => 'Years working with computers and the web',
					'stat2Number'      => '100+',
					'stat2Label'       => 'Websites, systems and projects built',
					'stat3Number'      => '40+',
					'stat3Label'       => 'Custom software and automation tools',
					'stat4Number'      => '∞',
					'stat4Label'       => 'Coffee consumed while debugging',
				) ),
				jat_b( 'developer-portfolio/about-experience', array(
					'align'        => 'wide',
					'jobs'         => wp_json_encode( array(
						array( 'role' => 'AI Consultant & Developer', 'period' => '2022 – Present', 'company' => 'SciStories', 'description' => 'AI-assisted systems and web platforms for research labs.' ),
						array( 'role' => 'Software Consultant & Custom Developer', 'period' => '2024 – Present', 'company' => 'Worcester Restaurant Group', 'description' => 'Operational and training tools for restaurant teams.' ),
						array( 'role' => 'Digital Game Systems Consultant', 'period' => '2024 – Present', 'company' => 'Librarium Games', 'description' => 'Digital versions of tabletop games.' ),
						array( 'role' => 'Independent Full-Stack Developer', 'period' => '2016 – Present', 'company' => 'Freelance / Contract', 'description' => 'Custom WordPress, React and Node.js builds for small businesses.' ),
						array( 'role' => 'Co-Owner & Developer', 'period' => '1998 – 2008', 'company' => 'AndersonGraf Technologies, San Antonio, TX', 'description' => 'An early IT and web company serving local businesses.' ),
					) ),
					'eduItems'     => wp_json_encode( array(
						array( 'title' => 'Computer Science', 'subtitle' => 'Harvard, 2023 – Present' ),
						array( 'title' => 'W3C / WAI Web Accessibility', 'subtitle' => 'Professional certification, 2022' ),
					) ),
					'achieveItems' => wp_json_encode( array(
						array( 'title' => 'Code camp courses', 'subtitle' => 'Full-stack and security tracks' ),
						array( 'title' => 'EdX courses', 'subtitle' => 'Computer science and AI' ),
					) ),
				) ),
				jat_b( 'developer-portfolio/tech-stack', array( 'align' => 'full' ) ),
				$cta,
			) ),
		),
	);
}

/** Create (or reuse) a page by slug. Returns its ID or 0. */
function jat_starter_page( string $slug, array $p ): int {
	$existing = get_page_by_path( $slug );
	if ( $existing ) {
		if ( 'publish' !== $existing->post_status ) {
			wp_update_post( array( 'ID' => $existing->ID, 'post_status' => 'publish' ) );
		}
		return (int) $existing->ID;
	}
	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => $p['title'],
		'post_name'    => $slug,
		'post_excerpt' => $p['excerpt'] ?? '',
		'post_content' => wp_slash( $p['content'] ),
		'meta_input'   => array( '_wp_page_template' => $p['template'] ? $p['template'] : 'default' ),
	), true );
	return is_wp_error( $id ) ? 0 : (int) $id;
}

/** Install everything. Safe to call more than once; each step skips what already exists. */
function jat_starter_install(): array {
	$log = array( 'services' => jat_starter_services() );

	$ids = array();
	foreach ( jat_starter_pages() as $slug => $p ) {
		$ids[ $slug ] = jat_starter_page( $slug, $p );
	}
	if ( $ids['home'] ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
	}
	if ( $ids['blog'] ) {
		update_option( 'page_for_posts', $ids['blog'] );
	}
	$stack = wp_json_encode( jat_starter_json( 'tech-stack.json' ) );
	foreach ( array( 'home', 'about' ) as $slug ) {
		if ( $ids[ $slug ] && ! get_post_meta( $ids[ $slug ], 'tech_stack_data', true ) ) {
			update_post_meta( $ids[ $slug ], 'tech_stack_data', wp_slash( $stack ) );
		}
	}

	// Legal pages. WordPress drafts a Privacy Policy page on install; reuse it.
	$privacy_id = (int) get_option( 'wp_page_for_privacy_policy' );
	foreach ( jat_starter_json( 'legal.json' ) as $l ) {
		if ( 'privacy-policy' === $l['slug'] && $privacy_id && get_post( $privacy_id ) ) {
			wp_update_post( array( 'ID' => $privacy_id, 'post_status' => 'publish', 'post_content' => wp_slash( $l['content'] ) ) );
			continue;
		}
		$id = jat_starter_page( $l['slug'], array( 'title' => $l['title'], 'template' => '', 'content' => $l['content'] ) );
		if ( 'privacy-policy' === $l['slug'] && $id ) {
			update_option( 'wp_page_for_privacy_policy', $id );
		}
	}

	// The default "Sample Page" goes to the trash, unless someone has edited it.
	$sample = get_page_by_path( 'sample-page' );
	if ( $sample && $sample->post_modified_gmt === $sample->post_date_gmt ) {
		wp_trash_post( $sample->ID );
	}

	// Concept layouts on top, so "Restore previous" returns to the Developer Portfolio layouts.
	foreach ( array_keys( jat_concept_targets() ) as $key ) {
		jat_concept_apply( $key );
	}

	// Concept pages are published here (an existing site gets them as drafts to review).
	jat_concept_create_pages();
	foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'draft', 'posts_per_page' => -1, 'meta_key' => '_jat_concept_page' ) ) as $p ) { // phpcs:ignore WordPress.DB.SlowDBQuery -- one-time setup.
		wp_update_post( array( 'ID' => $p->ID, 'post_status' => 'publish' ) );
	}

	update_option( 'jat_starter_done', time() );
	return $log;
}
