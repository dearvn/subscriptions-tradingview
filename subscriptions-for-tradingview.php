<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://donaldit.net/
 * @since             1.0.0
 * @package           Subscriptions_For_TradingView
 *
 * @wordpress-plugin
 * Plugin Name:       Subscriptions For TradingView
 * Plugin URI:        https://github.com/dearvn/subscriptions-tradingview
 * Description:       <code><strong>Subscriptions For TradingView</strong></code> allow collecting repeated payments through subscriptions orders on the eCommerce store for both admin and users.
 * Version:           1.4.4
 * Author:            Donald<donald.nguyen.it@gmail.com>
 * Author URI:        https://donaldit.net/?utm_source=tradingviews-subs-official&utm_medium=subs-org-backend&utm_campaign=official
 * Text Domain:       subscriptions-for-tradingview
 * Domain Path:       /languages
 *
 * Requires at least:        5.0
 * Tested up to:             6.0.2
 * WC requires at least:     5.0
 * WC tested up to:          6.8.2
 *
 * License:           GNU General Public License v3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$old_pro_exists = false;
$plug           = get_plugins();

$old_tdv_pro_present   = false;
$installed_plugins = get_plugins();


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	/**
	 * Define plugin constants.
	 *
	 * @since             1.0.0
	 */
	function define_subscriptions_for_tradingview_constants() {

		subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_VERSION', '1.4.4' );
		subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH', plugin_dir_path( __FILE__ ) );
		subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_URL', plugin_dir_url( __FILE__ ) );
		subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_SERVER_URL', 'https://donaldit.net' );
		subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_ITEM_REFERENCE', 'Subscriptions for TradingView' );
        subscriptions_for_tradingview_constants( 'SUBSCRIPTIONS_FOR_TRADINGVIEW_TEMPLATE_PATH', plugin_dir_path( __FILE__ ). 'templates' );
	}


	/**
	 * Callable function for defining plugin constants.
	 *
	 * @param   String $key    Key for contant.
	 * @param   String $value   value for contant.
	 * @since             1.0.0
	 */
	function subscriptions_for_tradingview_constants( $key, $value ) {

		if ( ! defined( $key ) ) {

			define( $key, $value );
		}
	}

	// Upgrade notice.
	add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), 'tdv_upgrade_notice' );

	/**
	 * Upgrade Notice for Subscription Plugin.
	 *
	 * @return void
	 */
	function tdv_upgrade_notice() {
		$wps_tdv_get_count = new Subscriptions_For_TradingView_Admin( 'subscriptions-for-tradingview', '1.4.2' );
		$wps_tdv_pending_product_count  = $wps_tdv_get_count->wps_tdv_get_count( 'pending', 'count', 'products' );
		$wps_tdv_pending_orders_count   = $wps_tdv_get_count->wps_tdv_get_count( 'pending', 'count', 'mwb_renewal_orders' );
		$wps_tdv_pending_subs_count     = $wps_tdv_get_count->wps_tdv_get_count( 'pending', 'count', 'post_type_subscription' );
		if ( '0' != $wps_tdv_pending_product_count || '0' != $wps_tdv_pending_orders_count || '0' != $wps_tdv_pending_subs_count ) {
			?>
	<tr class="plugin-update-tr active notice-warning notice-alt">
			<td  colspan="4" class="plugin-update colspanchange">
				<div class="notice notice-warning inline update-message notice-alt">
					<p>
						<?php esc_html_e( 'Heads up, The latest update includes some substantial changes across different areas of the plugin.', 'subscriptions-for-tradingview' ); ?>
					</p>
					<p><b><?php esc_html_e( 'Please Click', 'subscriptions-for-tradingview' ); ?><a href="<?php echo esc_attr( admin_url( 'admin.php' ) . '?page=subscriptions_for_tradingview_menu' ); ?>"> here </a><?php esc_html_e( 'To Goto the Migration Page and Run the Migration Functionality.', 'subscriptions-for-tradingview' ); ?></b></p>
				</div>
			</td>
		</tr>
	<style>
	.wps-notice-section > p:before {
		content: none;
	}
	</style>

			<?php
		}
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-subscriptions-for-tradingview-activator.php
	 */
	function activate_subscriptions_for_tradingview() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-subscriptions-for-tradingview-activator.php';
		Subscriptions_For_TradingView_Activator::subscriptions_for_tradingview_activate();
		$wps_tdv_active_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_tdv_active_plugin ) && ! empty( $wps_tdv_active_plugin ) ) {
			$wps_tdv_active_plugin['subscriptions-for-tradingview'] = array(
				'plugin_name' => __( 'Subscriptions For TradingView', 'subscriptions-for-tradingview' ),
				'active' => '1',
			);
		} else {
			$wps_tdv_active_plugin = array();
			$wps_tdv_active_plugin['subscriptions-for-tradingview'] = array(
				'plugin_name' => __( 'Subscriptions For TradingView', 'subscriptions-for-tradingview' ),
				'active' => '1',
			);
		}
		update_option( 'wps_all_plugins_active', $wps_tdv_active_plugin );
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-subscriptions-for-tradingview-deactivator.php
	 */
	function deactivate_subscriptions_for_tradingview() {
		require_once plugin_dir_path( __FILE__ ) . 'includes/class-subscriptions-for-tradingview-deactivator.php';
		Subscriptions_For_TradingView_Deactivator::subscriptions_for_tradingview_deactivate();
		$wps_tdv_deactive_plugin = get_option( 'wps_all_plugins_active', false );
		if ( is_array( $wps_tdv_deactive_plugin ) && ! empty( $wps_tdv_deactive_plugin ) ) {
			foreach ( $wps_tdv_deactive_plugin as $wps_tdv_deactive_key => $wps_tdv_deactive ) {
				if ( 'subscriptions-for-tradingview' === $wps_tdv_deactive_key ) {
					$wps_tdv_deactive_plugin[ $wps_tdv_deactive_key ]['active'] = '0';
				}
			}
		}
		update_option( 'wps_all_plugins_active', $wps_tdv_deactive_plugin );
	}

	register_activation_hook( __FILE__, 'activate_subscriptions_for_tradingview' );
	register_deactivation_hook( __FILE__, 'deactivate_subscriptions_for_tradingview' );

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require plugin_dir_path( __FILE__ ) . 'includes/class-subscriptions-for-tradingview.php';

	if ( ! function_exists( 'wps_tdv_check_multistep' ) ) {
		/**
		 * This function is used to check susbcripton product in cart.
		 *
		 * @name wps_tdv_check_multistep
		 * @since 1.0.2
		 */
		function wps_tdv_check_multistep() {
			$bool = false;
			$wps_tdv_check = get_option( 'wps_tdv_multistep_done', false );
			$wps_tdv_enable_plugin = get_option( 'wps_tdv_enable_plugin', false );
			if ( ! empty( $wps_tdv_check ) && 'on' == $wps_tdv_enable_plugin ) {
				$bool = true;
			}

			return $bool;
		}
	}
	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function run_subscriptions_for_tradingview() {

		define_subscriptions_for_tradingview_constants();

		$tdv_tdv_plugin_standard = new Subscriptions_For_TradingView();
		$tdv_tdv_plugin_standard->tdv_run();
		$GLOBALS['tdv_wps_tdv_obj'] = $tdv_tdv_plugin_standard;
		$GLOBALS['wps_tdv_notices'] = false;

	}
	run_subscriptions_for_tradingview();


	// Add settings link on plugin page.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'subscriptions_for_tradingview_settings_link' );

	/**
	 * Settings link.
	 *
	 * @since    1.0.0
	 * @param   Array $links    Settings link array.
	 */
	function subscriptions_for_tradingview_settings_link( $links ) {

		return array(
			'<a href="' . admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' ) . '">' . __( 'Settings', 'subscriptions-for-tradingview' ) . '</a>',
		);
	}


	register_activation_hook( __FILE__, 'wps_tdv_flush_rewrite_rules' );
	register_deactivation_hook( __FILE__, 'wps_tdv_flush_rewrite_rules' );

	/**
	 * This function is used to create tabs
	 *
	 * @name wps_tdv_flush_rewrite_rules
	 * @since 1.0.0.
	 * @author Donald<donald.nguyen.it@gmail.com>
	 * @link https://www.donaldit.net/
	 */
	function wps_tdv_flush_rewrite_rules() {
		add_rewrite_endpoint( 'wps_subscriptions', EP_PAGES );
		add_rewrite_endpoint( 'show-subscription', EP_PAGES );
		add_rewrite_endpoint( 'wps-add-payment-method', EP_PAGES );
		flush_rewrite_rules();
	}

	add_action( 'init', 'wps_tdv_register_custom_order_types' );

	/**
	 * This function is used to create custom post type for subscription.
	 *
	 * @name wps_tdv_register_custom_order_types
	 * @since 1.0.0
	 */
	function wps_tdv_register_custom_order_types() {
		wc_register_order_type(
			'wps_subscriptions',
			apply_filters(
				'wps_tdv_register_custom_order_types',
				array(
					'labels'                           => array(
						'name'               => __( 'Subscriptions', 'subscriptions-for-tradingview' ),
						'singular_name'      => __( 'Subscription', 'subscriptions-for-tradingview' ),
						'add_new'            => __( 'Add Subscription', 'subscriptions-for-tradingview' ),
						'add_new_item'       => __( 'Add New Subscription', 'subscriptions-for-tradingview' ),
						'edit'               => __( 'Edit', 'subscriptions-for-tradingview' ),
						'edit_item'          => __( 'Edit Subscription', 'subscriptions-for-tradingview' ),
						'new_item'           => __( 'New Subscription', 'subscriptions-for-tradingview' ),
						'view'               => __( 'View Subscription', 'subscriptions-for-tradingview' ),
						'view_item'          => __( 'View Subscription', 'subscriptions-for-tradingview' ),
						'search_items'       => __( 'Search Subscriptions', 'subscriptions-for-tradingview' ),
						'not_found'          => __( 'Not Found', 'subscriptions-for-tradingview' ),
						'not_found_in_trash' => __( 'No Subscriptions found in the trash', 'subscriptions-for-tradingview' ),
						'parent'             => __( 'Parent Subscriptions', 'subscriptions-for-tradingview' ),
						'menu_name'          => __( 'Subscriptions', 'subscriptions-for-tradingview' ),
					),
					'description'                      => __( 'These subscriptions are stored.', 'subscriptions-for-tradingview' ),
					'public'                           => false,
					'show_ui'                          => true,
					'capability_type'                  => 'shop_order',
					'map_meta_cap'                     => true,
					'publicly_queryable'               => false,
					'exclude_from_search'              => true,
					'show_in_menu'                     => false,
					'hierarchical'                     => false,
					'show_in_nav_menus'                => false,
					'rewrite'                          => false,
					'query_var'                        => false,
					'supports'                         => array( 'title', 'comments', 'custom-fields' ),
					'has_archive'                      => false,
					'exclude_from_orders_screen'       => true,
					'add_order_meta_boxes'             => true,
					'exclude_from_order_count'         => true,
					'exclude_from_order_views'         => true,
					'exclude_from_order_webhooks'      => true,
					'exclude_from_order_reports'       => true,
					'exclude_from_order_sales_reports' => true,
				)
			)
		);
	}
	add_action( 'activated_plugin', 'wps_sfe_redirect_on_settings' );

	if ( ! function_exists( 'wps_sfe_redirect_on_settings' ) ) {
		/**
		 * This function is used to check plugin.
		 *
		 * @name wps_sfe_redirect_on_settings
		 * @param string $plugin plugin.
		 * @since 1.0.3
		 */
		function wps_sfe_redirect_on_settings( $plugin ) {
			if ( plugin_basename( __FILE__ ) === $plugin ) {
				$general_settings_url = admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' );
				wp_safe_redirect( esc_url( $general_settings_url ) );
				exit();
			}
		}
	}
	
	/**
	 * Load custom payment gateway.
	 *
	 * @param array $methods array containing the payment methods in WooCommerce.
	 * @since 1.0.0
	 * @return array
	 */
	function wps_paypal_integration_for_woocommerce_extended( $methods ) {
		$methods[] = 'WC_Gateway_Wps_Paypal_Integration';
		return $methods;
	}

	add_filter( 'woocommerce_payment_gateways', 'wps_paypal_integration_for_woocommerce_extended' );
	/**
	 * Extending main WC_Payment_Gateway class.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function wps_paypal_integration_for_woocommerce_gateway() {
		require_once SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'includes/class-wc-gateway-wps-paypal-integration.php';
	}

	add_action( 'init', 'wps_paypal_integration_for_woocommerce_gateway' );
} else {
	// WooCommerce is not active so deactivate this plugin.
	add_action( 'admin_init', 'wps_tdv_activation_failure' );

	/**
	 * Deactivate this plugin.
	 *
	 * @name wps_tdv_activation_failure
	 * @since 1.0.0
	 */
	function wps_tdv_activation_failure() {

		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	// Add admin error notice.
	add_action( 'admin_notices', 'wps_tdv_activation_failure_admin_notice' );

	/**
	 * This function is used to display admin error notice when WooCommerce is not active.
	 *
	 * @name wps_tdv_activation_failure_admin_notice
	 * @since 1.0.0
	 */
	function wps_tdv_activation_failure_admin_notice() {

		// to hide Plugin activated notice.
		unset( $_GET['activate'] );

		?>

		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( 'WooCommerce is not activated, Please activate WooCommerce first to activate Subscriptions TradingView.', 'subscriptions-for-tradingview' ); ?></p>
		</div>

		<?php
	}
}
