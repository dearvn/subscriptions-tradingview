<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/admin
 * @author     Donald<donald.nguyen.it@gmail.com>
 */
class Subscriptions_For_TradingView_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wps_tdv_admin_enqueue_styles( $hook ) {

		$wps_tdv_screen_ids = wps_tdv_get_page_screen();
		$screen = get_current_screen();

		if ( isset( $screen->id ) && in_array( $screen->id, $wps_tdv_screen_ids ) || 'wp-swings_page_home' == $screen->id ) {
			// Multistep form css.
			if ( ! wps_tdv_check_multistep() ) {
				$style_url        = SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'build/style-index.css';
				wp_enqueue_style(
					'wps-tdv-admin-react-styles',
					$style_url,
					array(),
					time(),
					false
				);
				return;
			}
			wp_enqueue_style( 'wps-tdv-select2-css', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/select-2/subscriptions-for-tradingview-select2.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-tdv-meterial-css', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-components-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-tdv-meterial-css2', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.css', array(), time(), 'all' );
			wp_enqueue_style( 'wps-tdv-meterial-lite', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-lite.min.css', array(), time(), 'all' );

			wp_enqueue_style( 'wps-tdv-meterial-icons-css', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/icon.css', array(), time(), 'all' );

			wp_enqueue_style( $this->plugin_name . '-admin-global', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/css/subscriptions-for-tradingview-admin-global.css', array( 'wps-tdv-meterial-icons-css' ), time(), 'all' );

			wp_enqueue_style( $this->plugin_name, SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/css/subscriptions-for-tradingview-admin.css', array(), time(), 'all' );
		}

		if ( isset( $screen->id ) && 'product' == $screen->id && 'wp-swings_page_home' == $screen->id ) {
			wp_enqueue_style( 'wps-tdv-admin-single-product-css', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/css/subscription-for-tradingview-product-edit.css', array(), time(), 'all' );

		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 * @param    string $hook      The plugin page slug.
	 */
	public function wps_tdv_admin_enqueue_scripts( $hook ) {

		$wps_tdv_screen_ids = wps_tdv_get_page_screen();
		$screen = get_current_screen();
		if ( isset( $screen->id ) && in_array( $screen->id, $wps_tdv_screen_ids ) || 'wp-swings_page_home' == $screen->id ) {

			if ( ! wps_tdv_check_multistep() ) {

				// Js for the multistep from.
				$script_path       = '../../build/index.js';
				$script_asset_path = SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'build/index.asset.php';
				$script_asset      = file_exists( $script_asset_path )
					? require $script_asset_path
					: array(
						'dependencies' => array(
							'wp-hooks',
							'wp-element',
							'wp-i18n',
							'wc-components',
						),
						'version'      => filemtime( $script_path ),
					);
				$script_url        = SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'build/index.js';
				wp_register_script(
					'wps-tdv-react-app-block',
					$script_url,
					$script_asset['dependencies'],
					$script_asset['version'],
					true
				);
				wp_enqueue_script( 'wps-tdv-react-app-block' );
				wp_localize_script(
					'wps-tdv-react-app-block',
					'frontend_ajax_object',
					array(
						'ajaxurl'            => admin_url( 'admin-ajax.php' ),
						'wps_tdv_react_nonce' => wp_create_nonce( 'ajax-nonce' ),
						'redirect_url' => admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' ),
						'disable_track_url' => admin_url( 'admin.php?page=subscriptions_for_tradingview_menu&tdv_tab=subscriptions-for-tradingview-developer' ),
						'supported_gateway' => wps_tdv_get_subscription_supported_payment_method(),
						'wps_build_in_paypal_setup_url' => admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wps_paypal' ),
					)
				);
				return;
			}
			wp_enqueue_script( 'wps-tdv-select2', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/select-2/subscriptions-for-tradingview-select2.js', array( 'jquery' ), time(), false );

			wp_enqueue_script( 'wps-tdv-metarial-js', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-components-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-tdv-metarial-js2', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-components-v5.0-web.min.js', array(), time(), false );
			wp_enqueue_script( 'wps-tdv-metarial-lite', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'package/lib/material-design/material-lite.min.js', array(), time(), false );

			wp_register_script( $this->plugin_name . 'admin-js', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/js/subscriptions-for-tradingview-admin.js', array( 'jquery', 'wps-tdv-select2', 'wps-tdv-metarial-js', 'wps-tdv-metarial-js2', 'wps-tdv-metarial-lite' ), $this->version, false );

			wp_localize_script(
				$this->plugin_name . 'admin-js',
				'tdv_admin_param',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'reloadurl' => admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' ),
					'tdv_gen_tab_enable' => get_option( 'tdv_radio_switch_demo' ),
				)
			);

			wp_enqueue_script( $this->plugin_name . 'admin-js' );
		}

		if ( isset( $screen->id ) && 'product' == $screen->id ) {
			wp_register_script( 'wps-tdv-admin-single-product-js', SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/js/subscription-for-tradingview-product-edit.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'wps-tdv-admin-single-product-js' );

			$wps_tdv_data = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'reloadurl' => admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' ),
				'day' => __( 'Days', 'subscriptions-for-tradingview' ),
				'week' => __( 'Weeks', 'subscriptions-for-tradingview' ),
				'month' => __( 'Months', 'subscriptions-for-tradingview' ),
				'year' => __( 'Years', 'subscriptions-for-tradingview' ),
				'expiry_notice' => __( 'Expiry Interval must be greater than subscription interval', 'subscriptions-for-tradingview' ),
				'expiry_days_notice' => __( 'Expiry Interval must not be greater than 90 Days', 'subscriptions-for-tradingview' ),
				'expiry_week_notice' => __( 'Expiry Interval must not be greater than 52 Weeks', 'subscriptions-for-tradingview' ),
				'expiry_month_notice' => __( 'Expiry Interval must not be greater than 24 Months', 'subscriptions-for-tradingview' ),
				'expiry_year_notice' => __( 'Expiry Interval must not be greater than 5 Years', 'subscriptions-for-tradingview' ),
				'trial_days_notice' => __( 'Trial period must not be greater than 90 Days', 'subscriptions-for-tradingview' ),
				'trial_week_notice' => __( 'Trial period must not be greater than 52 Weeks', 'subscriptions-for-tradingview' ),
				'trial_month_notice' => __( 'Trial period must not be greater than 24 Months', 'subscriptions-for-tradingview' ),
				'trial_year_notice' => __( 'Trial period must not be greater than 5 Years', 'subscriptions-for-tradingview' ),
			);
			wp_localize_script(
				'wps-tdv-admin-single-product-js',
				'tdv_product_param',
				$wps_tdv_data
			);
			wp_enqueue_script( 'jquery-ui-datepicker' );

		}
	}
	/**
	 * Adding settings menu for Subscriptions TradingView.
	 *
	 * @since    1.0.0
	 */
	public function wps_tdv_options_page() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['wps-plugins'] ) ) {

			add_menu_page( 'TradingView', 'TradingView', 'manage_options', 'wps-plugins', array( $this, 'wps_plugins_listing_page' ), SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL . 'admin/images/tradingviews_logo.png', 15 );
			$tdv_menus = apply_filters( 'wps_add_plugins_menus_array', array() );
			if ( is_array( $tdv_menus ) && ! empty( $tdv_menus ) ) {
				foreach ( $tdv_menus as $tdv_key => $tdv_value ) {
					add_submenu_page( 'wps-plugins', $tdv_value['name'], $tdv_value['name'], 'manage_options', $tdv_value['menu_link'], array( $tdv_value['instance'], $tdv_value['function'] ) );
				}
				$is_home = false;
			}
		} else {
			if ( ! empty( $submenu['wps-plugins'] ) ) {
				foreach ( $submenu['wps-plugins'] as $key => $value ) {
					if ( 'Home' === $value[0] ) {
						$is_home = true;
					}
				}
				if ( ! $is_home ) {
					if ( wps_tdv_check_multistep() ) {
						add_submenu_page( 'wps-plugins', 'Home', 'Home', 'manage_options', 'home', array( $this, 'wps_tdv_welcome_callback_function' ), 1 );
					}
				}
			}
		}
		add_submenu_page( 'woocommerce', __( 'TradingView Subscriptions', 'subscriptions-for-tradingview' ), __( 'TradingView Subscriptions', 'subscriptions-for-tradingview' ), 'manage_options', 'subscriptions-for-tradingview', array( $this, 'wps_tdv_addsubmenu_woocommerce' ) );

	}

	/**
	 * This function is used to add submenu of subscription inside woocommerce.
	 *
	 * @since 1.2.0
	 * @return void
	 */
	public function wps_tdv_addsubmenu_woocommerce() {
		$permalink = admin_url( 'admin.php?page=subscriptions_for_tradingview_menu&tdv_tab=subscriptions-for-tradingview-subscriptions-table' );
		wp_safe_redirect( $permalink );
		exit;
	}

	/**
	 *
	 * Adding the default menu into the wordpress menu
	 *
	 * @name tradingviews_callback_function
	 * @since 1.0.0
	 */
	public function wps_tdv_welcome_callback_function() {
		include SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/partials/subscriptions-for-tradingview-welcome.php';
	}

	/**
	 * Removing default submenu of parent menu in backend dashboard
	 *
	 * @since   1.0.0
	 */
	public function wps_tdv_remove_default_submenu() {
		global $submenu;
		if ( is_array( $submenu ) && array_key_exists( 'wps-plugins', $submenu ) ) {
			if ( isset( $submenu['wps-plugins'][0] ) ) {
				unset( $submenu['wps-plugins'][0] );
			}
		}
	}


	/**
	 * Subscriptions TradingView wps_tdv_admin_submenu_page.
	 *
	 * @since 1.0.0
	 * @param array $menus Marketplace menus.
	 */
	public function wps_tdv_admin_submenu_page( $menus = array() ) {
		$menus[] = array(
			'name'            => __( 'Subscriptions For TradingView', 'subscriptions-for-tradingview' ),
			'slug'            => 'subscriptions_for_tradingview_menu',
			'menu_link'       => 'subscriptions_for_tradingview_menu',
			'instance'        => $this,
			'function'        => 'wps_tdv_options_menu_html',
		);
		return $menus;
	}


	/**
	 * Subscriptions TradingView wps_plugins_listing_page.
	 *
	 * @since 1.0.0
	 */
	public function wps_plugins_listing_page() {
		// Add menus.
		$active_marketplaces = apply_filters( 'wps_add_plugins_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/partials/welcome.php';
		}
	}

	/**
	 * Subscriptions TradingView admin menu page.
	 *
	 * @since    1.0.0
	 */
	public function wps_tdv_options_menu_html() {

		include_once SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/partials/subscriptions-for-tradingview-admin-dashboard.php';
	}


	/**
	 * Subscriptions TradingView admin menu page.
	 *
	 * @since    1.0.0
	 * @param array $tdv_settings_general Settings fields.
	 */
	public function wps_tdv_admin_general_settings_page( $tdv_settings_general ) {

		$tdv_settings_general = array(
            array(
				'title' => __( 'TradingView Token', 'subscriptions-for-tradingview' ),
				'type'  => 'text',
				'description'  => __( 'Use this option to save TradingView Token.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_token',
				'value' => get_option( 'wps_tdv_token', '' ),
				'class' => 'tdv-text-class',
				'placeholder' => __( 'TradingView Token', 'subscriptions-for-tradingview' ),
			),
			array(
				'title' => __( 'Enable/Disable Subscription', 'subscriptions-for-tradingview' ),
				'type'  => 'checkbox',
				'description'  => __( 'Check this box to enable the subscription.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_enable_plugin',
				'class' => 'tdv-checkbox-class',
				'value' => 'on',
				'checked' => ( 'on' === get_option( 'wps_tdv_enable_plugin', '' ) ? 'on' : 'off' ),
			),
			array(
				'title' => __( 'Add to cart text', 'subscriptions-for-tradingview' ),
				'type'  => 'text',
				'description'  => __( 'Use this option to change add to cart button text.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_add_to_cart_text',
				'value' => get_option( 'wps_tdv_add_to_cart_text', '' ),
				'class' => 'tdv-text-class',
				'placeholder' => __( 'Add to cart button text', 'subscriptions-for-tradingview' ),
			),
			array(
				'title' => __( 'Place order text', 'subscriptions-for-tradingview' ),
				'type'  => 'text',
				'description'  => __( 'Use this option to change place order button text.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_place_order_button_text',
				'value' => get_option( 'wps_tdv_place_order_button_text', '' ),
				'class' => 'tdv-text-class',
				'placeholder' => __( 'Place order button text', 'subscriptions-for-tradingview' ),
			),
			array(
				'title' => __( 'Allow Customer to cancel Subscription', 'subscriptions-for-tradingview' ),
				'type'  => 'checkbox',
				'description'  => __( 'Enable this option to allow the customer to cancel the subscription.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_cancel_subscription_for_customer',
				'checked' => ( 'on' === get_option( 'wps_tdv_cancel_subscription_for_customer', '' ) ? 'on' : 'off' ),
				'value' => 'on',
				'class' => 'tdv-checkbox-class',
			),
			array(
				'title' => __( 'Enable Log', 'subscriptions-for-tradingview' ),
				'type'  => 'checkbox',
				'description'  => __( 'Enable Log.', 'subscriptions-for-tradingview' ),
				'id'    => 'wps_tdv_enable_subscription_log',
				'value' => 'on',
				'checked' => ( 'on' === get_option( 'wps_tdv_enable_subscription_log', '' ) ? 'on' : 'off' ),
				'class' => 'tdv-checkbox-class',
			),
			array(
				'type'  => 'button',
				'id'    => 'wps_tdv_save_general_settings',
				'button_text' => __( 'Save Settings', 'subscriptions-for-tradingview' ),
				'class' => 'tdv-button-class',
			),
		);
		// Add general settings.
		return apply_filters( 'wps_tdv_add_general_settings_fields', $tdv_settings_general );

	}


	/**
	 * Subscriptions TradingView save tab settings.
	 *
	 * @name tdv_admin_save_tab_settings.
	 * @since 1.0.0
	 */
	public function tdv_admin_save_tab_settings() {
		global $tdv_wps_tdv_obj;
		global $wps_tdv_notices;
		if ( isset( $_POST['wps_tdv_save_general_settings'] ) && isset( $_POST['wps-tdv-general-nonce-field'] ) ) {
			$wps_tdv_geberal_nonce = sanitize_text_field( wp_unslash( $_POST['wps-tdv-general-nonce-field'] ) );
			if ( wp_verify_nonce( $wps_tdv_geberal_nonce, 'wps-tdv-general-nonce' ) ) {
				$wps_tdv_gen_flag = false;
				// General settings.
				$tdv_genaral_settings = apply_filters( 'wps_tdv_general_settings_array', array() );
				$tdv_button_index = array_search( 'submit', array_column( $tdv_genaral_settings, 'type' ) );
				if ( isset( $tdv_button_index ) && ( null == $tdv_button_index || '' == $tdv_button_index ) ) {
					$tdv_button_index = array_search( 'button', array_column( $tdv_genaral_settings, 'type' ) );
				}
				if ( isset( $tdv_button_index ) && '' !== $tdv_button_index ) {

					unset( $tdv_genaral_settings[ $tdv_button_index ] );
					if ( is_array( $tdv_genaral_settings ) && ! empty( $tdv_genaral_settings ) ) {
						foreach ( $tdv_genaral_settings as $tdv_genaral_setting ) {
							if ( isset( $tdv_genaral_setting['id'] ) && '' !== $tdv_genaral_setting['id'] ) {

								if ( isset( $_POST[ $tdv_genaral_setting['id'] ] ) && ! empty( $_POST[ $tdv_genaral_setting['id'] ] ) ) {

									$posted_value = sanitize_text_field( wp_unslash( $_POST[ $tdv_genaral_setting['id'] ] ) );
									update_option( $tdv_genaral_setting['id'], $posted_value );
								} else {
									update_option( $tdv_genaral_setting['id'], '' );
								}
							} else {
								$wps_tdv_gen_flag = true;
							}
						}
					}
					if ( $wps_tdv_gen_flag ) {
						$wps_tdv_error_text = esc_html__( 'Id of some field is missing', 'subscriptions-for-tradingview' );
						$tdv_wps_tdv_obj->wps_tdv_plug_admin_notice( $wps_tdv_error_text, 'error' );
					} else {
						$wps_tdv_notices = true;
					}
				}
			}
		}
		if ( isset( $_POST['tdv_track_button'] ) && isset( $_POST['wps-tdv-general-nonce-field'] ) ) {
			$wps_tdv_geberal_nonce = sanitize_text_field( wp_unslash( $_POST['wps-tdv-general-nonce-field'] ) );
			if ( wp_verify_nonce( $wps_tdv_geberal_nonce, 'wps-tdv-general-nonce' ) ) {

				if ( isset( $_POST['wps_tdv_enable_tracking'] ) && '' !== $_POST['wps_tdv_enable_tracking'] ) {
					$posted_value = sanitize_text_field( wp_unslash( $_POST['wps_tdv_enable_tracking'] ) );
					update_option( 'wps_tdv_enable_tracking', $posted_value );
				} else {
					update_option( 'wps_tdv_enable_tracking', '' );
				}
				$wps_tdv_notices = true;

			}
		}
	}

	/**
	 * This function is used Subscription type checkobox for simple products
	 *
	 * @name wps_tdv_create_subscription_product_type
	 * @since    1.0.0
	 * @param    Array $products_type Products type.
	 * @return   Array  $products_type.
	 */
	public function wps_tdv_create_subscription_product_type( $products_type ) {
		$products_type['wps_tdv_product'] = array(
			'id'            => '_wps_tdv_product',
			'wrapper_class' => 'show_if_simple',
			'label'         => __( 'Subscription', 'subscriptions-for-tradingview' ),
			'description'   => __( 'This is the Subscriptions type product.', 'subscriptions-for-tradingview' ),
			'default'       => 'no',
		);
		return $products_type;

	}


	/**
	 * This function is used to add subscription settings for product.
	 *
	 * @name wps_tdv_custom_product_tab_for_subscription
	 * @since    1.0.0
	 * @param    Array $tabs Products tabs array.
	 * @return   Array  $tabs
	 */
	public function wps_tdv_custom_product_tab_for_subscription( $tabs ) {
		$tabs['wps_tdv_product'] = array(
			'label'    => __( 'Subscription Settings', 'subscriptions-for-tradingview' ),
			'target'   => 'wps_tdv_product_target_section',
			// Add class for product.
			'class'    => apply_filters( 'wps_swf_settings_tabs_class', array() ),
			'priority' => 80,
		);
		// Add tb for product.
		return apply_filters( 'wps_swf_settings_tabs', $tabs );

	}



	/**
	 * This function is used to add custom fileds for subscription products.
	 *
	 * @name wps_tdv_custom_product_fields_for_subscription
	 * @since    1.0.0
	 */
	public function wps_tdv_custom_product_fields_for_subscription() {
		global $post;
		$post_id = $post->ID;
		$product = wc_get_product( $post_id );

		$wps_tdv_subscription_number = get_post_meta( $post_id, 'wps_tdv_subscription_number', true );
		if ( empty( $wps_tdv_subscription_number ) ) {
			$wps_tdv_subscription_number = 1;
		}
		$wps_tdv_subscription_interval = get_post_meta( $post_id, 'wps_tdv_subscription_interval', true );
		if ( empty( $wps_tdv_subscription_interval ) ) {
			$wps_tdv_subscription_interval = 'day';
		}

		$wps_tdv_subscription_expiry_number = get_post_meta( $post_id, 'wps_tdv_subscription_expiry_number', true );
		$wps_tdv_subscription_expiry_interval = get_post_meta( $post_id, 'wps_tdv_subscription_expiry_interval', true );
		$wps_tdv_subscription_initial_signup_price = get_post_meta( $post_id, 'wps_tdv_subscription_initial_signup_price', true );
		$wps_tdv_subscription_free_trial_number = get_post_meta( $post_id, 'wps_tdv_subscription_free_trial_number', true );
		$wps_tdv_subscription_free_trial_interval = get_post_meta( $post_id, 'wps_tdv_subscription_free_trial_interval', true );
        
        $wps_tdv_subscription_chart_ids = get_post_meta( $post_id, 'wps_tdv_subscription_chart_ids', true );
        $charts = $wps_tdv_subscription_chart_ids ?  $wps_tdv_subscription_chart_ids : [];
		?>
		<div id="wps_tdv_product_target_section" class="panel woocommerce_options_panel hidden">

		<p class="form-field wps_tdv_subscription_number_field ">
			<label for="wps_tdv_subscription_number">
			<?php esc_html_e( 'Subscriptions Per Interval', 'subscriptions-for-tradingview' ); ?>
			</label>
			<input type="number" class="short wc_input_number"  min="1" required name="wps_tdv_subscription_number" id="wps_tdv_subscription_number" value="<?php echo esc_attr( $wps_tdv_subscription_number ); ?>" placeholder="<?php esc_html_e( 'Enter subscription interval', 'subscriptions-for-tradingview' ); ?>"> 
			<select id="wps_tdv_subscription_interval" name="wps_tdv_subscription_interval" class="wps_tdv_subscription_interval" >
				<?php foreach ( wps_tdv_subscription_period() as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wps_tdv_subscription_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Choose the subscriptions time interval for the product "for example 10 days"', 'subscriptions-for-tradingview' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
		<p class="form-field wps_tdv_subscription_expiry_field ">
			<label for="wps_tdv_subscription_expiry_number">
			<?php esc_html_e( 'Subscriptions Expiry Interval', 'subscriptions-for-tradingview' ); ?>
			</label>
			<input type="number" class="short wc_input_number"  min="1" name="wps_tdv_subscription_expiry_number" id="wps_tdv_subscription_expiry_number" value="<?php echo esc_attr( $wps_tdv_subscription_expiry_number ); ?>" placeholder="<?php esc_html_e( 'Enter subscription expiry', 'subscriptions-for-tradingview' ); ?>"> 
			<select id="wps_tdv_subscription_expiry_interval" name="wps_tdv_subscription_expiry_interval" class="wps_tdv_subscription_expiry_interval" >
				<?php foreach ( wps_tdv_subscription_expiry_period( $wps_tdv_subscription_interval ) as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wps_tdv_subscription_expiry_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Choose the subscriptions expiry time interval for the product "leave empty for unlimited"', 'subscriptions-for-tradingview' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
		<p class="form-field wps_tdv_subscription_initial_signup_field ">
			<label for="wps_tdv_subscription_initial_signup_price">
			<?php
			esc_html_e( 'Initial Signup fee', 'subscriptions-for-tradingview' );
			echo esc_html( '(' . get_woocommerce_currency_symbol() . ')' );
			?>
			</label>
			<input type="number" class="short wc_input_price"  min="1" step="any" name="wps_tdv_subscription_initial_signup_price" id="wps_tdv_subscription_initial_signup_price" value="<?php echo esc_attr( $wps_tdv_subscription_initial_signup_price ); ?>" placeholder="<?php esc_html_e( 'Enter signup fee', 'subscriptions-for-tradingview' ); ?>"> 
			
		 <?php
			$description_text = __( 'Choose the subscriptions initial fee for the product "leave empty for no initial fee"', 'subscriptions-for-tradingview' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
		<p class="form-field wps_tdv_subscription_free_trial_field ">
			<label for="wps_tdv_subscription_free_trial_number">
			<?php esc_html_e( 'Free trial interval', 'subscriptions-for-tradingview' ); ?>
			</label>
			<input type="number" class="short wc_input_number"  min="1" name="wps_tdv_subscription_free_trial_number" id="wps_tdv_subscription_free_trial_number" value="<?php echo esc_attr( $wps_tdv_subscription_free_trial_number ); ?>" placeholder="<?php esc_html_e( 'Enter free trial interval', 'subscriptions-for-tradingview' ); ?>"> 
			<select id="wps_tdv_subscription_free_trial_interval" name="wps_tdv_subscription_free_trial_interval" class="wps_tdv_subscription_free_trial_interval" >
				<?php foreach ( wps_tdv_subscription_period() as $value => $label ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $wps_tdv_subscription_free_trial_interval, true ); ?>><?php echo esc_html( $label ); ?></option>
				<?php } ?>
				</select>
		 <?php
			$description_text = __( 'Choose the trial period for subscription "leave empty for no trial period"', 'subscriptions-for-tradingview' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
        
        <p class="form-field wps_tdv_subscription_chart_id_field ">
			<label for="wps_tdv_subscription_chart_ids">
			<?php esc_html_e( 'Chart Indicators', 'subscriptions-for-tradingview' ); ?>
			</label>
            <?php 

            $tv = new Subscriptions_For_TradingView_Api();
            $chart_ids = $tv->getPrivateIndicators();
            
            if ($chart_ids) {
                
            foreach ( $chart_ids as $key => $item ) { ?>
                <input name="wps_tdv_subscription_chart_ids[]" type="checkbox" id="wps_tdv_subscription_chart_id-<?php echo $key+1;?>" value="<?php echo $item['id'];?>" class="wps_tdv_subscription_chart_id mdc-switch__native-control" role="switch" aria-checked="
                    <?php
                    if ( in_array($item['id'], $charts) ) {
                        echo 'true';
                    } else {
                        echo 'false';
                    }
                    ?>"
                    <?php
                    if ( in_array($item['id'], $charts) ) {
                        echo 'checked="true"';
                    }
                    ?>
                /> <?php echo esc_html( $item['name'] ); ?></br>
                
            <?php }} ?>
		 <?php
			$description_text = __( 'Choose Indicators to assign product.', 'subscriptions-for-tradingview' );
			echo wp_kses_post( wc_help_tip( $description_text ) ); // WPCS: XSS ok.
			?>
		</p>
        
		<?php
			wp_nonce_field( 'wps_tdv_edit_nonce', 'wps_tdv_edit_nonce_filed' );
			// Add filed on product edit page.
			do_action( 'wps_tdv_product_edit_field', $post_id );
		?>
		</div>
		<?php

	}


	/**
	 * This function is used to save custom fields for subscription products.
	 *
	 * @name wps_tdv_save_custom_product_fields_data_for_subscription
	 * @since    1.0.0
	 * @param    int    $post_id Post ID.
	 * @param    object $post post.
	 */
	public function wps_tdv_save_custom_product_fields_data_for_subscription( $post_id, $post ) {

		if ( ! isset( $_POST['wps_tdv_edit_nonce_filed'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wps_tdv_edit_nonce_filed'] ) ), 'wps_tdv_edit_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		}
		$wps_tdv_product = isset( $_POST['_wps_tdv_product'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_wps_tdv_product', $wps_tdv_product );
		if ( isset( $_POST['_wps_tdv_product'] ) && ! empty( $_POST['_wps_tdv_product'] ) ) {

			$wps_tdv_subscription_number = isset( $_POST['wps_tdv_subscription_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_number'] ) ) : '';
			$wps_tdv_subscription_interval = isset( $_POST['wps_tdv_subscription_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_interval'] ) ) : '';
			$wps_tdv_subscription_expiry_number = isset( $_POST['wps_tdv_subscription_expiry_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_expiry_number'] ) ) : '';
			$wps_tdv_subscription_expiry_interval = isset( $_POST['wps_tdv_subscription_expiry_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_expiry_interval'] ) ) : '';
			$wps_tdv_subscription_initial_signup_price = isset( $_POST['wps_tdv_subscription_initial_signup_price'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_initial_signup_price'] ) ) : '';
			$wps_tdv_subscription_free_trial_number = isset( $_POST['wps_tdv_subscription_free_trial_number'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_free_trial_number'] ) ) : '';
			$wps_tdv_subscription_free_trial_interval = isset( $_POST['wps_tdv_subscription_free_trial_interval'] ) ? sanitize_text_field( wp_unslash( $_POST['wps_tdv_subscription_free_trial_interval'] ) ) : '';
            $wps_tdv_subscription_chart_ids = isset( $_POST['wps_tdv_subscription_chart_ids'] ) ? wp_unslash( $_POST['wps_tdv_subscription_chart_ids'] ) : '';

			update_post_meta( $post_id, 'wps_tdv_subscription_number', $wps_tdv_subscription_number );
			update_post_meta( $post_id, 'wps_tdv_subscription_interval', $wps_tdv_subscription_interval );
			update_post_meta( $post_id, 'wps_tdv_subscription_expiry_number', $wps_tdv_subscription_expiry_number );
			update_post_meta( $post_id, 'wps_tdv_subscription_expiry_interval', $wps_tdv_subscription_expiry_interval );
			update_post_meta( $post_id, 'wps_tdv_subscription_initial_signup_price', $wps_tdv_subscription_initial_signup_price );
			update_post_meta( $post_id, 'wps_tdv_subscription_free_trial_number', $wps_tdv_subscription_free_trial_number );
			update_post_meta( $post_id, 'wps_tdv_subscription_free_trial_interval', $wps_tdv_subscription_free_trial_interval );
            update_post_meta( $post_id, 'wps_tdv_subscription_chart_ids', $wps_tdv_subscription_chart_ids );

            
			do_action( 'wps_tdv_save_simple_subscription_field', $post_id, $_POST );
		}

	}

	/**
	 * This function is used to cancel susbcription.
	 *
	 * @name wps_tdv_admin_cancel_susbcription
	 * @since 1.0.0
	 */
	public function wps_tdv_admin_cancel_susbcription() {

		if ( isset( $_GET['wps_subscription_status_admin'] ) && isset( $_GET['wps_subscription_id'] ) && isset( $_GET['_wpnonce'] ) && ! empty( $_GET['_wpnonce'] ) ) {
			$wps_status   = sanitize_text_field( wp_unslash( $_GET['wps_subscription_status_admin'] ) );
			$wps_subscription_id = sanitize_text_field( wp_unslash( $_GET['wps_subscription_id'] ) );
			if ( wps_tdv_check_valid_subscription( $wps_subscription_id ) ) {
				// Cancel subscription.
				do_action( 'wps_tdv_subscription_cancel', $wps_subscription_id, 'Cancel' );
				$redirect_url = admin_url() . 'admin.php?page=subscriptions_for_tradingview_menu&tdv_tab=subscriptions-for-tradingview-subscriptions-table';
				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}

	/**
	 * This function is used to custom order status for susbcription.
	 *
	 * @name wps_tdv_register_new_order_statuses
	 * @param array $order_status order_status.
	 * @since 1.0.0
	 */
	public function wps_tdv_register_new_order_statuses( $order_status ) {

		$order_status['wc-wps_renewal'] = array(
			'label'                     => _x( 'TradingView Renewal', 'Order status', 'subscriptions-for-tradingview' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			/* translators: %s: number of orders */
			'label_count'               => _n_noop( 'TradingView Renewal <span class="count">(%s)</span>', 'TradingView Renewal <span class="count">(%s)</span>', 'subscriptions-for-tradingview' ),
		);
		return $order_status;
	}

	/**
	 * This function is used to custom order status for susbcription.
	 *
	 * @name wps_tdv_new_wc_order_statuses.
	 * @since 1.0.0
	 * @param array $order_statuses order_statuses.
	 */
	public function wps_tdv_new_wc_order_statuses( $order_statuses ) {
		$order_statuses['wc-wps_renewal'] = _x( 'TradingView Renewal', 'Order status', 'subscriptions-for-tradingview' );

		return $order_statuses;
	}
    
    function wps_tdv_admin_save_order_meta( $order_id ){
        
        $tradingview_id = get_post_meta( $order_id, 'tradingview_id', true );
        $new_tradingview_id = wc_clean( $_POST[ 'tradingview_id' ] );
        if ($new_tradingview_id && $new_tradingview_id != $tradingview_id) {
            update_post_meta( $order_id, 'tradingview_id', $new_tradingview_id);
            
            $wps_subscription_id = get_post_meta( $order_id, 'wps_subscription_id', true );
            update_post_meta( $wps_subscription_id, 'tradingview_id', $new_tradingview_id);
            
            wps_tdv_remove_account_tradingview($wps_subscription_id, $tradingview_id);
            
            wps_tdv_add_account_tradingview($wps_subscription_id, $new_tradingview_id);
            
            $order = wc_get_order( $order_id );
            $user_id = $order->get_user_id();
            if (get_user_meta( $user_id, 'tradingview_id', true )) {
                update_user_meta( $user_id, 'tradingview_id', $new_tradingview_id );
            } else {
                add_user_meta( $user_id, 'tradingview_id', $new_tradingview_id );
            }
        }
    }

    public function wps_tdv_admin_order_data_after_billing_address($order)
    {
        $tradingview_id = get_post_meta( $order->get_id(), 'tradingview_id', true );
        ?>
          <div class="address">
              <p<?php if( ! $tradingview_id ) { echo ' class="none_set"'; } ?>>
                  <strong>TradingView UserName:</strong>
                  <?php echo $tradingview_id ? esc_html( $tradingview_id ) : '' ?>
              </p>
          </div>
          <div class="edit_address">
              <?php

                  woocommerce_wp_text_input(
                  array(
                      'id'            => 'tradingview_id',
                      'value'         => $tradingview_id,
                      'label'         => __( 'TradingView UserName', 'woocommerce' ),
                      'placeholder'   => '',
                      'desc_tip'      => 'true',
                      'wrapper_class' => 'form-field-wide',
                      'custom_attributes' => array( 'required' => 'required' ),
                  )
              );
              ?>
          </div>
      <?php
    }

	/**
	 * This function is used to custom field compatibility with WPML.
	 *
	 * @name wps_tdv_add_lock_custom_fields_ids.
	 * @since 1.0.3
	 * @param array $ids ids.
	 */
	public function wps_tdv_add_lock_custom_fields_ids( $ids ) {

		$ids[] = '_wps_tdv_product';
		$ids[] = 'wps_tdv_subscription_number';
		$ids[] = 'wps_tdv_subscription_interval';
		$ids[] = 'wps_tdv_subscription_expiry_number';
		$ids[] = 'wps_tdv_subscription_expiry_interval';
		$ids[] = 'wps_tdv_subscription_initial_signup_price';
		$ids[] = 'wps_tdv_subscription_free_trial_number';
		$ids[] = 'wps_tdv_subscription_free_trial_interval';

		return apply_filters( 'wps_tdv_add_lock_fields_ids_pro', $ids );
	}

	/**
	 * Update the option for settings from the multistep form.
	 *
	 * @name wps_tdv_save_settings_filter
	 * @since 1.0.0
	 */
	public function wps_tdv_save_settings_filter() {

		check_ajax_referer( 'ajax-nonce', 'nonce' );

		$term_accpted = ! empty( $_POST['consetCheck'] ) ? sanitize_text_field( wp_unslash( $_POST['consetCheck'] ) ) : ' ';
		if ( ! empty( $term_accpted ) && 'yes' == $term_accpted ) {
			update_option( 'wps_tdv_enable_tracking', 'on' );
		}

		// settings fields.
		$enable_plugin = ! empty( $_POST['EnablePlugin'] ) ? sanitize_text_field( wp_unslash( $_POST['EnablePlugin'] ) ) : '';
		$add_to_cart_text = ! empty( $_POST['AddToCartText'] ) ? sanitize_text_field( wp_unslash( $_POST['AddToCartText'] ) ) : '';
		$token = ! empty( $_POST['Token'] ) ? sanitize_text_field( wp_unslash( $_POST['Token'] ) ) : '';
		
        $place_order_text = ! empty( $_POST['PlaceOrderText'] ) ? sanitize_text_field( wp_unslash( $_POST['PlaceOrderText'] ) ) : '';

		$product_name = ! empty( $_POST['ProductName'] ) ? sanitize_text_field( wp_unslash( $_POST['ProductName'] ) ) : 'Subscription';
		$product_description = ! empty( $_POST['ProductDescription'] ) ? sanitize_text_field( wp_unslash( $_POST['ProductDescription'] ) ) : 'This is Subscription';
		$short_description = ! empty( $_POST['ProductShortDescription'] ) ? sanitize_text_field( wp_unslash( $_POST['ProductShortDescription'] ) ) : 'This is Subscription Product';

		$product_price = ! empty( $_POST['ProductPrice'] ) ? sanitize_text_field( wp_unslash( $_POST['ProductPrice'] ) ) : '';

		$subscription_number = ! empty( $_POST['SubscriptionNumber'] ) ? sanitize_text_field( wp_unslash( $_POST['SubscriptionNumber'] ) ) : '';

		$subscription_interval = ! empty( $_POST['SubscriptionInterval'] ) ? sanitize_text_field( wp_unslash( $_POST['SubscriptionInterval'] ) ) : '';

		// Update settings.
		if ( 'true' == $enable_plugin ) {
			update_option( 'wps_tdv_enable_plugin', 'on' );
			update_option( 'wps_tdv_add_to_cart_text ', $add_to_cart_text );
            update_option( 'wps_tdv_token', $token );

			update_option( 'wps_tdv_place_order_button_text ', $place_order_text );
		}

		$allready_created = get_option( 'wps_tdv_multistep_product_create_done', 'no' );
		// Create products.
		if ( $enable_plugin && 'no' == $allready_created ) {
			$post_id = wp_insert_post(
				array(
					'post_title' => $product_name,
					'post_type' => 'product',
					'post_content' => $product_description,
					'post_excerpt' => $short_description,
					'post_status' => 'publish',
				)
			);

			wp_set_object_terms( $post_id, 'simple', 'product_type' );
			update_post_meta( $post_id, '_visibility', 'visible' );
			update_post_meta( $post_id, '_stock_status', 'instock' );

			update_post_meta( $post_id, '_wps_tdv_product', 'yes' );
			update_post_meta( $post_id, 'wps_tdv_subscription_number', $subscription_number );
			update_post_meta( $post_id, 'wps_tdv_subscription_interval', $subscription_interval );

			update_post_meta( $post_id, '_regular_price', $product_price );
			update_post_meta( $post_id, '_sale_price', '' );
			update_post_meta( $post_id, '_price', $product_price );
			$product = wc_get_product( $post_id );

			$product->save();
			update_option( 'wps_tdv_multistep_product_create_done', 'yes' );
		}

		if ( isset( $_POST['EnableWpsPaypal'] ) ) {
			$wps_paypal_settings = get_option( 'woocommerce_wps_paypal_settings', array() );
			$wps_paypal_settings['enabled']  = ! empty( $_POST['EnableWpsPaypal'] ) ? 'yes' : 'no';
			$wps_paypal_settings['testmode'] = ! empty( $_POST['EnableWpsPaypalTestmode'] ) ? 'yes' : 'no';

			$wps_paypal_settings['client_id']     = ! empty( $_POST['WpsPaypalClientId'] ) ? sanitize_text_field( wp_unslash( $_POST['WpsPaypalClientId'] ) ) : '';
			$wps_paypal_settings['client_secret'] = ! empty( $_POST['WpsPaypalClientSecret'] ) ? sanitize_text_field( wp_unslash( $_POST['WpsPaypalClientSecret'] ) ) : '';

			update_option( 'woocommerce_wps_paypal_settings', $wps_paypal_settings );
		}
		update_option( 'wps_tdv_multistep_done', 'yes' );

		wp_send_json( 'yes' );
	}

	/**
	 * Update the option for settings from the multistep form.
	 *
	 * @name wps_tdv_save_settings_filter
	 * @since 1.0.0
	 */
	public function wps_tdv_install_plugin_configuration() {
		check_ajax_referer( 'ajax-nonce', 'nonce' );
		$wps_plugin_name = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';
		$response = false;
		if ( ! empty( $wps_plugin_name ) ) {
			$wps_plugin_file_path = $wps_plugin_name . '/' . $wps_plugin_name . '.php';

			if ( file_exists( WP_PLUGIN_DIR . '/' . $wps_plugin_file_path ) && ! is_plugin_active( $wps_plugin_file_path ) ) {
				activate_plugin( $wps_plugin_file_path );
				$response = true;
			} else {

				include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
				include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

				$wps_plugin_api    = plugins_api(
					'plugin_information',
					array(
						'slug' => $wps_plugin_name,
						'fields' => array( 'sections' => false ),
					)
				);
				if ( isset( $wps_plugin_api->download_link ) ) {
					$wps_ajax_obj = new WP_Ajax_Upgrader_Skin();
					$wps_obj = new Plugin_Upgrader( $wps_ajax_obj );
					$wps_install = $wps_obj->install( $wps_plugin_api->download_link );
					activate_plugin( $wps_plugin_file_path );
					$response = true;
				}
			}
		}
		wp_send_json( $response );

	}
    
    public function wps_tdv_add_new_user_column( $columns ) {
        $columns['tradingview_id'] = 'TradingView UserName';
        return $columns;
    }

    public function wps_tdv_add_new_user_column_content( $content, $column, $user_id ) {

        if ( 'tradingview_id' === $column ) {
            $content = get_the_author_meta( 'tradingview_id', $user_id );
        }

        return $content;
    }

	/**
	 * Developer_admin_hooks_listing
	 *
	 * @name wps_developer_admin_hooks_listing
	 */
	public function wps_developer_admin_hooks_listing() {
		$admin_hooks = array();
		$val         = self::wps_developer_hooks_function( SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/' );
		if ( ! empty( $val['hooks'] ) ) {
			$admin_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::wps_developer_hooks_function( SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$admin_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}

		return $admin_hooks;
	}

	/**
	 * Developer_public_hooks_listing
	 */
	public function wps_developer_public_hooks_listing() {

		$public_hooks = array();
		$val          = self::wps_developer_hooks_function( SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'public/' );

		if ( ! empty( $val['hooks'] ) ) {
			$public_hooks[] = $val['hooks'];
			unset( $val['hooks'] );
		}
		$data = array();
		foreach ( $val['files'] as $v ) {
			if ( 'css' !== $v && 'js' !== $v && 'images' !== $v ) {
				$helo = self::wps_developer_hooks_function( SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'public/' . $v . '/' );
				if ( ! empty( $helo['hooks'] ) ) {
					$public_hooks[] = $helo['hooks'];
					unset( $helo['hooks'] );
				}
				if ( ! empty( $helo ) ) {
					$data[] = $helo;
				}
			}
		}
		return $public_hooks;
	}

	/**
	 * Developer_hooks_function.
	 *
	 * @name wps_developer_hooks_function.
	 * @param string $path Path of the file.
	 */
	public function wps_developer_hooks_function( $path ) {
		$all_hooks = array();
		$scan      = scandir( $path );
		$response  = array();
		foreach ( $scan as $file ) {
			if ( strpos( $file, '.php' ) ) {
				$myfile = file( $path . $file );
				foreach ( $myfile as $key => $lines ) {
					if ( preg_match( '/do_action/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['action_hook'] = $lines;
						$all_hooks[ $key ]['desc']        = $myfile[ $key - 1 ];
					}
					if ( preg_match( '/apply_filters/i', $lines ) && ! strpos( $lines, 'str_replace' ) && ! strpos( $lines, 'preg_match' ) ) {
						$all_hooks[ $key ]['filter_hook'] = $lines;
						$all_hooks[ $key ]['desc']        = $myfile[ $key - 1 ];
					}
				}
			} elseif ( strpos( $file, '.' ) == '' && strpos( $file, '.' ) !== 0 ) {
				$response['files'][] = $file;
			}
		}
		if ( ! empty( $all_hooks ) ) {
			$response['hooks'] = $all_hooks;
		}
		return $response;
	}
	/**
	 * Get Count
	 *
	 * @param string  $status .
	 * @param string  $action .
	 * @param boolean $type .
	 * @return $result .
	 */
	public function wps_tdv_get_count( $status = 'all', $action = 'count', $type = false ) {
		return 0;
	}
}

