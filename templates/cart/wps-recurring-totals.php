<?php
/**
 * Recurring totals template of Subscription For TradingView
 *
 * @package Subscription For TradingView
 * @since   1.0.0
 * @version 2.0.0
 * @author  TDV
 *
 * @var array $recurring_totals Recurring total List.
 */

use function WPML\FP\apply;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<tr class="tws-recurring-totals-items">
	<th><?php esc_html_e( 'Recurring totals', 'tdv-woocommerce-subscription' ); ?></th>
	<td>
		<?php
		foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) :

            $product = $cart_item['data'];
        
            if ( $product->is_on_sale() ) {
                $price = $product->get_sale_price();
            } else {
                $price = $product->get_regular_price();
            }
            
            $product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $price ) ) );
            $product_price = wps_tdv_subscription_product_get_price_html( $product_price, $product );


            $price_html = '<div class="recurring-price-info">';
            $price_html .= $product_price;//TWS_Subscription_Cart()->get_formatted_subscription_total_amount( $cart_item['data'], $cart_item['quantity'], $cart_item['tws-subscription-info'] );
            $price_html .= '</div>';


            ?>
            <div class="recurring-amount"><?php echo wp_kses_post( $price_html ); ?></div>
            <?php
			//endif;
		endforeach;
		?>
	</td>
</tr>

