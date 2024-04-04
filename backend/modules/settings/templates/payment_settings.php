<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings\Inputs;
use Bookly\Backend\Components\Settings\Payments;
use Bookly\Backend\Components\Settings\Selects;
use Bookly\Backend\Components\Controls\Elements;
use Bookly\Lib\Utils\DateTime;
use BooklyChip\Lib\Plugin;
?>

<div class="card bookly-collapse-with-arrow" data-gateway="<?php echo esc_attr( $type ) ?>">
    <div class="card-header d-flex align-items-center">
        <?php Elements::renderReorder() ?>
        <a href="#bookly_pmt_chip" class="ml-2" role="button" data-toggle="bookly-collapse">
            Chip
        </a>
        <img class="ml-auto" src="<?php echo plugins_url( 'backend/modules/settings/resources/images/chip.svg', Plugin::getMainFile() ) ?>" />
    </div>
    <div id="bookly_pmt_chip" class="bookly-collapse bookly-show">
        <div class="card-body">
            <?php Selects::renderSingle( 'bookly_chip_enabled', null, null, array(), array( 'data-expand' => '1' ) ) ?>
            <div class="bookly_chip_enabled-expander">
                <div class="form-group">
                    <h4><?php esc_html_e( 'Instructions', 'bookly' ) ?></h4>
                    <ol>
                        <li><?php printf( __( 'Provide <b>Secret key</b> and <b>Brand ID</b> which are available in the <a href="%s" target="_blank">Dashboard</a>.', 'bookly' ), 'https://gate.chip-in.asia' ) ?></li>
                        <li><?php printf( __( 'In the Dashboard\'s <b>Developer</b> section, click <b>Keys</b> to get Secret Key.', 'bookly' )) ?></li>
                        <li><?php printf( __( 'In the Dashboard\'s <b>Developer</b> section, click <b>Brands</b> to get Brand ID.', 'bookly' )) ?></li>
                    </ol>
                </div>
                <?php Inputs::renderText( 'bookly_chip_secret_key', __( 'Secret Key', 'bookly' ) ) ?>
                <?php Inputs::renderText( 'bookly_chip_brand_id', __( 'Brand ID', 'bookly' ) ) ?>
                <?php Payments::renderPriceCorrection( 'chip' ) ?>
                <?php
                $values = array( array( '0', __( 'OFF', 'bookly' ) ) );
                foreach ( array_merge( range( 1, 23, 1 ), range( 24, 168, 24 ), array( 336, 504, 672 ) ) as $hour ) {
                    $values[] = array( $hour * HOUR_IN_SECONDS, DateTime::secondsToInterval( $hour * HOUR_IN_SECONDS ) );
                }
                Selects::renderSingle( 'bookly_chip_timeout', __( 'Time interval of payment gateway', 'bookly' ), __( 'This setting determines the time limit after which the payment made via the payment gateway is considered to be incomplete. This functionality requires a scheduled cron job.', 'bookly' ), $values );
                ?>
            </div>
        </div>
    </div>
</div>