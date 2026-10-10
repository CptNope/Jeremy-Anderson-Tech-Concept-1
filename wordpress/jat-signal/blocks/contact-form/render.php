<?php
/**
 * jat/contact-form — inquiry form plus "what happens next".
 * Sends through inc/forms.php (email + private copy under Inquiries). If a Contact Form 7
 * shortcode or form ID is set and the plugin is active, that form is shown instead, in the same frame.
 *
 * @var array $attributes
 */

defined( 'ABSPATH' ) || exit;

$cf7    = trim( (string) ( $attributes['cf7'] ?? '' ) );
$steps  = jat_json( $attributes['steps'] ?? '[]' );
$status = isset( $_GET['jat_form'] ) ? sanitize_key( wp_unslash( $_GET['jat_form'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- display only.
$hid    = jat_id( 'inquiry' );
$fid    = jat_id( 'f' );
$f      = static fn( $n ) => $fid . '-' . $n;
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'section contact', 'id' => 'contact-form', 'aria-labelledby' => $hid ) ); ?>>
	<div class="wrap aside-col">
		<div>
			<h2 id="<?php echo esc_attr( $hid ); ?>" class="intro-h"><?php echo esc_html( (string) ( $attributes['heading'] ?? __( 'Project inquiry', 'jat-signal' ) ) ); ?></h2>
			<?php if ( $cf7 && shortcode_exists( 'contact-form-7' ) ) : ?>
				<div class="cf7-frame"><?php echo do_shortcode( ctype_digit( $cf7 ) || preg_match( '/^[a-f0-9]{7}$/', $cf7 ) ? '[contact-form-7 id="' . esc_attr( $cf7 ) . '"]' : $cf7 ); // phpcs:ignore ?></div>
			<?php else : ?>
				<form class="form" data-jat-form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
					<div class="form-status" role="status" aria-live="polite" tabindex="-1"<?php echo in_array( $status, array( 'sent', 'invalid', 'expired', 'limited' ), true ) ? ' data-kind="' . ( 'sent' === $status ? 'ok' : 'err' ) . '"' : ' hidden'; ?>>
						<?php
						if ( 'sent' === $status ) {
							echo '<strong>' . esc_html__( 'Thanks, your message is in.', 'jat-signal' ) . '</strong> ' . esc_html__( 'You’ll hear back within one business day.', 'jat-signal' );
						} elseif ( 'invalid' === $status ) {
							echo '<strong>' . esc_html__( 'Some fields need attention.', 'jat-signal' ) . '</strong> ' . esc_html__( 'Check your name, email and message, then send again.', 'jat-signal' );
						} elseif ( 'expired' === $status ) {
							echo esc_html__( 'This form expired. Please send it again.', 'jat-signal' );
						} elseif ( 'limited' === $status ) {
							/* translators: %s email */
							echo esc_html( sprintf( __( 'Too many messages from this connection. Email %s instead.', 'jat-signal' ), jat_email() ) );
						}
						?>
					</div>
					<input type="hidden" name="action" value="jat_contact">
					<input type="hidden" name="_jat_t" value="<?php echo esc_attr( (string) time() ); ?>">
					<?php wp_nonce_field( 'jat_contact', '_jat_nonce' ); ?>
					<div class="hp" aria-hidden="true"><label for="<?php echo esc_attr( $f( 'url' ) ); ?>">Company URL</label><input id="<?php echo esc_attr( $f( 'url' ) ); ?>" name="company_url" type="text" tabindex="-1" autocomplete="off"></div>
					<div class="form-row">
						<div class="field"><label for="<?php echo esc_attr( $f( 'name' ) ); ?>"><?php esc_html_e( 'Name', 'jat-signal' ); ?></label><input class="input" id="<?php echo esc_attr( $f( 'name' ) ); ?>" name="name" type="text" autocomplete="name" required maxlength="100"><p class="msg"><?php echo jat_icon( 'cross' ); // phpcs:ignore ?><span></span></p></div>
						<div class="field"><label for="<?php echo esc_attr( $f( 'email' ) ); ?>"><?php esc_html_e( 'Work email', 'jat-signal' ); ?></label><input class="input" id="<?php echo esc_attr( $f( 'email' ) ); ?>" name="email" type="email" autocomplete="email" required maxlength="254" placeholder="you@company.com"><p class="msg"><?php echo jat_icon( 'cross' ); // phpcs:ignore ?><span></span></p></div>
					</div>
					<div class="form-row">
						<div class="field"><label for="<?php echo esc_attr( $f( 'org' ) ); ?>"><?php esc_html_e( 'Company or organization (optional)', 'jat-signal' ); ?></label><input class="input" id="<?php echo esc_attr( $f( 'org' ) ); ?>" name="org" type="text" autocomplete="organization" maxlength="120"></div>
						<div class="field"><label for="<?php echo esc_attr( $f( 'site' ) ); ?>"><?php esc_html_e( 'Current website (optional)', 'jat-signal' ); ?></label><input class="input" id="<?php echo esc_attr( $f( 'site' ) ); ?>" name="site" type="text" inputmode="url" autocomplete="url" maxlength="200" placeholder="mycompany.com"><p class="msg"><?php echo jat_icon( 'cross' ); // phpcs:ignore ?><span></span></p></div>
					</div>
					<?php if ( ! isset( $attributes['showChannels'] ) || $attributes['showChannels'] ) : ?>
						<fieldset class="field">
							<legend><?php esc_html_e( 'What do you need? (choose any)', 'jat-signal' ); ?></legend>
							<div class="choices">
								<?php foreach ( jat_channels() as $key => $c ) : ?>
									<label class="choice" style="--c:var(<?php echo esc_attr( $c['var'] ); ?>)"><input type="checkbox" name="channel[]" value="<?php echo esc_attr( $key ); ?>"><span><i aria-hidden="true"></i><?php echo esc_html( $c['id'] . ' · ' . $c['name'] ); ?></span></label>
								<?php endforeach; ?>
							</div>
						</fieldset>
					<?php endif; ?>
					<div class="form-row">
						<div class="field"><label for="<?php echo esc_attr( $f( 'budget' ) ); ?>"><?php esc_html_e( 'Budget range', 'jat-signal' ); ?></label>
							<select class="input" id="<?php echo esc_attr( $f( 'budget' ) ); ?>" name="budget">
								<?php foreach ( array( __( 'Not sure yet', 'jat-signal' ), __( 'Under $2,500', 'jat-signal' ), __( '$2,500 – $7,500', 'jat-signal' ), __( '$7,500 – $20,000', 'jat-signal' ), __( '$20,000+', 'jat-signal' ), __( 'Monthly care plan', 'jat-signal' ) ) as $o ) : ?><option><?php echo esc_html( $o ); ?></option><?php endforeach; ?>
							</select></div>
						<div class="field"><label for="<?php echo esc_attr( $f( 'when' ) ); ?>"><?php esc_html_e( 'Ideal start', 'jat-signal' ); ?></label>
							<select class="input" id="<?php echo esc_attr( $f( 'when' ) ); ?>" name="when">
								<?php foreach ( array( __( 'Flexible', 'jat-signal' ), __( 'Within a month', 'jat-signal' ), __( 'In 1–3 months', 'jat-signal' ), __( 'Later this year', 'jat-signal' ) ) as $o ) : ?><option><?php echo esc_html( $o ); ?></option><?php endforeach; ?>
							</select></div>
					</div>
					<div class="field">
						<label for="<?php echo esc_attr( $f( 'message' ) ); ?>"><?php esc_html_e( 'What should the system do?', 'jat-signal' ); ?></label>
						<textarea class="input" id="<?php echo esc_attr( $f( 'message' ) ); ?>" name="message" rows="6" required minlength="20" maxlength="4000" aria-describedby="<?php echo esc_attr( $f( 'hint' ) ); ?>"></textarea>
						<div class="field-hint"><span class="hint" id="<?php echo esc_attr( $f( 'hint' ) ); ?>"><?php esc_html_e( 'A few sentences is plenty: the problem, who uses it, and any deadline.', 'jat-signal' ); ?></span><span class="readout count" data-count aria-hidden="true">0 / 4,000</span></div>
						<p class="msg"><?php echo jat_icon( 'cross' ); // phpcs:ignore ?><span></span></p>
					</div>
					<div class="cta-row">
						<button class="btn btn-primary" type="submit"><span class="lbl"><?php echo esc_html( (string) ( $attributes['submitText'] ?? __( 'Request a discovery call', 'jat-signal' ) ) ); ?></span><?php echo jat_icon( 'arrow' ); // phpcs:ignore ?></button>
						<span class="readout long"><?php esc_html_e( 'Proposal within 3 business days of the call', 'jat-signal' ); ?></span>
					</div>
				</form>
			<?php endif; ?>
		</div>
		<div class="sticky-card">
			<span class="readout"><?php echo esc_html( (string) ( $attributes['asideTitle'] ?? __( 'What happens next', 'jat-signal' ) ) ); ?></span>
			<?php if ( $steps ) : ?>
				<ol class="next-steps">
					<?php foreach ( $steps as $s ) : ?>
						<li><span class="n"><?php echo esc_html( $s['meta'] ?? '' ); ?></span><strong class="t-item"><?php echo esc_html( $s['title'] ?? '' ); ?></strong><span class="muted small"><?php echo esc_html( $s['text'] ?? '' ); ?></span></li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>
			<?php echo jat_copy_line(); // phpcs:ignore ?>
		</div>
	</div>
</section>
