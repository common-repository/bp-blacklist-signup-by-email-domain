<?php
/*
Plugin Name: BP Blacklist Signup By Email Domain
Description: Only allow users with email addresses from domains not in the blacklist to register in BuddyPress.
Version: 1.1.0
Author: Venutius
Plugin URI: https://buddyuser.com/plugin-bp-blcklist-signup-by-email-domain/
Author URI: https://buddyuser.com
License: GPLv2 or later
Text Domain: bp-blacklist-signup-by-email-domain
 * Domain Path: /languages
*/

/**
 * BP Blacklist Signup By Email Domain Core.
 *
 * @package BP_BSED
 * @subpackage Core
 */

add_action( 'bp_include', array( 'BP_BSED', 'init' ) );

/**
 * BP Blacklist Signup By Email Domain.
 *
 * @since 1.0.0
 */
class BP_BSED {
	/**
	 * Static initializer.
	 */
	public static function init() {
		return new self();
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		// This plugin only works on BuddyPress 1.6+.
		if ( false === function_exists( 'bp_version' ) ) {
			return;
		}

		// Translations for everybody!
		if ( file_exists( dirname( __FILE__ ).'/languages/bp-blacklist-signup-by-email-domain-' . get_locale() . 'mo' ) ) {
			load_plugin_textdomain('bp-blacklist-signup-by-email-domain', dirname( __FILE__ ).'/languages/bp-blacklist-signup-by-email-domain-' . get_locale() . 'mo' );
		}		load_plugin_textdomain( 'bp-blacklist-signup-by-email-domain' );

		// Hooks.
		add_action( 'bp_signup_validate',               array( $this, 'validate' ) );
		add_action( 'bp_before_account_details_fields', array( $this, 'registration_msg' ) );

		// Set up admin area if in the WP dashboard.
		if ( defined( 'WP_NETWORK_ADMIN' ) ) {
			require dirname( __FILE__ ) . '/bp-bsed-admin.php';
			BP_BSED_Admin_Settings::init();
		}
	}

	/**
	 * Validate email address against custom blacklisted email domains.
	 */
	public function validate() {
		global $bp;

		$allowed_domains = self::allowed_domains();

		// make sure we have some blacklisted domains added
		if ( empty( $allowed_domains ) ) {
			return;
		}

		// get full email domain of email address
		$email_domain = substr(
			$bp->signup->email,
			strpos( $bp->signup->email, '@' ) + 1
		);

		$okay = true;

		foreach ( $allowed_domains as $allowed ) {
			// See if email address matches a blacklisted domain.
			if ( self::string_ends_with( $email_domain, $allowed ) ) {
				// First character doesn't contain a dot; check length.
				if ( strpos( $allowed, '.' ) !== 0 ) {
					if ( strlen( $allowed ) == strlen( $email_domain ) ) {
						$okay = false;
						break;
					}

				} else {
					$okay = false;
					break;
				}
			}
		}

		// Email address doesn't match any blacklisted domain, so throw up an error.
		if ( ! $okay ) {
			$settings = self::get_settings();

			$bp->signup->errors['signup_email'] = sprintf(
				str_replace( '{{domains}}', '%s', wp_kses_data( $settings['error_msg'] ) ),
				implode( ', ', $allowed_domains )
			);
		}

	}

	/**
	 * Show custom registration blurb if available.
	 *
	 * Only shown if blacklisted email domains are set.
	 */
	public function registration_msg() {
		$allowed_domains = self::allowed_domains();

		// Make sure we have some blacklisted domains added.
		if ( empty( $allowed_domains ) ) {
			return;
		}

		$settings = self::get_settings();

		if ( empty( $settings['registration_msg'] ) ) {
			return;
		}

		echo apply_filters( 'comment_text',
			sprintf(
				str_replace(
					array( '{{domains}}', '{{adminemail}}' ),
					array( '%1$s', '%2$s' ),
					$settings['registration_msg']
				),
				'<strong>' . implode( ', ', esc_url($allowed_domains) ) . '</strong>',
				antispambot( bp_get_option( 'admin_email' ) )
			)
		);
	}

	/**
	 * Only allow certain email domains to register in BuddyPress.
	 *
	 * @return array
	 */
	public static function allowed_domains() {
		$settings = self::get_settings();

		if ( empty( $settings['blacklist_domains'] ) ) {
			return false;
		}

		$domains = preg_split( '/\R/', $settings['blacklist_domains'] );

		return apply_filters( 'bp_bsed_refused_domains', $domains );
	}

	/**
	 * Get our settings.
	 *
	 * Sets defaults if not set.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$settings = bp_get_option( 'bp_bsed' );

		// Set defaults.
		if ( is_string( $settings ) ) {
			$settings = array();

			// Can be overriden on settings page.
			if ( empty( $settings['registration_msg'] ) ) {
				$settings['registration_msg'] = esc_attr__( 'Registrations are currently will not be allowed for email addresses from these domains - {{domains}}. If you are having difficulty, please write to {{adminemail}} to request an account.', 'bp-blacklist-signup-by-email-domain' );
			}

		}

		// Always have an error message.
		if ( empty( $settings['error_msg'] ) ) {
			$settings['error_msg'] = esc_attr__( 'This email address is not allowed. Please use another domain', 'bp-blacklist-signup-by-email-domain' );
		}

		return $settings;
	}

	/**
	 * Static method to find out if a string ends with a certain string.
	 *
	 * @link http://stackoverflow.com/a/619725
	 *
	 * @param string $str The string we want to test against
	 * @param string $test The string we're attempting to find
	 * @bool
	 */
	public static function string_ends_with( $str, $test ) {
	    $strlen = strlen($str);
		$testlen = strlen($test);
		if ($testlen > $strlen) return false;

		return substr_compare( $str, $test, $strlen - $testlen, $testlen ) === 0;
	}

}
