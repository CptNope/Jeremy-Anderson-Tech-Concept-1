<?php
/**
 * Theme setup and assets.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', static function () {
	load_theme_textdomain( 'jat-signal', JAT_DIR . '/languages' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'custom-logo', array( 'height' => 36, 'width' => 36, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_editor_style( array( 'assets/css/theme.css', 'assets/css/editor.css' ) );
	register_nav_menus( array( 'primary' => __( 'Primary', 'jat-signal' ) ) ); // Optional: the header uses its own items unless a menu is assigned.
} );

/** Front end styles and behaviour. */
add_action( 'wp_enqueue_scripts', static function () {
	wp_enqueue_style( 'jat-signal', JAT_URI . '/assets/css/theme.css', array(), JAT_VERSION );
	wp_enqueue_script( 'jat-signal', JAT_URI . '/assets/js/theme.js', array(), JAT_VERSION, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script( 'jat-signal', 'JAT_I18N', array(
		'menu'    => __( 'Menu', 'jat-signal' ),
		'close'   => __( 'Close', 'jat-signal' ),
		'copy'    => __( 'Copy', 'jat-signal' ),
		'copied'  => __( 'Copied', 'jat-signal' ),
		'select'  => __( 'Selected · press copy', 'jat-signal' ),
		'states'  => array( 'run' => __( 'Acquiring signal', 'jat-signal' ), 'lock' => __( 'Trig’d', 'jat-signal' ), 'settled' => __( 'Locked · stable', 'jat-signal' ) ),
		'form'    => array(
			'name'      => __( 'Add your name so we know who to reply to.', 'jat-signal' ),
			'nameLong'  => __( 'Use 100 characters or fewer.', 'jat-signal' ),
			'email'     => __( 'Add your email address so we can reply.', 'jat-signal' ),
			'emailBad'  => __( 'Check the email address. It should look like you@company.com.', 'jat-signal' ),
			'site'      => __( 'Add the full address, like mycompany.com, or leave it blank.', 'jat-signal' ),
			/* translators: %d: characters still needed */
			'msgShort'  => __( 'Tell us a little more about what the system should do (%d more characters).', 'jat-signal' ),
			'msgLong'   => __( 'Keep it under 4,000 characters. You can share more on the call.', 'jat-signal' ),
			/* translators: %d: number of fields */
			'attention' => __( '%d field(s) need attention before sending:', 'jat-signal' ),
			'offline'   => __( 'You appear to be offline.', 'jat-signal' ),
			/* translators: %s: email address */
			'offline2'  => sprintf( __( 'Your message is saved on this device. Reconnect and send again, or email %s.', 'jat-signal' ), jat_email() ),
			'sending'   => __( 'Sending…', 'jat-signal' ),
			'sent'      => __( 'Thanks, your message is in.', 'jat-signal' ),
			'sent2'     => __( 'You’ll hear back within one business day.', 'jat-signal' ),
			'expired'   => __( 'This form expired. Refresh the page and send again; your text is kept on this device.', 'jat-signal' ),
			/* translators: %s: email address */
			'failed'    => sprintf( __( 'Something went wrong on our side. Your text is kept on this device. Try again, or email %s.', 'jat-signal' ), jat_email() ),
		),
	) );
} );

/** Preload the two variable fonts (theme.json prints the @font-face rules). Marks the document as JS-capable before paint. */
add_action( 'wp_head', static function () {
	echo "<script>document.documentElement.classList.add('js')</script>\n";
	foreach ( array( 'archivo-var.woff2', 'martian-mono-var.woff2' ) as $f ) {
		printf( "<link rel=\"preload\" href=\"%s\" as=\"font\" type=\"font/woff2\" crossorigin>\n", esc_url( JAT_URI . '/assets/fonts/' . $f ) );
	}
	echo '<meta name="theme-color" content="#0A0C0F">' . "\n";
}, 1 );

/** Favicon: the JAT mark, unless a Site Icon is set in Settings › General. */
add_action( 'wp_head', static function () {
	if ( has_site_icon() ) {
		return;
	}
	printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( JAT_URI . '/assets/images/jat-mark.svg' ) );
}, 2 );

/** Brand sprite (mark, lockups) referenced by <use href="#lk-h"> in the header and footer. */
add_action( 'wp_body_open', static function () {
	include JAT_DIR . '/assets/images/sprite.svg.php';
} );

/** Body classes used by the stylesheet. */
add_filter( 'body_class', static function ( array $classes ): array {
	$classes[] = 'jat';
	return $classes;
} );

/** Block styles that map to the identity's components. */
add_action( 'init', static function () {
	$styles = array(
		'core/group'     => array( array( 'screen', __( 'Screen (graticule panel)', 'jat-signal' ) ), array( 'band', __( 'Band (ink section)', 'jat-signal' ) ), array( 'card', __( 'Card', 'jat-signal' ) ) ),
		'core/paragraph' => array( array( 'lede', __( 'Lede', 'jat-signal' ) ), array( 'readout', __( 'Readout', 'jat-signal' ) ) ),
		'core/list'      => array( array( 'check', __( 'Checklist', 'jat-signal' ) ), array( 'rule', __( 'Ruled rows', 'jat-signal' ) ) ),
		'core/table'     => array( array( 'spec', __( 'Spec table', 'jat-signal' ) ) ),
		'core/button'    => array( array( 'secondary', __( 'Secondary', 'jat-signal' ) ) ),
		'core/heading'   => array( array( 'display', __( 'Display', 'jat-signal' ) ) ),
	);
	foreach ( $styles as $block => $list ) {
		foreach ( $list as $s ) {
			register_block_style( $block, array( 'name' => $s[0], 'label' => $s[1] ) );
		}
	}

	register_block_pattern_category( 'jat-sections', array( 'label' => __( 'JAT · Sections', 'jat-signal' ) ) );
	register_block_pattern_category( 'jat-pages', array( 'label' => __( 'JAT · Full pages', 'jat-signal' ) ) );
	// Categories from Developer Portfolio, kept so its patterns still appear where expected.
	register_block_pattern_category( 'developer-portfolio-hero', array( 'label' => __( 'Hero Sections', 'jat-signal' ) ) );
	register_block_pattern_category( 'developer-portfolio-sections', array( 'label' => __( 'Portfolio Sections', 'jat-signal' ) ) );
	register_block_pattern_category( 'developer-portfolio-cta', array( 'label' => __( 'Call to Action', 'jat-signal' ) ) );
} );

/** Editor-only: meta sidebars for projects, posts and services (unchanged from Developer Portfolio). */
add_action( 'enqueue_block_editor_assets', static function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$deps   = array( 'wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-element', 'wp-core-data' );
	$map    = array( 'project' => 'project-meta-sidebar', 'post' => 'post-meta-sidebar', 'service' => 'service-meta-sidebar' );
	if ( $screen && isset( $map[ $screen->post_type ] ) ) {
		wp_enqueue_script( 'jat-' . $map[ $screen->post_type ], JAT_URI . '/assets/js/' . $map[ $screen->post_type ] . '.js', $deps, JAT_VERSION, true );
	}
} );

/** Block settings sidebar styles: admin chrome only, never inside the editor canvas. */
add_action( 'admin_enqueue_scripts', static function () {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	if ( $screen && method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor() ) {
		wp_enqueue_style( 'jat-editor-ui', JAT_URI . '/assets/css/editor-ui.css', array( 'wp-components' ), JAT_VERSION );
	}
} );

/** Excerpts: 25 words, as before. */
add_filter( 'excerpt_length', static fn() => 25 );
add_filter( 'excerpt_more', static fn() => '…' );
