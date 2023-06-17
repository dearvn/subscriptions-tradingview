<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $tdv_wps_tdv_obj;
// Add filed above susbcription list.
$tdv_template_settings = apply_filters( 'tdv_template_settings_array', array() );
?>
<!--  template file for admin settings. -->
<div class="tdv-section-wrap">
	<?php

		require_once SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH . 'admin/partials/class-subscriptions-for-tradingview-admin-subscription-list.php';
	?>
</div>
