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

// if the user has a user role and the user does not have edit access to any folders and the user does not have access to control panels, then deny access to software welcome screen
if (
    ($user['role'] == 3)
    && (no_acl_check($user['id']) == false)
    && ($user['manage_calendars'] == false)
    && ($user['manage_forms'] == false)
    && ($user['manage_visitors'] == false)
    && ($user['manage_contacts'] == false)
    && ($user['manage_emails'] == false)
    && ($user['manage_ecommerce'] == false)
    && ($user['manage_ecommerce_reports'] == false)
    && (count(get_items_user_can_edit('ad_regions', $user['id'])) == 0)
) {
    log_activity("access denied to welcome screen", $_SESSION['sessionusername']);
    output_error('Access denied. <a href="javascript:history.go(-1)">Go back</a>.');
}

switch($user[role])
{
    case '0':
    $role = 'administrator';
    break;
    case '1':
    $role = 'designer';
    break;
    case '2':
    $role = 'manager';
    break;
    case '3':
    $role = 'user';
    break;
}

// if the user is a Manager or above then output site log link
if ($user['role'] < 3) {
    $output_button_bar =
        '<div id="button_bar">
            <a href="view_log.php">Site Log</a>
        </div>';
}

// if the user has access to the visitors tab, then output visitor information
if (($user['role'] < 3) || ($user['manage_visitors'] == TRUE)) {
    // get the date today in order to get the timestamp for the beginning of today
    $date_today = date('Y-m-d');
    
    // get the timestamp for the beginning of today
    $timestamp_today = strtotime($date_today);
    
    // get the number of visitors for today
    $query = "SELECT COUNT(*) as number_of_visitors_for_today FROM visitors WHERE start_timestamp >= '$timestamp_today'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_today = $row['number_of_visitors_for_today'];
    
    // get the timestamp for the beginning of yesterday
    $timestamp_yesterday = $timestamp_today - 86400;
    
    // get the timestamp for current time yesterday
    $timestamp_current_time_yesterday = time() - 86400;
    
    // get the number of visitors for yesterday through the current time
    $query = "SELECT COUNT(*) as number_of_visitors_for_yesterday_comparison FROM visitors WHERE (start_timestamp >= '$timestamp_yesterday') AND (start_timestamp <= '$timestamp_current_time_yesterday')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_yesterday_comparison = $row['number_of_visitors_for_yesterday_comparison'];
    
    // if the number of visitors for today is greater than or equal to the number of visitors for yesterday (up to the current time), then show up trend
    if ($number_of_visitors_for_today >= $number_of_visitors_for_yesterday_comparison) {
        $output_trend_today = '<span class="trend_up"><img title="Today is trending up over the same time yesterday." src="images/up_arrow.png" alt="(Up)" width="13" height="11" /></span>';
        
    // else the number of visitors for today is less than the number of visitors for yesterday (up to the current time), so show down trend
    } else {
        $output_trend_today = '<span class="trend_down"><img title="Today is trending down over the same time yesterday." src="images/down_arrow.png" alt="(Down)" width="13" height="11" /></span>';
    }
    
    // get the number of visitors for yesterday
    $query = "SELECT COUNT(*) as number_of_visitors_for_yesterday FROM visitors WHERE (start_timestamp >= '$timestamp_yesterday') AND (start_timestamp < '$timestamp_today')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_yesterday = $row['number_of_visitors_for_yesterday'];
    
    $timestamp_day_before_yesterday = $timestamp_yesterday - 86400;
    
    // get the number of visitors for the day before yesterday
    $query = "SELECT COUNT(*) as number_of_visitors_for_day_before_yesterday FROM visitors WHERE (start_timestamp >= '$timestamp_day_before_yesterday') AND (start_timestamp < '$timestamp_yesterday')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_day_before_yesterday = $row['number_of_visitors_for_day_before_yesterday'];
    
    // if the number of visitors for yesterday is greater than or equal to the number of visitors for the day before yesterday, then show up trend
    if ($number_of_visitors_for_yesterday >= $number_of_visitors_for_day_before_yesterday) {
        $output_trend_yesterday = '<span class="trend_up"><img title="There were more visitors yesterday than the day before yesterday." src="images/up_arrow.png" alt="(Up)" width="13" height="11" /></span>';
        
    // else the number of visitors for yesterday is less than the number of visitors for the day before yesterday, so show down trend
    } else {
        $output_trend_yesterday = '<span class="trend_down"><img title="There were fewer visitors yesterday than the day before yesterday." src="images/down_arrow.png" alt="(Down)" width="13" height="11" /></span>';
    }
    
    $timestamp_7_days_ago = $timestamp_today - 604800;
    
    // get the number of visitors for the past 7 days
    $query = "SELECT COUNT(*) as number_of_visitors_for_past_7_days FROM visitors WHERE (start_timestamp >= '$timestamp_7_days_ago') AND (start_timestamp < '$timestamp_today')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_past_7_days = $row['number_of_visitors_for_past_7_days'];
    
    $timestamp_14_days_ago = $timestamp_7_days_ago - 604800;
    
    // get the number of visitors for the week before the past week
    $query = "SELECT COUNT(*) as number_of_visitors_for_week_before_last FROM visitors WHERE (start_timestamp >= '$timestamp_14_days_ago') AND (start_timestamp < '$timestamp_7_days_ago')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_week_before_last = $row['number_of_visitors_for_week_before_last'];
    
    // if the number of visitors for the past week is greater than or equal to the number of visitors for the week before, then show up trend
    if ($number_of_visitors_for_past_7_days >= $number_of_visitors_for_week_before_last) {
        $output_trend_past_7_days = '<span class="trend_up"><img title="There were more visitors over the past 7 days than the previous 7 days." src="images/up_arrow.png" alt="(Up)" width="13" height="11" /></span>';
        
    // else the number of visitors for the past week is less than the number of visitors for the week before, so show down trend
    } else {
        $output_trend_past_7_days = '<span class="trend_down"><img title="There were less visitors over the past 7 days than the previous 7 days." src="images/down_arrow.png" alt="(Down)" width="13" height="11" /></span>';
    }
    
    $timestamp_30_days_ago = $timestamp_today - 2592000;
    
    // get the number of visitors for the past 30 days
    $query = "SELECT COUNT(*) as number_of_visitors_for_past_30_days FROM visitors WHERE (start_timestamp >= '$timestamp_30_days_ago') AND (start_timestamp < '$timestamp_today')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_past_30_days = $row['number_of_visitors_for_past_30_days'];
    
    $timestamp_60_days_ago = $timestamp_30_days_ago - 2592000;
    
    // get the number of visitors for the month before the past month
    $query = "SELECT COUNT(*) as number_of_visitors_for_month_before_last FROM visitors WHERE (start_timestamp >= '$timestamp_60_days_ago') AND (start_timestamp < '$timestamp_30_days_ago')";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);
    $number_of_visitors_for_month_before_last = $row['number_of_visitors_for_month_before_last'];
    
    // if the number of visitors for the past month is greater than or equal to the number of visitors for the last month, then show up trend
    if ($number_of_visitors_for_past_30_days >= $number_of_visitors_for_month_before_last) {
        $output_trend_past_30_days = '<span class="trend_up"><img title="There were more visitors over the past 30 days than the previous 30 days." src="images/up_arrow.png" alt="(Up)" width="13" height="11" /></span>';
        
    // else the number of visitors for the past month is less than the number of visitors for the last month, so show down trend
    } else {
        $output_trend_past_30_days = '<span class="trend_down"><img title="There were fewer visitors over the past 30 days than the previous 30 days." src="images/down_arrow.png" alt="(Down)" width="13" height="11" /></span>';
    }
    
    $output_visitor_summary =
        '<div id="visitor_summary">
            <span style="margin-right: 1em"><strong>Visitors</strong>:</span>
            Today: <span style="font-weight: bold">' . number_format($number_of_visitors_for_today) . '</span> '  . $output_trend_today . '
            Yesterday: <span style="font-weight: bold">' . number_format($number_of_visitors_for_yesterday) . '</span> '  . $output_trend_yesterday . '
            Past 7 Days: <span style="font-weight: bold">' . number_format($number_of_visitors_for_past_7_days) . '</span> '  . $output_trend_past_7_days . '
            Past 30 Days: <span style="font-weight: bold">' . number_format($number_of_visitors_for_past_30_days) . '</span> '  . $output_trend_past_30_days . '
        </div>';
}

