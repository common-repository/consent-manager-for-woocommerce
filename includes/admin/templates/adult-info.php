<div class="row">
    <div class="child_info" style="width: 45%; float: left; margin-right: 25px;">
        <h3><?=get_user_meta($adult_info->user_id, 'first_name', true) ?> <?=get_user_meta($adult_info->user_id, 'last_name', true)?>, <?=$adult_info->years?> years</h3>
        <p><?=$adult_info->address?></p>
        <p><strong>D.O.B:</strong> <?=date("d.m.Y", strtotime($adult_info->dob))?></p>
        <p><strong>Phone:</strong> <?=$adult_info->phone?></p>
        <p><strong>Mobile:</strong> <?=$adult_info->mobile?></p>

    </div>
    <div style="display: inline-block; width: 45%">
        <h3>Emergency Info</h3>

        <p><strong>Contact name:</strong> <?=get_user_meta($adult_info->user_id, 'emergency_contact_name', true);?></p>
        <p><strong>Relationship:</strong> <?=get_user_meta($adult_info->user_id, 'emergency_relationship', true);?></p>
        <p><strong>Home phone:</strong> <?=get_user_meta($adult_info->user_id, 'emergency_phone', true);?></p>
        <p><strong>Mobile phone:</strong> <?=get_user_meta($adult_info->user_id, 'emergency_mobile', true);?></p>
        <p><strong>Medical notes:</strong> <?=get_user_meta($adult_info->user_id, 'emergency_medical_notes', true);?></p>

        <hr/>

        <h3>Consent</h3>

        <p><strong>Medical agree:</strong>
            <input type="checkbox" <?=$adult_info->medical_agree ? 'checked' : ''?> disabled />
        </p>
        <p><strong>Share info agree:</strong>
            <input type="checkbox" <?=$adult_info->share_agree ? 'checked' : ''?> disabled />
        </p>
    </div>
</div>
