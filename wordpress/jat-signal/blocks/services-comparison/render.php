<?php
/**
 * developer-portfolio/services-comparison — comparison table. The first data column (you) is highlighted;
 * cell types (gold, check, cross, partial, muted) become icons plus text, never color alone.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$heading = (string) ( $attributes['heading'] ?? 'How This Compares' );
$intro   = (string) ( $attributes['intro'] ?? '' );
$columns = array_map( 'strval', jat_json( $attributes['columns'] ?? '[]' ) );
$rows    = jat_json( $attributes['rows'] ?? '[]' );
if ( ! $columns || ! $rows ) {
	return;
}
$hid  = jat_id( 'cmp' );
$icon = array( 'check' => 'check', 'cross' => 'cross', 'partial' => 'part' );
$sr   = array( 'check' => __( 'Yes', 'jat-signal' ), 'cross' => __( 'No', 'jat-signal' ), 'partial' => __( 'Partly', 'jat-signal' ) );

echo jat_section_open( '', $hid ); // phpcs:ignore
echo jat_section_head( $heading, $intro, $hid ); // phpcs:ignore
?>
<div class="table-wrap scroll-x" tabindex="0" role="region" aria-label="<?php echo esc_attr( sprintf( /* translators: %s table heading */ __( '%s, scrollable table', 'jat-signal' ), $heading ) ); ?>">
	<table class="spec compare stack" role="table">
		<thead><tr role="row">
			<?php foreach ( $columns as $i => $col ) : ?>
				<th scope="col" role="columnheader"<?php echo 1 === $i ? ' class="you"' : ''; ?>><?php echo '' === $col ? '<span class="vh">' . esc_html__( 'Criterion', 'jat-signal' ) . '</span>' : esc_html( $col ); ?></th>
			<?php endforeach; ?>
		</tr></thead>
		<tbody>
			<?php foreach ( $rows as $row ) : ?>
				<tr role="row">
					<th scope="row" role="rowheader" data-label=""><?php echo esc_html( $row['label'] ?? '' ); ?></th>
					<?php
					foreach ( (array) ( $row['cells'] ?? array() ) as $ci => $cell ) :
						$type  = $row['types'][ $ci ] ?? 'muted';
						$cell  = (string) $cell;
						$label = $columns[ $ci + 1 ] ?? '';
						$text  = in_array( trim( $cell ), array( '✓', '✔', '✗', '✕', '—', '-' ), true ) ? '' : $cell;
						?>
						<td role="cell" data-label="<?php echo esc_attr( $label ); ?>" class="t-<?php echo esc_attr( $type ); ?><?php echo 0 === $ci ? ' you' : ''; ?>">
							<?php if ( isset( $icon[ $type ] ) ) : ?>
								<span class="mark mark-<?php echo esc_attr( $type ); ?>"><?php echo jat_icon( $icon[ $type ] ); // phpcs:ignore ?></span><?php echo '' === $text ? '<span class="vh">' . esc_html( $sr[ $type ] ) . '</span>' : ''; ?>
							<?php endif; ?>
							<?php echo esc_html( $text ); ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<?php
echo jat_section_close(); // phpcs:ignore
