
<?=$messages?>

<form <?=$attributes?>>

    <div class="row">
        
    <div class="col-sm-6">    
    <div class="form-group">
        <label for="email_address">Email*</label>
        <input type="email" name="email_address" id="email_address">
    </div>
    </div>

    <div class="col-sm-6">
    <div class="form-group">
        <label for="current_password">Current Password*</label>
        <input type="password" name="current_password" id="current_password">
    </div>

    <?php if ($strong_password_help): ?>
        <?=$strong_password_help?>
    <?php endif ?>
    </div>
        
    <div class="col-sm-6">
    <div class="form-group">
        <label for="new_password">New Password*</label>
        <input type="password" name="new_password" id="new_password">
    </div>
    </div>

    <div class="col-sm-6">
    <div class="form-group">
        <label for="new_password_verify">Confirm New Password*</label>
        <input type="password" name="new_password_verify" id="new_password_verify">
    </div>
    </div>

    <?php if (PASSWORD_HINT): ?>
        <div class="col-sm-12">
        <div class="form-group">
            <label for="password_hint">Password Hint (Optional)</label>
            <input type="text" name="password_hint" id="password_hint" placeholder="Enter something to remind you">
        </div>
        </div>
    <?php endif ?>

    <div class="col-sm-12">
    <button type="submit" class="btn btn-primary">Change Password</button>
        
    <?php if ($my_account_url): ?>
        <a href="<?=h($my_account_url)?>" class="btn btn-secondary">Cancel</a>
    <?php endif ?>
    </div>
    
    <!-- Required hidden fields (do not remove) -->
    <?=$system?>
    
    </div>
        
</form>
