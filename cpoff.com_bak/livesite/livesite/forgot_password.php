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

// If the user has not submitted the form, then show form.
if (!$_POST) {
    echo get_forgot_password_screen();
    exit();
}

validate_token_field();

$page_name = db_value(
    "SELECT page_name
    FROM page
    WHERE page_type = 'forgot password'
    LIMIT 1");

if ($page_name != '') {
    $url = PATH . encode_url_path($page_name);
} else {
    $url = PATH . SOFTWARE_DIRECTORY . '/forgot_password.php';
}

$form = new liveform('forgot_password');

$form->add_fields_to_session();

$email = $form->get_field_value('email');
$screen = $form->get_field_value('screen');
$send_to = $form->get_field_value('send_to');

$form->validate_required_field('email', 'Email is required.');

// If there is not an error then get user info.
if (!$form->check_form_errors()) {
    $user = db_item(
        "SELECT
            user_id AS id,
            user_username AS username,
            user_password_hint AS password_hint
        FROM user
        WHERE user_email = '" . e($email) . "'");

    if (!$user['id']) {
        $form->mark_error('email', 'Sorry, we could not find an account for the email address you entered.');
    }
}

// If there is an error, forward user back to previous screen.
if ($form->check_form_errors()) {
    go($url);
}

// If password hint is enabled and the user has a password hint,
// and the user has not already told us that the password hint did not help,
// then show the password hint to the user.
if (
    PASSWORD_HINT
    && ($user['password_hint'] != '')
    && ($screen != 'password_hint')
) {
    $form->assign_field_value('screen', 'password_hint');

    go($url);
}

$random_password = random_password();

// insert new random password into database
db(
    "UPDATE user 
    SET 
        user_password = '" . md5($random_password) . "',
        user_random_password = '1',
        user_password_hint = '' 
    WHERE user_id = '" . $user['id'] . "'");

email(array(
    'to' => $email,
    'from_name' => ORGANIZATION_NAME,
    'from_email_address' => EMAIL_ADDRESS,
    'subject' => 'Forgotten Password',
    'body' =>
"You requested that your password be sent to you via e-mail.  For security reasons, we cannot retrieve your original password.  However, we can give you a temporary password that will allow you to access your account.

Email: $email
Temporary Password: $random_password

" . URL_SCHEME . HOSTNAME . $send_to . "

"));

log_activity('forgotten password was reset by user', $user['username']);

$form->remove();

$form->assign_field_value('screen', 'confirm');

$form->add_notice('Your request has been processed. You will receive an e-mail with a new temporary password shortly. If you do not receive the e-mail, then please check your spam folder. <a href="' . h(escape_url($send_to)) . '">Login</a>.');

go($url);