<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace subscriptions_for_tradingview_public.
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/public
 * @author     Donald<donald.nguyen.it@gmail.com>
 */
class Subscriptions_For_TradingView_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wps_tdv_public_enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'public/css/subscriptions-for-tradingview-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function wps_tdv_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'public/js/subscriptions-for-tradingview-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'tdv_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

	}

	/**
	 * This function is used to show subscription price on single product page.
	 *
	 * @name wps_tdv_price_html_subscription_product
	 * @param string $price product price.
	 * @param object $product Product.
	 * @since    1.0.0
	 */
	public function wps_tdv_price_html_subscription_product( $price, $product ) {

		if ( ! wps_tdv_check_product_is_subscription( $product ) ) {
			return $price;
		}
		$price = apply_filters( 'wps_rbpfw_price', $price, $product );
		$price = $this->wps_tdv_subscription_product_get_price_html( $price, $product );
		do_action( 'wps_tdv_show_start_date_frontend', $product );
		return $price;
	}

	/**
	 * This function is used to show subscription price and interval on subscription product page.
	 *
	 * @name wps_tdv_subscription_product_get_price_html
	 * @param object $price price.
	 * @param string $product product.
	 * @param array  $cart_item cart_item.
	 * @since    1.0.0
	 */
	public function wps_tdv_subscription_product_get_price_html( $price, $product, $cart_item = array(), $subscription_id = null ) {

        if ( is_object( $product ) ) {
			$product_id = $product->get_id();
			$wps_tdv_subscription_number = get_post_meta( $product_id, 'wps_tdv_subscription_number', true );
			$wps_tdv_subscription_expiry_number = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_number', true );
			$wps_tdv_subscription_interval = get_post_meta( $product_id, 'wps_tdv_subscription_interval', true );

            $wps_susbcription_trial_end = !empty($subscription_id) ? get_post_meta( $subscription_id, 'wps_susbcription_trial_end', true ) : 0;
                
			if ( isset( $wps_tdv_subscription_expiry_number ) && ! empty( $wps_tdv_subscription_expiry_number ) ) {

				$wps_tdv_subscription_expiry_interval = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_interval', true );

				$wps_price_html = wps_tdv_get_time_interval( $wps_tdv_subscription_expiry_number, $wps_tdv_subscription_expiry_interval );
				// Show interval html.

				$wps_price_html = apply_filters( 'wps_tdv_show_time_interval', $wps_price_html, $product_id, $cart_item );
				$wps_price = wps_tdv_get_time_interval_for_price( $wps_tdv_subscription_number, $wps_tdv_subscription_interval );

				/* translators: %s: susbcription interval */
				$wps_tdv_price_html = '<span class="wps_tdv_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price ) . '</span>';

				$price .= apply_filters( 'wps_tdv_show_sync_interval', $wps_tdv_price_html, $product_id );

				/* translators: %s: susbcription interval */
				//$price .= '<span class="wps_tdv_expiry_interval">' . sprintf( esc_html__( ' For %s ', 'subscriptions-for-tradingview' ), $wps_price_html ) . '</span>';
                
                
                if (!empty($wps_susbcription_trial_end) || !$subscription_id) {
                    $price = $this->wps_tdv_get_free_trial_period_html( $product_id, $price );
                    $price = $this->wps_tdv_get_initial_signup_fee_html( $product_id, $price );
                }
                
				$price = apply_filters( 'wps_tdv_show_one_time_subscription_price', $price, $product_id );

			} elseif ( isset( $wps_tdv_subscription_number ) && ! empty( $wps_tdv_subscription_number ) ) {

                $wps_price_html = wps_tdv_get_time_interval_for_price( $wps_tdv_subscription_number, $wps_tdv_subscription_interval );

                /* translators: %s: susbcription interval */
                $wps_tdv_price_html = '<span class="wps_tdv_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price_html ) . '</span>';

                $price .= apply_filters( 'wps_tdv_show_sync_interval', $wps_tdv_price_html, $product_id );

                if (!empty($wps_susbcription_trial_end) || !$subscription_id) {
                    $price = $this->wps_tdv_get_free_trial_period_html( $product_id, $price );
                    $price = $this->wps_tdv_get_initial_signup_fee_html( $product_id, $price );
                }
                $price = apply_filters( 'wps_tdv_show_one_time_subscription_price', $price, $product_id );

			}
		}
		return apply_filters( 'wps_tdv_price_html', $price, $wps_price_html, $product_id );
	}



    public function wps_tdv_subscription_product_get_info( $product_id ) {

        $wps_tdv_subscription_number = get_post_meta( $product_id, 'wps_tdv_subscription_number', true );
        $wps_tdv_subscription_expiry_number = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_number', true );
        $wps_tdv_subscription_interval = get_post_meta( $product_id, 'wps_tdv_subscription_interval', true );

        //$wps_susbcription_trial_end = !empty($subscription_id) ? get_post_meta( $subscription_id, 'wps_susbcription_trial_end', true ) : 0;
        $product    = wc_get_product( $product_id );
        
        if ( $product->is_on_sale() ) {
            $price = $product->get_sale_price();
        } else {
            $price = $product->get_regular_price();
        }
        if ( isset( $wps_tdv_subscription_expiry_number ) && ! empty( $wps_tdv_subscription_expiry_number ) ) {

            $wps_tdv_subscription_expiry_interval = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_interval', true );

            $wps_price_html = wps_tdv_get_time_interval( $wps_tdv_subscription_expiry_number, $wps_tdv_subscription_expiry_interval );
            // Show interval html.

            $wps_price = wps_tdv_get_time_interval_for_price( $wps_tdv_subscription_number, $wps_tdv_subscription_interval );

            /* translators: %s: susbcription interval */
            $wps_tdv_price_html = '<span class="wps_tdv_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price ) . '</span>';

            

            $price = $this->wps_tdv_get_free_trial_period_html( $product_id );
            $price = $this->wps_tdv_get_initial_signup_fee_html( $product_id, $price );

            return apply_filters( 'wps_tdv_show_one_time_subscription_price', $price, $product_id );

        } elseif ( isset( $wps_tdv_subscription_number ) && ! empty( $wps_tdv_subscription_number ) ) {

            $wps_price_html = wps_tdv_get_time_interval_for_price( $wps_tdv_subscription_number, $wps_tdv_subscription_interval );

            /* translators: %s: susbcription interval */
            $wps_tdv_price_html = '<span class="wps_tdv_interval">' . sprintf( esc_html( ' / %s ' ), $wps_price_html ) . '</span>';

            $price .= apply_filters( 'wps_tdv_show_sync_interval', $wps_tdv_price_html, $product_id );

            if (!empty($wps_susbcription_trial_end) || !$subscription_id) {
                $price = $this->wps_tdv_get_free_trial_period_html( $product_id );
                $price = $this->wps_tdv_get_initial_signup_fee_html( $product_id, $price );
            }
            return apply_filters( 'wps_tdv_show_one_time_subscription_price', $price, $product_id );

        }
	}

    
	/**
	 * This function is used to show initial signup fee on subscription product page.
	 *
	 * @name wps_tdv_get_initial_signup_fee_html
	 * @param int    $product_id Product ID.
	 * @param string $price Product Price.
	 * @since    1.0.0
	 */
	public function wps_tdv_get_initial_signup_fee_html( $product_id, $price ) {
		$wps_tdv_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_tdv_subscription_initial_signup_price', true );
		if ( isset( $wps_tdv_subscription_initial_signup_price ) && ! empty( $wps_tdv_subscription_initial_signup_price ) ) {
			if ( function_exists( 'wps_mmctdv_admin_fetch_currency_rates_from_base_currency' ) && ! is_admin() ) {

				if ( WC()->session->__isset( 's_selected_currency' ) ) {
					$to_currency = WC()->session->get( 's_selected_currency' );
				} else {
					$to_currency = get_woocommerce_currency();
				}

				$wps_tdv_subscription_initial_signup_price = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( $to_currency, $wps_tdv_subscription_initial_signup_price );
			}
			/* translators: %s: signup fee */

			$price .= '<span class="wps_tdv_signup_fee">' . sprintf( esc_html__( ' and %s Verification fee', 'subscriptions-for-tradingview' ), wc_price( $wps_tdv_subscription_initial_signup_price ) ) . '</span>';
		}
		return $price;
	}

	/**
	 * This function is used to show free trial period on subscription product page.
	 *
	 * @name wps_tdv_get_free_trial_period_html
	 * @param int    $product_id Product ID.
	 * @param string $price Product Price.
	 * @since    1.0.0
	 */
	public function wps_tdv_get_free_trial_period_html( $product_id, $price = 0 ) {

		$wps_tdv_subscription_free_trial_number = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_number', true );
		$wps_tdv_subscription_free_trial_interval = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_interval', true );
		if ( isset( $wps_tdv_subscription_free_trial_number ) && ! empty( $wps_tdv_subscription_free_trial_number ) ) {
			$wps_price_html = wps_tdv_get_time_interval( $wps_tdv_subscription_free_trial_number, $wps_tdv_subscription_free_trial_interval );
			/* translators: %s: free trial number */

			$price = ($price ? $price.' and':'').'<span class="wps_tdv_free_trial">' . sprintf( esc_html__( ' %s  free trial', 'subscriptions-for-tradingview' ), $wps_price_html ) . '</span>';
		}
		return $price;
	}

	/**
	 * This function is used to change Add to cart button text.
	 *
	 * @name wps_tdv_product_add_to_cart_text
	 * @param object $text Add to cart text.
	 * @param string $product Product..
	 * @since    1.0.0
	 */
	public function wps_tdv_product_add_to_cart_text( $text, $product ) {

		if ( wps_tdv_check_product_is_subscription( $product ) ) {
			$wps_add_to_cart_text = $this->wps_tdv_get_add_to_cart_button_text();

			if ( isset( $wps_add_to_cart_text ) && ! empty( $wps_add_to_cart_text ) ) {
				$text = $wps_add_to_cart_text;
			}
		}

		return $text;
	}

	/**
	 * This function is used to get add to cart button text.
	 *
	 * @name wps_tdv_get_add_to_cart_button_text
	 * @since    1.0.0
	 */
	public function wps_tdv_get_add_to_cart_button_text() {

		$wps_add_to_cart_text = get_option( 'wps_tdv_add_to_cart_text', '' );
		return $wps_add_to_cart_text;
	}
    
    /**
	 * This function is used to get token.
	 *
	 * @name wps_tdv_get_token
	 * @since    1.0.0
	 */
	public function wps_tdv_get_token() {

		return get_option( 'wps_tdv_token', '' );
	}

	/**
	 * This function is used to change place order button text.
	 *
	 * @name wps_tdv_woocommerce_order_button_text
	 * @param string $text Place order text.
	 * @since    1.0.0
	 */
	public function wps_tdv_woocommerce_order_button_text( $text ) {
		$wps_tdv_place_order_button_text = $this->wps_tdv_get_place_order_button_text();
		if ( isset( $wps_tdv_place_order_button_text ) && ! empty( $wps_tdv_place_order_button_text ) && $this->wps_tdv_check_cart_has_subscription_product() ) {
			$text = $wps_tdv_place_order_button_text;
		}

		return $text;
	}

	/**
	Remove all possible fields
	**/
	public function wps_tdv_remove_order_notes_field( $fields ) {
		return false;
	}

	public function wps_tdv_add_account_tradingview_into_chart($wps_subscription_id, $order_id) {

        wps_tdv_add_account_tradingview($wps_subscription_id);
		
	}
    
    public function wps_tdv_expire_tradingview ($wps_subscription_id) {
        wps_tdv_remove_account_tradingview($wps_subscription_id);
    }
    
    public function wps_tdv_cancel_tradingview ($wps_subscription_id, $user_id) {
        wps_tdv_remove_account_tradingview($wps_subscription_id);
    }
    
    public function wps_tdv_modify_account_tradingview_into_chart($wps_subscription_id, $expired_date = null) {

        wps_tdv_add_account_tradingview($wps_subscription_id, '', $expired_date);
		
	}

	/**
	Remove all possible fields
	**/
	public function wps_tdv_remove_checkout_fields( $fields ) {

		// Billing fields
		unset( $fields['billing']['billing_company'] );
		unset( $fields['billing']['billing_state'] );
		unset( $fields['billing']['billing_country'] );
		unset( $fields['billing']['billing_address_1'] );
		unset( $fields['billing']['billing_address_2'] );
		unset( $fields['billing']['billing_city'] );
		unset( $fields['billing']['billing_postcode'] );

//		$fields['billing']['tradingview_id'] = array(
//			'required' => true,
//			'type' => 'text',
//			'label' => __('TradingView UserName', 'woocommerce'),
//		);
		return $fields;
	}
    
    
    
    public function wps_tdv_add_tradingview_id_field()
    {
        woocommerce_form_field('tradingview_id', array(
            'type' => 'text',
            'label' => __('TradingView UserName') ,
            'class' => array( 'update_totals_on_change' ),
            'placeholder' => __('TradingView UserName') ,
            'required' => true
        ));
    }

    
  
    public function wps_tdv_woocommerce_checkout_fee( $cart ) {
            global $woocommerce;

        if ( is_admin() && ! defined( 'DOING_AJAX' ) || !isset($_POST['post_data']) ) return;
    
        $params = explode("&", urldecode(wp_unslash($_POST['post_data'])));
        $tradingview_id = '';
        foreach($params as $item) {
            list($key, $value) = explode("=", $item);
            
            if ($key == 'tradingview_id') {
                $tradingview_id = $value;
                break;
            }
        }
        if (empty($tradingview_id)) {
            return;
        }
        
        
        
        
//        if ( $tradingview_id ) {
//            //self::wps_tdv_add_subscription_price_and_sigup_fee($cart, true);
//            
//            
//            
//           //$cart_object->subtotal='Option Fee';
//            //WC()->cart->subtotal=$radio;
//            $cart->cart_contents_total = 200;
//            $cart->set_cart_contents_total(200); 
//            $cart->set_subtotal(200);
//
//            //WC()->cart->total=WC()->cart->get_cart_total();
//            //$cart_total = WC()->cart->get_cart_contents_total();
//
//            //calculate_totals( );
//            $a=WC()->cart->get_cart_contents_total();
//            $cart->add_fee( 'Option Fee', 200 );
//        }
        
        
        $posts = wps_tdv_susbcription_tradingview_existed($tradingview_id);
        
        $exist = count($posts) > 0 ? true : false;
//        if (!$exist) {
//            return;
//            //apply_filters( 'woocommerce_checkout_my_checkout_section', __( 'You had spent a trial period with this TradingView account.', 'woocommerce' ));
//        }
        
        //add_filter( 'woocommerce_cart_totals_after_order_total', array($this, 'wps_tdv_cart_recurring_totals'), 10 );
            
        //self::wps_tdv_cart_recurring_totals();
        
        //return;
        if ( isset( $cart ) && ! empty( $cart ) ) {
			foreach ( $cart->cart_contents as $key => $cart_data ) {
                
				if ( wps_tdv_check_product_is_subscription( $cart_data['data'] ) ) {

                    $product = $cart_data['data'];
					
                    $extra_text = 'Your TradingView Username has registered trial before.';
                    if (!$exist) {
                        $extra_text = $this->wps_tdv_subscription_product_get_info($product->get_id());
                    }
                    
                    
                    if ($exist) {
                        if ( $product->is_on_sale() ) {
                            $price = $product->get_sale_price();
                        } else {
                            $price = $product->get_regular_price();
                        }
                        
                        

                        // Cart price.
                        //$product_price = apply_filters( 'wps_tdv_cart_price_subscription', $product_price, $cart_data );
                        $product->set_price( $price );
                        $product->set_name( $product->get_name().'<br/>'.$extra_text );
                        //$product->total = $price; 
                        //$product->subtotal = $price;

                        //WC()->cart->subtotal = $price;
                        //WC()->cart->total = $price;
                        //$cart->cart_contents_total = $price;
                        $cart->set_cart_contents_total($price); 
                        $cart->set_subtotal($price);
                        //$total = WC()->cart->get_cart_total();
                        //WC()->cart->set_total('total', $price);
                        
                        //WC()->cart->set_total($price);
                        //set_cart_total
                        //$cart->add_fee( 'Recurring totals', $price );
                        
                        //$cart->fees = $price;
                        
                        //WC()->cart->total = $price;
                        //WC()->cart->calculate_totals();
                        //wc_price($price);
                        //$woocommerce->cart->add_fee( 'Surcharge', $price, true, 'standard' ); 
                        $cart_total = $cart->cart_contents_total;
                        
                        //$woocommerce->cart->add_fee( 'Recurring totals', $price, true, 'standard' ); 
                    }
				}
			}
		}
   
    }
    
    public function wps_tdv_woocommerce_checkout_change_fee_to_order( $order, $data ){
        
        
            $cartTotal = WC()->cart->get_cart_contents_total();
            echo $cartTotal;die;
            // DOan
            $payment_fee = $cartTotal / $number_of_payments * ($number_of_payments - 1) * 0.02;

            $item_fee = new WC_Order_Item_Fee();
            $item_fee->set_name('אמלת תשלומים');
            $item_fee->set_amount($payment_fee); // Fee amout
            $item_fee->set_tax_class(''); // default for ''
            $item_fee->set_tax_status('none'); // or 'none'
            $item_fee->set_total($payment_fee); // Fee amount

            // Add Fee item to the order
            $order->add_item($item_fee);

            $order->calculate_totals();
            $order->save();



    }
    
    
    public function wps_tdv_woocommerce_checkout_refresh_payment_method(){
        // jQuery
        ?>
        <script type="text/javascript">
            (function($){
                $( 'form.checkout' ).on( 'change', 'input[name^="payment_method"]', function() {
                    $('body').trigger('update_checkout');
                    debugger;
                });
            })(jQuery);
        </script>
        <?php
    }
  
    public function wps_tdv_woocommerce_checkout_tradingview_set_session( $posted_data ) {
        parse_str( $posted_data, $output );
        if ( isset( $output['tradingview_id'] ) ){
            WC()->session->set( 'tradingview_id', $output['tradingview_id'] );
        }
    }


    /*
        Validation nag.
        Remove this if the fields are to be optional.
    */
    public function wps_tdv_woocommerce_after_checkout_validation( $data, $errors ) { 
        if ( empty( $_POST['tradingview_id'] ) ) {
            $errors->add( 'required-field', __( 'TradingView UserName is a required field.', 'woocommerce' ) );
        } else {
            $tradingview_id = sanitize_key( wp_unslash( $_POST['tradingview_id'] ) );
            
            if (!wps_tdv_validate_account_tradingview($tradingview_id)) {
                $errors->add( 'required-field', __( 'TradingView UserName is invalid.', 'woocommerce' ) );
            }
        }
    }
    
    /**
	Remove all possible fields
	**/
	public function wps_tdv_save_tradingview_id( $order_id ) {
        if ( isset( $_POST['tradingview_id'] ) ) {
            $tradingview_id = sanitize_key( wp_unslash( $_POST['tradingview_id'] ) );
            update_post_meta( $order_id, 'tradingview_id', $tradingview_id );
            
            $order = wc_get_order( $order_id );
            $user_id = $order->get_user_id();
            if (get_user_meta( $user_id, 'tradingview_id', true )) {
                update_user_meta( $user_id, 'tradingview_id', $tradingview_id );
            } else {
                add_user_meta( $user_id, 'tradingview_id', $tradingview_id );
            }
        }
    }
    
	/**
	 * This function is used to get order button text.
	 *
	 * @name wps_tdv_get_place_order_button_text
	 * @since    1.0.0
	 */
	public function wps_tdv_get_place_order_button_text() {

		$wps_tdv_place_order_button_text = get_option( 'wps_tdv_place_order_button_text', '' );
		return $wps_tdv_place_order_button_text;
	}

	/**
	 * This function is used to check cart have subscription product.
	 *
	 * @name wps_tdv_check_cart_has_subscription_product
	 * @since    1.0.0
	 */
	public function wps_tdv_check_cart_has_subscription_product() {
		$wps_has_subscription = false;

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				if ( wps_tdv_check_product_is_subscription( $cart_item['data'] ) ) {
					$wps_has_subscription = true;
					break;
				}
			}
		}
		return $wps_has_subscription;
	}

	/**
	 * This function is used to subscription price on in cart.
	 *
	 * @name wps_tdv_show_subscription_price_on_cart
	 * @param string $product_price Product price.
	 * @param object $cart_item cart item.
	 * @param int    $cart_item_key cart_item_key.
	 * @since    1.0.0
	 */
	public function wps_tdv_show_subscription_price_on_cart( $product_price, $cart_item, $cart_item_key ) {

		if ( wps_tdv_check_product_is_subscription( $cart_item['data'] ) ) {

			if ( $cart_item['data']->is_on_sale() ) {
				$price = $cart_item['data']->get_sale_price();
			} else {
				$price = $cart_item['data']->get_regular_price();
			}
			if ( function_exists( 'wps_mmctdv_admin_fetch_currency_rates_from_base_currency' ) ) {
				$price = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( '', $price );
			}
			$product_price = wc_price( wc_get_price_to_display( $cart_item['data'], array( 'price' => $price ) ) );
			// Use for role base pricing.
			$product_price = apply_filters( 'wps_rbpfw_cart_price', $product_price, $cart_item );
			$product_price = $this->wps_tdv_subscription_product_get_price_html( $product_price, $cart_item['data'], $cart_item );
		}
		return $product_price;
	}

    public function wps_tdv_woocommerce_customer_details($order ) {
        $tradingview_id = get_post_meta( $order->get_id(), 'tradingview_id', true );
        
        printf( '<address><p><b>TradingView UserName: </b>' . __("%s", "subscriptions-for-tradingview") . '</p></address>', $tradingview_id );
    }
    
    public function wps_tdv_woocommerce_order_item_meta_start($item_id, $item, $order, $plain_text ) {
        $product = wc_get_product($item->get_product_id());
        if ( $product->is_on_sale() ) {
            $price = $product->get_sale_price();
        } else {
            $price = $product->get_regular_price();
        }
        if ( function_exists( 'wps_mmctdv_admin_fetch_currency_rates_from_base_currency' ) ) {
            $price = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( '', $price );
        }
        
        $wps_subscription_id = get_post_meta( $order->get_id(), 'wps_subscription_id', true );
		
        if (empty($wps_subscription_id)) {
            $wps_subscription_id = get_post_meta( $order->get_id(), 'wps_tdv_subscription', true );
        }
        
        $product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $price ) ) );
        $product_price = $this->wps_tdv_subscription_product_get_price_html( $product_price, $product, array(), $wps_subscription_id );
        
        		
        $wps_next_payment_date = get_post_meta( $wps_subscription_id, 'wps_next_payment_date', true );
        $wps_susbcription_end = get_post_meta( $wps_subscription_id, 'wps_susbcription_end', true );
				
        printf( '<p>' . __("%s", "subscriptions-for-tradingview") . '</p>', $product_price );
        if (!empty($wps_next_payment_date)) {
            printf( '<p>'. __( 'Next Payment Date', 'subscriptions-for-tradingview' ).": " . __("%s", "subscriptions-for-tradingview") . '</p>', wps_tdv_get_the_wordpress_date_format( $wps_next_payment_date ) );
        }
        if (!empty($wps_susbcription_end)) {
            printf( '<p>'. __( 'Subscription Expiry Date', 'subscriptions-for-tradingview' ).": " . __("%s", "subscriptions-for-tradingview") . '</p>', wps_tdv_get_the_wordpress_date_format( $wps_susbcription_end ) );
        }
    }

    public function wps_tdv_order_formatted_line_subtotal( $subtotal, $item, $order ) {

        $product_id = $item['product_id'];
        $product    = wc_get_product( $product_id );

//        if ( ! $this->is_subscription( $product ) ) {
//            return $subtotal;
//        }

//        $price_is_per             = td_get_prop( $product, '_tws_price_is_per' );
//        $price_time_option        = td_get_prop( $product, '_tws_price_time_option' );
//        $price_time_option_string = tws_get_price_per_string( $price_is_per, $price_time_option );
//        $subtotal                .= ' / ' . $price_time_option_string;
        
        if ( $product->is_on_sale() ) {
            $price = $product->get_sale_price();
        } else {
            $price = $product->get_regular_price();
        }
        
//        $product_price = wc_price( wc_get_price_to_display( $product, array( 'price' => $price ) ) );
//        $product_price = $this->wps_tdv_subscription_product_get_price_html( $product_price, $product, array(), $wps_subscription_id );
//        
//        		
//        $wps_next_payment_date = get_post_meta( $wps_subscription_id, 'wps_next_payment_date', true );
//        $wps_susbcription_end = get_post_meta( $wps_subscription_id, 'wps_susbcription_end', true );
//		
        $subtotal .= $price."=======";
    }
    
    
    /**
    * Add recurring totals inside the cart.
    */
    public function wps_tdv_cart_recurring_totals() {
       if ( ! isset( WC()->cart ) ) {
           return false;
       }

       wc_get_template( 'cart/wps-recurring-totals.php', array(), '', SUBSCRIPTIONS_FOR_TRADINGVIEW_TEMPLATE_PATH . '/' );

    }
    
    
    /**
    * Change price in cart.
    *
    * @param string $price_html HTML price.
    * @param array  $cart_item Cart Item.
    * @param string $cart_item_key Cart Item Key.
    *
    * @return mixed|void
    */
    public function wps_tdv_cart_change_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

       $product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

       if ( isset( $cart_item['data']) && wps_tdv_check_product_is_subscription( $cart_item['data'] ) ) {
           $product = $cart_item['data'];

           //$price         = apply_filters( 'tws_change_price_in_cart_html', $cart_item['data']->get_price(), $cart_item['data'] );
           //$price_current = apply_filters( 'tws_change_price_current_in_cart_html', $product->get_price(), $product );
           //$product->set_price( $price );

           $price_html = $this->change_general_price_html( $product, 1, true, $cart_item );

           //$price_html = apply_filters( 'tws_get_price_html', $price_html, $cart_item, $product_id );
           //$product->set_price( $price_current );
       }

       return $price_html;

    }
    
    /**
    * Change subtotal html price on cart.
    *
    * @param string $price_html Html Price.
    * @param array  $cart_item Cart item.
    * @param string $cart_item_key Cart Item key.
    *
    * @return string
    */
    public function change_subtotal_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {

       $product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];

       if ( ! tws_is_subscription_product( $product_id ) || ! isset( $cart_item['data'] ) ) {
           return $price_html;
       }

       $product       = $cart_item['data'];
       $price         = apply_filters( 'tws_change_subtotal_price_in_cart_html', $cart_item['data']->get_price(), $cart_item['data'], $cart_item );
       $price_current = apply_filters( 'tws_change_subtotal_price_current_in_cart_html', $product->get_price(), $product );

       $product->set_price( $price );
       $price_html = $this->change_general_price_html( $product, $cart_item['quantity'], false, $cart_item );

       $product->set_price( $price_current );

       return apply_filters( 'tws_subscription_subtotal_html', $price_html, $cart_item['data'], $cart_item );
    }


    /**
    * Return the subscription total amount of a product.
    *
    * @param WC_Product $product Product.
    * @param int        $quantity Quantity.
    * @param bool|array $subscription_info Subscription information.
    *
    * @return string
    */
    public function get_formatted_subscription_total_amount( $product, $quantity, $subscription_info = false ) {

       $sbs_total_format = '';
       $max_length       = TWS_Subscription_Helper::get_subscription_product_max_length( $product );

       if ( $max_length && $max_length > 1 ) {

           $sbs_total_format         = get_option( 'tws_total_subscription_length_text', esc_html_x( 'Subscription total for {{sub-time}}: {{sub-total}}', 'do not translate the text inside the brackets', 'tdv-woocommerce-subscription' ) );
           $max_length_text          = TWS_Subscription_Helper::get_subscription_max_length_formatted_for_price( $product );
           $total_subscription_price = TWS_Subscription_Helper::get_total_subscription_price( $product, $subscription_info );
           $total_subscription_price = wc_get_price_to_display(
               $product,
               array(
                   'qty'   => $quantity,
                   'price' => $total_subscription_price,
               )
           );
           $sbs_total_format         = str_replace( '{{sub-time}}', $max_length_text, $sbs_total_format );
           $sbs_total_format         = str_replace( '{{sub-total}}', wc_price( $total_subscription_price ), $sbs_total_format );

           if ( ! wc_prices_include_tax() ) {
               $sbs_total_format .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
           }

           $sbs_total_format = '<div class="tws-subscription-total">' . $sbs_total_format . '</div>';

       }

       return apply_filters( 'tws_checkout_subscription_total_amount', $sbs_total_format, $product, $quantity );
    }


    /**
    * Change price HTML to the product
    *
    * @param WC_Product $product WC_Product.
    * @param int        $quantity Quantity.
    * @param bool       $show_complete_price To show the complete price inside cart subtotal.
    * @param array      $cart_item Cart item.
    *
    * @return string
    * @since  1.2.0
    */
    public function change_general_price_html( $product, $quantity = 1, $show_complete_price = false, $cart_item = null ) {

       if ( is_null( $cart_item ) ) {
           return $product->get_price_html();
       }

//       $show_complete_price_on_substotal_cart = apply_filters( 'tws_show_complete_price_on_substotal_cart', $show_complete_price );
//
//       if ( isset( $cart_item['tws-subscription-info'] ) ) {
//           $subscription_info = $cart_item['tws-subscription-info'];
//       } else {
//           $subscription_info = $this->get_subscription_meta_on_cart( $cart_item['data'] );
//       }

       $price = wc_get_price_to_display(
           $product,
           array(
               'qty'   => $quantity,
               'price' => $product->get_price(),
           )
       );

       $price_html  = '<div class="tws-wrapper"><div class="tws-price">';
       $price_html .= wc_price( $price );
       if ( ! isset( $subscription_info['sync'] ) || ! $subscription_info['sync'] ) {
           $price_html .= '<span class="price_time_opt"> / ' . TWS_Subscription_Helper::get_subscription_period_for_price( $product, $subscription_info ) . '</span>';
       } elseif ( isset( $subscription_info['sync'], $subscription_info['next_payment_due_date'] ) && $subscription_info['sync'] && 0 == $price ) { //phpcs:ignore
           if ( current_action() === 'woocommerce_cart_item_subtotal' ) {
               return $price_html;
           }
           $recurring_period        = TWS_Subscription_Helper::get_subscription_period_for_price( $cart_item['data'], $cart_item['tws-subscription-info'] );
           $recurring_price         = TWS_Subscription_Helper::get_subscription_recurring_price( $cart_item['data'], $cart_item['tws-subscription-info'] );
           $recurring_price_display = wc_get_price_to_display(
               $cart_item['data'],
               array(
                   'qty'   => $cart_item['quantity'],
                   'price' => $recurring_price,
               )
           );

           if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
               $recurring_tax = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
           } else {
               $recurring_tax = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
           }

           $pri = wc_price( $recurring_price_display ) . ' / ' . $recurring_period . ' ' . $recurring_tax;
           $pri = apply_filters( 'tws_recurring_price_html', $pri, $recurring_price, $recurring_period, $cart_item );
           // translators: 1.. html price, 2. date, 3. recurring price.
           return sprintf( __( '%1$s until %2$s then %3$s', 'tdv-woocommerce-subscription' ), $price_html, date_i18n( wc_date_format(), $subscription_info['next_payment_due_date'] ), $pri );
       }
        
        $price_html  = $price_html . '<span class="tws-max-lenght">' . $max_length . '</span>';
        $price_html .= '</div>';

        $price_html .= '</div>';

       // APPLY_FILTER: tws_change_subtotal_product_price: to change the html price of a subscription product.
       return $price_html; //apply_filters( 'tws_change_subtotal_product_price', $price_html, $product, $quantity, $cart_item, $show_complete_price_on_substotal_cart );

    }


    
    
    public function wps_tdv_change_subtotal_price_in_cart_html( $price_html, $cart_item, $cart_item_key ) {
        $product_id = ! empty( $cart_item['variation_id'] ) ? $cart_item['variation_id'] : $cart_item['product_id'];
        
        $product    = wc_get_product( $product_id );

        if ( ! wps_tdv_check_product_is_subscription( $product ) || ! isset( $cart_item['data'] ) ) {
            return $price_html;
        }

        $product       = $cart_item['data'];
//        $price         = apply_filters( 'tws_change_subtotal_price_in_cart_html', $cart_item['data']->get_price(), $cart_item['data'], $cart_item );
//        $price_current = apply_filters( 'tws_change_subtotal_price_current_in_cart_html', $product->get_price(), $product );
//
//        $product->set_price( $price );
//        $price_html = $this->change_general_price_html( $product, $cart_item['quantity'], false, $cart_item );

        $product->set_price( 100 );
    }
    
    
	/**
	 * This function is used to add susbcription price.
	 *
	 * @name wps_tdv_add_subscription_price_and_sigup_fee
	 * @param object $cart cart.
	 * @since    1.0.0
	 */
	public function wps_tdv_add_subscription_price_and_sigup_fee( $cart, $exist = false) {

		if ( isset( $cart ) && ! empty( $cart ) ) {

			foreach ( $cart->cart_contents as $key => $cart_data ) {
				if ( wps_tdv_check_product_is_subscription( $cart_data['data'] ) ) {

					$product_id = $cart_data['data']->get_id();
                    
                    $wps_tdv_free_trial_number = 0;
                    $wps_tdv_signup_fee = 0;
                    if (empty($exist)) {
                        $wps_tdv_free_trial_number = $this->wps_tdv_get_subscription_trial_period_number( $product_id );

                        $wps_tdv_signup_fee = $this->wps_tdv_get_subscription_initial_signup_price( $product_id );
                        $wps_tdv_signup_fee = is_numeric( $wps_tdv_signup_fee ) ? (float) $wps_tdv_signup_fee : 0;
                    }
					if ( isset( $wps_tdv_free_trial_number ) && ! empty( $wps_tdv_free_trial_number ) ) {
						if ( 0 != $wps_tdv_signup_fee ) {
							// Cart price.
							$wps_tdv_signup_fee = apply_filters( 'wps_tdv_cart_price_subscription', $wps_tdv_signup_fee, $cart_data );
							$cart_data['data']->set_price( $wps_tdv_signup_fee );
						} else {
							// Cart price.
							$wps_cart_price = apply_filters( 'wps_tdv_cart_price_subscription', 0, $cart_data );
							$cart_data['data']->set_price( $wps_cart_price );
						}
					} else {
						$product_price = $cart_data['data']->get_price();
						// Cart price.
						$product_price = apply_filters( 'wps_tdv_cart_price_subscription', $product_price, $cart_data );
						$product_price += $wps_tdv_signup_fee;
						$cart_data['data']->set_price( $product_price );
					}
				}
			}
		}

	}

	/**
	 * This function is used to add susbcription price.
	 *
	 * @name wps_tdv_get_subscription_trial_period_number
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_tdv_get_subscription_trial_period_number( $product_id ) {
		$wps_tdv_subscription_free_trial_number = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_number', true );
		return $wps_tdv_subscription_free_trial_number;
	}

	/**
	 * This function is used to add initial singup price.
	 *
	 * @name wps_tdv_get_subscription_initial_signup_price
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_tdv_get_subscription_initial_signup_price( $product_id ) {
		$wps_tdv_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_tdv_subscription_initial_signup_price', true );
		return $wps_tdv_subscription_initial_signup_price;
	}

	/**
	 * This function is used to process checkout.
	 *
	 * @name wps_tdv_process_checkout
	 * @param int   $order_id order_id.
	 * @param array $posted_data posted_data.
	 * @since    1.0.0
	 * @throws \Exception Return error.
	 */
	public function wps_tdv_process_checkout( $order_id, $posted_data ) {

		if ( ! $this->wps_tdv_check_cart_has_subscription_product() ) {
			return;
		}
		$order = wc_get_order( $order_id );
		/*delete failed order subscription*/
		wps_tdv_delete_failed_subscription( $order->get_id() );

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$wps_skip_creating_subscription = apply_filters( 'wps_skip_creating_subscription', true, $cart_item );
				if ( wps_tdv_check_product_is_subscription( $cart_item['data'] ) && $wps_skip_creating_subscription ) {

					if ( $cart_item['data']->is_on_sale() ) {
						$price = $cart_item['data']->get_sale_price();
					} else {
						$price = $cart_item['data']->get_regular_price();
					}
					$wps_recurring_total = $price * $cart_item['quantity'];

					$product_id = $cart_item['data']->get_id();

					$wps_recurring_data = $this->wps_tdv_get_subscription_recurring_data( $product_id );
					$wps_recurring_data['wps_recurring_total'] = $wps_recurring_total;
					$wps_recurring_data['product_id'] = $product_id;
					$wps_recurring_data['product_name'] = $cart_item['data']->get_name();
					$wps_recurring_data['product_qty'] = $cart_item['quantity'];

					$wps_recurring_data['line_tax_data'] = $cart_item['line_tax_data'];
					$wps_recurring_data['line_subtotal'] = $cart_item['line_subtotal'];
					$wps_recurring_data['line_subtotal_tax'] = $cart_item['line_subtotal_tax'];
					$wps_recurring_data['line_total'] = $cart_item['line_total'];
					$wps_recurring_data['line_tax'] = $cart_item['line_tax'];

					$wps_recurring_data = apply_filters( 'wps_tdv_cart_data_for_susbcription', $wps_recurring_data, $cart_item );

					if ( apply_filters( 'wps_tdv_is_upgrade_downgrade_order', false, $wps_recurring_data, $order, $posted_data, $cart_item ) ) {
						return;
					}

					$subscription = $this->wps_tdv_create_subscription( $order, $posted_data, $wps_recurring_data );
					if ( is_wp_error( $subscription ) ) {
						throw new Exception( $subscription->get_error_message() );
					} else {
						$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_order_has_subscription', true );
						if ( 'yes' != $wps_has_susbcription ) {
							update_post_meta( $order_id, 'wps_tdv_order_has_subscription', 'yes' );
						}
						do_action( 'wps_tdv_subscription_process_checkout', $order_id, $posted_data, $subscription );
					}
				}
			}
			$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_order_has_subscription', true );

			if ( 'yes' == $wps_has_susbcription ) {
				// phpcs:disable WordPress.Security.NonceVerification.Missing
				// After process checkout.
				do_action( 'wps_tdv_subscription_process_checkout_payment_method', $order_id, $posted_data );
				// phpcs:enable WordPress.Security.NonceVerification.Missing
			}
		}
	}

	/**
	 * This function is used to get ruccuring data.
	 *
	 * @name wps_tdv_get_subscription_recurring_data
	 * @param int $product_id product_id.
	 * @since    1.0.0
	 */
	public function wps_tdv_get_subscription_recurring_data( $product_id ) {

		$wps_recurring_data = array();

		$wps_tdv_subscription_number = get_post_meta( $product_id, 'wps_tdv_subscription_number', true );
		$wps_tdv_subscription_interval = get_post_meta( $product_id, 'wps_tdv_subscription_interval', true );

		$wps_recurring_data['wps_tdv_subscription_number'] = $wps_tdv_subscription_number;
		$wps_recurring_data['wps_tdv_subscription_interval'] = $wps_tdv_subscription_interval;
		$wps_tdv_subscription_expiry_number = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_number', true );

		if ( isset( $wps_tdv_subscription_expiry_number ) && ! empty( $wps_tdv_subscription_expiry_number ) ) {
			$wps_recurring_data['wps_tdv_subscription_expiry_number'] = $wps_tdv_subscription_expiry_number;
		}

		$wps_tdv_subscription_expiry_interval = get_post_meta( $product_id, 'wps_tdv_subscription_expiry_interval', true );

		if ( isset( $wps_tdv_subscription_expiry_interval ) && ! empty( $wps_tdv_subscription_expiry_interval ) ) {
			$wps_recurring_data['wps_tdv_subscription_expiry_interval'] = $wps_tdv_subscription_expiry_interval;
		}
		$wps_tdv_subscription_initial_signup_price = get_post_meta( $product_id, 'wps_tdv_subscription_initial_signup_price', true );

		if ( isset( $wps_tdv_subscription_expiry_interval ) && ! empty( $wps_tdv_subscription_expiry_interval ) ) {
			$wps_recurring_data['wps_tdv_subscription_initial_signup_price'] = $wps_tdv_subscription_initial_signup_price;
		}

		$wps_tdv_subscription_free_trial_number = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_number', true );

		if ( isset( $wps_tdv_subscription_free_trial_number ) && ! empty( $wps_tdv_subscription_free_trial_number ) ) {
			$wps_recurring_data['wps_tdv_subscription_free_trial_number'] = $wps_tdv_subscription_free_trial_number;
		}
		$wps_tdv_subscription_free_trial_interval = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_interval', true );
		if ( isset( $wps_tdv_subscription_free_trial_interval ) && ! empty( $wps_tdv_subscription_free_trial_interval ) ) {
			$wps_recurring_data['wps_tdv_subscription_free_trial_interval'] = $wps_tdv_subscription_free_trial_interval;
		}
		$wps_recurring_data = apply_filters( 'wps_tdv_recurring_data', $wps_recurring_data, $product_id );
		return $wps_recurring_data;
	}


	/**
	 * This function is used to create susbcription post.
	 *
	 * @name wps_tdv_create_subscription
	 * @param object $order order.
	 * @param array  $posted_data posted_data.
	 * @param array  $wps_recurring_data wps_recurring_data.
	 * @since    1.0.0
	 */
	public function wps_tdv_create_subscription( $order, $posted_data, $wps_recurring_data ) {
        // TODO
		if ( ! empty( $order ) ) {
			$order_id = $order->get_id();
			$current_date  = current_time( 'timestamp' );

			$wps_default_args = array(
				'wps_parent_order'   => $order_id,
				'wps_customer_id'    => $order->get_user_id(),
				'wps_schedule_start' => $current_date,
                'tradingview_id'     => get_post_meta( $order_id, 'tradingview_id', true )
			);

			$wps_args              = wp_parse_args( $wps_recurring_data, $wps_default_args );
			if ( isset( $posted_data['payment_method'] ) && $posted_data['payment_method'] ) {
				$wps_enabled_gateways = WC()->payment_gateways->get_available_payment_gateways();

				if ( isset( $wps_enabled_gateways[ $posted_data['payment_method'] ] ) ) {
					$wps_payment_method = $wps_enabled_gateways[ $posted_data['payment_method'] ];
					$wps_payment_method->validate_fields();
					$wps_args['_payment_method']       = $wps_payment_method->id;
					$wps_args['_payment_method_title'] = $wps_payment_method->get_title();
				}
			}
			$wps_args['wps_order_currency'] = $order->get_currency();
			$wps_args['wps_subscription_status'] = 'pending';

			$wps_args = apply_filters( 'wps_tdv_new_subscriptions_data', $wps_args );
			// translators: post title date parsed by strftime.
			$post_title_date = gmdate( _x( '%1$b %2$d, %Y @ %I:%M %p', 'subscription post title. "Subscriptions order - <this>"', 'subscriptions-for-tradingview' ) );
			$wps_subscription_data = array();
			$wps_subscription_data['post_type']     = 'wps_subscriptions';

			$wps_subscription_data['post_status']   = 'wc-wps_renewal';
			$wps_subscription_data['post_author']   = 1;
			$wps_subscription_data['post_parent']   = $order_id;
            
			/* translators: %s: post title date */
			$wps_subscription_data['post_title']    = sprintf( _x( 'TradingView Subscription &ndash; %s', 'Subscription post title', 'subscriptions-for-tradingview' ), $post_title_date );
			$wps_subscription_data['post_date_gmt'] = $order->get_date_created()->date( 'Y-m-d H:i:s' );
			$wps_subscription_data['post_date_gmt'] = $order->get_date_created()->date( 'Y-m-d H:i:s' );

			$subscription_id = wp_insert_post( $wps_subscription_data, true );

			if ( is_wp_error( $subscription_id ) ) {
				return $subscription_id;
			}
            // TODO
            $current_time = current_time( 'timestamp' );
			update_post_meta( $order_id, 'wps_subscription_id', $subscription_id );
			
            $wps_susbcription_trial_end = wps_tdv_susbcription_trial_date( $subscription_id, $current_time );
            update_post_meta( $subscription_id, 'wps_susbcription_trial_end', $wps_susbcription_trial_end );
            
            $wps_susbcription_end = wps_tdv_susbcription_expiry_date( $subscription_id, $current_time, $wps_susbcription_trial_end );
			update_post_meta( $subscription_id, 'wps_susbcription_end', $wps_susbcription_end );
            
            $wps_next_payment_date = wps_tdv_next_payment_date( $subscription_id, $current_time, $wps_susbcription_trial_end );
			update_post_meta( $subscription_id, 'wps_next_payment_date', $wps_next_payment_date );
            update_post_meta( $subscription_id, '_order_key', wc_generate_order_key() );

			/*if free trial*/

			$new_order = new WC_Order( $subscription_id );

			$billing_details = $order->get_address( 'billing' );
			$shipping_details = $order->get_address( 'shipping' );

			$new_order->set_address( $billing_details, 'billing' );
			$new_order->set_address( $shipping_details, 'shipping' );

			// If initial fee available.
			if ( isset( $wps_args['wps_tdv_subscription_initial_signup_price'] ) && ! empty( $wps_args['wps_tdv_subscription_initial_signup_price'] ) && empty( $wps_args['wps_tdv_subscription_free_trial_number'] ) ) {
				$initial_signup_price = $wps_args['wps_tdv_subscription_initial_signup_price'];
				// Currency switchers.
				if ( function_exists( 'wps_mmctdv_admin_fetch_currency_rates_from_base_currency' ) ) {
					$initial_signup_price = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $initial_signup_price );
				}
				$line_subtotal = $wps_args['line_subtotal'] - $initial_signup_price;
				$line_total = $line_subtotal;
				$wps_args['line_subtotal'] = $line_subtotal;
				$wps_args['line_total'] = $line_total;
			} elseif ( isset( $wps_args['wps_tdv_subscription_free_trial_number'] ) && ! empty( $wps_args['wps_tdv_subscription_free_trial_number'] ) ) {
				// Currency switchers.
				if ( function_exists( 'wps_mmctdv_admin_fetch_currency_rates_from_base_currency' ) ) {
					$wps_args['line_subtotal'] = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $wps_args['wps_recurring_total'] );
					$wps_args['line_total'] = wps_mmctdv_admin_fetch_currency_rates_from_base_currency( $wps_args['wps_order_currency'], $wps_args['wps_recurring_total'] );
				} else {
					$wps_args['line_subtotal'] = $wps_args['wps_recurring_total'];
					$wps_args['line_total'] = $wps_args['wps_recurring_total'];
				}
				$line_subtotal = $wps_args['line_subtotal'];
				$line_total = $wps_args['line_total'];
			} else {
				$wps_args['line_subtotal'] = $wps_args['wps_recurring_total'];
				$wps_args['line_total'] = $wps_args['wps_recurring_total'];
				$line_subtotal = $wps_args['line_subtotal'];
				$line_total = $wps_args['line_total'];
			}

			$_product = wc_get_product( $wps_args['product_id'] );

			$include = get_option( 'woocommerce_prices_include_tax' );
			if ( 'yes' == $include ) {

				$wps_pro_args = array(
					'variation'    => array(),
					'totals'       => array(
						'subtotal'     => $line_subtotal - $wps_args['line_subtotal_tax'],
						'subtotal_tax' => $wps_args['line_subtotal_tax'],
						'total'        => $line_total - $wps_args['line_subtotal_tax'],
						'tax'          => $wps_args['line_tax'],
						'tax_data'     => $wps_args['line_tax_data'],
					),
				);

			} else {

				$wps_pro_args = array(
					'variation' => array(),
					'totals'    => array(
						'subtotal'     => $line_subtotal,
						'subtotal_tax' => $wps_args['line_subtotal_tax'],
						'total'        => $line_total,
						'tax'          => $wps_args['line_tax'],
						'tax_data'     => $wps_args['line_tax_data'],
					),
				);
			}

			$wps_pro_args = apply_filters( 'wps_product_args_for_order', $wps_pro_args );

			$item_id = $new_order->add_product(
				$_product,
				$wps_args['product_qty'],
				$wps_pro_args
			);

			$new_order->update_taxes();
			$new_order->calculate_totals();
			$new_order->save();
			// After susbcription order created.
			do_action( 'wps_tdv_subscription_order', $new_order, $order_id );
			wps_tdv_update_meta_key_for_susbcription( $subscription_id, $wps_args );
			// After susbcription order created.
			do_action( 'wps_tdv_after_created_subscription', $subscription_id, $order_id );
			// After susbcription created.
			return apply_filters( 'wps_tdv_created_subscription', $subscription_id, $order_id );

		}

	}
    function wps_tdv_woocommerce_cart_item_name( $product_name, $cart_item, $cart_item_key ){
        $prie = $this->wps_tdv_subscription_product_get_info($cart_item['product_id']);
        return $product_name.$prie;
    }
    
	/**
	 * This function is used to add payment method form.
	 *
	 * @name wps_tdv_after_woocommerce_pay
	 * @since    1.0.0
	 */
	public function wps_tdv_after_woocommerce_pay() {
		global $wp;
		$wps_valid_request = false;

		if ( ! isset( $wp->query_vars['order-pay'] ) || ! wps_tdv_check_valid_subscription( absint( $wp->query_vars['order-pay'] ) ) ) {
			return;
		}

		ob_clean();
		echo '<div class="woocommerce">';
		if ( ! isset( $_GET['wps_add_payment_method'] ) && empty( $_GET['wps_add_payment_method'] ) ) {
			return;
		}
		$wps_subscription  = wc_get_order( absint( $_GET['wps_add_payment_method'] ) );
		$wps_valid_request = wps_tdv_validate_payment_request( $wps_subscription );

		if ( $wps_valid_request ) {
			$this->wps_tdv_set_customer_address( $wps_subscription );

			wc_get_template( 'myaccount/wps-add-new-payment-details.php', array( 'wps_subscription' => $wps_subscription ), '', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'public/partials/templates/' );
		}
	}

	/**
	 * This function is used to set customer address.
	 *
	 * @name wps_tdv_set_customer_address
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.1
	 */
	public function wps_tdv_set_customer_address( $wps_subscription ) {
		$wps_tdv_billing_country = $wps_subscription->get_billing_country();
		$wps_tdv_billing_state = $wps_subscription->get_billing_state();
		$wps_tdv_billing_postcode = $wps_subscription->get_billing_postcode();
		if ( $wps_tdv_billing_country ) {
			WC()->customer->set_billing_country( $wps_tdv_billing_country );
		}
		if ( $wps_tdv_billing_state ) {
			WC()->customer->set_billing_state( $wps_tdv_billing_state );
		}
		if ( $wps_tdv_billing_postcode ) {
			WC()->customer->set_billing_postcode( $wps_tdv_billing_postcode );
		}

	}

	/**
	 * This function is used to set customer address.
	 *
	 * @name wps_tdv_set_customer_address_for_payment
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.1
	 */
	public function wps_tdv_set_customer_address_for_payment( $wps_subscription ) {
		$wps_subscription_billing_country  = $wps_subscription->get_billing_country();
		$wps_subscription_billing_state  = $wps_subscription->get_billing_state();
		$wps_subscription_billing_postcode = $wps_subscription->get_billing_postcode();
		$wps_subscription_billing_city     = $wps_subscription->get_billing_postcode();

		if ( $wps_subscription_billing_country ) {
			WC()->customer->set_billing_country( $wps_subscription_billing_country );
		}
		if ( $wps_subscription_billing_state ) {
			WC()->customer->set_billing_state( $wps_subscription_billing_state );
		}
		if ( $wps_subscription_billing_postcode ) {
			WC()->customer->set_billing_postcode( $wps_subscription_billing_postcode );
		}
		if ( $wps_subscription_billing_city ) {
			WC()->customer->set_billing_city( $wps_subscription_billing_city );
		}

	}

	/**
	 * This function is used to process payment method form.
	 *
	 * @name wps_tdv_change_payment_method_form
	 * @since    1.0.0
	 */
	public function wps_tdv_change_payment_method_form() {
		if ( ! isset( $_POST['_wps_tdv_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wps_tdv_nonce'] ) ), 'wps_tdv__change_payment_method' ) ) {
			return;
		}

		if ( ! isset( $_POST['wps_change_change_payment'] ) && empty( $_POST['wps_change_change_payment'] ) ) {
			return;
		}
		$subscription_id = absint( $_POST['wps_change_change_payment'] );
		$wps_subscription = wc_get_order( $subscription_id );

		ob_start();
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( $wps_subscription->get_order_key() == $order_key ) {

			$this->wps_tdv_set_customer_address_for_payment( $wps_subscription );
			// Update payment method.
			$new_payment_method = isset( $_POST['payment_method'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_method'] ) ) : '';
			if ( empty( $new_payment_method ) ) {

				$wps_notice = __( 'Please enable payment method', 'subscriptions-for-tradingview' );
				wc_add_notice( $wps_notice, 'error' );
				$result_redirect = wc_get_endpoint_url( 'show-subscription', $wps_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
				wp_safe_redirect( $result_redirect );
				exit;
			}
			$available_gateways = WC()->payment_gateways->get_available_payment_gateways();

			$available_gateways[ $new_payment_method ]->validate_fields();
			$payment_method_title = $available_gateways[ $new_payment_method ]->get_title();

			if ( wc_notice_count( 'error' ) == 0 ) {

				$result = $available_gateways[ $new_payment_method ]->process_payment( $wps_subscription->get_id(), false, true );

				if ( 'success' == $result['result'] ) {
					$result['redirect'] = wc_get_endpoint_url( 'show-subscription', $wps_subscription->get_id(), wc_get_page_permalink( 'myaccount' ) );
						update_post_meta( $wps_subscription->get_id(), '_payment_method', $new_payment_method );
						update_post_meta( $wps_subscription->get_id(), '_payment_method_title', $payment_method_title );
				}

				if ( 'success' != $result['result'] ) {
					return;
				}
				$wps_subscription->save();

				$wps_notice = __( 'Payment Method Added Successfully', 'subscriptions-for-tradingview' );
				wc_add_notice( $wps_notice );
				wp_safe_redirect( $result['redirect'] );
				exit;
			}
		}
		ob_get_clean();
	}

	/**
	 * This function is used to process payment method form.
	 *
	 * @name wps_tdv_set_susbcription_total.
	 * @param int    $total total.
	 * @param object $wps_subscription wps_subscription.
	 * @since    1.0.0
	 */
	public function wps_tdv_set_susbcription_total( $total, $wps_subscription ) {

		global $wp;
		$order_key = isset( $_GET['key'] ) ? sanitize_text_field( wp_unslash( $_GET['key'] ) ) : '';
		if ( ! empty( $_POST['_wps_tdv_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wps_tdv_nonce'] ) ), 'wps_tdv__change_payment_method' ) && isset( $_POST['wps_change_change_payment'] ) && $wps_subscription->get_order_key() == $order_key && $wps_subscription->get_id() == absint( $_POST['wps_change_change_payment'] ) ) {
			$total = 0;
		} elseif ( isset( $wp->query_vars['order-pay'] ) && wps_tdv_check_valid_subscription( absint( $wp->query_vars['order-pay'] ) ) ) {

			$total = 0;
		}

		return $total;
	}

	/**
	 * This function is used to hide offline payment gateway for subscription product.
	 *
	 * @name wps_tdv_unset_offline_payment_gateway_for_subscription
	 * @param array $available_gateways available_gateways.
	 * @since    1.0.0
	 */
	public function wps_tdv_unset_offline_payment_gateway_for_subscription( $available_gateways ) {
		if ( is_admin() || ! is_checkout() ) {
			return $available_gateways;
		}
		$wps_has_subscription = false;

		foreach ( WC()->cart->get_cart_contents() as $key => $values ) {

			if ( wps_tdv_check_product_is_subscription( $values['data'] ) ) {
				$wps_has_subscription = true;
				break;
			}
		}
		if ( $wps_has_subscription ) {
			if ( isset( $available_gateways ) && ! empty( $available_gateways ) && is_array( $available_gateways ) ) {
				foreach ( $available_gateways as $key => $gateways ) {
					$wps_supported_method = array( 'stripe' );
					// Supported paymnet gateway.
					$wps_payment_method = apply_filters( 'wps_tdv_supported_payment_gateway_for_woocommerce', $wps_supported_method, $key );

					if ( ! in_array( $key, $wps_payment_method ) ) {
						unset( $available_gateways[ $key ] );
					}
				}
			}
		}
		return $available_gateways;
	}

	/**
	 * Register the endpoints on my_account page.
	 *
	 * @name wps_tdv_add_subscription_tab_on_myaccount_page
	 * @since    1.0.0
	 */
	public function wps_tdv_add_subscription_tab_on_myaccount_page() {
		add_rewrite_endpoint( 'wps_subscriptions', EP_PAGES );
		add_rewrite_endpoint( 'show-subscription', EP_PAGES );
		add_rewrite_endpoint( 'wps-add-payment-method', EP_PAGES );
	}

	/**
	 * Register the endpoints on my_account page.
	 *
	 * @name wps_tdv_custom_endpoint_query_vars.
	 * @param array $vars vars.
	 * @since    1.0.0
	 */
	public function wps_tdv_custom_endpoint_query_vars( $vars ) {
		$vars[] = 'wps_subscriptions';
		$vars[] = 'show-subscription';
		$vars[] = 'wps-add-payment-method';
		return $vars;
	}

	/**
	 * This function is used to add TradingView susbcriptions Tab in MY ACCOUNT Page
	 *
	 * @name wps_tdv_add_subscription_dashboard_on_myaccount_page
	 * @since 1.0.0
	 * @param array $items items.
	 */
	public function wps_tdv_add_subscription_dashboard_on_myaccount_page( $items ) {

		$logout = $items['customer-logout'];
		unset( $items['customer-logout'] );
		$items['wps_subscriptions'] = __( 'Subscriptions', 'subscriptions-for-tradingview' );
		$items['customer-logout'] = $logout;

		return $items;
	}

	/**
	 * This function is used to add my account page template
	 *
	 * @name wps_tdv_subscription_dashboard_content.
	 * @param int $wps_current_page current page.
	 * @since 1.0.0
	 */
	public function wps_tdv_subscription_dashboard_content( $wps_current_page = 1 ) {

		$user_id = get_current_user_id();

		$args = array(
			'numberposts' => -1,
			'post_type'   => 'wps_subscriptions',
			'post_status' => 'wc-wps_renewal',
			'meta_query' => array(
				array(
					'key'   => 'wps_customer_id',
					'value' => $user_id,
				),
			),

		);
		$wps_subscriptions = get_posts( $args );

		$wps_per_page = get_option( 'posts_per_page', 10 );
		$wps_current_page = empty( $wps_current_page ) ? 1 : absint( $wps_current_page );
		$wps_num_pages = ceil( count( $wps_subscriptions ) / $wps_per_page );
		$subscriptions = array_slice( $wps_subscriptions, ( $wps_current_page - 1 ) * $wps_per_page, $wps_per_page );
		wc_get_template(
			'myaccount/wps-susbcrptions.php',
			array(
				'wps_subscriptions' => $subscriptions,
				'wps_current_page'  => $wps_current_page,
				'wps_num_pages' => $wps_num_pages,
				'paginate'      => true,
			),
			'',
			SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'public/partials/templates/'
		);
	}

	/**
	 * This function is used to restrict guest user for subscription product.
	 *
	 * @name wps_tdv_subscription_before_checkout_form
	 * @since 1.0.0
	 * @param object $checkout checkout.
	 */
	public function wps_tdv_subscription_before_checkout_form( $checkout = '' ) {

		if ( ! is_user_logged_in() ) {
			if ( $this->wps_tdv_check_cart_has_subscription_product() ) {
				if ( true === $checkout->enable_guest_checkout ) {
					$checkout->enable_guest_checkout = false;
				}
			}
		}
	}

	/**
	 * This function is used to show recurring price on account page.
	 *
	 * @name wps_tdv_display_susbcription_recerring_total_account_page_callback
	 * @since 1.0.0
	 * @param int $subscription_id subscription_id.
	 */
	public function wps_tdv_display_susbcription_recerring_total_account_page_callback( $subscription_id ) {
		$susbcription = wc_get_order( $subscription_id );

		if ( isset( $susbcription ) && ! empty( $susbcription ) ) {
			$price = $susbcription->get_total();
			$wps_curr_args = array(
				'currency' => $susbcription->get_currency(),
			);
		} else {
			$price = get_post_meta( $subscription_id, 'wps_recurring_total', true );
		}

		$wps_curr_args = array();

		$price = wc_price( $price, $wps_curr_args );
		$wps_recurring_number = get_post_meta( $subscription_id, 'wps_tdv_subscription_number', true );
		$wps_recurring_interval = get_post_meta( $subscription_id, 'wps_tdv_subscription_interval', true );
		$wps_price_html = wps_tdv_get_time_interval_for_price( $wps_recurring_number, $wps_recurring_interval );

		/* translators: %s: subscription interval */
		$price .= sprintf( esc_html( ' / %s ' ), $wps_price_html );
		$wps_subscription_status = get_post_meta( $subscription_id, 'wps_subscription_status', true );
		if ( 'cancelled' === $wps_subscription_status ) {
			$price = '---';
		}
		echo wp_kses_post( $price );
	}


	/**
	 * This function is used to include subscription details template on account page.
	 *
	 * @name wps_tdv_show_subscription_details
	 * @since 1.0.0
	 * @param int $wps_subscription_id wps_subscription_id.
	 */
	public function wps_tdv_show_subscription_details( $wps_subscription_id ) {

		if ( ! wps_tdv_check_valid_subscription( $wps_subscription_id ) ) {
			echo '<div class="woocommerce-error wps_tdv_invalid_subscription">' . esc_html__( 'Not a valid subscription', 'subscriptions-for-tradingview' ) . '</div>';
			return;
		}

		wc_get_template( 'myaccount/wps-show-subscription-details.php', array( 'wps_subscription_id' => $wps_subscription_id ), '', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'public/partials/templates/' );

	}


	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wps_tdv_cancel_susbcription
	 * @since 1.0.0
	 */
	public function wps_tdv_cancel_susbcription() {

		if ( isset( $_GET['wps_subscription_status'] ) && isset( $_GET['wps_subscription_id'] ) && isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$user_id      = get_current_user_id();

			$wps_status   = sanitize_text_field( wp_unslash( $_GET['wps_subscription_status'] ) );
			$wps_subscription_id = sanitize_text_field( wp_unslash( $_GET['wps_subscription_id'] ) );
			if ( wps_tdv_check_valid_subscription( $wps_subscription_id ) ) {
				$this->wps_tdv_cancel_susbcription_order_by_customer( $wps_subscription_id, $wps_status, $user_id );
			}
		}
	}

	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wps_tdv_cancel_susbcription_order_by_customer
	 * @param int    $wps_subscription_id wps_subscription_id.
	 * @param string $wps_status wps_status.
	 * @param int    $user_id user_id.
	 * @since 1.0.0
	 */
	public function wps_tdv_cancel_susbcription_order_by_customer( $wps_subscription_id, $wps_status, $user_id ) {

		$wps_customer_id = get_post_meta( $wps_subscription_id, 'wps_customer_id', true );
		if ( 'active' == $wps_status && $wps_customer_id == $user_id ) {

			do_action( 'wps_tdv_subscription_cancel', $wps_subscription_id, 'Cancel' );

			do_action( 'wps_tdv_cancel_susbcription', $wps_subscription_id, $user_id );
			wc_add_notice( __( 'Subscription Cancelled Successfully', 'subscriptions-for-tradingview' ), 'success' );
			$redirect_url = wc_get_endpoint_url( 'show-subscription', $wps_subscription_id, wc_get_page_permalink( 'myaccount' ) );
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * This function is used to update susbcription.
	 *
	 * @name wps_tdv_woocommerce_order_status_changed
	 * @param int    $order_id order_id.
	 * @param string $old_status old_status.
	 * @param string $new_status new_status.
	 * @since 1.0.0
	 */
	public function wps_tdv_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {

		$is_activated = get_post_meta( $order_id, 'wps_tdv_subscription_activated', true );

		if ( 'yes' == $is_activated ) {
			return;
		}
		if ( $old_status != $new_status ) {
			if ( 'completed' == $new_status || 'processing' == $new_status ) {
				$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_order_has_subscription', true );

				if ( 'yes' == $wps_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wps_subscriptions',
						'post_status'   => 'wc-wps_renewal',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wps_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wps_subscription_status',
								'value' => 'pending',
							),

						),
					);
					$wps_subscriptions = get_posts( $args );

					if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
						foreach ( $wps_subscriptions as $key => $subscription ) {

							$status = 'active';
							$status = apply_filters( 'wps_tdv_set_subscription_status', $status, $subscription->ID );
							$current_time = apply_filters( 'wps_tdv_subs_curent_time', current_time( 'timestamp' ), $subscription->ID );

							update_post_meta( $subscription->ID, 'wps_subscription_status', $status );
							update_post_meta( $subscription->ID, 'wps_schedule_start', $current_time );

							$wps_susbcription_trial_end = wps_tdv_susbcription_trial_date( $subscription->ID, $current_time );
							update_post_meta( $subscription->ID, 'wps_susbcription_trial_end', $wps_susbcription_trial_end );

							$wps_next_payment_date = wps_tdv_next_payment_date( $subscription->ID, $current_time, $wps_susbcription_trial_end );

							$wps_next_payment_date = apply_filters( 'wps_tdv_next_payment_date', $wps_next_payment_date, $subscription->ID );

							update_post_meta( $subscription->ID, 'wps_next_payment_date', $wps_next_payment_date );

							$wps_susbcription_end = wps_tdv_susbcription_expiry_date( $subscription->ID, $current_time, $wps_susbcription_trial_end );
							$wps_susbcription_end = apply_filters( 'wps_tdv_susbcription_end_date', $wps_susbcription_end, $subscription->ID );
							update_post_meta( $subscription->ID, 'wps_susbcription_end', strtotime(date_i18n('Y-m-d 23:23:59', $wps_susbcription_end)) );

							// Set billing id.
							$billing_agreement_id = get_post_meta( $order_id, '_ppec_billing_agreement_id', true );
							if ( isset( $billing_agreement_id ) && ! empty( $billing_agreement_id ) ) {
								update_post_meta( $subscription->ID, '_wps_paypal_subscription_id', $billing_agreement_id );
							}
							do_action( 'wps_tdv_order_status_changed', $order_id, $subscription->ID );
						}
						update_post_meta( $order_id, 'wps_tdv_subscription_activated', 'yes' );
					}
				}
			}
		}
	}

	/**
	 * This function is used to set next payment date.
	 *
	 * @name wps_tdv_check_next_payment_date
	 * @param int    $subscription_id subscription_id.
	 * @param string $wps_next_payment_date wps_next_payment_date.
	 * @since 1.0.0
	 */
	public function wps_tdv_check_next_payment_date( $subscription_id, $wps_next_payment_date ) {
		$wps_tdv_subscription_number = get_post_meta( $subscription_id, 'wps_tdv_subscription_number', true );
		$wps_tdv_subscription_expiry_number = get_post_meta( $subscription_id, 'wps_tdv_subscription_expiry_number', true );
		$wps_tdv_subscription_free_trial_number = get_post_meta( $subscription_id, 'wps_tdv_subscription_free_trial_number', true );

		if ( empty( $wps_tdv_subscription_free_trial_number ) ) {
			if ( ! empty( $wps_tdv_subscription_number ) && ! empty( $wps_tdv_subscription_expiry_number ) ) {
				if ( $wps_tdv_subscription_number == $wps_tdv_subscription_expiry_number ) {
					$wps_next_payment_date = 0;
				}
			}
		}
		return $wps_next_payment_date;
	}
	/**
	 * This function is used to set single quantity for susbcription product.
	 *
	 * @name wps_tdv_hide_quantity_fields_for_subscription
	 * @param bool   $return return.
	 * @param object $product product.
	 * @since 1.0.0
	 */
	public function wps_tdv_hide_quantity_fields_for_subscription( $return, $product ) {

		if ( wps_tdv_check_plugin_enable() && wps_tdv_check_product_is_subscription( $product ) ) {
			$return = true;
		}
		return apply_filters( 'wps_tdv_show_quantity_fields_for_susbcriptions', $return, $product );
	}

	/**
	 * This function is used to restrict guest user susbcription product.
	 *
	 * @name wps_tdv_woocommerce_add_to_cart_validation
	 * @param bool $validate validate.
	 * @param int  $product_id product_id.
	 * @param int  $quantity quantity.
	 * @param int  $variation_id as variation_id.
	 * @param bool $variations as variations.
	 * @since 1.0.0
	 */
	public function wps_tdv_woocommerce_add_to_cart_validation( $validate, $product_id, $quantity, $variation_id = 0, $variations = null ) {

		$product = wc_get_product( $product_id );
		if ( is_object( $product ) && 'variable' === $product->get_type() ) {
			$product    = wc_get_product( $variation_id );
			$product_id = $variation_id;
		}
		if ( $this->wps_tdv_check_cart_has_subscription_product() && wps_tdv_check_product_is_subscription( $product ) ) {

			$validate = apply_filters( 'wps_tdv_add_to_cart_validation', false, $product_id, $quantity );

			if ( ! $validate ) {
				wc_add_notice( __( 'You can not add multiple subscription products in cart', 'subscriptions-for-tradingview' ), 'error' );
			}
		}
		return apply_filters( 'wps_tdv_expiry_add_to_cart_validation', $validate, $product_id, $quantity );
	}

	/**
	 * This function is used to set payment options.
	 *
	 * @name wps_tdv_woocommerce_cart_needs_payment
	 * @param bool   $wps_needs_payment wps_needs_payment.
	 * @param object $cart cart.
	 * @since 1.0.0
	 */
	public function wps_tdv_woocommerce_cart_needs_payment( $wps_needs_payment, $cart ) {
		$wps_is_payment = false;
		$wps_cart_has_subscription = false;
		if ( $wps_needs_payment ) {
			return $wps_needs_payment;
		}

		if ( ! empty( WC()->cart->cart_contents ) ) {
			foreach ( WC()->cart->cart_contents as $cart_item ) {

				if ( wps_tdv_check_product_is_subscription( $cart_item['data'] ) ) {
					$wps_cart_has_subscription = true;
					$product_id = $cart_item['data']->get_id();
					$wps_free_trial_length = get_post_meta( $product_id, 'wps_tdv_subscription_free_trial_number', true );
					if ( $wps_free_trial_length > 0 ) {
						$wps_is_payment = true;
						break;
					}
				}
			}
		}
		if ( $wps_is_payment && 0 == $cart->total ) {
			$wps_needs_payment = true;
		} elseif ( $wps_cart_has_subscription && 0 == $cart->total ) {
			$wps_needs_payment = true;
		}

		return apply_filters( 'wps_tdv_needs_payment', $wps_needs_payment, $cart );
	}

	/**
	 * This function is used to update susbcription.
	 *
	 * @name wps_tdv_woocommerce_order_status_changed
	 * @param int    $order_id order_id.
	 * @param string $old_status old_status.
	 * @param string $new_status new_status.
	 * @since 1.0.0
	 */
	public function wps_tdv__cancel_subs_woocommerce_order_status_changed( $order_id, $old_status, $new_status ) {

		if ( $old_status != $new_status ) {

			if ( 'cancelled' === $new_status ) {
				$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_order_has_subscription', true );

				if ( 'yes' == $wps_has_susbcription ) {
					$args = array(
						'numberposts' => -1,
						'post_type'   => 'wps_subscriptions',
						'post_status'   => 'wc-wps_renewal',
						'meta_query' => array(
							'relation' => 'AND',
							array(
								'key'   => 'wps_parent_order',
								'value' => $order_id,
							),
							array(
								'key'   => 'wps_subscription_status',
								'value' => array( 'active', 'pending' ),
							),
						),
					);
					$wps_subscriptions = get_posts( $args );
					if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
						foreach ( $wps_subscriptions as $key => $subscription ) {
							wps_tdv_send_email_for_cancel_susbcription( $subscription->ID );
							update_post_meta( $subscription->ID, 'wps_subscription_status', 'cancelled' );
						}
					}
                    
                    // TODO REMOVE ACCOUNT
                    
				}
			} elseif ( 'failed' === $new_status ) {
				$this->wps_tdv_hold_subscription( $order_id );
			} elseif ( 'completed' == $new_status || 'processing' == $new_status ) {
				$this->wps_tdv_active_after_on_hold( $order_id );
			}
		}
	}

	/**
	 * This function is used to hold the subscription when order failed.
	 *
	 * @param int $order_id order_id.
	 * @return void
	 */
	public function wps_tdv_hold_subscription( $order_id ) {

		$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_renewal_order', true );
		if ( 'yes' == $wps_has_susbcription ) {
			$parent_order = get_post_meta( $order_id, 'wps_tdv_parent_order_id', true );
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $parent_order,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => array( 'active', 'pending' ),
					),
				),
			);
			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {

					update_post_meta( $subscription->ID, 'wps_subscription_status', 'on-hold' );
					do_action( 'wps_tdv_subscription_on_hold_renewal', $subscription->ID );
				}
			}
		} else {
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $order_id,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => array( 'active', 'pending' ),
					),
				),
			);

			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {
					update_post_meta( $subscription->ID, 'wps_subscription_status', 'on-hold' );
					do_action( 'wps_tdv_subscription_on_hold_renewal', $subscription->ID );
				}
			}
		}

	}

	/**
	 * This function is used to activate subscription after on hold.
	 *
	 * @param int $order_id order_id.
	 * @return void
	 */
	public function wps_tdv_active_after_on_hold( $order_id ) {

		$wps_has_susbcription = get_post_meta( $order_id, 'wps_tdv_renewal_order', true );
		if ( 'yes' == $wps_has_susbcription ) {
			$parent_order = get_post_meta( $order_id, 'wps_tdv_parent_order_id', true );
			$args = array(
				'numberposts' => -1,
				'post_type'   => 'wps_subscriptions',
				'post_status'   => 'wc-wps_renewal',
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'   => 'wps_parent_order',
						'value' => $parent_order,
					),
					array(
						'key'   => 'wps_subscription_status',
						'value' => 'on-hold',
					),
				),
			);

			$wps_subscriptions = get_posts( $args );
			if ( isset( $wps_subscriptions ) && ! empty( $wps_subscriptions ) && is_array( $wps_subscriptions ) ) {
				foreach ( $wps_subscriptions as $key => $subscription ) {
					$wps_next_payment_date = wps_tdv_next_payment_date( $subscription->ID, current_time( 'timestamp' ), 0 );
					update_post_meta( $subscription->ID, 'wps_subscription_status', 'active' );
					update_post_meta( $subscription->ID, 'wps_next_payment_date', $wps_next_payment_date );
					do_action( 'wps_tdv_subscription_active_renewal', $subscription->ID );

				}
			}
		}
	}

	/**
	 * Registration required if have subscription products for guest user.
	 *
	 * @param boolean $registration_required .
	 */
	public function wps_tdv_registration_required( $registration_required ) {
		$wps_has_subscription = wps_tdv_is_cart_has_subscription_product();
		if ( $wps_has_subscription && ! $registration_required ) {
			$registration_required = true;
		}
		return $registration_required;
	}

	/**
	 * Show the notice for stripe payment description.
	 *
	 * @param string  $description .
	 * @param integer $gateway_id .
	 */
	public function wps_tdv_change_payment_gateway_description( $description, $gateway_id ) {
		$available_gateways   = WC()->payment_gateways->get_available_payment_gateways();
		$experimental_feature = 'no';
		if ( isset( $available_gateways['stripe'] ) && isset( $available_gateways['stripe']->settings['upe_checkout_experience_enabled'] ) ) {
			$experimental_feature = $available_gateways['stripe']->settings['upe_checkout_experience_enabled'];
		}
		$wps_has_subscription = wps_tdv_is_cart_has_subscription_product();

		if ( 'stripe' === $gateway_id && $wps_has_subscription && 'yes' === $experimental_feature ) {
			$description .= '<i><span class="wps_tdv_experimental_feature_notice">' . esc_html__( 'Only the Card is supported for the recurring payment', 'subscriptions-for-tradingview' ) . '</span><i><br>';
		}
		return $description;

	}


}