// initialize variable for storing the maximum number of items that should appear in the recent update area
$maximum_number_of_items = 35;

// initialize variable for storing the maximum number of items for special items (e.g. files, designer files, and products)
$special_maximum_number_of_items = 10;

// initialize array for storing items that might appear in the recent updates area
$recent_update_items = array();

// initialize array that will be used for sorting the items for the recent updates area
$recent_update_item_timestamps = array();

$folders_that_user_has_access_to = array();

// if user is a basic user, then get folders that user has access to
if ($user['role'] == 3) {
    $folders_that_user_has_access_to = get_folders_that_user_has_access_to($user['id']);
}

$pages = array();

// get all pages sorted by last modified descending
$query =
    "SELECT
        page.page_name as name,
        page.page_timestamp as timestamp,
        user.user_username as username,
        page.page_folder as folder_id,
        page.page_type
    FROM page
    LEFT JOIN user ON page.page_user = user.user_id
    ORDER BY page.page_timestamp DESC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

// loop through the result in order to prepare array of items
while ($row = mysqli_fetch_assoc($result)) {
    $pages[] = $row;
}

// initialize variable to keep track of how many items have been added
$count = 0;

// loop through the items in order to determine which the user has access to
foreach ($pages as $page) {
    // if user has access to item then add it to arrays
    if (check_folder_access_in_array($page['folder_id'], $folders_that_user_has_access_to) == TRUE) {
        $page['type'] = 'page';
        $recent_update_items[] = $page;
        $recent_update_item_timestamps[] = $page['timestamp'];
        
        $count++;
        
        // if the maximum number of items has been added, then we are done, so break out of the loop
        if ($count == $maximum_number_of_items) {
            break;
        }
    }
}

$short_links = array();

// Get all short links sorted by last modified descending
$query =
    "SELECT
        short_links.id,
        short_links.name,
        short_links.destination_type,
        short_links.created_user_id,
        short_links.last_modified_timestamp AS timestamp,
        user.user_username AS username,
        page.page_folder AS folder_id
    FROM short_links
    LEFT JOIN user ON short_links.last_modified_user_id = user.user_id
    LEFT JOIN page ON short_links.page_id = page.page_id
    ORDER BY short_links.last_modified_timestamp DESC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
$short_links = mysqli_fetch_items($result);

// initialize variable to keep track of how many items have been added
$count = 0;

// loop through the items in order to determine which the user has access to
foreach ($short_links as $short_link) {
    // if user has access to item then add it to arrays
    if (
        (USER_ROLE < 3)
        ||
        (
            (
                ($short_link['destination_type'] == 'page')
                || ($short_link['destination_type'] == 'product_group')
                || ($short_link['destination_type'] == 'product')
            )
            && (check_folder_access_in_array($short_link['folder_id'], $folders_that_user_has_access_to) == true)
        )
        ||
        (
            ($short_link['destination_type'] == 'url')
            && (USER_ID == $short_link['created_user_id'])
        )
    ) {
        $short_link['type'] = 'short_link';
        $recent_update_items[] = $short_link;
        $recent_update_item_timestamps[] = $short_link['timestamp'];
        
        $count++;
        
        // if the maximum number of items has been added, then we are done, so break out of the loop
        if ($count == $maximum_number_of_items) {
            break;
        }
    }
}

