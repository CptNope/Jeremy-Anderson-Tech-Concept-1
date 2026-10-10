<?php
/**
 * JAT Signal — theme bootstrap.
 *
 * Drop-in replacement for the Developer Portfolio theme: the content model (post types,
 * taxonomies, meta, developer-portfolio/* blocks) is kept identical so the existing site
 * keeps working, and every surface is redrawn in the Calibrated Signal identity.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

define( 'JAT_VERSION', '1.0.0' );
define( 'JAT_DIR', get_template_directory() );
define( 'JAT_URI', get_template_directory_uri() );

require JAT_DIR . '/inc/helpers.php';        // Shared markup helpers (icons, channels, cards, rows).
require JAT_DIR . '/inc/setup.php';          // Theme supports, assets, fonts, sprite, body classes.
require JAT_DIR . '/inc/content-model.php';  // Projects, Services, taxonomies and meta (unchanged keys).
require JAT_DIR . '/inc/blocks.php';         // Registers every block in /blocks and the shared editor script.
require JAT_DIR . '/inc/compat.php';         // Behaviour carried over from Developer Portfolio.
require JAT_DIR . '/inc/seo.php';            // Structured data and meta tags (yields to SEO plugins).
require JAT_DIR . '/inc/forms.php';          // Contact form handler (wp_mail, nonce, honeypot, rate limit).
require JAT_DIR . '/inc/concept.php';        // Concept layouts: draft pages + one-click apply/restore screen.
require JAT_DIR . '/inc/starter.php';        // Fresh installs only: services, pages and legal pages to start from.
