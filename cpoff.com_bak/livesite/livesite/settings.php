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
validate_area_access($user, 'manager');

include_once('liveform.class.php');
$liveform = new liveform('settings');

if (!$_POST) {
    
    $query = "SELECT * FROM config";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    
    $url_scheme = $row['url_scheme'];
    $hostname = $row['hostname'];
    $email_address = $row['email_address'];
    $title = $row['title'];
    $meta_description = $row['meta_description'];
    $meta_keywords = $row['meta_keywords'];
    $mobile = $row['mobile'];
    $search_type = $row['search_type'];
    $social_networking = $row['social_networking'];
    $social_networking_type = $row['social_networking_type'];
    $social_networking_facebook = $row['social_networking_facebook'];
    $social_networking_twitter = $row['social_networking_twitter'];
    $social_networking_addthis = $row['social_networking_addthis'];
    $social_networking_plusone = $row['social_networking_plusone'];
    $social_networking_linkedin = $row['social_networking_linkedin'];
    $social_networking_code = $row['social_networking_code'];
    $captcha = $row['captcha'];
    $auto_dialogs = $row['auto_dialogs'];
    $mass_deletion = $row['mass_deletion'];
    $strong_password = $row['strong_password'];
    $password_hint = $row['password_hint'];
    $remember_me = $row['remember_me'];
    $forgot_password_link = $row['forgot_password_link'];
    $proxy_address = $row['proxy_address'];
    $badge_label = $row['badge_label'];
    $timezone = $row['timezone'];
    $date_format = $row['date_format'];
    $organization_name = $row['organization_name'];
    $organization_address_1 = $row['organization_address_1'];
    $organization_address_2 = $row['organization_address_2'];
    $organization_city = $row['organization_city'];
    $organization_state = $row['organization_state'];
    $organization_zip_code = $row['organization_zip_code'];
    $organization_country = $row['organization_country'];
    $opt_in_label = $row['opt_in_label'];
    $plain_text_email_campaign_footer = $row['plain_text_email_campaign_footer'];
    $visitor_tracking = $row['visitor_tracking'];
    $tracking_code_duration = $row['tracking_code_duration'];
    $pay_per_click_flag = $row['pay_per_click_flag'];
    $stats_url = $row['stats_url'];
    $google_analytics = $row['google_analytics'];
    $google_analytics_web_property_id = $row['google_analytics_web_property_id'];
    $page_editor_version = $row['page_editor_version'];
    $page_editor_font = $row['page_editor_font'];
    $page_editor_font_size = $row['page_editor_font_size'];
    $page_editor_font_style = $row['page_editor_font_style'];
    $page_editor_font_color = $row['page_editor_font_color'];
    $page_editor_background_color = $row['page_editor_background_color'];
    $registration_contact_group_id = $row['registration_contact_group_id'];
    $registration_email_address = $row['registration_email_address'];
    $member_id_label = $row['member_id_label'];
    $membership_contact_group_id = $row['membership_contact_group_id'];
    $membership_email_address = $row['membership_email_address'];
    $membership_expiration_warning_email = $row['membership_expiration_warning_email'];
    $membership_expiration_warning_email_subject = $row['membership_expiration_warning_email_subject'];
    $membership_expiration_warning_email_page_id = $row['membership_expiration_warning_email_page_id'];
    $membership_expiration_warning_email_days_before_expiration = $row['membership_expiration_warning_email_days_before_expiration'];
    $ecommerce_on_or_off = $row['ecommerce'];
    $ecommerce_multicurrency = $row['ecommerce_multicurrency'];
    $ecommerce_tax = $row['ecommerce_tax'];
    $ecommerce_tax_exempt = $row['ecommerce_tax_exempt'];
    $ecommerce_tax_exempt_label = $row['ecommerce_tax_exempt_label'];
    $ecommerce_shipping = $row['ecommerce_shipping'];
    $ecommerce_recipient_mode = $row['ecommerce_recipient_mode'];
    $usps_user_id = $row['usps_user_id'];
    $ecommerce_address_verification = $row['ecommerce_address_verification'];
    $ecommerce_address_verification_enforcement_type = $row['ecommerce_address_verification_enforcement_type'];
    $ups_key = $row['ups_key'];
    $ups_user_id = $row['ups_user_id'];
    $ups_password = $row['ups_password'];
    $ups_account = $row['ups_account'];
    $ecommerce_product_restriction_message = $row['ecommerce_product_restriction_message'];
    $ecommerce_no_shipping_methods_message = $row['ecommerce_no_shipping_methods_message'];
    $ecommerce_end_of_day_time = $row['ecommerce_end_of_day_time'];
    $ecommerce_email_address = $row['ecommerce_email_address'];
    $ecommerce_gift_card = $row['ecommerce_gift_card'];
    $ecommerce_gift_card_validity_days = $row['ecommerce_gift_card_validity_days'];
    $ecommerce_givex = $row['ecommerce_givex'];
    $ecommerce_givex_primary_hostname = $row['ecommerce_givex_primary_hostname'];
    $ecommerce_givex_secondary_hostname = $row['ecommerce_givex_secondary_hostname'];
    $ecommerce_givex_user_id = $row['ecommerce_givex_user_id'];
    $ecommerce_givex_password = $row['ecommerce_givex_password'];
    $ecommerce_credit_debit_card = $row['ecommerce_credit_debit_card'];
    $ecommerce_american_express = $row['ecommerce_american_express'];
    $ecommerce_diners_club = $row['ecommerce_diners_club'];
    $ecommerce_discover_card = $row['ecommerce_discover_card'];
    $ecommerce_mastercard = $row['ecommerce_mastercard'];
    $ecommerce_visa = $row['ecommerce_visa'];
    $ecommerce_payment_gateway = $row['ecommerce_payment_gateway'];
    $ecommerce_payment_gateway_transaction_type = $row['ecommerce_payment_gateway_transaction_type'];
    $ecommerce_payment_gateway_mode = $row['ecommerce_payment_gateway_mode'];
    $ecommerce_authorizenet_api_login_id = $row['ecommerce_authorizenet_api_login_id'];
    $ecommerce_authorizenet_transaction_key = $row['ecommerce_authorizenet_transaction_key'];
    $ecommerce_clearcommerce_client_id = $row['ecommerce_clearcommerce_client_id'];
    $ecommerce_clearcommerce_user_id = $row['ecommerce_clearcommerce_user_id'];
    $ecommerce_clearcommerce_password = $row['ecommerce_clearcommerce_password'];
    $ecommerce_first_data_global_gateway_store_number = $row['ecommerce_first_data_global_gateway_store_number'];
    $ecommerce_first_data_global_gateway_pem_file_name = $row['ecommerce_first_data_global_gateway_pem_file_name'];
    $ecommerce_paypal_payflow_pro_partner = $row['ecommerce_paypal_payflow_pro_partner'];
    $ecommerce_paypal_payflow_pro_merchant_login = $row['ecommerce_paypal_payflow_pro_merchant_login'];
    $ecommerce_paypal_payflow_pro_user = $row['ecommerce_paypal_payflow_pro_user'];
    $ecommerce_paypal_payflow_pro_password = $row['ecommerce_paypal_payflow_pro_password'];
    $ecommerce_paypal_payments_pro_api_username = $row['ecommerce_paypal_payments_pro_api_username'];
    $ecommerce_paypal_payments_pro_api_password = $row['ecommerce_paypal_payments_pro_api_password'];
    $ecommerce_paypal_payments_pro_api_signature = $row['ecommerce_paypal_payments_pro_api_signature'];
    $ecommerce_sage_merchant_id = $row['ecommerce_sage_merchant_id'];
    $ecommerce_sage_merchant_key = $row['ecommerce_sage_merchant_key'];
    $ecommerce_stripe_api_key = $row['ecommerce_stripe_api_key'];
    $ecommerce_surcharge_percentage = $row['ecommerce_surcharge_percentage'];
    $ecommerce_paypal_express_checkout = $row['ecommerce_paypal_express_checkout'];
    $ecommerce_paypal_express_checkout_transaction_type = $row['ecommerce_paypal_express_checkout_transaction_type'];
    $ecommerce_paypal_express_checkout_mode = $row['ecommerce_paypal_express_checkout_mode'];
    $ecommerce_paypal_express_checkout_api_username = $row['ecommerce_paypal_express_checkout_api_username'];
    $ecommerce_paypal_express_checkout_api_password = $row['ecommerce_paypal_express_checkout_api_password'];
    $ecommerce_paypal_express_checkout_api_signature = $row['ecommerce_paypal_express_checkout_api_signature'];
    $ecommerce_offline_payment = $row['ecommerce_offline_payment'];
    $ecommerce_offline_payment_only_specific_orders = $row['ecommerce_offline_payment_only_specific_orders'];
    $ecommerce_private_folder_id = $row['ecommerce_private_folder_id'];
    $ecommerce_retrieve_order_next_page_id = $row['ecommerce_retrieve_order_next_page_id'];
    $ecommerce_reward_program = $row['ecommerce_reward_program'];
    $ecommerce_reward_program_points = $row['ecommerce_reward_program_points'];
    $ecommerce_reward_program_membership = $row['ecommerce_reward_program_membership'];
    $ecommerce_reward_program_membership_days = $row['ecommerce_reward_program_membership_days'];
    $ecommerce_reward_program_email = $row['ecommerce_reward_program_email'];
    $ecommerce_reward_program_email_bcc_email_address = $row['ecommerce_reward_program_email_bcc_email_address'];
    $ecommerce_reward_program_email_subject = $row['ecommerce_reward_program_email_subject'];
    $ecommerce_reward_program_email_page_id = $row['ecommerce_reward_program_email_page_id'];
    $ecommerce_custom_product_field_1_label = $row['ecommerce_custom_product_field_1_label'];
    $ecommerce_custom_product_field_2_label = $row['ecommerce_custom_product_field_2_label'];
    $ecommerce_custom_product_field_3_label = $row['ecommerce_custom_product_field_3_label'];
    $ecommerce_custom_product_field_4_label = $row['ecommerce_custom_product_field_4_label'];
    $forms = $row['forms'];
    $calendars = $row['calendars'];
    $ads = $row['ads'];
    $affiliate_program = $row['affiliate_program'];
    $affiliate_default_commission_rate = $row['affiliate_default_commission_rate'];
    $affiliate_automatic_approval = $row['affiliate_automatic_approval'];
    $affiliate_contact_group_id = $row['affiliate_contact_group_id'];
    $affiliate_email_address = $row['affiliate_email_address'];
    $affiliate_group_offer_id = $row['affiliate_group_offer_id'];
    $additional_sitemap_content = $row['additional_sitemap_content'];
    $additional_robots_content = $row['additional_robots_content'];
    $debug = $row['debug'];
    $last_modified_user_id = $row['last_modified_user_id'];
    $last_modified_timestamp = $row['last_modified_timestamp'];

    $last_modified = '';

    if ($last_modified_timestamp) {

        $last_modified .=
            ' Last modified ' .
            get_relative_time(array('timestamp' => $last_modified_timestamp));

        if ($last_modified_user_id) {

            $last_modified_username = db_value(
                "SELECT user_username FROM user WHERE user_id = '$last_modified_user_id'");

            if ($last_modified_username != '') {
                $last_modified .= ' by ' . h($last_modified_username);
            }

        }

    }

    // format end of day time (cut off seconds)
    $ecommerce_end_of_day_time = mb_substr($ecommerce_end_of_day_time, 0, 5);

    if ($url_scheme == 'https://') {
        $secure_mode_checked = ' checked="checked"';
    } else {
        $secure_mode_checked = '';
    }

    if ($forgot_password_link == 1) {
        $forgot_password_link_checked = ' checked="checked"';
    } else {
        $forgot_password_link_checked = '';
    }

    // If the search type is "simple", then select that radio button.
    if ($search_type == 'simple') {
        $search_type_simple_checked = ' checked="checked"';
        $search_type_advanced_checked = '';
    
    // Otherwise the search type is "advanced", so select it.
    } else {
        $search_type_simple_checked = '';
        $search_type_advanced_checked = ' checked="checked"';
    }

    $mobile_checked = '';

    // If mobile is enabled, then check check box.
    if ($mobile == 1) {
        $mobile_checked = ' checked="checked"';
    }

    // Assume that social networking should not be checked until we find out otherwise.
    $social_networking_checked = '';

    // Assume that social networking rows should be hidden until we find out otherwise.
    $social_networking_type_row_style = 'display: none';
    $social_networking_services_row_style = 'display: none';
    $social_networking_code_row_style = 'display: none';

    // If social networking is enabled, then check check box and determine which other rows should be shown.
    if ($social_networking == 1) {
        $social_networking_checked = ' checked="checked"';

        $social_networking_type_row_style = '';

        // If social networking type is "simple", then show services row.
        if ($social_networking_type == 'simple') {
            $social_networking_services_row_style = '';

        // Otherwise social networking type is "advanced", so show code row.
        } else {
            $social_networking_code_row_style = '';
        }
    }
    
    // If the social networking type is "simple", then select that radio button.
    if ($social_networking_type == 'simple') {
        $social_networking_type_simple_checked = ' checked="checked"';
        $social_networking_type_advanced_checked = '';
    
    // Otherwise the social networking type is "advanced", so select it.
    } else {
        $social_networking_type_simple_checked = '';
        $social_networking_type_advanced_checked = ' checked="checked"';
    }
    
    // if facebook is enabled, then check check box
    if ($social_networking_facebook == 1) {
        $social_networking_facebook_checked = ' checked="checked"';
        
    // else facebook is disabled, so do not check check box
    } else {
        $social_networking_facebook_checked = '';
    }
    
    // if twitter is enabled, then check check box
    if ($social_networking_twitter == 1) {
        $social_networking_twitter_checked = ' checked="checked"';
        
    // else twitter is disabled, so do not check check box
    } else {
        $social_networking_twitter_checked = '';
    }
    
    // if addthis is enabled, then check check box
    if ($social_networking_addthis == 1) {
        $social_networking_addthis_checked = ' checked="checked"';
        
    // else addthis is disabled, so do not check check box
    } else {
        $social_networking_addthis_checked = '';
    }
    
    // if google plus one is enabled, then check check box
    if ($social_networking_plusone == 1) {
        $social_networking_plusone_checked = ' checked="checked"';
        
    // else google plus one is disabled, so do not check check box
    } else {
        $social_networking_plusone_checked = '';
    }
    
    // if linkedin is enabled, then check check box
    if ($social_networking_linkedin == 1) {
        $social_networking_linkedin_checked = ' checked="checked"';
        
    // else linkedin is disabled, so do not check check box
    } else {
        $social_networking_linkedin_checked = '';
    }
    
    if ($captcha == 1) {
        $captcha_checked = ' checked="checked"';
    } else {
        $captcha_checked = '';
    }

    if ($auto_dialogs == 1) {
        $auto_dialogs_checked = ' checked="checked"';
    } else {
        $auto_dialogs_checked = '';
    }

    if ($mass_deletion == 1) {
        $mass_deletion_checked = ' checked="checked"';
    } else {
        $mass_deletion_checked = '';
    }

    if ($strong_password == 1) {
        $strong_password_checked = ' checked="checked"';
    } else {
        $strong_password_checked = '';
    }

    if ($password_hint == 1) {
        $password_hint_checked = ' checked="checked"';
    } else {
        $password_hint_checked = '';
    }
    
    if ($remember_me == 1) {
        $remember_me_checked = ' checked="checked"';
    } else {
        $remember_me_checked = '';
    }

    if ($debug == 1) {
        $debug_checked = ' checked="checked"';
    } else {
        $debug_checked = '';
    }

    $timezones = get_timezones();

    // Check to see if the server's timezone is one of the timezones in our pick list
    // and get the label if it exists.
    $server_timezone_label = array_search(SERVER_TIMEZONE, $timezones);

    // If a label could not be found then just use the actual server's timezone for the label.
    if (!$server_timezone_label) {
        $server_timezone_label = SERVER_TIMEZONE;
    }

    $output_timezone_options = '<option value="">Server Default: ' . h($server_timezone_label) . '</option>';

    // If there is a value for the current timezone setting and it is not in our list of supported timezones,
    // then output a custom option for it.  We add this feature so that if someone needs
    // to use a timezone that is not in our list, they can manually set it in the database,
    // and they can continue to save the site settings without the value getting wiped out.
    if (($timezone != '') && (in_array($timezone, $timezones) == false)) {
        $output_timezone_options .= '<option value="' . h($timezone) . '" selected="selected">Custom: ' . h($timezone) . '</option>';
    }

    // Loop through the time zones in order to prepare options for pick list.
    foreach ($timezones as $label => $value) {
        $selected = '';

        // If this timezone is the current timezone, then select it.
        if ($value == $timezone) {
            $selected = ' selected="selected"';
        }

        $output_timezone_options .= '<option value="' . h($value) . '"' . $selected . '>' . h($label) . '</option>';
    }

    // get banned IP addresses, in order to prepare data for field
    $query = "SELECT ip_address FROM banned_ip_addresses ORDER BY id";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    $banned_ip_addresses = '';
    
    // loop through the banned IP addresses, in order to prepare data for field
    while ($row = mysqli_fetch_array($result)) {
        // if a banned IP address has already been added to the output, then output a new line
        if ($banned_ip_addresses != '') {
            $banned_ip_addresses .= "\n";
        }
        
        // add banned IP address to output
        $banned_ip_addresses .= $row['ip_address'];
    }

    // If the date format is "month_day", then select that radio button.
    if ($date_format == 'month_day') {
        $date_format_month_day_checked = ' checked="checked"';
        $date_format_day_month_checked = '';
    
    // Otherwise the date format is "day_month", so select it.
    } else {
        $date_format_month_day_checked = '';
        $date_format_day_month_checked = ' checked="checked"';
    }
    
    $page_editor_version_latest_checked = '';
    $page_editor_version_previous_checked = '';
    
    // if the latest editor is selected, then check that option
    if ($page_editor_version == 'latest') {
        $page_editor_version_latest_checked = ' checked="checked"';
    
    // else check the previous editor option
    } else {
        $page_editor_version_previous_checked = ' checked="checked"';
    }

    if ($page_editor_font == 1) {
        $page_editor_font_checked = ' checked="checked"';
    } else {
        $page_editor_font_checked = '';
    }

    if ($page_editor_font_size == 1) {
        $page_editor_font_size_checked = ' checked="checked"';
    } else {
        $page_editor_font_size_checked = '';
    }

    if ($page_editor_font_style == 1) {
        $page_editor_font_style_checked = ' checked="checked"';
    } else {
        $page_editor_font_style_checked = '';
    }

    if ($page_editor_font_color == 1) {
        $page_editor_font_color_checked = ' checked="checked"';
    } else {
        $page_editor_font_color_checked = '';
    }

    if ($page_editor_background_color == 1) {
        $page_editor_background_color_checked = ' checked="checked"';
    } else {
        $page_editor_background_color_checked = '';
    }
    
    $spell_checker_engine_info = get_spell_checker_engine_info();
    
    if ($membership_expiration_warning_email == 1) {
        $membership_expiration_warning_email_checked = ' checked="checked"';
    } else {
        $membership_expiration_warning_email_checked = '';
        
        $membership_expiration_warning_email_subject_row_style = 'display: none';
        $membership_expiration_warning_email_page_id_row_style = 'display: none';
        $membership_expiration_warning_email_days_before_expiration_row_style = 'display: none';
    }
    
    if ($ecommerce_on_or_off == 1) {
        $ecommerce_checked = ' checked="checked"';
    } else {
        $ecommerce_checked = '';
    }
    
    // get next order number
    $query = "SELECT next_order_number FROM next_order_number";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $ecommerce_next_order_number = $row['next_order_number'];
    
    if ($ecommerce_multicurrency == 1) {
        $ecommerce_multicurrency_checked = ' checked="checked"';
    } else {
        $ecommerce_multicurrency_checked = '';
    }
    
    if ($ecommerce_tax == 1) {
        $ecommerce_tax_checked = ' checked="checked"';
    } else {
        $ecommerce_tax_checked = '';
    }
    
    if ($ecommerce_tax_exempt == 1) {
        $ecommerce_tax_exempt_checked = ' checked="checked"';
    } else {
        $ecommerce_tax_exempt_checked = '';
    }
    
    if ($ecommerce_shipping == 1) {
        $ecommerce_shipping_checked = ' checked="checked"';
    } else {
        $ecommerce_shipping_checked = '';
    }

    if ($ecommerce_recipient_mode == 'single recipient') {
        $ecommerce_recipient_mode_single_recipient = ' checked="checked"';
        $ecommerce_recipient_mode_multirecipient = '';
    } else {
        $ecommerce_recipient_mode_single_recipient = '';
        $ecommerce_recipient_mode_multirecipient = ' checked="checked"';
    }
    
    if ($ecommerce_address_verification == 1) {
        $ecommerce_address_verification_checked = ' checked="checked"';
    }

    if ($ecommerce_address_verification_enforcement_type == 'warning') {
        $ecommerce_address_verification_enforcement_type_warning_checked = ' checked="checked"';
        $ecommerce_address_verification_enforcement_type_error_checked = '';
    
    } else {
        $ecommerce_address_verification_enforcement_type_warning_checked = '';
        $ecommerce_address_verification_enforcement_type_error_checked = ' checked="checked"';
    }
    
    if ($ecommerce_gift_card == 1) {
        $ecommerce_gift_card_checked = ' checked="checked"';
    } else {
        $ecommerce_gift_card_checked = '';
    }
    
    if ($ecommerce_gift_card_validity_days == 0) {
        $ecommerce_gift_card_validity_days = '';
    }

    if ($ecommerce_givex == 1) {
        $ecommerce_givex_checked = ' checked="checked"';
    } else {
        $ecommerce_givex_checked = '';
    }
    
    if ($ecommerce_credit_debit_card == 1) {
        $ecommerce_credit_debit_card_checked = ' checked="checked"';
    } else {
        $ecommerce_credit_debit_card_checked = '';
    }
    
    if ($ecommerce_american_express == 1) {
        $ecommerce_american_express_checked = ' checked="checked"';
    } else {
        $ecommerce_american_express_checked = '';
    }
    
    if ($ecommerce_diners_club == 1) {
        $ecommerce_diners_club_checked = ' checked="checked"';
    } else {
        $ecommerce_diners_club_checked = '';
    }
    
    if ($ecommerce_discover_card == 1) {
        $ecommerce_discover_card_checked = ' checked="checked"';
    } else {
        $ecommerce_discover_card_checked = '';
    }
    
    if ($ecommerce_mastercard == 1) {
        $ecommerce_mastercard_checked = ' checked="checked"';
    } else {
        $ecommerce_mastercard_checked = '';
    }

    if ($ecommerce_visa == 1) {
        $ecommerce_visa_checked = ' checked="checked"';
    } else {
        $ecommerce_visa_checked = '';
    }
    
    // prepare all pem file options for First Data Global Gateway pem file name picklist
    $query = "SELECT name FROM files WHERE (type = 'pem')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    while ($row = mysqli_fetch_assoc($result)) {
        // if file is the current selected pem file, select it by default
        if ($row['name'] == $ecommerce_first_data_global_gateway_pem_file_name) {
            $selected_or_not = ' selected="selected"';
        } else {
            $selected_or_not = '';
        }

        $ecommerce_first_data_global_gateway_pem_file_name_options .= '<option value="' . h($row['name']) . '"' . $selected_or_not . '>' . h($row['name']) . '</option>';
    }
    
    // initialize variables for holding select information for payment gateway pick list
    $ecommerce_payment_gateway_authorizenet = '';
    $ecommerce_payment_gateway_clearcommerce = '';
    $ecommerce_payment_gateway_first_data_global_gateway = '';
    $ecommerce_payment_gateway_paypal_payflow_pro = '';
    $ecommerce_payment_gateway_paypal_payments_pro = '';
    $ecommerce_payment_gateway_sage = '';
    $ecommerce_payment_gateway_stripe = '';
    
    // prepare payment gateway option to be selected
    switch ($ecommerce_payment_gateway) {
        case 'Authorize.Net':
            $ecommerce_payment_gateway_authorizenet = ' selected="selected"';
            break;
            
        case 'ClearCommerce':
            $ecommerce_payment_gateway_clearcommerce = ' selected="selected"';
            break;
            
        case 'First Data Global Gateway':
            $ecommerce_payment_gateway_first_data_global_gateway = ' selected="selected"';
            break;
            
        case 'PayPal Payflow Pro':
            $ecommerce_payment_gateway_paypal_payflow_pro = ' selected="selected"';
            break;
            
        case 'PayPal Payments Pro':
            $ecommerce_payment_gateway_paypal_payments_pro = ' selected="selected"';
            break;
            
        case 'Sage':
            $ecommerce_payment_gateway_sage = ' selected="selected"';
            break;

        case 'Stripe':
            $ecommerce_payment_gateway_stripe = ' selected="selected"';
            break;
    }
    
    if ($ecommerce_payment_gateway_transaction_type == 'Authorize & Capture') {
        $ecommerce_payment_gateway_transaction_type_authorize = '';
        $ecommerce_payment_gateway_transaction_type_authorize_and_capture = ' checked="checked"';
    } else {
        $ecommerce_payment_gateway_transaction_type_authorize = ' checked="checked"';
        $ecommerce_payment_gateway_transaction_type_authorize_and_capture = '';
    }
    
    if ($ecommerce_payment_gateway_mode == 'live') {
        $ecommerce_payment_gateway_mode_test = '';
        $ecommerce_payment_gateway_mode_live = ' checked="checked"';
    } else {
        $ecommerce_payment_gateway_mode_test = ' checked="checked"';
        $ecommerce_payment_gateway_mode_live = '';
    }

    // If the surcharge is set to 0, then output empty string instead of 0.
    if ($ecommerce_surcharge_percentage == 0) {
        $ecommerce_surcharge_percentage = '';

    // Otherwise, there is a value, so remove unnecessary zeros.
    } else {
        $ecommerce_surcharge_percentage = floatval($ecommerce_surcharge_percentage);
    }
    
    // assume that reset encryption key should not be disabled, until we find out otherwise
    $ecommerce_reset_encryption_key_disabled = '';
    $ecommerce_reset_encryption_key_disabled_message = '';
    
    // if mcrypt is disabled, then disable reset encryption key
    if ((extension_loaded('mcrypt') == FALSE) || (in_array('rijndael-256', mcrypt_list_algorithms()) == FALSE)) {
        $ecommerce_reset_encryption_key_disabled = ' disabled="disabled"';
        $ecommerce_reset_encryption_key_disabled_message = ' (MCrypt is disabled)';
    }
    
    if ($ecommerce_paypal_express_checkout == 1) {
        $ecommerce_paypal_express_checkout_checked = ' checked="checked"';
    } else {
        $ecommerce_paypal_express_checkout_checked = '';
    }
    
    if ($ecommerce_paypal_express_checkout_transaction_type == 'Authorize & Capture') {
        $ecommerce_paypal_express_checkout_transaction_type_authorize = '';
        $ecommerce_paypal_express_checkout_transaction_type_authorize_and_capture = ' checked="checked"';
    } else {
        $ecommerce_paypal_express_checkout_transaction_type_authorize = ' checked="checked"';
        $ecommerce_paypal_express_checkout_transaction_type_authorize_and_capture = '';
    }
    
    if ($ecommerce_paypal_express_checkout_mode == 'live') {
        $ecommerce_paypal_express_checkout_mode_sandbox = '';
        $ecommerce_paypal_express_checkout_mode_live = ' checked="checked"';
    } else {
        $ecommerce_paypal_express_checkout_mode_sandbox = ' checked="checked"';
        $ecommerce_paypal_express_checkout_mode_live = '';
    }
    
    if ($ecommerce_offline_payment == 1) {
        $ecommerce_offline_payment_checked = ' checked="checked"';
    } else {
        $ecommerce_offline_payment_checked = '';
    }
    
    if ($ecommerce_offline_payment_only_specific_orders == 1) {
        $ecommerce_offline_payment_only_specific_orders_checked = ' checked="checked"';
    } else {
        $ecommerce_offline_payment_only_specific_orders_checked = '';
    }
    
    if ($ecommerce_reward_program == 1) {
        $ecommerce_reward_program_checked = ' checked="checked"';
    } else {
        $ecommerce_reward_program_checked = '';
    }
    
    if ($ecommerce_reward_program_membership == 1) {
        $ecommerce_reward_program_membership_checked = ' checked="checked"';
    } else {
        $ecommerce_reward_program_membership_checked = '';
    }
    
    if ($ecommerce_reward_program_email == 1) {
        $ecommerce_reward_program_email_checked = ' checked="checked"';
    } else {
        $ecommerce_reward_program_email_checked = '';
    }
    
    // if membership days is 0 for reward program, then set value to blank
    if ($ecommerce_reward_program_membership_days == 0) {
        $ecommerce_reward_program_membership_days = '';
    }
    
    // initialize variables for determining if e-commerce rows are shown or hidden
    $ecommerce_multicurrency_row_style = 'display: none';        
    $ecommerce_tax_row_style = 'display: none';
    $ecommerce_tax_exempt_row_style = 'display: none';
    $ecommerce_tax_exempt_label_row_style = 'display: none';
    $ecommerce_shipping_row_style = 'display: none';
    $ecommerce_recipient_mode_row_style = 'display: none';
    $usps_user_id_row_style = 'display: none';
    $ecommerce_address_verification_row_style = 'display: none';
    $ecommerce_address_verification_enforcement_type_row_style = 'display: none';
    $ups_row_style = 'display: none';
    $ups_key_row_style = 'display: none';
    $ups_user_id_row_style = 'display: none';
    $ups_password_row_style = 'display: none';
    $ups_account_row_style = 'display: none';
    $ecommerce_product_restriction_message_row_style = 'display: none';
    $ecommerce_no_shipping_methods_message_row_style = 'display: none';
    $ecommerce_end_of_day_time_row_style = 'display: none';
    $ecommerce_next_order_number_row_style = 'display: none';
    $ecommerce_email_address_row_style = 'display: none';
    $ecommerce_gift_card_row_style = 'display: none';
    $ecommerce_gift_card_validity_days_row_style = 'display: none';
    $ecommerce_givex_row_style = 'display: none';
    $ecommerce_givex_primary_hostname_row_style = 'display: none';
    $ecommerce_givex_secondary_hostname_row_style = 'display: none';
    $ecommerce_givex_user_id_row_style = 'display: none';
    $ecommerce_givex_password_row_style = 'display: none';
    $ecommerce_payment_methods_row_style = 'display: none';
    $ecommerce_credit_debit_card_row_style = 'display: none';
    $ecommerce_accepted_cards_row_style = 'display: none';
    $ecommerce_payment_gateway_row_style = 'display: none';
    $ecommerce_payment_gateway_transaction_type_row_style = 'display: none';
    $ecommerce_payment_gateway_mode_row_style = 'display: none';
    $ecommerce_authorizenet_api_login_id_row_style = 'display: none';
    $ecommerce_authorizenet_transaction_key_row_style = 'display: none';
    $ecommerce_clearcommerce_client_id_row_style = 'display: none';
    $ecommerce_clearcommerce_user_id_row_style = 'display: none';
    $ecommerce_clearcommerce_password_row_style = 'display: none';
    $ecommerce_first_data_global_gateway_store_number_row_style = 'display: none';
    $ecommerce_first_data_global_gateway_pem_file_name_row_style = 'display: none';
    $ecommerce_paypal_payments_pro_gateway_mode_row_style = 'display: none';
    $ecommerce_paypal_payments_pro_api_username_row_style = 'display: none';
    $ecommerce_paypal_payments_pro_api_password_row_style = 'display: none';
    $ecommerce_paypal_payments_pro_api_signature_row_style = 'display: none';
    $ecommerce_paypal_payflow_pro_partner_row_style = 'display: none';
    $ecommerce_paypal_payflow_pro_merchant_login_row_style = 'display: none';
    $ecommerce_paypal_payflow_pro_user_row_style = 'display: none';
    $ecommerce_paypal_payflow_pro_password_row_style = 'display: none';
    $ecommerce_sage_merchant_id_row_style = 'display: none';
    $ecommerce_sage_merchant_key_row_style = 'display: none';
    $ecommerce_stripe_api_key_row_style = 'display: none';
    $ecommerce_surcharge_percentage_row_style = 'display: none';
    $ecommerce_reset_encryption_key_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_transaction_type_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_mode_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_api_username_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_api_password_row_style = 'display: none';
    $ecommerce_paypal_express_checkout_api_signature_row_style = 'display: none';
    $ecommerce_offline_payment_row_style = 'display: none';
    $ecommerce_offline_payment_only_specific_orders_row_style = 'display: none';
    $ecommerce_private_folder_id_row_style = 'display: none';
    $ecommerce_retrieve_order_next_page_id_row_style = 'display: none';
    $ecommerce_reward_program_row_style = 'display: none';
    $ecommerce_reward_program_points_row_style = 'display: none';
    $ecommerce_reward_program_membership_row_style = 'display: none';
    $ecommerce_reward_program_membership_days_row_style = 'display: none';
    $ecommerce_reward_program_email_row_style = 'display: none';
    $ecommerce_reward_program_email_bcc_email_address_row_style = 'display: none';
    $ecommerce_reward_program_email_subject_row_style = 'display: none';
    $ecommerce_reward_program_email_page_id_row_style = 'display: none';
    $ecommerce_custom_product_field_1_label_row_style = 'display: none';
    $ecommerce_custom_product_field_2_label_row_style = 'display: none';
    $ecommerce_custom_product_field_3_label_row_style = 'display: none';
    $ecommerce_custom_product_field_4_label_row_style = 'display: none';
    
    // if e-commerce is on then prepare to show e-commerce fields
    if ($ecommerce_on_or_off == 1) {
        $ecommerce_multicurrency_row_style = '';
        $ecommerce_tax_row_style = '';
        
        // if tax is on, then prepare to show tax exempt
        if ($ecommerce_tax == 1) {
            $ecommerce_tax_exempt_row_style = '';
            
            // if tax exempt is on, then prepare to show tax exempt label
            if ($ecommerce_tax_exempt == 1) {
                $ecommerce_tax_exempt_label_row_style = '';
            }
        }
        
        $ecommerce_shipping_row_style = '';
        
        // if shipping is on, then prepare to show shipping fields
        if ($ecommerce_shipping == 1) {
            $ecommerce_recipient_mode_row_style = '';
            $usps_user_id_row_style = '';
            $ecommerce_address_verification_row_style = '';
            
            if ($ecommerce_address_verification == 1) {
                $ecommerce_address_verification_enforcement_type_row_style = '';
            }

            $ups_row_style = '';
            $ups_key_row_style = '';
            $ups_user_id_row_style = '';
            $ups_password_row_style = '';
            $ups_account_row_style = '';
            
            $ecommerce_product_restriction_message_row_style = '';
            $ecommerce_no_shipping_methods_message_row_style = '';
            $ecommerce_end_of_day_time_row_style = '';
        }
        
        $ecommerce_next_order_number_row_style = '';
        $ecommerce_email_address_row_style = '';
        $ecommerce_gift_card_row_style = '';
        
        // if gift card is on, then prepare to show gift card fields
        if ($ecommerce_gift_card == 1) {
            $ecommerce_givex_row_style = '';
            $ecommerce_gift_card_validity_days_row_style = '';

            if ($ecommerce_givex == 1) {
                $ecommerce_givex_primary_hostname_row_style = '';
                $ecommerce_givex_secondary_hostname_row_style = '';
                $ecommerce_givex_user_id_row_style = '';
                $ecommerce_givex_password_row_style = '';
            }
        }
        
        $ecommerce_payment_methods_row_style = '';
        $ecommerce_credit_debit_card_row_style = '';
        
        // if credit/debit card is on, then prepare to show credit/debit card fields
        if ($ecommerce_credit_debit_card == 1) {
            $ecommerce_accepted_cards_row_style = '';
            $ecommerce_surcharge_percentage_row_style = '';
            $ecommerce_reset_encryption_key_row_style = '';
            $ecommerce_payment_gateway_row_style = '';
            
            // if there is a payment gateway selected, then prepare to show payment gateway fields
            if ($ecommerce_payment_gateway != '') {
                $ecommerce_payment_gateway_transaction_type_row_style = '';
                $ecommerce_payment_gateway_mode_row_style = '';
                
                // prepare payment gateway fields depending on which payment gateway is selected
                switch ($ecommerce_payment_gateway) {
                    case 'Authorize.Net':
                        $ecommerce_authorizenet_api_login_id_row_style = '';
                        $ecommerce_authorizenet_transaction_key_row_style = '';
                        break;
                        
                    case 'ClearCommerce':
                        $ecommerce_clearcommerce_client_id_row_style = '';
                        $ecommerce_clearcommerce_user_id_row_style = '';
                        $ecommerce_clearcommerce_password_row_style = '';
                        break;
                        
                    case 'First Data Global Gateway':
                        $ecommerce_first_data_global_gateway_store_number_row_style = '';
                        $ecommerce_first_data_global_gateway_pem_file_name_row_style = '';
                        break;
                        
                    case 'PayPal Payflow Pro':
                        $ecommerce_paypal_payflow_pro_partner_row_style = '';
                        $ecommerce_paypal_payflow_pro_merchant_login_row_style = '';
                        $ecommerce_paypal_payflow_pro_user_row_style = '';
                        $ecommerce_paypal_payflow_pro_password_row_style = '';
                        break;
                        
                    case 'PayPal Payments Pro':
                        $ecommerce_payment_gateway_mode_row_style = 'display: none';
                        $ecommerce_paypal_payments_pro_api_username_row_style = '';
                        $ecommerce_paypal_payments_pro_api_password_row_style = '';
                        $ecommerce_paypal_payments_pro_api_signature_row_style = '';
                        $ecommerce_paypal_payments_pro_gateway_mode_row_style = '';
                        break;
                        
                    case 'Sage':
                        $ecommerce_payment_gateway_mode_row_style = 'display: none';
                        $ecommerce_sage_merchant_id_row_style = '';
                        $ecommerce_sage_merchant_key_row_style = '';
                        break;

                    case 'Stripe':
                        $ecommerce_payment_gateway_mode_row_style = 'display: none';
                        $ecommerce_stripe_api_key_row_style = '';
                        break;
                }
            }
        }
        
        $ecommerce_paypal_express_checkout_row_style = '';
        
        // if PayPal Express Checkout is on, then prepare to show fields
        if ($ecommerce_paypal_express_checkout == 1) {
            $ecommerce_paypal_express_checkout_transaction_type_row_style = '';
            $ecommerce_paypal_express_checkout_mode_row_style = '';
            $ecommerce_paypal_express_checkout_api_username_row_style = '';
            $ecommerce_paypal_express_checkout_api_password_row_style = '';
            $ecommerce_paypal_express_checkout_api_signature_row_style = '';
        }
        
        $ecommerce_offline_payment_row_style = '';
        
        // if offline payment is checked, then show the only specific orders row
        if ($ecommerce_offline_payment == '1') {
            $ecommerce_offline_payment_only_specific_orders_row_style = '';
        }
        
        $ecommerce_private_folder_id_row_style = '';
        $ecommerce_retrieve_order_next_page_id_row_style = '';
        $ecommerce_reward_program_row_style = '';
        
        // if reward program is enabled, then prepare to show fields for that
        if ($ecommerce_reward_program == 1) {
            $ecommerce_reward_program_points_row_style = '';
            $ecommerce_reward_program_membership_row_style = '';
            
            // if membership for reward program is enabled, then prepare to show fields for that
            if ($ecommerce_reward_program_membership == 1) {
                $ecommerce_reward_program_membership_days_row_style = '';
            }
            
            $ecommerce_reward_program_email_row_style = '';
            
            // if e-mail for reward program is enabled, then prepare to show fields for that
            if ($ecommerce_reward_program_email == 1) {
                $ecommerce_reward_program_email_bcc_email_address_row_style = '';
                $ecommerce_reward_program_email_subject_row_style = '';
                $ecommerce_reward_program_email_page_id_row_style = '';
            }
        }

        $ecommerce_custom_product_field_1_label_row_style = '';
        $ecommerce_custom_product_field_2_label_row_style = '';
        $ecommerce_custom_product_field_3_label_row_style = '';
        $ecommerce_custom_product_field_4_label_row_style = '';
    }
    
    if ($forms == 1) {
        $forms_checked = ' checked="checked"';
    } else {
        $forms_checked = '';
    }
    
    if ($calendars == 1) {
        $calendars_checked = ' checked="checked"';
    } else {
        $calendars_checked = '';
    }

    if ($ads == 1) {
        $ads_checked = ' checked="checked"';
    } else {
        $ads_checked = '';
    }
    
    if ($affiliate_program == 1) {
        $affiliate_program_checked = ' checked="checked"';
    } else {
        $affiliate_program_checked = '';
        
        $affiliate_default_commission_rate_row_style = 'display: none';
        $affiliate_automatic_approval_row_style = 'display: none';
        $affiliate_contact_group_id_row_style = 'display: none';
        $affiliate_email_address_row_style = 'display: none';
        $affiliate_group_offer_id_row_style = 'display: none';
    }
    
    if ($affiliate_automatic_approval == 1) {
        $affiliate_automatic_approval_checked = ' checked="checked"';
    } else {
        $affiliate_automatic_approval_checked = '';
    }
    
    if ($visitor_tracking == 1) {
        $visitor_tracking_checked = ' checked="checked"';
    } else {
        $visitor_tracking_checked = '';
    }
    
    // if Google Analytics is enabled, check it and display the related rows
    if ($google_analytics == 1) {
        $google_analytics_checked = ' checked="checked"';
        
        $google_analytics_web_property_id_row_style = '';
        
    // else, do not check it and hide the related rows
    } else {
        $google_analytics_checked = '';
        
        $google_analytics_web_property_id_row_style = 'display: none';
    }
    
    $output =
