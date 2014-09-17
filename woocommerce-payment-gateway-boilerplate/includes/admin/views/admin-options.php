<h3><?php _e( 'Gateway Name', 'woocommerce-payment-gateway-boilerplate' ); ?></h3>

<div class="gateway-banner updated">
  <img src="<?php echo WC_Gateway_Name()->plugin_url() . '/assets/images/logo.png'; ?>" />
  <p class="main"><strong><?php _e( 'Getting started', 'woocommerce-payment-gateway-boilerplate' ); ?></strong></p>
  <p><?php _e( 'A payment gateway description can be placed here.', 'woocommerce-payment-gateway-boilerplate' ); ?></p>

  <p class="main"><strong><?php _e( 'Gateway Status', 'woocommerce-payment-gateway-boilerplate' ); ?></strong></p>
  <ul>
    <li><?php echo __( 'Debug Enabled?', 'woocommerce-payment-gateway-boilerplate' ) . ' <strong>' . $this->debug . '</strong>'; ?></li>
    <li><?php echo __( 'Sandbox Enabled?', 'woocommerce-payment-gateway-boilerplate' ) . ' <strong>' . $this->sandbox . '</strong>'; ?></li>
  </ul>

  <?php if( empty( $this->public_key ) ) { ?>
  <p><a href="https://www.gateway-domain.com" target="_blank" class="button button-primary"><?php _e( 'Sign up for Gateway Name', 'woocommerce-payment-gateway-boilerplate' ); ?></a> <a href="https://www.gateway-domaim.com" target="_blank" class="button"><?php _e( 'Learn more', 'woocommerce-payment-gateway-boilerplate' ); ?></a></p>
  <?php } ?>
</div>

<table class="form-table">
  <?php $this->generate_settings_html(); ?>
  <script type="text/javascript">
  jQuery( '#woocommerce_gateway_name_sandbox' ).change( function () {
    var sandbox = jQuery( '#woocommerce_gateway_name_sandbox_public_key, #woocommerce_gateway_name_sandbox_private_key' ).closest( 'tr' ),
    production  = jQuery( '#woocommerce_gateway_name_public_key, #woocommerce_gateway_name_private_key' ).closest( 'tr' );

    if ( jQuery( this ).is( ':checked' ) ) {
      sandbox.show();
      production.hide();
    } else {
      sandbox.hide();
      production.show();
    }
  }).change();
  </script>
</table>
