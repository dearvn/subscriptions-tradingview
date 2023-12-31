<?php
/**
 * The add new payment.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/public
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * This function is used to cancel url.
 *
 * @name wps_tdv_cancel_url.
 * @param int    $wps_subscription_id wps_subscription_id.
 * @param String $wps_status wps_status.
 * @since 1.0.0
 */
function wps_tdv_cancel_url( $wps_subscription_id, $wps_status ) {

	$wps_link = add_query_arg(
		array(
			'wps_subscription_id'        => $wps_subscription_id,
			'wps_subscription_status' => $wps_status,
		)
	);
	$wps_link = wp_nonce_url( $wps_link, $wps_subscription_id . $wps_status );

	return $wps_link;
}

?>
<div class="wps_tdv_details_wrap">
	<table class="shop_table wps_tdv_details">
		<h3><?php esc_html_e( 'Subscription Details', 'subscriptions-for-tradingview' ); ?></h3>
		<tbody>
            
			<tr>
				<td><?php esc_html_e( 'TradingView UserName', 'subscriptions-for-tradingview' ); ?></td>
				<td>
				<?php
					$tradingview_id = get_post_meta( $wps_subscription_id, 'tradingview_id', true );
					echo esc_html( $tradingview_id );
				?>
				</td>
			</tr>
            
			<tr>
				<td><?php esc_html_e( 'Status', 'subscriptions-for-tradingview' ); ?></td>
				<td>
				<?php
					$wps_status = get_post_meta( $wps_subscription_id, 'wps_subscription_status', true );
					echo esc_html( $wps_status );
				?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Subscription Date', 'subscriptions-for-tradingview' ); ?></td>
				<td>
				<?php
					$wps_schedule_start = get_post_meta( $wps_subscription_id, 'wps_schedule_start', true );
					echo esc_html( wps_tdv_get_the_wordpress_date_format( $wps_schedule_start ) );
				?>
				</td>
			</tr>
			<tr>
				<td><?php esc_html_e( 'Next Payment Date', 'subscriptions-for-tradingview' ); ?></td>
				<td>
				<?php
					$wps_next_payment_date = get_post_meta( $wps_subscription_id, 'wps_next_payment_date', true );
				if ( 'cancelled' === $wps_status ) {
					$wps_next_payment_date = '';
					$wps_susbcription_end = '';
					$wps_recurring_total = '---';
				}
					echo esc_html( wps_tdv_get_the_wordpress_date_format( $wps_next_payment_date ) );
				?>
				</td>
			</tr>
			<?php
			$wps_trail_date = get_post_meta( $wps_subscription_id, 'wps_susbcription_trial_end', true );

			if ( ! empty( $wps_trail_date ) ) {
				?>
				<tr>
					<td><?php esc_html_e( 'Trial End Date', 'subscriptions-for-tradingview' ); ?></td>
					<td>
					<?php
						echo esc_html( wps_tdv_get_the_wordpress_date_format( $wps_trail_date ) );
					?>
					</td>
				</tr>
				<?php
			}
			?>
			
			<?php
				$wps_next_payment_date = get_post_meta( $wps_subscription_id, '_payment_method', true );
			if ( empty( $wps_next_payment_date ) ) {
					$subscription = wc_get_order( $wps_subscription_id );
					$wps_tdv_add_payment_url = wp_nonce_url( add_query_arg( array( 'wps_add_payment_method' => $wps_subscription_id ), $subscription->get_checkout_payment_url() ) );
				?>
							<tr>
								<td>
									<a href="<?php echo esc_url( $wps_tdv_add_payment_url ); ?>" class="button wps_tdv_add_payment_url"><?php esc_html_e( 'Add Payment Method', 'subscriptions-for-tradingview' ); ?></a>
								</td>
							</tr>
						<?php

			}

			?>
			<?php do_action( 'wps_tdv_subscription_details_html', $wps_subscription_id ); ?>
		</tbody>
	</table>
	<table class="shop_table wps_tdv_order_details">
		<h3><?php esc_html_e( 'Subscription Order Details', 'subscriptions-for-tradingview' ); ?></h3>
		<thead>
			<tr>
				<th>
					<?php esc_html_e( 'Product Name', 'subscriptions-for-tradingview' ); ?>
				</th>
				<th>
					<?php esc_html_e( 'Total', 'subscriptions-for-tradingview' ); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<?php
						$wps_product_name = get_post_meta( $wps_subscription_id, 'product_name', true );
						$product_qty = get_post_meta( $wps_subscription_id, 'product_qty', true );

						echo esc_html( $wps_product_name ) . ' x ' . esc_html( $product_qty );
						do_action( 'wps_tdv_product_details_html', $wps_subscription_id );
					?>
					
				 </td>
				<td>
				<?php
					do_action( 'wps_tdv_display_susbcription_recerring_total_account_page', $wps_subscription_id );
				?>
				</td>
			</tr>
			<?php do_action( 'wps_tdv_order_details_html_before_cancel', $wps_subscription_id ); ?>
			<tr>
				<?php
					$wps_tdv_cancel_subscription = get_option( 'wps_tdv_cancel_subscription_for_customer', '' );
				if ( 'on' == $wps_tdv_cancel_subscription ) {

					$wps_status = get_post_meta( $wps_subscription_id, 'wps_subscription_status', true );
					if ( 'active' == $wps_status ) {
						$wps_cancel_url = wps_tdv_cancel_url( $wps_subscription_id, $wps_status );
						?>
							<td>
								<a href="<?php echo esc_url( $wps_cancel_url ); ?>" class="button wps_tdv_cancel_subscription"><?php esc_html_e( 'Cancel', 'subscriptions-for-tradingview' ); ?></a>
							</td>
						<?php
					}
				}
				?>
					<?php do_action( 'wps_tdv_order_details_html_after_cancel_button', $wps_subscription_id ); ?>
				</tr>
					<?php do_action( 'wps_tdv_order_details_html_after_cancel', $wps_subscription_id ); ?>
		</tbody>
	</table>
	<?php do_action( 'wps_tdv_after_subscription_details', $wps_subscription_id ); ?>
</div>
