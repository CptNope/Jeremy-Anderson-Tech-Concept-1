<?php
/**
 * Contact form (jat/contact-form block).
 *
 * Posts to admin-post.php. Works without JavaScript (redirects back with a status) and with it
 * (fetch + JSON). Every inquiry is emailed with wp_mail() and also kept as a private
 * "Inquiry" in the admin, so nothing is lost if the host's mail delivery fails.
 *
 * @package JAT_Signal
 */

defined( 'ABSPATH' ) || exit;

/** Private store for inquiries (Tools-style list in the admin, never public). */
add_action( 'init', static function () {
	register_post_type( 'jat_inquiry', array(
		'labels'          => array(
			'name'          => __( 'Inquiries', 'jat-signal' ),
			'singular_name' => __( 'Inquiry', 'jat-signal' ),
			'edit_item'     => __( 'Inquiry', 'jat-signal' ),
			'search_items'  => __( 'Search inquiries', 'jat-signal' ),
			'not_found'     => __( 'No inquiries yet.', 'jat-signal' ),
		),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => true,
		'menu_position'   => 26,
		'menu_icon'       => 'dashicons-email-alt',
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
		'show_in_rest'    => false,
	) );
} );

/** Where inquiries are sent (JAT Signal screen › Form recipient). */
function jat_form_recipient(): string {
	$to = get_option( 'jat_form_recipient', '' );
	$to = $to ? $to : jat_email();
	return sanitize_email( apply_filters( 'jat_form_recipient', $to ) );
}

/**
 * Validation shared by the handler. Rules mirror the front-end messages in theme.js.
 *
 * @return array{0: array, 1: array} [clean values, errors keyed by field]
 */
function jat_form_validate( array $in ): array {
	$v = array(
		'name'    => sanitize_text_field( wp_unslash( $in['name'] ?? '' ) ),
		'email'   => sanitize_email( wp_unslash( $in['email'] ?? '' ) ),
		'org'     => sanitize_text_field( wp_unslash( $in['org'] ?? '' ) ),
		'site'    => sanitize_text_field( wp_unslash( $in['site'] ?? '' ) ),
		'budget'  => sanitize_text_field( wp_unslash( $in['budget'] ?? '' ) ),
		'when'    => sanitize_text_field( wp_unslash( $in['when'] ?? '' ) ),
		'message' => sanitize_textarea_field( wp_unslash( $in['message'] ?? '' ) ),
		'channel' => array_values( array_intersect( array_map( 'sanitize_key', (array) ( $in['channel'] ?? array() ) ), array_keys( jat_channels() ) ) ),
	);
	$e   = array();
	$len = static fn( $s ) => mb_strlen( trim( $s ) );
	if ( $len( $v['name'] ) < 2 ) {
		$e['name'] = __( 'Add your name so we know who to reply to.', 'jat-signal' );
	} elseif ( $len( $v['name'] ) > 100 ) {
		$e['name'] = __( 'Use 100 characters or fewer.', 'jat-signal' );
	}
	if ( ! $v['email'] || ! is_email( $v['email'] ) ) {
		$e['email'] = __( 'Check the email address. It should look like you@company.com.', 'jat-signal' );
	}
	if ( $v['site'] && ! preg_match( '/^(https?:\/\/)?([a-z0-9-]+\.)+[a-z]{2,}(\/\S*)?$/i', $v['site'] ) ) {
		$e['site'] = __( 'Add the full address, like mycompany.com, or leave it blank.', 'jat-signal' );
	}
	if ( $len( $v['message'] ) < 20 ) {
		$e['message'] = __( 'Tell us a little more about what the system should do.', 'jat-signal' );
	} elseif ( $len( $v['message'] ) > 4000 ) {
		$e['message'] = __( 'Keep it under 4,000 characters. You can share more on the call.', 'jat-signal' );
	}
	foreach ( array( 'org' => 120, 'site' => 200, 'budget' => 60, 'when' => 60 ) as $k => $max ) {
		if ( $len( $v[ $k ] ) > $max ) {
			$e[ $k ] = __( 'That entry is too long.', 'jat-signal' );
		}
	}
	return array( $v, $e );
}

