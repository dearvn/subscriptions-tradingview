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
	exit; // Exit if accessed directly.
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<?php /* translators: %s: susbcription ID */ ?>
<p><?php printf( esc_html__( 'A subscription [#%s] has been Expired. Their subscription\'s details are as follows:', 'subscriptions-for-tradingview' ), esc_html( $wps_subscription ) ); ?></p>

<?php
wps_tdv_email_subscriptions_details( $wps_subscription );

do_action( 'woocommerce_email_footer', $email );
