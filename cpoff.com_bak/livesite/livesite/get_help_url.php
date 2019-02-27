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

function get_help_url() {

    // Get script name for current screen.
    $php_self_parts = explode('/', $_SERVER['PHP_SELF']);
    $script_name = $php_self_parts[count($php_self_parts) - 1];
    
    // Get screen by removing extension from script name.
    $screen = mb_substr($script_name, 0, mb_strrpos($script_name, '.'));

    $url = '';

    switch ($screen) {

        case 'welcome': $url = 'introduction#welcome'; break;
        case 'view_log': $url = 'introduction#site-log'; break;

        case 'settings': $url = 'site-settings'; break;

        case 'view_folders': $url = 'folders'; break;

        case 'add_folder':
        case 'edit_folder':
            $url = 'folders#create-edit-folder';
            break;

        case 'view_pages':

            switch($_GET['filter']) {

                case 'all_my_pages': $url = 'pages'; break;
                case 'all_my_archived_pages': $url = 'pages-cont#archived-pages'; break;
                case 'my_home_pages': $url = 'pages-cont#home-pages'; break;
                case 'my_searchable_pages': $url = 'pages-cont#searchable-pages'; break;
                case 'my_unsearchable_pages': $url = 'pages-cont#unsearchable-pages'; break;
                case 'my_sitemap_pages': $url = 'pages-cont#site-map-pages'; break;
                case 'my_rss_enabled_pages': $url = 'pages-cont#rss-enabled-pages'; break;
                case 'my_standard_pages': $url = 'pages-cont#standard-pages'; break;
                case 'my_photo_gallery_pages': $url = 'pages-cont#photo-gallery-pages'; break;
                case 'my_calendar_pages': $url = 'pages-cont#calendar-pages'; break;
                case 'my_custom_form_pages': $url = 'pages-cont#custom-form-pages'; break;
                case 'my_form_view_pages': $url = 'pages-cont#form-view-pages'; break;
                case 'my_affiliate_pages': $url = 'pages-cont#affiliate-pages'; break;
                case 'my_commerce_pages': $url = 'pages-cont#commerce-pages'; break;
                case 'my_account_pages': $url = 'pages-cont#account-pages'; break;
                case 'my_login_pages': $url = 'pages-cont#login-pages'; break;
                case 'my_miscellaneous_pages': $url = 'pages-cont#miscellaneous-pages'; break;
                case 'my_public_pages': $url = 'pages-cont#public-access-pages'; break;
                case 'my_guest_pages': $url = 'pages-cont#guest-access-pages'; break;
                case 'my_registration_pages': $url = 'pages-cont#registration-access-pages'; break;
                case 'my_membership_pages': $url = 'pages-cont#membership-access-pages'; break;
                case 'my_private_pages': $url = 'pages-cont#private-access-pages'; break;

                default: $url = 'pages'; break;

            }

            break;

        case 'toolbar': $url = 'pages#editing-page-content'; break;
        case 'edit_comment': $url = 'pages#edit-comment'; break;

        case 'add_page':
        case 'edit_page':
            $url = 'pages#edit-page-properties';
            break;

        case 'view_fields': $url = 'pages#edit-custom-form'; break;

        case 'add_field':
        case 'edit_field':
            $url = 'pages#edit-custom-form-field';
            break;

        case 'edit_form_list_view': $url = 'pages#edit-form-list-view'; break;
        case 'edit_form_item_view': $url = 'pages#edit-form-item-view'; break;
        case 'view_short_links': $url = 'pages-cont#short-links'; break;

        case 'add_short_link':
        case 'edit_short_link':
            $url = 'pages-cont#create-edit-short-link';
            break;

        case 'view_auto_dialogs': $url = 'pages-cont#auto-dialogs'; break;

        case 'add_auto_dialog':
        case 'edit_auto_dialog':
            $url = 'pages-cont#create-edit-auto-dialog';
            break;

        case 'view_files':

            switch($_GET['filter']) {

                case 'all_my_files': $url = 'files'; break;
                case 'all_my_archived_files': $url = 'files#archived-files'; break;
                case 'my_documents': $url = 'files#documents'; break;
                case 'my_photos': $url = 'files#photos'; break;
                case 'my_media': $url = 'files#media'; break;
                case 'my_attachments': $url = 'files#attachments'; break;
                case 'my_public_files': $url = 'files#public-access-files'; break;
                case 'my_guest_files': $url = 'files#guest-access-files'; break;
                case 'my_registration_files': $url = 'files#registration-access-files'; break;
                case 'my_member_files': $url = 'files#membership-access-files'; break;
                case 'my_private_files': $url = 'files#private-access-files'; break;

                default: $url = 'files'; break;
                
            }

            break;

        case 'add_file': $url = 'files#upload-files'; break;
        case 'edit_file': $url = 'files#edit-file'; break;

        case 'view_calendars': $url = 'calendars'; break;
        case 'calendars': $url = 'calendars#create-edit-calendar'; break;

        case 'add_calendar':
        case 'edit_calendar':
            $url = 'calendars#create-edit-calendar-properties';
            break;

        case 'add_calendar_event':
        case 'edit_calendar_event':
            $url = 'calendars#create-edit-calendar-event';
            break;

        case 'view_calendar_event_locations': $url = 'calendars#event-locations'; break;

        case 'add_calendar_event_location':
        case 'edit_calendar_event_location':
            $url = 'calendars#create-edit-calendar-event-location';
            break;

        case 'view_submitted_forms': $url = 'forms'; break;
        case 'edit_submitted_form': $url = 'forms#edit-submitted-form'; break;
        case 'import_submitted_forms': $url = 'forms#import-submitted-forms'; break;

        case 'view_visitor_reports': $url = 'visitors'; break;
        case 'view_visitor_report': $url = 'visitors#create-edit-visitor-report'; break;
        case 'view_visitor': $url = 'visitors#visit-details'; break;

        case 'view_contacts':

            switch($_GET['filter']) {

                case 'all_my_contacts': $url = 'contacts'; break;
                case 'my_subscribers': $url = 'contacts#subscribers'; break;
                case 'my_affiliates': $url = 'contacts#affiliates'; break;
                case 'my_customers': $url = 'contacts#customers'; break;
                case 'my_members': $url = 'contacts#members'; break;
                case 'my_active_members': $url = 'contacts#active-members'; break;
                case 'my_expired_members': $url = 'contacts#expired-members'; break;
                case 'my_unregistered_members': $url = 'contacts#unregistered-members'; break;
                case 'my_contacts_by_user': $url = 'contacts#contacts-by-user'; break;
                case 'my_contacts_by_business_address': $url = 'contacts#contacts-by-business-address'; break;
                case 'my_contacts_by_home_address': $url = 'contacts#contacts-by-home-address'; break;
                case 'all_duplicate_contacts': $url = 'contacts#duplicate-contacts'; break;

                default: $url = 'contacts'; break;
                
            }

            break;

        case 'add_contact':
        case 'edit_contact':
            $url = 'contacts#create-edit-contact';
            break;

        case 'import_contacts': $url = 'contacts#import-contacts'; break;
        case 'view_contact_groups': $url = 'contacts#contact-groups'; break;
        case 'view_contact_groups': $url = 'contacts#contact-groups'; break;

        case 'add_contact_group':
        case 'edit_contact_group':
            $url = 'contacts#create-edit-contact-group';
            break;

        case 'view_users':

            switch($_GET['filter']) {

                case 'all_my_users': $url = 'users'; break;
                case 'my_registered_users': $url = 'users#registered-users'; break;
                case 'my_private_users': $url = 'users#private-users'; break;
                case 'my_member_users': $url = 'users#member-users'; break;
                case 'my_content_managers': $url = 'users#content-managers'; break;
                case 'my_calendar_managers': $url = 'users#calendar-managers'; break;
                case 'my_submitted_forms_managers': $url = 'users#submitted-forms-managers'; break;
                case 'my_visitor_report_managers': $url = 'users#visitor-report-managers'; break;
                case 'my_contact_managers': $url = 'users#contact-managers'; break;
                case 'my_campaign_managers': $url = 'users#campaign-managers'; break;
                case 'my_commerce_managers': $url = 'users#commerce-managers'; break;
                case 'all_site_designers': $url = 'users#site-designers'; break;
                case 'all_site_administrators': $url = 'users#site-administrators'; break;
                case 'all_site_managers': $url = 'users#site-managers'; break;
                
                default: $url = 'users'; break;
                
            }

            break;

        case 'add_user':
        case 'edit_user':
            $url = 'users#create-edit-user';
            break;

        case 'import_users': $url = 'users#import-users'; break;

        case 'view_email_campaigns': $url = 'campaigns'; break;

        case 'add_email_campaign':
        case 'edit_email_campaign':
            $url = 'campaigns#create-edit-campaign';
            break;

        case 'view_email_campaign_history': $url = 'campaigns#campaign-history'; break;
        case 'view_email_campaign_profiles': $url = 'campaigns#campaign-profiles'; break;

        case 'add_email_campaign_profile':
        case 'edit_email_campaign_profile':
            $url = 'campaigns#create-edit-campaign-profile';
            break;

        case 'import_email_campaign_profiles': $url = 'campaigns#import-campaign-profiles'; break;

        case 'view_orders': $url = 'commerce'; break;
        case 'view_order': $url = 'commerce#view-order'; break;
        case 'view_order_reports': $url = 'commerce#order-reports'; break;
        case 'view_order_report': $url = 'commerce#create-edit-order-report'; break;
        case 'view_commissions': $url = 'commerce#commissions'; break;
        case 'edit_commission': $url = 'commerce#edit-commission'; break;
        case 'view_recurring_commission_profiles': $url = 'commerce#recurring-commission-profiles'; break;
        case 'edit_recurring_commission_profile': $url = 'commerce#edit-recurring-commission-profile'; break;
        case 'view_products': $url = 'commerce#products'; break;

        case 'add_product':
        case 'edit_product':
            $url = 'commerce#create-edit-product';
            break;

        case 'import_products': $url = 'commerce#import-products'; break;
        case 'edit_featured_and_new_items': $url = 'commerce#edit-featured-and-new-items'; break;
        case 'edit_featured_and_new_items': $url = 'commerce#edit-featured-and-new-items'; break;
        case 'view_product_groups': $url = 'commerce#product-groups'; break;

        case 'add_product_group':
        case 'edit_product_group':
            $url = 'commerce#create-edit-product-group';
            break;

        case 'duplicate_product_group': $url = 'commerce#duplicate-product-group'; break;
        case 'view_product_attributes': $url = 'commerce#product-attributes'; break;

        case 'add_product_attribute':
        case 'edit_product_attribute':
            $url = 'commerce#create-edit-product-attribute';
            break;

        case 'view_gift_cards': $url = 'commerce#gift-cards'; break;
        case 'add_gift_card': $url = 'commerce#create-gift-card'; break;
        case 'edit_gift_card': $url = 'commerce#edit-gift-card'; break;
        case 'view_offers': $url = 'commerce-cont#offers'; break;

        case 'add_offer':
        case 'edit_offer':
            $url = 'commerce-cont#create-edit-offer';
            break;

        case 'view_offer_rules': $url = 'commerce-cont#offer-rules'; break;

        case 'add_offer_rule':
        case 'edit_offer_rule':
            $url = 'commerce-cont#create-edit-offer-rule';
            break;

        case 'view_offer_actions': $url = 'commerce-cont#offer-actions'; break;

        case 'add_offer_action':
        case 'edit_offer_action':
            $url = 'commerce-cont#create-edit-offer-action';
            break;

        case 'view_key_codes': $url = 'commerce-cont#key-codes'; break;

        case 'add_key_code':
        case 'edit_key_code':
            $url = 'commerce-cont#create-edit-key-code';
            break;

        case 'import_key_codes': $url = 'commerce-cont#import-key-codes'; break;

        case 'view_zones': $url = 'commerce-cont#zones'; break;

        case 'add_zone':
        case 'edit_zone':
            $url = 'commerce-cont#create-edit-zone';
            break;

        case 'view_shipping_methods': $url = 'commerce-cont#shipping-methods'; break;

        case 'add_shipping_method':
        case 'edit_shipping_method':
            $url = 'commerce-cont#create-edit-shipping-method';
            break;

        case 'view_arrival_dates': $url = 'commerce-cont#arrival-dates'; break;

        case 'add_arrival_date':
        case 'edit_arrival_date':
            $url = 'commerce-cont#create-edit-arrival-date';
            break;

        case 'view_verified_shipping_addresses': $url = 'commerce-cont#verified-shipping-addresses'; break;

        case 'add_verified_shipping_address':
        case 'edit_verified_shipping_address':
            $url = 'commerce-cont#create-edit-verified-shipping-address';
            break;

        case 'view_containers': $url = 'commerce-cont#containers'; break;

        case 'add_container':
        case 'edit_container':
            $url = 'commerce-cont#create-edit-container';
            break;

        case 'view_shipping_report': $url = 'commerce-cont#shipping-report'; break;
        case 'view_ship_date_adjustments': $url = 'commerce-cont#ship-date-adjustments'; break;

        case 'add_ship_date_adjustment':
        case 'edit_ship_date_adjustment':
            $url = 'commerce-cont#create-edit-ship-date-adjustment';
            break;

        case 'view_tax_zones': $url = 'commerce-cont#tax-zones'; break;

        case 'add_tax_zone':
        case 'edit_tax_zone':
            $url = 'commerce-cont#create-edit-tax-zone';
            break;

        case 'view_referral_sources': $url = 'commerce-cont#referral-sources'; break;

        case 'add_referral_source':
        case 'edit_referral_source':
            $url = 'commerce-cont#create-edit-referral-source';
            break;

        case 'view_currencies': $url = 'commerce-cont#currencies'; break;

        case 'add_currency':
        case 'edit_currency':
            $url = 'commerce-cont#create-edit-currency';
            break;

        case 'view_countries': $url = 'commerce-cont#countries'; break;

        case 'add_country':
        case 'edit_country':
            $url = 'commerce-cont#create-edit-country';
            break;

        case 'view_states': $url = 'commerce-cont#states'; break;

        case 'add_state':
        case 'edit_state':
            $url = 'commerce-cont#create-edit-state';
            break;

        case 'view_ads': $url = 'ads'; break;

        case 'add_ad':
        case 'edit_ad':
            $url = 'ads#create-edit-ad';
            break;

        case 'view_styles': $url = 'design'; break;
        case 'add_style': $url = 'design#create-style'; break;

        case 'add_system_style':
        case 'edit_system_style':
            $url = 'design#create-edit-system-style';
            break;

        case 'view_system_style_source': $url = 'design#view-system-style-source'; break;

        case 'add_custom_style':
        case 'edit_custom_style':
            $url = 'design#create-edit-custom-style';
            break;

        case 'view_menus': $url = 'design#menus'; break;

        case 'add_menu':
        case 'edit_menu':
            $url = 'design#create-edit-menu';
            break;

        case 'view_menu_items': $url = 'design#menu-items'; break;

        case 'add_menu_item':
        case 'edit_menu_item':
            $url = 'design#create-edit-menu-item';
            break;

        case 'view_regions':

            switch($_GET['filter']) {

                case 'all_common_regions': $url = 'design#common-regions'; break;
                case 'all_designer_regions': $url = 'design#designer-regions'; break;
                case 'all_ad_regions': $url = 'design#ad-regions'; break;
                case 'all_dynamic_regions': $url = 'design#dynamic-regions'; break;
                case 'all_login_regions': $url = 'design#login-regions'; break;

                default: $url = 'design#common-regions'; break;
            }

            break;

        case 'add_common_region':
        case 'edit_common_region':
            $url = 'design#create-edit-common-region';
            break;

        case 'add_designer_region':
        case 'edit_designer_region':
            $url = 'design#create-edit-designer-region';
            break;

        case 'add_ad_region':
        case 'edit_ad_region':
            $url = 'design#create-edit-ad-region';
            break;

        case 'add_dynamic_region':
        case 'edit_dynamic_region':
            $url = 'design#create-edit-dynamic-region';
            break;

        case 'add_login_region':
        case 'edit_login_region':
            $url = 'design#create-edit-login-region';
            break;

        case 'view_themes': $url = 'design#themes'; break;
        case 'add_theme_file': $url = 'design#add-theme-file'; break;
        case 'edit_theme_file': $url = 'design#edit-theme-file'; break;
        case 'edit_theme_css': $url = 'design#edit-theme-css'; break;
        case 'theme_designer': $url = 'design#theme-designer'; break;
        case 'view_design_files': $url = 'design#design-files'; break;
        case 'add_design_file': $url = 'design#upload-design-files'; break;
        case 'edit_design_file': $url = 'design#edit-design-file'; break;
        case 'edit_javascript': $url = 'design#edit-javascript'; break;
        case 'import_design': $url = 'design#import-my-site'; break;
        case 'import_zip': $url = 'design#import-zip-file'; break;

    }

    if ($url) {
        return 'https://livesite.com/docs/' . $url;
    } else {
        return 'https://livesite.com/docs';
    }

}