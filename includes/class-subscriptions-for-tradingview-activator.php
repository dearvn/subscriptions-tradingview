<?php
/**
 * Fired during plugin activation
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/includes
 * @author     Donald<donald.nguyen.it@gmail.com>
 */
class Subscriptions_For_TradingView_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function subscriptions_for_tradingview_activate() {

		wp_clear_scheduled_hook( 'tradingviews_tracker_send_event' );
		wp_schedule_event( time() + 10, apply_filters( 'tradingviews_tracker_event_recurrence', 'daily' ), 'tradingviews_tracker_send_event' );
	}
}
