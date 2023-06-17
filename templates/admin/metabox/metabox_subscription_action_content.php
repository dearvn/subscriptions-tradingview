<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Metabox for Subscription Action Content
 *
 * @package TradingView Subscription
 * @since   1.0.0
 * @author  TDV
 *
 * @var TWS_Subscription $subscription Current subscription.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'TDV_TWS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

$dev = isset( $_GET['tws_dev'] ); //phpcs:ignore
?>
<div class="subscription_actions">
	<select name="tws_subscription_actions" class="wc-enhanced-select">
		<option value=""><?php esc_html_e( 'Actions', 'tdv-woocommerce-subscription' ); ?></option>
		<?php if ( $subscription->can_be_active() ) : ?>
			<option
				value="active"><?php esc_html_e( 'Activate Subscription', 'tdv-woocommerce-subscription' ); ?></option>
		<?php endif ?>
		<?php if ( $subscription->can_be_cancelled() ) : ?>
			<option
				value="cancelled"><?php esc_html_e( 'Cancel Subscription', 'tdv-woocommerce-subscription' ); ?></option>
		<?php endif ?>
	</select>
</div>
<div class="subscription_actions_footer">
	<button type="submit" class="button button-primary"
		title="<?php esc_html_e( 'Process', 'tdv-woocommerce-subscription' ); ?>" name="tws_subscription_button"
		value="actions"><?php esc_html_e( 'Process', 'tdv-woocommerce-subscription' ); ?></button>
</div>
