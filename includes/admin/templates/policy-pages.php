<div>

    <form method="post" action="options.php">

        <?php settings_fields( 'cmw-gdpr-policy-url-settings-group' ); ?>
        <?php do_settings_sections( 'cmw-gdpr-policy-url-settings-group' ); ?>

        <table style="width: 70%; margin-top: 20px;" class="form-table">
            <tbody>
            <tr valign="top">
                <td scope="row" class="titledesc" style="width: 30%;">
                    <label for="cmw_terms_and_conditions">Website usage terms and conditions</label>
                </td>
                <td>
                    <input type="text" name="cmw_terms_and_conditions" value="<?=get_option('cmw_terms_and_conditions')?>" placeholder="enter page URL" class="regular-text code" style="width: 100%;" />
                </td>
            </tr>
            <tr valign="top">
                <td scope="row" class="titledesc">
                    <label for="cmw_booking_terms">Bookings & Purchase terms and conditions</label>
                </td>
                <td>
                    <input type="text" name="cmw_booking_terms" value="<?=get_option('cmw_booking_terms')?>" placeholder="enter page URL" class="regular-text code" style="width: 100%;" />
                </td>
            </tr>
            <tr valign="top">
                <td scope="row" class="titledesc">
                    <label for="cmw_privacy_policy">Privacy Policy</label>
                </td>
                <td>
                    <input type="text" name="cmw_privacy_policy" value="<?=get_option('cmw_privacy_policy')?>" placeholder="enter page URL" class="regular-text code" style="width: 100%;" />
                </td>
            </tr>
            </tbody>
        </table>

        <?php submit_button(); ?>

    </form>

</div>

<div style="font-style: italic; position: absolute; bottom: 40px;">
    Created by Arqino Digital Limited - <a href="http://www.arqino.com" target="_blank">www.arqino.com</a>
</div>
