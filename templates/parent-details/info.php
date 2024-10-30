<?php
$request = isset($_COOKIE['req']) && !empty($_COOKIE['req'])
    ? json_decode(base64_decode($_COOKIE['req']), true)
    : '';
if (!empty($request)) {
    $fname = $request['fname'];
    $lname = $request['lname'];
    $phone = $request['phone'];
    $mobile = $request['mobile'];
    $subscr = $request['subscriprion'];
    $address = $request['address'];
    $postcode = $request['postcode'];
} else {
    $fname = isset($parent_meta) ? $parent_meta['first_name'][0] : '';
    $lname = isset($parent_meta) ? $parent_meta['last_name'][0] : '';
    $phone = isset($parent_extra) ? $parent_extra->phone : '';
    $mobile = isset($parent_extra) ? $parent_extra->mobile : '';
    $address = isset($parent_extra) ? $parent_extra->address : '';
    $postcode = isset($parent_extra) ? $parent_extra->postcode : '';
    $subscr = isset($parent_extra) && $parent_extra->subscription;
}
?>
<form method="post" action="">
    <div class="form-group">
        <p class="form-row form-row form-row-first">
            <label>First Name</label>
            <input type="text" name="fname" value="<?=$fname?>" placeholder="Enter your first name" />
        </p>

        <p class="form-row form-row form-row-last">
            <label>Last Name</label>
            <input type="text" name="lname" value="<?=$lname?>" placeholder="Enter your last name" />
        </p>

    </div>

    <div class="clear"></div>

    <div class="form-group address-group">
        <label>Address</label>

        <?php if (isset($parent_meta) && $parent_meta['billing_address_1'][0] > '') :
            $parent = new CMW_ParentInfo();
            ?>
        <label class="use_billing_address">
            <input type="checkbox" name="same_as_booking" class="same_as_booking"
            <?= $parent->get_billing_address()['address'] == $address ? 'checked' : ''?>
            /> Use billing address
        </label>
        <?php endif; ?>
        <input type="text" name="address" value="<?=$address?>" class="address" placeholder="Type in your house/apartment number and street address here" />
        <span class="address-loader"></span>
        <div class="addressSearchSuggestions"></div>

        <input type="text" name="postcode" value="<?=$postcode?>" class="postcode" placeholder="Enter your postcode" />
    </div>

    <div class="form-group">
        <label>Tel. Home</label>
        <input type="text" id="phone" name="phone" value="<?=$phone?>" min="0" placeholder="0XX XXXX XXXX" />
    </div>

    <div class="form-group">
        <label>Mobile</label>
        <input type="text" id="mobile" name="mobile" value="<?=$mobile?>" min="0" placeholder="0XXXX-XXX-XXX" />
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="text" name="account_email" value="<?=isset($parent_info) ? $parent_info->user_email : ''?>" min="0" />
    </div>
<!---->
<!--    <div class="form-group">-->
<!--        <label>-->
<!--            Nobody likes spam and we certainly won’t spam you. Please let us know if you don’t want to be added to our mailing list. We share information about art, community events and classes.-->
<!--            <input type="checkbox" name="subscription" --><?//=$subscr ? 'checked' : ''?><!--/>-->
<!--        </label>-->
<!--    </div>-->

    <div class="clear"></div>
    <hr>

<!--    <fieldset>-->
<!--        <legend>--><?php //_e( 'Password Change', 'woocommerce' ); ?><!--</legend>-->
<!---->
<!--        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">-->
<!--            <label for="password_current">--><?php //_e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?><!--</label>-->
<!--            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" />-->
<!--        </p>-->
<!--        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">-->
<!--            <label for="password_1">--><?php //_e( 'New Password (leave blank to leave unchanged)', 'woocommerce' ); ?><!--</label>-->
<!--            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" />-->
<!--        </p>-->
<!--        <p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">-->
<!--            <label for="password_2">--><?php //_e( 'Confirm New Password', 'woocommerce' ); ?><!--</label>-->
<!--            <input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" />-->
<!--        </p>-->
<!--    </fieldset>-->

    <p><?php wp_nonce_field( 'save_account_details' ); ?></p>
    <input type="hidden" name="user_id" value="<?=isset($parent_info) ? $parent_info->ID : ''?>"/>
    <input type="hidden" name="route_name" value="parent_info"/>
    <input type="submit" value="Save" name="save" />
</form>
