<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Modify Products</title>
        <?=get_generator_meta_tag()?>
        <link rel="stylesheet" type="text/css" href="<?=CONTROL_PANEL_STYLESHEET_URL?>">
        <?=get_favicon_tags()?>
        <script type="text/javascript">
            function edit_products()
            {                        
                // If the user selected an option for the enabled field then update field in the form.
                if (document.getElementById("enabled").value != "") {
                    opener.document.form.edit_enabled.value = document.getElementById("enabled").value;
                }

                // If shipping is enabled, then deal with allowed/disallowed zones.
                if (document.form.allowed_zones) {
                    opener.document.form.edit_allowed_zones.value = "";
                    
                    // Loop through all allowed zone checkboxes.
                    for (i = 0; i < document.form.allowed_zones.length; i++) {
                        // If zone checkbox is checked, then add zone to hidden form field on opener.
                        if (document.form.allowed_zones[i].checked == true) {
                            // If there is already zones in the list of zones, then add a comma first.
                            if (opener.document.form.edit_allowed_zones.value) {
                                opener.document.form.edit_allowed_zones.value += ",";
                            }
                            
                            opener.document.form.edit_allowed_zones.value += document.form.allowed_zones[i].value;
                        }
                    }
                    
                    opener.document.form.edit_disallowed_zones.value = "";
                    
                    // Loop through all disallowed zone checkboxes.
                    for (i = 0; i < document.form.disallowed_zones.length; i++) {
                        // If zone checkbox is checked, then add zone to hidden form field on opener.
                        if (document.form.disallowed_zones[i].checked == true) {
                            // If there is already zones in the list of zones, then add a comma first.
                            if (opener.document.form.edit_disallowed_zones.value) {
                                opener.document.form.edit_disallowed_zones.value += ",";
                            }
                            
                            opener.document.form.edit_disallowed_zones.value += document.form.disallowed_zones[i].value;
                        }
                    }
                }
                
                opener.document.form.submit();
                window.close();
            }
        </script>
    </head>
    <body class="ecommerce">
        <div id="content">
            <h1>Modify Products</h1>
            <div class="subheading" style="margin-bottom: 1em">You may update the selected Products via the form below.</div>
            <form name="form">
                <table class="field" style="margin-bottom: 1em !important">
                    <tr>
                        <td>Status:</td>
                        <td>
                            <?=$liveform->output_field(array(
                                'type' => 'select',
                                'id' => 'enabled',
                                'options' => $enabled_options))?>
                        </td>
                    </tr>
                </table>

                <?php if (ECOMMERCE_SHIPPING): ?>
                    <table style="margin-bottom: 1em">
                        <tr>
                            <td style="width: 50%; vertical-align: top; padding-right: 2em">

                                <div style="margin-bottom: .5em; font-weight: bold">
                                    Allow Shipping Zones
                                </div>

                                <?php foreach($zones as $zone): ?>
                                    <?=$liveform->output_field(array(
                                        'type' => 'checkbox',
                                        'id' => 'allowed_zone_' . $zone['id'],
                                        'name' => 'allowed_zones',
                                        'value' => $zone['id'],
                                        'class' => 'checkbox'))?><label for="allowed_zone_<?=$zone['id']?>">

                                    <?=h($zone['name'])?></label><br>
                                <?php endforeach ?>

                            </td>
                            <td style="width: 50%; vertical-align: top">

                                <div style="margin-bottom: .5em; font-weight: bold">
                                    Disallow Shipping Zones
                                </div>

                                <?php foreach($zones as $zone): ?>
                                    <?=$liveform->output_field(array(
                                        'type' => 'checkbox',
                                        'id' => 'disallowed_zone_' . $zone['id'],
                                        'name' => 'disallowed_zones',
                                        'value' => $zone['id'],
                                        'class' => 'checkbox'))?><label for="disallowed_zone_<?=$zone['id']?>">
                                    <?=h($zone['name'])?></label><br>
                                <?php endforeach ?>

                            </td>
                        </tr>
                    </table>
                <?php endif ?>

                <div class="buttons">
                    <input type="button" value="Modify Products" class="submit-primary" onclick="edit_products()">
                </div>
            </form>
        </div>
    </body>
</html>