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

include_once('liveform.class.php');
$liveform = new liveform('edit_product');

if (!$_POST) {
    $query = "SELECT *
             FROM products
             WHERE id = '" . escape($_GET['id']) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed');
    $row = mysqli_fetch_assoc($result);

    $name = $row['name'];
    $enabled = $row['enabled'];
    $short_description = $row['short_description'];
    $full_description = $row['full_description'];
    $details = $row['details'];
    $code = $row['code'];
    $keywords = $row['keywords'];
    $image_name = $row['image_name'];
    $price = sprintf("%01.2lf", $row['price'] / 100);
    $taxable = $row['taxable'];
    $contact_group_id = $row['contact_group_id'];
    $order_receipt_bcc_email_address = $row['order_receipt_bcc_email_address'];
    $email_page = $row['email_page'];
    $email_bcc = $row['email_bcc'];
    $order_receipt_message = $row['order_receipt_message'];
    $required_product = $row['required_product'];
    $selection_type = $row['selection_type'];
    $default_quantity = $row['default_quantity'];
    $minimum_quantity = $row['minimum_quantity'];
    $maximum_quantity = $row['maximum_quantity'];
    $address_name = $row['address_name'];
    $title = $row['title'];
    $meta_description = $row['meta_description'];
    $meta_keywords = $row['meta_keywords'];
    $inventory = $row['inventory'];
    $inventory_quantity = $row['inventory_quantity'];
    $backorder = $row['backorder'];
    $out_of_stock_message = $row['out_of_stock_message'];
    $shippable = $row['shippable'];

    $weight = '';

    if ($row['weight'] > 0) {
        $weight = $row['weight']+0;
    }

    $primary_weight_points = $row['primary_weight_points'];
    $secondary_weight_points = $row['secondary_weight_points'];

    $length = '';

    if ($row['length'] > 0) {
        $length = $row['length']+0;
    }

    $width = '';

    if ($row['width'] > 0) {
        $width = $row['width']+0;
    }

    $height = '';

    if ($row['height'] > 0) {
        $height = $row['height']+0;
    }

    $container_required = $row['container_required'];
    $preparation_time = $row['preparation_time'];
    $free_shipping = $row['free_shipping'];
    $extra_shipping_cost = sprintf("%01.2lf", $row['extra_shipping_cost'] / 100);
    $commissionable = $row['commissionable'];
    $commission_rate_limit = $row['commission_rate_limit'];
    $recurring = $row['recurring'];
    $recurring_schedule_editable_by_customer = $row['recurring_schedule_editable_by_customer'];
    $start = $row['start'];
    $number_of_payments = $row['number_of_payments'];
    $payment_period = $row['payment_period'];
    $recurring_profile_disabled_perform_actions = $row['recurring_profile_disabled_perform_actions'];
    $recurring_profile_disabled_expire_membership = $row['recurring_profile_disabled_expire_membership'];
    $recurring_profile_disabled_revoke_private_access = $row['recurring_profile_disabled_revoke_private_access'];
    $recurring_profile_disabled_email = $row['recurring_profile_disabled_email'];
    $recurring_profile_disabled_email_subject = $row['recurring_profile_disabled_email_subject'];
    $recurring_profile_disabled_email_page_id = $row['recurring_profile_disabled_email_page_id'];
    $sage_group_id = $row['sage_group_id'];
    $membership_renewal = $row['membership_renewal'];
    $grant_private_access = $row['grant_private_access'];
    $private_folder = $row['private_folder'];
    $private_days = $row['private_days'];
    $send_to_page = $row['send_to_page'];
    $reward_points = $row['reward_points'];
    $gift_card = $row['gift_card'];
    $gift_card_email_subject = $row['gift_card_email_subject'];
    $gift_card_email_format = $row['gift_card_email_format'];
    $gift_card_email_body = $row['gift_card_email_body'];
    $gift_card_email_page_id = $row['gift_card_email_page_id'];
    $submit_form = $row['submit_form'];
    $submit_form_custom_form_page_id = $row['submit_form_custom_form_page_id'];
    $submit_form_quantity_type = $row['submit_form_quantity_type'];
    $submit_form_create = $row['submit_form_create'];
    $submit_form_update = $row['submit_form_update'];
    $submit_form_update_where_field = $row['submit_form_update_where_field'];
    $submit_form_update_where_value = $row['submit_form_update_where_value'];
    $add_comment = $row['add_comment'];
    $add_comment_page_id = $row['add_comment_page_id'];
    $add_comment_message = $row['add_comment_message'];
    $add_comment_name = $row['add_comment_name'];
    $add_comment_only_for_submit_form_update = $row['add_comment_only_for_submit_form_update'];
    $product_form = $row['form'];
    $form_name = $row['form_name'];
    $form_label_column_width = $row['form_label_column_width'];
    $form_quantity_type = $row['form_quantity_type'];
    $custom_field_1 = $row['custom_field_1'];
    $custom_field_2 = $row['custom_field_2'];
    $custom_field_3 = $row['custom_field_3'];
    $custom_field_4 = $row['custom_field_4'];
    $notes = $row['notes'];
    $google_product_category = $row['google_product_category'];
    $gtin = $row['gtin'];
    $brand = $row['brand'];
    $mpn = $row['mpn'];
    $seo_score = $row['seo_score'];

    $output_enabled_checked = '';

    if ($enabled == 1) {
        $output_enabled_checked = ' checked="checked"';
    }

    // if taxable is on
    if ($taxable == 1) {
        $taxable_checked = 'checked="checked"';
    // else taxable is not on
    } else {
        $taxable_checked = '';
    }

    // if shippable is on
    if ($shippable == 1) {
        $shippable_checked = 'checked="checked"';
    // else shippable is not on
    } else {
        $shippable_checked = '';
    }

    $container_required_checked = '';

    if ($container_required) {
        $container_required_checked = ' checked="checked"';
    }

    // if free shipping is on
    if ($free_shipping == 1) {
        $free_shipping_checked = 'checked="checked"';
    // else free shipping is not on
    } else {
        $free_shipping_checked = '';
    }

    // if shipping is not on, hide shippable row
    if (ECOMMERCE_SHIPPING == false) {
        $shippable_row_style = 'display: none';
    }

    $container_required_row_style = '';

    // if shipping is not on or product is not shippable, hide shipping options
    if ((ECOMMERCE_SHIPPING == false) || ($shippable == 0)) {
        $weight_row_style = 'display: none';
        $primary_weight_points_row_style = 'display: none';
        $secondary_weight_points_row_style = 'display: none';
        $dimensions_row_style = 'display: none';
        $container_required_row_style = 'display: none';
        $preparation_time_row_style = 'display: none';
        $free_shipping_row_style = 'display: none';
        $extra_shipping_cost_row_style = 'display: none';
        $allowed_zones_row_style = 'display: none';
        
    // else if free shipping is on, then hide extra shipping cost field
    } elseif ($free_shipping == 1) {
        $extra_shipping_cost_row_style = 'display: none';
    }

    $zones = array();

    // get all zones for zones selection
    $query = "SELECT id, name FROM zones ORDER BY name";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    while ($row = mysqli_fetch_assoc($result)) {
        $zones[] = array('id'=>$row['id'], 'name'=>$row['name']);
    }
    
    $allowed_zones = array();
    $disallowed_zones = array();

    // foreach zone, check if zone is allowed or disallowed for this product
    foreach ($zones as $key => $value) {
        $query = "SELECT zone_id FROM products_zones_xref WHERE product_id = '" . escape($_GET['id']) . "' AND zone_id = '" . $zones[$key]['id'] . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // if product and zone were found
        if (mysqli_num_rows($result)) {
            $allowed_zones[] = $zones[$key];
        } else {
            $disallowed_zones[] = $zones[$key];
        }
    }

    // foreach allowed zone prepare option
    foreach ($allowed_zones as $key => $value) {
        $output_allowed_zones .= '<option value="' . $allowed_zones[$key]['id'] . '">' . h($allowed_zones[$key]['name']) . '</option>';
    }

    // foreach disallowed zone prepare option
    foreach ($disallowed_zones as $key => $value) {
        $output_disallowed_zones .= '<option value="' . $disallowed_zones[$key]['id'] . '">' . h($disallowed_zones[$key]['name']) . '</option>';
    }
    
    // if the affiliate program is enabled, prepare affiliate program output
    if (AFFILIATE_PROGRAM == true) {
        if ($commissionable == 1) {
            $commissionable_checked = 'checked="checked"';
        } else {
            $commissionable_checked = '';
            $commission_rate_limit_row_style = 'display: none';
        }
        
        // clear affiliate commission rate if it is 0
        if ($commission_rate_limit == 0) {
            $commission_rate_limit = '';
        }
        
        $output_commissionable =
            '<tr>
                <td>Commissionable:</td>
                <td><input type="checkbox" name="commissionable" id="commissionable" value="1"' . $commissionable_checked . ' class="checkbox" onclick="show_or_hide_commissionable()" /></td>
            </tr>
            <tr id="commission_rate_limit_row" style="' . $commission_rate_limit_row_style . '">
                <td style="padding-left: 2em">Commission Rate Limit:</td>
                <td><input type="text" name="commission_rate_limit" value="' . $commission_rate_limit . '" size="6" maxlength="6" /> % (leave blank for no limit)</td>
            </tr>';
    }
    
    // assume that recurring check box should not be checked until we find out otherwise
    $recurring_checked = '';
    
    // assume that fields should be hidden until we find out otherwise
    $recurring_schedule_editable_by_customer_row_style = 'display: none';
    $recurring_schedule_editable_by_customer_message_style = 'display: none';
    $start_row_style = 'display: none';
    $number_of_payments_row_style = 'display: none';
    $payment_period_row_style = 'display: none';
    
    // if recurring is on, then show recurring payment options
    if ($recurring == 1) {
        $recurring_checked = ' checked="checked"';
        $recurring_schedule_editable_by_customer_row_style = '';
        $start_row_style = '';
        $number_of_payments_row_style = '';
        $payment_period_row_style = '';
    }
    
    // assume that the recurring_schedule_editable_by_customer check box should not be checked until we find out otherwise
    $recurring_schedule_editable_by_customer_checked = '';
    
    // if recurring_schedule_editable_by_customer is on, then check check box
    if ($recurring_schedule_editable_by_customer == 1) {
        $recurring_schedule_editable_by_customer_checked = ' checked="checked"';
        $recurring_schedule_editable_by_customer_message_style = '';
    }
    
    // determine if start row should be outputted
    $output_start_row = '';
    
    // if payment gateway is not ClearCommerce, then prepare to output start row
    if (ECOMMERCE_PAYMENT_GATEWAY != 'ClearCommerce') {
        $output_start_row =
            '<tr id="start_row" style="' . $start_row_style . '">
                <td style="padding-left: 2em">Start (days):</td>
                <td><input type="text" name="start" value="' . $start . '" size="7" maxlength="7" /> day(s) from order date. (0 to start immediately)</td>
            </tr>';
    }
    
    // set number of payments to empty string if value is 0
    if ($number_of_payments == 0) {
        $number_of_payments = '';
    }
    
    // determine if recurring profile disabled rows should be outputted
    $output_recurring_profile_disabled_rows = '';
    
    // if credit/debit card payment method is enabled and payment gateway is PayPal Payments Pro, then prepare to output recurring profile disabled rows
    if ((ECOMMERCE_CREDIT_DEBIT_CARD == true) && (ECOMMERCE_PAYMENT_GATEWAY == 'PayPal Payments Pro')) {
        // assume that we will not show any recurring profile disabled fields until we find out otherwise
        $recurring_profile_disabled_perform_actions_row_style = 'display: none';
        $recurring_profile_disabled_expire_membership_row_style = 'display: none';
        $recurring_profile_disabled_revoke_private_access_row_style = 'display: none';
        $recurring_profile_disabled_email_row_style = 'display: none';
        $recurring_profile_disabled_email_subject_row_style = 'display: none';
        $recurring_profile_disabled_email_page_id_row_style = 'display: none';
        
        // if recurring is on, then show profile disabled fields
        if ($recurring == 1) {
            $recurring_profile_disabled_perform_actions_row_style = '';
            
            // if perform actions is on, then show perform action fields
            if ($recurring_profile_disabled_perform_actions == 1) {
                $recurring_profile_disabled_expire_membership_row_style = '';
                $recurring_profile_disabled_revoke_private_access_row_style = '';
                $recurring_profile_disabled_email_row_style = '';
                
                // if e-mail is on, then show e-mail fields
                if ($recurring_profile_disabled_email == 1) {
                    $recurring_profile_disabled_email_subject_row_style = '';
                    $recurring_profile_disabled_email_page_id_row_style = '';
                }
            }
        }
        
        $recurring_profile_disabled_perform_actions_checked = '';
        
        // if perform actions is on, then check check box
        if ($recurring_profile_disabled_perform_actions == 1) {
            $recurring_profile_disabled_perform_actions_checked = ' checked="checked"';
        }
        
        $recurring_profile_disabled_expire_membership_checked = '';
        
        // if expire membership is on, then check check box
        if ($recurring_profile_disabled_expire_membership == 1) {
            $recurring_profile_disabled_expire_membership_checked = ' checked="checked"';
        }
        
        $recurring_profile_disabled_revoke_private_access_checked = '';
        
        // if revoke private access is on, then check check box
        if ($recurring_profile_disabled_revoke_private_access == 1) {
            $recurring_profile_disabled_revoke_private_access_checked = ' checked="checked"';
        }
        
        $recurring_profile_disabled_email_checked = '';
        
        // if e-mail is on, then check check box
        if ($recurring_profile_disabled_email == 1) {
            $recurring_profile_disabled_email_checked = ' checked="checked"';
        }
        
        $output_recurring_profile_disabled_rows =
            '<tr id="recurring_profile_disabled_perform_actions_row" style="' . $recurring_profile_disabled_perform_actions_row_style . '">
                <td style="padding-left: 2em"><label for="recurring_profile_disabled_perform_actions">Perform action(s) if profile is disabled:</label></td>
                <td><input type="checkbox" name="recurring_profile_disabled_perform_actions" id="recurring_profile_disabled_perform_actions" value="1"' . $recurring_profile_disabled_perform_actions_checked . ' class="checkbox" onclick="show_or_hide_recurring_profile_disabled_perform_actions()" /> (requires recurring payment job)</td>
            </tr>
            <tr id="recurring_profile_disabled_expire_membership_row" style="' . $recurring_profile_disabled_expire_membership_row_style . '">
                <td style="padding-left: 40px"><label for="recurring_profile_disabled_expire_membership">Expire Membership:</label></td>
                <td><input type="checkbox" name="recurring_profile_disabled_expire_membership" id="recurring_profile_disabled_expire_membership" value="1"' . $recurring_profile_disabled_expire_membership_checked . ' class="checkbox" /></td>
            </tr>
            <tr id="recurring_profile_disabled_revoke_private_access_row" style="' . $recurring_profile_disabled_revoke_private_access_row_style . '">
                <td style="padding-left: 40px"><label for="recurring_profile_disabled_revoke_private_access">Revoke Private Access:</label></td>
                <td><input type="checkbox" name="recurring_profile_disabled_revoke_private_access" id="recurring_profile_disabled_revoke_private_access" value="1"' . $recurring_profile_disabled_revoke_private_access_checked . ' class="checkbox" /></td>
            </tr>
            <tr id="recurring_profile_disabled_email_row" style="' . $recurring_profile_disabled_email_row_style . '">
                <td style="padding-left: 40px"><label for="recurring_profile_disabled_email">Send E-mail to Customer:</label></td>
                <td><input type="checkbox" name="recurring_profile_disabled_email" id="recurring_profile_disabled_email" value="1"' . $recurring_profile_disabled_email_checked . ' class="checkbox" onclick="show_or_hide_recurring_profile_disabled_email()" /></td>
            </tr>
            <tr id="recurring_profile_disabled_email_subject_row" style="' . $recurring_profile_disabled_email_subject_row_style . '">
                <td style="padding-left: 60px">Subject:</td>
                <td><input type="text" name="recurring_profile_disabled_email_subject" value="' . h($recurring_profile_disabled_email_subject) . '" maxlength="255" size="40" /></td>
            </tr>
            <tr id="recurring_profile_disabled_email_page_id_row" style="' . $recurring_profile_disabled_email_page_id_row_style . '">
                <td style="padding-left: 60px">Page:</td>
                <td><select name="recurring_profile_disabled_email_page_id"><option value="">-None-</option>' .  select_page($recurring_profile_disabled_email_page_id) . '</select></td>
            </tr>';
    }
    
    // determine if Sage group ID row should be outputted
    $output_sage_group_id_row = '';
    
    // if credit/debit card payment method is enabled and payment gateway is Sage, then output Sage group ID row
    if ((ECOMMERCE_CREDIT_DEBIT_CARD == TRUE) && (ECOMMERCE_PAYMENT_GATEWAY == 'Sage')) {
        // assume that we will not show Sage group ID field until we find out otherwise
        $sage_group_id_row_style = 'display: none';
        
        // if recurring is on, then show Sage group ID field
        if ($recurring == 1) {
            $sage_group_id_row_style = '';
        }
        
        $output_sage_group_id_row =
            '<tr id="sage_group_id_row" style="' . $sage_group_id_row_style . '">
                <td style="padding-left: 2em">Sage Group ID:</td>
                <td><input type="text" name="sage_group_id" value="' . $sage_group_id . '" size="10" maxlength="9" /></td>
            </tr>';
    }

    if ($minimum_quantity == 0) {
        $minimum_quantity = '';
    }

    if ($maximum_quantity == 0) {
        $maximum_quantity = '';
    }

    $output_attributes = '';

    // Get product attributes.
    $attributes = db_items(
        "SELECT
            id,
            name
        FROM product_attributes
        ORDER BY name", 'id');

    // If there are attributes, then get options, selected attributes, and output attribute area.
    if ($attributes) {
        $attribute_options = db_items(
            "SELECT
                id,
                product_attribute_id,
                label
            FROM product_attribute_options
            ORDER BY
                product_attribute_id,
                sort_order");

        // Loop through the options in order to add them to the attributes array.
        foreach ($attribute_options as $attribute_option) {
            $attributes[$attribute_option['product_attribute_id']]['options'][] = $attribute_option;
        }

        // Get the selected attributes for this product.
        $selected_attributes = db_items(
            "SELECT
                attribute_id,
                option_id
            FROM products_attributes_xref
            WHERE product_id = '" . e($_GET['id']) . "'
            ORDER BY sort_order");

        // We use array_values() below so that the array is treated as an array
        // and not an object in js, in order to maintain order of the attributes.
        $output_attributes =
            '<tr>
                <th colspan="2"><h2>Attributes</h2></th>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="attributes"></div>
                    <script>
                        init_product_attributes({
                            attributes: ' . encode_json(array_values($attributes)) . ',
                            selected_attributes: ' . encode_json($selected_attributes) . '});
                    </script>
                </td>
            </tr>';
    }
    
    $inventory_checked = '';
    $inventory_quantity_row_style = 'display: none';
    $backorder_row_style = 'display: none';
    $out_of_stock_message_row_style = 'display: none';
    
    // if inventory is enabled, then check check box and show other fields
    if ($inventory == 1) {
        $inventory_checked = ' checked="checked"';
        $inventory_quantity_row_style = '';
        $backorder_row_style = '';
        $out_of_stock_message_row_style = '';
    }
    
    $backorder_checked = '';
    
    // if backorder is enabled, then check check box
    if ($backorder == 1) {
        $backorder_checked = ' checked="checked"';
    }
    
    // if grant private access is set for the product, then show grant private access options
    if ($grant_private_access == 1) {
        $grant_private_access_checked = 'checked="checked"';
    // else grant private access is not set for the product, so hide grant private access options
    } else {
        $private_folder_row_style = 'display: none';
        $private_days_row_style = 'display: none';
        $send_to_page_row_style = 'display: none';
    }

    // If private days is 0, then set value to blank.
    if ($private_days == 0) {
        $private_days = '';
    }

    $gift_card_checked = '';
    $gift_card_email_subject_row_style = ' style="display: none"';
    $gift_card_email_format_row_style = ' style="display: none"';
    $gift_card_email_body_row_style = ' style="display: none"';
    $gift_card_email_page_id_row_style = ' style="display: none"';

    // If gift card is enabled, then check check box and show related rows.
    if ($gift_card == 1) {
        $gift_card_checked = ' checked="checked"';
        $gift_card_email_subject_row_style = '';
        $gift_card_email_format_row_style = '';

        if ($gift_card_email_format == 'plain_text') {
            $gift_card_email_body_row_style = '';
        } else {
            $gift_card_email_page_id_row_style = '';
        }
    }

    $gift_card_email_format_plain_text_checked = '';
    $gift_card_email_format_html_checked = '';

    if ($gift_card_email_format == 'plain_text') {
        $gift_card_email_format_plain_text_checked = ' checked="checked"';
    } else {
        $gift_card_email_format_html_checked = ' checked="checked"';
    }

    $submit_form_checked = '';
    $submit_form_custom_form_page_id_row_style = ' style="display: none"';
    $submit_form_create_row_style = ' style="display: none"';
    $submit_form_create_fields_row_style = ' style="display: none"';
    $submit_form_update_row_style = ' style="display: none"';
    $submit_form_update_fields_row_style = ' style="display: none"';

    // If submit form is enabled, then check check box and show related rows.
    if ($submit_form == 1) {
        $submit_form_checked = ' checked="checked"';
        $submit_form_custom_form_page_id_row_style = '';
        $submit_form_create_row_style = '';

        // If submit form create is enabled, then show related rows.
        if ($submit_form_create == 1) {
            $submit_form_create_fields_row_style = '';
        }

        $submit_form_update_row_style = '';

        // If submit form update is enabled, then show related rows.
        if ($submit_form_update == 1) {
            $submit_form_update_fields_row_style = '';
        }
    }

    if ($submit_form_quantity_type == 'One Form per Quantity') {
        $submit_form_quantity_type_one_form_per_quantity_checked = ' checked="checked"';
        $submit_form_quantity_type_one_form_per_product_checked = '';
        
    } else {
        $submit_form_quantity_type_one_form_per_product_checked = ' checked="checked"';
        $submit_form_quantity_type_one_form_per_quantity_checked = '';
    }

    $submit_form_create_checked = '';

    // If submit form create is enabled, then check check box.
    if ($submit_form_create == 1) {
        $submit_form_create_checked = ' checked="checked"';
    }

    $submit_form_update_checked = '';

    // If submit form update is enabled, then check check box.
    if ($submit_form_update == 1) {
        $submit_form_update_checked = ' checked="checked"';
    }

    // Get submit form fields, in order to prepare JavaScript that will add fields to screen.
    $submit_form_fields = db_items(
        "SELECT
            action,
            form_field_id,
            value
        FROM product_submit_form_fields
        WHERE product_id = '" . escape($_GET['id']) . "'
        ORDER BY id");

    $output_submit_form_create_javascript = '';
    $output_submit_form_update_javascript = '';

    foreach ($submit_form_fields as $submit_form_field) {
        if ($submit_form_field['action'] == 'create') {
            $output_submit_form_create_javascript .= 'product_submit_form_add_field(' . encode_json($submit_form_field) . ');' . "\n";
        } else {
            $output_submit_form_update_javascript .= 'product_submit_form_add_field(' . encode_json($submit_form_field) . ');' . "\n";
        }
    }

    $add_comment_checked = '';
    $add_comment_page_id_row_style = ' style="display: none"';
    $add_comment_message_row_style = ' style="display: none"';
    $add_comment_name_row_style = ' style="display: none"';
    $add_comment_only_for_submit_form_update_row_style = ' style="display: none"';

    // If add comment is enabled, then check check box and show related rows.
    if ($add_comment == 1) {
        $add_comment_checked = ' checked="checked"';
        $add_comment_page_id_row_style = '';
        $add_comment_message_row_style = '';
        $add_comment_name_row_style = '';
        $add_comment_message_row_style = '';
        $add_comment_only_for_submit_form_update_row_style = '';
    }

    $add_comment_only_for_submit_form_update_checked = '';

    // If only for submit form is enabled, then check check box.
    if ($add_comment_only_for_submit_form_update == 1) {
        $add_comment_only_for_submit_form_update_checked = ' checked="checked"';
    }
    
    // if the product_form checkbox is checked, display rows
    if ($product_form == 1) {
        $product_form_checked = 'checked="checked"';
        $form_name_row_style = '';
        $form_label_column_width_row_style = '';
        $form_quantity_type_row_style = '';
        $form_designer_row_style = '';
        $output_product_form_designer_button = '<a href="view_fields.php?product_id=' . h(urlencode($_REQUEST['id'])) . '">Edit Product Form</a>';
        
    // else, do not display the rows
    } else {
        $product_form_checked = '';
        $form_name_row_style = 'display: none';
        $form_label_column_width_row_style = 'display: none';
        $form_quantity_type_row_style = 'display: none';
        $form_designer_row_style = 'display: none';
        $output_product_form_designer_button = '';
    }
    
    // if form_quantity_type is set to One Form per Quantity, select it
    if ($form_quantity_type == 'One Form per Quantity') {
        $form_quantity_type_one_form_per_quantity_checked = 'checked="checked"';
        $form_quantity_type_one_form_per_product_checked = '';
        
    // else, form_quantity_type must be set to One Form per Product
    } else {
        $form_quantity_type_one_form_per_product_checked = 'checked="checked"';
        $form_quantity_type_one_form_per_quantity_checked = '';
    }

    $output_custom_product_field_rows = '';

    // If there is at least one active custom product field, then output area for that.
    if (
        (ECOMMERCE_CUSTOM_PRODUCT_FIELD_1_LABEL != '')
        || (ECOMMERCE_CUSTOM_PRODUCT_FIELD_2_LABEL != '')
        || (ECOMMERCE_CUSTOM_PRODUCT_FIELD_3_LABEL != '')
        || (ECOMMERCE_CUSTOM_PRODUCT_FIELD_4_LABEL != '')
    ) {
        $output_custom_product_field_rows .=
            '<tr>
                    <th colspan="2"><h2>Custom Product Fields</h2></th>
            </tr>';

        // If the first custom product field is active, then output row for it.
        if (ECOMMERCE_CUSTOM_PRODUCT_FIELD_1_LABEL != '') {
            $output_custom_product_field_rows .=
                '<tr>
                    <td>' . h(ECOMMERCE_CUSTOM_PRODUCT_FIELD_1_LABEL) . ':</td>
                    <td><input type="text" name="custom_field_1" value="' . h($custom_field_1) . '" size="80" maxlength="255" /></td>
                </tr>';
        }

        // If the second custom product field is active, then output row for it.
        if (ECOMMERCE_CUSTOM_PRODUCT_FIELD_2_LABEL != '') {
            $output_custom_product_field_rows .=
                '<tr>
                    <td>' . h(ECOMMERCE_CUSTOM_PRODUCT_FIELD_2_LABEL) . ':</td>
                    <td><input type="text" name="custom_field_2" value="' . h($custom_field_2) . '" size="80" maxlength="255" /></td>
                </tr>';
        }

        // If the third custom product field is active, then output row for it.
        if (ECOMMERCE_CUSTOM_PRODUCT_FIELD_3_LABEL != '') {
            $output_custom_product_field_rows .=
                '<tr>
                    <td>' . h(ECOMMERCE_CUSTOM_PRODUCT_FIELD_3_LABEL) . ':</td>
                    <td><input type="text" name="custom_field_3" value="' . h($custom_field_3) . '" size="80" maxlength="255" /></td>
                </tr>';
        }

        // If the fourth custom product field is active, then output row for it.
        if (ECOMMERCE_CUSTOM_PRODUCT_FIELD_4_LABEL != '') {
            $output_custom_product_field_rows .=
                '<tr>
                    <td>' . h(ECOMMERCE_CUSTOM_PRODUCT_FIELD_4_LABEL) . ':</td>
                    <td><input type="text" name="custom_field_4" value="' . h($custom_field_4) . '" size="80" maxlength="255" /></td>
                </tr>';
        }
    }
    
    print
        output_header() . '
        <div id="subnav">
            <h1>' . h($short_description) . '</h1>
            <p class="subheading">Product ID: ' . h($name) . '</p>
        </div>
        <div id="button_bar">
            ' . $output_product_form_designer_button . '
            <a href="duplicate_product.php?id=' . h($_GET['id']) . get_token_query_string_field() . '">Duplicate</a>
        </div>
        <div id="content">
            
            ' . $liveform->output_errors() . '
            ' . $liveform->output_notices() . '
            <a href="#" id="help_link">Help</a>
            <h1>Edit Product</h1>
            <div class="subheading">Edit shippable product, downloadable product, donation, recurring fee, membership dues, or payment.</div>
            ' . get_wysiwyg_editor_code(array('order_receipt_message', 'full_description', 'details', 'out_of_stock_message')) . '
            <form name="form" action="edit_product.php" method="post" onsubmit="prepare_selects(new Array(\'allowed_zones\'))" class="product_form">
                ' . get_token_field() . '
                <input type="hidden" name="send_to" value="' . h($_GET['send_to']) . '" />
                <table class="field">
                    <tr>
                        <th colspan="2"><h2>Product Information</h2></th>
                    </tr>
                    <tr>
                        <td>Product ID / SKU:</td>
                        <td><input type="text" name="name" value="' . h($name) . '" /></td>
                    </tr>
                    <tr>
                        <td><label for="enabled">Enable:</label></td>
                        <td><input type="checkbox" id="enabled" name="enabled" value="1"' . $output_enabled_checked . ' class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td>Unit Price (' . BASE_CURRENCY_SYMBOL . '):</td>
                        <td><input type="text" name="price" value="' . $price . '" size="5" /></td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Product Options</h2></th>
                    </tr>
                    <tr>
                        <td><label for="recurring">Recurring Payment:</label></td>
                        <td><input type="checkbox" name="recurring" id="recurring" value="1"' . $recurring_checked . ' class="checkbox" onclick="show_or_hide_recurring()" /></td>
                    </tr>
                    <tr id="recurring_schedule_editable_by_customer_row" style="' . $recurring_schedule_editable_by_customer_row_style . '">
                        <td style="padding-left: 2em"><label for="recurring_schedule_editable_by_customer">Allow customer to set schedule:</label></td>
                        <td><input type="checkbox" name="recurring_schedule_editable_by_customer" id="recurring_schedule_editable_by_customer" value="1"' . $recurring_schedule_editable_by_customer_checked . ' class="checkbox" onclick="if (this.checked == true) {document.getElementById(\'recurring_schedule_editable_by_customer_message\').style.display = \'\';} else {document.getElementById(\'recurring_schedule_editable_by_customer_message\').style.display = \'none\';}" /><span id="recurring_schedule_editable_by_customer_message" style="' . $recurring_schedule_editable_by_customer_message_style . '"> (you may select default values for the schedule below)</span></td>
                    </tr>
                    ' . $output_start_row . '
                    <tr id="number_of_payments_row" style="' . $number_of_payments_row_style . '">
                        <td style="padding-left: 2em">Number of Payments:</td>
                        <td><input type="text" name="number_of_payments" value="' . $number_of_payments . '" size="7" maxlength="7" />' . get_number_of_payments_message() . '</td>
                    </tr>
                    <tr id="payment_period_row" style="' . $payment_period_row_style . '">
                        <td style="padding-left: 2em">Payment Period:</td>
                        <td><select name="payment_period">' .  select_payment_period($payment_period) . '</select></td>
                    </tr>
                    ' . $output_recurring_profile_disabled_rows . '
                    ' . $output_sage_group_id_row . '
                    <tr>
                        <td>Taxable:</td>
                        <td><input type="checkbox" name="taxable" value="1"' . $taxable_checked . ' class="checkbox" /></td>
                    </tr>
                    <tr style="' . $shippable_row_style . '">
                        <td><label for="shippable">Shippable:</label></td>
                        <td><input type="checkbox" name="shippable" id="shippable" value="1"' . $shippable_checked . ' class="checkbox" onclick="show_or_hide_shippable()"></td>
                    </tr>
                    <tr id="weight_row" style="' . $weight_row_style . '">
                        <td style="padding-left: 2em"><label for="weight">Weight:</label></td>
                        <td>
                            <input
                                type="number"
                                step="any"
                                id="weight"
                                name="weight"
                                value="' . $weight . '"
                                style="width: 90px">&nbsp;

                            pounds
                        </td>
                    </tr>
                    <tr id="primary_weight_points_row" style="' . $primary_weight_points_row_style . '">
                        <td style="padding-left: 2em">Primary Weight Points:</td>
                        <td><input type="text" name="primary_weight_points" value="' . $primary_weight_points . '" size="4" /></td>
                    </tr>
                    <tr id="secondary_weight_points_row" style="' . $secondary_weight_points_row_style . '">
                        <td style="padding-left: 2em">Secondary Weight Points:</td>
                        <td><input type="text" name="secondary_weight_points" value="' . $secondary_weight_points . '" size="4" /></td>
                    </tr>
                    <tr id="dimensions_row" style="' . $dimensions_row_style . '">
                        <td style="padding-left: 2em"><label for="length">Dimensions:</label></td>
                        <td>

                            <label for="length">L:</label>

                            <input
                                type="number"
                                step="any"
                                id="length"
                                name="length" 
                                value="' . $length . '"
                                placeholder="Length"
                                style="width: 90px"> &nbsp;
                            
                            <label for="width">W:</label>

                            <input
                                type="number"
                                step="any"
                                id="width"
                                name="width"
                                value="' . $width . '"
                                placeholder="Width"
                                style="width: 90px"> &nbsp;
                            
                            <label for="height">H:</label>

                            <input
                                type="number"
                                step="any"
                                id="height"
                                name="height"
                                value="' . $height . '"
                                placeholder="Height"
                                style="width: 90px"> &nbsp;

                            inches

                        </td>
                    </tr>
                    <tr id="container_required_row" style="' . $container_required_row_style . '">
                        <td style="padding-left: 2em">
                            <label for="container_required">Container Required:</label>
                        </td>
                        <td>
                            <input
                                type="checkbox"
                                id="container_required"
                                name="container_required"
                                value="1"
                                ' . $container_required_checked . '
                                class="checkbox">
                        </td>
                    </tr>
                    <tr id="preparation_time_row" style="' . $preparation_time_row_style . '">
                        <td style="padding-left: 2em">Preparation Time:</td>
                        <td><input type="text" name="preparation_time" value="' . $preparation_time . '" size="3" /> day(s) from order date.</td>
                    </tr>
                    <tr id="free_shipping_row" style="' . $free_shipping_row_style . '">
                        <td style="padding-left: 2em">Free Shipping:</td>
                        <td><input type="checkbox" id="free_shipping" name="free_shipping" value="1"' . $free_shipping_checked . ' class="checkbox" onclick="show_or_hide_free_shipping()" /></td>
                    </tr>
                    <tr id="extra_shipping_cost_row" style="' . $extra_shipping_cost_row_style . '">
                        <td style="padding-left: 2em">Extra Shipping Cost (' . BASE_CURRENCY_SYMBOL . '):</td>
                        <td><input type="text" name="extra_shipping_cost" value="' . $extra_shipping_cost . '" size="5" maxlength="9" /></td>
                    </tr>
                    <tr id="allowed_zones_row" style="' . $allowed_zones_row_style . '">
                        <td colspan="2" style="padding-left: 2em">
                            <table style="width: 500px" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width: 50%">
                                        <div style="margin-bottom: 3px">Allowed Zones</div>
                                        <input type="hidden" id="allowed_zones_hidden" name="allowed_zones_hidden" value="">
                                        <select id="allowed_zones" multiple="multiple" size="10" style="width: 95%">' . $output_allowed_zones . '</select>
                                    </td>
                                    <td style="text-align: center; vertical-align: middle; padding-left: 15px; padding-right: 15px">
                                        <input type="button" value="&gt;&gt;" onclick="move_options(\'allowed_zones\', \'disallowed_zones\', \'right\');" /><br />
                                        <br />
                                        <input type="button" value="&lt;&lt;" onclick="move_options(\'allowed_zones\', \'disallowed_zones\', \'left\');" /><br />
                                    </td>
                                    <td style="width: 50%">
                                        <div style="margin-bottom: 3px">Disallowed Zones</div>
                                        <select id="disallowed_zones" multiple="multiple" size="10" style="width: 95%">' . $output_disallowed_zones . '</select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    ' . $output_commissionable . '
                    <tr>
                        <th colspan="2"><h2>Catalog, Order Form & Cart Page Display Options</h2></th>
                    </tr>
                    <tr>
                        <td>Short Description:</td>
                        <td><input type="text" name="short_description" value="' . h($short_description) . '" maxlength="100" size="60" /></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Full Description:</td>
                        <td><textarea id="full_description" name="full_description" style="width: 600px; height: 200px">' . h(prepare_rich_text_editor_content_for_output($full_description)) . '</textarea></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Details:</td>
                        <td><textarea id="details" name="details" style="width: 600px; height: 200px">' . h(prepare_rich_text_editor_content_for_output($details)) . '</textarea></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Code:</td>
                        <td>
                            <textarea id="code" name="code" style="width: 500px; height: 100px">' . h($code) . '</textarea>
                            ' . get_codemirror_includes() . '
                            ' . get_codemirror_javascript(array('id' => 'code', 'code_type' => 'mixed')) . '
                        </td>
                    </tr>
                    <tr>
                        <td>Search Keywords:</td>
                        <td><input type="text" name="keywords" value="' . h($keywords) . '" maxlength="255" size="100" /></td>
                    </tr>
                    <tr>
                        <td>Image:</td>
                        <td><select name="image_name"><option value="">-None-</option>' . select_image_options($image_name) . '</select></td>
                    </tr>
                    <tr>
                        <td>Selection Type:</td>
                        <td><select name="selection_type">' .  select_selection_type($selection_type) . '</select></td>
                    </tr>
                    <tr>
                        <td>Default Quantity:</td>
                        <td><input type="text" name="default_quantity" value="' . $default_quantity . '" size="3" maxlength="9" /></td>
                    </tr>
                    <tr>
                        <td>Minimum Quantity:</td>
                        <td><input type="text" name="minimum_quantity" value="' . $minimum_quantity . '" size="3" maxlength="9" /></td>
                    </tr>
                    <tr>
                        <td>Maximum Quantity:</td>
                        <td><input type="text" name="maximum_quantity" value="' . $maximum_quantity . '" size="3" maxlength="9" /></td>
                    </tr>
                    ' . $output_attributes . '
                    <tr>
                        <th colspan="2"><h2>Search Engine Optimization</h2></th>
                    </tr>
                    <tr>
                        <td>Catalog Name:</td>
                        <td><span style="white-space: nowrap">' . URL_SCHEME . HOSTNAME . OUTPUT_PATH . 'example-catalog/<input type="text" name="address_name" value="' . h($address_name) . '" size="60" maxlength="255" /></span></td>
                    </tr>
                    <tr>
                        <td>
                            <label for="title">Web Browser Title:</label>
                        </td>
                        <td>
                            <input id="title" name="title" type="text" value="' . h($title) . '" maxlength="255" style="width: 98%">
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            <label for="meta_description">Web Browser Description:</label>
                        </td>
                        <td>
                            <textarea id="meta_description" name="meta_description" maxlength="255" rows="3" style="width: 99%">'
                                . h($meta_description) .
                            '</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">
                            <label for="meta_keywords">Web Browser Keywords:</label>
                        </td>
                        <td>
                            <textarea id="meta_keywords" name="meta_keywords" rows="3" style="width: 99%">'
                                . h($meta_keywords) .
                            '</textarea>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Inventory</h2></th>
                    </tr>
                    <tr>
                        <td><label for="inventory">Track Inventory:</label></td>
                        <td><input type="checkbox" name="inventory" id="inventory" value="1"' . $inventory_checked . ' class="checkbox" onclick="show_or_hide_inventory()" /></td>
                    </tr>
                    <tr id="inventory_quantity_row" style="' . $inventory_quantity_row_style . '">
                        <td style="padding-left: 2em">Inventory Quantity:</td>
                        <td><input type="text" name="inventory_quantity" value="' . h($inventory_quantity) . '" size="6" maxlength="9" /></td>
                    </tr>
                    <tr id="backorder_row" style="' . $backorder_row_style . '">
                        <td style="padding-left: 2em"><label for="backorder">Accept Backorders:</label></td>
                        <td><input type="checkbox" name="backorder" id="backorder" value="1"' . $backorder_checked . ' class="checkbox" /></td>
                    </tr>
                    <tr id="out_of_stock_message_row" style="' . $out_of_stock_message_row_style . '">
                        <td style="padding-left: 2em; vertical-align: top">Out of Stock Message:</td>
                        <td><textarea id="out_of_stock_message" name="out_of_stock_message" style="width: 600px; height: 200px">' . h(prepare_rich_text_editor_content_for_output($out_of_stock_message)) . '</textarea></td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Order Checkout Options</h2></th>
                    </tr>
                    <tr>
                        <td>Requires Product:</td>
                        <td><select name="required_product"><option value=""></option>' .  select_product($required_product) . '</select></td>
                    </tr>
                    <tr>
                        <td>Enable Product Form:</td>
                        <td>
                            <input type="checkbox" onclick="show_or_hide_form()" class="checkbox" value="1" id="product_form" name="product_form" ' . $product_form_checked . ' /><span id="form_notice" style="display: none; padding-left: 1em">(when ready, click "Save &amp; Continue" at the bottom of this screen to create the Product Form.)</span>
                            <input type="hidden" id="original_form_state" name="original_form_state" value="' . h($product_form) . '" />
                            <input type="hidden" id="current_form_state" name="current_form_state" value="' . h($product_form) . '" />
                        </td>
                    </tr>
                    <tr id="form_name_row" style="' . $form_name_row_style . '">
                        <td style="padding-left: 2em">Form Title for Display:</td>
                        <td><input type="text" maxlength="100" size="30" value="' . $form_name . '" name="form_name" /></td>
                    </tr>
                    <tr id="form_label_column_width_row" style="' . $form_label_column_width_row_style . '">
                        <td style="padding-left: 2em">Label Column Width:</td>
                        <td><input type="text" maxlength="3" size="3" value="' . $form_label_column_width . '" name="form_label_column_width" /> % (leave blank for auto)</td>
                    </tr>
                    <tr id="form_quantity_type_row" style="' . $form_quantity_type_row_style . '">
                        <td style="padding-left: 2em; vertical-align: top">Quantity Type:</td>
                        <td><input type="radio" class="radio" value="One Form per Quantity" id="form_quantity_type_one_form_per_quantity" name="form_quantity_type" ' . $form_quantity_type_one_form_per_quantity_checked . ' /><label for="form_quantity_type_one_form_per_quantity"> One form per quantity</label><br />
                        <input type="radio" class="radio" value="One Form per Product" id="form_quantity_type_one_form_per_product" name="form_quantity_type" ' . $form_quantity_type_one_form_per_product_checked . ' /><label for="form_quantity_type_one_form_per_product"> One form per product</label></td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Order Complete Options</h2></th>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Order Receipt Page Message:</td>
                        <td><textarea id="order_receipt_message" name="order_receipt_message" style="width: 600px; height: 200px">' . h(prepare_rich_text_editor_content_for_output($order_receipt_message)) . '</textarea></td>
                    </tr>
                    <tr>
                        <td>Order Receipt BCC E-mail Address:</td>
                        <td><input type="email" name="order_receipt_bcc_email_address" value="' . h($order_receipt_bcc_email_address) . '" size="40" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td>E-mail Additional Page to Customer:</td>
                        <td><select name="email_page"><option value=""></option>' .  select_page($email_page) . '</select></td>
                    </tr>
                    <tr>
                        <td style="padding-left: 2em">BCC E-mail Address:</td>
                        <td><input type="email" name="email_bcc" value="' . h($email_bcc) . '" size="40" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td>Add Customer to Contact Group:</td>
                        <td><select name="contact_group_id"><option value=""></option>' . select_contact_group($contact_group_id, $user) . '</select></td>
                    </tr>
                    <tr>
                        <td>Add Days to Customer\'s Membership:</td>
                        <td><input type="text" name="membership_renewal" value="' . $membership_renewal . '" size="3" />&nbsp;&nbsp;day(s) (0 for none)</td>
                    </tr>
                    <tr>
                        <td><label for="grant_private_access">Grant Private Access to Customer:</label></td>
                        <td><input type="checkbox" name="grant_private_access" id="grant_private_access" value="1"' . $grant_private_access_checked . ' class="checkbox" onclick="show_or_hide_grant_private_access()" /></td>
                    </tr>
                    <tr id="private_folder_row" style="' . $private_folder_row_style . '">
                        <td style="padding-left: 2em">Set &quot;View&quot; Access to Folder:</td>
                        <td><select name="private_folder"><option value=""></option>' .  select_folder($private_folder, 0, 0, 0, array(), array(), 'private') . '</select></td>
                    </tr>
                    <tr id="private_days_row" style="' . $private_days_row_style . '">
                        <td style="padding-left: 2em">Length:</td>
                        <td><input type="text" name="private_days" value="' . $private_days . '" size="3" /> day(s) (leave blank for no expiration)</td>
                    </tr>
                    <tr id="send_to_page_row" style="' . $send_to_page_row_style . '">
                        <td style="padding-left: 2em">Set Customer\'s Start Page to:</td>
                        <td><select name="send_to_page"><option value=""></option>' .  select_page($send_to_page) . '</select></td>
                    </tr>
                    <tr>
                        <td>Reward Points:</td>
                        <td><input type="text" name="reward_points" value="' . $reward_points . '" size="5" maxlength="9" /></td>
                    </tr>
                    <tr>
                        <td><label for="gift_card">Email Gift Card:</label></td>
                        <td><input type="checkbox" name="gift_card" id="gift_card" value="1"' . $gift_card_checked . ' class="checkbox" onclick="toggle_product_gift_card()" /></td>
                    </tr>
                    <tr id="gift_card_email_subject_row"' . $gift_card_email_subject_row_style . '>
                        <td style="padding-left: 2em">Subject:</td>
                        <td><input name="gift_card_email_subject" value="' . h($gift_card_email_subject) . '" type="text" size="80" maxlength="255" /></td>
                    </tr>
                    <tr id="gift_card_email_format_row"' . $gift_card_email_format_row_style . '>
                        <td style="padding-left: 2em">Format:</td>
                        <td><input type="radio" id="gift_card_email_format_plain_text" name="gift_card_email_format" value="plain_text"' . $gift_card_email_format_plain_text_checked . ' class="radio" onclick="toggle_product_gift_card_email_format()" /><label for="gift_card_email_format_plain_text">Plain Text</label> &nbsp;<input type="radio" id="gift_card_email_format_html" name="gift_card_email_format" value="html"' . $gift_card_email_format_html_checked . ' class="radio" onclick="toggle_product_gift_card_email_format()" /><label for="gift_card_email_format_html">HTML</label></td>
                    </tr>
                    <tr id="gift_card_email_body_row"' . $gift_card_email_body_row_style . '>
                        <td style="padding-left: 2em; vertical-align: top">Body:</td>
                        <td><textarea name="gift_card_email_body" rows="10" cols="70">' . h($gift_card_email_body) . '</textarea></td>
                    </tr>
                    <tr id="gift_card_email_page_id_row"' . $gift_card_email_page_id_row_style . '>
                        <td style="padding-left: 2em">Page:</td>
                        <td><select name="gift_card_email_page_id"><option value=""></option>' . select_page($gift_card_email_page_id) . '</select></td>
                    </tr>
                    <tr>
                        <td><label for="submit_form">Create/Update Submitted Form:</label></td>
                        <td><input type="checkbox" name="submit_form" id="submit_form" value="1"' . $submit_form_checked . ' class="checkbox" onclick="toggle_product_submit_form()" /></td>
                    </tr>
                    <tr id="submit_form_custom_form_page_id_row"' . $submit_form_custom_form_page_id_row_style . '>
                        <td style="padding-left: 2em">Custom Form:</td>
                        <td>
                            <select id="submit_form_custom_form_page_id" name="submit_form_custom_form_page_id" onchange="product_submit_form_update_custom_form_fields()"><option value=""></option>' .  select_page($submit_form_custom_form_page_id, 'custom form') . '</select>
                            <script>product_submit_form_update_custom_form_fields();</script>
                        </td>
                    </tr>
                    <tr id="submit_form_create_row"' . $submit_form_create_row_style . '>
                        <td style="padding-left: 2em"><label for="submit_form_create">Create Submitted Form:</label></td>
                        <td><input type="checkbox" name="submit_form_create" id="submit_form_create" value="1"' . $submit_form_create_checked . ' class="checkbox" onclick="toggle_product_submit_form_create()" /></td>
                    </tr>
                    <tr id="submit_form_create_fields_row"' . $submit_form_create_fields_row_style . '>
                        <td>&nbsp;</td>
                        <td>
                            <div style="margin-bottom: 1em">
                                Please configure the fields below that should be set when a Submitted Form is created.
                            </div>
                            <table id="submit_form_create_field_table" class="chart" style="margin-bottom: 1.25em; width: auto">
                                <tbody>
                                </tbody>
                            </table>
                            <div style="margin-bottom: 1em"><a href="javascript:void(0)" onclick="product_submit_form_add_field({action: \'create\'})" class="button">Add Field</a></div>
                            <input type="hidden" id="last_submit_form_create_field_number" name="last_submit_form_create_field_number" value="0" />
                            <script>
                                var last_submit_form_field_number = [];                            
                                last_submit_form_field_number["create"] = 0;
                                ' . $output_submit_form_create_javascript . '
                            </script>
                        </td>
                    </tr>
                    <tr id="submit_form_update_row"' . $submit_form_update_row_style . '>
                        <td style="padding-left: 2em"><label for="submit_form_update">Update Submitted Form:</label></td>
                        <td><input type="checkbox" name="submit_form_update" id="submit_form_update" value="1"' . $submit_form_update_checked . ' class="checkbox" onclick="toggle_product_submit_form_update()" /></td>
                    </tr>
                    <tr id="submit_form_update_fields_row"' . $submit_form_update_fields_row_style . '>
                        <td style="padding-left: 4em">&nbsp;</td>
                        <td>
                            <div style="margin-bottom: 1em">
                                Please configure the fields below that should be set when a Submitted Form is updated.
                            </div>
                            <table id="submit_form_update_field_table" class="chart" style="margin-bottom: 1.25em; width: auto">
                                <tbody>
                                </tbody>
                            </table>
                            <div style="margin-bottom: 3em"><a href="javascript:void(0)" onclick="product_submit_form_add_field({action: \'update\'})" class="button">Add Field</a></div>
                            <input type="hidden" id="last_submit_form_update_field_number" name="last_submit_form_update_field_number" value="0" />
                            <script>
                                last_submit_form_field_number["update"] = 0;
                                ' . $output_submit_form_update_javascript . '
                            </script>
                            <div style="margin-bottom: 1em">
                                Please specify which Submitted Form should be updated.
                            </div>
                            <div style="margin-bottom: 1em">
                                Where&nbsp;
                                <select id="submit_form_update_where_field" name="submit_form_update_where_field"></select>&nbsp;
                                is equal to &nbsp;
                                <input type="text" name="submit_form_update_where_value" value="' . h($submit_form_update_where_value) . '" size="40" maxlength="255">
                                <script>init_product_submit_form_update_where("' . escape_javascript($submit_form_update_where_field) . '")</script>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="add_comment">Add Comment:</label></td>
                        <td><input type="checkbox" name="add_comment" id="add_comment" value="1"' . $add_comment_checked . ' class="checkbox" onclick="toggle_product_add_comment()" /></td>
                    </tr>
                    <tr id="add_comment_page_id_row"' . $add_comment_page_id_row_style . '>
                        <td style="padding-left: 2em">Page:</td>
                        <td><select id="add_comment_page_id" name="add_comment_page_id"><option value=""></option>' .  select_page($add_comment_page_id) . '</select></td>
                    </tr>
                    <tr id="add_comment_message_row"' . $add_comment_message_row_style . '>
                        <td style="padding-left: 2em; vertical-align: top">Comment:</td>
                        <td><textarea name="add_comment_message" style="width: 400px; height: 100px">' . h($add_comment_message) . '</textarea></td>
                    </tr>
                    <tr id="add_comment_name_row"' . $add_comment_name_row_style . '>
                        <td style="padding-left: 2em">Added by:</td>
                        <td><input type="text" name="add_comment_name" value="' . h($add_comment_name) . '" size="40" /></td>
                    </tr>
                    <tr id="add_comment_only_for_submit_form_update_row"' . $add_comment_only_for_submit_form_update_row_style . '>
                        <td style="padding-left: 2em"><label for="add_comment_only_for_submit_form_update">Only add Comment if<br />Submitted Form was updated:</label></td>
                        <td><input type="checkbox" id="add_comment_only_for_submit_form_update" name="add_comment_only_for_submit_form_update" value="1"' . $add_comment_only_for_submit_form_update_checked . ' class="checkbox" /></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Form/Comment Quantity Type:</td>
                        <td>
                            <label>
                                <input type="radio" class="radio" value="One Form per Quantity" name="submit_form_quantity_type"' . $submit_form_quantity_type_one_form_per_quantity_checked . '>
                                One form/comment per quantity
                            </label><br>

                            <label>
                                <input type="radio" class="radio" value="One Form per Product" name="submit_form_quantity_type"' . $submit_form_quantity_type_one_form_per_product_checked . '>
                                One form/comment per product
                            </label>
                        </td>
                    </tr>
                    ' . $output_custom_product_field_rows . '
                    <tr>
                        <th colspan="2"><h2>Product Notes for Order Exporting</h2></th>
                    </tr>
                    <tr>
                        <td style="vertical-align: top">Notes:</td>
                        <td><textarea id="notes" name="notes" style="width: 225px; height: 70px">' . $notes . '</textarea></td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>RSS Feed</h2></th>
                    </tr>
                    <tr>
                        <td>Google Product Category:</td>
                        <td><input type="text" name="google_product_category" value="' . h($google_product_category) . '" size="100" maxlength="255" /></td>
                    </tr>
                    <tr>
                        <td>GTIN:</td>
                        <td><input type="text" name="gtin" value="' . h($gtin) . '" size="30" maxlength="50" /> (e.g. UPC)</td>
                    </tr>
                    <tr>
                        <td>Brand:</td>
                        <td><input type="text" name="brand" value="' . h($brand) . '" size="30" maxlength="100" /></td>
                    </tr>
                    <tr>
                        <td>MPN:</td>
                        <td><input type="text" name="mpn" value="' . h($mpn) . '" size="30" maxlength="50" /> (i.e. manufacturer product number)</td>
                    </tr>
                </table>
                <div class="buttons">
                    <input type="submit" name="submit_save" id="create_button" value="Save" class="submit-primary">&nbsp;&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" OnClick="javascript:history.go(-1);" class="submit-secondary">&nbsp;&nbsp;&nbsp;<input type="submit" name="submit_delete" value="Delete" class="delete" onclick="return confirm(\'WARNING: This product will be permanently deleted.\')">
                </div>
                <input type="hidden" name="id" value="' . h($_GET['id']) . '">
            </form>
        </div>' .
        output_footer();

    $liveform->remove_form();

} else {
    validate_token_field();
    
    // delete product references in products_zones_xref (we do this reguardless of whether we are deleting the product or updating the product)
    $query = "DELETE FROM products_zones_xref ".
             "WHERE product_id = '" . escape($_POST['id']) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // Delete product attribute references (we do this for both delete and update).
    db("DELETE FROM products_attributes_xref WHERE product_id = '" . escape($_POST['id']) . "'");

    // Delete submit form fields references for this product (we do this for both delete and update).
    db("DELETE FROM product_submit_form_fields WHERE product_id = '" . escape($_POST['id']) . "'");

    // if product was selected for delete
    if ($_POST['submit_delete'] == 'Delete') {
        // delete product
        $query =    "DELETE FROM products ".
                    "WHERE id = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // delete product references in products_groups_xref
        $query =    "DELETE FROM products_groups_xref ".
                    "WHERE product = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // delete form fields for this product
        $query = "DELETE FROM form_fields WHERE (product_id = '" . escape($_POST['id']) . "') AND (product_id != '0')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // delete form field options for this product
        $query = "DELETE FROM form_field_options WHERE (product_id = '" . escape($_POST['id']) . "') AND (product_id != '0')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // Delete target options for this product.
        db("DELETE FROM target_options WHERE (product_id = '" . escape($_POST['id']) . "') AND (product_id != '0')");
        
        // delete all of the keywords for this product
        $query = "DELETE FROM tag_cloud_keywords WHERE (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // delete all of the keywords xref records for this product
        $query = "DELETE FROM tag_cloud_keywords_xref WHERE (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // Check if this product has short links, in order to determine if we need to delete them and update rewrite file.
        $query =
            "SELECT COUNT(*)
            FROM short_links
            WHERE
                (destination_type = 'product')
                AND (product_id = '" . escape($_POST['id']) . "')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_row($result);

        // If a short link exists, then delete them and update short links in rewrite file.
        if ($row[0] != 0) {
            $query =
                "DELETE FROM short_links
                WHERE
                    (destination_type = 'product')
                    AND (product_id = '" . escape($_POST['id']) . "')";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        }        

        log_activity("product ($_POST[name]) was deleted", $_SESSION['sessionusername']);
    
    // else product was not selected for delete
    } else {
        // if user has a user role, then verify that user has access to contact group that was selected
        if ($user['role'] == 3) {
            $new_contact_group_id = $_POST['contact_group_id'];
            
            // get current contact group id
            $query =
                "SELECT contact_group_id
                FROM products
                WHERE id = '" . escape($_POST['id']) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            
            $current_contact_group_id = $row['contact_group_id'];
            
            // if contact group is trying to be changed
            // and a contact group was selected
            // and user does not have access to contact group,
            // then don't allow contact group to be changed
            if (($new_contact_group_id != $current_contact_group_id) && ($new_contact_group_id) && (validate_contact_group_access($user, $new_contact_group_id) == false)) {
                log_activity("access denied because user does not have access to contact group that user selected for product", $_SESSION['sessionusername']);
                output_error('Access denied. <a href="javascript:history.go(-1)">Go back</a>.');
            }
        }
        
        // remove commas from price
        $price = str_replace(',', '', $_POST['price']);
        
        // convert price from dollars to cents
        $price = $price * 100;
        
        // remove commas from extra shipping cost
        $extra_shipping_cost = str_replace(',', '', $_POST['extra_shipping_cost']);
        
        // convert extra shipping cost from dollars to cents
        $extra_shipping_cost = $extra_shipping_cost * 100;

        $_POST['order_receipt_bcc_email_address'] = trim($_POST['order_receipt_bcc_email_address']);
        
        // if a order receipt bcc email address was supplied, validate the e-mail address
        if ($_POST['order_receipt_bcc_email_address']) {
            if (validate_email_address($_POST['order_receipt_bcc_email_address']) == FALSE) {
                output_error('The e-mail address is invalid. <a href="javascript:history.go(-1);">Go back</a>.');
            }
        }

        $_POST['email_bcc'] = trim($_POST['email_bcc']);
        
        // if a bcc e-mail address was supplied, validate bcc e-mail address
        if ($_POST['email_bcc']) {
            if (validate_email_address($_POST['email_bcc']) == FALSE) {
                output_error('The e-mail address is invalid. <a href="javascript:history.go(-1);">Go back</a>.');
            }
        }
        
        // if the affiliate program is enabled, prepare affiliate program SQL
        if (AFFILIATE_PROGRAM == true) {
            $sql_commissionable =
                    "commissionable = '" . escape($_POST['commissionable']) . "',
                    commission_rate_limit = '" . escape($_POST['commission_rate_limit']) . "',";
        }
        
        // determine if recurring profile disabled fields should be updated
        $sql_recurring_profile_disabled = '';
        
        // if credit/debit card payment method is enabled and payment gateway is PayPal Payments Pro, then prepare to update recurring profile disabled fields
        if ((ECOMMERCE_CREDIT_DEBIT_CARD == true) && (ECOMMERCE_PAYMENT_GATEWAY == 'PayPal Payments Pro')) {
            $sql_recurring_profile_disabled =
                "recurring_profile_disabled_perform_actions = '" . escape($_POST['recurring_profile_disabled_perform_actions']) . "',
                recurring_profile_disabled_expire_membership = '" . escape($_POST['recurring_profile_disabled_expire_membership']) . "',
                recurring_profile_disabled_revoke_private_access = '" . escape($_POST['recurring_profile_disabled_revoke_private_access']) . "',
                recurring_profile_disabled_email = '" . escape($_POST['recurring_profile_disabled_email']) . "',
                recurring_profile_disabled_email_subject = '" . escape($_POST['recurring_profile_disabled_email_subject']) . "',
                recurring_profile_disabled_email_page_id = '" . escape($_POST['recurring_profile_disabled_email_page_id']) . "',";
        }
        
        // determine if Sage group ID field should be updated
        $sql_sage_group_id = '';
        
        // if credit/debit card payment method is enabled and payment gateway is Sage, then prepare to update Sage group ID field
        if ((ECOMMERCE_CREDIT_DEBIT_CARD == TRUE) && (ECOMMERCE_PAYMENT_GATEWAY == 'Sage')) {
            $sql_sage_group_id = "sage_group_id = '" . escape($_POST['sage_group_id']) . "',";
        }

        // get current product information
        $query =
            "SELECT
                form,
                title,
                meta_description,
                full_description,
                details,
                seo_analysis_current,
                address_name
            FROM products
            WHERE id = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        
        $original_state = $row['form'];
        $title = $row['title'];
        $meta_description = $row['meta_description'];
        $full_description = $row['full_description'];
        $details = $row['details'];
        $seo_analysis_current = $row['seo_analysis_current'];
        $current_address_name = $row['address_name'];
        
        // if the address name is NOT blank then use that value for the address name
        if ($_POST['address_name'] != '') {
            $address_name = $_POST['address_name'];
            
        // else if the short description is NOT blank then use that value
        } elseif ($_POST['short_description'] != '') {
            $address_name = $_POST['short_description'];
            
        // else if the name is NOT blank then use that value
        } elseif ($_POST['name'] != '') {
            $address_name = $_POST['name'];
            
        // else use id
        } else {
            $address_name = $_POST['id'];
        }
        
        // prepare the address name for the database
        $address_name = prepare_catalog_item_address_name($address_name, $_POST['id']);

        // If this product is enabled, then deal with adding/updating keywords for tag clouds.
        if ($_POST['enabled'] == 1) {
            // get the tag cloud keywords xref records for this product
            $query = "SELECT item_id FROM tag_cloud_keywords_xref WHERE (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product')";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            
            // if there is an xref record, then update the keywords in the tag cloud
            if (mysqli_num_rows($result) > 0) {
                $new_keywords = array();
                
                // get the new keywords
                $new_keywords = explode(',', $_POST['keywords']);
                
                // loop through the keywords to remove any extra spaces before and after the keyword
                foreach ($new_keywords as $key => $new_keyword) {
                    if ($new_keyword != '') {
                        $new_keywords[$key] = trim($new_keyword);
                    }
                }
                
                // remove duplicate entries from the array
                $new_keywords = array_unique($new_keywords);
                
                $original_keywords = array();
                
                // get the original meta keywords for this product
                $query = "SELECT keyword FROM tag_cloud_keywords WHERE (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product')";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                while($row = mysqli_fetch_assoc($result)) {
                    $original_keywords[] = $row['keyword'];
                }
                
                // if there are original keywords, then compare them to the new keywords and remove any keywords that are in both arrays from the new keywords array,
                // and remove any original keywords that are not in the new keywords array from the database
                if (count($original_keywords) > 0) {
                    // loop through the old and new keywords arrays to remove any keywords that are in both, and to remove old keywords from the database that are not in the new keywords array
                    foreach ($original_keywords as $original_keyword) {
                        $found_keyword = FALSE;
                        
                        foreach ($new_keywords as $key => $new_keyword) {
                            // if the original keyword matches the new keyword, then remove it from the new keywords array and indicate that a keyword was found
                            if ($original_keyword == $new_keyword) {
                                unset($new_keywords[$key]);
                                $found_keyword = TRUE;
                            }
                        }
                        
                        // if a keyword was not found, then remove it from the database
                        if ($found_keyword == FALSE) {
                            $query = "DELETE FROM tag_cloud_keywords WHERE ((keyword = '" . escape($original_keyword) . "') AND (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product'))";
                            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                        }
                    }
                }
                
                // loop through the new keywords and add them to the database
                foreach ($new_keywords as $key => $new_keyword) {
                    // if the new keyword is not blank, then insert the keyword
                    if ($new_keyword != '') {
                        $query = 
                            "INSERT INTO tag_cloud_keywords 
                            (
                                keyword, 
                                item_id, 
                                item_type
                            ) VALUES (
                                '" . escape($new_keyword) . "',
                                '" . escape($_POST['id']) . "',
                                'product'
                            )";
                        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                    }
                }
            }

        // Otherwise the product is disabled, so delete tag cloud keywords.
        } else {
            db("DELETE FROM tag_cloud_keywords WHERE (item_id = '" . escape($_POST['id']) . "') AND (item_type = 'product')");
        }
        
        $sql_seo_analysis_current = "";
        
        // if the seo analysis is current and the title, meta description, full description, or details has changed, the prepare to clear current status
        if (
            ($seo_analysis_current == 1)
            &&
            (
                (trim($title) != trim($_POST['title']))
                || (trim($meta_description) != trim($_POST['meta_description']))
                || (trim($full_description) != trim($_POST['full_description']))
                || (trim($details) != trim($_POST['details']))
            )
        ) {
            $sql_seo_analysis_current = "seo_analysis_current = '0',";
        }
        
        // update the product
        $query =
            "UPDATE products
            SET
                name = '" . escape($_POST['name']) . "',
                enabled = '" . escape($_POST['enabled']) . "',
                short_description = '" . escape($_POST['short_description']) . "',
                full_description = '" . escape(prepare_rich_text_editor_content_for_input($_POST['full_description'])) . "',
                details = '" . escape(prepare_rich_text_editor_content_for_input($_POST['details'])) . "',
                code = '" . escape($_POST['code']) . "',
                keywords = '" . escape($_POST['keywords']) . "',
                image_name = '" . escape($_POST['image_name']) . "',
                price = '" . escape($price) . "',
                taxable = '" . escape($_POST['taxable']) . "',
                contact_group_id = '" . escape($_POST['contact_group_id']) . "',
                order_receipt_bcc_email_address = '" . escape($_POST['order_receipt_bcc_email_address']) . "',
                email_page = '" . escape($_POST['email_page']) . "',
                email_bcc = '" . escape($_POST['email_bcc']) . "',
                order_receipt_message = '" . escape(prepare_rich_text_editor_content_for_input($_POST['order_receipt_message'])) . "',
                required_product = '" . escape($_POST['required_product']) . "',
                shippable = '" . escape($_POST['shippable']) . "',
                weight = '" . escape($_POST['weight']) . "',
                primary_weight_points = '" . escape($_POST['primary_weight_points']) . "',
                secondary_weight_points = '" . escape($_POST['secondary_weight_points']) . "',
                length = '" . e($_POST['length']) . "',
                width = '" . e($_POST['width']) . "',
                height = '" . e($_POST['height']) . "',
                container_required = '" . e($_POST['container_required']) . "',
                preparation_time = '" . escape($_POST['preparation_time']) . "',
                free_shipping = '" . escape($_POST['free_shipping']) . "',
                extra_shipping_cost = '" . escape($extra_shipping_cost) . "',
                $sql_commissionable
                selection_type = '" . escape($_POST['selection_type']) . "',
                default_quantity = '" . escape($_POST['default_quantity']) . "',
                minimum_quantity = '" . escape($_POST['minimum_quantity']) . "',
                maximum_quantity = '" . escape($_POST['maximum_quantity']) . "',
                address_name = '" . escape($address_name) . "',
                title = '" . escape($_POST['title']) . "',
                meta_description = '" . escape($_POST['meta_description']) . "',
                meta_keywords = '" . escape($_POST['meta_keywords']) . "',
                inventory = '" . escape($_POST['inventory']) . "',
                inventory_quantity = '" . escape($_POST['inventory_quantity']) . "',
                backorder = '" . escape($_POST['backorder']) . "',
                out_of_stock_message = '" . escape(prepare_rich_text_editor_content_for_input($_POST['out_of_stock_message'])) . "',
                recurring = '" . escape($_POST['recurring']) . "',
                recurring_schedule_editable_by_customer = '" . escape($_POST['recurring_schedule_editable_by_customer']) . "',
                start = '" . escape($_POST['start']) . "',
                number_of_payments = '" . escape($_POST['number_of_payments']) . "',
                payment_period = '" . escape($_POST['payment_period']) . "',
                $sql_recurring_profile_disabled
                $sql_sage_group_id
                membership_renewal = '" . escape($_POST['membership_renewal']) . "',
                grant_private_access = '" . escape($_POST['grant_private_access']) . "',
                private_folder = '" . escape($_POST['private_folder']) . "',
                private_days = '" . escape($_POST['private_days']) . "',
                send_to_page = '" . escape($_POST['send_to_page']) . "',
                reward_points = '" . escape($_POST['reward_points']) . "',
                gift_card = '" . escape($_POST['gift_card']) . "',
                gift_card_email_subject = '" . escape($_POST['gift_card_email_subject']) . "',
                gift_card_email_format = '" . escape($_POST['gift_card_email_format']) . "',
                gift_card_email_body = '" . escape($_POST['gift_card_email_body']) . "',
                gift_card_email_page_id = '" . escape($_POST['gift_card_email_page_id']) . "',
                submit_form = '" . escape($_POST['submit_form']) . "',
                submit_form_custom_form_page_id = '" . escape($_POST['submit_form_custom_form_page_id']) . "',
                submit_form_quantity_type = '" . e($_POST['submit_form_quantity_type']) . "',
                submit_form_create = '" . escape($_POST['submit_form_create']) . "',
                submit_form_update = '" . escape($_POST['submit_form_update']) . "',
                submit_form_update_where_field = '" . e($_POST['submit_form_update_where_field']) . "',
                submit_form_update_where_value = '" . e($_POST['submit_form_update_where_value']) . "',
                add_comment = '" . escape($_POST['add_comment']) . "',
                add_comment_page_id = '" . escape($_POST['add_comment_page_id']) . "',
                add_comment_message = '" . escape($_POST['add_comment_message']) . "',
                add_comment_name = '" . escape($_POST['add_comment_name']) . "',
                add_comment_only_for_submit_form_update = '" . escape($_POST['add_comment_only_for_submit_form_update']) . "',
                form = '" . escape($_POST['product_form']) . "',
                form_name = '" . escape($_POST['form_name']) . "',
                form_label_column_width = '" . escape($_POST['form_label_column_width']) . "',
                form_quantity_type = '" . escape($_POST['form_quantity_type']) . "',
                custom_field_1 = '" . escape($_POST['custom_field_1']) . "',
                custom_field_2 = '" . escape($_POST['custom_field_2']) . "',
                custom_field_3 = '" . escape($_POST['custom_field_3']) . "',
                custom_field_4 = '" . escape($_POST['custom_field_4']) . "',
                notes = '" . escape($_POST['notes']) . "',
                google_product_category = '" . escape($_POST['google_product_category']) . "',
                gtin = '" . escape($_POST['gtin']) . "',
                brand = '" . escape($_POST['brand']) . "',
                mpn = '" . escape($_POST['mpn']) . "',
                $sql_seo_analysis_current
                user = '" . $user['id'] . "',
                timestamp = UNIX_TIMESTAMP()
            WHERE id = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed');

        // load all allowed zones in array by exploding string that has allowed zone ids separated by commas
        $allowed_zones = explode(',', $_POST['allowed_zones_hidden']);

        // foreach allowed zone insert row in products_zones_xref table
        foreach ($allowed_zones as $zone_id) {
            // if zone id is not blank, insert row
            if ($zone_id) {
                $query = "INSERT INTO products_zones_xref (product_id, zone_id) VALUES ('" . escape($_POST['id']) . "', '" . escape($zone_id) . "')";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            }
        }

        // If the user added attributes, then save them.
        if ($_POST['attributes']) {
            $attributes = decode_json($_POST['attributes']);

            $sort_order = 0;

            foreach ($attributes as $attribute) {
                $sort_order++;
                
                db(
                    "INSERT INTO products_attributes_xref (
                        product_id,
                        attribute_id,
                        option_id,
                        sort_order)
                    VALUES (
                        '" . e($_POST['id']) . "',
                        '" . e($attribute['attribute_id']) . "',
                        '" . e($attribute['option_id']) . "',
                        '$sort_order')");
            }
        }

        // If a custom form was selected for submit form feature, then check if we need to add fields to database.
        if ($_POST['submit_form_custom_form_page_id']) {
            // Create array for storing submit form fields that have a value set, so if a user tried
            // to set multiple values for the same field, we don't add the extras.
            $added_submit_form_fields = array();
            
            // Loop through all submit form create fields in order to insert them into database.
            for ($field_number = 1; $field_number <= $_POST['last_submit_form_create_field_number']; $field_number++) {
                // If a field was selected, and the field has not already been added,
                // then continue to check if field should be added to database.
                if (
                    ($_POST['submit_form_create_field_' . $field_number . '_form_field_id'])
                    && (in_array($_POST['submit_form_create_field_' . $field_number . '_form_field_id'], $added_submit_form_fields) == false)
                ) {
                    // Check to make sure that selected field actually exists on the custom form
                    // in order to make sure that user is not trying to do something funny like trying to
                    // set a field on a different form from the one they selected.
                    $field_id = db_value(
                        "SELECT id
                        FROM form_fields
                        WHERE
                            (id = '" . escape($_POST['submit_form_create_field_' . $field_number . '_form_field_id']) . "')
                            AND (page_id = '" . escape($_POST['submit_form_custom_form_page_id']) . "')");

                    // If a field was found for the selected field and selected custom form,
                    // then continue to add field to database.
                    if ($field_id) {
                        db(
                            "INSERT INTO product_submit_form_fields (
                                product_id,
                                action,
                                form_field_id,
                                value)
                            VALUES (
                                '" . escape($_POST['id']) . "',
                                'create',
                                '" . escape($_POST['submit_form_create_field_' . $field_number . '_form_field_id']) . "',
                                '" . escape(trim($_POST['submit_form_create_field_' . $field_number . '_value'])) . "')");

                        // Remember that the field has been added so we don't add multiple records for the same field.
                        $added_submit_form_fields[] = $_POST['submit_form_create_field_' . $field_number . '_form_field_id'];
                    }
                }
            }

            $added_submit_form_fields = array();
            
            // Loop through all submit form update fields in order to insert them into database.
            for ($field_number = 1; $field_number <= $_POST['last_submit_form_update_field_number']; $field_number++) {
                // If a field was selected, and the field has not already been added,
                // then continue to check if field should be added to database.
                if (
                    ($_POST['submit_form_update_field_' . $field_number . '_form_field_id'])
                    && (in_array($_POST['submit_form_update_field_' . $field_number . '_form_field_id'], $added_submit_form_fields) == false)
                ) {
                    // Check to make sure that selected field actually exists on the custom form
                    // in order to make sure that user is not trying to do something funny like trying to
                    // set a field on a different form from the one they selected.
                    $field_id = db_value(
                        "SELECT id
                        FROM form_fields
                        WHERE
                            (id = '" . escape($_POST['submit_form_update_field_' . $field_number . '_form_field_id']) . "')
                            AND (page_id = '" . escape($_POST['submit_form_custom_form_page_id']) . "')");

                    // If a field was found for the selected field and selected custom form,
                    // then continue to add field to database.
                    if ($field_id) {
                        db(
                            "INSERT INTO product_submit_form_fields (
                                product_id,
                                action,
                                form_field_id,
                                value)
                            VALUES (
                                '" . escape($_POST['id']) . "',
                                'update',
                                '" . escape($_POST['submit_form_update_field_' . $field_number . '_form_field_id']) . "',
                                '" . escape(trim($_POST['submit_form_update_field_' . $field_number . '_value'])) . "')");

                        // Remember that the field has been added so we don't add multiple records for the same field.
                        $added_submit_form_fields[] = $_POST['submit_form_update_field_' . $field_number . '_form_field_id'];
                    }
                }
            }
        }

        log_activity('product (' . $_POST['name'] . ') was modified', $_SESSION['sessionusername']);
    }
    
    // if the product was not deleted and the original product form state was set to 0 (off), and if the new product form state is different than the original.
    if (($_POST['submit_delete'] != 'Delete') && (($original_state == 0) && ($_POST['current_form_state'] != $original_state))) {
        // forward user to view form designer
        header('Location: ' . URL_SCHEME . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/view_fields.php?product_id=' . $_POST['id']);
    } else {
        // if there is a send to set, then forward user to send to
        if ($_POST['send_to'] != '') {
            header('Location: ' . URL_SCHEME . HOSTNAME . $_POST['send_to']);
            
        // else there is not a send to set, so forward user to view products screen.
        } else {
            header('Location: ' . URL_SCHEME . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/view_products.php');
        }
    }
}