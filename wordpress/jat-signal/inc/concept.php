<?php
/**
 * Concept layouts.
 *
 * On an existing site, activating the theme never rewrites content: it only creates the concept's
 * Approach, Contact and four channel pages as drafts, and flushes rewrite rules. A fresh install
 * gets a complete starter site instead (inc/starter.php).
 *
 * Appearance › JAT Signal lets the owner apply the Calibrated Signal layout to Home, Services,
 * About and Projects one page at a time. Each apply keeps the previous content (a WordPress
 * revision plus a stored copy) and can be restored with one click.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/** Serialize one block comment. */
function jat_b( string $name, array $attrs = array() ): string {
	return '<!-- wp:' . $name . ( $attrs ? ' ' . serialize_block_attributes( $attrs ) : '' ) . ' /-->';
}

/** IDs of published posts of a type, by slug or title, in the given order (missing ones skipped). */
function jat_ids( string $type, array $keys, string $by = 'slug' ): array {
	$ids = array();
	foreach ( $keys as $k ) {
		if ( 'slug' === $by ) {
			$p = get_page_by_path( $k, OBJECT, $type );
		} else {
			$found = get_posts( array( 'post_type' => $type, 'title' => $k, 'posts_per_page' => 1, 'post_status' => 'publish' ) );
			$p     = $found ? $found[0] : null;
		}
		if ( $p && 'publish' === $p->post_status ) {
			$ids[] = (int) $p->ID;
		}
	}
	return $ids;
}

/** First block of a given name in some content, as [attrs] (or empty array). */
function jat_find_block( string $content, string $name ): ?array {
	foreach ( parse_blocks( $content ) as $b ) {
		if ( ( $b['blockName'] ?? '' ) === $name ) {
			return $b;
		}
	}
	return null;
}

/** The closing call to action used across concept pages. */
function jat_concept_close( string $heading = 'Tell us what the system needs to do.', string $desc = 'A free 30-minute discovery call, then a written proposal within three business days.' ): string {
	return jat_b( 'developer-portfolio/contact-cta', array(
		'heading'             => $heading,
		'headingHighlight'    => '',
		'description'         => $desc,
		'buttonText'          => 'Book a discovery call',
		'buttonUrl'           => '',
		'secondaryButtonText' => 'See pricing',
		'secondaryButtonUrl'  => '/services/',
	) );
}

/* ------------------------------------------------------------------ */
/* Concept layouts for existing pages                                  */
/* ------------------------------------------------------------------ */

/** Pages the screen can switch to the concept layout. */
function jat_concept_targets(): array {
	return array(
		'home'     => array( 'label' => __( 'Home', 'jat-signal' ), 'id' => (int) get_option( 'page_on_front' ), 'template' => '', 'build' => 'jat_concept_home' ),
		'services' => array( 'label' => __( 'Services', 'jat-signal' ), 'id' => ( $p = get_page_by_path( 'services' ) ) ? $p->ID : 0, 'template' => 'page-services', 'build' => 'jat_concept_services' ),
		'about'    => array( 'label' => __( 'About', 'jat-signal' ), 'id' => ( $p = get_page_by_path( 'about' ) ) ? $p->ID : 0, 'template' => 'page-about', 'build' => 'jat_concept_about' ),
		'projects' => array( 'label' => __( 'Projects', 'jat-signal' ), 'id' => ( $p = get_page_by_path( 'projects' ) ) ? $p->ID : 0, 'template' => 'page-projects', 'build' => 'jat_concept_projects' ),
	);
}

