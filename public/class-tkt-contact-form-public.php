<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_Contact_Form
 * @subpackage Tkt_Contact_Form/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, registers styles and scripts.
 * Creates ShortCode for the Contact form and Handles Email Sending.
 *
 * @package    Tkt_Contact_Form
 * @subpackage Tkt_Contact_Form/public
 * @author     Your Name <hello@tukutoi.com>
 */
class Tkt_Contact_Form_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The unique prefix of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_prefix    The string used to uniquely prefix technical functions of this plugin.
	 */
	private $plugin_prefix;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name      The name of the plugin.
	 * @param      string $plugin_prefix          The unique prefix of this plugin.
	 * @param      string $version          The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_prefix, $version ) {

		$this->plugin_name   = $plugin_name;
		$this->plugin_prefix = $plugin_prefix;
		$this->version = $version;

		$this->required_fields = array(
			'eman_dleif'    => '',
			'liame_dleif'   => '',
			'tcejbus_dleif' => '',
			'egassem_dleif' => '',
		);

		$this->form_fields = array(
			'eman_dleif'        => '',
			'liame_dleif'       => '',
			'tcejbus_dleif'     => '',
			'egassem_dleif'     => '',
			'submit'            => '',
			'_wpnonce'          => '',
			'_wp_http_referer'  => '',
			'error_empty'       => '',
			'error_noemail'     => '',
			'success'           => '',
			'id'                => '',
		);

		$this->honeypot_fields = array(
			'name'    => '',
			'email'   => '',
			'subject' => '',
			'message' => '',
		);

		$this->send_email_response = array(
			'sent'      => false,
			'result'    => null,
		);

		$this->error = array(
			'eman_dleif'    => '',
			'liame_dleif'   => '',
			'tcejbus_dleif' => '',
			'egassem_dleif' => '',
		);

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tkt-contact-form-public.css', array(), $this->version, 'screen' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tkt-contact-form-public.js', array( 'jquery' ), $this->version, true );

	}

	/**
	 * The Contact Form ShortCode
	 *
	 * @see https://developer.wordpress.org/plugins/shortcodes/enclosing-shortcodes/
	 *
	 * @since    1.0.0
	 * @param    array  $atts    ShortCode Attributes.
	 * @param    mixed  $content ShortCode enclosed content.
	 * @param    string $tag    The Shortcode tag.
	 */
	public function contact_form( $atts, $content = null, $tag ) {

		$atts = shortcode_atts(
			array(
				'id'            => 1,
				'subject'       => '',
				'label_name'    => 'Your Name',
				'label_email'   => 'Your E-mail Address',
				'label_subject' => 'Subject',
				'label_message' => 'Your Message',
				'label_submit'  => 'Submit',
				'error_empty'   => 'Please fill in all the required fields.',
				'error_noemail' => 'Please enter a valid e-mail address.',
				'success'       => "Thanks for your e-mail! We'll get back to you as soon as we can.",
			),
			$atts,
			$tag
		);

		// Sanitize/Validate ShortCode attribute values.
		$atts = $this->sanitize_atts( $atts );

		// Enqueue scripts and styles only when ShortCode is used.
		$this->enqueue_on_demand();

		// Get the HTML Form or/and Message.
		$contact_form = $this->form( $this->send_email_response, $atts, $this->form_fields );

		// Display Success Message if send form successful, or error + form if failure.
		if ( isset( $_GET['success'] ) && 'true' === $_GET['success'] ) {
			return $contact_form['info'];
		} else {
			return $contact_form['info'] . $contact_form['form'];
		}

	}

	/**
	 * Build the HTML Form
	 *
	 * @return array Send Email Response.
	 */
	public function handle_contact_form() {

		if ( empty( $_POST ) ) {
			return $this->send_email_response;
		}

		$sent = false;

		// Check Nonce.
		if ( isset( $_REQUEST['_wpnonce'] ) && false === wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'tkt_cntct_frm_nonce' ) ) {
			return $this->send_email_response;
		}

		$error = false;

		foreach ( $this->honeypot_fields as $field => $value ) {
			unset( $_POST[ $field ] );
		}

		/**
		 * If there are any other fields in $_POST something is sketchy, thus abort.
		 * We expect an empty array (no difference).
		 */
		$diff = array_diff_key( $_POST, $this->form_fields );

		if ( ! empty( $diff ) ) {
			return $this->send_email_response;
		}

		// fetch everything that has been POSTed, sanitize.
		foreach ( $_POST as $field => $value ) {

			if ( 'liame_dleif' === $field ) {
				$value = sanitize_email( $value );
			} elseif ( 'egassem_dleif' === $field ) {
				$value = sanitize_textarea_field( $value );
			} else {
				$value = sanitize_text_field( $value );
			}

			$this->form_fields[ $field ] = $value;

		}

		// if the required fields are empty, switch $error to TRUE and set the result text to the shortcode attribute named 'error_empty'.
		foreach ( $this->required_fields as $required_field => $value ) {

			$value = trim( $this->form_fields[ $required_field ] );

			if ( empty( $value ) ) {
				$this->error[ $required_field ] = 'tkt-missing-or-invalid';
				$error = true;
				$result = $this->form_fields['error_empty'];
			}
		}

		// if the e-mail is not valid or missing, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'.
		if ( ! is_email( $this->form_fields['liame_dleif'] ) ) {
			$this->error['liame_dleif'] = 'tkt-missing-or-invalid';
			$error = true;
			$result = $this->form_fields['error_noemail'];
		}

		if ( false === $error ) {

			$email_subject = sanitize_text_field( $this->form_fields['tcejbus_dleif'] );
			$email_message = sanitize_textarea_field( $this->form_fields['egassem_dleif'] ) . "\n\nIP: " . sanitize_text_field( $this->get_the_ip() );
			$headers  = 'From: ' . sanitize_text_field( $this->form_fields['eman_dleif'] ) . ' <' . sanitize_email( $this->form_fields['liame_dleif'] ) . ">\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			$receiver = sanitize_email( apply_filters( 'tkt_cntct_frm_email', get_bloginfo( 'admin_email' ), $this->form_fields['id'] ) );

			$email_subject = sanitize_text_field( apply_filters( 'tkt_cntct_frm_subject', $email_subject, $this->form_fields, $receiver ) );
			$email_message = sanitize_textarea_field( apply_filters( 'tkt_cntct_frm_message', $email_message, $this->form_fields, $receiver ) );

			do_action( 'tkt_cntct_frm_pre_send_mail', $this->form_fields );
			wp_mail( $receiver, $email_subject, $email_message, $headers );
			do_action( 'tkt_cntct_frm_post_send_mail', $receiver, $email_subject, $email_message, $headers, $this->form_fields );

			if ( $_SERVER && isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
				$redirect_url  = is_ssl() ? 'https://' : 'http://';
				/**
				 * We can NOT escape or sanitize an URL here at this point! False WPCS alarm.
				 * If we do this, since we do not have a protocol yet prepended, esc_url_raw will fallback to HTTP.
				 */
				$redirect_url .= wp_unslash( $_SERVER['HTTP_HOST'] );
				$redirect_url .= sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
				$redirect_url = $redirect_url . '?success=true';
			} else {
				$redirect_url = get_home_url();
			}
			$redirect_url = apply_filters( 'tkt_cntct_frm_redirect_uri', $redirect_url, $this->form_fields['id'] );

			do_action( 'tkt_cntct_frm_pre_redirect', $redirect_url, $this->form_fields['id'] );
			wp_redirect( esc_url_raw( $redirect_url ) );
			do_action( 'tkt_cntct_frm_post_redirect' );

			exit;

		}

		$this->send_email_response = array(
			'sent'      => $sent,
			'result'    => $result,
		);

	}

	/**
	 * Build the HTML Form
	 *
	 * @param array $send_email_response The response of the send_mail function.
	 * @param array $atts The ShortCode attributes.
	 * @param array $form_data The Form POST Data.
	 */
	private function form( $send_email_response, $atts, $form_data ) {

		$info = '';

		if ( ! empty( $send_email_response['result'] ) ) {
			$info = '<div class="tkt-error">' . $send_email_response['result'] . '</div>';
		} elseif ( isset( $_GET['success'] ) && 'true' === $_GET['success'] ) {
			$info = '<div class="tkt-success">' . $atts['success'] . '</div>';
		}

		/**
		 * Build Form HTML.
		 * NOTE: Real Field Name/ID are mirrored. Non mirrored field names and IDs signify Bot Fields (honeypot)
		 */
		$form = '<form class="tkt-contact-form" method="post" action="' . esc_url_raw( get_permalink() ) . '" id="' . esc_attr( $atts['id'] ) . '">
		    <div>
		        <label for="eman_dleif">' . esc_html( $atts['label_name'] ) . ':</label>
		        <input type="text" class="' . esc_attr( $this->error['eman_dleif'] ) . '" name="eman_dleif" id="eman_dleif" size="50" maxlength="50" value="' . esc_attr( $form_data['eman_dleif'] ) . '" />
		    </div>
		    <div>
		        <label for="liame_dleif">' . esc_html( $atts['label_email'] ) . ':</label>
		        <input type="text" class="' . esc_attr( $this->error['liame_dleif'] ) . '" name="liame_dleif" id="liame_dleif" value="' . esc_attr( $form_data['liame_dleif'] ) . '" />
		    </div>
		    <div>
		        <label for="tcejbus_dleif">' . esc_html( $atts['label_subject'] ) . ':</label>
		        <input type="text" class="' . esc_attr( $this->error['tcejbus_dleif'] ) . '" name="tcejbus_dleif" id="tcejbus_dleif" size="50" maxlength="50" value="' . esc_attr( $form_data['tcejbus_dleif'] ) . '" />
		    </div>
		    <div>
		        <label for="egassem_dleif">' . esc_html( $atts['label_message'] ) . ':</label>
		        <textarea class="' . esc_attr( $this->error['egassem_dleif'] ) . '" name="egassem_dleif" id="egassem_dleif" cols="50" rows="15">' . esc_textarea( $form_data['egassem_dleif'] ) . '</textarea>
		    </div>

			<label class="tkt-ohnohoney" for="name"></label>
		    <input class="tkt-ohnohoney" autocomplete="off" type="text" name="name" id="name" />
		    <label class="tkt-ohnohoney" for="email"></label>
		    <input class="tkt-ohnohoney" autocomplete="off" type="email" name="email" id="email" />
		    <label class="tkt-ohnohoney" for="subject"></label>
		    <input class="tkt-ohnohoney" autocomplete="off" type="text" name="subject" id="subject" />
		    <label class="tkt-ohnohoney" for="message"></label>
		    <textarea class="tkt-ohnohoney" autocomplete="off" name="message" id="message"></textarea>

		    <div>
		        <input type="submit" value="' . esc_attr( $atts['label_submit'] ) . '" name="submit" id="submit" />
		    </div>
		    <input type="hidden" id="error_empty" name="error_empty" value="' . esc_attr( $atts['error_empty'] ) . '">
		    <input type="hidden" id="error_noemail" name="error_noemail" value="' . esc_attr( $atts['error_noemail'] ) . '">
		    <input type="hidden" id="success" name="success" value="' . esc_attr( $atts['success'] ) . '">
		    <input type="hidden" id="id" name="id" value="' . esc_attr( $atts['id'] ) . '">

		    ' . wp_nonce_field( 'tkt_cntct_frm_nonce', '_wpnonce', true, false ) . '
		</form>';

		$contact_form = array(
			'info'  => $info,
			'form'  => $form,
		);

		return $contact_form;

	}

	/**
	 * Sanitize/Validate the ShortCode attributes.
	 * At the moment only for Email and Text/Area fields.
	 *
	 * @param array $atts The ShortCode Attributes.
	 */
	private function sanitize_atts( $atts ) {

		foreach ( $atts as $key => $value ) {
			if ( 'email' === $key ) {
				$atts[ $key ] = sanitize_email( $value );
			} else {
				$atts[ $key ] = sanitize_text_field( $value );
			}
		}

		return $atts;

	}

	/**
	 * Enqueue Scripts and Styles on Demand.
	 */
	private function enqueue_on_demand() {

		wp_enqueue_style( $this->plugin_name );
		wp_enqueue_script( $this->plugin_name );

	}

	/**
	 * Try to fetch the User's real IP
	 */
	private function get_the_ip() {
		if ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) );
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['HTTP_CLIENT_IP'] ) );
		} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
			return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
		} else {
			return 'Could not detect the IP';
		}
	}

}
