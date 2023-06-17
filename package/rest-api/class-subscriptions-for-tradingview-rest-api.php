<?php
/**
 * The file that defines the core plugin api class
 *
 * A class definition that includes api's endpoints and functions used across the plugin
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/package/rest-api/version1
 */

/**
 * The core plugin  api class.
 *
 * This is used to define internationalization, api-specific hooks, and
 * endpoints for plugin.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/package/rest-api/version1
 * @author     Donald<donald.nguyen.it@gmail.com>
 */
class Subscriptions_For_TradingView_Rest_Api {

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
	 * Define the core functionality of the plugin api.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the merthods, and set the hooks for the api and
	 *
	 * @since    1.0.0
	 * @param   string $plugin_name    Name of the plugin.
	 * @param   string $version        Version of the plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Define endpoints for the plugin.
	 *
	 * Uses the Subscriptions_For_TradingView_Rest_Api class in order to create the endpoint
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	public function wps_tdv_add_endpoint() {
		register_rest_route(
			'tdv-route/v1',
			'/tdv-dummy-data/',
			array(
				'methods'  => WP_REST_Server::CREATABLE,
				'callback' => array( $this, 'wps_tdv_default_callback' ),
				'permission_callback' => array( $this, 'wps_tdv_default_permission_check' ),
			)
		);
	}


	/**
	 * Begins validation process of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $result   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_tdv_default_permission_check( $request ) {

		// Add rest api validation for each request.
		$result = true;
		return $result;
	}


	/**
	 * Begins execution of api endpoint.
	 *
	 * @param   Array $request    All information related with the api request containing in this array.
	 * @return  Array   $wps_tdv_response   return rest response to server from where the endpoint hits.
	 * @since    1.0.0
	 */
	public function wps_tdv_default_callback( $request ) {

		require_once SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'package/rest-api/version1/class-subscriptions-for-tradingview-api-process.php';
		$wps_tdv_api_obj = new Subscriptions_For_TradingView_Api_Process();
		$wps_tdv_resultsdata = $wps_tdv_api_obj->wps_tdv_default_process( $request );
		if ( is_array( $wps_tdv_resultsdata ) && isset( $wps_tdv_resultsdata['status'] ) && 200 == $wps_tdv_resultsdata['status'] ) {
			unset( $wps_tdv_resultsdata['status'] );
			$wps_tdv_response = new WP_REST_Response( $wps_tdv_resultsdata, 200 );
		} else {
			$wps_tdv_response = new WP_Error( $wps_tdv_resultsdata );
		}
		return $wps_tdv_response;
	}
}
