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

if (!$_POST) {
    // get offer rule data
    $query = "SELECT * FROM offer_rules WHERE id = '" . escape($_GET['id']) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    $row = mysqli_fetch_assoc($result);

    $name = $row['name'];
    $required_subtotal = sprintf("%01.2lf", $row['required_subtotal'] / 100);
    $required_product_id = $row['required_product_id'];
    $required_quantity = $row['required_quantity'];

    $output =
        output_header() . '
        <div id="subnav">
            <h1>' . h($name) . '</h1>
        </div>
        <div id="content">
            
            <a href="#" id="help_link">Help</a>
            <h1>Edit Special Offer Rule</h1>
            <div class="subheading" style="margin-bottom: 1em">Edit an offer rule that can be assigned to any offer.</div>
            <form name="form" action="edit_offer_rule.php" method="post">
                ' . get_token_field() . '
                <table class="field">
                    <tr>
                        <th colspan="2"><h2>New Offer Rule Name</h2></th>
                    </tr>
                    <tr>
                        <td>Offer Rule Name:</td>
                        <td><input type="text" name="name" maxlength="50" value="' . h($name) . '" /></td>
                    </tr>
                    <tr>
                        <th colspan="2"><h2>Offer Rule Details</h2></th>
                    </tr>
                    <tr>
                        <td>Required Subtotal (' . BASE_CURRENCY_SYMBOL . '):</td>
                        <td><input type="text" name="required_subtotal" size="5" value="' . $required_subtotal . '" /></td>
                    </tr>
                    <tr>
                        <td colspan="2">and/or</td>
                    </tr>
                    <tr>
                        <td>Required Product:</td>
                        <td><select name="required_product_id"><option value="">-None-</option>' .  select_product($required_product_id) . '</select></td>
                    </tr>
                    <tr>
                        <td>Required Quantity:</td>
                        <td><input type="text" name="required_quantity" size="3" maxlength="10" value="' . $required_quantity . '" /></td>
                    </tr>
                </table>
                <div class="buttons">
                    <input type="submit" name="submit_save" value="Save" class="submit-primary">&nbsp;&nbsp;&nbsp;<input type="button" name="cancel" value="Cancel" OnClick="javascript:history.go(-1);" class="submit-secondary">&nbsp;&nbsp;&nbsp;<input type="submit" name="submit_delete" value="Delete" class="delete" onclick="return confirm(\'WARNING: This offer rule will be permanently deleted.\')">
                </div>
                <input type="hidden" name="id" value="' . h($_GET['id']) . '">
            </form>
        </div>' .
        output_footer();

    print $output;

} else {
    validate_token_field();
    
    // if offer rule was selected for delete
    if ($_POST['submit_delete'] == 'Delete') {
        // delete offer rule
        $query = "DELETE FROM offer_rules WHERE id = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        log_activity('offer rule (' . $_POST['name'] . ') was deleted', $_SESSION['sessionusername']);
    // else offer rule was not selected for delete
    } else {
        // convert required subtotal from dollars to cents
        $required_subtotal = $_POST['required_subtotal'] * 100;

        // update offer rule
        $query = "UPDATE offer_rules SET
                    name = '" . escape($_POST['name']) . "',
                    required_subtotal = '" . escape($required_subtotal) . "',
                    required_product_id = '" . escape($_POST['required_product_id']) . "',
                    required_quantity = '" . escape($_POST['required_quantity']) . "',
                    user = '" . $user['id'] . "',
                    timestamp = UNIX_TIMESTAMP()
                WHERE id = '" . escape($_POST['id']) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');

        log_activity('offer rule (' . $_POST['name'] . ') was modified', $_SESSION['sessionusername']);
    }

    // forward user to view offer rules screen
    header('Location: ' . URL_SCHEME . $_SERVER['HTTP_HOST'] . PATH . SOFTWARE_DIRECTORY . '/view_offer_rules.php');
}
?>