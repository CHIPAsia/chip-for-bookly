<?php
namespace BooklyChip\Lib;

use Bookly\Lib as BooklyLib;

class Installer extends Base\Installer
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $status = get_option( 'bookly_pmt_chip', '0' );
        $this->options = array(
            'bookly_chip_enabled'         => $status == 'disabled' ? '0' : $status,
            'bookly_chip_secret_key'      => get_option( 'bookly_pmt_chip_secret_key', '' ),
            'bookly_chip_brand_id'        => get_option( 'bookly_pmt_chip_brand_id', '' ),
            'bookly_chip_timeout'         => '0',
            'bookly_chip_increase'        => '0',
            'bookly_chip_addition'        => '0',
            'bookly_l10n_label_pay_chip'  => __( 'I will pay now with CHIP', 'bookly' ),
        );
    }
}