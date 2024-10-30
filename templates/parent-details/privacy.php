<?php
$subscr = isset($parent_extra) && $parent_extra->subscription ? $parent_extra->subscription : '';

?>
<form method="post" action="">

    <div class="form-group">
        <label>
            Please let us know if you want to be added to our mailing list. We share information about art, community events and upcoming classes.
            <input type="checkbox" name="subscription" <?=$subscr ? 'checked' : ''?>/>
        </label>
    </div>

    <p><?php wp_nonce_field( 'save_account_details' ); ?></p>
    <input type="hidden" name="user_id" value="<?=isset($parent_info) ? $parent_info->ID : ''?>"/>
    <input type="hidden" name="route_name" value="privacy"/>
    <input type="submit" value="Save" name="save" />
</form>
