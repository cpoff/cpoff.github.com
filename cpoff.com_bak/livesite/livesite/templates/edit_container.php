<div id="subnav">
    <h1>
        <?php if ($screen == 'create'): ?>
            [new container]
        <?php else: ?>
            <?=h($container['name'])?>
        <?php endif ?>
    </h1>
</div>
<div id="content">
    <?=$form->get_messages()?>
    <a href="#" id="help_link">Help</a>
    <h1><?=ucfirst($screen)?> Container</h1>
    <div class="subheading" style="margin-bottom: 1.5em">
        <?php if ($screen == 'create'): ?>
            Create a new shipping container (e.g. box) that products are packaged in.
        <?php else: ?>
            Edit a shipping container (e.g. box) that products are packaged in.
        <?php endif ?>
    </div>
    <form method="post">
        <?=get_token_field()?>
        <table class="field">
            <tr>
                <td><label for="name">Name:</label></td>
                <td>
                    <input type="text" id="name" name="name" size="50" maxlength="100" required>
                </td>
            </tr>
            <tr>
                <td><label for="enabled">Enable:</label></td>
                <td>
                    <input type="checkbox" id="enabled" name="enabled" value="1" class="checkbox">
                </td>
            </tr>
            <tr>
                <td><label for="length">Dimensions:</label></td>
                <td>

                    <label for="length">L:</label>

                    <input
                        type="number"
                        step="any"
                        id="length"
                        name="length"
                        placeholder="Length"
                        required
                        style="width: 90px"> &nbsp;

                    <label for="width">W:</label>

                    <input
                        type="number"
                        step="any"
                        id="width"
                        name="width"
                        placeholder="Width"
                        required
                        style="width: 90px"> &nbsp;
                    
                    <label for="height">H:</label>
                    
                    <input
                        type="number"
                        step="any"
                        id="height"
                        name="height"
                        placeholder="Height"
                        required
                        style="width: 90px"> &nbsp;

                    inches

                </td>
            </tr>
            <tr>
                <td><label for="weight">Weight:</label></td>
                <td>

                    <input
                        type="number"
                        step="any"
                        id="weight"
                        name="weight"
                        placeholder="when empty"
                        style="width: 90px">&nbsp;

                    pounds

                </td>
            </tr>
            <tr>
                <td><label for="cost">Cost:</label></td>
                <td>
                    <?=BASE_CURRENCY_SYMBOL?>
                    <input
                        type="number"
                        step="any"
                        id="cost"
                        name="cost"
                        style="width: 70px">
                </td>
            </tr>
        </table>
        <div class="buttons">
            <input type="submit" name="submit_button" value="<?php if ($screen == 'create'): ?>Create<?php else: ?>Save<?php endif ?>" class="submit-primary">&nbsp;&nbsp;
            <input type="button" name="cancel" value="Cancel" onclick="javascript:history.go(-1);" class="submit-secondary">
            <?php if ($screen == 'edit'): ?>
                &nbsp;&nbsp;<input type="submit" name="delete" value="Delete" class="delete" onclick="return confirm('WARNING: This container will be permanently deleted.')">
            <?php endif ?>
        </div>
    </form>
</div>