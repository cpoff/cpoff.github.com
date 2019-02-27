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
    case 'Name':
        $sort_column = 'name';
        break;

    case 'Required Subtotal':
        $sort_column = 'required_subtotal';
        break;

    case 'Required Product':
        $sort_column = 'products.name';
        break;

    case 'Required Quantity':
        $sort_column = 'required_quantity';
        break;

    case 'Last Modified':
        $sort_column = 'timestamp';
        break;

    default:
        $sort_column = 'timestamp';
        $asc_desc = 'DESC';
}

if ($_GET['sort']) {
    $asc_desc = $_GET['order'];
}

$query = "SELECT
            offer_rules.id,
            offer_rules.name,
            offer_rules.required_subtotal,
            products.name as required_product_name,
            offer_rules.required_quantity,
            user.user_username as user,
            offer_rules.timestamp as timestamp
        FROM offer_rules
        LEFT JOIN products ON offer_rules.required_product_id = products.id
        LEFT JOIN user ON offer_rules.user = user.user_id
        ORDER BY $sort_column $asc_desc";

$result = mysqli_query(db::$con, $query) or output_error('Query failed.');

$number_of_results = 0;

while ($row = mysqli_fetch_array($result)) {
    $id = $row['id'];
    $name = $row['name'];
    $required_subtotal = sprintf("%01.2lf", $row['required_subtotal'] / 100);
    $required_product_name = $row['required_product_name'];
    $username = $row['user'];
    $timestamp = $row['timestamp'];

    if ($required_product_name) {
        $required_quantity = $row['required_quantity'];
    } else {
        $required_quantity = '';
    }

    $output_link_url = 'edit_offer_rule.php?id=' . $id;
    
    $number_of_results++;
    
    $output_rows .=
        '<tr class="pointer" onclick="window.location.href=\'' . $output_link_url . '\'">
            <td class="chart_label">' . h($name) . '</td>
            <td style="text-align: right; padding-right: 1em;">' . BASE_CURRENCY_SYMBOL . $required_subtotal . '</td>
            <td>' . h($required_product_name) . '</td>
            <td>' . $required_quantity . '</td>
            <td>' . get_relative_time(array('timestamp' => $timestamp)) . ' by ' . h($username) . '</td>
        </tr>';
}

$output = output_header() . '
<div id="subnav">
    ' . render(array('template' => 'commerce_subnav.php')) . '
</div>
<div id="button_bar">
    <a href="add_offer_rule.php">Create Offer Rule</a>
</div>
<div id="content">
    
    <a href="#" id="help_link">Help</a>
    <h1>All Offer Rules</h1>
    <div class="subheading">All rules available to any offer.</div>
    <div class="view_summary">
        Viewing '. number_format($number_of_results) .' of ' . number_format($number_of_results) . ' Total
    </div>
    <table class="chart">
        <tr>
            <th>' . asc_or_desc('Name', 'view_offer_rules') . '</th>
            <th style="text-align: right; padding-right: 1em;">' . asc_or_desc('Required Subtotal', 'view_offer_rules') . '</th>
            <th>' . asc_or_desc('Required Product', 'view_offer_rules') . '</th>
            <th>' . asc_or_desc('Required Quantity', 'view_offer_rules') . '</th>
            <th>' . asc_or_desc('Last Modified', 'view_offer_rules') . '</th>
        </tr>
        ' . $output_rows . '
    </table>
</div>' .
output_footer();

print $output;
?>