$files = array();

// get all files sorted by last modified descending
$query =
    "SELECT
        files.id,
        files.name,
        files.timestamp,
        user.user_username as username,
        files.folder as folder_id
    FROM files
    LEFT JOIN user ON files.user = user.user_id
    WHERE files.design = '0'
    ORDER BY files.timestamp DESC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

// loop through the result in order to prepare array of items
while ($row = mysqli_fetch_assoc($result)) {
    $files[] = $row;
}

// initialize variable to keep track of how many items have been added
$count = 0;

// loop through the items in order to determine which the user has access to
foreach ($files as $file) {
    // if user has access to item then add it to arrays
    if (check_folder_access_in_array($file['folder_id'], $folders_that_user_has_access_to) == TRUE) {
        $file['type'] = 'file';
        $recent_update_items[] = $file;
        $recent_update_item_timestamps[] = $file['timestamp'];
        
        $count++;
        
        // if the maximum number of items has been added, then we are done, so break out of the loop
        if ($count == $special_maximum_number_of_items) {
            break;
        }
    }
}

$folders = array();

// get all folders sorted by last modified descending
$query =
    "SELECT
        folder.folder_id as id,
        folder.folder_name as name,
        folder.folder_timestamp as timestamp,
        user.user_username as username
    FROM folder
    LEFT JOIN user ON folder.folder_user = user.user_id
    ORDER BY folder.folder_timestamp DESC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

// loop through the result in order to prepare array of items
while ($row = mysqli_fetch_assoc($result)) {
    $folders[] = $row;
}

// initialize variable to keep track of how many items have been added
$count = 0;

// loop through the items in order to determine which the user has access to
foreach ($folders as $folder) {
    // if user has access to item then add it to arrays
    if (check_folder_access_in_array($folder['id'], $folders_that_user_has_access_to) == TRUE) {
        $folder['type'] = 'folder';
        $recent_update_items[] = $folder;
        $recent_update_item_timestamps[] = $folder['timestamp'];
        
        $count++;
        
        // if the maximum number of items has been added, then we are done, so break out of the loop
        if ($count == $maximum_number_of_items) {
            break;
        }
    }
}

// if calendars is enabled and the user has access to manage calendars, then get calendars and events
if (
    (CALENDARS == TRUE)
    && (($user['role'] < 3) || ($user['manage_calendars'] == TRUE))
) {
    $calendars = array();
    
    // get all calendars sorted by last modified descending
    $query =
        "SELECT
            calendars.id,
            calendars.name,
            calendars.last_modified_timestamp as timestamp,
            user.user_username as username
        FROM calendars
        LEFT JOIN user ON calendars.last_modified_user_id = user.user_id
        ORDER BY calendars.last_modified_timestamp DESC";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $calendars[] = $row;
    }

    // initialize variable to keep track of how many items have been added
    $count = 0;

    // loop through the items in order to determine which the user has access to
    foreach ($calendars as $calendar) {
        // if user has access to item then add it to arrays
        if (validate_calendar_access($calendar['id']) == TRUE) {
            $calendar['type'] = 'calendar';
            $recent_update_items[] = $calendar;
            $recent_update_item_timestamps[] = $calendar['timestamp'];
            
            $count++;
            
            // if the maximum number of items has been added, then we are done, so break out of the loop
            if ($count == $maximum_number_of_items) {
                break;
            }
        }
    }
    
    $calendar_events = array();
    
    // get all calendar events sorted by last modified descending
    $query =
        "SELECT
            calendar_events.id,
            calendar_events.name,
            calendar_events.last_modified_timestamp as timestamp,
            user.user_username as username
        FROM calendar_events
        LEFT JOIN user ON calendar_events.last_modified_user_id = user.user_id
        ORDER BY calendar_events.last_modified_timestamp DESC";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $calendar_events[] = $row;
    }

    // initialize variable to keep track of how many items have been added
    $count = 0;

    // loop through the items in order to determine which the user has access to
    foreach ($calendar_events as $calendar_event) {
        // if user has access to item then add it to arrays
        if (validate_calendar_event_access($calendar_event['id']) == TRUE) {
            $calendar_event['type'] = 'calendar_event';
            $recent_update_items[] = $calendar_event;
            $recent_update_item_timestamps[] = $calendar_event['timestamp'];
            
            $count++;
            
            // if the maximum number of items has been added, then we are done, so break out of the loop
            if ($count == $maximum_number_of_items) {
                break;
            }
        }
    }
}