function jat_concept_home( WP_Post $page ): string {
	$hero    = jat_find_block( $page->post_content, 'developer-portfolio/hero-section' );
	$badge   = $hero['attrs']['badgeText'] ?? '';
	$about   = get_page_by_path( 'about' );
	$intro   = $about ? jat_find_block( $about->post_content, 'developer-portfolio/about-intro' ) : null;
	$work    = jat_ids( 'project', array( 'gods-eye-lite', 'cytoscape-immune-defense', 'chronos-canon' ) );
	if ( count( $work ) < 3 ) {
		$open = array_filter( get_posts( array( 'post_type' => 'project', 'posts_per_page' => 20, 'fields' => 'ids' ) ), static fn( $id ) => jat_project_is_open( $id ) );
		$work = array_values( array_unique( array_merge( $work, $open ) ) );
	}
	$prices = jat_ids( 'service', array( 'Business Site', 'Custom Platform', 'Custom AI Tool', 'Hardening Package', 'Guardian' ), 'title' );
	$blog   = (int) get_option( 'page_for_posts' );

	$hero_attrs = array(
		'align'               => 'full',
		'name'                => 'Systems that hold steady.',
		'role'                => '',
		'description'         => 'We build the website, software and AI your business runs on, then secure it, host it and keep it running. Prices are published. You work directly with the engineer.',
		'primaryButtonText'   => 'Book a discovery call',
		'primaryButtonUrl'    => '',
		'secondaryButtonText' => 'See the work',
		'secondaryButtonUrl'  => '/projects/',
		'note'                => 'Free 30-minute call · Written proposal in 3 business days',
		'showChannels'        => true,
	);
	if ( '' !== $badge ) {
		$hero_attrs['badgeText'] = $badge;
	}
	$founder = array();
	if ( $intro ) {
		$founder['imageUrl'] = $intro['attrs']['imageUrl'] ?? '';
		$founder['imageAlt'] = $intro['attrs']['imageAlt'] ?? '';
	}
	$founder['className'] = 'is-style-band';

	return implode( "\n\n", array(
		jat_b( 'developer-portfolio/hero-section', $hero_attrs ),
		jat_b( 'jat/versus' ),
		jat_b( 'jat/channels', array( 'className' => 'is-style-band' ) ),
		jat_b( 'developer-portfolio/featured-projects', array(
			'layout'           => 'feature',
			'heading'          => 'Engineering you can inspect.',
			'headingHighlight' => '',
			'description'      => 'Client engagements stay confidential. These open-source projects are public, live and readable line by line, so you can judge the work before you hire it.',
			'postIds'          => wp_json_encode( array_slice( $work, 0, 3 ) ),
			'linkText'         => 'All projects',
			'linkUrl'          => '/projects/',
		) ),
		jat_b( 'jat/price-list', array( 'postIds' => wp_json_encode( $prices ), 'className' => 'is-style-band' ) ),
		jat_b( 'jat/steps' ),
		jat_b( 'jat/founder', $founder ),
		jat_b( 'developer-portfolio/latest-posts', array(
			'heading'          => 'Field notes.',
			'headingHighlight' => '',
			'description'      => 'Build logs, security notes and what we learn shipping real systems.',
			'linkText'         => 'All posts',
			'linkUrl'          => $blog ? wp_make_link_relative( get_permalink( $blog ) ) : '/blog/',
		) ),
		jat_concept_close(),
	) );
}

function jat_concept_services( WP_Post $page ): string {
	$out   = array();
	$added = false;
	foreach ( parse_blocks( $page->post_content ) as $b ) {
		if ( empty( $b['blockName'] ) ) {
			continue;
		}
		if ( 'jat/channels' === $b['blockName'] ) {
			continue; // Re-added below exactly once.
		}
		$out[] = serialize_block( $b );
		if ( 'developer-portfolio/services-hero' === $b['blockName'] && ! $added ) {
			$out[] = jat_b( 'jat/channels', array( 'heading' => '' ) );
			$added = true;
		}
	}
	if ( ! $added ) {
		array_unshift( $out, jat_b( 'developer-portfolio/services-hero', array( 'heading' => 'Services and pricing', 'headingHighlight' => '', 'subtitle' => 'Four service channels, published prices, and one engineer accountable for all of it.' ) ), jat_b( 'jat/channels', array( 'heading' => '' ) ) );
	}
	return implode( "\n\n", $out );
}

function jat_concept_about( WP_Post $page ): string {
	$keep = array();
	foreach ( array( 'developer-portfolio/about-intro', 'developer-portfolio/about-experience', 'developer-portfolio/tech-stack' ) as $name ) {
		$b             = jat_find_block( $page->post_content, $name );
		// Without an existing block, use an empty portrait rather than the old theme's stock photo.
		$keep[ $name ] = $b ? serialize_block( $b ) : jat_b( $name, 'developer-portfolio/about-intro' === $name ? array( 'imageUrl' => '' ) : array() );
	}
	$beliefs = array(
		array( 'title' => 'Measure first', 'text' => 'Audit, test and benchmark before recommending anything. Decisions follow evidence.' ),
		array( 'title' => 'Plain language', 'text' => 'Every recommendation is explained so the person paying for it can repeat it to their team.' ),
		array( 'title' => 'Secure by default', 'text' => 'Least privilege, current software and monitored logs are the starting point of every build.' ),
		array( 'title' => 'Built for everyone', 'text' => 'Accessibility is a requirement, tested with keyboards and screen readers before launch.' ),
		array( 'title' => 'Own the outcome', 'text' => 'If something we built breaks, we answer for it. Our misestimates are not billed.' ),
		array( 'title' => 'Show the work', 'text' => 'Prices, process and code are public wherever possible.' ),
	);
	return implode( "\n\n", array(
		$keep['developer-portfolio/about-intro'],
		$keep['developer-portfolio/about-experience'],
		jat_b( 'jat/check-grid', array( 'heading' => 'What we believe', 'lede' => 'Six commitments that shape every proposal and every line of code.', 'items' => wp_json_encode( $beliefs ), 'className' => 'is-style-band' ) ),
		$keep['developer-portfolio/tech-stack'],
		jat_concept_close(),
	) );
}

