<?php
namespace BooklyChip\Backend\Modules\Settings\ProxyProviders;

use Bookly\Backend\Modules\Settings\Proxy;
use Bookly\Lib\Entities\Payment;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function preparePaymentGatewaySettings( $payment_data )
    {
        $type = Payment::TYPE_CHIP;
        $payment_data[ $type ] = self::renderTemplate( 'payment_settings', compact( 'type' ), false );

        return $payment_data;
    }

    /**
     * @inheritDoc
     */
    public static function saveSettings( array $alert, $tab, array $params )
    {
        if ( $tab == 'payments' ) {
            $options = array(
                'bookly_chip_enabled',
                'bookly_chip_secret_key',
                'bookly_chip_brand_id',
                'bookly_chip_increase',
                'bookly_chip_addition',
                'bookly_chip_timeout'
            );
            foreach ( $options as $option_name ) {
                if ( array_key_exists( $option_name, $params ) ) {
                    update_option( $option_name, trim( $params[ $option_name ] ) );
                }
            }
        }
    }
}