// if e-commerce is enabled and the user has access to manage e-commerce, then get e-commerce items
if (
    (ECOMMERCE == TRUE)
    && (($user['role'] < 3) || ($user['manage_ecommerce'] == TRUE))
) {
    $products = array();
    
    // get all products sorted by last modified descending
    $query =
        "SELECT
            products.id,
            products.short_description as name,
            products.timestamp,
            user.user_username as username
        FROM products
        LEFT JOIN user ON products.user = user.user_id
        ORDER BY products.timestamp DESC
        LIMIT $special_maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($products as $product) {
        $product['type'] = 'product';
        $recent_update_items[] = $product;
        $recent_update_item_timestamps[] = $product['timestamp'];
    }
    
    $product_groups = array();
    
    // get all product groups sorted by last modified descending
    $query =
        "SELECT
            product_groups.id,
            product_groups.name,
            product_groups.timestamp,
            user.user_username as username
        FROM product_groups
        LEFT JOIN user ON product_groups.user = user.user_id
        ORDER BY product_groups.timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $product_groups[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($product_groups as $product_group) {
        $product_group['type'] = 'product_group';
        $recent_update_items[] = $product_group;
        $recent_update_item_timestamps[] = $product_group['timestamp'];
    }
    
    $offers = array();
    
    // get all offers sorted by last modified descending
    $query =
        "SELECT
            offers.id,
            offers.code as name,
            offers.timestamp,
            user.user_username as username
        FROM offers
        LEFT JOIN user ON offers.user = user.user_id
        ORDER BY offers.timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $offers[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($offers as $offer) {
        $offer['type'] = 'offer';
        $recent_update_items[] = $offer;
        $recent_update_item_timestamps[] = $offer['timestamp'];
    }
}

// If ads are enabled, then get them.
if (ADS === true) {
    $ads = array();

    // get all ads sorted by last modified descending
    $query =
        "SELECT
            ads.id,
            ads.name,
            ads.last_modified_timestamp as timestamp,
            user.user_username as username,
            ads.ad_region_id
        FROM ads
        LEFT JOIN user ON ads.last_modified_user_id = user.user_id
        ORDER BY ads.last_modified_timestamp DESC";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $ads[] = $row;
    }

    // initialize variable to keep track of how many items have been added
    $count = 0;

    // loop through the items in order to determine which the user has access to
    foreach ($ads as $ad) {
        // if user has access to item then add it to arrays
        if (($user['role'] < 3) || (in_array($ad['ad_region_id'], get_items_user_can_edit('ad_regions', $user['id'])) == TRUE)) {
            $ad['type'] = 'ad';
            $recent_update_items[] = $ad;
            $recent_update_item_timestamps[] = $ad['timestamp'];
            
            $count++;
            
            // if the maximum number of items has been added, then we are done, so break out of the loop
            if ($count == $maximum_number_of_items) {
                break;
            }
        }
    }
}
    
$menus = array();

// get all menus sorted by last modified descending
$query =
    "SELECT
        menus.id,
        menus.name,
        menus.last_modified_timestamp as timestamp,
        user.user_username as username
    FROM menus
    LEFT JOIN user ON menus.last_modified_user_id = user.user_id
    ORDER BY menus.last_modified_timestamp DESC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

// loop through the result in order to prepare array of items
while ($row = mysqli_fetch_assoc($result)) {
    $menus[] = $row;
}

// initialize variable to keep track of how many items have been added
$count = 0;

// loop through the items in order to determine which the user has access to
foreach ($menus as $menu) {
    // if user has access to item then add it to arrays
    if (($user['role'] < 3) || (in_array($menu['id'], get_items_user_can_edit('menus', $user['id'])) == TRUE)) {
        $menu['type'] = 'menu';
        $recent_update_items[] = $menu;
        $recent_update_item_timestamps[] = $menu['timestamp'];
        
        $count++;
        
        // if the maximum number of items has been added, then we are done, so break out of the loop
        if ($count == $maximum_number_of_items) {
            break;
        }
    }
}

// if the user has access to the design tab, then get design items
if ($user['role'] < 2) {
    $styles = array();
    
    // get all styles sorted by last modified descending
    $query =
        "SELECT
            style.style_id as id,
            style.style_name as name,
            style.style_timestamp as timestamp,
            user.user_username as username,
            style.style_type
        FROM style
        LEFT JOIN user ON style.style_user = user.user_id
        ORDER BY style.style_timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $styles[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($styles as $style) {
        $style['type'] = 'style';
        $recent_update_items[] = $style;
        $recent_update_item_timestamps[] = $style['timestamp'];
    }
    
    $common_regions = array();
    
    // get all common regions sorted by last modified descending
    $query =
        "SELECT
            cregion.cregion_id as id,
            cregion.cregion_name as name,
            cregion.cregion_timestamp as timestamp,
            user.user_username as username
        FROM cregion
        LEFT JOIN user ON cregion.cregion_user = user.user_id
        WHERE cregion.cregion_designer_type = 'no'
        ORDER BY cregion.cregion_timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $common_regions[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($common_regions as $common_region) {
        $common_region['type'] = 'common_region';
        $recent_update_items[] = $common_region;
        $recent_update_item_timestamps[] = $common_region['timestamp'];
    }
    
    $designer_regions = array();
    
    // get all designer regions sorted by last modified descending
    $query =
        "SELECT
            cregion.cregion_id as id,
            cregion.cregion_name as name,
            cregion.cregion_timestamp as timestamp,
            user.user_username as username
        FROM cregion
        LEFT JOIN user ON cregion.cregion_user = user.user_id
        WHERE cregion.cregion_designer_type = 'yes'
        ORDER BY cregion.cregion_timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $designer_regions[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($designer_regions as $designer_region) {
        $designer_region['type'] = 'designer_region';
        $recent_update_items[] = $designer_region;
        $recent_update_item_timestamps[] = $designer_region['timestamp'];
    }

    // If ads are enabled, then get ad regions.
    if (ADS === true) {
        $ad_regions = array();
        
        // get all ad regions sorted by last modified descending
        $query =
            "SELECT
                ad_regions.id,
                ad_regions.name,
                ad_regions.last_modified_timestamp as timestamp,
                user.user_username as username
            FROM ad_regions
            LEFT JOIN user ON ad_regions.last_modified_user_id = user.user_id
            ORDER BY ad_regions.last_modified_timestamp DESC
            LIMIT $maximum_number_of_items";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // loop through the result in order to prepare array of items
        while ($row = mysqli_fetch_assoc($result)) {
            $ad_regions[] = $row;
        }

        // loop through the items in order to add them to arrays
        foreach ($ad_regions as $ad_region) {
            $ad_region['type'] = 'ad_region';
            $recent_update_items[] = $ad_region;
            $recent_update_item_timestamps[] = $ad_region['timestamp'];
        }
    }
    
    // if the user is an administrator and dynamic regions are enabled, then get dynamic regions
    if (($user['role'] == 0) && (defined('DYNAMIC_REGIONS') == TRUE) && (DYNAMIC_REGIONS == TRUE)) {
        $dynamic_regions = array();
        
        // get all dynamic regions sorted by last modified descending
        $query =
            "SELECT
                dregion.dregion_id as id,
                dregion.dregion_name as name,
                dregion.dregion_timestamp as timestamp,
                user.user_username as username
            FROM dregion
            LEFT JOIN user ON dregion.dregion_user = user.user_id
            ORDER BY dregion.dregion_timestamp DESC
            LIMIT $maximum_number_of_items";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        // loop through the result in order to prepare array of items
        while ($row = mysqli_fetch_assoc($result)) {
            $dynamic_regions[] = $row;
        }

        // loop through the items in order to add them to arrays
        foreach ($dynamic_regions as $dynamic_region) {
            $dynamic_region['type'] = 'dynamic_region';
            $recent_update_items[] = $dynamic_region;
            $recent_update_item_timestamps[] = $dynamic_region['timestamp'];
        }
    }
    
    $login_regions = array();
    
    // get all login regions sorted by last modified descending
    $query =
        "SELECT
            login_regions.id,
            login_regions.name,
            login_regions.last_modified_timestamp as timestamp,
            user.user_username as username
        FROM login_regions
        LEFT JOIN user ON login_regions.last_modified_user_id = user.user_id
        ORDER BY login_regions.last_modified_timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $login_regions[] = $row;
    }

    // loop through the items in order to add them to arrays
    foreach ($login_regions as $login_region) {
        $login_region['type'] = 'login_region';
        $recent_update_items[] = $login_region;
        $recent_update_item_timestamps[] = $login_region['timestamp'];
    }
    
    $themes = array();
    
    // get all themes sorted by last modified descending
    $query =
        "SELECT
            files.id,
            files.name,
            files.timestamp,
            user.user_username as username
        FROM files
        LEFT JOIN user ON files.user = user.user_id
        WHERE (files.type = 'css') AND (files.design = '1')
        ORDER BY files.timestamp DESC
        LIMIT $maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $themes[] = $row;
    }
    
    // loop through the items in order to add them to arrays
    foreach ($themes as $theme) {
        $theme['type'] = 'theme';
        $recent_update_items[] = $theme;
        $recent_update_item_timestamps[] = $theme['timestamp'];
    }
    
    $design_files = array();
    
    // get all design files sorted by last modified descending
    // even though themes are considered design files, we are going to exclude this from this query because we don't want them appear twice (as both a theme and a design file)
    $query =
        "SELECT
            files.id,
            files.name,
            files.timestamp,
            user.user_username as username,
            files.folder as folder_id
        FROM files
        LEFT JOIN user ON files.user = user.user_id
        WHERE (files.design = '1') AND (files.type != 'css')
        ORDER BY files.timestamp DESC
        LIMIT $special_maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $design_files[] = $row;
    }
    
    // loop through the items in order to add them to arrays
    foreach ($design_files as $design_file) {
        $design_file['type'] = 'design_file';
        $recent_update_items[] = $design_file;
        $recent_update_item_timestamps[] = $design_file['timestamp'];
    }
}   

// sort the recent update items by the timestamp descending
array_multisort($recent_update_item_timestamps, SORT_DESC, $recent_update_items);

// update array to only contain the maximum number of items
$recent_update_items = array_slice($recent_update_items, 0, $maximum_number_of_items);

$output_recent_update_rows = '';

// loop through the recent update items, in order to output rows
foreach ($recent_update_items as $recent_update_item) {
    $type_name = '';
    $output_link_url = '';
    
    // get type name
    switch ($recent_update_item['type']) {
        case 'page':
            $type_name = 'Page';
            
            $query_string_from = '';
            
            // if page type is a certain page type, then prepare from
            switch ($recent_update_item['page_type']) {
                case 'view order':
                case 'custom form':
                case 'custom form confirmation':
                case 'form item view':
                case 'calendar event view':
                case 'catalog detail':
                case 'shipping address and arrival':
                case 'shipping method':
                case 'logout':
                    $query_string_from = '?from=control_panel';
                    break;
            }

            $output_link_url = h(escape_javascript(PATH)) . h(escape_javascript(encode_url_path($recent_update_item['name']))) . $query_string_from;
            
            break;

        case 'short_link':
            $type_name = 'Short Link';
            $output_link_url = 'edit_short_link.php?id=' . $recent_update_item['id'];
            break;
            
        case 'file':
            $type_name = 'File';
            $output_link_url = 'edit_file.php?id=' . $recent_update_item['id'];
            break;
            
        case 'folder':
            $type_name = 'Folder';
            $output_link_url = 'edit_folder.php?id=' . $recent_update_item['id'];
            break;
            
        case 'calendar':
            $type_name = 'Calendar';
            $output_link_url = 'calendars.php?id=' . $recent_update_item['id'];
            break;
            
        case 'calendar_event':
            $type_name = 'Event';
            $output_link_url = 'edit_calendar_event.php?id=' . $recent_update_item['id'];
            break;
            
        case 'product':
            $type_name = 'Product';
            $output_link_url = 'edit_product.php?id=' . $recent_update_item['id'];
            break;
            
        case 'product_group':
            $type_name = 'Product Group';
            $output_link_url = 'edit_product_group.php?id=' . $recent_update_item['id'];
            break;
            
        case 'offer':
            $type_name = 'Offer';
            $output_link_url = 'edit_offer.php?id=' . $recent_update_item['id'];
            break;
            
        case 'ad':
            $type_name = 'Ad';
            $output_link_url = 'edit_ad.php?id=' . $recent_update_item['id'];
            break;
            
        case 'menu':
            $type_name = 'Menu';
            $output_link_url = 'view_menu_items.php?id=' . $recent_update_item['id'] . '&from=welcome&send_to=' . h(escape_javascript(urlencode(get_request_uri())));
            break;
            
        case 'style':
            $type_name = 'Page Style';
            $output_link_url = 'edit_' . $recent_update_item['style_type'] . '_style.php?id=' . $recent_update_item['id'];
            break;
            
        case 'common_region':
            $type_name = 'Common Region';
            $output_link_url = 'edit_common_region.php?id=' . $recent_update_item['id'];
            break;
            
        case 'designer_region':
            $type_name = 'Designer Region';
            $output_link_url = 'edit_designer_region.php?id=' . $recent_update_item['id'];
            break;
            
        case 'ad_region':
            $type_name = 'Ad Region';
            $output_link_url = 'edit_ad_region.php?id=' . $recent_update_item['id'];
            break;
            
        case 'dynamic_region':
            $type_name = 'Dynamic Region';
            $output_link_url = 'edit_dynamic_region.php?id=' . $recent_update_item['id'];
            break;
            
        case 'login_region':
            $type_name = 'Login Region';
            $output_link_url = 'edit_login_region.php?id=' . $recent_update_item['id'];
            break;
            
        case 'theme':
            $type_name = 'Theme';
            $output_link_url = 'edit_theme_file.php?id=' . $recent_update_item['id'];
            break;
            
        case 'design_file':
            $type_name = 'Design File';
            $output_link_url = 'edit_design_file.php?id=' . $recent_update_item['id'];
            break;
    }
    
    // if the username is known, then prepend ' by '
    if ($recent_update_item['username'] != '') {
        $recent_update_item['username'] = ' by ' . $recent_update_item['username'];
    }
    
    $output_recent_update_rows .=
        '<tr>
            <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($recent_update_item['name']) . '</td>
            <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($type_name) . '</td>
            <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $recent_update_item['timestamp'])) . h($recent_update_item['username']) . '</td>
        </tr>';
}

