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

validate_token_field();

$order_id = $_GET['id'];

// get order information
$query =
    "SELECT
        user_id,
        status
    FROM orders
    WHERE id = '" . escape($order_id) . "'";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

// if the order was not found, output error
if (mysqli_num_rows($result) == 0) {
    output_error('The order could not be found. <a href="javascript:history.go(-1)">Go back</a>.');
}

$row = mysqli_fetch_assoc($result);
$order_user_id = $row['user_id'];
$status = $row['status'];

// get user id for user
$query = "SELECT user_id FROM user WHERE user_username = '" . escape($_SESSION['sessionusername']) . "'";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
$row = mysqli_fetch_assoc($result);
$user_id = $row['user_id'];

// if order user id is different than user id, output error
if ($order_user_id != $user_id) {
    output_error('You do not have access to this order. <a href="javascript:history.go(-1)">Go back</a>.');
}

// if the order is complete, output error
if ($status != 'incomplete') {
    output_error('The order is complete. You may only delete incomplete orders. <a href="javascript:history.go(-1)">Go back</a>.');
}

// if the order that is being deleted is the active order then remove active order from session
if ($order_id == $_SESSION['ecommerce']['order_id']) {
    unset($_SESSION['ecommerce']['order_id']);
}

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

// prepare confirmation notice for my account screen
include_once('liveform.class.php');
$my_account = new liveform('my_account');
$my_account->add_notice('The order has been deleted.');

go(get_page_type_url('my account'));