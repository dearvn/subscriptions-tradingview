<?php
/**
 * Fired during plugin activation
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/package/rest-api/version1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'Subscriptions_For_TradingView_Api_Process' ) ) {

	/**
	 * The plugin API class.
	 *
	 * This is used to define the functions and data manipulation for custom endpoints.
	 *
	 * @since      1.0.0
	 * @package    Subscriptions_For_TradingView
	 * @subpackage Subscriptions_For_TradingView/package/rest-api/version1
	 * @author     Donald<donald.nguyen.it@gmail.com>
	 */
	class Subscriptions_For_TradingView_Api_Process {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 */
		public function __construct() {

		}

		/**
		 * Define the function to process data for custom endpoint.
		 *
		 * @since    1.0.0
		 * @param   Array $tdv_request  data of requesting headers and other information.
		 * @return  Array $wps_tdv_rest_response    returns processed data and status of operations.
		 */
		public function wps_tdv_default_process( $tdv_request ) {
			$wps_tdv_rest_response = array();

			// Write your custom code here.

			$wps_tdv_rest_response['status'] = 200;
			$wps_tdv_rest_response['data'] = $tdv_request->get_headers();
			return $wps_tdv_rest_response;
		}
	}
}
