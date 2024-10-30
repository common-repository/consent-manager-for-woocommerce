<div class="row">
    <div class="form-group col-lg-8 col-xs-12">
        <label>Your name</label>
        <input type="text" name="name" placeholder="Full name" class="blue-input required" value="<?=get_user_meta(get_current_user_id(), 'first_name', true)?> <?=get_user_meta(get_current_user_id(), 'last_name', true)?>"/>
    </div>
    <div class="form-group col-lg-4">
        <label>Date of Birth</label>
        <input type="text" name="dob" id="dob" placeholder="MM.DD.YYYY" class="blue-input required" value="<?=$parent_info->dob ? date("d.m.Y", strtotime($parent_info->dob)) : ''?>"/>
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-xs-12">
        <label>Home phone</label>
        <input type="text" name="adult_phone" placeholder="0XX XXXX XXXX" required class="required" value="<?=$parent_info ? $parent_info->phone : ''?>" />
    </div>
    <div class="form-group col-lg-4 col-xs-12">
        <label>Mobile phone</label>
        <input type="text" name="adult_mobile" placeholder="0XXXX-XXX-XXX" required class="required" value="<?=$parent_info ? $parent_info->mobile : ''?>" />
    </div>
    <div class="form-group col-lg-4 col-xs-12">
        <label>Email</label>
        <input type="email" name="adult_email" required class="required" value="<?=$user_info->user_email?>" />
    </div>
</div>

<div class="row">
    <div class="form-group col-lg-4 col-xs-12" style="min-height: 110px;">
        <label>Photo</label>
        <input type="file" name="adult_photo"/>
        <div class="img-container add-photo">
            <?php if (file_exists(WP_CONTENT_DIR . '/uploads/adult_photo/' . get_current_user_id() . '.jpg')) : ?>
                <div class="child_photo" style="background-image: url(<?= WP_CONTENT_URL ?>/uploads/adult_photo/<?=get_current_user_id()?>.jpg);">
                </div>
            <?php else : ?>
                <div>
                    <img src="<?=WP_PLUGIN_URL?>/consent-manager-for-woocommerce/assets/images/other_icons/faces.png">
                    <p>Add photo</p>
                    <span>+</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php $theme = wp_get_theme(); ?>
    <div class="form-group col-lg-7 col-xs-12">
        <div class="<?=$theme == "Twenty Seventeen" ? 'col-lg-12  col-xs-12' : 'col-lg-6  col-xs-6'?>"">
            <label>Female</label>
            <div class="female gender <?=$parent_info->gender == 'f' ? 'active' : ''?>" data-value="f"></div>
            <label class="radio-label">
                <input type="radio" name="gender" value="f" <?=!$parent_info->gender ? 'checked' : $parent_info->gender == 'f' ? 'checked' : ''?>>
            </label>
        </div>
        <div class="<?=$theme == "Twenty Seventeen" ? 'col-lg-12  col-xs-12' : 'col-lg-6  col-xs-6'?>"">
            <label>Male</label>
            <div class="male gender <?=$parent_info->gender == 'm' ? 'active' : ''?>" data-value="m"></div>
            <label class="radio-label">
                <input type="radio" name="gender" value="m" <?=$parent_info->gender == 'm' ? 'checked' : ''?>>
            </label>
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="form-group col-lg-9 col-xs-12 address-group">
        <label>Address</label>
        <input type="text" name="address" class="address pink-input"
               placeholder="Type in your house/apartment number and street address here"
               value="<?=$parent_info ? $parent_info->address : ''?>"
        />
        <span class="address-loader"></span>
        <div class="addressSearchSuggestions"></div>

        <input type="text" name="postcode" class="postcode pink-input top-border-none"
               placeholder="Enter your postcode"
               value="<?=$parent_info ? $parent_info->postcode : ''?>"
        />
    </div>
</div>
<input type="hidden" name="adult-info"/>


<a class="btn btn-blue pull-right next-btn" data-goto="2">Next</a>
