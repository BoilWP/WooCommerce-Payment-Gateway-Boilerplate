<?php
if( !defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WooCommerce Gateway Name for subscriptions and pre-orders.
 *
 * @class   WC_Gateway_Payment_Gateway_Boilerplate_Addons
 * @extends WC_Gateway_Payment_Gateway_Boilerplate
 * @version 1.0.0
 * @package WooCommerce Payment Gateway Boilerplate/Includes
 * @author  Sebastien Dumont
 */

class WC_Gateway_Payment_Gateway_Boilerplate_Addons extends WC_Gateway_Payment_Gateway_Boilerplate {

  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();

    if( class_exists( 'WC_Subscriptions_Order' ) ) {
      add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 3 );
    }

    if( class_exists( 'WC_Pre_Orders_Order' ) ) {
      add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_release_payment' ) );
    }
  }

  /**
   * Process the subscription
   *
   * @param  int $order_id
   * @return array
   */
  public function process_subscription( $order_id ) {
    $order = new WC_Order( $order_id );

    if( $this->sandbox == 'yes' ) {
      $error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and there are no JavaScript errors on the page.', 'woocommerce-payment-gateway-boilerplate' );
    }

    $initial_payment = WC_Subscriptions_Order::get_total_initial_payment( $order );

    if( $initial_payment > 0 ) {
      $payment_response = $this->process_subscription_payment( $order, $initial_payment );
    }

    if( isset( $payment_response ) && is_wp_error( $payment_response ) ) {
      throw new Exception( $payment_response->get_error_message() );
    }
    else {
      // Remove cart
      WC()->cart->empty_cart();

      // Return thank you page redirect
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url( $order )
      );
    }

    return array(
      'result'   => 'fail',
      'redirect' => ''
    );
  }

  /**
   * Process the pre-order
   *
   * @param  int $order_id
   * @return array
   */
  public function process_pre_order( $order_id ) {
    if( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {
      $order = new WC_Order( $order_id );

      // Order limit
      if( $order->order_total * 100 < 50 ) {
        $error_msg = __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'woocommerce-payment-gateway-boilerplate' );
      }

      if( $this->sandbox == 'yes' ) {
        $error_msg .= ' ' . __( 'Developers: Please make sure that you\'re including jQuery and there are no JavaScript errors on the page.', 'woocommerce-payment-gateway-boilerplate' );
      }

      // Reduce stock levels
      $order->reduce_order_stock();

      // Remove cart
      WC()->cart->empty_cart();

      // Is pre ordered!
      WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

      // Return thank you page redirect
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url( $order )
      );

      return array(
        'result'   => 'fail',
        'redirect' => ''
      );
    }
    else {
      return parent::process_payment( $order_id );
    }
  }

  /**
   * Process the payment
   *
   * @param  int $order_id
   * @return array
   */
  public function process_payment( $order_id ) {
    // Processing subscription
    if( class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {
      return $this->process_subscription( $order_id );

    // Processing pre-order
    }
    else if( class_exists( 'WC_Pre_Orders_Order' ) && WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {
      return $this->process_pre_order( $order_id );

    // Processing regular product
    }
    else {
      return parent::process_payment( $order_id );
    }
  }

  /**
   * process_subscription_payment function.
   *
   * @param  WC_order $order
   * @param  integer $amount (default: 0)
   * @return bool|WP_Error
   */
  public function process_subscription_payment( $order = '', $amount = 0 ) {
    $order_items       = $order->get_items();
    $order_item        = array_shift( $order_items );
    $subscription_name = sprintf( __( '%s - Subscription for "%s"', 'woocommerce-payment-gateway-boilerplate' ), esc_html( get_bloginfo( 'name' ) ), $order_item['name'] ) . ' ' . sprintf( __( '(Order %s)', 'woocommerce-payment-gateway-boilerplate' ), $order->get_order_number() );

    if( $amount * 100 < 50 ) {
      return new WP_Error( 'simplify_error', __( 'Sorry, the minimum allowed order total is 0.50 to use this payment method.', 'woocommerce-payment-gateway-boilerplate' ) );
    }

    if( 'APPROVED' == $payment['status'] ) {
      // Payment complete
      $order->payment_complete( $payment->id );

      // Add order note
      $order->add_order_note( sprintf( __( 'Gateway name payment approved (ID: %s)', 'woocommerce-payment-gateway-boilerplate' ), $payment['id'] ) );

      return true;
    }
    else {
      $order->add_order_note( __( 'Gateway name payment declined', 'woocommerce-payment-gateway-boilerplate' ) );

      return new WP_Error( 'gateway_name_payment_declined', __( 'Payment was declined - please try again.', 'woocommerce-payment-gateway-boilerplate' ) );
    }
  }

  /**
   * scheduled_subscription_payment function.
   *
   * @param  float $amount_to_charge The amount to charge.
   * @param  WC_Order $order The WC_Order object of the order which the subscription was purchased in.
   * @param  int $product_id The ID of the subscription product for which this payment relates.
   * @return void
   */
  public function scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {
    $result = $this->process_subscription_payment( $order, $amount_to_charge );

    if( is_wp_error( $result ) ) {
      WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
    }
    else {
      WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );
    }
  }

  /**
   * Process a pre-order payment when the pre-order is released
   *
   * @param  WC_Order $order
   * @return wp_error|null
   */
  public function process_pre_order_release_payment( $order ) {
    try {
      $order_items    = $order->get_items();
      $order_item     = array_shift( $order_items );
      $pre_order_name = sprintf( __( '%s - Pre-order for "%s"', 'woocommerce-payment-gateway-boilerplate' ), esc_html( get_bloginfo( 'name' ) ), $order_item['name'] ) . ' ' . sprintf( __( '(Order %s)', 'woocommerce-payment-gateway-boilerplate' ), $order->get_order_number() );

      if( 'APPROVED' == $payment['status'] ) {
        // Payment complete
        $order->payment_complete( $payment->id );

        // Add order note
        $order->add_order_note( sprintf( __( 'Gateway name payment approved (ID: %s)', 'woocommerce-payment-gateway-boilerplate' ), $payment['id'] ) );
      }
      else {
        return new WP_Error( 'simplify_payment_declined', __( 'Payment was declined - the customer need to try another card.', 'woocommerce-payment-gateway-boilerplate' ) );
      }
    } catch ( Exception $e ) {
      $order_note = sprintf( __( 'Gateway name Transaction Failed (%s)', 'woocommerce-payment-gateway-boilerplate' ), $e->getMessage() );

      // Mark order as failed if not already set,
      // otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
      if( 'failed' != $order->status ) {
        $order->update_status( 'failed', $order_note );
      }
      else {
        $order->add_order_note( $order_note );
      }
    }
  }

}

?>