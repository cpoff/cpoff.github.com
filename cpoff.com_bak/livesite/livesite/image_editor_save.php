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

validate_token_field();

// if image editor did not send a link to the image, then output an error
if (!isset($_GET['image_file'])) {
    output_error('We\'re sorry, we encountered a problem while retrieving your edited image from PicMonkey. Your image was not updated on the website. Please try again later.');
}

// get parameters from image editor
$image_url = $_GET['image_file'];
$object_type = $_GET['object_type'];
$object_id = $_GET['object_id'];
$file_id = $_GET['file_id'];

$error = FALSE;

// get file data from database
$query = 
    "SELECT
        name,
        folder,
        design,
        type
    FROM files 
    WHERE id = '" . escape($file_id) . "'";
$result = mysqli_query(db::$con, $query) or output_error('Query failed.');
$row = mysqli_fetch_assoc($result);
$file_name = $row['name'];
$original_file_name = $row['name'];
$folder_id = $row['folder'];
$design = $row['design'];
$type = $row['type'];

/** Start Access Control Checks **/

// if the user has a user role, then check access
if ($user['role'] == 3) {
    // if user does not have access to edit this file or if it is a design file, then output error
    if ((check_edit_access($folder_id) == false) || ($design == 1)) {
        log_activity("access denied to save image from PicMonkey because user does not have access to edit image", $_SESSION['sessionusername']);
        $error = TRUE;
    }

    // do access control for various object types
    switch ($object_type) {
        case 'ad':
            // get this ad's name and ad region id
            $query = "SELECT name, ad_region_id FROM ads WHERE id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            $name = $row['name'];
            $ad_region_id = $row['ad_region_id'];
            
            // if the user does not have access to the ad region that this ad is in, then log activity and output error
            if (in_array($ad_region_id, get_items_user_can_edit('ad_regions', $user['id'])) == FALSE) {
                log_activity("access denied to update ad content with image from PicMonkey because user does not have access to edit ad (" . $name . ")", $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
            
        case 'pregion':
            // A user might be editing images in an inline page region that does not exist yet,
            // because region has not been saved/created yet, so that is why we add this check.
            if ($object_id) {
                // get the folder id from the page that this pregion is on
                $query =
                    "SELECT page.page_folder as pregion_folder_id
                    FROM pregion
                    LEFT JOIN page ON pregion.pregion_page = page.page_id
                    WHERE pregion.pregion_id = '" . escape($object_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                $row = mysqli_fetch_assoc($result);
                $pregion_folder_id = $row['pregion_folder_id'];
                
                // if the user does not have edit access to the pregion's folder, then log activity and output error
                if (check_edit_access($pregion_folder_id) == false) {
                    log_activity("access denied to update page region content with image from PicMonkey because user does not have access to edit folder that page region is in", $_SESSION['sessionusername']);
                    $error = TRUE;
                }
            }
            
            break;

        case 'system_region_header':
            // get the folder id from the page that this system region header is on
            $query =
                "SELECT page_folder as system_region_header_folder_id
                FROM page
                WHERE page_id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            $system_region_header_folder_id = $row['system_region_header_folder_id'];
            
            // if the user does not have edit access to the page's folder, then log activity and output error
            if (check_edit_access($system_region_header_folder_id) == false) {
                log_activity('access denied to update system region header content with image from PicMonkey because user does not have access to edit folder that the page is in', $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;

        case 'system_region_footer':
            // get the folder id from the page that this system region footer is on
            $query =
                "SELECT page_folder as system_region_footer_folder_id
                FROM page
                WHERE page_id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            $system_region_footer_folder_id = $row['system_region_footer_folder_id'];
            
            // if the user does not have edit access to the page's folder, then log activity and output error
            if (check_edit_access($system_region_footer_folder_id) == false) {
                log_activity('access denied to update system region footer content with image from PicMonkey because user does not have access to edit folder that the page is in', $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
        
        case 'cregion':
            // if user does not have access to this common region, then user does not have access to edit region, so log activity and output error
            if (in_array($object_id, get_items_user_can_edit('common_regions', $user['id'])) == FALSE) {
                log_activity("access denied to update common region content with image from PicMonkey because user does not have access to edit common region", $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
        
        case 'calendar_event':
            // if user does not have access to manage calendars or if they do not have access to edit this calendar event then log activity and output error
            if (($user['manage_calendars'] == FALSE) || (validate_calendar_event_access($object_id) == FALSE)) {
                log_activity("access denied to update calendar event content with image from PicMonkey because user does not have access to edit calendar that the calendar event is in", $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
        
        case 'product_group':
        case 'product':
            // if user does not have access to manage ecommerce then log activity and output error
            if ($user['manage_ecommerce'] == FALSE) {
                log_activity("access denied to update product or product group with image from PicMonkey because user does not have access to commerce", $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
            
        case 'form_field':
            // get the folder id from the page that the custom form is in
            $query =
                "SELECT page.page_folder as form_field_folder_id
                FROM form_fields
                LEFT JOIN page ON form_fields.page_id = page.page_id
                WHERE form_fields.id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            $form_field_folder_id = $row['form_field_folder_id'];
            
            // if the user does not have edit access to the form field's folder, then log activity and output error
            if (check_edit_access($form_field_folder_id) == false) {
                log_activity("access denied to update form field content with image from PicMonkey because user does not have access to edit folder that custom form is in", $_SESSION['sessionusername']);
                $error = TRUE;
            }
            
            break;
    }

    // if there was an error then output error
    if ($error == TRUE) {
        output_error('Access denied. <a href="javascript:history.go(-1)">Go back</a>.');
    }
}

/** Update database and image names if necessary **/

// if the image url that was supplied does not start with http:// or https://, then log activity and output error in order to prevent someone from hacking by saving the contents of any local file to the image
// the https is added for forward compatibility reasons, in case image editor decides to use a different URL
if ((mb_substr($image_url, 0, 7) != 'http://') && (mb_substr($image_url, 0, 8) != 'https://')) {
    log_activity('image URL (' . $image_url . ') from PicMonkey was invalid', $_SESSION['sessionusername']);
    output_error('The image URL (' . $image_url . ') from PicMonkey is not valid. <a href="javascript:history.go(-1)">Go back</a>.');
}

// get the image contents so that the image can be saved
$image_data = @file_get_contents($image_url);

// if there was an error accessing the file, then output error
if ($image_data === FALSE) {
    log_activity('image (' . $file_name . ') could not be retrieved from PicMonkey', $_SESSION['sessionusername']);
    output_error('We\'re sorry, we encountered a problem while retrieving your edited image from PicMonkey. Your image was not updated on the website. Please try again later.');
}

// if there is an image id and the file was not a GIF, then the image needs to be replaced, so save the image over the original
// we don't want to replace GIF's because we always save a new copy as a PNG,
// because the image editor does not support exporting as GIF.
if (
    ($_REQUEST['_imageid'] != '')
    && (mb_strtolower($type) != 'gif')
) {
    // delete the existing file. we have to do this in order to avoid permission errors in certain cirumstances
    unlink(FILE_DIRECTORY_PATH . '/' . $file_name);
    
    // save the file
    $handle = fopen(FILE_DIRECTORY_PATH . '/' . $file_name, 'w');
    fwrite($handle, $image_data);
    fclose($handle);
    
    // update image in database
    $query =
        "UPDATE files 
        SET 
            size = '" . escape(filesize(FILE_DIRECTORY_PATH . '/' . $file_name)) . "',
            timestamp = UNIX_TIMESTAMP(), 
            user = '" . $user['id'] . "' 
        WHERE id = '" . escape($file_id) . "'";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    log_activity("file ($file_name) was modified via PicMonkey", $_SESSION['sessionusername']);

// else save a new copy was selected or this file was a GIF so update the image name so that a new copy can be saved later on and save a new copy of the file in the database
} else {
    // get file name with and without file extension
    $file_name_without_extension = mb_substr($file_name, 0, mb_strrpos($file_name, '.'));
    $file_extension = mb_substr($file_name, mb_strrpos($file_name, '.') + 1);

    // If the file was a GIF, then change it to PNG.
    if (mb_strtolower($type) == 'gif') {
        $file_name = $file_name_without_extension . '.png';
        $file_extension = 'png';
    }

    // Check if file name is already in use and change it if necessary.
    $file_name = get_unique_name(array(
        'name' => $file_name,
        'type' => 'file'));

    // save the file
    $handle = fopen(FILE_DIRECTORY_PATH . '/' . $file_name, 'w');
    fwrite($handle, $image_data);
    fclose($handle);
    
    // insert file data into files table
    $query =
        "INSERT INTO files (
            name,
            folder,
            type,
            size,
            user,
            design,
            timestamp)
        VALUES (
            '" . escape($file_name) . "',
            '" . escape($folder_id) . "',
            '" . escape($file_extension) . "',
            '" . escape(filesize(FILE_DIRECTORY_PATH . '/' . $file_name)) . "',
            '" . $user['id'] . "',
            '0',
            UNIX_TIMESTAMP())";
    $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
    
    log_activity("file ($file_name) was created via PicMonkey", $_SESSION['sessionusername']);
}

/** replace image in content with new dimensions and src value if necessary **/

$column_to_update = '';

// if there is a column to update, then set it so that it can be used later on in various places
if ($_GET['column_to_update']) {
    $column_to_update = $_GET['column_to_update'];
}

switch ($object_type) {
    case 'ad':
        // get content
        $query = "SELECT content FROM ads WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['content'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE ads SET content = '" . escape($content) . "' WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
    
    case 'calendar_event':
        // get content
        $query = "SELECT full_description FROM calendar_events WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['full_description'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE calendar_events SET full_description = '" . escape($content) . "' WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
    
    case 'cregion':
        // get content
        $query = "SELECT cregion_content as content FROM cregion WHERE cregion_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['content'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE cregion SET cregion_content = '" . escape($content) . "' WHERE cregion_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
    
    case 'pregion':
        // A user might be editing images in an inline page region that does not exist yet,
        // because region has not been saved/created yet, so that is why we add this check.
        if ($object_id) {
            // get content
            $query = "SELECT pregion_content as content FROM pregion WHERE pregion_id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
            $row = mysqli_fetch_assoc($result);
            $content = $row['content'];
            
            $content = update_image_in_content($content, $original_file_name, $file_name);
            
            // update content in database
            $query = "UPDATE pregion SET pregion_content = '" . escape($content) . "' WHERE pregion_id = '" . escape($object_id) . "'";
            $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        }
        
        break;

    case 'system_region_header':
        // get content
        $query = "SELECT system_region_header as content FROM page WHERE page_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['content'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE page SET system_region_header = '" . escape($content) . "' WHERE page_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;

    case 'system_region_footer':
        // get content
        $query = "SELECT system_region_footer as content FROM page WHERE page_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['content'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE page SET system_region_footer = '" . escape($content) . "' WHERE page_id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
    
    case 'product':
    case 'product_group':
        // set the sql table based on the object type
        if ($object_type == 'product') {
            $sql_table = 'products';
        } else {
            $sql_table = 'product_groups';
        }
        
        $sql_column = '';
        $content = '';
        
        // set column to update and get content
        switch ($column_to_update) {
            case 'image_name':
                $sql_column = 'image_name';
                $content = $file_name;
                break;
                
            case 'details':
                $sql_column = 'details';
                
                $query = "SELECT details FROM $sql_table WHERE id = '" . escape($object_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                $row = mysqli_fetch_assoc($result);
                $content = update_image_in_content($row['details'], $original_file_name, $file_name);
                break;
                
            default:
                $sql_column = 'full_description';
                
                $query = "SELECT full_description FROM $sql_table WHERE id = '" . escape($object_id) . "'";
                $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
                $row = mysqli_fetch_assoc($result);
                $content = update_image_in_content($row['full_description'], $original_file_name, $file_name);
                break;
        }
        
        // update content in database
        $query = "UPDATE $sql_table SET $sql_column = '" . escape($content) . "' WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
        
    case 'form_field':
        // get content
        $query = "SELECT information FROM form_fields WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        $row = mysqli_fetch_assoc($result);
        $content = $row['information'];
        
        $content = update_image_in_content($content, $original_file_name, $file_name);
        
        // update content in database
        $query = "UPDATE form_fields SET information = '" . escape($content) . "' WHERE id = '" . escape($object_id) . "'";
        $result = mysqli_query(db::$con, $query) or output_error('Query failed.');
        break;
}

// if the image was replaced and it was not a GIF, then it has the same file name, so output code that will update the user's cache,
// so that the user will see the new image and forward the user to the original page that they were at
if (
    ($_REQUEST['_imageid'] != '')
    && (mb_strtolower($type) != 'gif')
) {
    $output_rawurlencode_iframe = '';
    
    // if the file name is different when it is rawurlencoded, then we need to clear the cache for both the plain file name and the rawurlencoded file name
    // IE will show the old image if we do not do this, because sometimes a file is embedded with its plain name and sometimes with its rawurlencoded name
    if ($file_name != encode_url_path($file_name)) {
        $output_rawurlencode_iframe = '<iframe id="image_rawurlencode" src="' . OUTPUT_PATH . h(encode_url_path($file_name)) . '" style="display: none"></iframe>';
    }
    
    print
        '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                ' . get_generator_meta_tag() . '
                <script type="text/javascript">
                    function init()
                    {
                        // reload the iframe with the image
                        document.getElementById("image").contentWindow.location.reload(true);
                        
                        // if the rawurlencode iframe exists, then reload it
                        if (document.getElementById("image_rawurlencode")) {
                            document.getElementById("image_rawurlencode").contentWindow.location.reload(true);
                        }
                        
                        // wait a little bit to make sure that the iframe(s) have reloaded and then send the user to the original page that they came from
                        setTimeout("window.parent.location = \'' . URL_SCHEME . HOSTNAME . escape_javascript($_GET['send_to']) . '\';", 1000);
                    }
                    
                    window.onload = init; 
                </script>
            </head>
            <body>
                <iframe id="image" src="' . OUTPUT_PATH . h($file_name) . '" style="display: none"></iframe>
                ' . $output_rawurlencode_iframe . '
            </body>
        </html>';
                
// else the user chose to save a new copy or the image was a GIF, so the image has a new name,
// so we don't need to update the cache, so forward user to the page that they came from
} else {
    print
        '<!DOCTYPE html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                ' . get_generator_meta_tag() . '
                <script type="text/javascript">
                    function init()
                    {
                        window.parent.location = "' . URL_SCHEME . HOSTNAME . escape_javascript($_GET['send_to']) . '";
                    }
                    
                    window.onload = init; 
                </script>
            </head>
            <body>
            </body>
        </html>';
}
?>