$output_recent_updates = '';

// if there are recent updates, then output area
if ($output_recent_update_rows != '') {
    $output_recent_updates =
        '<h2>Recent Updates</h2>
        <table class="chart">
            ' . $output_recent_update_rows . '
        </table>';
}

$latest_activity_maximum_number_of_items = 5;

$output_contacts = '';

// if the user has access to contacts, then get contacts
if (($user['role'] < 3) || ($user['manage_contacts'] == TRUE)) {
    $contacts = array();
    
    // if the user is above a user role, then get the contacts in a certain way (for performance reasons)
    if ($user['role'] < 3) {
        $query =
            "SELECT
                contacts.id,
                contacts.first_name,
                contacts.last_name,
                contacts.email_address,
                contacts.timestamp,
                user.user_username as username
            FROM contacts
            LEFT JOIN user ON contacts.user = user.user_id
            ORDER BY contacts.timestamp DESC
            LIMIT $latest_activity_maximum_number_of_items";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // loop through the result in order to prepare array of items
        while ($row = mysqli_fetch_assoc($result)) {
            $contacts[] = $row;
        }
        
    // else the user has a user role, so get the contacts in a different way
    } else {
        $contact_groups = get_items_user_can_edit('contact_groups', $user['id']);
        
        // if the user has access to at least one contact group, then get contacts
        if (count($contact_groups) > 0) {
            $sql_where = '';
            
            // loop through the contact groups in order to prepare where SQL statement
            foreach ($contact_groups as $contact_group) {
                // if there is already where content then add an or
                if ($sql_where != '') {
                    $sql_where .= ' OR ';
                }

                // add condition for this contact group
                $sql_where .= '(contacts_contact_groups_xref.contact_group_id = ' . $contact_group . ')';
            }
            
            $query =
                "SELECT
                    contacts.id,
                    contacts.first_name,
                    contacts.last_name,
                    contacts.email_address,
                    contacts.timestamp,
                    user.user_username as username
                FROM contacts
                LEFT JOIN user ON contacts.user = user.user_id
                LEFT JOIN contacts_contact_groups_xref ON contacts.id = contacts_contact_groups_xref.contact_id
                WHERE $sql_where
                GROUP BY contacts.id
                ORDER BY contacts.timestamp DESC
                LIMIT $latest_activity_maximum_number_of_items";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            
            // loop through the result in order to prepare array of items
            while ($row = mysqli_fetch_assoc($result)) {
                $contacts[] = $row;
            }
        }
    }
    
    $output_contact_rows = '';

    // loop through the contacts, in order to output rows
    foreach ($contacts as $contact) {
        $output_link_url = 'edit_contact.php?id=' . $contact['id'];
        
        $name = '';
        
        // if there is a first name, then add it to the name
        if ($contact['first_name'] != '') {
            $name .= $contact['first_name'];
        }
        
        // if there is a last name, then add it to the name
        if ($contact['last_name'] != '') {
            // if the name is not blank, then add space
            if ($name != '') {
                $name .= ' ';
            }
            
            $name .= $contact['last_name'];
        }
        
        // if the username is known, then prepend ' by '
        if ($contact['username'] != '') {
            $contact['username'] = ' by ' . $contact['username'];
        }
        
        $output_contact_rows .=
            '<tr>
                <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($name) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($contact['email_address']) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $contact['timestamp'])) . h($contact['username']) . '</td>
            </tr>';
    }
    
    // if there is at least one contact, then output contact area
    if (count($contacts) > 0) {
        $output_contacts =
            '<h3>Contacts</h3>
            <table class="chart" style="margin-bottom: 2em">
                ' . $output_contact_rows . '
            </table>';
    }
}

