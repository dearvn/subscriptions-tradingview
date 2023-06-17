<?php
/**
 * My Account Subscriptions Section of TradingView Subscription
 *
 * @package TradingView Subscription
 * @since   1.0.0
 * @version 2.0.0
 * @author  TDV
 *
 * @var array $subscriptions Subscription List.
 * @var $max_pages
 * @var $current_page
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

do_action( 'tws_my_subscriptions_view_before' );
$subscription_status_list = tws_get_status();
?>
<?php if ( empty( $subscriptions ) ) : ?>
	<p class="tws-my-subscriptions"><?php esc_html_e( 'There is no active subscription for your account.', 'tdv-woocommerce-subscription' ); ?></p>
<?php else : ?>
	<table class="shop_table tws_subscription_table my_account_orders shop_table_responsive">
		<thead>
		<tr>
			<th class="tws-subscription"><?php esc_html_e( 'Subscription', 'tdv-woocommerce-subscription' ); ?></th>
			<th class="tws-subscription-start-date"><?php esc_html_e( 'Started on', 'tdv-woocommerce-subscription' ); ?></th>
			<th class="tws-subscription-recurring"><?php esc_html_e( 'Recurring', 'tdv-woocommerce-subscription' ); ?></th>
			<th class="tws-subscription-payment-date"><?php esc_html_e( 'Next Billing', 'tdv-woocommerce-subscription' ); ?></th>
			<th class="tws-subscription-end-on"><?php esc_html_e( 'Ends on', 'tdv-woocommerce-subscription' ); ?></th>
			<th class="tws-subscription-status"><?php esc_html_e( 'Status', 'tdv-woocommerce-subscription' ); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php
		foreach ( $subscriptions as $subscription_post ) :

			$subscription_id       = is_numeric( $subscription_post ) ? $subscription_post : $subscription_post->ID;
			$subscription          = tws_get_subscription( $subscription_id );
			$subscription_name     = sprintf( '%s - %s', $subscription->get_number(), $subscription->get( 'product_name' ) );
			$subscription_status   = $subscription_status_list[ $subscription->get_status() ];
			$next_payment_due_date = ( ! in_array( $subscription_status, array( 'paused', 'cancelled' ), true ) && $subscription->get( 'payment_due_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'payment_due_date' ) ) : '<span class="empty-date">-</span>';
			$start_date            = ( $subscription->get( 'start_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'start_date' ) ) : '<div class="empty-date">-</div>';
			$end_date              = ( $subscription->get( 'end_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'end_date' ) ) : false;
			$end_date              = ! $end_date && ( $subscription->get( 'expired_date' ) ) ? date_i18n( wc_date_format(), $subscription->get( 'expired_date' ) ) : '<div class="empty-date">-</div>';

			?>

			<tr class="tws-item">
				<td class="tws-subscription"
					data-title="<?php esc_html_e( 'Subscription', 'tdv-woocommerce-subscription' ); ?>">
					<?php if ( $subscription->get_user_id() === get_current_user_id() ) : ?>
					<a href="<?php echo esc_url( tws_get_view_subscription_url( $subscription->get_id() ) ); ?>">
						<?php echo esc_html( $subscription_name ); ?>
					</a>
					<?php else : ?>
						<?php echo esc_html( $subscription_name ); ?>
					<?php endif; ?>
				</td>

				<td class="tws-subscription-start-date"
					data-title="<?php esc_html_e( 'Started on', 'tdv-woocommerce-subscription' ); ?>">
					<?php echo wp_kses_post( $start_date ); ?>
				</td>

				<td class="tws-subscription-recurring"
					data-title="<?php esc_html_e( 'Recurring', 'tdv-woocommerce-subscription' ); ?>">
					<?php echo wp_kses_post( TWS_Subscription_Helper()->get_formatted_recurring( $subscription ) ); ?>
				</td>

				<td class="tws-subscription-payment-date"
					data-title="<?php esc_html_e( 'Next Billing', 'tdv-woocommerce-subscription' ); ?>">
					<?php echo wp_kses_post( $next_payment_due_date ); ?>
				</td>

				<td class="tws-subscription-payment-date"
					data-title="<?php esc_html_e( 'Ended on', 'tdv-woocommerce-subscription' ); ?>">
					<?php echo wp_kses_post( $end_date ); ?>
				</td>

				<td class="tws-subscription-status"
					data-title="<?php esc_html_e( 'Status', 'tdv-woocommerce-subscription' ); ?>">
					<?php printf( '<span class="status %1$s">%1$s</span>', esc_attr( $subscription_status ) ); ?>
				</td>

			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php
endif;

if ( 1 < $max_pages ) :
	?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'my-subscription', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'tdv-woocommerce-subscription' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $max_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'my-subscription', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'tdv-woocommerce-subscription' ); ?></a>
			<?php endif; ?>
		</div>
<?php endif;

do_action( 'tws_my_subscriptions_view_after' );
?>
