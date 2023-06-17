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
$tdv_genaral_settings = apply_filters( 'wps_tdv_general_settings_array', array() );
?>
<!--  template file for admin settings. -->
<form action="" method="POST" class="wps-tdv-gen-section-form">
	<div class="tdv-secion-wrap">
		<?php
		$tdv_general_html = $tdv_wps_tdv_obj->wps_tdv_plug_generate_html( $tdv_genaral_settings );
		echo esc_html( $tdv_general_html );
		wp_nonce_field( 'wps-tdv-general-nonce', 'wps-tdv-general-nonce-field' );
		?>
	</div>
</form>
