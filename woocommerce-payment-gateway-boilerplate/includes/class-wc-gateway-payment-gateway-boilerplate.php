<?php
if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooCommerce Gateway Name.
 *
 * @class   WC_Gateway_Payment_Gateway_Boilerplate
 * @extends WC_Payment_Gateway
 * @version 1.0.0
 * @package WooCommerce Payment Gateway Boilerplate/Includes
 * @author  Sebastien Dumont
 */
class WC_Gateway_Payment_Gateway_Boilerplate extends WC_Payment_Gateway {

  /**
   * Constructor for the gateway.
   *
   * @access public
   * @return void
   */
  public function __construct() {
    $this->id                 = 'gateway_name';
    $this->icon               = apply_filters( 'woocommerce_payment_gateway_boilerplate_icon', plugins_url( '/assets/images/cards.png', dirname( __FILE__ ) ) );
    $this->has_fields         = false;
    $this->credit_fields      = false;

    $this->order_button_text  = __( 'Pay with Gateway Name', 'woocommerce-payment-gateway-boilerplate' );

    $this->method_title       = __( 'Gateway Name', 'woocommerce-payment-gateway-boilerplate' );
    $this->method_description = __( 'Take payments via Gateway Name.', 'woocommerce-payment-gateway-boilerplate' );

    // TODO: Rename 'WC_Gateway_Payment_Gateway_Boilerplate' to match the name of this class.
    $this->notify_url         = WC()->api_request_url( 'WC_Gateway_Payment_Gateway_Boilerplate' );

    // TODO: 
    $this->api_endpoint       = 'https://api.payment-gateway.com/';

    // TODO: Use only what the payment gateway supports.
    $this->supports           = array(
      'subscriptions',
      'products',
      'subscription_cancellation',
      'subscription_reactivation',
      'subscription_suspension',
      'subscription_amount_changes',
      'subscription_payment_method_change',
      'subscription_date_changes',
      'default_credit_card_form',
      'refunds',
      'pre-orders'
    );

    // TODO: Replace the transaction url here or use the function 'get_transaction_url' at the bottom.
    $this->view_transaction_url = 'https://www.domain.com';

    // Load the form fields.
    $this->init_form_fields();

    // Load the settings.
    $this->init_settings();

    // Get setting values.
    $this->enabled        = $this->get_option( 'enabled' );

    $this->title          = $this->get_option( 'title' );
    $this->description    = $this->get_option( 'description' );
    $this->instructions   = $this->get_option( 'instructions' );

    $this->sandbox        = $this->get_option( 'sandbox' );
    $this->private_key    = $this->sandbox == 'no' ? $this->get_option( 'private_key' ) : $this->get_option( 'sandbox_private_key' );
    $this->public_key     = $this->sandbox == 'no' ? $this->get_option( 'public_key' ) : $this->get_option( 'sandbox_public_key' );

    $this->debug          = $this->get_option( 'debug' );

    // Logs.
    if( $this->debug == 'yes' ) {
      if( class_exists( 'WC_Logger' ) ) {
        $this->log = new WC_Logger();
      }
      else {
        $this->log = $woocommerce->logger();
      }
    }

    $this->init_gateway_sdk();

    // Hooks.
    if( is_admin() ) {
      add_action( 'wp_enqueue_scripts', array( $this, 'payment_scripts' ) );
      add_action( 'admin_notices', array( $this, 'checks' ) );

      add_action( 'woocommerce_receipt_' . $this->id, array( $this, 'receipt_page' ) );
      add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );

    // Customer Emails.
    add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
  }

  /**
   * Init Payment Gateway SDK.
   *
   * @access protected
   * @return void
   */
  protected function init_gateway_sdk() {
    // TODO: Insert your gateway sdk script here and call it.
  }

  /**
   * Admin Panel Options
   * - Options for bits like 'title' and availability on a country-by-country basis
   *
   * @access public
   * @return void
   */
  public function admin_options() {
    include_once( WC_Gateway_Name()->plugin_path() . '/includes/admin/views/admin-options.php' );
  }

  /**
   * Check if SSL is enabled and notify the user.
   *
   * @TODO:  Use only what you need.
   * @access public
   */
  public function checks() {
    if( $this->enabled == 'no' ) {
      return;
    }

    // PHP Version.
    if( version_compare( phpversion(), '5.3', '<' ) ) {
      echo '<div class="error"><p>' . sprintf( __( 'Gateway Name Error: Gateway Name requires PHP 5.3 and above. You are using version %s.', 'woocommerce-payment-gateway-boilerplate' ), phpversion() ) . '</p></div>';
    }

    // Check required fields.
    else if( !$this->public_key || !$this->private_key ) {
      echo '<div class="error"><p>' . __( 'Gateway Name Error: Please enter your public and private keys', 'woocommerce-payment-gateway-boilerplate' ) . '</p></div>';
    }

    // Show message if enabled and FORCE SSL is disabled and WordPress HTTPS plugin is not detected.
    else if( 'no' == get_option( 'woocommerce_force_ssl_checkout' ) && !class_exists( 'WordPressHTTPS' ) ) {
      echo '<div class="error"><p>' . sprintf( __( 'Gateway Name is enabled, but the <a href="%s">force SSL option</a> is disabled; your checkout may not be secure! Please enable SSL and ensure your server has a valid SSL certificate - Gateway Name will only work in sandbox mode.', 'woocommerce-payment-gateway-boilerplate'), admin_url( 'admin.php?page=wc-settings&tab=checkout' ) ) . '</p></div>';
    }
  }

