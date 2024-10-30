<!--<form method="post" action="">-->
    <div class="row">
        <div class="form-group col-lg-12">
            <label>
                <?=get_option('cmw_adult_medical_treatment_text') ? : 'I give my consent for my child to receive emergency medical treatment.'?>
                <input type="checkbox" name="medical_agree" <?=$parent_info && $parent_info->medical_agree ? 'checked' : ''?> />
            </label>
        </div>

        <div class="form-group col-lg-12">
            <label>
                <?=get_option('cmw_adult_photo_consent_text') ? : 'I give my consent for you to use a likeness of me and/or my child to be used on any film, photo or audio recording.' ?>
                <input type="checkbox" name="sharing_agree" <?=$parent_info && $parent_info->share_agree ? 'checked' : ''?>/>
            </label>
        </div>

    </div>

    <input type="hidden" name="user_id" value="<?= $user_info ? $user_info->ID : ''?>"/>
<!--    <input type="hidden" name="route_name" value="agree"/>-->
<!--    <input type="submit" value="Save" name="save" />-->

<div class="row">

    <a class="btn btn-pink prev-btn" data-goto="2">Back</a>
    <button type="submit" class="btn btn-blue pull-right">Save</button>
</div>
<!--</form>-->
