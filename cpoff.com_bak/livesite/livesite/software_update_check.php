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

function software_update_check() {

    // Update the config table to remember that the check has been completed today.
    db("UPDATE config SET last_software_update_check_timestamp = UNIX_TIMESTAMP()");

    if (!function_exists('curl_init')) {
        log_activity('daily software update check could not communicate with the software update server, because cURL is not installed, so it is not known if there is a software update available');
        return false;
    }

    $software_update_available = false;

    $request = array();

    $request['hostname'] = HOSTNAME_SETTING;
    $request['url'] = URL_SCHEME . HOSTNAME_SETTING . PATH;
    $request['version'] = VERSION;
    $request['edition'] = EDITION;
    $request['uname'] = php_uname();
    $request['os'] = PHP_OS;
    $request['web_server'] = $_SERVER['SERVER_SOFTWARE'];
    $request['php_version'] = phpversion();
    $request['mysql_version'] = db("SELECT VERSION()");
    $request['installer'] = INSTALLER;
    $request['private_label'] = PRIVATE_LABEL;

    // Get the timestamp for when the settings were last updated
    $request['settings_timestamp'] = db("SELECT last_modified_timestamp FROM config");

    $request['payment_gateway'] = ECOMMERCE_PAYMENT_GATEWAY;
    $request['paypal_express_checkout'] = ECOMMERCE_PAYPAL_EXPRESS_CHECKOUT;

    $request['theme_type'] = '';

    $theme_id = db("SELECT id FROM files WHERE activated_desktop_theme = '1'");

    if ($theme_id) {

        if (db("SELECT id FROM system_theme_css_rules WHERE file_id = '$theme_id' LIMIT 1")) {
            $request['theme_type'] = 'system';

        } else {
            $request['theme_type'] = 'custom';
        }

    }

    $request['pages'] = db("SELECT COUNT(*) FROM page");
    $request['pages_timestamp'] = db(
        "SELECT page_timestamp FROM page ORDER BY page_timestamp DESC LIMIT 1");

    $request['comments'] = db("SELECT COUNT(*) FROM comments");
    $request['comments_timestamp'] = db(
        "SELECT last_modified_timestamp FROM comments ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['files'] = db("SELECT COUNT(*) FROM files");
    $request['files_timestamp'] = db("SELECT timestamp FROM files ORDER BY timestamp DESC LIMIT 1");
    $request['files_size'] = db("SELECT SUM(size) FROM files");

    $request['events'] = db("SELECT COUNT(*) FROM calendar_events");
    $request['events_timestamp'] = db(
        "SELECT last_modified_timestamp FROM calendar_events
        ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['custom_forms'] = db("SELECT COUNT(*) FROM page WHERE page_type = 'custom form'");
    $request['custom_forms_timestamp'] = db(
        "SELECT page_timestamp FROM page WHERE page_type = 'custom form'
        ORDER BY page_timestamp DESC LIMIT 1");

    $request['submitted_forms'] = db("SELECT COUNT(*) FROM forms");
    $request['submitted_forms_timestamp'] = db(
        "SELECT last_modified_timestamp FROM forms ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['visitors'] = db(
        "SELECT COUNT(*) FROM visitors WHERE start_timestamp > '" . (time() - 86400) . "'");

    $request['contacts'] = db("SELECT COUNT(*) FROM contacts");
    $request['contacts_timestamp'] = db(
        "SELECT timestamp FROM contacts ORDER BY timestamp DESC LIMIT 1");

    $request['members'] = db("SELECT COUNT(*) FROM contacts WHERE member_id != ''");
    $request['members_timestamp'] = db(
        "SELECT timestamp FROM contacts WHERE member_id != '' ORDER BY timestamp DESC LIMIT 1");

    $request['affiliates'] = db("SELECT COUNT(*) FROM contacts WHERE affiliate_code != ''");
    $request['affiliates_timestamp'] = db(
        "SELECT timestamp FROM contacts WHERE affiliate_code != '' ORDER BY timestamp DESC LIMIT 1");

    $request['users'] = db("SELECT COUNT(*) FROM user");
    $request['users_timestamp'] = db(
        "SELECT user_timestamp FROM user ORDER BY user_timestamp DESC LIMIT 1");

    $request['editors'] = get_number_of_edit_users();

    $request['email_campaigns'] = db("SELECT COUNT(*) FROM email_campaigns");
    $request['email_campaigns_timestamp'] = db(
        "SELECT last_modified_timestamp FROM email_campaigns
        ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['email_recipients'] = db("SELECT COUNT(*) FROM email_recipients");
    $request['email_recipients_unique'] = NUMBER_OF_EMAIL_RECIPIENTS;

    $request['orders'] = db("SELECT COUNT(*) FROM orders WHERE status != 'incomplete'");
    $request['orders_timestamp'] = db(
        "SELECT last_modified_timestamp FROM orders WHERE status != 'incomplete'
        ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['products'] = db("SELECT COUNT(*) FROM products");
    $request['products_timestamp'] = db(
        "SELECT timestamp FROM products ORDER BY timestamp DESC LIMIT 1");
    $request['products_shippable'] = db("SELECT COUNT(*) FROM products WHERE shippable = '1'");

    $request['offers'] = db("SELECT COUNT(*) FROM offers");
    $request['offers_timestamp'] = db(
        "SELECT timestamp FROM offers ORDER BY timestamp DESC LIMIT 1");

    $request['ads'] = db("SELECT COUNT(*) FROM ads");
    $request['ads_timestamp'] = db(
        "SELECT last_modified_timestamp FROM ads ORDER BY last_modified_timestamp DESC LIMIT 1");

    $request['styles'] = db("SELECT COUNT(*) FROM style");
    $request['styles_timestamp'] = db(
        "SELECT style_timestamp FROM style ORDER BY style_timestamp DESC LIMIT 1");

    $request['common_regions'] = db("SELECT COUNT(*) FROM cregion");
    $request['common_regions_timestamp'] = db(
        "SELECT cregion_timestamp FROM cregion ORDER BY cregion_timestamp DESC LIMIT 1");

    $request['themes'] = db(
        "SELECT COUNT(*) FROM files
        WHERE
            (type = 'css')
            AND (design = '1')
            AND (theme = '1')");

    $request['themes_timestamp'] = db(
        "SELECT timestamp FROM files
        WHERE
            (type = 'css')
            AND (design = '1')
            AND (theme = '1')
        ORDER BY timestamp DESC LIMIT 1");

    $data = encode_json($request);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://livesite.com/software_update_check');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
    curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)));

    // if there is a proxy address, then send cURL request through proxy
    if (PROXY_ADDRESS != '') {
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($ch, CURLOPT_PROXY, PROXY_ADDRESS);
    }

    $response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        log_activity(
            'daily software update check could not communicate with the software update server, so it is not known if there is a software update available. cURL Error Number: ' . $curl_errno . '. cURL Error Message: ' . $curl_error . '.');
        return false;
    }

    $response = decode_json($response);

    if (!isset($response['version'])) {
        log_activity('daily software update check received an invalid response from the software update server, so it is not known if there is a software update available');
        return false;
    }
    
    // If the software update check is not disabled in the config.php file,
    // then continue to determine if there is a software update.
    if (
        (defined('SOFTWARE_UPDATE_CHECK') == FALSE)
        || (SOFTWARE_UPDATE_CHECK == TRUE)
    ) {
        // figure out if new version is greater than old version
        
        $new_version = trim($response['version']);
        $new_version_parts = explode('.', $new_version);
        
        $old_version = VERSION;
        $old_version_parts = explode('.', $old_version);
        
        // assume that new version is not greater than old version, until we find out otherwise
        $new_version_is_greater_than_old_version = FALSE;

        // if the major number of the new version is greater than the major number of the old version,
        // then the new version is greater than the old version
        if ($new_version_parts[0] > $old_version_parts[0]) {
            $new_version_is_greater_than_old_version = TRUE;
            
        // else if the major number of the new version is equal to the major number of the old version,
        // then continue to check
        } elseif ($new_version_parts[0] == $old_version_parts[0]) {
            // if the minor number of the new version is greater than the minor number of the old version,
            // then the new version is greater than the old version
            if ($new_version_parts[1] > $old_version_parts[1]) {
                $new_version_is_greater_than_old_version = TRUE;
                
            // else if the minor number of the new version is equal to the minor number of the old version,
            // then continue to check
            } elseif ($new_version_parts[1] == $old_version_parts[1]) {
                // if the maintenance number of the new version is greater than the maintenance number of the old version,
                // then the new version is greater than the old version
                if ($new_version_parts[2] > $old_version_parts[2]) {
                    $new_version_is_greater_than_old_version = TRUE;
                }
            }
        }

        // assume that there is not an available software update until we find out otherwise
        $software_update_available = 0;
        
        // if the new version is greater than the old version, then there is an available software update
        if ($new_version_is_greater_than_old_version == TRUE) {
            $software_update_available = 1;
        }
        
        // update database to remember whether there is a software update available or not
        db("UPDATE config SET software_update_available = '" . $software_update_available . "'");

        // Set value to boolean so we can return it later.
        settype($software_update_available, 'boolean');

    }

    // If messages were included in the response, then deal with them.
    if (isset($response['messages']) == true) {
        $messages = $response['messages'];

        // If the messages data is valid and formed a proper array, then continue.
        if (is_array($messages) == true) {
            // Delete current messages for this site, so we can later add new ones.
            db("DELETE FROM messages");

            $sql_exceptions = "";

            // Loop through new messages in order to add them to this site.
            foreach ($messages as $message) {
                db(
                    "INSERT INTO messages (
                        id,
                        title,
                        frequency,
                        url,
                        width,
                        height)
                    VALUES (
                        '" . e($message['id']) . "',
                        '" . e($message['title']) . "',
                        '" . e($message['frequency']) . "',
                        '" . e($message['url']) . "',
                        '" . e($message['width']) . "',
                        '" . e($message['height']) . "')");

                // Prepare sql exception statement so that we don't delete
                // user records for this message further below.

                if ($sql_exceptions != '') {
                    $sql_exceptions .= " AND ";
                }

                $sql_exceptions .= "(message_id != '" . e($message['id']) . "')";
            }

            $sql_where = "";

            // If there was at least one new message, then prepare where
            // clause to prevent deletion of records for active messages.
            if ($sql_exceptions != '') {
                $sql_where = "WHERE $sql_exceptions";
            }

            // Delete records of users viewing messages for messages
            // that no longer exist, because they are not needed anymore,
            // and we don't want that db table to get huge.
            db("DELETE FROM users_messages_xref $sql_where");
        }
    }

    return $software_update_available;

}