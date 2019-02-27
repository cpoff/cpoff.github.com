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

// init varibles to be used in the output
$object_id = $_GET['object_id'];
$object_type = $_GET['object_type'];
$file_name = $_GET['file_name'];

// get file data from database
$query = 
    "SELECT
        id,
        folder,
        design
    FROM files
    WHERE name = '" . escape($file_name) . "'";
$result = mysqli_query(db::$con, $query) or output_error('Query failed');

// if the file does not exist, then output error
if (mysqli_num_rows($result) == 0) {
    output_error('The image cannot be updated because it no longer exists. <a href="javascript:history.go(-1)">Go back</a>.');
}

$row = mysqli_fetch_assoc($result);
$file_id = $row['id'];
$file_folder = $row['folder'];
$file_design = $row['design'];

// if user does not have access to edit this file, or if it is a design file, then output error
if (($user['role'] == 3) && ((check_edit_access($file_folder) == false) || ($file_design == 1))) {
    log_activity("access denied to edit image with PicMonkey because user does not have access to edit image", $_SESSION['sessionusername']);
    output_error('Access denied. <a href="javascript:history.go(-1)">Go back</a>.');
}

$column_to_update = '';

// if there is a column specified to update, then prepare it for the link
if ($_GET['column_to_update'] != '') {
    $column_to_update = '&column_to_update=' . h(urlencode($_GET['column_to_update']));
}

$software_title = '';

if (PRIVATE_LABEL == FALSE) {
    $software_title = 'liveSite - ';
}

// if CDN is enabled, then use Google CDN for jQuery for performance reasons
if (
    (defined('CDN') == FALSE)
    || (CDN == TRUE)
) {
    $output_jquery =
        '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/jquery-ui.min.js"></script>';

// else CDN is disabled, so use local jQuery
} else {
    $output_jquery =
        '<script src="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/jquery/jquery-1.7.2.min.js"></script>
        <script src="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/jquery/jquery-ui-1.8.21.min.js"></script>';
}

echo
    '<!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <title>' . $software_title . h($_SERVER['HTTP_HOST']) . '</title>
            ' . get_generator_meta_tag() . '
            <link rel="stylesheet" type="text/css" href="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/jquery/theme/standard.' . ENVIRONMENT_SUFFIX . '.css" />
            <link rel="stylesheet" type="text/css" href="' . CONTROL_PANEL_STYLESHEET_URL . '" />
            ' . get_favicon_tags() . '
            <script type="text/javascript">
                var path = "' . escape_javascript(PATH) . '";
                var software_directory = "' . escape_javascript(SOFTWARE_DIRECTORY) . '";
                var software_token = "' . $_SESSION['software']['token'] . '";
            </script>
            ' . $output_jquery . '
            <script type="text/javascript" src="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/backend.' . ENVIRONMENT_SUFFIX . '.js"></script>
        </head>
        <body class="files nofooter" style="background: #000;">
            <div style="background: #000; padding: .5em 0;"><a href="' . URL_SCHEME . HOSTNAME . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/welcome.php"><img src="' . LOGO_URL . '" width="150" border="0" alt="logo" title="" /></a></div>
            <div id="subnav">
                <div>You\'re editing ' . h($file_name) . '...</div>
            </div>
            <div id="content" style="margin: 0; padding: 0">
                <iframe id="image_editor" scrolling="no" src="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/image_editor_send.php?file_id=' . $file_id . '&object_id=' . h(urlencode($object_id)) . '&object_type=' . h(urlencode($object_type)) . $column_to_update . '&send_to=' . h(urlencode($_GET['send_to'])) . '" style="display: block; padding: 0em; margin: 0em; width: 100%; height: 640px;" frameborder="0"><p class="error">Your Browser does not support frames.</iframe>
            </div>
            <script type="text/javascript">
                function init()
                {
                    document.getElementById("image_editor").style.height = document.documentElement.clientHeight * .8 + "px";
                }
                
                window.onload = init;
            </script>
            ' . output_footer();