function jat_concept_projects( WP_Post $page ): string {
	$current = jat_find_block( $page->post_content, 'developer-portfolio/projects-listing' );
	$attrs   = array_merge( $current['attrs'] ?? array(), array(
		'heading'          => 'Work you can open and inspect.',
		'headingHighlight' => '',
		'description'      => 'Client engagements stay confidential and are published only with permission. These projects are live, public and readable line by line.',
		'splitProprietary' => true,
	) );
	return implode( "\n\n", array( jat_b( 'developer-portfolio/projects-listing', $attrs ), jat_concept_close() ) );
}

/** Apply the concept layout to one target. Keeps the previous content for one-click restore. */
function jat_concept_apply( string $key ) {
	$targets = jat_concept_targets();
	$t       = $targets[ $key ] ?? null;
	$page    = $t && $t['id'] ? get_post( $t['id'] ) : null;
	if ( ! $page ) {
		return new WP_Error( 'jat_missing', __( 'That page does not exist.', 'jat-signal' ) );
	}
	if ( ! get_post_meta( $page->ID, '_jat_previous_content', true ) ) {
		update_post_meta( $page->ID, '_jat_previous_content', wp_slash( $page->post_content ) );
		update_post_meta( $page->ID, '_jat_previous_template', (string) get_post_meta( $page->ID, '_wp_page_template', true ) );
	}
	$content = call_user_func( $t['build'], $page );
	$res     = wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $content ) ), true );
	if ( is_wp_error( $res ) ) {
		return $res;
	}
	if ( $t['template'] ) {
		update_post_meta( $page->ID, '_wp_page_template', $t['template'] );
	}
	update_post_meta( $page->ID, '_jat_concept_applied', time() );
	return true;
}

/** Put back the content that was there before the concept layout. */
function jat_concept_restore( string $key ) {
	$targets = jat_concept_targets();
	$t       = $targets[ $key ] ?? null;
	$page    = $t && $t['id'] ? get_post( $t['id'] ) : null;
	if ( ! $page ) {
		return new WP_Error( 'jat_missing', __( 'That page does not exist.', 'jat-signal' ) );
	}
	$prev = get_post_meta( $page->ID, '_jat_previous_content', true );
	if ( '' === $prev ) {
		return new WP_Error( 'jat_nothing', __( 'There is no earlier layout stored for this page.', 'jat-signal' ) );
	}
	wp_update_post( array( 'ID' => $page->ID, 'post_content' => wp_slash( $prev ) ) );
	update_post_meta( $page->ID, '_wp_page_template', (string) get_post_meta( $page->ID, '_jat_previous_template', true ) );
	delete_post_meta( $page->ID, '_jat_previous_content' );
	delete_post_meta( $page->ID, '_jat_previous_template' );
	delete_post_meta( $page->ID, '_jat_concept_applied' );
	return true;
}

/* ------------------------------------------------------------------ */
/* New concept pages (created as drafts)                               */
/* ------------------------------------------------------------------ */

