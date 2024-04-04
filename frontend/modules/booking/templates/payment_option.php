<?php defined( 'ABSPATH' ) || exit; // Exit if accessed directly
use Bookly\Lib\Utils;
/** @var Bookly\Lib\CartInfo $cart_info */
?>
<div class="bookly-box bookly-list">
    <label>
        <input type="radio" class="bookly-js-payment" name="payment-method-<?php echo $form_id ?>" value="chip"/>
        <span><?php echo Utils\Common::getTranslatedOption( 'bookly_l10n_label_pay_chip' ) ?>
            <?php if ( $show_price ) : ?>
                <span class="bookly-js-pay"><?php echo Utils\Price::format( $cart_info->getPayNow() ) ?></span>
            <?php endif ?>
        </span>
        <img src="<?php echo $url_cards_image ?>" alt="cards"/>
    </label>
</div>