<?php
namespace BooklyChip\Frontend\Modules\Booking\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Booking\Proxy;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function preparePaymentOptions( $options, $form_id, $show_price, BooklyLib\CartInfo $cart_info, $userData )
    {
        $gateway = 'chip';
        if ( Proxy\CustomerGroups::allowedGateway( $gateway, $userData ) !== false ) {
            $cart_info->setGateway( $gateway );
            $url_cards_image = plugins_url( 'backend/modules/settings/resources/images/chip.svg', \BooklyChip\Lib\Plugin::getMainFile() );
            $options[ $gateway ] = array(
                'html' => self::renderTemplate(
                    'payment_option',
                    compact( 'form_id', 'url_cards_image', 'show_price', 'cart_info' ),
                    false
                ),
                'pay' => $cart_info->getPayNow(),
            );
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public static function booklyFormOptions( array $bookly_options )
    {
        $bookly_options['chip'] = array(
            'enabled' => (int) ( get_option( 'bookly_chip_enabled' ) ),
        );

        return $bookly_options;
    }

    /**
     * @inheritDoc
     */
    public static function stepOptions( array $options, $step, $userData )
    {
        if ( $step == 'payment' ) {
            // This will add chip_example_params to admin_ajax when customer are on payment step
            // $options['chip_example_params'] = 'example';
        }

        return $options;
    }
}