function jat_concept_new_pages(): array {
	$proof = static fn( array $slugs ) => wp_json_encode( jat_ids( 'project', $slugs ) );
	$close = jat_concept_close();
	$pages = array();

	$pages['approach'] = array(
		'title'   => 'Approach',
		'parent'  => '',
		'excerpt' => 'How a project runs: free discovery call, written proposal in three business days, signed scope, build and hardening, launch and support.',
		'content' => implode( "\n\n", array(
			jat_b( 'jat/page-head', array( 'title' => 'How a project runs.', 'lede' => 'A short, documented process with prices, dates and responsibilities written down before work starts.' ) ),
			jat_b( 'jat/steps', array( 'heading' => '', 'lede' => '', 'linkText' => '', 'linkUrl' => '', 'items' => wp_json_encode( array(
				array( 'title' => 'Discovery call', 'meta' => 'Day 0', 'text' => 'Free, 30 to 60 minutes. Your goals, constraints, budget and what success looks like.', 'note' => 'Free' ),
				array( 'title' => 'Written proposal', 'meta' => '3 business days', 'text' => 'Scope, deliverables, price, timeline and exclusions in plain language.', 'note' => 'You decide' ),
				array( 'title' => 'Scope and deposit', 'meta' => 'Signed', 'text' => 'A signed scope document and a 50% deposit on fixed-fee projects.', 'note' => 'Start in 1–2 weeks' ),
				array( 'title' => 'Design, build, harden', 'meta' => 'Build', 'text' => 'Reviews as it takes shape, then security, accessibility and performance testing.', 'note' => 'Per package timeline' ),
				array( 'title' => 'Launch and run', 'meta' => 'Launch', 'text' => 'Training, documentation, 30 to 90 days of support, then an optional care plan.', 'note' => 'Balance due on delivery' ),
			) ) ) ),
			jat_b( 'jat/check-grid', array( 'className' => 'is-style-band' ) ),
			jat_b( 'jat/check-grid', array( 'heading' => 'What you get at handoff', 'lede' => '', 'layout' => 'list', 'channel' => 'ch4', 'items' => wp_json_encode( array(
				array( 'title' => 'Admin access to everything: hosting, domain, code and accounts stay in your name.' ),
				array( 'title' => 'Documentation written for the people who will use the system.' ),
				array( 'title' => 'Training for your team on editing and day-to-day care.' ),
				array( 'title' => 'A record of every security change made.' ),
				array( 'title' => 'Included support for 30, 60 or 90 days depending on the package.' ),
			) ) ) ),
			'<!-- wp:group {"className":"section is-style-band terms","layout":{"type":"default"}} -->
<div class="wp-block-group section is-style-band terms"><!-- wp:group {"className":"wrap two-col","layout":{"type":"default"}} -->
<div class="wp-block-group wrap two-col"><!-- wp:heading -->
<h2 class="wp-block-heading">Terms in plain language</h2>
<!-- /wp:heading -->

<!-- wp:group {"className":"prose","layout":{"type":"default"}} -->
<div class="wp-block-group prose"><!-- wp:paragraph -->
<p><strong>Deposits.</strong> 50% to start fixed-fee projects, the balance on delivery. Retainers bill monthly in advance. Hourly work bills every two weeks.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Changes.</strong> New requirements get a written change-order estimate before any work. If we misestimate our own work, you don\'t pay for it.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Care plans.</strong> Three-month minimum, then 30 days notice to cancel. Unused hours can move into a prepaid hour bank.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><a href="/services/">See all pricing</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->',
			$close,
		) ),
	);

	$pages['contact'] = array(
		'title'   => 'Contact',
		'parent'  => '',
		'excerpt' => 'Book a free 30-minute discovery call. Written proposal within three business days.',
		'content' => implode( "\n\n", array(
			jat_b( 'jat/page-head', array( 'title' => 'Tell us what the system needs to do.', 'lede' => 'Send a few details and we will set up a free 30-minute discovery call. You will have a written proposal within three business days of that call.' ) ),
			jat_b( 'jat/contact-form' ),
		) ),
	);

	$channel = static function ( string $key, string $title, string $h1, string $lede, string $outcome, string $outcome_lede, array $areas, array $proof_slugs, array $faqs ) use ( $proof, $close ): array {
		$c       = jat_channels()[ $key ];
		$pkg     = 'ch4' === $key
			? jat_b( 'developer-portfolio/services-retainers', array( 'heading' => 'Care plans', 'intro' => 'Monthly plans with response times you can count on. Three-month minimum, then 30 days notice.', 'className' => 'is-style-band' ) )
			: jat_b( 'developer-portfolio/services-packages', array( 'heading' => 'Packages', 'intro' => 'Fixed fees with a signed scope, or hourly at the published rate.', 'category' => $c['category'], 'sectionTagType' => $c['badge'], 'sectionTag' => $c['name'], 'className' => 'is-style-band' ) );
		$proof_b = jat_b( 'developer-portfolio/featured-projects', array( 'heading' => 'Proof you can open', 'headingHighlight' => '', 'description' => 'Public projects that use the same engineering this channel sells.', 'postIds' => $proof( $proof_slugs ), 'linkText' => 'All projects', 'linkUrl' => '/projects/' ) );
		return array(
			'title'   => $c['name'],
			'parent'  => 'services',
			'excerpt' => $lede,
			'content' => implode( "\n\n", array(
				jat_b( 'jat/page-head', array( 'title' => $h1, 'lede' => $lede, 'channel' => $key ) ),
				jat_b( 'jat/channel-intro', array( 'channel' => $key, 'heading' => $outcome, 'lede' => $outcome_lede, 'items' => wp_json_encode( $areas ) ) ),
				$pkg,
				$proof_b,
				jat_b( 'developer-portfolio/services-faq', array( 'heading' => 'Questions about ' . strtolower( $c['name'] ), 'intro' => 'If yours isn\'t here, ask on the call.', 'items' => wp_json_encode( $faqs ) ) ),
				$close,
			) ),
		);
	};

	$pages['platforms'] = $channel( 'ch1', 'Web design, WordPress engineering and custom software', 'Websites and apps that win work.',
		'Design, custom WordPress engineering and progressive web apps, built fast, accessible and secure from the first commit.',
		'A site your team can run, and your customers can use on any device.',
		'Every Platforms build ships with a block-based editor your staff can use, Core Web Vitals optimization, schema markup, WCAG 2.2 AA accessibility testing and basic security hardening. You own the code and the content.',
		array(
			array( 'id' => 'web-design', 'title' => 'Web design and redesign', 'outcome' => 'A clear, credible site that turns visitors into calls.', 'points' => array( 'Content strategy and page structure built around your services', 'Visual design system matched to your brand', 'Responsive layouts tested on real phones', 'Accessibility designed in, not patched on' ) ),
			array( 'id' => 'wordpress', 'title' => 'Custom WordPress engineering', 'outcome' => 'A platform that fits how your business actually works.', 'points' => array( 'Block themes and reusable patterns your team can edit', 'Custom post types, taxonomies and plugin features', 'Custom Gutenberg blocks with editor controls', 'WooCommerce with custom checkout when you sell online' ) ),
			array( 'id' => 'custom-software', 'title' => 'Custom software and PWAs', 'outcome' => 'Tools your staff and customers install like apps.', 'points' => array( 'React applications with Node.js and REST APIs', 'Offline-capable progressive web apps', 'Installable on iOS, Android and desktop', 'Automated testing and deployment pipelines' ) ),
			array( 'id' => 'migrations', 'title' => 'Migrations and rebuilds', 'outcome' => 'Move or rebuild without losing search rank.', 'points' => array( 'Host-to-host WordPress migrations with DNS and SSL', 'URL mapping and 301 redirects for every old page', 'Pre- and post-launch crawl comparison', '30 days of post-migration monitoring' ) ),
		),
		array( 'cytoscape-immune-defense', 'chronos-canon' ),
		array(
			array( 'q' => 'Will I be able to edit the site myself?', 'a' => 'Yes. Builds use the WordPress block editor with patterns designed for your content, and Custom Platform includes CMS training for your team.' ),
			array( 'q' => 'Do you use page builders?', 'a' => 'No. Builds use native WordPress blocks and custom blocks, which load faster and do not lock you into a builder subscription.' ),
			array( 'q' => 'Who owns the site?', 'a' => 'You do: the code, the content and the domain. Hosting with JAT is optional.' ),
		)
	);
	$pages['intelligence'] = $channel( 'ch2', 'AI development and automation', 'AI that removes real work.',
		'Custom AI tools, private language models and automation that solve one operational problem at a time, on data you control.',
		'Start with the problem, not the model.',
		'Every Intelligence engagement begins by finding where AI genuinely saves time or money, what it would cost, and whether to build or buy. Then we build one focused, production-ready tool and train your team to use it.',
		array(
			array( 'id' => 'discovery', 'title' => 'AI discovery and roadmaps', 'outcome' => 'Know where AI fits before you spend on it.', 'points' => array( 'Two-hour workshop with your team', 'Top three opportunities ranked by value and feasibility', 'Build-versus-buy recommendation', 'Written roadmap with cost estimates' ) ),
			array( 'id' => 'ai-tools', 'title' => 'Custom AI tools', 'outcome' => 'One tool, one problem, in production.', 'points' => array( 'Document assistants and chat over your own files', 'Log analyzers and internal search', 'Custom interface or API', 'Prompt engineering and testing against real cases' ) ),
			array( 'id' => 'private-llm', 'title' => 'Private models and RAG', 'outcome' => 'Keep sensitive data on infrastructure you control.', 'points' => array( 'Local model setup (Ollama) or vetted cloud models', 'Vector search and retrieval pipelines (RAG)', 'Data privacy review', 'Integration with WordPress and existing systems' ) ),
			array( 'id' => 'automation', 'title' => 'Integrations and automation', 'outcome' => 'Connect the tools you already pay for.', 'points' => array( 'REST API integrations between your systems', 'n8n or custom workflow automation', 'AI-assisted monitoring and alerting', 'Documentation your team can maintain' ) ),
		),
		array( 'chronos-canon', 'gods-eye-lite' ),
		array(
			array( 'q' => 'Does my data leave my company?', 'a' => 'It doesn\'t have to. Local models can run on your own hardware or private cloud, and every engagement includes a data privacy review.' ),
			array( 'q' => 'Which AI models do you use?', 'a' => 'Whichever fits the job and your privacy needs: local open models through Ollama, or established cloud models. The recommendation comes with its trade-offs written down.' ),
			array( 'q' => 'What if AI isn\'t the right answer?', 'a' => 'Then the discovery roadmap will say so. Sometimes a simple automation or a better form solves the problem for less.' ),
		)
	);
	$pages['security'] = $channel( 'ch3', 'Cybersecurity and compliance', 'Know where you are exposed.',
		'Audits, hardening and monitoring for WordPress sites and web applications, explained without fear and documented change by change.',
		'Security that is measured, fixed and watched.',
		'Start with an audit that rates every risk. Close the gaps with a hardening package that documents each change. Then watch for attacks with centralized logging and alerts your staff can understand.',
		array(
			array( 'id' => 'audit', 'title' => 'Security audits', 'outcome' => 'A clear, prioritized list of what to fix.', 'points' => array( 'Plugin and theme vulnerability scan', 'User roles, login and 2FA review', 'SSL/TLS, file permissions and server configuration', 'Risk-rated written report and debrief call' ) ),
			array( 'id' => 'hardening', 'title' => 'Hardening', 'outcome' => 'Every fix implemented and documented.', 'points' => array( 'Core, plugin and theme hardening', 'Brute-force blocking and two-factor authentication', 'Security headers and web application firewall', 'Malware scan and cleanup when needed' ) ),
			array( 'id' => 'monitoring', 'title' => 'SIEM and SOC monitoring', 'outcome' => 'See attacks before they become incidents.', 'points' => array( 'Centralized logging across sites and apps', 'Alert rules for common attack patterns', 'AI-assisted log analysis', 'Incident response runbook and staff dashboard' ) ),
			array( 'id' => 'privacy', 'title' => 'Massachusetts privacy review', 'outcome' => 'Data handling you can explain to customers.', 'points' => array( 'Cookie consent and tracking audit', 'Plugin telemetry and third-party data flows', 'Data handling practices review', 'Remediation checklist' ) ),
		),
		array( 'siem-soc-maintenance-dashboard', 'scistories-security-plugin', 'securigotchi' ),
		array(
			array( 'q' => 'Will hardening break my site?', 'a' => 'Changes are tested on a copy first and every one is documented, so anything unexpected can be traced and reversed.' ),
			array( 'q' => 'We already have antivirus and an IT company. Is that enough?', 'a' => 'Device and network security is different from web application security. The audit shows exactly where your website and plugins stand.' ),
			array( 'q' => 'How often should we audit?', 'a' => 'At least yearly, and after any major change. The Partner care plan includes a quarterly full audit.' ),
		)
	);
	$pages['operations'] = $channel( 'ch4', 'Managed hosting, care plans and search visibility', 'Handled every month.',
		'Managed hosting, updates, backups, monitoring, performance and search visibility, on infrastructure run by the people who built your site.',
		'After launch is when the real work starts.',
		'Care plans keep software current, backups offsite and performance measured, with guaranteed response times. Search work keeps you visible in Google and in the answers AI assistants give.',
		array(
			array( 'id' => 'hosting', 'title' => 'Managed hosting', 'outcome' => 'Fast, monitored hosting run by your engineer.', 'points' => array( 'Managed cloud infrastructure on Cloudways and DigitalOcean', 'SSL, CDN and caching configured for your site', 'Offsite backups and restore testing', 'Scoped in your proposal' ) ),
			array( 'id' => 'care', 'title' => 'Care plans', 'outcome' => 'Updates and fixes with response times you can count on.', 'points' => array( 'Core, plugin and theme updates', 'Uptime monitoring and monthly status reports', 'Response times from 48 hours to same-day', 'Hours for content edits and improvements' ) ),
			array( 'id' => 'performance', 'title' => 'Performance', 'outcome' => 'Pages that load fast on real phones.', 'points' => array( 'Core Web Vitals audit and fixes', 'Targeting 90+ PageSpeed on mobile and desktop', 'Image, caching, CDN and asset optimization', 'Ongoing tracking on Guardian and above' ) ),
			array( 'id' => 'visibility', 'title' => 'SEO, AEO and GEO', 'outcome' => 'Be found in search results and AI answers.', 'points' => array( 'Technical SEO audit and Search Console setup', 'Local SEO for Boston and Worcester businesses', 'Schema markup for answer engines (AEO)', 'Structured, citable content for AI assistants (GEO)' ) ),
		),
		array( 'distributed-wordpress-maintenance-platform', 'gods-eye-lite' ),
		array(
			array( 'q' => 'Do unused retainer hours roll over?', 'a' => 'They don\'t roll over automatically, but you can move them into a prepaid hour bank at the retainer rate.' ),
			array( 'q' => 'Can I cancel a care plan?', 'a' => 'After the three-month minimum, with 30 days notice.' ),
			array( 'q' => 'What is GEO?', 'a' => 'Generative engine optimization: structuring your content and data so AI assistants can find, understand and cite your business in their answers.' ),
		)
	);
	return $pages;
}

