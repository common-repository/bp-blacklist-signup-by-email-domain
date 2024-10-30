<?php
/**
 * Admin code for BP Blacklist Signup By Email Domain
 *
 * @package BP_BSED
 * @subpackage Admin
 */

/**
 * Admin handler class for BP Blacklist Signup By Email Domain.
 *
 * @since 1.0.0
 */
class BP_BSED_Admin_Settings {

	/**
	 * Settings holder.
	 *
	 * @var array
	 */
	protected $settings = array();

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
		add_action( 'bp_admin_init',              array( $this, 'screen' ),     99 );
		add_action( 'bp_register_admin_settings', array( $this, 'add_fields' ), 50 );
	}

	/**
	 * Sets up the screen.
	 *
	 * This method handles saving for our custom form fields and grabs our settings
	 * only when on the "BuddyPress > Settings" admin page.
	 */
	public function screen() {
		// We're on the bp-settings page
		if ( isset( $_GET['page'] ) && 'bp-settings' == $_GET['page'] ) {
			// save
			if ( ! empty( $_POST['submit'] ) ) {
				global $wp_settings_fields;

				// Piggyback off existing admin referer.
				check_admin_referer( 'buddypress-options' );

				// Do not let bp_core_admin_settings_save() save our placeholder fields.
				unset( $wp_settings_fields['buddypress']['bp_bsed'] );

				// Sanitize before saving
				$retval = array();
				$retval['blacklist_domains'] = wp_filter_nohtml_kses( trim( $_REQUEST['bp_bsed']['blacklist_domains'] ) );
				$retval['error_msg']         = wp_filter_nohtml_kses( $_REQUEST['bp_bsed']['error_msg'] );
				$retval['registration_msg']  = wp_filter_kses( $_REQUEST['bp_bsed']['registration_msg'] );

				bp_update_option( 'bp_bsed', $retval );
			}

			// Get settings.
			$this->settings = BP_BSED::get_settings();
		}
	}

	/**
	 * Register our form fields.
	 *
	 * This is so they show up on the "BuddyPress > Settings" admin page.  Note:
	 * The settings fields passed as the first parameter in add_settings_field()
	 * are placeholders.
	 *
	 * Actual field names are named in the respective callbacks and saved under
	 * the screen() method.
	 */
	public function add_fields() {
		add_settings_section( 'bp_bsed', esc_attr__( 'Email Address Restrictions', 'bp-blacklist-signup-by-email-domain' ), '__return_false', 'buddypress' );

		add_settings_field(
			'bp-blacklist-signup-by-email-domain-whitelist-domains',
			esc_attr__( 'Blacklist Email Domains', 'bp-blacklist-signup-by-email-domain' ),
			array( $this, 'textarea_blacklist_domains' ),
			'buddypress',
			'bp_bsed',
			array( 'label_for' => 'bp_bsed_blacklist_domains' )
		);

		add_settings_field(
			'bp-blacklist-signup-by-email-domain-error-msg',
			esc_attr__( 'Error Message', 'bp-blacklist-signup-by-email-domain' ),
			array( $this, 'textarea_error_msg' ),
			'buddypress',
			'bp_bsed',
			array( 'label_for' => 'bp_bsed_error_msg' )
		);

		add_settings_field(
			'bp-blacklist-signup-by-email-domain-registration-msg',
			esc_attr__( 'Registration Message', 'bp-blacklist-signup-by-email-domain' ),
			array( $this, 'textarea_registration_msg' ),
			'buddypress',
			'bp_bsed',
			array( 'label_for' => 'bp_bsed_registration_msg' )
		);
	}

	/** FORM FIELDS ***************************************************/

	/**
	 * Callback for add_settings_field().
	 */
	public function textarea_blacklist_domains() {
		$value = isset( $this->settings['blacklist_domains'] ) ? $this->settings['blacklist_domains'] : '';
	?>

		<textarea class="large-text" rows="5" cols="45" id="bp_bsed_blacklist_domains" name="bp_bsed[blacklist_domains]"><?php echo esc_textarea( $value ); ?></textarea>
		<p class="description"><?php esc_attr_e( 'Limit site registrations to exclude certain domains. One domain per line.', 'bp-blacklist-signup-by-email-domain' ); ?></p>

	<?php
	}

	/**
	 * Callback for add_settings_field().
	 */
	public function textarea_error_msg() {
		$value = stripslashes( $this->settings['error_msg'] );
	?>

		<textarea class="large-text" rows="5" cols="45" id="bp_bsed_error_msg" name="bp_bsed[error_msg]"><?php echo esc_textarea( $value ); ?></textarea>
		<p class="description"><?php esc_attr_e( 'When a user enters an email address that matches a blacklisted domain, this error message gets displayed.  You can use the {{domains}} token to list the email domains that are allowed.', 'bp-blacklist-signup-by-email-domain' ); ?></p>

	<?php
	}

	/**
	 * Callback for add_settings_field().
	 */
	public function textarea_registration_msg() {
		$value = stripslashes( $this->settings['registration_msg'] );
	?>

		<textarea class="large-text" rows="5" cols="45" id="bp_bsed_registration_msg" name="bp_bsed[registration_msg]"><?php echo esc_textarea( $value ); ?></textarea>
		<p class="description"><?php esc_attr_e( 'Enter a custom message to display on the registration page. This will be shown right before the registration form.  You can use the {{domains}} token to list the email domains that are not allowed and the {{adminemail}} token to display the email address of the administrator.  Leave blank if desired.', 'bp-blacklist-signup-by-email-domain' ); ?></p>

	<?php
	}
}
