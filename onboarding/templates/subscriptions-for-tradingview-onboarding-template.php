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

global $tdv_wps_tdv_obj;
$tdv_onboarding_form_fields = apply_filters( 'wps_tdv_on_boarding_form_fields', array() );
?>

<?php if ( ! empty( $tdv_onboarding_form_fields ) ) : ?>
	<div class="mdc-dialog mdc-dialog--scrollable wps-tdv-on-boarding-dialog">
		<div class="wps-tdv-on-boarding-wrapper-background mdc-dialog__container">
			<div class="wps-tdv-on-boarding-wrapper mdc-dialog__surface" role="alertdialog" aria-modal="true" aria-labelledby="my-dialog-title" aria-describedby="my-dialog-content">
				<div class="mdc-dialog__content">
					<div class="wps-tdv-on-boarding-close-btn">
						<a href="#"><span class="tdv-close-form material-icons wps-tdv-close-icon mdc-dialog__button" data-mdc-dialog-action="close">clear</span></a>
					</div>

					<h3 class="wps-tdv-on-boarding-heading mdc-dialog__title"><?php esc_html_e( 'Welcome to TradingView', 'subscriptions-for-tradingview' ); ?> </h3>
					<p class="wps-tdv-on-boarding-desc"><?php esc_html_e( 'We love making new friends! Subscribe below and we promise to keep you up-to-date with our latest new plugins, updates, awesome deals and a few special offers.', 'subscriptions-for-tradingview' ); ?></p>

					<form action="#" method="post" class="wps-tdv-on-boarding-form">
						<?php
						$tdv_onboarding_html = $tdv_wps_tdv_obj->wps_tdv_plug_generate_html( $tdv_onboarding_form_fields );
						echo esc_html( $tdv_onboarding_html );
						?>
						<div class="wps-tdv-on-boarding-form-btn__wrapper mdc-dialog__actions">
							<div class="wps-tdv-on-boarding-form-submit wps-tdv-on-boarding-form-verify ">
								<input type="submit" class="wps-tdv-on-boarding-submit wps-on-boarding-verify mdc-button mdc-button--raised" value="Send Us">
							</div>
							<div class="wps-tdv-on-boarding-form-no_thanks">
								<a href="#" class="wps-tdv-on-boarding-no_thanks mdc-button" data-mdc-dialog-action="discard"><?php esc_html_e( 'Skip For Now', 'subscriptions-for-tradingview' ); ?></a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="mdc-dialog__scrim"></div>
	</div>
<?php endif; ?>
