<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/includes
 * @author     Donald<donald.nguyen.it@gmail.com>
 */
class Subscriptions_For_TradingView {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Subscriptions_For_TradingView_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $tdv_onboard    To initializsed the object of class onboard.
	 */
	protected $tdv_onboard;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_VERSION' ) ) {
			$this->version = SUBSCRIPTIONS_FOR_TRADINGVIEW_VERSION;
		} else {

			$this->version = '1.4.3';
		}

		$this->plugin_name = 'subscriptions-for-tradingview';

		$this->subscriptions_for_tradingview_dependencies();
		$this->subscriptions_for_tradingview_locale();
		if ( is_admin() ) {
			$this->subscriptions_for_tradingview_admin_hooks();
		}
		$this->subscriptions_for_tradingview_public_hooks();

		$this->subscriptions_for_tradingview_api_hooks();
		$this->init();
		$this->wps_tdv_init_payment_integration();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Subscriptions_For_TradingView_Loader. Orchestrates the hooks of the plugin.
	 * - Subscriptions_For_TradingView_i18n. Defines internationalization functionality.
	 * - Subscriptions_For_TradingView_Admin. Defines all hooks for the admin area.
	 * - Subscriptions_For_TradingView_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function subscriptions_for_tradingview_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-i18n.php';

		if ( is_admin() ) {

			// The class responsible for defining all actions that occur in the admin area.
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-subscriptions-for-tradingview-admin.php';

			// The class responsible for on-boarding steps for plugin.
			if ( is_dir( plugin_dir_path( dirname( __FILE__ ) ) . 'onboarding' ) && ! class_exists( 'Subscriptions_For_TradingView_Onboarding_Steps' ) ) {

				require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-onboarding-steps.php';
			}

			if ( class_exists( 'Subscriptions_For_TradingView_Onboarding_Steps' ) ) {
				$tdv_onboard_steps = new Subscriptions_For_TradingView_Onboarding_Steps();
			}
		}

		// The class responsible for defining all actions that occur in the public-facing side of the site.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-subscriptions-for-tradingview-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'package/rest-api/class-subscriptions-for-tradingview-rest-api.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/subscriptions-for-tradingview-common-function.php';

		$this->loader = new Subscriptions_For_TradingView_Loader();

		/**
		 * Include the log file.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-log.php';
		/**
		 * Include the cron file.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-scheduler.php';
        
        /**
		 * Include the tradingview api file.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-subscriptions-for-tradingview-api.php';
        

	}
	/**
	 * The function is used to include email class.
	 */
	public function init() {
		add_filter( 'woocommerce_email_classes', array( $this, 'wps_tdv_woocommerce_email_classes' ) );
	}

	/**
	 * The function is used to include payment gateway integration.
	 */
	public function wps_tdv_init_payment_integration() {

		$wps_tdv_dir = plugin_dir_path( dirname( __FILE__ ) ) . 'package/gateways';
		wps_tdv_include_process_directory( $wps_tdv_dir );
		do_action( 'wps_tdv_payment_integration' );
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Subscriptions_For_TradingView_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function subscriptions_for_tradingview_locale() {

		$plugin_i18n = new Subscriptions_For_TradingView_I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function subscriptions_for_tradingview_admin_hooks() {

		$tdv_plugin_admin = new Subscriptions_For_TradingView_Admin( $this->tdv_get_plugin_name(), $this->tdv_get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $tdv_plugin_admin, 'wps_tdv_admin_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $tdv_plugin_admin, 'wps_tdv_admin_enqueue_scripts' );

		// Add settings menu for Subscriptions TradingView.
		$this->loader->add_action( 'admin_menu', $tdv_plugin_admin, 'wps_tdv_options_page' );
		$this->loader->add_action( 'admin_menu', $tdv_plugin_admin, 'wps_tdv_remove_default_submenu', 50 );

		// All admin actions and filters after License Validation goes here.
		$this->loader->add_filter( 'wps_add_plugins_menus_array', $tdv_plugin_admin, 'wps_tdv_admin_submenu_page', 15 );

		$this->loader->add_filter( 'wps_tdv_general_settings_array', $tdv_plugin_admin, 'wps_tdv_admin_general_settings_page', 10 );

		// Saving tab settings.
		$this->loader->add_action( 'admin_init', $tdv_plugin_admin, 'tdv_admin_save_tab_settings' );
		// Multistep.
		$this->loader->add_action( 'wp_ajax_wps_tdv_save_settings_filter', $tdv_plugin_admin, 'wps_tdv_save_settings_filter' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_tdv_save_settings_filter', $tdv_plugin_admin, 'wps_tdv_save_settings_filter' );

		$this->loader->add_action( 'wp_ajax_wps_tdv_install_plugin_configuration', $tdv_plugin_admin, 'wps_tdv_install_plugin_configuration' );
		$this->loader->add_action( 'wp_ajax_nopriv_wps_tdv_install_plugin_configuration', $tdv_plugin_admin, 'wps_tdv_install_plugin_configuration' );
		// Developer's Hook Listing.
		$this->loader->add_action( 'tdv_developer_admin_hooks_array', $tdv_plugin_admin, 'wps_developer_admin_hooks_listing' );
		$this->loader->add_action( 'tdv_developer_public_hooks_array', $tdv_plugin_admin, 'wps_developer_public_hooks_listing' );

		if ( wps_tdv_check_plugin_enable() ) {
			$this->loader->add_action( 'product_type_options', $tdv_plugin_admin, 'wps_tdv_create_subscription_product_type' );

			$this->loader->add_filter( 'woocommerce_product_data_tabs', $tdv_plugin_admin, 'wps_tdv_custom_product_tab_for_subscription' );

			$this->loader->add_action( 'woocommerce_product_data_panels', $tdv_plugin_admin, 'wps_tdv_custom_product_fields_for_subscription' );

			$this->loader->add_action( 'woocommerce_process_product_meta', $tdv_plugin_admin, 'wps_tdv_save_custom_product_fields_data_for_subscription', 10, 2 );

			$this->loader->add_action( 'init', $tdv_plugin_admin, 'wps_tdv_admin_cancel_susbcription' );

			$this->loader->add_filter( 'woocommerce_register_shop_order_post_statuses', $tdv_plugin_admin, 'wps_tdv_register_new_order_statuses' );

			$this->loader->add_filter( 'wc_order_statuses', $tdv_plugin_admin, 'wps_tdv_new_wc_order_statuses' );
			// WPLM Translation.
			$this->loader->add_filter( 'wcml_js_lock_fields_ids', $tdv_plugin_admin, 'wps_tdv_add_lock_custom_fields_ids' );

            $this->loader->add_filter( 'woocommerce_admin_order_data_after_billing_address', $tdv_plugin_admin, 'wps_tdv_admin_order_data_after_billing_address', 10, 1 );
            
            $this->loader->add_filter( 'woocommerce_process_shop_order_meta', $tdv_plugin_admin, 'wps_tdv_admin_save_order_meta', 10, 1 );

            $this->loader->add_filter( 'manage_users_columns', $tdv_plugin_admin, 'wps_tdv_add_new_user_column');

            $this->loader->add_filter( 'manage_users_custom_column', $tdv_plugin_admin, 'wps_tdv_add_new_user_column_content', 15, 3 );

            
		}

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function subscriptions_for_tradingview_public_hooks() {

		$tdv_plugin_public = new Subscriptions_For_TradingView_Public( $this->tdv_get_plugin_name(), $this->tdv_get_version() );

		if ( wps_tdv_check_plugin_enable() ) {
			$this->loader->add_action( 'wp_enqueue_scripts', $tdv_plugin_public, 'wps_tdv_public_enqueue_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $tdv_plugin_public, 'wps_tdv_public_enqueue_scripts' );

			$this->loader->add_filter( 'woocommerce_get_price_html', $tdv_plugin_public, 'wps_tdv_price_html_subscription_product', 10, 2 );
			$this->loader->add_filter( 'woocommerce_product_single_add_to_cart_text', $tdv_plugin_public, 'wps_tdv_product_add_to_cart_text', 10, 2 );
			$this->loader->add_filter( 'woocommerce_product_add_to_cart_text', $tdv_plugin_public, 'wps_tdv_product_add_to_cart_text', 10, 2 );
			$this->loader->add_filter( 'woocommerce_order_button_text', $tdv_plugin_public, 'wps_tdv_woocommerce_order_button_text' );

			$this->loader->add_filter( 'woocommerce_cart_item_price', $tdv_plugin_public, 'wps_tdv_show_subscription_price_on_cart', 10, 3 );

			$this->loader->add_action( 'woocommerce_before_calculate_totals', $tdv_plugin_public, 'wps_tdv_add_subscription_price_and_sigup_fee' );

			$this->loader->add_action( 'woocommerce_checkout_order_processed', $tdv_plugin_public, 'wps_tdv_process_checkout', 99, 2 );

			$this->loader->add_action( 'woocommerce_available_payment_gateways', $tdv_plugin_public, 'wps_tdv_unset_offline_payment_gateway_for_subscription' );

			$this->loader->add_action( 'init', $tdv_plugin_public, 'wps_tdv_add_subscription_tab_on_myaccount_page' );

			$this->loader->add_filter( 'query_vars', $tdv_plugin_public, 'wps_tdv_custom_endpoint_query_vars' );
			$this->loader->add_filter( 'woocommerce_account_menu_items', $tdv_plugin_public, 'wps_tdv_add_subscription_dashboard_on_myaccount_page' );

			$this->loader->add_action( 'woocommerce_account_wps_subscriptions_endpoint', $tdv_plugin_public, 'wps_tdv_subscription_dashboard_content' );

			$this->loader->add_action( 'woocommerce_before_checkout_form', $tdv_plugin_public, 'wps_tdv_subscription_before_checkout_form' );

			$this->loader->add_action( 'wps_tdv_display_susbcription_recerring_total_account_page', $tdv_plugin_public, 'wps_tdv_display_susbcription_recerring_total_account_page_callback' );

			$this->loader->add_action( 'woocommerce_account_show-subscription_endpoint', $tdv_plugin_public, 'wps_tdv_show_subscription_details' );

            $this->loader->add_action( 'woocommerce_order_details_after_customer_details', $tdv_plugin_public, 'wps_tdv_woocommerce_customer_details');
            $this->loader->add_action( 'woocommerce_order_item_meta_start', $tdv_plugin_public, 'wps_tdv_woocommerce_order_item_meta_start', 10, 4 );

            
			$this->loader->add_action( 'init', $tdv_plugin_public, 'wps_tdv_cancel_susbcription' );

			$this->loader->add_action( 'woocommerce_order_status_changed', $tdv_plugin_public, 'wps_tdv_woocommerce_order_status_changed', 99, 3 );

			$this->loader->add_action( 'after_woocommerce_pay', $tdv_plugin_public, 'wps_tdv_after_woocommerce_pay', 100 );

			$this->loader->add_action( 'wp_loaded', $tdv_plugin_public, 'wps_tdv_change_payment_method_form', 20 );

            $this->loader->add_action( 'wps_tdv_expire_subscription_scheduler', $tdv_plugin_public, 'wps_tdv_expire_tradingview ' );
            $this->loader->add_action( 'wps_tdv_cancel_susbcription', $tdv_plugin_public, 'wps_tdv_cancel_tradingview ', 10, 2 );
            
            
			$this->loader->add_filter( 'woocommerce_order_get_total', $tdv_plugin_public, 'wps_tdv_set_susbcription_total', 11, 2 );
			$this->loader->add_filter( 'woocommerce_is_sold_individually', $tdv_plugin_public, 'wps_tdv_hide_quantity_fields_for_subscription', 10, 2 );

			$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $tdv_plugin_public, 'wps_tdv_woocommerce_add_to_cart_validation', 10, 5 );

			$this->loader->add_filter( 'woocommerce_cart_needs_payment', $tdv_plugin_public, 'wps_tdv_woocommerce_cart_needs_payment', 99, 2 );

			$this->loader->add_action( 'woocommerce_order_status_changed', $tdv_plugin_public, 'wps_tdv__cancel_subs_woocommerce_order_status_changed', 150, 3 );

			$this->loader->add_filter( 'woocommerce_checkout_registration_required', $tdv_plugin_public, 'wps_tdv_registration_required', 900 );

			$this->loader->add_filter( 'woocommerce_gateway_description', $tdv_plugin_public, 'wps_tdv_change_payment_gateway_description', 10, 2 );

			$this->loader->add_filter( 'woocommerce_checkout_fields', $tdv_plugin_public, 'wps_tdv_remove_checkout_fields', 10, 1 );
            
            $this->loader->add_filter( 'woocommerce_review_order_before_payment', $tdv_plugin_public, 'wps_tdv_add_tradingview_id_field', 10, 1);

            $this->loader->add_filter( 'woocommerce_checkout_update_order_meta', $tdv_plugin_public, 'wps_tdv_save_tradingview_id', 10, 1 );
            
            $this->loader->add_filter( 'woocommerce_after_checkout_validation', $tdv_plugin_public, 'wps_tdv_woocommerce_after_checkout_validation', 10, 2 );
            
            
            $this->loader->add_filter( 'woocommerce_cart_calculate_fees', $tdv_plugin_public, 'wps_tdv_woocommerce_checkout_fee', 10, 1 );
            
            $this->loader->add_action( 'woocommerce_checkout_create_order', $tdv_plugin_public, 'wps_tdv_woocommerce_checkout_change_fee_to_order', 10, 2 );
            
            //$this->loader->add_action( 'woocommerce_review_order_before_payment', $tdv_plugin_public, 'wps_tdv_woocommerce_checkout_refresh_payment_method' );
    
            
//            $this->loader->add_filter( 'woocommerce_checkout_update_order_review', $tdv_plugin_public, 'wps_tdv_woocommerce_checkout_tradingview_set_session', 10, 1 );

            
			$this->loader->add_filter( 'woocommerce_enable_order_notes_field', $tdv_plugin_public, 'wps_tdv_remove_order_notes_field', 9999 );

			$this->loader->add_filter( 'wps_tdv_created_subscription', $tdv_plugin_public, 'wps_tdv_add_account_tradingview_into_chart', 10, 2 );
            $this->loader->add_filter( 'wps_tdv_renewal_subscription', $tdv_plugin_public, 'wps_tdv_modify_account_tradingview_into_chart', 10, 2 );
            
            $this->loader->add_filter( 'woocommerce_order_formatted_line_subtotal', $tdv_plugin_public, 'wps_tdv_order_formatted_line_subtotal', 10, 3 );
            
            //$this->loader->add_filter( 'woocommerce_cart_item_subtotal', $tdv_plugin_public, 'wps_tdv_change_subtotal_price_in_cart_html', 99, 3 );
            
            //$this->loader->add_filter( 'woocommerce_cart_item_name', $tdv_plugin_public, 'wps_tdv_woocommerce_cart_item_name', 10, 3 );
            
            //$this->loader->add_filter( 'woocommerce_cart_totals_after_order_total', $tdv_plugin_public, 'wps_tdv_cart_recurring_totals', 10 );
			//$this->loader->add_filter( 'woocommerce_review_order_after_order_total', $tdv_plugin_public, 'wps_tdv_cart_recurring_totals', 10 );

            //$this->loader->add_filter( 'woocommerce_cart_item_price', $tdv_plugin_public, 'wps_tdv_cart_change_price_in_cart_html', 99, 3 );
			
		}
	}

	/**
	 * The function include email class.
	 *
	 * @name wps_tdv_woocommerce_email_classes.
	 * @since 1.0.0
	 * @param Array $emails emails.
	 */
	public function wps_tdv_woocommerce_email_classes( $emails ) {
		$emails['wps_tdv_cancel_subscription'] = require_once plugin_dir_path( dirname( __FILE__ ) ) . 'emails/class-subscriptions-for-tradingview-cancel-subscription-email.php';
		$emails['wps_tdv_expired_subscription'] = require_once plugin_dir_path( dirname( __FILE__ ) ) . 'emails/class-subscriptions-for-tradingview-expired-subscription-email.php';

		return apply_filters( 'wps_tdv_email_classes', $emails );
	}
	/**
	 * Register all of the hooks related to the api functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function subscriptions_for_tradingview_api_hooks() {

		$tdv_plugin_api = new Subscriptions_For_TradingView_Rest_Api( $this->tdv_get_plugin_name(), $this->tdv_get_version() );

		$this->loader->add_action( 'rest_api_init', $tdv_plugin_api, 'wps_tdv_add_endpoint' );

	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function tdv_run() {
		$this->loader->tdv_run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function tdv_get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Subscriptions_For_TradingView_Loader    Orchestrates the hooks of the plugin.
	 */
	public function tdv_get_loader() {
		return $this->loader;
	}


	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Subscriptions_For_TradingView_Onboard    Orchestrates the hooks of the plugin.
	 */
	public function tdv_get_onboard() {
		return $this->tdv_onboard;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function tdv_get_version() {
		return $this->version;
	}

	/**
	 * Predefined default wps_tdv_plug tabs.
	 *
	 * @return  Array       An key=>value pair of Subscriptions TradingView tabs.
	 */
	public function wps_tdv_plug_default_tabs() {

		$tdv_default_tabs = array();
        $tdv_default_tabs['subscriptions-for-tradingview-subscriptions-table'] = array(
			'title'       => esc_html__( 'Subscription Table', 'subscriptions-for-tradingview' ),
			'name'        => 'subscriptions-for-tradingview-subscriptions-table',
			'file_path'        => SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH,
		);
		
		$tdv_default_tabs['subscriptions-for-tradingview-general'] = array(
			'title'       => esc_html__( 'General Setting', 'subscriptions-for-tradingview' ),
			'name'        => 'subscriptions-for-tradingview-general',
			'file_path'        => SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH,
		);
		$tdv_default_tabs = apply_filters( 'wps_tdv_tdv_plugin_standard_admin_settings_tabs', $tdv_default_tabs );

		
		return $tdv_default_tabs;
	}

	/**
	 * Locate and load appropriate tempate.
	 *
	 * @since   1.0.0
	 * @param string $content_path content_path file for inclusion.
	 */
	public function wps_tdv_plug_load_template( $content_path ) {

		if ( file_exists( $content_path ) ) {

			include $content_path;
		} else {

			/* translators: %s: file path */
			$tdv_notice = sprintf( esc_html__( 'Unable to locate file at location "%s". Some features may not work properly in this plugin. Please contact us!', 'subscriptions-for-tradingview' ), $content_path );
			$this->wps_tdv_plug_admin_notice( $tdv_notice, 'error' );
		}
	}

	/**
	 * Show admin notices.
	 *
	 * @param  string $tdv_message    Message to display.
	 * @param  string $type       notice type, accepted values - error/update/update-nag.
	 * @since  1.0.0
	 */
	public static function wps_tdv_plug_admin_notice( $tdv_message, $type = 'error' ) {

		$tdv_classes = 'notice ';

		switch ( $type ) {

			case 'update':
				$tdv_classes .= 'updated is-dismissible';
				break;

			case 'update-nag':
				$tdv_classes .= 'update-nag is-dismissible';
				break;

			case 'success':
				$tdv_classes .= 'notice-success is-dismissible';
				break;

			default:
				$tdv_classes .= 'notice-error is-dismissible';
		}

		$tdv_notice  = '<div class="' . esc_attr( $tdv_classes ) . ' wps-errorr-8">';
		$tdv_notice .= '<p>' . esc_html( $tdv_message ) . '</p>';
		$tdv_notice .= '</div>';

		echo wp_kses_post( $tdv_notice );
	}


	/**
	 * Show wordpress and server info.
	 *
	 * @return  Array $tdv_system_data       returns array of all wordpress and server related information.
	 * @since  1.0.0
	 */
	public function wps_tdv_plug_system_status() {
		global $wpdb;
		$tdv_system_status = array();
		$tdv_wordpress_status = array();
		$tdv_system_data = array();

		// Get the web server.
		$tdv_system_status['web_server'] = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';

		// Get PHP version.
		$tdv_system_status['php_version'] = function_exists( 'phpversion' ) ? phpversion() : __( 'N/A (phpversion function does not exist)', 'subscriptions-for-tradingview' );

		// Get the server's IP address.
		$tdv_system_status['server_ip'] = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : '';

		// Get the server's port.
		$tdv_system_status['server_port'] = isset( $_SERVER['SERVER_PORT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ) : '';

		// Get the uptime.
		$tdv_system_status['uptime'] = function_exists( 'exec' ) ? @exec( 'uptime -p' ) : __( 'N/A (make sure exec function is enabled)', 'subscriptions-for-tradingview' );

		// Get the server path.
		$tdv_system_status['server_path'] = defined( 'ABSPATH' ) ? ABSPATH : __( 'N/A (ABSPATH constant not defined)', 'subscriptions-for-tradingview' );

		// Get the OS.
		$tdv_system_status['os'] = function_exists( 'php_uname' ) ? php_uname( 's' ) : __( 'N/A (php_uname function does not exist)', 'subscriptions-for-tradingview' );

		// Get WordPress version.
		$tdv_wordpress_status['wp_version'] = function_exists( 'get_bloginfo' ) ? get_bloginfo( 'version' ) : __( 'N/A (get_bloginfo function does not exist)', 'subscriptions-for-tradingview' );

		// Get and count active WordPress plugins.
		$tdv_wordpress_status['wp_active_plugins'] = function_exists( 'get_option' ) ? count( get_option( 'active_plugins' ) ) : __( 'N/A (get_option function does not exist)', 'subscriptions-for-tradingview' );

		// See if this site is multisite or not.
		$tdv_wordpress_status['wp_multisite'] = function_exists( 'is_multisite' ) && is_multisite() ? __( 'Yes', 'subscriptions-for-tradingview' ) : __( 'No', 'subscriptions-for-tradingview' );

		// See if WP Debug is enabled.
		$tdv_wordpress_status['wp_debug_enabled'] = defined( 'WP_DEBUG' ) ? __( 'Yes', 'subscriptions-for-tradingview' ) : __( 'No', 'subscriptions-for-tradingview' );

		// See if WP Cache is enabled.
		$tdv_wordpress_status['wp_cache_enabled'] = defined( 'WP_CACHE' ) ? __( 'Yes', 'subscriptions-for-tradingview' ) : __( 'No', 'subscriptions-for-tradingview' );

		// Get the total number of WordPress users on the site.
		$tdv_wordpress_status['wp_users'] = function_exists( 'count_users' ) ? count_users() : __( 'N/A (count_users function does not exist)', 'subscriptions-for-tradingview' );

		// Get the number of published WordPress posts.
		$tdv_wordpress_status['wp_posts'] = wp_count_posts()->publish >= 1 ? wp_count_posts()->publish : 0;

		// Get PHP memory limit.
		$tdv_system_status['php_memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'subscriptions-for-tradingview' );

		// Get the PHP error log path.
		$tdv_system_status['php_error_log_path'] = ! ini_get( 'error_log' ) ? __( 'N/A', 'subscriptions-for-tradingview' ) : ini_get( 'error_log' );

		// Get PHP max upload size.
		$tdv_system_status['php_max_upload'] = function_exists( 'ini_get' ) ? (int) ini_get( 'upload_max_filesize' ) : __( 'N/A (ini_get function does not exist)', 'subscriptions-for-tradingview' );

		// Get PHP max post size.
		$tdv_system_status['php_max_post'] = function_exists( 'ini_get' ) ? (int) ini_get( 'post_max_size' ) : __( 'N/A (ini_get function does not exist)', 'subscriptions-for-tradingview' );

		// Get the PHP architecture.
		if ( PHP_INT_SIZE == 4 ) {
			$tdv_system_status['php_architecture'] = '32-bit';
		} elseif ( PHP_INT_SIZE == 8 ) {
			$tdv_system_status['php_architecture'] = '64-bit';
		} else {
			$tdv_system_status['php_architecture'] = 'N/A';
		}

		// Get server host name.
		$tdv_system_status['server_hostname'] = function_exists( 'gethostname' ) ? gethostname() : __( 'N/A (gethostname function does not exist)', 'subscriptions-for-tradingview' );

		// Show the number of processes currently running on the server.
		$tdv_system_status['processes'] = function_exists( 'exec' ) ? @exec( 'ps aux | wc -l' ) : __( 'N/A (make sure exec is enabled)', 'subscriptions-for-tradingview' );

		// Get the memory usage.
		$tdv_system_status['memory_usage'] = function_exists( 'memory_get_peak_usage' ) ? round( memory_get_peak_usage( true ) / 1024 / 1024, 2 ) : 0;

		// Get CPU usage.
		// Check to see if system is Windows, if so then use an alternative since sys_getloadavg() won't work.
		if ( stristr( PHP_OS, 'win' ) ) {
			$tdv_system_status['is_windows'] = true;
			$tdv_system_status['windows_cpu_usage'] = function_exists( 'exec' ) ? @exec( 'wmic cpu get loadpercentage /all' ) : __( 'N/A (make sure exec is enabled)', 'subscriptions-for-tradingview' );
		}

		// Get the memory limit.
		$tdv_system_status['memory_limit'] = function_exists( 'ini_get' ) ? (int) ini_get( 'memory_limit' ) : __( 'N/A (ini_get function does not exist)', 'subscriptions-for-tradingview' );

		// Get the PHP maximum execution time.
		$tdv_system_status['php_max_execution_time'] = function_exists( 'ini_get' ) ? ini_get( 'max_execution_time' ) : __( 'N/A (ini_get function does not exist)', 'subscriptions-for-tradingview' );

		// Get outgoing IP address.
		$tdv_system_status['outgoing_ip'] = function_exists( 'wps_tdv_get_file_content' ) ? wps_tdv_get_file_content( 'http://ipecho.net/plain' ) : __( 'N/A (wps_tdv_get_file_content function does not exist)', 'subscriptions-for-tradingview' );

		$tdv_system_data['php'] = $tdv_system_status;
		$tdv_system_data['wp'] = $tdv_wordpress_status;

		return $tdv_system_data;
	}

	/**
	 * Generate html components.
	 *
	 * @param  string $tdv_components    html to display.
	 * @since  1.0.0
	 */
	public function wps_tdv_plug_generate_html( $tdv_components = array() ) {
		if ( is_array( $tdv_components ) && ! empty( $tdv_components ) ) {
			foreach ( $tdv_components as $tdv_component ) {
				$wps_tdv_name = array_key_exists( 'name', $tdv_component ) ? $tdv_component['name'] : $tdv_component['id'];
				switch ( $tdv_component['type'] ) {

					case 'hidden':
					case 'number':
					case 'email':
					case 'text':
						?>
					<div class="wps-form-group wps-tdv-<?php echo esc_attr( $tdv_component['type'] ); ?>">
						<div class="wps-form-group__label">
							<label for="<?php echo esc_attr( $tdv_component['id'] ); ?>" class="wps-form-label"><?php echo esc_html( $tdv_component['title'] ); // WPCS: XSS ok. ?></label>
						</div>
						<div class="wps-form-group__control">
							<label class="mdc-text-field mdc-text-field--outlined">
								<span class="mdc-notched-outline">
									<span class="mdc-notched-outline__leading"></span>
									<span class="mdc-notched-outline__notch">
										<?php if ( 'number' != $tdv_component['type'] ) { ?>
											<span class="mdc-floating-label" id="my-label-id" style=""><?php echo esc_attr( $tdv_component['placeholder'] ); ?></span>
										<?php } ?>
									</span>
									<span class="mdc-notched-outline__trailing"></span>
								</span>
								<input 
								class="mdc-text-field__input <?php echo esc_attr( $tdv_component['class'] ); ?>" 
								name="<?php echo esc_attr( $wps_tdv_name ); ?>"
								id="<?php echo esc_attr( $tdv_component['id'] ); ?>"
								type="<?php echo esc_attr( $tdv_component['type'] ); ?>"
								value="<?php echo esc_attr( $tdv_component['value'] ); ?>"
								placeholder="<?php echo esc_attr( $tdv_component['placeholder'] ); ?>"
								>
							</label>
							<div class="mdc-text-field-helper-line">
								<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo esc_attr( $tdv_component['description'] ); ?></div>
							</div>
						</div>
					</div>
						<?php
						break;

					case 'password':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label for="<?php echo esc_attr( $tdv_component['id'] ); ?>" class="wps-form-label"><?php echo esc_html( $tdv_component['title'] ); // WPCS: XSS ok. ?></label>
						</div>
						<div class="wps-form-group__control">
							<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--with-trailing-icon">
								<span class="mdc-notched-outline">
									<span class="mdc-notched-outline__leading"></span>
									<span class="mdc-notched-outline__notch">
									</span>
									<span class="mdc-notched-outline__trailing"></span>
								</span>
								<input 
								class="mdc-text-field__input <?php echo esc_attr( $tdv_component['class'] ); ?> wps-form__password" 
								name="<?php echo esc_attr( $wps_tdv_name ); ?>"
								id="<?php echo esc_attr( $tdv_component['id'] ); ?>"
								type="<?php echo esc_attr( $tdv_component['type'] ); ?>"
								value="<?php echo esc_attr( $tdv_component['value'] ); ?>"
								placeholder="<?php echo esc_attr( $tdv_component['placeholder'] ); ?>"
								>
								<i class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing wps-password-hidden" tabindex="0" role="button">visibility</i>
							</label>
							<div class="mdc-text-field-helper-line">
								<div class="mdc-text-field-helper-text--persistent wps-helper-text" id="" aria-hidden="true"><?php echo esc_attr( $tdv_component['description'] ); ?></div>
							</div>
						</div>
					</div>
						<?php
						break;

					case 'textarea':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label class="wps-form-label" for="<?php echo esc_attr( $tdv_component['id'] ); ?>"><?php echo esc_attr( $tdv_component['title'] ); ?></label>
						</div>
						<div class="wps-form-group__control">
							<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--textarea"  	for="text-field-hero-input">
								<span class="mdc-notched-outline">
									<span class="mdc-notched-outline__leading"></span>
									<span class="mdc-notched-outline__notch">
										<span class="mdc-floating-label"><?php echo esc_attr( $tdv_component['placeholder'] ); ?></span>
									</span>
									<span class="mdc-notched-outline__trailing"></span>
								</span>
								<span class="mdc-text-field__resizer">
									<textarea class="mdc-text-field__input <?php echo esc_attr( $tdv_component['class'] ); ?>" rows="2" cols="25" aria-label="Label" name="<?php echo esc_attr( $wps_tdv_name ); ?>" id="<?php echo esc_attr( $tdv_component['id'] ); ?>" placeholder="<?php echo esc_attr( $tdv_component['placeholder'] ); ?>"><?php echo esc_textarea( $tdv_component['value'] ); // WPCS: XSS ok. ?></textarea>
								</span>
							</label>

						</div>
					</div>

						<?php
						break;

					case 'select':
					case 'multiselect':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label class="wps-form-label" for="<?php echo esc_attr( $tdv_component['id'] ); ?>"><?php echo esc_html( $tdv_component['title'] ); ?></label>
						</div>
						<div class="wps-form-group__control">
							<div class="wps-form-select">
								<select name="<?php echo esc_attr( $wps_tdv_name ); ?><?php echo ( 'multiselect' === $tdv_component['type'] ) ? '[]' : ''; ?>" id="<?php echo esc_attr( $tdv_component['id'] ); ?>" class="mdl-textfield__input <?php echo esc_attr( $tdv_component['class'] ); ?>" <?php echo 'multiselect' === $tdv_component['type'] ? 'multiple="multiple"' : ''; ?> >
									<?php
									foreach ( $tdv_component['options'] as $tdv_key => $tdv_val ) {
										?>
										<option value="<?php echo esc_attr( $tdv_key ); ?>"
											<?php
											if ( is_array( $tdv_component['value'] ) ) {
												selected( in_array( (string) $tdv_key, $tdv_component['value'], true ), true );
											} else {
												selected( $tdv_component['value'], (string) $tdv_key );
											}
											?>
											>
											<?php echo esc_html( $tdv_val ); ?>
										</option>
										<?php
									}
									?>
								</select>
								<label class="mdl-textfield__label" for="octane"><?php echo esc_html( $tdv_component['description'] ); ?></label>
							</div>
						</div>
					</div>

						<?php
						break;

					case 'checkbox':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label for="<?php echo esc_attr( $tdv_component['id'] ); ?>" class="wps-form-label"><?php echo esc_html( $tdv_component['title'] ); ?></label>
						</div>
						<div class="wps-form-group__control wps-pl-4">
							<div class="mdc-form-field">
								<div class="mdc-checkbox">
									<input 
									name="<?php echo esc_attr( $wps_tdv_name ); ?>"
									id="<?php echo esc_attr( $tdv_component['id'] ); ?>"
									type="checkbox"
									class="mdc-checkbox__native-control <?php echo esc_attr( isset( $tdv_component['class'] ) ? $tdv_component['class'] : '' ); ?>"
									value="<?php echo esc_attr( $tdv_component['value'] ); ?>"
									<?php
									if ( 'on' === $tdv_component['checked'] ) {
										checked( $tdv_component['checked'], 'on' );
									}
									?>
									/>
									<div class="mdc-checkbox__background">
										<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
											<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
										</svg>
										<div class="mdc-checkbox__mixedmark"></div>
									</div>
									<div class="mdc-checkbox__ripple"></div>
								</div>
								<label for="<?php echo esc_attr( $tdv_component['id'] ); ?>"><?php echo esc_html( $tdv_component['description'] ); // WPCS: XSS ok. ?></label>
							</div>
						</div>
					</div>
						<?php
						break;

					case 'radio':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label for="<?php echo esc_attr( $tdv_component['id'] ); ?>" class="wps-form-label"><?php echo esc_html( $tdv_component['title'] ); ?></label>
						</div>
						<div class="wps-form-group__control wps-pl-4">
							<div class="wps-flex-col">
								<?php
								foreach ( $tdv_component['options'] as $tdv_radio_key => $tdv_radio_val ) {
									?>
									<div class="mdc-form-field">
										<div class="mdc-radio">
											<input
											name="<?php echo esc_attr( $wps_tdv_name ); ?>"
											value="<?php echo esc_attr( $tdv_radio_key ); ?>"
											type="radio"
											class="mdc-radio__native-control <?php echo esc_attr( $tdv_component['class'] ); ?>"
											<?php checked( $tdv_radio_key, $tdv_component['value'] ); ?>
											>
											<div class="mdc-radio__background">
												<div class="mdc-radio__outer-circle"></div>
												<div class="mdc-radio__inner-circle"></div>
											</div>
											<div class="mdc-radio__ripple"></div>
										</div>
										<label for="radio-1"><?php echo esc_html( $tdv_radio_val ); ?></label>
									</div>	
									<?php
								}
								?>
							</div>
						</div>
					</div>
						<?php
						break;

					case 'radio-switch':
						?>

					<div class="wps-form-group">
						<div class="wps-form-group__label">
							<label for="" class="wps-form-label"><?php echo esc_html( $tdv_component['title'] ); ?></label>
						</div>
						<div class="wps-form-group__control">
							<div>
								<div class="mdc-switch">
									<div class="mdc-switch__track"></div>
									<div class="mdc-switch__thumb-underlay">
										<div class="mdc-switch__thumb"></div>
										<input name="<?php echo esc_attr( $wps_tdv_name ); ?>" type="checkbox" id="basic-switch" value="on" class="mdc-switch__native-control" role="switch" aria-checked="
																<?php
																if ( 'on' == $tdv_component['value'] ) {
																	echo 'true';
																} else {
																	echo 'false';
																}
																?>
										"
										<?php checked( $tdv_component['value'], 'on' ); ?>
										>
									</div>
								</div>
							</div>
						</div>
					</div>
						<?php
						break;

					case 'button':
						?>
					<div class="wps-form-group">
						<div class="wps-form-group__label"></div>
						<div class="wps-form-group__control">
							<button class="mdc-button mdc-button--raised" name="<?php echo esc_attr( $wps_tdv_name ); ?>"
								id="<?php echo esc_attr( $tdv_component['id'] ); ?>"> <span class="mdc-button__ripple"></span>
								<span class="mdc-button__label"><?php echo esc_attr( $tdv_component['button_text'] ); ?></span>
							</button>
						</div>
					</div>

						<?php
						break;

					case 'submit':
						?>
					<tr valign="top">
						<td scope="row">
							<input type="submit" class="button button-primary" 
							name="<?php echo esc_attr( $wps_tdv_name ); ?>"
							id="<?php echo esc_attr( $tdv_component['id'] ); ?>"
							value="<?php echo esc_attr( $tdv_component['button_text'] ); ?>"
							/>
						</td>
					</tr>
						<?php
						break;

					default:
						break;
				}
			}
		}
	}
}