output_header() . '
<div id="subnav">
	<h1>Site Settings</h1>
	<p>All site-wide settings and defaults.' . $last_modified . '</p>
</div>
<div id="content">
    
    ' . $liveform->output_errors() . '
    ' . $liveform->output_notices() . '
    <a href="#" id="help_link">Help</a>
    <div class="subheading">&nbsp;</div>
    <form name="form" action="settings.php" method="post" style="margin: 0px" autocomplete="off">
        <!--
            The following two fields are used to workaround a Safari bug where it incorrectly
            autofills the member id label field and payment service password field.
            https://discussions.apple.com/thread/5476502
            https://discussions.apple.com/thread/6027332
        -->
        <input id="fake_user_name" name="fake_user[name]" style="position:absolute; top:-100px;" type="text" value="No Autofill for Site Settings">
        <input id="fake_password" name="fake_password[name]" style="position:absolute; top:-100px;" type="password" value="No Autofill for Site Settings">
        ' . get_token_field() . '
        <h2>General</h2>
        <table>
            <tr>
                <td>Website IP Address:</td>
                <td>' . h($_SERVER['SERVER_ADDR']) . '</td>
            </tr>            
            <tr>
                <td>Hostname:</td>
                <td><input type="text" name="hostname" value="' . h($hostname) . '" size="40" maxlength="255" /></td>
            </tr>
			<tr>
                <td>Secure Mode:</td>
                <td><div><input type="checkbox" name="secure_mode" value="1"' . $secure_mode_checked . ' class="checkbox" />&nbsp;&nbsp; <strong>Warning:</strong> Do not enable <strong>Secure Mode</strong> until you have <a href="https://' . HOSTNAME_SETTING . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/test_secure_mode.php" target="_blank">verified</a> that your site has a working SSL Certificate.</div></td>
            </tr>
            <tr>
                <td>Support E-mail Address:</td>
                <td><input type="text" name="email_address" value="' . h($email_address) . '" size="40" /></td>
            </tr>
            <tr>
                <td>
                    <label for="title">Title:</label>
                </td>
                <td>
                    <input type="text" id="title" name="title" value="' . h($title) . '" maxlength="255" style="width: 98%">
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <label for="meta_description">Meta Description:</label>
                </td>
                <td>
                    <textarea id="meta_description" name="meta_description" maxlength="255" rows="3" style="width: 99%">'
                        . h($meta_description) .
                    '</textarea>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top">
                    <label for="meta_keywords">Meta Keywords:</label>
                </td>
                <td>
                    <textarea id="meta_keywords" name="meta_keywords" rows="3" style="width: 99%">'
                        . h($meta_keywords) .
                    '</textarea>
                </td>
            </tr>
            <tr>
                <td><label for="mobile">Enable Mobile:</label></td>
                <td><input type="checkbox" name="mobile" id="mobile" value="1"' . $mobile_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td><label for="social_networking">Enable Social Networking:</label></td>
                <td><input type="checkbox" name="social_networking" id="social_networking" value="1"' . $social_networking_checked . ' class="checkbox" onclick="show_or_hide_social_networking()" /></td>
            </tr>
            <tr id="social_networking_type_row" style="' . $social_networking_type_row_style . '">
                <td style="padding-left: 2em">Setup:</td>
                <td><input type="radio" id="social_networking_type_simple" name="social_networking_type" value="simple"' . $social_networking_type_simple_checked . ' class="radio" onclick="show_or_hide_social_networking_type()" /><label for="social_networking_type_simple">Simple</label> <input type="radio" id="social_networking_type_advanced" name="social_networking_type" value="advanced"' . $social_networking_type_advanced_checked . ' class="radio" onclick="show_or_hide_social_networking_type()" /><label for="social_networking_type_advanced">Advanced</label></td>
            </tr>
            <tr id="social_networking_services_row" style="' . $social_networking_services_row_style . '">
                <td style="padding-left: 2em">Services:</td>
                <td>
                    <input type="checkbox" name="social_networking_addthis" id="social_networking_addthis" value="1"' . $social_networking_addthis_checked . ' class="checkbox" /><label for="social_networking_addthis"> AddThis (i.e. share button for many services)</label><br />
                    <input type="checkbox" name="social_networking_twitter" id="social_networking_twitter" value="1"' . $social_networking_twitter_checked . ' class="checkbox" /><label for="social_networking_twitter"> Twitter</label><br />
                    <input type="checkbox" name="social_networking_facebook" id="social_networking_facebook" value="1"' . $social_networking_facebook_checked . ' class="checkbox" /><label for="social_networking_facebook"> Facebook</label><br />
                    <input type="checkbox" name="social_networking_plusone" id="social_networking_plusone" value="1"' . $social_networking_plusone_checked . ' class="checkbox" /><label for="social_networking_plusone"> Google +1</label><br />
                    <input type="checkbox" name="social_networking_linkedin" id="social_networking_linkedin" value="1"' . $social_networking_linkedin_checked . ' class="checkbox" /><label for="social_networking_linkedin"> LinkedIn</label><br />
                </td>
            </tr>
            <tr id="social_networking_code_row" style="' . $social_networking_code_row_style . '">
                <td style="padding-left: 2em; vertical-align: top">Code:</td>
                <td><textarea name="social_networking_code" rows="10" cols="80">' . h($social_networking_code) . '</textarea></td>
            </tr>
            <tr>
                <td><label for="captcha">Enable CAPTCHA:</label></td>
                <td><input type="checkbox" id="captcha" name="captcha" value="1"' . $captcha_checked . ' class="checkbox" /> (spam protection)</td>
            </tr>
            <tr>
                <td><label for="auto_dialogs">Enable Auto Dialogs:</label></td>
                <td><input type="checkbox" id="auto_dialogs" name="auto_dialogs" value="1"' . $auto_dialogs_checked . ' class="checkbox"></td>
            </tr>
            <tr>
                <td>Allow Mass Deletion:</td>
                <td><input type="checkbox" name="mass_deletion" value="1"' . $mass_deletion_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Require Strong Password:</td>
                <td><input type="checkbox" name="strong_password" value="1"' . $strong_password_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Allow Password Hint:</td>
                <td><input type="checkbox" name="password_hint" value="1"' . $password_hint_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Allow Remember Me:</td>
                <td><input type="checkbox" name="remember_me" value="1"' . $remember_me_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Forgot Password Link:</td>
                <td><input type="checkbox" name="forgot_password_link" value="1"' . $forgot_password_link_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Verbose Database Errors:</td>
                <td><input type="checkbox" name="debug" value="1"' . $debug_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Site Search Type:</td>
                <td><input type="radio" id="search_type_simple" name="search_type" value="simple"' . $search_type_simple_checked . ' class="radio" /><label for="search_type_simple">Simple</label> <input type="radio" id="search_type_advanced" name="search_type" value="advanced"' . $search_type_advanced_checked . ' class="radio" /><label for="search_type_advanced">Advanced</label></td>
            </tr>
            <tr>
                <td>Proxy Address:</td>
                <td><input type="text" name="proxy_address" value="' . h($proxy_address) . '" size="40" maxlength="255" /></td>
            </tr>
            <tr>
                <td>Default Badge Label:</td>
                <td><input type="text" name="badge_label" value="' . h($badge_label) . '" size="20" maxlength="100" /></td>
            </tr>
            <tr>
                <td style="vertical-align: top">Banned IP Addresses:</td>
                <td>
                    <table>
                        <tr>
                            <td style="vertical-align: top; padding: 0em 1em 0em 0em">
                                <textarea name="banned_ip_addresses" rows="10" cols="20">' . h($banned_ip_addresses) . '</textarea>
                            </td>
                            <td style="vertical-align: top; padding: 0em">
                                Enter one IP address per line.<br />
                                Wildcards are supported.<br />
                                For example:<br />
                                192.168.0.1<br />
                                192.168.1.*<br />
                                10.0.*.*
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <h2>Date &amp; Time</h2>
        <table>
            <tr>
                <td>Current Site Time:</td>
                <td>' . get_absolute_time(array('timestamp' => time(), 'timezone_type' => 'site')) . '</td>
            </tr>
            <tr>
                <td>Timezone:</td>
                <td><select name="timezone">' . $output_timezone_options . '</select></td>
            </tr>
            <tr>
                <td>Date Format:</td>
                <td>
                    <input type="radio" id="date_format_month_day" name="date_format" value="month_day"' . $date_format_month_day_checked . ' class="radio" /><label for="date_format_month_day">month/day/year (2/14/' . date('Y') . ')</label><br />
                    <input type="radio" id="date_format_day_month" name="date_format" value="day_month"' . $date_format_day_month_checked . ' class="radio" /><label for="date_format_day_month">day/month/year (14/2/' . date('Y') . ')</label>
                </td>
            </tr>
        </table>
        <h2>Rich-text Editor</h2>
        <table>
            <tr>
                <td>Editor Version:</td>
                <td><input type="radio" id="page_editor_version_latest" name="page_editor_version" value="latest"' . $page_editor_version_latest_checked . ' class="radio" /><label for="page_editor_version_latest">Latest</label> <input type="radio" id="page_editor_version_previous" name="page_editor_version" value="previous"' . $page_editor_version_previous_checked . ' class="radio" /><label for="page_editor_version_previous">Previous</label></td>
            </tr>
            <tr>
                <td>Font Selection:</td>
                <td><input type="checkbox" name="page_editor_font" value="1"' . $page_editor_font_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Font Size Selection:</td>
                <td><input type="checkbox" name="page_editor_font_size" value="1"' . $page_editor_font_size_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Font Style Selection:</td>
                <td><input type="checkbox" name="page_editor_font_style" value="1"' . $page_editor_font_style_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Font Color Button:</td>
                <td><input type="checkbox" name="page_editor_font_color" value="1"' . $page_editor_font_color_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Background Color Button:</td>
                <td><input type="checkbox" name="page_editor_background_color" value="1"' . $page_editor_background_color_checked . ' class="checkbox" /></td>
            </tr>
            <tr>
                <td>Spell Checker Engine:</td>
                <td>' . $spell_checker_engine_info['name'] . '</td>
            </tr>
        </table>
        <h2>Registration</h2>
        <table>
            <tr>
                <td>Registration Contact Group:</td>
                <td><select name="registration_contact_group_id"><option value="">-None-</option>' . select_contact_group($registration_contact_group_id, $user) . '</select></td>
            </tr>
            <tr>
                <td>Registration E-mail Address:</td>
                <td><input type="text" name="registration_email_address" value="' . h($registration_email_address) . '" size="40" /></td>
            </tr>
        </table>
        <h2>Membership</h2>
        <table>
            <tr>
                <td>Member ID Label:</td>
                <td><input type="text" name="member_id_label" value="' . h($member_id_label) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Membership Contact Group:</td>
                <td><select name="membership_contact_group_id"><option value="">-None-</option>' . select_contact_group($membership_contact_group_id, $user) . '</select></td>
            </tr>
            <tr>
                <td>Membership E-mail Address:</td>
                <td><input type="text" name="membership_email_address" value="' . h($membership_email_address) . '" size="40" /></td>
            </tr>
            <tr>
                <td><label for="membership_expiration_warning_email">Send Expiration Warning E-mail to Members:</label></td>
                <td><input type="checkbox" name="membership_expiration_warning_email" id="membership_expiration_warning_email" value="1"' . $membership_expiration_warning_email_checked . ' class="checkbox" onclick="show_or_hide_membership_expiration_warning_email()" /> (requires scheduled task for membership job)</td>
            </tr>
            <tr id="membership_expiration_warning_email_subject" style="' . $membership_expiration_warning_email_subject_row_style . '">
                <td style="padding-left: 2em">Subject:</td>
                <td><input name="membership_expiration_warning_email_subject" type="text" value="' . h($membership_expiration_warning_email_subject) . '" size="40" maxlength="255" /> (Member\'s Expiration Date will be appended to Subject)</td>
            </tr>
            <tr id="membership_expiration_warning_email_page_id" style="' . $membership_expiration_warning_email_page_id_row_style . '">
                <td style="padding-left: 2em">Page:</td>
                <td><select name="membership_expiration_warning_email_page_id"><option value="">-None-</option>' . select_page($membership_expiration_warning_email_page_id) . '</select></td>
            </tr>
            <tr id="membership_expiration_warning_email_days_before_expiration" style="' . $membership_expiration_warning_email_days_before_expiration_row_style . '">
                <td style="padding-left: 2em">Send:</td>
                <td><input name="membership_expiration_warning_email_days_before_expiration" type="text" value="' . h($membership_expiration_warning_email_days_before_expiration) . '" size="3" maxlength="4" /> days(s) before expiration date</td>
            </tr>
        </table>
        <h2>Forms</h2>
        <table>
            <tr>
                <td><label for="forms">Enable Forms:</label></td>
                <td><input type="checkbox" name="forms" id="forms" value="1"' . $forms_checked . ' class="checkbox" /></td>
            </tr>
        </table>
        <h2>Calendars</h2>
        <table>
            <tr>
                <td><label for="calendars">Enable Calendars:</label></td>
                <td><input type="checkbox" name="calendars" id="calendars" value="1"' . $calendars_checked . ' class="checkbox" /></td>
            </tr>
        </table>
        <h2>Ads</h2>
        <table>
            <tr>
                <td><label for="ads">Enable Ads:</label></td>
                <td><input type="checkbox" name="ads" id="ads" value="1"' . $ads_checked . ' class="checkbox" /></td>
            </tr>
        </table>
        <h2>Campaigns</h2>
        <table>
            <tr>
                <td>Organization Name:</td>
                <td><input type="text" name="organization_name" value="' . h($organization_name) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization Address 1:</td>
                <td><input type="text" name="organization_address_1" value="' . h($organization_address_1) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization Address 2:</td>
                <td><input type="text" name="organization_address_2" value="' . h($organization_address_2) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization City:</td>
                <td><input type="text" name="organization_city" value="' . h($organization_city) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization State:</td>
                <td><input type="text" name="organization_state" value="' . h($organization_state) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization Zip Code:</td>
                <td><input type="text" name="organization_zip_code" value="' . h($organization_zip_code) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Organization Country:</td>
                <td><input type="text" name="organization_country" value="' . h($organization_country) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Opt-In Label:</td>
                <td><input type="text" name="opt_in_label" value="' . h($opt_in_label) . '" size="40" maxlength="255" /></td>
            </tr>
            <tr>
                <td style="vertical-align: top">Plain Text Footer:</td>
                <td><textarea name="plain_text_email_campaign_footer" rows="5" cols="70">' . h($plain_text_email_campaign_footer) . '</textarea></td>
            </tr>
        </table>
        <h2>Commerce</h2>
        <table>
            <tr>
                <td><label for="ecommerce">Enable Commerce:</label></td>
                <td><input type="checkbox" name="ecommerce" id="ecommerce" value="1"' . $ecommerce_checked . ' class="checkbox" onclick="show_or_hide_ecommerce()" /></td>
            </tr>
            <tr id="ecommerce_multicurrency_row" style="' . $ecommerce_multicurrency_row_style . '">
                <td>Multi-Currency:</td>
                <td><input type="checkbox" name="ecommerce_multicurrency" id="ecommerce_multicurrency" value="1"' . $ecommerce_multicurrency_checked . ' class="checkbox" /></td>
            </tr>
            <tr id="ecommerce_tax_row" style="' . $ecommerce_tax_row_style . '">
                <td>Tax:</td>
                <td><input type="checkbox" name="ecommerce_tax" id="ecommerce_tax" value="1"' . $ecommerce_tax_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_tax()" /></td>
            </tr>
            <tr id="ecommerce_tax_exempt_row" style="' . $ecommerce_tax_exempt_row_style . '">
                <td style="padding-left: 2em">Allow Tax-Exempt:</td>
                <td><input type="checkbox" name="ecommerce_tax_exempt" id="ecommerce_tax_exempt" value="1"' . $ecommerce_tax_exempt_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_tax_exempt()" /></td>
            </tr>
            <tr id="ecommerce_tax_exempt_label_row" style="' . $ecommerce_tax_exempt_label_row_style . '">
                <td style="padding-left: 4em">Tax-Exempt Label:</td>
                <td><input type="text" name="ecommerce_tax_exempt_label" value="' . h($ecommerce_tax_exempt_label) . '" size="40" maxlength="255" /></td>
            </tr>
            <tr id="ecommerce_shipping_row" style="' . $ecommerce_shipping_row_style . '">
                <td><label for="ecommerce_shipping">Shipping:</label></td>
                <td><input type="checkbox" name="ecommerce_shipping" id="ecommerce_shipping" value="1"' . $ecommerce_shipping_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_shipping()" /></td>
            </tr>
            <tr id="ecommerce_recipient_mode_row" style="' . $ecommerce_recipient_mode_row_style . '">
                <td style="padding-left: 2em">Recipient Mode:</td>
                <td><input type="radio" name="ecommerce_recipient_mode" id="ecommerce_recipient_mode_single_recipient" value="single recipient"' . $ecommerce_recipient_mode_single_recipient . ' class="radio" /><label for="ecommerce_recipient_mode_single_recipient">Single Recipient</label> <input type="radio" name="ecommerce_recipient_mode" id="ecommerce_recipient_mode_multirecipient" value="multi-recipient"' . $ecommerce_recipient_mode_multirecipient . ' class="radio" /><label for="ecommerce_recipient_mode_multirecipient">Multi-Recipient</label></td>
            </tr>
            <tr id="usps_user_id_row" style="' . $usps_user_id_row_style . '">
                <td style="padding-left: 2em">
                    <label for="usps_user_id">USPS Web Tools User ID:</label>
                </td>
                <td>
                    <input
                        type="text"
                        id="usps_user_id"
                        name="usps_user_id"
                        value="' . h($usps_user_id) . '"
                        size="40"
                        maxlength="100">
                </td>
            </tr>
            <tr id="ecommerce_address_verification_row" style="' . $ecommerce_address_verification_row_style . '">
                <td style="padding-left: 2em">Verify US Addresses:</td>
                <td><input type="checkbox" name="ecommerce_address_verification" id="ecommerce_address_verification" value="1"' . $ecommerce_address_verification_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_address_verification()" /> (requires an approved USPS Web Tools account)</td>
            </tr>
            <tr id="ecommerce_address_verification_enforcement_type_row" style="' . $ecommerce_address_verification_enforcement_type_row_style . '">
                <td style="padding-left: 4em">Enforcement:</td>
                <td><input type="radio" id="ecommerce_address_verification_enforcement_type_warning" name="ecommerce_address_verification_enforcement_type" value="warning"' . $ecommerce_address_verification_enforcement_type_warning_checked . ' class="radio" /><label for="ecommerce_address_verification_enforcement_type_warning">Warning</label> <input type="radio" id="ecommerce_address_verification_enforcement_type_error" name="ecommerce_address_verification_enforcement_type" value="error"' . $ecommerce_address_verification_enforcement_type_error_checked . ' class="radio" /><label for="ecommerce_address_verification_enforcement_type_error">Error</label></td>
            </tr>
            <tr id="ups_row" style="' . $ups_row_style . '">
                <td style="padding-left: 2em" colspan="2">
                    <label for="ups_key">UPS:</label>
                </td>
            </tr>
            <tr id="ups_key_row" style="' . $ups_key_row_style . '">
                <td style="padding-left: 4em">
                    <label for="ups_key">Access Key:</label>
                </td>
                <td>
                    <input
                        type="text"
                        id="ups_key"
                        name="ups_key"
                        value="' . h($ups_key) . '"
                        size="40"
                        maxlength="100">
                </td>
            </tr>
            <tr id="ups_user_id_row" style="' . $ups_user_id_row_style . '">
                <td style="padding-left: 4em">
                    <label for="ups_user_id">User ID:</label>
                </td>
                <td>
                    <input
                        type="text"
                        id="ups_user_id"
                        name="ups_user_id"
                        value="' . h($ups_user_id) . '"
                        size="40"
                        maxlength="100">
                </td>
            </tr>
            <tr id="ups_password_row" style="' . $ups_password_row_style . '">
                <td style="padding-left: 4em">
                    <label for="ups_password">Password:</label>
                </td>
                <td>
                    <input
                        type="password"
                        id="ups_password"
                        name="ups_password"
                        value="' . h($ups_password) . '"
                        size="40"
                        maxlength="100">
                </td>
            </tr>
            <tr id="ups_account_row" style="' . $ups_account_row_style . '">
                <td style="padding-left: 4em">
                    <label for="ups_account">Account Number:</label>
                </td>
                <td>
                    <input
                        type="text"
                        id="ups_account"
                        name="ups_account"
                        value="' . h($ups_account) . '"
                        size="40"
                        maxlength="100">
                </td>
            </tr>
            <tr id="ecommerce_product_restriction_message_row" style="' . $ecommerce_product_restriction_message_row_style . '">
                <td style="padding-left: 2em">Product Restriction Message:</td>
                <td><input type="text" name="ecommerce_product_restriction_message" value="' . h($ecommerce_product_restriction_message) . '" size="40" maxlength="255" /></td>
            </tr>
            <tr id="ecommerce_no_shipping_methods_message_row" style="' . $ecommerce_no_shipping_methods_message_row_style . '">
                <td style="padding-left: 2em">No Shipping Methods Message:</td>
                <td><input type="text" name="ecommerce_no_shipping_methods_message" value="' . h($ecommerce_no_shipping_methods_message) . '" size="40" maxlength="255" /></td>
            </tr>
            <tr id="ecommerce_end_of_day_time_row" style="' . $ecommerce_end_of_day_time_row_style . '">
                <td style="padding-left: 2em">End of Day Time:</td>
                <td><input type="text" name="ecommerce_end_of_day_time" value="' . h($ecommerce_end_of_day_time) . '" size="5" maxlength="5" /> Format: hh:mm (i.e. 17:00), Current Site Time: ' . date("H:i", time()) . '</td>
            </tr>
            <tr id="ecommerce_next_order_number_row" style="' . $ecommerce_next_order_number_row_style . '">
                <td>Next Order Number:</td>
                <td><input type="text" name="ecommerce_next_order_number" value="' . $ecommerce_next_order_number . '" size="40" maxlength="20" /></td>
            </tr>
            <tr id="ecommerce_email_address_row" style="' . $ecommerce_email_address_row_style . '">
                <td>Commerce E-mail Address:</td>
                <td><input type="text" name="ecommerce_email_address" value="' . h($ecommerce_email_address) . '" size="40" /></td>
            </tr>
            <tr id="ecommerce_gift_card_row" style="' . $ecommerce_gift_card_row_style . '">
                <td><label for="ecommerce_gift_card">Accept Gift Cards:</label></td>
                <td><input type="checkbox" name="ecommerce_gift_card" id="ecommerce_gift_card" value="1"' . $ecommerce_gift_card_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_gift_card()" /></td>
            </tr>
            <tr id="ecommerce_gift_card_validity_days_row" style="' . $ecommerce_gift_card_validity_days_row_style . '">
                <td style="padding-left: 2em">Validity Length:</td>
                <td><input type="text" name="ecommerce_gift_card_validity_days" value="' . $ecommerce_gift_card_validity_days . '" size="5" maxlength="5">&nbsp; day(s) (leave blank for no expiration)</td>
            </tr>
            <tr id="ecommerce_givex_row" style="' . $ecommerce_givex_row_style . '">
                <td style="padding-left: 2em"><label for="ecommerce_givex">Accept Givex Cards:</label></td>
                <td><input type="checkbox" name="ecommerce_givex" id="ecommerce_givex" value="1"' . $ecommerce_givex_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_givex()" /> (requires Givex service)</td>
            </tr>
            <tr id="ecommerce_givex_primary_hostname_row" style="' . $ecommerce_givex_primary_hostname_row_style . '">
                <td style="padding-left: 4em">Primary Hostname:</td>
                <td><input type="text" name="ecommerce_givex_primary_hostname" value="' . h($ecommerce_givex_primary_hostname) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_givex_secondary_hostname_row" style="' . $ecommerce_givex_secondary_hostname_row_style . '">
                <td style="padding-left: 4em">Secondary Hostname:</td>
                <td><input type="text" name="ecommerce_givex_secondary_hostname" value="' . h($ecommerce_givex_secondary_hostname) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_givex_user_id_row" style="' . $ecommerce_givex_user_id_row_style . '">
                <td style="padding-left: 4em">User ID:</td>
                <td><input type="text" name="ecommerce_givex_user_id" value="' . h($ecommerce_givex_user_id) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_givex_password_row" style="' . $ecommerce_givex_password_row_style . '">
                <td style="padding-left: 4em">Password:</td>
                <td><input type="text" name="ecommerce_givex_password" value="' . h($ecommerce_givex_password) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_payment_methods_row" style="' . $ecommerce_payment_methods_row_style . '">
                <td>Payment Methods:</td>
                <td>&nbsp;</td>
            </tr>
            <tr id="ecommerce_credit_debit_card_row" style="' . $ecommerce_credit_debit_card_row_style . '">
                <td style="padding-left: 2em">Credit/Debit Card:</td>
                <td><input type="checkbox" name="ecommerce_credit_debit_card" id="ecommerce_credit_debit_card" value="1"' . $ecommerce_credit_debit_card_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_credit_debit_card()" /></td>
            </tr>
            <tr id="ecommerce_accepted_cards_row" style="' . $ecommerce_accepted_cards_row_style . '">
                <td style="vertical-align: top; padding-left: 4em">Accepted Cards:</td>
                <td>
                    <input type="checkbox" name="ecommerce_american_express" id="ecommerce_american_express" value="1"' . $ecommerce_american_express_checked . ' class="checkbox" /><label for="ecommerce_american_express"> American Express</label><br />
                    <input type="checkbox" name="ecommerce_diners_club" id="ecommerce_diners_club" value="1"' . $ecommerce_diners_club_checked . ' class="checkbox" /><label for="ecommerce_diners_club"> Diners Club</label><br />
                    <input type="checkbox" name="ecommerce_discover_card" id="ecommerce_discover_card" value="1"' . $ecommerce_discover_card_checked . ' class="checkbox" /><label for="ecommerce_discover_card"> Discover Card</label><br />
                    <input type="checkbox" name="ecommerce_mastercard" id="ecommerce_mastercard" value="1"' . $ecommerce_mastercard_checked . ' class="checkbox" /><label for="ecommerce_mastercard"> MasterCard</label><br />
                    <input type="checkbox" name="ecommerce_visa" id="ecommerce_visa" value="1"' . $ecommerce_visa_checked . ' class="checkbox" /><label for="ecommerce_visa"> Visa</label><br />
                </td>
            </tr>
            <tr id="ecommerce_payment_gateway_row" style="' . $ecommerce_payment_gateway_row_style . '">
                <td style="padding-left: 4em">Payment Gateway:</td>
                <td><select name="ecommerce_payment_gateway" id="ecommerce_payment_gateway" onchange="show_or_hide_ecommerce_payment_gateway()"><option value="">-None-</option><option value="Authorize.Net"' . $ecommerce_payment_gateway_authorizenet . '>Authorize.Net</option><option value="ClearCommerce"' . $ecommerce_payment_gateway_clearcommerce . '>ClearCommerce/PayFuse</option><option value="First Data Global Gateway"' . $ecommerce_payment_gateway_first_data_global_gateway . '>First Data Global Gateway</option><option value="PayPal Payflow Pro"' . $ecommerce_payment_gateway_paypal_payflow_pro . '>PayPal Payflow Pro</option><option value="PayPal Payments Pro"' . $ecommerce_payment_gateway_paypal_payments_pro . '>PayPal Payments Pro</option><option value="Sage"' . $ecommerce_payment_gateway_sage . '>Sage</option><option value="Stripe"' . $ecommerce_payment_gateway_stripe . '>Stripe</option></select></td>
            </tr>
            <tr id="ecommerce_payment_gateway_transaction_type_row" style="' . $ecommerce_payment_gateway_transaction_type_row_style . '">
                <td style="padding-left: 6em">Transaction Type:</td>
                <td><input type="radio" name="ecommerce_payment_gateway_transaction_type" id="ecommerce_payment_gateway_transaction_type_authorize" value="Authorize"' . $ecommerce_payment_gateway_transaction_type_authorize . ' class="radio" /><label for="ecommerce_payment_gateway_transaction_type_authorize">Authorize</label> <input type="radio" name="ecommerce_payment_gateway_transaction_type" id="ecommerce_payment_gateway_transaction_type_authorize_and_capture" value="Authorize &amp; Capture"' . $ecommerce_payment_gateway_transaction_type_authorize_and_capture . ' class="radio" /><label for="ecommerce_payment_gateway_transaction_type_authorize_and_capture">Authorize &amp; Capture</label></td>
            </tr>
            <tr id="ecommerce_payment_gateway_mode_row" style="' . $ecommerce_payment_gateway_mode_row_style . '">
                <td style="padding-left: 6em">Mode:</td>
                <td><input type="radio" name="ecommerce_payment_gateway_mode" id="ecommerce_payment_gateway_mode_test" value="test"' . $ecommerce_payment_gateway_mode_test . ' class="radio" /><label for="ecommerce_payment_gateway_mode_test">Test</label> <input type="radio" name="ecommerce_payment_gateway_mode" id="ecommerce_payment_gateway_mode_live" value="live"' . $ecommerce_payment_gateway_mode_live . ' class="radio" /><label for="ecommerce_payment_gateway_mode_live">Live</label></td>
            </tr>
            <tr id="ecommerce_paypal_payments_pro_gateway_mode_row" style="' . $ecommerce_paypal_payments_pro_gateway_mode_row_style . '">
                <td style="padding-left: 6em">Mode:</td>
                <td><input type="radio" name="ecommerce_paypal_payments_pro_gateway_mode" id="ecommerce_paypal_payments_pro_gateway_mode_test" value="test"' . $ecommerce_payment_gateway_mode_test . ' class="radio" /><label for="ecommerce_paypal_payments_pro_gateway_mode_test">Sandbox</label> <input type="radio" name="ecommerce_paypal_payments_pro_gateway_mode" id="ecommerce_paypal_payments_pro_gateway_mode_live" value="live"' . $ecommerce_payment_gateway_mode_live . ' class="radio" /><label for="ecommerce_paypal_payments_pro_gateway_mode_live">Live</label></td>
            </tr>
            <tr id="ecommerce_authorizenet_api_login_id_row" style="' . $ecommerce_authorizenet_api_login_id_row_style . '">
                <td style="padding-left: 6em">API Login ID:</td>
                <td><input type="text" name="ecommerce_authorizenet_api_login_id" value="' . h($ecommerce_authorizenet_api_login_id) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_authorizenet_transaction_key_row" style="' . $ecommerce_authorizenet_transaction_key_row_style . '">
                <td style="padding-left: 6em">Transaction Key:</td>
                <td><input type="password" name="ecommerce_authorizenet_transaction_key" value="' . h($ecommerce_authorizenet_transaction_key) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_clearcommerce_client_id_row" style="' . $ecommerce_clearcommerce_client_id_row_style . '">
                <td style="padding-left: 6em">Client ID:</td>
                <td><input type="text" name="ecommerce_clearcommerce_client_id" value="' . h($ecommerce_clearcommerce_client_id) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_clearcommerce_user_id_row" style="' . $ecommerce_clearcommerce_user_id_row_style . '">
                <td style="padding-left: 6em">User ID:</td>
                <td><input type="text" name="ecommerce_clearcommerce_user_id" value="' . h($ecommerce_clearcommerce_user_id) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_clearcommerce_password_row" style="' . $ecommerce_clearcommerce_password_row_style . '">
                <td style="padding-left: 6em">Password:</td>
                <td><input type="password" name="ecommerce_clearcommerce_password" value="' . h($ecommerce_clearcommerce_password) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_first_data_global_gateway_store_number_row" style="' . $ecommerce_first_data_global_gateway_store_number_row_style . '">
                <td style="padding-left: 6em">Store Number:</td>
                <td><input type="text" name="ecommerce_first_data_global_gateway_store_number" value="' . h($ecommerce_first_data_global_gateway_store_number) . '" size="40" maxlength="100" /></td>
            </tr>
            
            <tr id="ecommerce_first_data_global_gateway_pem_file_name_row" style="' . $ecommerce_first_data_global_gateway_pem_file_name_row_style . '">
                <td style="padding-left: 6em">PEM File:</td>
                <td><select name="ecommerce_first_data_global_gateway_pem_file_name" id="ecommerce_first_data_global_gateway_pem_file_name"><option value="">-None-</option>' . $ecommerce_first_data_global_gateway_pem_file_name_options . '</select></td>
            </tr>
            <tr id="ecommerce_paypal_payflow_pro_partner_row" style="' . $ecommerce_paypal_payflow_pro_partner_row_style . '">
                <td style="padding-left: 6em">Partner:</td>
                <td><input type="text" name="ecommerce_paypal_payflow_pro_partner" value="' . h($ecommerce_paypal_payflow_pro_partner) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payflow_pro_merchant_login_row" style="' . $ecommerce_paypal_payflow_pro_merchant_login_row_style . '">
                <td style="padding-left: 6em">Merchant Login:</td>
                <td><input type="text" name="ecommerce_paypal_payflow_pro_merchant_login" value="' . h($ecommerce_paypal_payflow_pro_merchant_login) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payflow_pro_user_row" style="' . $ecommerce_paypal_payflow_pro_user_row_style . '">
                <td style="padding-left: 6em">User:</td>
                <td><input type="text" name="ecommerce_paypal_payflow_pro_user" value="' . h($ecommerce_paypal_payflow_pro_user) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payflow_pro_password_row" style="' . $ecommerce_paypal_payflow_pro_password_row_style . '">
                <td style="padding-left: 6em">Password:</td>
                <td><input type="password" name="ecommerce_paypal_payflow_pro_password" value="' . h($ecommerce_paypal_payflow_pro_password) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payments_pro_api_username_row" style="' . $ecommerce_paypal_payments_pro_api_username_row_style . '">
                <td style="padding-left: 6em">API Username:</td>
                <td><input type="text" name="ecommerce_paypal_payments_pro_api_username" value="' . h($ecommerce_paypal_payments_pro_api_username) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payments_pro_api_password_row" style="' . $ecommerce_paypal_payments_pro_api_password_row_style . '">
                <td style="padding-left: 6em">API Password:</td>
                <td><input type="password" name="ecommerce_paypal_payments_pro_api_password" value="' . h($ecommerce_paypal_payments_pro_api_password) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_payments_pro_api_signature_row" style="' . $ecommerce_paypal_payments_pro_api_signature_row_style . '">
                <td style="padding-left: 6em">API Signature:</td>
                <td><input type="password" name="ecommerce_paypal_payments_pro_api_signature" value="' . h($ecommerce_paypal_payments_pro_api_signature) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_sage_merchant_id_row" style="' . $ecommerce_sage_merchant_id_row_style . '">
                <td style="padding-left: 6em">Merchant ID:</td>
                <td><input type="text" name="ecommerce_sage_merchant_id" value="' . h($ecommerce_sage_merchant_id) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_sage_merchant_key_row" style="' . $ecommerce_sage_merchant_key_row_style . '">
                <td style="padding-left: 6em">Merchant Key:</td>
                <td><input type="password" name="ecommerce_sage_merchant_key" value="' . h($ecommerce_sage_merchant_key) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_stripe_api_key_row" style="' . $ecommerce_stripe_api_key_row_style . '">
                <td style="padding-left: 6em">API Key:</td>
                <td><input type="password" name="ecommerce_stripe_api_key" value="' . h($ecommerce_stripe_api_key) . '" size="40" maxlength="100" /> &nbsp;(Enter the Test or Live Secret Key)</td>
            </tr>
            <tr id="ecommerce_surcharge_percentage_row" style="' . $ecommerce_surcharge_percentage_row_style . '">
                <td style="padding-left: 4em">Surcharge:</td>
                <td><input type="text" name="ecommerce_surcharge_percentage" value="' . $ecommerce_surcharge_percentage . '" size="6" maxlength="7" /> %</td>
            </tr>
            <tr id="ecommerce_reset_encryption_key_row" style="' . $ecommerce_reset_encryption_key_row_style . '">
                <td style="padding-left: 4em"><label for="ecommerce_reset_encryption_key">Reset Encryption Key:</label></td>
                <td><input type="checkbox" id="ecommerce_reset_encryption_key" name="ecommerce_reset_encryption_key" value="1"' . $ecommerce_reset_encryption_key_disabled . ' class="checkbox" />' . $ecommerce_reset_encryption_key_disabled_message . '</td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_row" style="' . $ecommerce_paypal_express_checkout_row_style . '">
                <td style="padding-left: 2em">PayPal Express Checkout:</td>
                <td><input type="checkbox" name="ecommerce_paypal_express_checkout" id="ecommerce_paypal_express_checkout" value="1"' . $ecommerce_paypal_express_checkout_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_paypal_express_checkout()" /></td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_transaction_type_row" style="' . $ecommerce_paypal_express_checkout_transaction_type_row_style . '">
                <td style="padding-left: 4em">Transaction Type:</td>
                <td><input type="radio" name="ecommerce_paypal_express_checkout_transaction_type" id="ecommerce_paypal_express_checkout_transaction_type_authorize" value="Authorize"' . $ecommerce_paypal_express_checkout_transaction_type_authorize . ' class="radio" /><label for="ecommerce_paypal_express_checkout_transaction_type_authorize">Authorize</label> <input type="radio" name="ecommerce_paypal_express_checkout_transaction_type" id="ecommerce_paypal_express_checkout_transaction_type_authorize_and_capture" value="Authorize &amp; Capture"' . $ecommerce_paypal_express_checkout_transaction_type_authorize_and_capture . ' class="radio" /><label for="ecommerce_paypal_express_checkout_transaction_type_authorize_and_capture">Authorize &amp; Capture</label></td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_mode_row" style="' . $ecommerce_paypal_express_checkout_mode_row_style . '">
                <td style="padding-left: 4em">Mode:</td>
                <td><input type="radio" name="ecommerce_paypal_express_checkout_mode" id="ecommerce_paypal_express_checkout_mode_sandbox" value="sandbox"' . $ecommerce_paypal_express_checkout_mode_sandbox . ' class="radio" /><label for="ecommerce_paypal_express_checkout_mode_sandbox">Sandbox</label> <input type="radio" name="ecommerce_paypal_express_checkout_mode" id="ecommerce_paypal_express_checkout_mode_live" value="live"' . $ecommerce_paypal_express_checkout_mode_live . ' class="radio" /><label for="ecommerce_paypal_express_checkout_mode_live">Live</label></td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_api_username_row" style="' . $ecommerce_paypal_express_checkout_api_username_row_style . '">
                <td style="padding-left: 4em">API Username:</td>
                <td><input type="text" name="ecommerce_paypal_express_checkout_api_username" value="' . h($ecommerce_paypal_express_checkout_api_username) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_api_password_row" style="' . $ecommerce_paypal_express_checkout_api_password_row_style . '">
                <td style="padding-left: 4em">API Password:</td>
                <td><input type="password" name="ecommerce_paypal_express_checkout_api_password" value="' . h($ecommerce_paypal_express_checkout_api_password) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_paypal_express_checkout_api_signature_row" style="' . $ecommerce_paypal_express_checkout_api_signature_row_style . '">
                <td style="padding-left: 4em">API Signature:</td>
                <td><input type="password" name="ecommerce_paypal_express_checkout_api_signature" value="' . h($ecommerce_paypal_express_checkout_api_signature) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_offline_payment_row" style="' . $ecommerce_offline_payment_row_style . '">
                <td style="padding-left: 2em">Allow Offline Payments:</td>
                <td><input type="checkbox" name="ecommerce_offline_payment" id="ecommerce_offline_payment" value="1"' . $ecommerce_offline_payment_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_offline_payment()" /></td>
            </tr>
            <tr id="ecommerce_offline_payment_only_specific_orders_row" style="' . $ecommerce_offline_payment_only_specific_orders_row_style . '">
                <td style="padding-left: 4em">Only on specific orders:</td>
                <td><input type="checkbox" name="ecommerce_offline_payment_only_specific_orders" id="ecommerce_offline_payment_only_specific_orders" value="1"' . $ecommerce_offline_payment_only_specific_orders_checked . ' class="checkbox" /></td>
            </tr>
            <tr id="ecommerce_private_folder_id_row" style="' . $ecommerce_private_folder_id_row_style . '">

                <td>
                    <label for="ecommerce_private_folder_id">
                        Grant Private Access:
                    </label>
                </td>

                <td>
                    <select id="ecommerce_private_folder_id" name="ecommerce_private_folder_id">
                        <option value=""></option>
                        ' . select_folder($ecommerce_private_folder_id, 0, 0, 0, array(), array(), 'private') . '
                    </select>
                </td>

            </tr>
            <tr id="ecommerce_retrieve_order_next_page_id_row" style="' . $ecommerce_retrieve_order_next_page_id_row_style . '">
                <td>Reorder/Retrieve Order Next Page:</td>
                <td><select name="ecommerce_retrieve_order_next_page_id"><option value="">-None-</option>' . select_page($ecommerce_retrieve_order_next_page_id) . '</select></td>
            </tr>
            <tr id="ecommerce_reward_program_row" style="' . $ecommerce_reward_program_row_style . '">
                <td><label for="ecommerce_reward_program">Enable Reward Program:</label></td>
                <td><input type="checkbox" name="ecommerce_reward_program" id="ecommerce_reward_program" value="1"' . $ecommerce_reward_program_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_reward_program()" /></td>
            </tr>
            <tr id="ecommerce_reward_program_points_row" style="' . $ecommerce_reward_program_points_row_style . '">
                <td style="padding-left: 2em">Goal:</td>
                <td><input type="text" name="ecommerce_reward_program_points" value="' . $ecommerce_reward_program_points . '" size="5" maxlength="9" /> point(s)</td>
            </tr>
            <tr id="ecommerce_reward_program_membership_row" style="' . $ecommerce_reward_program_membership_row_style . '">
                <td style="padding-left: 2em"><label for="ecommerce_reward_program_membership">Grant Membership:</label></td>
                <td><input type="checkbox" name="ecommerce_reward_program_membership" id="ecommerce_reward_program_membership" value="1"' . $ecommerce_reward_program_membership_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_reward_program_membership()" /></td>
            </tr>
            <tr id="ecommerce_reward_program_membership_days_row" style="' . $ecommerce_reward_program_membership_days_row_style . '">
                <td style="padding-left: 4em">Membership Length:</td>
                <td><input type="text" name="ecommerce_reward_program_membership_days" value="' . $ecommerce_reward_program_membership_days . '" size="5" maxlength="5" /> day(s) (leave blank for lifetime membership)</td>
            </tr>
            <tr id="ecommerce_reward_program_email_row" style="' . $ecommerce_reward_program_email_row_style . '">
                <td style="padding-left: 2em"><label for="ecommerce_reward_program_email">Send E-mail:</label></td>
                <td><input type="checkbox" name="ecommerce_reward_program_email" id="ecommerce_reward_program_email" value="1"' . $ecommerce_reward_program_email_checked . ' class="checkbox" onclick="show_or_hide_ecommerce_reward_program_email()" /></td>
            </tr>
            <tr id="ecommerce_reward_program_email_bcc_email_address_row" style="' . $ecommerce_reward_program_email_bcc_email_address_row_style . '">
                <td style="padding-left: 4em">BCC E-mail Address:</td>
                <td><input type="text" name="ecommerce_reward_program_email_bcc_email_address" value="' . h($ecommerce_reward_program_email_bcc_email_address) . '" size="40" maxlength="100" /></td>
            </tr>
            <tr id="ecommerce_reward_program_email_subject_row" style="' . $ecommerce_reward_program_email_subject_row_style . '">
                <td style="padding-left: 4em">Subject:</td>
                <td><input type="text" name="ecommerce_reward_program_email_subject" value="' . h($ecommerce_reward_program_email_subject) . '" size="80" maxlength="255" /></td>
            </tr>
            <tr id="ecommerce_reward_program_email_page_id_row" style="' . $ecommerce_reward_program_email_page_id_row_style . '">
                <td style="padding-left: 4em">Page:</td>
                <td><select name="ecommerce_reward_program_email_page_id"><option value=""></option>' . select_page($ecommerce_reward_program_email_page_id) . '</select></td>
            </tr>
            <tr id="ecommerce_custom_product_field_1_label_row" style="' . $ecommerce_custom_product_field_1_label_row_style . '">
                <td>Custom Product Field #1 Label:</td>
                <td><input type="text" name="ecommerce_custom_product_field_1_label" value="' . h($ecommerce_custom_product_field_1_label) . '" size="40" /></td>
            </tr>
            <tr id="ecommerce_custom_product_field_2_label_row" style="' . $ecommerce_custom_product_field_2_label_row_style . '">
                <td>Custom Product Field #2 Label:</td>
                <td><input type="text" name="ecommerce_custom_product_field_2_label" value="' . h($ecommerce_custom_product_field_2_label) . '" size="40" /></td>
            </tr>
            <tr id="ecommerce_custom_product_field_3_label_row" style="' . $ecommerce_custom_product_field_3_label_row_style . '">
                <td>Custom Product Field #3 Label:</td>
                <td><input type="text" name="ecommerce_custom_product_field_3_label" value="' . h($ecommerce_custom_product_field_3_label) . '" size="40" /></td>
            </tr>
            <tr id="ecommerce_custom_product_field_4_label_row" style="' . $ecommerce_custom_product_field_4_label_row_style . '">
                <td>Custom Product Field #4 Label:</td>
                <td><input type="text" name="ecommerce_custom_product_field_4_label" value="' . h($ecommerce_custom_product_field_4_label) . '" size="40" /></td>
            </tr>
        </table>
        <h2>Affiliate Program</h2>
        <table>
            <tr>
                <td>Enable Affiliate Program:</td>
                <td><input type="checkbox" name="affiliate_program" id="affiliate_program" value="1"' . $affiliate_program_checked . ' class="checkbox" onclick="show_or_hide_affiliate_program()" /></td>
            </tr>
            <tr id="affiliate_default_commission_rate_row" style="' . $affiliate_default_commission_rate_row_style . '">
                <td>Default Commission Rate:</td>
                <td><input type="text" name="affiliate_default_commission_rate" value="' . $affiliate_default_commission_rate . '" size="6" maxlength="6" /> %</td>
            </tr>
            <tr id="affiliate_automatic_approval_row" style="' . $affiliate_automatic_approval_row_style . '">
                <td>Automatically Approve Affiliates:</td>
                <td><input type="checkbox" name="affiliate_automatic_approval" id="affiliate_automatic_approval" value="1"' . $affiliate_automatic_approval_checked . ' class="checkbox" /></td>
            </tr>
            <tr id="affiliate_contact_group_id_row" style="' . $affiliate_contact_group_id_row_style . '">
                <td>Affiliate Contact Group:</td>
                <td><select name="affiliate_contact_group_id"><option value=""></option>' . select_contact_group($affiliate_contact_group_id, $user) . '</select></td>
            </tr>
            <tr id="affiliate_email_address_row" style="' . $affiliate_email_address_row_style . '">
                <td>Administrator E-mail Address:</td>
                <td><input type="text" name="affiliate_email_address" value="' . h($affiliate_email_address) . '" size="40" /></td>
            </tr>
            <tr id="affiliate_group_offer_id_row" style="' . $affiliate_group_offer_id_row_style . '">
                <td>Group Offer:</td>
                <td><select name="affiliate_group_offer_id"><option value=""></option>' . select_offer($affiliate_group_offer_id) . '</select></td>
            </tr>
        </table>
        <h2>Visitors</h2>
        <table>
            <tr>
                <td><label for="visitor_tracking">Enable Visitor Tracking:</label></td>
                <td><input type="checkbox" id="visitor_tracking" name="visitor_tracking" value="1"' . $visitor_tracking_checked . ' class="checkbox" /></td>
            </tr>

            <tr>
                <td><label for="tracking_code_duration">Tracking Code Duration:</label></td>
                <td>
                    <input
                        type="number"
                        id="tracking_code_duration"
                        name="tracking_code_duration"
                        value="' . h($tracking_code_duration) . '"
                        size="3"
                        min="1"
                        max="365"
                        required
                        style="width: 50px">&nbsp;

                    day(s)
                </td>
            </tr>

            <tr>
                <td>Pay Per Click Tracking Code Flag:</td>
                <td><input type="text" name="pay_per_click_flag" value="' . h($pay_per_click_flag) . '" size="40" /></td>
            </tr>
            <tr>
                <td>Website Analytics URL:</td>
                <td><input type="text" name="stats_url" value="' . h($stats_url) . '" size="40" /></td>
            </tr>
            <tr>
                <td><label for="google_analytics">Enable Google Analytics:</label></td>
                <td><input type="checkbox" id="google_analytics" name="google_analytics" value="1"' . $google_analytics_checked . ' class="checkbox" onclick="show_or_hide_google_analytics()" /></td>
            </tr>
            <tr id="google_analytics_web_property_id_row" style="' . $google_analytics_web_property_id_row_style . '">
                <td style="padding-left: 2em">Web Property ID:</td>
                <td><input type="text" name="google_analytics_web_property_id" value="' . $google_analytics_web_property_id . '" size="20" maxlength="50" /></td>
            </tr>
        </table>
        <h2>Search Engine Optimization</h2>
        <table>
            <tr>
                <td style="vertical-align: top">Additional sitemap.xml Content:</td>
                <td><textarea name="additional_sitemap_content" rows="5" cols="70">' . h($additional_sitemap_content) . '</textarea></td>
            </tr>
            <tr>
                <td style="vertical-align: top">Additional robots.txt Content:</td>
                <td><textarea name="additional_robots_content" rows="5" cols="70">' . h($additional_robots_content) . '</textarea></td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="submit_save" value="Save" class="submit-primary">&nbsp;&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" OnClick="javascript:history.go(-1);" class="submit-secondary">
        </div>
    </form>
</div>' .
output_footer();

    print $output;
    
    $liveform->remove_form('settings');

} else {
    validate_token_field();
    
    $hostname = $_POST['hostname'];

    // Remove http:// or https:// from hostname.
    $hostname = preg_replace('/http:\/\//i', '', $hostname);
    $hostname = preg_replace('/https:\/\//i', '', $hostname);
    
    // if the user selected to reset encryption key, then do that
    if ($_POST['ecommerce_reset_encryption_key'] == 1) {
        // if MCrypt is disabled, then output error
        if ((extension_loaded('mcrypt') == FALSE) || (in_array('rijndael-256', mcrypt_list_algorithms()) == FALSE)) {
            output_error('The encryption key could not be reset, because the MCrypt PHP extension is not enabled. <a href="javascript:history.go(-1)">Go back</a>.');
        }
        
        // get contents of config.php file in order to reset encryption key
        $config_content = file_get_contents(CONFIG_FILE_PATH);
        
        // open the config.php file so the encryption key can be reset
        $handle = @fopen(CONFIG_FILE_PATH, 'w');
        
        // if the config.php file could not be opened for writing, then output error
        if ($handle == FALSE) {
            output_error('The encryption key could not be reset, because the config.php file (' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/config.php) is not writable. Please configure the config.php file so it can be written to and then try again. For Unix, set the permissions for the file to 777. For Windows, give the anonymous web user rights to write to and delete the file. <a href="javascript:history.go(-1)">Go back</a>.');
        }
        
        $old_encryption_key = ENCRYPTION_KEY;
        $new_encryption_key = generate_encryption_key();
        
        // if there is not an old encryption key in the config.php file, then add new encryption key to config.php file
        if (defined('ENCRYPTION_KEY') == FALSE) {
            $config_content = str_replace('?>', "define('ENCRYPTION_KEY', '" . $new_encryption_key . "'); // DO NOT MODIFY OR SHARE\r\n?>", $config_content);
            
        // else there is an old encryption key in the config.php file, so update it
        } else {
            $config_content = str_replace($old_encryption_key, $new_encryption_key, $config_content);
        }
        
        // update the config.php file with the new content
        @fwrite($handle, $config_content);
        
        // close the config.php file
        @fclose($handle);
        
        // get all orders that have an unencrypted credit card number or encrypted credit card number
        $query = 
            "SELECT
                id,
                card_number
            FROM orders
            WHERE
                (card_number != '')
                AND (SUBSTRING(card_number, 1, 1) != '*')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        $orders = array();
        
        while ($row = mysqli_fetch_assoc($result)) {
            $orders[] = $row;
        }
        
        // loop through all orders in order to re-encrypt or encrypt credit card numbers
        foreach ($orders as $order) {
            // if the credit card number is already encrypted, then decrypt it with old key and encrypt it with new key
            if (mb_strlen($order['card_number']) > 16) {
                $order['card_number'] = decrypt_credit_card_number($order['card_number'], $old_encryption_key);
                
                // if the decryption was successful, then encrypt it with new key and store it
                if (is_numeric($order['card_number']) == TRUE) {
                    $query = "UPDATE orders SET card_number = '" . encrypt_credit_card_number($order['card_number'], $new_encryption_key) . "' WHERE id = '" . $order['id'] . "'";
                    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                }
                
            // else the credit card number is not already encrypted, so encrypt it for the first time
            } else {
                $query = "UPDATE orders SET card_number = '" . encrypt_credit_card_number($order['card_number'], $new_encryption_key) . "' WHERE id = '" . $order['id'] . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            }
        }
    }
    
    // if secure mode was checked then use secure url scheme
    if ($_POST['secure_mode'] == 1) {
        $url_scheme = 'https://';
        
    // else secure mode was not checked, so use standard url scheme
    } else {
        $url_scheme = 'http://';
    }

    // Remove commas from gift card validity days.
    $gift_card_validity_days = str_replace(',', '', $_POST['ecommerce_gift_card_validity_days']);
    
    if ($_POST['ecommerce_payment_gateway'] == 'PayPal Payments Pro') {
        $payment_gateway_mode = $_POST['ecommerce_paypal_payments_pro_gateway_mode'];
    } else {
        $payment_gateway_mode = $_POST['ecommerce_payment_gateway_mode'];
    }
    
    $query =
        "UPDATE config
        SET
            url_scheme = '$url_scheme',
            hostname = '" . escape($hostname) . "',
            email_address = '" . escape($_POST['email_address']) . "',
            title = '" . escape($_POST['title']) . "',
            meta_description = '" . escape($_POST['meta_description']) . "',
            meta_keywords = '" . escape($_POST['meta_keywords']) . "',
            mobile = '" . escape($_POST['mobile']) . "',
            search_type = '" . escape($_POST['search_type']) . "',
            social_networking = '" . escape($_POST['social_networking']) . "',
            social_networking_type = '" . escape($_POST['social_networking_type']) . "',
            social_networking_facebook = '" . escape($_POST['social_networking_facebook']) . "',
            social_networking_twitter = '" . escape($_POST['social_networking_twitter']) . "',
            social_networking_addthis = '" . escape($_POST['social_networking_addthis']) . "',
            social_networking_plusone = '" . escape($_POST['social_networking_plusone']) . "',
            social_networking_linkedin = '" . escape($_POST['social_networking_linkedin']) . "',
            social_networking_code = '" . escape($_POST['social_networking_code']) . "',
            captcha = '" . escape($_POST['captcha']) . "',
            auto_dialogs = '" . escape($_POST['auto_dialogs']) . "',
            mass_deletion = '" . escape($_POST['mass_deletion']) . "',
            strong_password = '" . escape($_POST['strong_password']) . "',
            password_hint = '" . escape($_POST['password_hint']) . "',
            remember_me = '" . escape($_POST['remember_me']) . "',
            forgot_password_link = '" . escape($_POST['forgot_password_link']) . "',
            proxy_address = '" . escape($_POST['proxy_address']) . "',
            badge_label = '" . escape($_POST['badge_label']) . "',
            timezone = '" . escape($_POST['timezone']) . "',
            date_format = '" . escape($_POST['date_format']) . "',
            organization_name = '" . escape($_POST['organization_name']) . "',
            organization_address_1 = '" . escape($_POST['organization_address_1']) . "',
            organization_address_2 = '" . escape($_POST['organization_address_2']) . "',
            organization_city = '" . escape($_POST['organization_city']) . "',
            organization_state = '" . escape($_POST['organization_state']) . "',
            organization_zip_code = '" . escape($_POST['organization_zip_code']) . "',
            organization_country = '" . escape($_POST['organization_country']) . "',
            opt_in_label = '" . escape($_POST['opt_in_label']) . "',
            plain_text_email_campaign_footer = '" . escape(trim($_POST['plain_text_email_campaign_footer'])) . "',
            debug = '" . escape($_POST['debug']) . "',
            visitor_tracking = '" . escape($_POST['visitor_tracking']) . "',
            tracking_code_duration = '" . e($_POST['tracking_code_duration']) . "',
            pay_per_click_flag = '" . escape($_POST['pay_per_click_flag']) . "',
            stats_url = '" . escape($_POST['stats_url']) . "',
            google_analytics = '" . escape($_POST['google_analytics']) . "',
            google_analytics_web_property_id = '" . escape($_POST['google_analytics_web_property_id']) . "',
            page_editor_version = '" . escape($_POST['page_editor_version']) . "',
            page_editor_font = '" . escape($_POST['page_editor_font']) . "',
            page_editor_font_size = '" . escape($_POST['page_editor_font_size']) . "',
            page_editor_font_style = '" . escape($_POST['page_editor_font_style']) . "',
            page_editor_font_color = '" . escape($_POST['page_editor_font_color']) . "',
            page_editor_background_color = '" . escape($_POST['page_editor_background_color']) . "',
            registration_contact_group_id = '" . escape($_POST['registration_contact_group_id']) . "',
            registration_email_address = '" . escape($_POST['registration_email_address']) . "',
            member_id_label = '" . escape($_POST['member_id_label']) . "',
            membership_contact_group_id = '" . escape($_POST['membership_contact_group_id']) . "',
            membership_email_address = '" . escape($_POST['membership_email_address']) . "',
            membership_expiration_warning_email = '" . escape($_POST['membership_expiration_warning_email']) . "',
            membership_expiration_warning_email_subject = '" . escape($_POST['membership_expiration_warning_email_subject']) . "',
            membership_expiration_warning_email_page_id = '" . escape($_POST['membership_expiration_warning_email_page_id']) . "',
            membership_expiration_warning_email_days_before_expiration = '" . escape($_POST['membership_expiration_warning_email_days_before_expiration']) . "',
            ecommerce = '" . escape($_POST['ecommerce']) . "',
            ecommerce_multicurrency = '" . escape($_POST['ecommerce_multicurrency']) . "',
            ecommerce_tax = '" . escape($_POST['ecommerce_tax']) . "',
            ecommerce_tax_exempt = '" . escape($_POST['ecommerce_tax_exempt']) . "',
            ecommerce_tax_exempt_label = '" . escape($_POST['ecommerce_tax_exempt_label']) . "',
            ecommerce_shipping = '" . escape($_POST['ecommerce_shipping']) . "',
            ecommerce_recipient_mode = '" . escape($_POST['ecommerce_recipient_mode']) . "',
            usps_user_id = '" . e(trim($_POST['usps_user_id'])) . "',
            ecommerce_address_verification = '" . escape($_POST['ecommerce_address_verification']) . "',
            ecommerce_address_verification_enforcement_type = '" . escape($_POST['ecommerce_address_verification_enforcement_type']) . "',
            ups_key = '" . e(trim($_POST['ups_key'])) . "',
            ups_user_id = '" . e(trim($_POST['ups_user_id'])) . "',
            ups_password = '" . e(trim($_POST['ups_password'])) . "',
            ups_account = '" . e(trim($_POST['ups_account'])) . "',
            ecommerce_product_restriction_message = '" . escape($_POST['ecommerce_product_restriction_message']) . "',
            ecommerce_no_shipping_methods_message = '" . escape($_POST['ecommerce_no_shipping_methods_message']) . "',
            ecommerce_end_of_day_time = '" . escape($_POST['ecommerce_end_of_day_time']) . "',
            ecommerce_email_address = '" . escape($_POST['ecommerce_email_address']) . "',
            ecommerce_gift_card = '" . escape($_POST['ecommerce_gift_card']) . "',
            ecommerce_gift_card_validity_days = '" . escape($gift_card_validity_days) . "',
            ecommerce_givex = '" . escape($_POST['ecommerce_givex']) . "',
            ecommerce_givex_primary_hostname = '" . escape($_POST['ecommerce_givex_primary_hostname']) . "',
            ecommerce_givex_secondary_hostname = '" . escape($_POST['ecommerce_givex_secondary_hostname']) . "',
            ecommerce_givex_user_id = '" . escape(trim($_POST['ecommerce_givex_user_id'])) . "',
            ecommerce_givex_password = '" . escape(trim($_POST['ecommerce_givex_password'])) . "',
            ecommerce_credit_debit_card = '" . escape($_POST['ecommerce_credit_debit_card']) . "',
            ecommerce_american_express = '" . escape($_POST['ecommerce_american_express']) . "',
            ecommerce_diners_club = '" . escape($_POST['ecommerce_diners_club']) . "',
            ecommerce_discover_card = '" . escape($_POST['ecommerce_discover_card']) . "',
            ecommerce_mastercard = '" . escape($_POST['ecommerce_mastercard']) . "',
            ecommerce_visa = '" . escape($_POST['ecommerce_visa']) . "',
            ecommerce_payment_gateway = '" . escape($_POST['ecommerce_payment_gateway']) . "',
            ecommerce_payment_gateway_transaction_type = '" . escape($_POST['ecommerce_payment_gateway_transaction_type']) . "',
            ecommerce_payment_gateway_mode = '" . escape($payment_gateway_mode) . "',
            ecommerce_authorizenet_api_login_id = '" . escape(trim($_POST['ecommerce_authorizenet_api_login_id'])) . "',
            ecommerce_authorizenet_transaction_key = '" . escape(trim($_POST['ecommerce_authorizenet_transaction_key'])) . "',
            ecommerce_clearcommerce_client_id = '" . escape(trim($_POST['ecommerce_clearcommerce_client_id'])) . "',
            ecommerce_clearcommerce_user_id = '" . escape(trim($_POST['ecommerce_clearcommerce_user_id'])) . "',
            ecommerce_clearcommerce_password = '" . escape(trim($_POST['ecommerce_clearcommerce_password'])) . "',
            ecommerce_first_data_global_gateway_store_number = '" . escape(trim($_POST['ecommerce_first_data_global_gateway_store_number'])) . "',
            ecommerce_first_data_global_gateway_pem_file_name = '" . escape(trim($_POST['ecommerce_first_data_global_gateway_pem_file_name'])) . "',
            ecommerce_paypal_payflow_pro_partner = '" . escape(trim($_POST['ecommerce_paypal_payflow_pro_partner'])) . "',
            ecommerce_paypal_payflow_pro_merchant_login = '" . escape(trim($_POST['ecommerce_paypal_payflow_pro_merchant_login'])) . "',
            ecommerce_paypal_payflow_pro_user = '" . escape(trim($_POST['ecommerce_paypal_payflow_pro_user'])) . "',
            ecommerce_paypal_payflow_pro_password = '" . escape(trim($_POST['ecommerce_paypal_payflow_pro_password'])) . "',
            ecommerce_paypal_payments_pro_api_username = '" . escape(trim($_POST['ecommerce_paypal_payments_pro_api_username'])) . "',
            ecommerce_paypal_payments_pro_api_password = '" . escape(trim($_POST['ecommerce_paypal_payments_pro_api_password'])) ."',
            ecommerce_paypal_payments_pro_api_signature = '" . escape(trim($_POST['ecommerce_paypal_payments_pro_api_signature'])) ."',
            ecommerce_sage_merchant_id = '" . escape(trim($_POST['ecommerce_sage_merchant_id'])) . "',
            ecommerce_sage_merchant_key = '" . escape(trim($_POST['ecommerce_sage_merchant_key'])) . "',
            ecommerce_stripe_api_key = '" . escape(trim($_POST['ecommerce_stripe_api_key'])) . "',
            ecommerce_surcharge_percentage = '" . escape($_POST['ecommerce_surcharge_percentage']) . "',
            ecommerce_paypal_express_checkout = '" . escape($_POST['ecommerce_paypal_express_checkout']) . "',
            ecommerce_paypal_express_checkout_transaction_type = '" . escape($_POST['ecommerce_paypal_express_checkout_transaction_type']) . "',
            ecommerce_paypal_express_checkout_mode = '" . escape($_POST['ecommerce_paypal_express_checkout_mode']) . "',
            ecommerce_paypal_express_checkout_api_username = '" . escape(trim($_POST['ecommerce_paypal_express_checkout_api_username'])) . "',
            ecommerce_paypal_express_checkout_api_password = '" . escape(trim($_POST['ecommerce_paypal_express_checkout_api_password'])) . "',
            ecommerce_paypal_express_checkout_api_signature = '" . escape(trim($_POST['ecommerce_paypal_express_checkout_api_signature'])) . "',
            ecommerce_offline_payment = '" . escape($_POST['ecommerce_offline_payment']) . "',
            ecommerce_offline_payment_only_specific_orders = '" . escape($_POST['ecommerce_offline_payment_only_specific_orders']) . "',
            ecommerce_private_folder_id = '" . e($_POST['ecommerce_private_folder_id']) . "',
            ecommerce_retrieve_order_next_page_id = '" . escape($_POST['ecommerce_retrieve_order_next_page_id']) . "',
            ecommerce_reward_program = '" . escape($_POST['ecommerce_reward_program']) . "',
            ecommerce_reward_program_points = '" . escape($_POST['ecommerce_reward_program_points']) . "',
            ecommerce_reward_program_membership = '" . escape($_POST['ecommerce_reward_program_membership']) . "',
            ecommerce_reward_program_membership_days = '" . escape($_POST['ecommerce_reward_program_membership_days']) . "',
            ecommerce_reward_program_email = '" . escape($_POST['ecommerce_reward_program_email']) . "',
            ecommerce_reward_program_email_bcc_email_address = '" . escape($_POST['ecommerce_reward_program_email_bcc_email_address']) . "',
            ecommerce_reward_program_email_subject = '" . escape($_POST['ecommerce_reward_program_email_subject']) . "',
            ecommerce_reward_program_email_page_id = '" . escape($_POST['ecommerce_reward_program_email_page_id']) . "',
            ecommerce_custom_product_field_1_label = '" . escape($_POST['ecommerce_custom_product_field_1_label']) . "',
            ecommerce_custom_product_field_2_label = '" . escape($_POST['ecommerce_custom_product_field_2_label']) . "',
            ecommerce_custom_product_field_3_label = '" . escape($_POST['ecommerce_custom_product_field_3_label']) . "',
            ecommerce_custom_product_field_4_label = '" . escape($_POST['ecommerce_custom_product_field_4_label']) . "',
            forms = '" . escape($_POST['forms']) . "',
            calendars = '" . escape($_POST['calendars']) . "',
            ads = '" . escape($_POST['ads']) . "',
            affiliate_program = '" . escape($_POST['affiliate_program']) . "',
            affiliate_default_commission_rate = '" . escape($_POST['affiliate_default_commission_rate']) . "',
            affiliate_automatic_approval = '" . escape($_POST['affiliate_automatic_approval']) . "',
            affiliate_contact_group_id = '" . escape($_POST['affiliate_contact_group_id']) . "',
            affiliate_email_address = '" . escape($_POST['affiliate_email_address']) . "',
            affiliate_group_offer_id = '" . escape($_POST['affiliate_group_offer_id']) . "',
            additional_sitemap_content = '" . escape(trim($_POST['additional_sitemap_content'])) . "',
            additional_robots_content = '" . escape(trim($_POST['additional_robots_content'])) . "',
            last_modified_user_id = '" . USER_ID . "',
            last_modified_timestamp = UNIX_TIMESTAMP()";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // if there was a next order number field, update next order number
    if (isset($_POST['ecommerce_next_order_number']) == true) {
        // if next order number that was submitted is blank, set order number to 1
        if (!$_POST['ecommerce_next_order_number']) {
            $ecommerce_next_order_number = 1;
        } else {
            $ecommerce_next_order_number = $_POST['ecommerce_next_order_number'];
        }
        
        // lock table, so no one can read table
        $query = "LOCK TABLES next_order_number WRITE";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // delete existing record for next order number
        $query = "DELETE FROM next_order_number";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // create new record for next order number
        $query = "INSERT INTO next_order_number VALUES ('" . escape($ecommerce_next_order_number) . "')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // release lock on table
        $query = "UNLOCK TABLES";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    }
    
    // delete all banned IP addresses from the database, so we can add the ones that were just submitted
    $query = "TRUNCATE banned_ip_addresses";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // assume that no banned ip addresses are invalid until we determine otherwise
    $invalid_banned_ip_addresses = false;
    
    // load all banned IP addresses into an array
    $banned_ip_addresses = explode("\n", $_POST['banned_ip_addresses']);
    
    // loop through all banned IP addresses in order to validate
    foreach ($banned_ip_addresses as $key => $banned_ip_address) {
        // remove spaces from beginning and end of banned IP address
        $banned_ip_address = trim($banned_ip_address);
        
        $banned_ip_address_parts = explode('.', $banned_ip_address);
        
        // if there are not 4 parts
        // or the parts contain too many characters
        // or the parts do not contain valid characters
        // then remove banned IP adddress, because it is not valid
        if (
            (count($banned_ip_address_parts) != 4)
            || (mb_strlen($banned_ip_address_parts[0]) > 3)
            || (mb_strlen($banned_ip_address_parts[1]) > 3)
            || (mb_strlen($banned_ip_address_parts[2]) > 3)
            || (mb_strlen($banned_ip_address_parts[3]) > 3)
            || ((is_numeric($banned_ip_address_parts[0]) == false) && ($banned_ip_address_parts[0] != '*'))
            || ((is_numeric($banned_ip_address_parts[1]) == false) && ($banned_ip_address_parts[1] != '*'))
            || ((is_numeric($banned_ip_address_parts[2]) == false) && ($banned_ip_address_parts[2] != '*'))
            || ((is_numeric($banned_ip_address_parts[3]) == false) && ($banned_ip_address_parts[3] != '*'))
        ) {
            unset($banned_ip_addresses[$key]);
            
            // if there is any data in the banned ip address, then remember that there is an invalid banned IP address
            if ($banned_ip_address != '') {
                $invalid_banned_ip_addresses = true;
            }
            
        // else the IP address is valid, so update it
        } else {
            $banned_ip_addresses[$key] = $banned_ip_address;
        }
    }
    
    // remove duplicate banned IP addresses from array
    $banned_ip_addresses = array_unique($banned_ip_addresses);
    
    // loop through all banned IP addresses in order to add them to database
    foreach ($banned_ip_addresses as $banned_ip_address) {
        $query = "INSERT INTO banned_ip_addresses (ip_address) VALUES('" . escape($banned_ip_address) . "')";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    }
    
    log_activity('settings were modified', $_SESSION['sessionusername']);
    
    $liveform->add_notice('The Site Settings have been saved.');
    
    // if one or more banned IP addresses were invalid, then prepare notice to warn user
    if ($invalid_banned_ip_addresses == true) {
        $liveform->add_notice('One or more banned IP addresses were not added because they were invalid.');
    }
    
    // forward user back to settings screen
    // we are using $url_scheme because we want to make sure we use the scheme they just selected
    header('Location: ' . $url_scheme . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/settings.php');
}