$output_users = '';

// if the user is a manager or above, then get users
if ($user['role'] < 3) {
    $users = array();
    
    $sql_where = "";
    
    // if the user is not an administrator, then prepare where condition for role
    if ($user['role'] > 0) {
        $sql_where = "WHERE user.user_role > '" . $user['role'] . "'";
    }
    
    $query =
        "SELECT
            user.user_id as id,
            user.user_username as username,
            user.user_email as email_address,
            user.user_timestamp as timestamp,
            last_modified_user.user_username as last_modified_username
        FROM user
        LEFT JOIN user as last_modified_user ON user.user_user = last_modified_user.user_id
        $sql_where
        ORDER BY user.user_timestamp DESC
        LIMIT $latest_activity_maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    $output_user_rows = '';

    // loop through the users, in order to output rows
    // we are using the variable name $recent_user instead of $user, because $user is a reserved variable for storing user information
    // there was a bug where the start page link in the header would not appear because were using the $user variable
    foreach ($users as $recent_user) {
        $output_link_url = 'edit_user.php?id=' . $recent_user['id'];
        
        // if the last modified username is known, then prepend ' by '
        if ($recent_user['last_modified_username'] != '') {
            $recent_user['last_modified_username'] = ' by ' . $recent_user['last_modified_username'];
        }
        
        $output_user_rows .=
            '<tr>
                <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($recent_user['username']) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($recent_user['email_address']) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $recent_user['timestamp'])) . h($recent_user['last_modified_username']) . '</td>
            </tr>';
    }
    
    // if there is at least one user, then output user area
    if (count($users) > 0) {
        $output_users =
            '<h3>Users</h3>
            <table class="chart" style="margin-bottom: 2em">
                ' . $output_user_rows . '
            </table>';
    }
}

