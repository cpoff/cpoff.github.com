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

$liveform = new liveform('view_key_codes');

// store all values collected in request to session
foreach ($_REQUEST as $key => $value) {
    // if the value is a string then add it to the session
    // we have to do this check because cookie arrays are sometimes included in the $_REQUEST array,
    // for certain php.ini settings
    if (is_string($value) == TRUE) {
        $_SESSION['software']['ecommerce']['view_key_codes'][$key] = trim($value);
    }
}

// if the user clicked on the clear button, then clear the search
if (isset($_GET['clear']) == true) {
    $_SESSION['software']['ecommerce']['view_key_codes']['query'] = '';
}

switch ($_SESSION['software']['ecommerce']['view_key_codes']['sort']) {
    case 'Key Code':
        $sort_column = 'key_codes.code';
        break;

    case 'Offer Code':
        $sort_column = 'key_codes.offer_code';
        break;

    case 'Offer Message':
        $sort_column = 'offers.description';
        break;

    case 'Enabled':
        $sort_column = 'key_codes.enabled';
        break;

    case 'Expiration Date':
        $sort_column = 'key_codes.expiration_date';
        break;

    case 'Single-Use':
        $sort_column = 'key_codes.single_use';
        break;

    case 'Last Modified':
        $sort_column = 'key_codes.timestamp';
        break;

    default:
        $sort_column = 'key_codes.timestamp';
        $_SESSION['software']['ecommerce']['view_key_codes']['sort'] = 'Last Modified';
        $_SESSION['software']['ecommerce']['view_key_codes']['order'] = 'desc';
        break;
}

// if order is not set, set to ascending
if (isset($_SESSION['software']['ecommerce']['view_key_codes']['order']) == false) {
    $_SESSION['software']['ecommerce']['view_key_codes']['order'] = 'asc';
}

$all_key_codes = db_value("SELECT COUNT(*) FROM key_codes");

$where = "";

// if there is a search query and it is not blank, then prepare SQL
if ((isset($_SESSION['software']['ecommerce']['view_key_codes']['query']) == true) && ($_SESSION['software']['ecommerce']['view_key_codes']['query'] != '')) {
    $where .= "WHERE (LOWER(CONCAT_WS(',', key_codes.code, key_codes.offer_code, CAST(key_codes.expiration_date AS CHAR), last_modified_user.user_username)) LIKE '%" . e(mb_strtolower($_SESSION['software']['ecommerce']['view_key_codes']['query'])) . "%')";
}

// Get all key codes.
$key_codes = db_items(
    "SELECT
        key_codes.id,
        key_codes.code,
        key_codes.offer_code,
        key_codes.enabled,
        key_codes.expiration_date,
        key_codes.single_use,
        last_modified_user.user_username AS last_modified_username,
        key_codes.timestamp AS last_modified_timestamp,
        offers.id AS offer_id,
        offers.description AS offer_description,
        offers.status AS offer_status,
        offers.start_date AS offer_start_date,
        offers.end_date AS offer_end_date
    FROM key_codes
    LEFT JOIN offers ON key_codes.offer_code = offers.code
    LEFT JOIN user AS last_modified_user ON key_codes.user = last_modified_user.user_id
    $where
    ORDER BY $sort_column " . e($_SESSION['software']['ecommerce']['view_key_codes']['order']));

// Get the current date so that later we can figure out if key codes have expired.
$current_date = date('Y-m-d');

// Loop through the key codes in order to set the status for each one,
// based on enabled and expiration date fields.
foreach ($key_codes as $key => $key_code) {
    // If this key code is active, then store that, so a color can be used to indicate that.
    if (
        $key_code['enabled']
        &&
        (
            ($key_code['expiration_date'] == '0000-00-00')
            || ($key_code['expiration_date'] >= $current_date)
        )
        && ($key_code['offer_status'] == 'enabled')
        && ($key_code['offer_start_date'] <= $current_date)
        && ($key_code['offer_end_date'] >= $current_date)
    ) {
        $key_codes[$key]['status_enabled'] = true;
    }
}

echo output_header();

require('templates/view_key_codes.php');

echo output_footer();

$liveform->remove_form();

?>