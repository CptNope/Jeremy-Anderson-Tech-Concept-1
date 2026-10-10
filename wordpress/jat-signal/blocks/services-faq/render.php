<?php
/**
 * developer-portfolio/services-faq — questions as disclosures, with FAQPage structured data.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) ( $attributes['heading'] ?? 'Common Questions' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$items   = array_values( array_filter( jat_json( $attributes['items'] ?? '[]' ), static fn( $i ) => ! empty( $i['q'] ) ) );
if ( ! $items ) {
	return;
}
$hid = jat_id( 'faq' );
echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( $heading, $intro, $hid ); // phpcs:ignore
echo '<div class="faq">';
foreach ( $items as $item ) {
	echo '<details><summary>' . esc_html( $item['q'] ) . jat_icon( 'plus' ) . '</summary><div class="faq-a"><p>' . esc_html( $item['a'] ?? '' ) . '</p></div></details>'; // phpcs:ignore
}
echo '</div>';
echo jat_section_close(); // phpcs:ignore
$schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'mainEntity' => array_map( static fn( $i ) => array( '@type' => 'Question', 'name' => $i['q'], 'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $i['a'] ?? '' ) ), $items ),
);
echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>';
