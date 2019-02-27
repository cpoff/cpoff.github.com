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

function get_change_random_password($properties) {

    $page_id = $properties['page_id'];

    $form = new liveform('change_random_password');

    if ($form->get('screen') == 'confirm') {

        $output = $form->get_messages();

    } else {

        $attributes =
            'action="' . OUTPUT_PATH . OUTPUT_SOFTWARE_DIRECTORY . '/change_random_password.php" ' .
            'method="post"';

        // If the form is empty, then set default values.
        if ($form->is_empty()) {
            $form->set('send_to', $_GET['send_to']);
        }

        // If strong password is enabled, then display password requirements.
        if (STRONG_PASSWORD) {
            $strong_password_help = get_strong_password_requirements();
        } else {
            $strong_password_help = '';
        }

        $form->set('new_password', 'required', true);
        $form->set('new_password_verify', 'required', true);

        if (PASSWORD_HINT) {
            $form->set('password_hint', 'maxlength', 100);
        }

        $system =
            get_token_field() . '
            <input type="hidden" name="send_to">';

        $output = render_layout(array(
            'page_id' => $page_id,
            'messages' => $form->get_messages(),
            'form' => $form,
            'attributes' => $attributes,
            'strong_password_help' => $strong_password_help,
            'system' => $system));

        $output = $form->prepare($output);
        
    }

    $form->remove();

    return
        '<div class="software_change_random_password">
            ' . $output . '
        </div>';

}