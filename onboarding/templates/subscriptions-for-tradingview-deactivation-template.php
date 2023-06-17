<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://donaldit.net
 * @since      1.0.0
 *
 * @package    Subscriptions_For_TradingView
 * @subpackage Subscriptions_For_TradingView/onboarding
 */

global $pagenow, $tdv_wps_tdv_obj;
if ( empty( $pagenow ) || 'plugins.php' != $pagenow ) {
	return false;
}

$tdv_onboarding_form_deactivate = apply_filters( 'wps_tdv_deactivation_form_fields', array() );
?>
<?php if ( ! empty( $tdv_onboarding_form_deactivate ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable wps-tdv-on-boarding-dialog">
		<div class="wps-tdv-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-tdv-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-tdv-on-boarding-close-btn">
						<a href="#">
							<span class="tdv-close-form material-icons wps-tdv-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span>
						</a>
					</div>

					<h3 class="wps-tdv-on-boarding-heading mdc-dialog__title"></h3>
					<p class="wps-tdv-on-boarding-desc"><?php esc_html_e( 'May we have a little info about why you are deactivating?', 'subscriptions-for-tradingview' ); ?></p>
					<form action="#" method="post" class="wps-tdv-on-boarding-form">
						<?php
						$tdv_onboarding_deactive_html = $tdv_wps_tdv_obj->wps_tdv_plug_generate_html( $tdv_onboarding_form_deactivate );
						echo esc_html( $tdv_onboarding_deactive_html );
						?>
						<div class="wps-tdv-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-tdv-on-boarding-form-submit wps-tdv-on-boarding-form-verify ">
								<input type="submit" class="wps-tdv-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-tdv-on-boarding-form-no_thanks">
								<a href="#" class="wps-tdv-deactivation-no_thanks mdc-button"><?php esc_html_e( 'Skip and Deactivate Now', 'subscriptions-for-tradingview' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
