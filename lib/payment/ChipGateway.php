<?php
namespace BooklyChip\Lib\Payment;

// wp-content/plugins/bookly-responsive-appointment-booking-tool/lib/entities/Payment.php
// need to add
/**
 *  add class constant
 * const TYPE_CHIP = 'chip';
 * 
 * add in typeToString method
 * case self::TYPE_CHIP:
 *   return 'CHIP';
 * 
 * add self::TYPE_CHIP, in getTypes method
 * 
 * need to alter enum to include 'chip'
 * ALTER TABLE `wp_bookly_payments` CHANGE `type` `type` ENUM('chip','local','free','paypal','authorize_net','stripe','2checkout','payu_biz','payu_latam','payson','mollie','woocommerce','cloud_stripe','cloud_square') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'local';
 * 
 * in file bookly-responsive-appointment-booking-tool/lib/notifications/cart/proxy/Pro.php
 * need to add this method
 * 
 * public static function sendCombinedToClient( Order $order, array|bool $queue  ) {
 * }
 * 
 * or bookly-responsive-appointment-booking-tool/lib/notifications/cart/Sender.php
 * 
 * comment the line Proxy\Pro::sendCombinedToClient( false, $order );
 * or change from false, $order to $order, false like below
 * Proxy\Pro::sendCombinedToClient( $order, false );
 */

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\Payment;
use BooklyChip\Lib\Boot;

class ChipGateway extends BooklyLib\Base\Gateway
{
    protected $type = Payment::TYPE_CHIP;
    protected $on_site = false;
    protected $cached_api;

    /**
     * @inerhitDoc
     */
    protected function getCheckoutUrl( array $intent_data )
    {
      return $intent_data['target_url'];
    }

    /**
     * @inerhitDoc
     */
    protected function getInternalMetaData()
    {
        return array(
            'description' => $this->request->getUserData()->cart->getItemsTitle(),
            'customer' => $this->request->getUserData()->getFullName(),
            'email' => $this->request->getUserData()->getEmail(),
        );
    }

    /**
     * @inerhitDoc
     */
    protected function createGatewayIntent()
    {
        $chip = $this->init_chip_api();
        
        $userData = $this->request->getUserData();

        $chip_amount = $this->request->getCartInfo()->getGatewayAmount();
        if ( ! BooklyLib\Config::isZeroDecimalsCurrency() ) {
            $chip_amount *= 100;
        }

        $gateway_payment = $this->getPayment();

        $params = [
          'success_callback' => admin_url( 'admin-ajax.php?action=chip_for_bookly_callback' ),
          'success_redirect' => $this->getResponseUrl( self::EVENT_RETRIEVE ),
          'failure_redirect' => $this->getResponseUrl( self::EVENT_RETRIEVE ),
          'cancel_redirect'  => $this->getResponseUrl( self::EVENT_RETRIEVE ),
          'creator_agent'    => 'Bookly: ' . Boot::$module_version,
          // 'reference'        => BooklyLib\Entities\Order::find( $this->order->getOrderId() )->getToken(),
          // 'reference'        => 'Order:' . $this->order->getOrderId() . '|Payment:' . $gateway_payment->getId(),
          'reference'        => $gateway_payment->getId(),
          'purchase' => [
            'total_override' => round( $chip_amount ),
            'timezone'       => 'Asia/Kuala_Lumpur',
            'currency'       => get_option( 'bookly_pmt_currency' ),
            'products'       => [[         
                'name'     => substr( $userData->cart->getItemsTitle(), 0, 256 ),
                'price'    => round( $chip_amount ),
            ]],
          ],
          'brand_id' => get_option( 'bookly_chip_brand_id' ),
          'client' => [
            'email'     => $userData->getEmail(),
            'full_name' => substr( $userData->getFullName(), 0, 128 ),
          ],
        ];
        
        // $customer = $userData->getCustomer();
        // 'metadata' => $this->getMetaData(),

        $payment = $chip->create_payment( $params );

        if ( isset( $payment['checkout_url'] ) ) {
          return array(
            'ref_id' => $payment['id'],
            'target_url' => $payment['checkout_url'],
          );
        }

        throw new \Exception( 'Invalid response' );
    }

    /**
     * @inerhitDoc
     */
    public function retrieveStatus()
    {
        $chip = $this->init_chip_api();

        $gateway_payment = $this->getPayment();
        $payment = $chip->get_payment( $gateway_payment->getRefId() );

        if ( $payment ) {
            if ( $payment['status'] == 'paid' ) {
                if ( $gateway_payment->getStatus() !== BooklyLib\Entities\Payment::STATUS_COMPLETED ) {
                    $total = (float) $gateway_payment->getPaid();
                    $received = $payment['payment']['amount'];
                    if ( ! BooklyLib\Config::isZeroDecimalsCurrency() ) {
                        $total *= 100; // amount in cents
                    }
                    if ( abs( $received - $total ) <= 0.01 && BooklyLib\Config::getCurrency() == strtoupper( $payment['purchase']['currency'] ) ) {
                        return self::STATUS_COMPLETED;
                    }
                    return self::STATUS_PROCESSING;
                }
            } else {
              if ($payment['status'] != 'cancelled') {
                $chip->cancel_payment($payment['id']);
              }
            }
        }

        throw new \Exception();
    }

    private function init_chip_api()
    {
        include_once \BooklyChip\Lib\Plugin::getDirectory() . '/lib/payment/Chip/class-bookly-api.php';
        if ( !$this->cached_api ) {
          $this->cached_api = new \Chip_Bookly_API(
            get_option( 'bookly_chip_secret_key' ),
            get_option( 'bookly_chip_brand_id' )
          );
        }

        return $this->cached_api;
    }
}