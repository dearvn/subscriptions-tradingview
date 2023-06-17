<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link  https://donaldit.net/
 * @since 1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}
global $tdv_wps_tdv_obj;
$tdv_default_tabs = $tdv_wps_tdv_obj->wps_tdv_plug_default_tabs();
$tdv_tab_key = '';
?>
<header>
	<?php
	// desc - This hook is used for trial.
	do_action( 'wps_tdv_settings_saved_notice' );
	?>
	<div class="wps-header-container wps-bg-white wps-r-8">
		<h1 class="wps-header-title"><?php echo esc_attr( 'TradingView' ); ?></h1>
	</div>
</header>
<main class="wps-main wps-bg-white wps-r-8">
	<section class="wps-section">
		<div>
			<?php
				// desc - This hook is used for trial.
			do_action( 'wps_tdv_before_common_settings_form' );
				// if submenu is directly clicked on woocommerce.
			$tdv_genaral_settings = apply_filters(
				'tdv_home_settings_array',
				array(
					array(
						'title' => __( 'Enable Tracking', 'subscriptions-for-tradingview' ),
						'type'  => 'radio-switch',
						'id'    => 'wps_tdv_enable_tracking',
						'value' => get_option( 'wps_tdv_enable_tracking' ),
						'class' => 'tdv-radio-switch-class',
						'options' => array(
							'yes' => __( 'YES', 'subscriptions-for-tradingview' ),
							'no' => __( 'NO', 'subscriptions-for-tradingview' ),
						),
					),
					array(
						'type'  => 'button',
						'id'    => 'tdv_track_button',
						'button_text' => __( 'Save', 'subscriptions-for-tradingview' ),
						'class' => 'tdv-button-class',
					),
				)
			);
			?>
			<form action="" method="POST" class="wps-tdv-gen-section-form">
				<div class="tdv-secion-wrap">
					<?php
					$tdv_general_html = $tdv_wps_tdv_obj->wps_tdv_plug_generate_html( $tdv_genaral_settings );
					echo esc_html( $tdv_general_html );
					wp_nonce_field( 'wps-tdv-general-nonce', 'wps-tdv-general-nonce-field' );
					?>
				</div>
			</form>
			<?php
			do_action( 'wps_tdv_before_common_settings_form' );
			$all_plugins = get_plugins();
			?>
		</div>
	</section>
	<style type="text/css">
		.cards {
			   display: flex;
			   flex-wrap: wrap;
			   padding: 20px 40px;
		}
		.card {
			flex: 1 0 518px;
			box-sizing: border-box;
			margin: 1rem 3.25em;
			text-align: center;
		}

	</style>
	<div class="centered">
		<section class="cards">
			<?php foreach ( get_plugins() as $key => $value ) : ?>
				<?php if ( 'Donald' === $value['Author'] ) : ?>
					<article class="card">
						<div class="container">
							<h4><b><?php echo esc_html( $value['Name'] ); ?></b></h4> 
							<p><?php echo esc_html( $value['Version'] ); ?></p> 
							<p><?php echo wp_kses_post( $value['Description'] ); ?></p>
						</div>
					</article>
				<?php endif; ?>
			<?php endforeach; ?>
		</section>
	</div>
