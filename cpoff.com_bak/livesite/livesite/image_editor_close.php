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
?>