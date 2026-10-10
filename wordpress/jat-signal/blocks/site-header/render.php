<?php
/**
 * developer-portfolio/site-header — sticky header: JAT lockup, navigation, call to action.
 *
 * navMode: "custom" uses the navItems JSON (as in Developer Portfolio); "auto" lists the
 * published Services, Projects, Approach, About and Blog pages; "menu" uses Appearance › Menus › Primary.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$mode  = $attributes['navMode'] ?? 'custom';
$items = array();

if ( 'menu' === $mode && has_nav_menu( 'primary' ) ) {
	$locations = get_nav_menu_locations();
	foreach ( (array) wp_get_nav_menu_items( $locations['primary'] ) as $mi ) {
		if ( ! $mi->menu_item_parent ) {
			$items[] = array( 'label' => $mi->title, 'url' => $mi->url );
		}
	}
} elseif ( 'auto' === $mode ) {
	foreach ( array( 'services', 'projects', 'approach', 'about' ) as $slug ) {
		$p = get_page_by_path( $slug );
		if ( $p && 'publish' === $p->post_status ) {
			$items[] = array( 'label' => get_the_title( $p ), 'url' => get_permalink( $p ) );
		}
	}
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog && 'publish' === get_post_status( $blog ) ) {
		$items[] = array( 'label' => get_the_title( $blog ), 'url' => get_permalink( $blog ) );
	}
} else {
	$items = jat_json( $attributes['navItems'] ?? '[]' );
}

// Current section: exact match gets aria-current="page", a parent path gets aria-current="true".
$req     = isset( $_SERVER['REQUEST_URI'] ) ? wp_parse_url( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ), PHP_URL_PATH ) : '/';
$current = trailingslashit( (string) $req );
$section = array(
	'project' => trailingslashit( (string) wp_parse_url( jat_page_url( 'projects' ), PHP_URL_PATH ) ),
	'service' => trailingslashit( (string) wp_parse_url( jat_page_url( 'services' ), PHP_URL_PATH ) ),
	'post'    => get_option( 'page_for_posts' ) ? trailingslashit( (string) wp_parse_url( get_permalink( (int) get_option( 'page_for_posts' ) ), PHP_URL_PATH ) ) : '',
);
$ctx_type = is_singular() ? get_post_type() : ( is_category() || is_tag() || is_date() ? 'post' : ( is_tax( array( 'project_tag', 'project_category' ) ) ? 'project' : ( is_tax( 'service_category' ) ? 'service' : '' ) ) );

$cta_text = $attributes['ctaText'] ?? "Let's Talk";
$cta_url  = $attributes['ctaUrl'] ?? '';
if ( '' === trim( (string) $cta_url ) ) {
	$cta_url = jat_contact_url();
}

$site_name = ( $attributes['siteTitle'] ?? '' ) ? $attributes['siteTitle'] : get_bloginfo( 'name' );
$logo_id   = (int) ( $attributes['logoId'] ?? 0 );
$lockup    = ! isset( $attributes['showLockup'] ) || $attributes['showLockup'];
$menu_id   = jat_id( 'primary-menu' );
?>
<div <?php echo get_block_wrapper_attributes( array( 'class' => 'site-header' ) ); ?>>
	<div class="wrap">
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s site name */ __( '%s, home', 'jat-signal' ), $site_name ) ); ?>">
			<?php if ( $logo_id && ! $lockup ) : ?>
				<?php echo wp_get_attachment_image( $logo_id, 'thumbnail', false, array( 'alt' => '', 'class' => 'brand-img' ) ); ?><span class="brand-name"><?php echo esc_html( $site_name ); ?></span>
			<?php elseif ( $lockup ) : ?>
				<?php echo jat_editor_sprite(); // phpcs:ignore ?><svg viewBox="1 6 468 68" aria-hidden="true" focusable="false"><use href="#lk-h"/></svg>
			<?php else : ?>
				<span class="brand-name"><?php echo esc_html( $site_name ); ?></span>
			<?php endif; ?>
		</a>
		<nav class="nav" aria-label="<?php esc_attr_e( 'Primary', 'jat-signal' ); ?>">
			<ul id="<?php echo esc_attr( $menu_id ); ?>">
				<?php
				foreach ( $items as $item ) :
					$label = trim( (string) ( $item['label'] ?? '' ) );
					$url   = (string) ( $item['url'] ?? '' );
					if ( '' === $label || '' === $url ) {
						continue;
					}
					$path    = trailingslashit( (string) wp_parse_url( jat_url( $url ) ? html_entity_decode( jat_url( $url ) ) : $url, PHP_URL_PATH ) );
					$is_home = '/' === $path || '' === trim( $path, '/' );
					$aria    = '';
					if ( $path === $current ) {
						$aria = ' aria-current="page"';
					} elseif ( ! $is_home && ( ( $ctx_type && ( $section[ $ctx_type ] ?? '' ) === $path ) || str_starts_with( $current, $path ) ) ) {
						$aria = ' aria-current="true"';
					}
					?>
					<li><a class="nl" href="<?php echo jat_url( $url ); // phpcs:ignore -- escaped in jat_url(). ?>"<?php echo $aria; // phpcs:ignore ?>><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php echo jat_button( $cta_url, $cta_text, 'primary', 'btn-sm' ); // phpcs:ignore -- escaped in jat_button(). ?>
			<button class="menu-btn" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr( $menu_id ); ?>"><?php echo jat_icon( 'menu' ); // phpcs:ignore ?><span class="lbl"><?php esc_html_e( 'Menu', 'jat-signal' ); ?></span></button>
		</nav>
	</div>
</div>
<?php
// Favicon override chosen in the block (kept from Developer Portfolio).
$favicon_id = (int) ( $attributes['faviconId'] ?? 0 );
if ( $favicon_id && ( $favicon_url = wp_get_attachment_image_url( $favicon_id, array( 512, 512 ) ) ) ) :
	?>
<script>(function(){var u=<?php echo wp_json_encode( $favicon_url ); ?>,e=document.querySelector('link[rel="icon"]');if(e){e.href=u}else{e=document.createElement('link');e.rel='icon';e.href=u;document.head.appendChild(e)}})();</script>
	<?php
endif;
