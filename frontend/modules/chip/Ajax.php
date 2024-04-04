<?php
namespace BooklyChip\Frontend\Modules\Chip;

use Bookly\Lib as BooklyLib;
use Bookly\Frontend\Modules\Payment;
use BooklyChip\Lib\Payment\ChipGateway;

class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritDoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'anonymous' );
    }

    /**
     * Override parent method to exclude actions from CSRF token verification.
     *
     * @param string $action
     * @return bool
     */
    protected static function csrfTokenValid( $action = null )
    {
        $excluded_actions = array(
            'callback',
        );

        return in_array( $action, $excluded_actions ) || parent::csrfTokenValid( $action );
    }

    /**
     * WebHook endpoint to handle payment.
     */
    public static function callback()
    {
        $data = json_decode( file_get_contents( 'php://input' ), true );
        $response_code = 200;

        if ( $data && isset( $data['event_type'] ) && in_array( $data['event_type'], array( 'purchase.paid' ) ) ) {
            try {
                /** @var BooklyLib\Entities\Payment $payment */
                $payment = BooklyLib\Entities\Payment::query()->where( 'ref_id', $data['id'] )->findOne();

                if ( $payment && $payment->getStatus() === BooklyLib\Entities\Payment::STATUS_PENDING ) {
                    $chip = new ChipGateway( Payment\Request::getInstance() );
                    $chip->setPayment( $payment )->retrieve();
                }
            } catch ( \Exception $e ) {
                BooklyLib\Utils\Log::error( $e->getMessage(), $e->getFile(), $e->getLine() );
                $response_code = 400;
            }
        }
        BooklyLib\Utils\Common::emptyResponse( $response_code );
    }

}