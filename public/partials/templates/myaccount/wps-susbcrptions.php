<?php
/**
 * The add show susbcription page.
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
?>
	<div class="wps_tdv_account_wrap">
		<?php
		if ( ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
			?>
				<table>
					<thead>
						<tr>
							<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-number"><span class="nobr"><?php esc_html_e( 'ID', 'subscriptions-for-tradingview' ); ?></span></th>
							<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-status"><span class="nobr"><?php esc_html_e( 'Status', 'subscriptions-for-tradingview' ); ?></span></th>
							<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-date"><span class="nobr"><?php echo esc_html_e( 'Next payment date', 'subscriptions-for-tradingview' ); ?></span></th>
							<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-total"><span class="nobr"><?php echo esc_html_e( 'Recurring Total', 'subscriptions-for-tradingview' ); ?></span></th>
							<th class="woocommerce-orders-table__header woocommerce-orders-table__header-order-actions"><?php esc_html_e( 'Action', 'subscriptions-for-tradingview' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $wps_subscriptions as $key => $wps_subscription ) {
						$parent_order_id   = get_post_meta( $wps_subscription->ID, 'wps_parent_order', true );
						$wps_wtdv_is_order = false;
						if ( function_exists( 'wps_tdv_check_valid_order' ) && ! wps_tdv_check_valid_order( $parent_order_id ) ) {
							$wps_wtdv_is_order = apply_filters( 'wps_wtdv_check_parent_order', $wps_wtdv_is_order, $parent_order_id );
							if ( false == $wps_wtdv_is_order ) {
								continue;
							}
						}
						?>
								<tr class="wps_tdv_account_row woocommerce-orders-table__row woocommerce-orders-table__row--status-processing order">
									<td class="wps_tdv_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-number">
								<?php echo esc_html( $wps_subscription->ID ); ?>
									</td>
									<td class="wps_tdv_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status">
								<?php
									$wps_status = get_post_meta( $wps_subscription->ID, 'wps_subscription_status', true );
									echo esc_html( $wps_status );
								?>
									</td>
									<td class="wps_tdv_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-date">
								<?php
									$wps_next_payment_date = get_post_meta( $wps_subscription->ID, 'wps_next_payment_date', true );
								if ( 'cancelled' === $wps_status ) {
									$wps_next_payment_date = '';
								}
									echo esc_html( wps_tdv_get_the_wordpress_date_format( $wps_next_payment_date ) );
								?>
									</td>
									<td class="wps_tdv_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-total">
									<?php
									do_action( 'wps_tdv_display_susbcription_recerring_total_account_page', $wps_subscription->ID );
									?>
									</td>
									<td class="wps_tdv_account_col woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions">
										<span class="wps_tdv_account_show_subscription">
											<a href="
									<?php
									echo esc_url( wc_get_endpoint_url( 'show-subscription', $wps_subscription->ID, wc_get_page_permalink( 'myaccount' ) ) );
									?>
											">
									<?php
									esc_html_e( 'Show', 'subscriptions-for-tradingview' );
									?>
											</a>
										</span>
									</td>
								</tr>
								<?php
					}
					?>
					</tbody>
				</table>
				<?php
				if ( 1 < $wps_num_pages ) {
					?>
			<div class="wps_tdv_pagination woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
					<?php if ( 1 !== $wps_current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo esc_url( wc_get_endpoint_url( 'wps_subscriptions', $wps_current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'subscriptions-for-tradingview' ); ?></a>
			<?php } ?>

					<?php if ( intval( $wps_num_pages ) !== $wps_current_page ) { ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button" href="<?php echo esc_url( wc_get_endpoint_url( 'wps_subscriptions', $wps_current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'subscriptions-for-tradingview' ); ?></a>
			<?php } ?>
			</div>
		<?php } ?>
			<?php
		} else {
			esc_html_e( 'You do not have any active subscription(s).', 'subscriptions-for-tradingview' );
		}
		?>
	</div>