/** Handle a submission. */
function jat_form_handle(): void {
	$wants_json = isset( $_SERVER['HTTP_ACCEPT'] ) && false !== strpos( sanitize_text_field( wp_unslash( $_SERVER['HTTP_ACCEPT'] ) ), 'application/json' );
	$back       = wp_get_referer() ? wp_get_referer() : home_url( '/' );
	$respond    = static function ( string $status, array $data = array(), int $code = 200 ) use ( $wants_json, $back ) {
		if ( $wants_json ) {
			wp_send_json( array_merge( array( 'status' => $status ), $data ), $code );
		}
		wp_safe_redirect( add_query_arg( 'jat_form', $status, remove_query_arg( 'jat_form', $back ) ) . '#contact-form' );
		exit;
	};

	if ( ! isset( $_POST['_jat_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_jat_nonce'] ) ), 'jat_contact' ) ) {
		$respond( 'expired', array( 'message' => __( 'This form expired. Refresh the page and send again; your text is kept on this device.', 'jat-signal' ) ), 403 );
	}
	// Honeypot and a minimum fill time: bots fill hidden fields and submit instantly.
	$started = isset( $_POST['_jat_t'] ) ? (int) $_POST['_jat_t'] : 0;
	if ( ! empty( $_POST['company_url'] ) || ( $started && time() - $started < 3 ) ) {
		$respond( 'sent' ); // Pretend success; nothing is sent.
	}
	// Rate limit: 5 submissions per hour per address.
	$ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key = 'jat_rl_' . md5( $ip . wp_salt( 'nonce' ) );
	$n   = (int) get_transient( $key );
	if ( $n >= 5 ) {
		$respond( 'limited', array( 'message' => sprintf( /* translators: %s email */ __( 'Too many messages from this connection. Email %s instead.', 'jat-signal' ), jat_email() ) ), 429 );
	}

	list( $v, $errors ) = jat_form_validate( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification -- verified above.
	if ( $errors ) {
		$respond( 'invalid', array( 'errors' => $errors ), 422 );
	}
	set_transient( $key, $n + 1, HOUR_IN_SECONDS );

	$chs      = jat_channels();
	$channels = implode( ', ', array_map( static fn( $k ) => $chs[ $k ]['id'] . ' ' . $chs[ $k ]['name'], $v['channel'] ) );
	$lines    = array(
		__( 'Name', 'jat-signal' )         => $v['name'],
		__( 'Email', 'jat-signal' )        => $v['email'],
		__( 'Organization', 'jat-signal' ) => $v['org'],
		__( 'Website', 'jat-signal' )      => $v['site'],
		__( 'Channels', 'jat-signal' )     => $channels,
		__( 'Budget', 'jat-signal' )       => $v['budget'],
		__( 'Start', 'jat-signal' )        => $v['when'],
	);
	$body = '';
	foreach ( $lines as $label => $value ) {
		if ( '' !== $value ) {
			$body .= $label . ': ' . $value . "\n";
		}
	}
	$body .= "\n" . $v['message'] . "\n\n— " . sprintf( /* translators: %s page URL */ __( 'Sent from %s', 'jat-signal' ), esc_url_raw( $back ) );

	/* translators: %s: sender name */
	$subject = sprintf( __( 'New inquiry from %s', 'jat-signal' ), $v['name'] );
	wp_insert_post( array(
		'post_type'    => 'jat_inquiry',
		'post_status'  => 'private',
		'post_title'   => $subject . ( $v['org'] ? ' · ' . $v['org'] : '' ),
		'post_content' => $body,
	) );
	$sent = wp_mail( jat_form_recipient(), '[' . wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) . '] ' . $subject, $body, array( 'Reply-To: ' . $v['name'] . ' <' . $v['email'] . '>' ) );

	$respond( 'sent', array( 'mailed' => (bool) $sent ) );
}
add_action( 'admin_post_nopriv_jat_contact', 'jat_form_handle' );
add_action( 'admin_post_jat_contact', 'jat_form_handle' );