/** Create any concept page that does not exist yet, as a draft. Existing pages are never touched. */
function jat_concept_create_pages(): array {
	$made = array();
	foreach ( jat_concept_new_pages() as $slug => $p ) {
		$parent_id = 0;
		$path      = $slug;
		if ( $p['parent'] ) {
			$parent = get_page_by_path( $p['parent'] );
			if ( ! $parent ) {
				continue;
			}
			$parent_id = $parent->ID;
			$path      = $p['parent'] . '/' . $slug;
		}
		if ( get_page_by_path( $path, OBJECT, 'page' ) || get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_parent' => $parent_id, 'post_status' => array( 'draft', 'pending', 'private', 'publish', 'future' ), 'posts_per_page' => 1, 'fields' => 'ids' ) ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'draft',
			'post_title'   => $p['title'],
			'post_name'    => $slug,
			'post_parent'  => $parent_id,
			'post_excerpt' => $p['excerpt'],
			'post_content' => wp_slash( $p['content'] ),
			'meta_input'   => array( '_wp_page_template' => 'page-canvas', '_jat_concept_page' => 1 ),
		), true );
		if ( ! is_wp_error( $id ) ) {
			$made[] = $id;
		}
	}
	return $made;
}

/**
 * On activation. A fresh install gets the full starter site (inc/starter.php). An existing site
 * keeps every page as it is: only the concept pages are added, as drafts, and rewrites are flushed.
 */
