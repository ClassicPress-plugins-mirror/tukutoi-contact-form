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
 * Defines the plugin name, version, and two hooks to
 * enqueue the public-facing stylesheet and JavaScript.
 * As you add hooks and methods, update this description.
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

		$this->form_data = array(
			'eman_dleif'    => '',
			'liame_dleif'   => '',
			'tcejbus_dleif' => '',
			'egassem_dleif' => '',
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
				'email'         => get_bloginfo( 'admin_email' ),
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

		// If the form is POSTed attemt to send the email.
		if ( $_SERVER && isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] ) {
			$this->send_mail( $atts );
		}

		// Get the HTML Form or Success Message.
		$contact_form = $this->form( $this->send_email_response, $atts, $this->form_data );

		// Display Success Message if send form successful, or error + form if failure.
		if ( true === $this->send_email_response['sent'] ) {
			return $contact_form['info'];
		} else {
			return $contact_form['info'] . $contact_form['form'];
		}

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
	 * Build the HTML Form
	 *
	 * @param array $atts The ShortCode attributes.
	 * @return array Send Email Response.
	 */
	private function send_mail( $atts ) {

		$sent = false;

		// Check Nonce.
		if ( isset( $_REQUEST['_wpnonce'] ) && false === wp_verify_nonce( sanitize_key( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'tkt_cntct_frm_nonce' ) ) {
			return $this->send_email_response;
		}

		$error = false;

		// clean out honeypot fields and other, at this point unnecessary fields.
		unset( $_POST['name'] );
		unset( $_POST['subject'] );
		unset( $_POST['email'] );
		unset( $_POST['message'] );
		unset( $_POST['_wpnonce'] );
		unset( $_POST['_wp_http_referer'] );
		unset( $_POST['submit'] );

		// if there are any other fields in $_POST something is sketchy, thus abort.
		// We expect an empty array (no difference).
		$diff = array_diff_key( $_POST, $this->required_fields );

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

			$this->form_data[ $field ] = $value;

		}

		// if the required fields are empty, switch $error to TRUE and set the result text to the shortcode attribute named 'error_empty'.
		foreach ( $this->required_fields as $required_field => $value ) {

			$value = trim( $this->form_data[ $required_field ] );

			if ( empty( $value ) ) {
				$this->error[ $required_field ] = 'missing-or-invalid';
				$error = true;
				$result = $atts['error_empty'];
			}
		}

		// if the e-mail is not valid or missing, switch $error to TRUE and set the result text to the shortcode attribute named 'error_noemail'.
		if ( ! is_email( $this->form_data['liame_dleif'] ) ) {
			$this->error['liame_dleif'] = 'missing-or-invalid';
			$error = true;
			$result = $atts['error_noemail'];
		}

		if ( false === $error ) {

			// Note: Form Data already sanitized above.
			$email_subject = $this->form_data['tcejbus_dleif'];
			$email_message = $this->form_data['egassem_dleif'] . "\n\nIP: " . sanitize_text_field( $this->get_the_ip() );
			$headers  = 'From: ' . $this->form_data['eman_dleif'] . ' <' . $this->form_data['liame_dleif'] . ">\n";
			$headers .= "Content-Type: text/plain; charset=UTF-8\n";
			$headers .= "Content-Transfer-Encoding: 8bit\n";
			wp_mail( $atts['email'], $email_subject, $email_message, $headers );
			$result = $atts['success'];
			$sent = true;

		}

		$this->send_email_response = array(
			'sent'      => $sent,
			'result'    => $result,
		);

		return $this->send_email_response;

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
			$info = '<div class="info">' . $send_email_response['result'] . '</div>';
		}

		/**
		 * Build Form HTML.
		 * NOTE: Real Field Name/ID are mirrored. Non mirrored field names and IDs signify Bot Fields (honeypot)
		 */
		$form = '<form class="contact-form" method="post" action="' . esc_url_raw( get_permalink() ) . '" id="contact_form">
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

			<label class="ohnohoney" for="name"></label>
		    <input class="ohnohoney" autocomplete="off" type="text" name="name" id="name" />
		    <label class="ohnohoney" for="email"></label>
		    <input class="ohnohoney" autocomplete="off" type="email" name="email" id="email" />
		    <label class="ohnohoney" for="subject"></label>
		    <input class="ohnohoney" autocomplete="off" type="text" name="subject" id="subject" />
		    <label class="ohnohoney" for="message"></label>
		    <textarea class="ohnohoney" autocomplete="off" name="message" id="message"></textarea>

		    <div>
		        <input type="submit" value="' . esc_attr( $atts['label_submit'] ) . '" name="submit" id="submit" />
		    </div>
		    ' . wp_nonce_field( 'tkt_cntct_frm_nonce', '_wpnonce' ) . '
		</form>';

		$contact_form = array(
			'info'  => $info,
			'form'  => $form,
		);

		return $contact_form;

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