$output_submitted_forms = '';

// if the user has access to manage forms, then get submitted forms
if (($user['role'] < 3) || ($user['manage_forms'] == TRUE)) {
    $submitted_forms = array();
    
    // if the user is above a user role, then get the submitted forms in a certain way
    if ($user['role'] < 3) {
        $query =
            "SELECT
                forms.id,
                custom_form_pages.form_name,
                user.user_username as username,
                contacts.first_name,
                contacts.last_name,
                forms.last_modified_timestamp as timestamp,
                last_modified_user.user_username as last_modified_username
            FROM forms
            LEFT JOIN custom_form_pages ON forms.page_id = custom_form_pages.page_id
            LEFT JOIN user ON forms.user_id = user.user_id
            LEFT JOIN contacts ON forms.contact_id = contacts.id
            LEFT JOIN user as last_modified_user ON forms.last_modified_user_id = last_modified_user.user_id
            ORDER BY forms.last_modified_timestamp DESC
            LIMIT $latest_activity_maximum_number_of_items";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // loop through the result in order to prepare array of items
        while ($row = mysqli_fetch_assoc($result)) {
            $submitted_forms[] = $row;
        }
        
    // else the user has a user role, so get the submitted forms in a different way
    } else {
        $custom_forms = array();
        
        // get all custom forms in order to determine which the user has access to
        $query =
            "SELECT
                page_id,
                page_folder as folder_id
            FROM page
            WHERE page_type = 'custom form'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        
        // loop through the result in order to prepare array of items
        while ($row = mysqli_fetch_assoc($result)) {
            // if user has access to the custom form then add it to array
            if (check_folder_access_in_array($row['folder_id'], $folders_that_user_has_access_to) == TRUE) {
                $custom_forms[] = $row;
            }
        }
        
        // if the user has access to at least one custom form, then get submitted forms
        if (count($custom_forms) > 0) {
            $sql_where = "";
            
            // loop through the custom forms in order to prepare where SQL conditions
            foreach ($custom_forms as $custom_form) {
                // if there is already where content then add an or
                if ($sql_where != "") {
                    $sql_where .= " OR ";
                }

                // add condition for this custom form
                $sql_where .= "(forms.page_id = '" . $custom_form['page_id'] . "')";
            }
            
            $query =
                "SELECT
                    forms.id,
                    custom_form_pages.form_name,
                    user.user_username as username,
                    forms.last_modified_timestamp as timestamp,
                    last_modified_user.user_username as last_modified_username
                FROM forms
                LEFT JOIN custom_form_pages ON forms.page_id = custom_form_pages.page_id
                LEFT JOIN user ON forms.user_id = user.user_id
                LEFT JOIN user as last_modified_user ON forms.last_modified_user_id = last_modified_user.user_id
                WHERE $sql_where
                ORDER BY forms.last_modified_timestamp DESC
                LIMIT $latest_activity_maximum_number_of_items";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            
            // loop through the result in order to prepare array of items
            while ($row = mysqli_fetch_assoc($result)) {
                $submitted_forms[] = $row;
            }
        }
    }
    
    $output_submitted_form_rows = '';

    // loop through the submitted forms, in order to output rows
    foreach ($submitted_forms as $submitted_form) {
        $output_link_url = 'edit_submitted_form.php?id=' . $submitted_form['id'];
        
        $name = '';
        
       // if there is a username then use that for the name
        if ($submitted_form['username'] != '') {
            $name = $submitted_form['username'];
        }
        
        // if the name is blank, then set it to placeholder
        if ($name == '') {
            $name = '[Unknown]';
        }
        
        // if the last modified username is not known, then set to [Unknown]
        if ($submitted_form['last_modified_username'] != '') {
            $submitted_form['last_modified_username'] = ' by ' . $submitted_form['last_modified_username'];
        }
        
        $output_submitted_form_rows .=
            '<tr>
                <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($submitted_form['form_name']) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($name) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $submitted_form['timestamp'])) . h($submitted_form['last_modified_username']) . '</td>
            </tr>';
    }
    
    // if there is at least one submitted form, then output submitted form area
    if (count($submitted_forms) > 0) {
        $output_submitted_forms =
            '<h3>Submitted Forms</h3>
            <table class="chart" style="margin-bottom: 2em">
                ' . $output_submitted_form_rows . '
            </table>';
    }
}

$output_carts = '';
$output_orders = '';