  /**
   * Check if this gateway is enabled.
   *
   * @access public
   */
  public function is_available() {
    if( $this->enabled == 'no' ) {
      return false;
    }

    if( !is_ssl() && 'yes' != $this->sandbox ) {
      return false;
    }

    if( !$this->public_key || !$this->private_key ) {
      return false;
    }

    return true;
  }

  /**
   * Initialise Gateway Settings Form Fields
   *
   * The standard gateway options have already been applied. 
   * Change the fields to match what the payment gateway your building requires.
   *
   * @access public
   */
  public function init_form_fields() {
    $this->form_fields = array(
      'enabled' => array(
        'title'       => __( 'Enable/Disable', 'woocommerce-payment-gateway-boilerplate' ),
        'label'       => __( 'Enable Gateway Name', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'checkbox',
        'description' => '',
        'default'     => 'no'
      ),
      'title' => array(
        'title'       => __( 'Title', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => __( 'Gateway Name', 'woocommerce-payment-gateway-boilerplate' ),
        'desc_tip'    => true
      ),
      'description' => array(
        'title'       => __( 'Description', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => 'Pay with Gateway Name.',
        'desc_tip'    => true
      ),
      'instructions' => array(
        'title'       => __( 'Instructions', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'textarea',
        'description' => __( 'Instructions that will be added to the thank you page and emails.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => '',
        'desc_tip'    => true,
      ),
      'debug' => array(
        'title'       => __( 'Debug Log', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'checkbox',
        'label'       => __( 'Enable logging', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => 'no',
        'description' => sprintf( __( 'Log Gateway name events inside <code>%s</code>', 'woocommerce-payment-gateway-boilerplate' ), wc_get_log_file_path( $this->id ) )
      ),
      'sandbox' => array(
        'title'       => __( 'Sandbox', 'woocommerce-payment-gateway-boilerplate' ),
        'label'       => __( 'Enable Sandbox Mode', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'checkbox',
        'description' => __( 'Place the payment gateway in sandbox mode using sandbox API keys (real payments will not be taken).', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => 'yes'
      ),
      'sandbox_private_key' => array(
        'title'       => __( 'Sandbox Private Key', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'Get your API keys from your Gateway Name account.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => '',
        'desc_tip'    => true
      ),
      'sandbox_public_key' => array(
        'title'       => __( 'Sandbox Public Key', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'Get your API keys from your Gateway Name account.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => '',
        'desc_tip'    => true
      ),
      'private_key' => array(
        'title'       => __( 'Private Key', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'Get your API keys from your Gateway Name account.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => '',
        'desc_tip'    => true
      ),
      'public_key' => array(
        'title'       => __( 'Public Key', 'woocommerce-payment-gateway-boilerplate' ),
        'type'        => 'text',
        'description' => __( 'Get your API keys from your Gateway Name account.', 'woocommerce-payment-gateway-boilerplate' ),
        'default'     => '',
        'desc_tip'    => true
      ),
    );
  }

  /**
   * Output for the order received page.
   *
   * @access public
   * @return void
   */
  public function receipt_page( $order ) {
    echo '<p>' . __( 'Thank you - your order is now pending payment.', 'woocommerce-payment-gateway-boilerplate' ) . '</p>';

    // TODO: 
  }

  /**
   * Payment form on checkout page.
   *
   * @TODO:  Use this function to add credit card 
   *         and custom fields on the checkout page.
   * @access public
   */
  public function payment_fields() {
    $description = $this->get_description();

    if( $this->sandbox == 'yes' ) {
      $description .= ' ' . __( 'TEST MODE ENABLED.' );
    }

    if( !empty( $description ) ) {
      echo wpautop( wptexturize( trim( $description ) ) );
    }

    // If credit fields are enabled, then the credit card fields are provided automatically.
    if( $this->credit_fields ) {
      $this->credit_card_form(
        array( 
          'fields_have_names' => false
        )
      );
    }

    // This includes your custom payment fields.
    include_once( WC_Gateway_Name()->plugin_path() . '/includes/views/html-payment-fields.php' );

  }

  /**
   * Outputs scripts used for the payment gateway.
   *
   * @access public
   */
  public function payment_scripts() {
    if( !is_checkout() || !$this->is_available() ) {
      return;
    }

    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    // TODO: Enqueue your wp_enqueue_script's here.

  }

  /**
   * Output for the order received page.
   *
   * @access public
   */
  public function thankyou_page( $order_id ) {
    if( !empty( $this->instructions ) ) {
      echo wpautop( wptexturize( wp_kses_post( $this->instructions ) ) );
    }

    $this->extra_details( $order_id );
  }

  /**
   * Add content to the WC emails.
   *
   * @access public
   * @param  WC_Order $order
   * @param  bool $sent_to_admin
   * @param  bool $plain_text
   */
  public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
    if( !$sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
      if( !empty( $this->instructions ) ) {
        echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
      }

      $this->extra_details( $order->id );
    }
  }

  /**
   * Gets the extra details you set here to be 
   * displayed on the 'Thank you' page.
   *
   * @access private
   */
  private function extra_details( $order_id = '' ) {
    echo '<h2>' . __( 'Extra Details', 'woocommerce-payment-gateway-boilerplate' ) . '</h2>' . PHP_EOL;

    // TODO: Place what ever instructions or details the payment gateway needs to display here.
  }

  /**
   * Process the payment and return the result.
   *
   * @TODO   You will need to add payment code inside.
   * @access public
   * @param  int $order_id
   * @return array
   */
  public function process_payment( $order_id ) {
    $order = new WC_Order( $order_id );

    // This array is used just for demo testing a successful transaction.
    $payment = array( 
      'id'     => 123,
      'status' => 'APPROVED'
    );

    if( $this->debug == 'yes' ) {
      $this->log->add( $this->id, 'Gateway Name payment response: ' . print_r( $payment, true ) . ')' );
    }

    /**
     * TODO: Call your gateway api and return it's results.
     * e.g. return the the payment status and if 'APPROVED' 
     * then WooCommerce will complete the order.
     */
    if( 'APPROVED' == $payment['status'] ) {
      // Payment complete.
      $order->payment_complete();

      // Store the transaction ID for WC 2.2 or later.
      add_post_meta( $order->id, '_transaction_id', $payment['id'], true );

      // Add order note.
      $order->add_order_note( sprintf( __( 'Gateway Name payment approved (ID: %s)', 'woocommerce-payment-gateway-boilerplate' ), $payment['id'] ) );

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Gateway Name payment approved (ID: ' . $payment['id'] . ')' );
      }

      // Reduce stock levels.
      $order->reduce_order_stock();

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Stocked reduced.' );
      }

      // Remove items from cart.
      WC()->cart->empty_cart();

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Cart emptied.' );
      }

      // Return thank you page redirect.
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url( $order )
      );
    }
    else {
      // Add order note.
      $order->add_order_note( __( 'Gateway Name payment declined', 'woocommerce-payment-gateway-boilerplate' ) );

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Gateway Name payment declined (ID: ' . $payment['id'] . ')' );
      }

      // Return message to customer.
      return array(
        'result'   => 'failure',
        'message'  => '',
        'redirect' => ''
      );
    }
  }

  /**
   * Process refunds.
   * WooCommerce 2.2 or later
   *
   * @access public
   * @param  int $order_id
   * @param  float $amount
   * @param  string $reason
   * @return bool|WP_Error
   */
  public function process_refund( $order_id, $amount = null, $reason = '' ) {

    $payment_id = get_post_meta( $order_id, '_transaction_id', true );
    $response = ''; // TODO: Use this variable to fetch a response from your payment gateway, if any.

    if( is_wp_error( $response ) ) {
      return $response;
    }

    if( 'APPROVED' == $refund['status'] ) {

      // Mark order as refunded
      $order->update_status( 'refunded', __( 'Payment refunded via Gateway Name.', 'woocommerce-payment-gateway-boilerplate' ) );

      $order->add_order_note( sprintf( __( 'Refunded %s - Refund ID: %s', 'woocommerce-payment-gateway-boilerplate' ), $refunded_cost, $refund_transaction_id ) );

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Gateway Name order #' . $order_id . ' refunded successfully!' );
      }
      return true;
    }
    else {

      $order->add_order_note( __( 'Error in refunding the order.', 'woocommerce-payment-gateway-boilerplate' ) );

      if( $this->debug == 'yes' ) {
        $this->log->add( $this->id, 'Error in refunding the order #' . $order_id . '. Gateway Name response: ' . print_r( $response, true ) );
      }

      return true;
    }

  }

  /**
   * Get the transaction URL.
   *
   * @TODO   Replace both 'view_transaction_url'\'s. 
   *         One for sandbox/testmode and one for live.
   * @param  WC_Order $order
   * @return string
   */
  public function get_transaction_url( $order ) {
    if( $this->sandbox == 'yes' ) {
      $this->view_transaction_url = 'https://www.sandbox.payment-gateway.com/?trans_id=%s';
    }
    else {
      $this->view_transaction_url = 'https://www.payment-gateway.com/?trans_id=%s';
    }

    return parent::get_transaction_url( $order );
  }

} // end class.

?>