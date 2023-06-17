<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://donaldit.net/
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {

	exit(); // Exit if accessed directly.
}

global $tdv_wps_tdv_obj;
global $wps_tdv_notices;
$tdv_active_tab   = isset( $_GET['tdv_tab'] ) ? sanitize_key( $_GET['tdv_tab'] ) : 'subscriptions-for-tradingview-general';
$tdv_default_tabs = $tdv_wps_tdv_obj->wps_tdv_plug_default_tabs();

if ( $wps_tdv_notices ) {
	$wps_tdv_error_text = esc_html__( 'Settings saved !', 'subscriptions-for-tradingview' );
	$tdv_wps_tdv_obj->wps_tdv_plug_admin_notice( $wps_tdv_error_text, 'success' );
}
do_action( 'wps_tdv_notice_message' );
if ( ! wps_tdv_check_multistep() ) {
	?>
	<div id="react-app"></div>
	<?php
	return;
}
?>
<header>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_attr( strtoupper( str_replace( '-', ' ', $tdv_wps_tdv_obj->tdv_get_plugin_name() ) ) ); ?></h1>

	</div>
</header>

<main class="wps-main wps-bg-white wps-r-8">
	
	<nav class="wps-navbar">
		<ul class="wps-navbar__items">
			<?php
			if ( is_array( $tdv_default_tabs ) && ! empty( $tdv_default_tabs ) ) {

				foreach ( $tdv_default_tabs as $tdv_tab_key => $tdv_default_tab ) {

					$tdv_tab_classes = 'wps-link ';

					if ( ! empty( $tdv_active_tab ) && $tdv_active_tab === $tdv_tab_key ) {
						$tdv_tab_classes .= 'active';
					}
					?>
					<li>
						<a id="<?php echo esc_attr( $tdv_tab_key ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=subscriptions_for_tradingview_menu' ) . '&tdv_tab=' . esc_attr( $tdv_tab_key ) ); ?>" class="<?php echo esc_attr( $tdv_tab_classes ); ?>"><?php echo esc_html( $tdv_default_tab['title'] ); ?></a>
					</li>
					<?php
				}
			}
			?>
		</ul>
	</nav>

	<section class="wps-section">
		<div>
			<?php
			do_action( 'wps_tdv_before_general_settings_form' );
			// if submenu is directly clicked on woocommerce.
			if ( empty( $tdv_active_tab ) ) {
				$tdv_active_tab = 'subscriptions-for-tradingview-general';
			}


				// look for the path based on the tab id in the admin templates.
			if ( ! isset( $tdv_default_tabs[ $tdv_active_tab ]['file_path'] ) ) {
				$file_path = SUBSCRIPTIONS_FOR_TRADINGVIEW_DIR_PATH;
			} else {
				$file_path = $tdv_default_tabs[ $tdv_active_tab ]['file_path'];
			}
				$tdv_tab_content_path = $file_path . 'admin/partials/' . $tdv_active_tab . '.php';
				$tdv_wps_tdv_obj->wps_tdv_plug_load_template( $tdv_tab_content_path );

				do_action( 'wps_tdv_after_general_settings_form' );
			?>
		</div>
	</section>