add_action( 'after_switch_theme', static function () {
	jat_register_post_types();
	$fresh = jat_is_fresh_site();
	if ( $fresh ) {
		jat_starter_install();
	}
	jat_concept_create_pages();
	delete_transient( 'jat_from_prices' );
	flush_rewrite_rules();
	set_transient( 'jat_activation_notice', $fresh ? 'fresh' : 'existing', HOUR_IN_SECONDS );
}, 20 );

/* ------------------------------------------------------------------ */
/* Admin screen: Appearance › JAT Signal                               */
/* ------------------------------------------------------------------ */

add_action( 'admin_menu', static function () {
	add_theme_page( __( 'JAT Signal', 'jat-signal' ), __( 'JAT Signal', 'jat-signal' ), 'edit_theme_options', 'jat-signal', 'jat_admin_screen' );
} );

add_action( 'admin_notices', static function () {
	$kind = get_transient( 'jat_activation_notice' );
	if ( ! $kind || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	delete_transient( 'jat_activation_notice' );
	printf(
		'<div class="notice notice-success is-dismissible"><p>%s <a href="%s">%s</a></p></div>',
		'fresh' === $kind
			? esc_html__( 'JAT Signal is active and your new site is set up: pages, services and prices, the concept pages and the legal pages. Add projects, posts and a portrait to finish it.', 'jat-signal' )
			: esc_html__( 'JAT Signal is active. Your existing pages, projects, services and posts are restyled as they are. Concept pages were added as drafts.', 'jat-signal' ),
		esc_url( admin_url( 'themes.php?page=jat-signal' ) ),
		esc_html__( 'Review concept layouts →', 'jat-signal' )
	);
} );

add_action( 'admin_post_jat_concept', static function () {
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'jat-signal' ) );
	}
	check_admin_referer( 'jat_concept' );
	$do  = sanitize_key( wp_unslash( $_POST['do'] ?? '' ) );
	$key = sanitize_key( wp_unslash( $_POST['key'] ?? '' ) );
	$msg = 'done';
	if ( 'apply' === $do ) {
		$r   = jat_concept_apply( $key );
		$msg = is_wp_error( $r ) ? 'error' : 'applied';
	} elseif ( 'restore' === $do ) {
		$r   = jat_concept_restore( $key );
		$msg = is_wp_error( $r ) ? 'error' : 'restored';
	} elseif ( 'pages' === $do ) {
		$msg = 'pages-' . count( jat_concept_create_pages() );
	} elseif ( 'settings' === $do ) {
		update_option( 'jat_public_email', sanitize_email( wp_unslash( $_POST['jat_public_email'] ?? '' ) ) );
		update_option( 'jat_form_recipient', sanitize_email( wp_unslash( $_POST['jat_form_recipient'] ?? '' ) ) );
		$msg = 'saved';
	}
	delete_transient( 'jat_from_prices' );
	wp_safe_redirect( add_query_arg( array( 'page' => 'jat-signal', 'jat' => $msg ), admin_url( 'themes.php' ) ) );
	exit;
} );

