<img src="./assets/logo.svg" alt="drawing" width="50"/>

# CHIP for Bookly

This module adds CHIP payment method option to your [Bookly](https://codecanyon.net/item/bookly-booking-plugin-responsive-appointment-booking-and-scheduling/7226091) plugin.

## Installation

* [Download zip file of WooCommerce plugin.](https://github.com/CHIPAsia/chip-for-bookly/archive/refs/heads/main.zip)
* Log in to your Wordpress admin panel and go: **Plugins** -> **Add New**
* Select **Upload Plugin**, choose zip file you downloaded in step 1 and press **Install Now**
* Activate plugin

## Configuration

Set the **Brand ID** and **Secret Key** in the plugins settings.

Additional configuration are required at the moment until it is included together in Bookly plugin:

- Edit file *wp-content/plugins/bookly-responsive-appointment-booking-tool/lib/entities/Payment.php*:
    - Add class constant 
        ```php
        const TYPE_CHIP = 'chip';
        ```
    - Add case to switch statement in `typeToString` method 
        ```php
        case self::TYPE_CHIP:
        return 'CHIP';
        ```
    - Add constant to `getTypes` method
        ```php
        self::TYPE_CHIP
        ```

- Alter table `wp_bookly_payments` so that *chip* are added to _enum_:
    ```sql
    ALTER TABLE `wp_bookly_payments` CHANGE `type` `type` ENUM('chip','local','free','paypal','authorize_net','stripe','2checkout','payu_biz','payu_latam','payson','mollie','woocommerce','cloud_stripe','cloud_square') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'local';
    ```

- Edit file *bookly-responsive-appointment-booking-tool/lib/notifications/cart/Sender.php* (this is temporary until it get fixed by Bookly):
    - Comment line below by adding *//* in infront of the line:
        ```php
        //Proxy\Pro::sendCombinedToClient( false, $order );
        ```

## Other

Facebook: [Merchants & DEV Community](https://www.facebook.com/groups/3210496372558088)