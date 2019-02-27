<?php

/**
 *
 * liveSite - Enterprise Website Platform
 * 
 * @author      Camelback Web Architects
 * @link        https://livesite.com
 * @copyright   2001-2017 Camelback Consulting, Inc.
 * @license     https://opensource.org/licenses/mit-license.html MIT License
 *
 */

include('init.php');
$user = validate_user();
validate_ecommerce_access($user);

validate_token_field();

include_once('liveform.class.php');

// if at least one order was selected
if ($_POST['orders']) {
    $number_of_orders = 0;
    
    switch ($_POST['action']) {
        // if the user selected to remove card data for orders, then do that
        case 'remove_card_data':
            // loop through all selected orders
            foreach ($_POST['orders'] as $order_id) {
                // get credit card data for order
                $query = 
                    "SELECT
                        card_number,
                        card_verification_number
                    FROM orders
                    WHERE id = '" . escape($order_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                $row = mysqli_fetch_assoc($result);
                $card_number = $row['card_number'];
                $card_verification_number = $row['card_verification_number'];
                
                // if there is a credit card number and the credit card number is not protected or there is a card verification number and it is not protected, then continue to remove credit card data
                if (
                    (($card_number != '') && (mb_substr($card_number, 0, 1) != '*'))
                    || (($card_verification_number != '') && (mb_substr($card_verification_number, 0, 1) != '*'))
                ) {
                    // if the credit card number is not already protected, then protect card number
                    if (mb_substr($card_number, 0, 1) != '*') {
                        // if the credit card number is encrypted, then decrypt it and then protect it
                        if (mb_strlen($card_number) > 16) {
                            // if encryption is enabled, then decrypt the credit card number
                            if (
                                (defined('ENCRYPTION_KEY') == TRUE)
                                && (extension_loaded('mcrypt') == TRUE)
                                && (in_array('rijndael-256', mcrypt_list_algorithms()) == TRUE)
                            ) {
                                $card_number = decrypt_credit_card_number($card_number, ENCRYPTION_KEY);
                                
                                // if the credit card number is not numeric, then there was a decryption error, so clear credit card number
                                if (is_numeric($card_number) == FALSE) {
                                    $card_number = '';
                                    
                                // else the decryption was successful, so protect credit card number
                                } else {
                                    $card_number = protect_credit_card_number($card_number);
                                }
                                
                            // else encryption is disabled, so clear credit card number
                            } else {
                                $card_number = '';
                            }
                            
                        // else the credit card number is not encrypted, so just protect it
                        } else {
                            $card_number = protect_credit_card_number($card_number);
                        }
                    }
                    
                    // if the card verification number is not already protected, then protect it
                    if (mb_substr($card_verification_number, 0, 1) != '*') {
                        $card_verification_number = protect_card_verification_number($card_verification_number);
                    }
                    
                    // update credit card data for order
                    $query =
                        "UPDATE orders
                        SET
                            card_number = '" . escape($card_number) . "',
                            card_verification_number = '" . escape($card_verification_number) . "'
                        WHERE id = '" . escape($order_id) . "'";
                    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                    
                    $number_of_orders++;
                }
            }
            
            // if credit card data was removed from more than 0 orders, then log activity
            if ($number_of_orders > 0) {
                log_activity('card data was removed from ' . number_format($number_of_orders) . ' order(s)', $_SESSION['sessionusername']);
            }
            
            $liveform_view_orders = new liveform('view_orders');
            $liveform_view_orders->add_notice('Card data was removed from ' . number_format($number_of_orders) . ' order(s).');
            
            break;
            
        // if orders are being deleted
        case 'delete':
            foreach ($_POST['orders'] as $order_id) {
                // delete order
                $query = "DELETE FROM orders WHERE id = '" . escape($order_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                
                // delete order items for order
                $query = "DELETE FROM order_items WHERE order_id = '" . escape($order_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

                // delete ship tos for order
                $query = "DELETE FROM ship_tos WHERE order_id = '" . escape($order_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

                db("DELETE FROM order_item_gift_cards WHERE order_id = '" . escape($order_id) . "'");
                
                db("DELETE FROM applied_gift_cards WHERE order_id = '" . escape($order_id) . "'");
                
                // Delete custom billing, shipping, and product form data for order.
                $query = "DELETE FROM form_data WHERE (order_id = '" . escape($order_id) . "') AND (order_id != '0')";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                
                // delete shipping tracking numbers for order
                $query = "DELETE FROM shipping_tracking_numbers WHERE order_id = '" . escape($order_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

                // Cancel order abandoned auto campaign for this order, if one exists.
                db(
                    "UPDATE email_campaigns
                    SET
                        email_campaigns.status = 'cancelled',
                        email_campaigns.last_modified_user_id = '',
                        email_campaigns.last_modified_timestamp = UNIX_TIMESTAMP()
                    WHERE
                        (email_campaigns.action = 'order_abandoned')
                        AND
                        (
                            (email_campaigns.status = 'ready')
                            OR (email_campaigns.status = 'paused')
                        )
                        AND (email_campaigns.order_id = '" . e($order_id) . "')");

                $number_of_orders++;
            }
            
            // if at least one order was deleted, then log activity
            if ($number_of_orders > 0) {
                log_activity(number_format($number_of_orders) . ' order(s) were deleted', $_SESSION['sessionusername']);
            }
            
            $liveform_view_orders = new liveform('view_orders');
            $liveform_view_orders->add_notice(number_format($number_of_orders) . ' order(s) were deleted.');
            
            break;
    }
}

header('Location: ' . URL_SCHEME . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/view_orders.php');
?>