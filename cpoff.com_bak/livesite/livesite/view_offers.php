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

switch($_GET['sort']) {
    case 'Offer Code':
        $sort_column = 'code';
        break;

    case 'Message':
        $sort_column = 'description';
        break;

    case 'Rule':
        $sort_column = 'offer_rules.name';
        break;

    case 'Status':
        $sort_column = 'status';
        break;

    case 'Start Date':
        $sort_column = 'start_date';
        break;

    case 'End Date':
        $sort_column = 'end_date';
        break;

    case 'Require Code':
        $sort_column = 'require_code';
        break;
        
    case 'Best':
        $sort_column = 'only_apply_best_offer';
        break;

    case 'Last Modified':
        $sort_column = 'timestamp';
        break;

    default:
        $sort_column = 'timestamp';
        $asc_desc = 'desc';
}

if ($_GET['sort']) {
    $asc_desc = $_GET['order'];
}

$number_of_results = 0;

// get offer actions for offers
$query =
    "SELECT
        offers_offer_actions_xref.offer_id,
        offers_offer_actions_xref.offer_action_id as id,
        offer_actions.name
    FROM offers_offer_actions_xref
    LEFT JOIN offer_actions ON offers_offer_actions_xref.offer_action_id = offer_actions.id
    ORDER BY offer_actions.name ASC";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
$offer_actions = mysqli_fetch_items($result);

$offers_with_offer_actions = array();

// loop through the offer actions in order to add them to array
foreach ($offer_actions as $offer_action) {
    // if an action has not been added for this offer yet, then prepare array
    if (isset($offers_with_offer_actions[$offer_action['offer_id']]) == FALSE) {
        $offers_with_offer_actions[$offer_action['offer_id']] = array();
    }
    
    // add offer action to array
    $offers_with_offer_actions[$offer_action['offer_id']][] = $offer_action;
}

// Get the current date so that later we can figure out if offers are active.
$current_date = date('Y-m-d');

$query = "SELECT
            offers.id,
            offers.code,
            offers.description,
            offer_rules.id as offer_rule_id,
            offer_rules.name as offer_rule_name,
            offers.status,
            offers.start_date,
            offers.end_date,
            offers.require_code,
            offers.only_apply_best_offer,
            user.user_username as user,
            offers.timestamp as timestamp
        FROM offers
        LEFT JOIN offer_rules ON offers.offer_rule_id = offer_rules.id
        LEFT JOIN user ON offers.user = user.user_id
        ORDER BY $sort_column $asc_desc";

$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
while ($row = mysqli_fetch_array($result)) {
    $id = $row['id'];
    $code = $row['code'];
    $description = $row['description'];
    $offer_rule_id = $row['offer_rule_id'];
    $offer_rule_name = $row['offer_rule_name'];
    $status = ucwords($row['status']);
    $start_date = $row['start_date'];
    $end_date = $row['end_date'];
    $require_code = $row['require_code'];
    $only_apply_best_offer = $row['only_apply_best_offer'];
    $username = $row['user'];
    $timestamp = $row['timestamp'];

    $output_link_url = 'edit_offer.php?id=' . $id;

    // If this offer is active, then use class that shows green color.
    if (
        ($row['status'] == 'enabled')
        && ($start_date <= $current_date)
        && ($end_date >= $current_date)
    ) {
        $output_status_class = 'status_enabled';
    
    // Otherwise this offer is not active, so use class that shows red color.
    } else {
        $output_status_class = 'status_disabled';
    }
    
    $output_offer_actions = '';
    
    // if this offer has an offer action, then output offer actions
    if (isset($offers_with_offer_actions[$id]) == TRUE) {
        // loop through the offer actions for this offer in order to output them
        foreach ($offers_with_offer_actions[$id] as $offer_action) {
            // if this is not the first offer action for this offer, then output comma and line break for separation
            if ($output_offer_actions != '') {
                $output_offer_actions .= ',<br />';
            }
            
            $output_offer_actions .= '<a href="edit_offer_action.php?id=' . $offer_action['id'] . '">' . h($offer_action['name']) . '</a>';
        }
    }
    
    $output_require_code_check_mark = '';
    
    if ($require_code == 1) {
        $output_require_code_check_mark = '<img src="images/check_mark.gif" width="7" height="7" alt="check mark" title="" />';
    }
    
    $output_only_apply_best_offer_check_mark = '';
    
    if ($only_apply_best_offer == 1) {
        $output_only_apply_best_offer_check_mark = '<img src="images/check_mark.gif" width="7" height="7" alt="check mark" title="" />';
    }
    
    $number_of_results++;
    
    $output_rows .=
        '<tr class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">
            <td class="chart_label ' . $output_status_class . '">' . h($code) . '</td>
            <td style="white-space: normal">' . h($description) . '</td>
            <td><a href="edit_offer_rule.php?id=' . $offer_rule_id . '">' . h($offer_rule_name) . '</a></td>
            <td>' . $output_offer_actions . '</td>
            <td>' . $status . '</td>
            <td>' . prepare_form_data_for_output($start_date, 'date') . '</td>
            <td>' . prepare_form_data_for_output($end_date, 'date') . '</td>
            <td style="text-align: center">' . $output_require_code_check_mark . '</td>
            <td style="text-align: center">' . $output_only_apply_best_offer_check_mark . '</td>
            <td>' . get_relative_time(array('timestamp' => $timestamp)) . ' by ' . h($username) . '</td>
        </tr>';
}

$output = output_header() . '
<div id="subnav">
    ' . render(array('template' => 'commerce_subnav.php')) . '
</div>
<div id="button_bar">
    <a href="add_offer.php">Create Offer</a>
</div>
<div id="content">
    
    <a href="#" id="help_link">Help</a>
    <h1>All Offers</h1>
    <div class="subheading">All order and product discounts.</div>
    <div class="view_summary">
        Viewing '. number_format($number_of_results) .' of ' . number_format($number_of_results) . ' Total
    </div>
    <table class="chart">
        <tr>
            <th>' . asc_or_desc('Offer Code','view_offers') . '</th>
            <th>' . asc_or_desc('Message','view_offers') . '</th>
            <th>' . asc_or_desc('Rule','view_offers') . '</th>
            <th>Actions</th>
            <th>' . asc_or_desc('Status','view_offers') . '</th>
            <th>' . asc_or_desc('Start Date','view_offers') . '</th>
            <th>' . asc_or_desc('End Date','view_offers') . '</th>
            <th style="text-align: center">' . asc_or_desc('Require Code','view_offers') . '</th>
            <th style="text-align: center">' . asc_or_desc('Best','view_offers') . '</th>
            <th>' . asc_or_desc('Last Modified','view_offers') . '</th>
        </tr>
        ' . $output_rows . '
    </table>
</div>' .
output_footer();

print $output;
?>