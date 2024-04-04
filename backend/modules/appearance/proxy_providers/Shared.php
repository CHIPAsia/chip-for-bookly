<?php
namespace BooklyChip\Backend\Modules\Appearance\ProxyProviders;

use Bookly\Backend\Modules\Appearance\Proxy;
use Bookly\Lib\Entities\Payment;
use BooklyChip\Lib\Plugin;

class Shared extends Proxy\Shared
{
    /**
     * @inheritDoc
     */
    public static function paymentGateways( $data )
    {
        $data[ Payment::TYPE_CHIP ] = array(
            'label_option_name' => 'bookly_l10n_label_pay_chip',
            'title' => 'Chip',
            'with_card' => false,
            'logo_url' => plugins_url( 'backend/modules/settings/resources/images/chip.svg', Plugin::getMainFile() ),
        );

        return $data;
    }

    /**
     * @inheritDoc
     */
    public static function prepareOptions( array $options_to_save, array $options )
    {
        return array_merge( $options_to_save, array_intersect_key( $options, array_flip( array(
            'bookly_l10n_label_pay_chip',
        ) ) ) );
    }
}