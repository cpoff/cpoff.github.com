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

// get file info
$query =
    "SELECT
        name,
        folder,
        design,
        type
    FROM files
    WHERE id = '" . escape($_GET['file_id']) . "'";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
$row = mysqli_fetch_assoc($result);
$file_name = $row['name'];
$folder_id = $row['folder'];
$design = $row['design'];
$file_name_extension = $row['type'];
    
// if the user has a user role and the user does not have edit access to the file or if it is a design file, then output error
if (($user['role'] == 3) && ((check_edit_access($folder_id) == false) || ($design == 1))) {
    log_activity("access denied to send image to PicMonkey because user does not have access to edit image", $_SESSION['sessionusername']);
    output_error('Access denied. <a href="javascript:history.go(-1)">Go back</a>.');
}

// get the file address to send to image editor
$file = FILE_DIRECTORY_PATH . '/' . $file_name;

// set the output format to the same format as the current image (use the extension to determine this)
$output_format = '';

switch($file_name_extension) {
    case 'jpg':
    case 'jpeg':
        $output_format = 'jpg';
        break;
        
    case 'png':
        $output_format = 'png';
        break;
        
    case 'gif':
        $output_format = 'png';
        break;
        
    default:
        $output_format = 'jpg';
        break;
}

// setup the parameters to send to image editor
$post_data = array();
$post_data['_apikey'] = 'c263024a4414147e763ba2e486d929b6';
$post_data['_import'] = 'image_file';

// If this is PHP 5.5.0 or greater then use new way to reference file path.
if (function_exists('curl_file_create')) {
    $post_data['image_file'] = curl_file_create($file);

// Otherwise the PHP version is before 5.5.0, so use old way.
} else {
    $post_data['image_file'] = '@' . $file;
}

$post_data['_imageid'] = $_GET['file_id'];

// if this is a form item view or form list view page type then set the parameter to only allow the replace image option when saving
if (($_GET['object_type'] == 'form_list_view') || ($_GET['object_type'] == 'form_item_view')) {
    $post_data['_replace'] = 'confirm';

// else set the parameter to ask the user to replace or save a new copy options when saving
} else {
    $post_data['_replace'] = 'ask';
}

$post_data['_export_title'] = 'Save & Close';
$post_data['_export'] = URL_SCHEME . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/image_editor_save.php';
$post_data['_export_agent'] = 'browser';
$post_data['_returntype'] = 'text';
$post_data['_out_format'] = $output_format;
$post_data['_close_target'] = URL_SCHEME . HOSTNAME . PATH . SOFTWARE_DIRECTORY . '/image_editor_close.php?send_to=' . urlencode($_GET['send_to']);
$post_data['file_id'] = $_GET['file_id'];
$post_data['object_id'] = $_GET['object_id'];
$post_data['object_type'] = $_GET['object_type'];
$post_data['send_to'] = $_GET['send_to'];
$post_data['token'] = $_SESSION['software']['token'];

// if there is a column to update, then send it through image editor so that we can use it when saving the image
if ($_GET['column_to_update'] != '') {
    $post_data['column_to_update'] = $_GET['column_to_update'];
}

// initialize cURL
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://www.picmonkey.com/service/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 999);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
curl_setopt($ch, CURLOPT_FORBID_REUSE, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));

// if there is a proxy address, then send cURL request through proxy
if (PROXY_ADDRESS != '') {
    curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
    curl_setopt($ch, CURLOPT_PROXY, PROXY_ADDRESS);
}

// get cURL response
$response_data = curl_exec($ch);

$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);

curl_close($ch);

// if there is an error, then log and output error
if (($curl_errno != '') || (mb_substr($response_data, 0, 4) != 'http')) {
    log_activity('image (' . $file_name . ') could not be sent to PicMonkey. cURL Error Number: ' . $curl_errno . '. cURL Error Message: ' . $curl_error . '. ' . $response_data, $_SESSION['sessionusername']);
    output_error('We\'re sorry, we encountered a problem while sending your image to PicMonkey for editing. PicMonkey appears to be unavailable. Please try again later. <a href="javascript:history.go(-1);">Go back</a>.<br /><br />' . h($response_data));

// else there was not an error, so send user to the URL that the image editor responded with
} else {
    header('Location: ' . $response_data);
}