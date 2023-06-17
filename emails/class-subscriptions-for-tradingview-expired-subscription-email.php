<?php
/**
 * Expired Email template
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Subscriptions_For_TradingView_Expired_Subscription_Email' ) ) {

	/**
	 * Expired Email template class
	 *
	 * @link       https://donaldit.net/
	 * @since      1.0.0
	 *
	 * @package    Subscriptions_For_TradingView
	 * @subpackage Subscriptions_For_TradingView/email
	 */
	class Subscriptions_For_TradingView_Expired_Subscription_Email extends WC_Email {
		/**
		 * Create class for email notification.
		 *
		 * @access public
		 */
		public function __construct() {

			$this->id          = 'wps_tdv_expired_subscription';
			$this->title       = __( 'Expired Subscription Email Notification', 'subscriptions-for-tradingview' );
			$this->description = __( 'This Email Notification Send if any subscription is Expired', 'subscriptions-for-tradingview' );
			$this->template_html  = 'wps-tdv-expired-subscription-email-template.php';
			$this->template_plain = 'plain/wps-tdv-expired-subscription-email-template.php';
			$this->template_base  = SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'emails/templates/';

			parent::__construct();

			$this->recipient = $this->get_option( 'recipient' );

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
			}
		}

		/**
		 * Get email subject.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Expired Susbcription Email {site_title}', 'subscriptions-for-tradingview' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  1.0.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'Subscription Expired', 'subscriptions-for-tradingview' );
		}

		/**
		 * This function is used to trigger for email.
		 *
		 * @since  1.0.0
		 * @param int $wps_subscription wps_subscription.
		 * @access public
		 * @return void
		 */
		public function trigger( $wps_subscription ) {
			$this->object = $wps_subscription;

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Get_content_html function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html,
				array(
					'wps_subscription'       => $this->object,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => false,
					'email' => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Get_content_plain function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain,
				array(
					'wps_subscription'       => $this->object,
					'email_heading'      => $this->get_heading(),
					'sent_to_admin'      => true,
					'plain_text'         => true,
					'email' => $this,
				),
				'',
				$this->template_base
			);
		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @access public
		 * @return void
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'subscriptions-for-tradingview' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'subscriptions-for-tradingview' ),
					'default' => 'no',
				),
				'recipient'  => array(
					'title'       => __( 'Recipient Email Address', 'subscriptions-for-tradingview' ),
					'type'        => 'text',
					// translators: placeholder is admin email.
					'description' => sprintf( __( 'Enter recipient email address. Defaults to %s.', 'subscriptions-for-tradingview' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'subscriptions-for-tradingview' ),
					'type'        => 'text',
					'description' => __( 'Enter the email subject', 'subscriptions-for-tradingview' ),
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
					'desc_tip'    => true,
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'subscriptions-for-tradingview' ),
					'type'        => 'text',
					'description' => __( 'Email Heading', 'subscriptions-for-tradingview' ),
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
					'desc_tip'    => true,
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'subscriptions-for-tradingview' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'subscriptions-for-tradingview' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}

	}

}

return new Subscriptions_For_TradingView_Expired_Subscription_Email();