/** Small form posting one action to admin-post.php. */
function jat_admin_button( string $do, string $key, string $label, string $class = 'button' ): string {
	return '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="display:inline">'
		. wp_nonce_field( 'jat_concept', '_wpnonce', true, false )
		. '<input type="hidden" name="action" value="jat_concept"><input type="hidden" name="do" value="' . esc_attr( $do ) . '"><input type="hidden" name="key" value="' . esc_attr( $key ) . '">'
		. '<button type="submit" class="' . esc_attr( $class ) . '">' . esc_html( $label ) . '</button></form>';
}

function jat_admin_screen(): void {
	$notice = isset( $_GET['jat'] ) ? sanitize_key( wp_unslash( $_GET['jat'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
	$texts  = array( 'applied' => __( 'Concept layout applied. The previous content is kept and can be restored below.', 'jat-signal' ), 'restored' => __( 'Previous layout restored.', 'jat-signal' ), 'saved' => __( 'Settings saved.', 'jat-signal' ), 'error' => __( 'That did not work. Nothing was changed.', 'jat-signal' ) );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'JAT Signal', 'jat-signal' ); ?></h1>
		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo 'error' === $notice ? 'error' : 'success'; ?>"><p><?php echo esc_html( $texts[ $notice ] ?? ( str_starts_with( $notice, 'pages-' ) ? sprintf( /* translators: %d count */ __( '%d concept page(s) created as drafts.', 'jat-signal' ), (int) substr( $notice, 6 ) ) : '' ) ); ?></p></div>
		<?php endif; ?>
		<p style="max-width:70ch"><?php esc_html_e( 'Your pages already render in the new design with their current content. Applying a concept layout rebuilds a page from the concept, using your real projects, services, prices and about text. The previous content is stored and can be restored with one click; it is also in the page’s Revisions.', 'jat-signal' ); ?></p>

		<h2><?php esc_html_e( 'Concept layouts', 'jat-signal' ); ?></h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th><?php esc_html_e( 'Page', 'jat-signal' ); ?></th><th><?php esc_html_e( 'Layout', 'jat-signal' ); ?></th><th><?php esc_html_e( 'Actions', 'jat-signal' ); ?></th></tr></thead>
			<tbody>
				<?php foreach ( jat_concept_targets() as $key => $t ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $t['label'] ); ?></strong><?php if ( $t['id'] ) : ?><br><a href="<?php echo esc_url( get_permalink( $t['id'] ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'View', 'jat-signal' ); ?></a> · <a href="<?php echo esc_url( get_edit_post_link( $t['id'] ) ); ?>"><?php esc_html_e( 'Edit', 'jat-signal' ); ?></a><?php endif; ?></td>
						<td>
							<?php
							if ( ! $t['id'] ) {
								esc_html_e( 'Page not found', 'jat-signal' );
							} elseif ( $applied = (int) get_post_meta( $t['id'], '_jat_concept_applied', true ) ) {
								/* translators: %s date */
								echo esc_html( sprintf( __( 'Concept (applied %s)', 'jat-signal' ), wp_date( get_option( 'date_format' ), $applied ) ) );
							} else {
								esc_html_e( 'Your current content, restyled', 'jat-signal' );
							}
							?>
						</td>
						<td>
							<?php
							if ( $t['id'] ) {
								echo jat_admin_button( 'apply', $key, get_post_meta( $t['id'], '_jat_previous_content', true ) ? __( 'Re-apply concept', 'jat-signal' ) : __( 'Apply concept layout', 'jat-signal' ), 'button button-primary' ); // phpcs:ignore
								if ( get_post_meta( $t['id'], '_jat_previous_content', true ) ) {
									echo ' ' . jat_admin_button( 'restore', $key, __( 'Restore previous', 'jat-signal' ) ); // phpcs:ignore
								}
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>

		<h2 style="margin-top:2em"><?php esc_html_e( 'Concept pages', 'jat-signal' ); ?></h2>
		<p><?php esc_html_e( 'Created as drafts so you can review them. Publishing one adds it to the header and footer automatically.', 'jat-signal' ); ?></p>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th><?php esc_html_e( 'Page', 'jat-signal' ); ?></th><th><?php esc_html_e( 'Status', 'jat-signal' ); ?></th><th><?php esc_html_e( 'Actions', 'jat-signal' ); ?></th></tr></thead>
			<tbody>
				<?php
				foreach ( jat_concept_new_pages() as $slug => $p ) :
					$path = $p['parent'] ? $p['parent'] . '/' . $slug : $slug;
					$page = get_page_by_path( $path, OBJECT, 'page' );
					if ( ! $page ) {
						$found = get_posts( array( 'post_type' => 'page', 'name' => $slug, 'post_status' => array( 'draft', 'pending', 'private' ), 'posts_per_page' => 1 ) );
						$page  = $found ? $found[0] : null;
					}
					?>
					<tr>
						<td><strong><?php echo esc_html( $p['title'] ); ?></strong><br><code>/<?php echo esc_html( $path ); ?>/</code></td>
						<td><?php echo $page ? esc_html( get_post_status_object( $page->post_status )->label ) : esc_html__( 'Not created', 'jat-signal' ); ?></td>
						<td><?php if ( $page ) : ?><a class="button" href="<?php echo esc_url( get_edit_post_link( $page->ID ) ); ?>"><?php esc_html_e( 'Edit', 'jat-signal' ); ?></a> <a class="button" href="<?php echo esc_url( get_preview_post_link( $page ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', 'jat-signal' ); ?></a><?php endif; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<p><?php echo jat_admin_button( 'pages', '', __( 'Create any missing concept pages', 'jat-signal' ) ); // phpcs:ignore ?></p>

		<h2 style="margin-top:2em"><?php esc_html_e( 'Contact details', 'jat-signal' ); ?></h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<?php wp_nonce_field( 'jat_concept' ); ?>
			<input type="hidden" name="action" value="jat_concept"><input type="hidden" name="do" value="settings">
			<table class="form-table" role="presentation">
				<tr><th scope="row"><label for="jat_public_email"><?php esc_html_e( 'Public email', 'jat-signal' ); ?></label></th><td><input class="regular-text" type="email" id="jat_public_email" name="jat_public_email" value="<?php echo esc_attr( jat_email() ); ?>"><p class="description"><?php esc_html_e( 'Shown in the footer, calls to action and the contact page. Used for “Book a call” until the Contact page is published.', 'jat-signal' ); ?></p></td></tr>
				<tr><th scope="row"><label for="jat_form_recipient"><?php esc_html_e( 'Form recipient', 'jat-signal' ); ?></label></th><td><input class="regular-text" type="email" id="jat_form_recipient" name="jat_form_recipient" value="<?php echo esc_attr( (string) get_option( 'jat_form_recipient', '' ) ); ?>" placeholder="<?php echo esc_attr( jat_email() ); ?>"><p class="description"><?php esc_html_e( 'Where contact form messages are emailed. Every message is also saved under Inquiries.', 'jat-signal' ); ?></p></td></tr>
			</table>
			<?php submit_button( __( 'Save contact details', 'jat-signal' ) ); ?>
		</form>
	</div>
	<?php
}
