<?php
namespace BooklyChip\Lib\Payment\ProxyProviders;

use Bookly\Frontend\Modules\Payment;
use Bookly\Lib\Payment\Proxy;
use Bookly\Lib\Entities;
use Bookly\Lib\CartInfo;
use BooklyChip\Lib\Payment\ChipGateway;

class Shared extends Proxy\Shared
{
    /**
     * @inerhitDoc
     */
    public static function getGatewayByName( $gateway, Payment\Request $request )
    {
        if ( $gateway === 'chip' ) {
            return new ChipGateway( $request );
        }

        return $gateway;
    }

    /**
     * @inheritDoc
     */
    public static function paymentSpecificPriceExists( $gateway )
    {
        if ( $gateway === 'chip' ) {
            return self::showPaymentSpecificPrices( false );
        }

        return $gateway;
    }

    /**
     * @inheritDoc
     */
    public static function applyGateway( CartInfo $cart_info, $gateway )
    {
        if ( $gateway === 'chip' ) {
            $cart_info->setGateway( $gateway );
        }

        return $cart_info;
    }

    /**
     * @inheritDoc
     */
    public static function prepareOutdatedUnpaidPayments( $payments )
    {
        $timeout = (int) get_option( 'bookly_chip_timeout' );
        if ( $timeout ) {
            $payments = array_merge( $payments, Entities\Payment::query()
                ->where( 'type', 'chip' )
                ->where( 'status', Entities\Payment::STATUS_PENDING )
                ->whereLt( 'created_at', date_create( current_time( 'mysql' ) )->modify( sprintf( '- %s seconds', $timeout ) )->format( 'Y-m-d H:i:s' ) )
                ->fetchCol( 'id' )
            );
        }

        return $payments;
    }

    /**
     * @inheritDoc
     */
    public static function showPaymentSpecificPrices( $show )
    {
        return $show ?: ( get_option( 'bookly_chip_increase' ) != 0 || get_option( 'bookly_chip_addition' ) != 0 );
    }
}