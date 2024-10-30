<div class="row">
    <div class="form-group col-lg-12">
        <label>Contact Name</label>
        <input type="text" name="name_emergency" class="blue-input required" value="<?= get_user_meta(get_current_user_id(), 'emergency_contact_name', true) ?>"/>
    </div>
</div>


<div class="row">
    <div class="form-group col-lg-12">
        <label>Relationship</label>
        <input type="text" name="relationship" class="pink-input required" value="<?= get_user_meta(get_current_user_id(), 'emergency_relationship', true) ?>"/>
    </div>
</div>


<div class="row">
    <div class="form-group col-lg-6">
        <label>Home Telephone</label>
        <input type="text" name="phone" class="pink-input required" id="phone" value="<?= get_user_meta(get_current_user_id(), 'emergency_phone', true) ?>"
               placeholder="0XX XXXX XXXX"/>
    </div>
    <div class="form-group col-lg-6">
        <label>Mobile</label>
        <input type="text" name="mobile" id="mobile" class="pink-input required" value="<?= get_user_meta(get_current_user_id(), 'emergency_mobile', true) ?>"
               placeholder="0XXXX-XXX-XXX"/>
    </div>

</div>

<div class="row">
    <div class="form-group col-lg-12">
        <label>Medical notes</label>
        <textarea name="medical_notes" id="medical_notes" class="blue-input" rows="5" cols="80" placeholder="Please let us know if there is anything we should be aware of in case of an emergency like an existing medical condition, dependency on medication or necessary steps to take."><?= get_user_meta(get_current_user_id(), 'emergency_medical_notes', true) ?></textarea>
    </div>
</div>

<div class="row">

    <a class="btn btn-pink prev-btn" data-goto="1">Back</a>
    <a class="btn btn-blue pull-right next-btn" data-goto="3">Next</a>
</div>