// if e-commerce is enabled and the user has access to manage e-commerce, then get carts and orders
if (
    (ECOMMERCE == TRUE)
    && (($user['role'] < 3) || ($user['manage_ecommerce'] == TRUE))
) {
    $carts = array();
    
    $query =
        "SELECT
            orders.id,
            user.user_username as username,
            contacts.first_name,
            contacts.last_name,
            orders.reference_code,
            orders.order_date as timestamp
        FROM orders
        LEFT JOIN user ON orders.user_id = user.user_id
        LEFT JOIN contacts ON orders.contact_id = contacts.id
        WHERE status = 'incomplete'
        ORDER BY orders.order_date DESC
        LIMIT $latest_activity_maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $carts[] = $row;
    }
    
    $output_cart_rows = '';

    // loop through the carts, in order to output rows
    foreach ($carts as $cart) {
        $output_link_url = 'view_order.php?id=' . $cart['id'];
        
        $name = '';
        
        // if there is a username then use that for the name
        if ($cart['username'] != '') {
            $name = $cart['username'];
            
        // else there is not a username, so use contact name
        } else {
            // if there is a first name, then add it to the name
            if ($cart['first_name'] != '') {
                $name .= $cart['first_name'];
            }
            
            // if there is a last name, then add it to the name
            if ($cart['last_name'] != '') {
                // if the name is not blank, then add space
                if ($name != '') {
                    $name .= ' ';
                }
                
                $name .= $cart['last_name'];
            }
        }
        
        // if the name is blank, then set it to placeholder
        if ($name == '') {
            $name = '[Visitor]';
        }
        
        // get total for order
        $query =
            "SELECT
                SUM(
                    (order_items.price * CAST(order_items.quantity AS signed))
                    + (order_items.tax * CAST(order_items.quantity AS signed))
                    + CAST((order_items.shipping * order_items.quantity) AS signed)
                ) as total
            FROM order_items
            WHERE order_items.order_id = '" . $cart['id'] . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];
        
        $output_cart_rows .=
            '<tr>
	        <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . $cart['reference_code'] . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($name) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . prepare_amount($total / 100) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $cart['timestamp'])) . '</td>
            </tr>';
    }
    
    // if there is at least one cart, then output cart area
    if (count($carts) > 0) {
        $output_carts =
            '<h3>Carts</h3>
            <table class="chart" style="margin-bottom: 2em">
                ' . $output_cart_rows . '
            </table>';
    }
    
    $orders = array();
    
    $query =
        "SELECT
            orders.id,
            orders.order_number,
            user.user_username as username,
            contacts.first_name,
            contacts.last_name,
            orders.total,
            orders.order_date as timestamp
        FROM orders
        LEFT JOIN user ON orders.user_id = user.user_id
        LEFT JOIN contacts ON orders.contact_id = contacts.id
        WHERE status != 'incomplete'
        ORDER BY orders.order_date DESC
        LIMIT $latest_activity_maximum_number_of_items";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    // loop through the result in order to prepare array of items
    while ($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
    
    $output_order_rows = '';

    // loop through the orders, in order to output rows
    foreach ($orders as $order) {
        $output_link_url = 'view_order.php?id=' . $order['id'];
        
        $name = '';
        
        // if there is a username then use that for the name
        if ($order['username'] != '') {
            $name = $order['username'];
            
        // else there is not a username, so use contact name
        } else {
            // if there is a first name, then add it to the name
            if ($order['first_name'] != '') {
                $name .= $order['first_name'];
            }
            
            // if there is a last name, then add it to the name
            if ($order['last_name'] != '') {
                // if the name is not blank, then add space
                if ($name != '') {
                    $name .= ' ';
                }
                
                $name .= $order['last_name'];
            }
        }
        
        // if the name is blank, then set it to placeholder
        if ($name == '') {
            $name = '[Visitor]';
        }
        
        $output_order_rows .=
            '<tr>
                <td class="chart_label pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . $order['order_number'] . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . h($name) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . prepare_amount($order['total'] / 100) . '</td>
                <td class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">' . get_relative_time(array('timestamp' => $order['timestamp'])) . '</td>
            </tr>';
    }
    
    // if there is at least one order, then output order area
    if (count($orders) > 0) {
        $output_orders =
            '<h3>Orders</h3>
            <table class="chart" style="margin-bottom: 2em">
                ' . $output_order_rows . '
            </table>';
    }
}

// if there is at least one contact, user, submitted form, cart, or order, then output latest activity area
if (
    ($output_contacts != '')
    || ($output_users != '')
    || ($output_submitted_forms != '')
    || ($output_carts != '')
    || ($output_orders != '')
) {
    $output_latest_activity =
        '<h2>Latest Activity</h2>
        ' . $output_contacts . '
        ' . $output_users . '
        ' . $output_submitted_forms . '
        ' . $output_carts . '
        ' . $output_orders;
}

// Output page.
print
    output_header() . '
    <div id="subnav">
        <a href="#" id="help_link" class="white_help_button">Help</a>
        <h1>Welcome, ' . h($_SESSION['sessionusername']) . '</h1>
        <table width="100%">
	    <tr>
		<td><p>You are logged in with <strong> ' . h($role) . '</strong> role access.</p></td>
		<td style="padding: 0; text-align: right"><p><strong>' . get_absolute_time(array('timestamp' => time())) .'</strong></p></td>
	    </tr>
	</table>
    </div>
    ' . $output_button_bar . '
    <div id="content"> 
        
        ' . $output_visitor_summary . '
        <table style="width:100%">
            <tr>
                <td id="recent_updates" style="padding-right: 2em; width:49%">
                    ' . $output_recent_updates . '
                </td>
		<td style="width:2%"></td>
                <td id="latest_activity" style="width:49%; vertical-align: top">
                    ' . $output_latest_activity . '
                </td>
            </tr>
        </table>
    </div>' .
    output_footer();
?>