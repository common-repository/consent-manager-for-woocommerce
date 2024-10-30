"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Validate = function () {
    function Validate() {
        _classCallCheck(this, Validate);
    }

    _createClass(Validate, [{
        key: "requiredVal",
        value: function requiredVal(val) {

            return val;
        }
    }, {
        key: "MaxLength",
        value: function MaxLength(val, length) {

            if (typeof val == 'string') {
                return val.length <= length;
            } else {
                return false;
            }
        }
    }, {
        key: "isNumeric",
        value: function isNumeric(val) {

            val = parseInt(val);

            if (isNaN(val)) {
                return false;
            }
            return typeof val == "number";
        }
    }, {
        key: "isPhone",
        value: function isPhone(val) {

            val = this.clearPhone(val);

            if (typeof parseInt(val) != "number") return false;

            if (val.search('0') != 0) return false;

            if (val.length > 11 || val.length < 8) return false;

            return true;
        }
    }, {
        key: "isMobile",
        value: function isMobile(val) {
            val = this.clearPhone(val);

            if (typeof parseInt(val) != "number") return false;

            if (val.search('0') != 0) return false;

            if (val.length != 11) return false;

            return true;
        }
    }, {
        key: "clearPhone",
        value: function clearPhone(val) {
            return val.replace(/[.?*+^$[\]\\(){}|-]/g, "").replace(/ /g, "");
        }
    }, {
        key: "checkDateOfBirth",
        value: function checkDateOfBirth(val) {

            var date = val.split('.');
            var valDate = new Date(date[2], date[1] - 1, date[0]);
            var now = new Date();

            return valDate.getTime() < now.getTime();
        }
    }]);

    return Validate;
}();
'use strict';

(function ($) {

    var ajaxUrl = CMW_variables.admin_url + 'admin-ajax.php?action=getDoctorInfo';
    var addressField = $('.address-group .address');
    var doctorAddressField = $('.address-group .doctor_address');
    var addressLoader = $('.address-loader');

    $('#doctor_name').autocomplete({
        appendTo: '#doctorSearchSuggestions',
        source: function source(req, response) {
            $.getJSON(ajaxUrl, req, response);
        },
        select: function select(event, ui) {

            $('input[name="doctor_id"]').val(ui.item.id);
            $('input[name="doctor_name"]').val(ui.item.doctor_name);
            $('input[name="doctor_address"]').val(ui.item.doctor_address);
            $('input[name="doctor_postcode"]').val(ui.item.doctor_postcode);
            $('input[name="doctor_phone"]').val(ui.item.doctor_phone);

            return false;
        },
        maxHeight: '',
        width: '60%',
        minLength: 3,
        html: true
    });


})(jQuery);
'use strict';

(function ($) {

    var childrenDiv = $('.children_info');
    var adultsDiv = $('.adults_info');
    var usersChildrenCount = 0;
    var childRowsArray;
    var additionalInfo = $('.woocommerce-shipping-fields');
    var createAccountField = $('.create-account');
    var maxCount = parseInt($('.variation-Persons').text().split(':')[1]) || parseInt($('.product-quantity').text().split(': ')[1]);
    var childrenInput = $('input[name="children_count"]');
    var adultsInput = $('input[name="adults_count"]');
    var countButton = $('#next_step');
    var adultsCountButton = $('#adult_next_step');

    $('.woocommerce-billing-fields').append('<div class="billing_address"><h3>Billing Address</h3></div>');
    var billingAddress = $('.billing_address');
    placeAddressFields();

    var adultsAndChildrenDiv = $('.adults_and_children');
    var adultsCountDiv = $('.adults_counter_input');

    if (adultsAndChildrenDiv.html() != undefined) {

        childrenDiv.hide();
        additionalInfo.hide();
        createAccountField.hide();
        billingAddress.hide();

        childrenInput.attr('min', 0);
        childrenInput.attr('max', maxCount);

        countButton.on('click', rewriteChildreInput);
        childrenInput.on('change', rewriteChildreInput);


        adultsCountButton.on('click', rewriteAdultsInput);
        adultsInput.on('change', rewriteAdultsInput)


    } else {
        countButton.on('click', function () {
            countButton.remove();
            childrenDiv.show();
            additionalInfo.show();
            createAccountField.show();
            billingAddress.show();
        });
    }

    $(document).on('click', '.number-spinner a',  function (e) {
        e.preventDefault();
        var btn = $(this),
            oldValue = btn.closest('.number-spinner').find('input').val().trim(),
            newVal = 0,
            inp = btn.closest('.number-spinner').find('input');

        if (btn.attr('data-dir') == 'up') {
            newVal = parseInt(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                newVal = parseInt(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }

        inp.val(newVal);

        rewriteChildreInput();

        rewriteAdultsInput();
    });


    function rewriteChildreInput() {

        changeMaxValues();

        if (!checkCount()) return false;

        var children_count = parseInt(childrenInput.val());
        var childRow = childrenDiv.find('.child-row');

        if (childRow.length) {

            if (childRow.length > children_count) {
                var max_rows = children_count - 1;

                childRow.each(function(index, el) {
                    if (index > max_rows) {
                        el.remove();
                    }
                });
            } else if (childRow.length < children_count) {

                var rows_needed = children_count - childRow.length;

                if (usersChildrenCount) {

                    if (usersChildrenCount >= children_count) {

                        cmw_draw_children_select(childRowsArray, rows_needed);

                    } else {

                        cmw_draw_add_child_form(rows_needed);
                    }

                } else {
                    cmw_draw_add_child_form(rows_needed);
                }



            }

        } else {
            $.ajax({
                url: CMW_variables.admin_url + 'admin-ajax.php?action=getChildrenFieldsForCheckout',
                type: 'POST',
                dataType: 'JSON',
                data: { children_count : children_count },
                success: function (data) {
                    if (data.child_rows.length) {

                        usersChildrenCount = data.child_rows.length;
                        childRowsArray = data.child_rows;

                        if (data.child_rows.length >= children_count) {

                            cmw_draw_children_select(data.child_rows, children_count);

                        } else {
                            children_count = children_count - data.child_rows.length;

                            cmw_draw_children_select(data.child_rows, data.child_rows.length);
                            cmw_draw_add_child_form(children_count);
                        }


                    } else { // if parent didn't add any child draw a simple form for it

                        cmw_draw_add_child_form(children_count);
                    }
                }
            });
        }


        countButton.remove();
        childrenDiv.show();
        //additionalInfo.show();
        //createAccountField.show();
        //billingAddress.show();
    }


    function rewriteAdultsInput()
    {
        if (!checkCount()) return false;

        var adults_count = parseInt(adultsInput.val());
        var adultRow = adultsDiv.find('.adult-row');

        if (adultRow.length) {

            if (adultRow.length > adults_count) {

                var max_rows = adults_count - 1;

                adultRow.each(function(index, el) {
                    if (index > max_rows) {
                        el.remove();
                    }
                });

            } else if (adultRow.length < adults_count) {

                var rows_needed = adults_count - adultRow.length;

                cmw_draw_add_adult_form(rows_needed);
            }

        } else {

            cmw_draw_add_adult_form(adults_count);

        }


        adultsCountButton.remove();
        adultsDiv.show();
        additionalInfo.show();
        createAccountField.show();
        billingAddress.show();
    }


    function cmw_draw_children_select(children, count) {

        var childRow;
        for (var i = 0; i < count; i++) {

            childRow = '<div class="child-row">';

            childRow += '<p class="form-row my-field-class form-row-wide validate-required" id="choose_child[]_field">' +
                '<label for="choose_child[]" class="">Choose child (only children with completed emergency and consent details are shown) <abbr class="required" title="required">*</abbr></label>' +
                '<select name="choose_child[]" id="choose_child[]" class="select">';


            children.forEach(function(el) {

                childRow += '<option value="' + el.child_id + '">' + el.name + '</option>'
            });

            childRow += '</select></p>';

            childRow += '</div>';


            childrenDiv.append(childRow);
        }
    }

    //function draw_adult_select(adults, count) {
    //
    //    var adultRow;
    //    for (var i = 0; i < count; i++) {
    //
    //        adultRow = '<div class="adult-row">';
    //
    //        adultRow += '<p class="form-row my-field-class form-row-wide validate-required" id="choose_adult[]_field">' +
    //            '<label for="choose_adult[]" class="">Choose adult (only adults with completed emergency and consent details are shown) <abbr class="required" title="required">*</abbr></label>' +
    //            '<select name="choose_adult[]" id="choose_adult[]" class="select">';
    //
    //
    //        adults.forEach(function(el) {
    //
    //            adultRow += '<option value="' + el.adult_id + '">' + el.name + ' (' + el.email + ')</option>'
    //        });
    //
    //        adultRow += '</select></p>';
    //
    //        adultRow += '</div>';
    //
    //
    //        adultsDiv.append(adultRow);
    //    }
    //}

    function cmw_draw_add_child_form(count)
    {
        var childRow;
        for (var i = 0; i < count; i++) {

            childRow = '<div class="child-row">';
            childRow += '<p class="form-row my-field-class form-row-wide validate-required" id="add_child_name[]_field">' +             '<label for="add_child_name[]" class="">Child name <abbr class="required" title="required">*</abbr></label>' +               '<input type="text" class="input-text" name="add_child_name[]" id="add_child_name[]" value="">' +
                '</p>' +
                '<p class="form-row validate-required" id="datepicker_' + i + '_field">' +
                '<label for="datepicker_' + i + '" class="">Date of birth <abbr class="required" title="required">*</abbr></label>' +
                '<input type="date" class="input-text my-field-class form-row-wide" name="add_child_years[]" id="datepicker_' + i + '" placeholder="DD/MM/YYYY" value="">'+
                '</p>';

            childRow += '</div>';

            childrenDiv.append(childRow);

        }
    }

    function cmw_draw_add_adult_form(count)
    {
        var adultRow;
        for (var i = 0; i < count; i++) {

            adultRow = '<div class="adult-row">';
            adultRow += '<p class="form-row my-field-class form-row-first validate-required" id="add_adult_f_name[]_field">' +
                '<label for="add_adult_f_name[]" class="">First name <abbr class="required" title="required">*</abbr></label>' +
                '<input type="text" class="input-text" name="add_adult_f_name[]" id="add_adult_f_name[]" value="">' +
                '</p>' +
                '<p class="form-row my-field-class form-row-last validate-required" id="add_adult_l_name[]_field">' +
                '<label for="add_adult_l_name[]" class="">Last name <abbr class="required" title="required">*</abbr></label>' +
                '<input type="text" class="input-text" name="add_adult_l_name[]" id="add_adult_l_name[]" value="">' +
                '</p>' +
                '<p class="form-row my-field-class form-row-wide validate-required" id="add_adult_email[]_field">' +
                '<label for="add_adult_email[]" class="">Email <abbr class="required" title="required">*</abbr></label>' +
                '<input type="email" class="input-text" name="add_adult_email[]" id="add_adult_email[]" value="">' +
                '</p>';

            adultRow += '</div>';

            adultsDiv.append(adultRow);

        }
    }

    function checkCount() {

        var children_count = parseInt(childrenInput.val());
        var adult_count = parseInt(adultsInput.val());

        $('span.error').remove();

        if (childrenInput.val() == '') {
            childrenInput.focus();
            return false;
        }

        if (maxCount < children_count) {
            adultsAndChildrenDiv.after('<span class="error">Too many children! ' + maxCount + ' is allowed</span>');

            return false;
        }

        if (maxCount < adult_count) {
            adultsCountDiv.after('<span class="error">Too many adults! ' + maxCount + ' is allowed</span>');
        }

        return true;
    }

    function changeMaxValues() {

        var children_count = parseInt(childrenInput.val()) ? parseInt(childrenInput.val()) : 0;
        var adult_count = parseInt(adultsInput.val()) ? parseInt(adultsInput.val()) : 0;

        childrenInput.attr('max', maxCount - adult_count);
        adultsInput.attr('max', maxCount - children_count);
    }

    function placeAddressFields() {
        var addressFields = $('.address-field');
        billingAddress.append(addressFields);
        billingAddress.append('<div class="clear"></div>');
    }
})(jQuery);
'use strict';

(function ($) {

    $('#add-photo-modal form').on('submit', function (e) {
        e.preventDefault();
        $(this).find('button').append('<span class="address-loader"></span>');
        $.ajax({
            url: CMW_variables.admin_url + "admin-ajax.php?action=uploadPhoto",
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function success() {
                window.location.reload();
            },
            error: function error() {}
        });
    });

    $('.add-photo').on('click', function () {
        $('input[name="child_photo"]').click();
        $('input[name="adult_photo"]').click();
    });

    $('input[name="child_photo"], input[name="adult_photo"]').on('change', function() {
        readURL(this);
    });

    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {

                $('.img-container.add-photo').html('<div class="child_photo" style="background-image: url(' + e.target.result + ');"> </div>');

            };

            reader.readAsDataURL(input.files[0]);
        }

    }

    var childrenRow = $('.children-row');
    var childrenCount = $('.children-row .child-icon').length;
    var rowWidth = childrenCount * 155 + childrenCount * 10;

    childrenRow.css('width', rowWidth + 'px');

    //$('.delete-form').on('submit', function(e) {
    //    e.preventDefault();
    //
    //    var childName = $(this).closest('.main-info').find('tbody tr td:eq(0)').text();
    //
    //    return confirm('Are you sure you want to delete the information about ' + childName);
    //});

})(jQuery);
'use strict';

(function ($) {

    $(document).ready(function () {

        var date = new Date();

        $('#dob').datepicker({
            dateFormat: 'dd.mm.yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "1990:" + date.getFullYear()
        });

        $('.datepicker').each(function () {
            $('#' + $(this).attr('id')).datepicker({
                dateFormat: 'dd.mm.yy',
                changeMonth: true,
                changeYear: true,
                yearRange: "1990:" + date.getFullYear()
            });
        });
    });
})(jQuery);
'use strict';

(function ($) {

    var sameAsBilling = document.querySelector('.same_as_booking');
    var address = document.querySelector('input[name="address"]');
    var postcode = document.querySelector('input[name="postcode"]');

    if (sameAsBilling) sameAsBilling.addEventListener('click', setBillingAddress);

    function setBillingAddress() {

        if (this.checked) {
            $.getJSON(CMW_variables.admin_url + 'admin-ajax.php?action=getBookingAddress', function (data) {

                address.value = data.address;
                postcode.value = data.postcode;
            });
        } else {
            address.value = '';
            postcode.value = '';
        }
    }
})(jQuery);
'use strict';

(function ($) {

    var gender = document.querySelectorAll(".gender");
    var genderRadio = document.querySelectorAll('input[name="gender"]');

    if (gender) {
        gender.forEach(function (el) {
            el.addEventListener('click', setGender);
        });
    }

    function setGender() {
        gender.forEach(function (el) {
            el.classList.remove('active');
        });
        genderRadio.forEach(function (el) {
            el.removeAttribute('checked');
        });

        this.classList.add('active');
        this.parentNode.children[2].children[0].setAttribute('checked', '');
    }
})(jQuery);
'use strict';

(function ($) {

    $(document).ready(function () {

        $('#phone, input[name="adult_phone"]').mask('099 9999 9?999');

        $('#mobile, input[name="doctor_phone"], input[name="adult_mobile"]').mask('0 9999-999-999');

        $('#dob').mask('99.99.9999');
    });

    function validateInput(evt) {
        var theEvent = evt || window.event;
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode( key );
        var regex = /[0-9]|\./;
        if( !regex.test(key) ) {
            theEvent.returnValue = false;
            if(theEvent.preventDefault) theEvent.preventDefault();
        }
    }

    $('input[name="children_count"], input[name="adults_count"]').on('keypress', validateInput);


})(jQuery);
'use strict';

(function ($) {

    var currentStep = 1;
    var nextBtn = document.querySelectorAll('.next-btn');
    var prevBtn = document.querySelectorAll('.prev-btn');
    var steps = document.querySelectorAll('.bs-wizard-step');
    var stepsContainers = document.querySelectorAll('.step');

    if (nextBtn) {
        nextBtn.forEach(function (el) {
            el.addEventListener('click', goToNextStep);
        });
    }

    if (prevBtn) {
        prevBtn.forEach(function (el) {
            el.addEventListener('click', gotToPrevStep);
        });
    }

    if (stepsContainers.length) {
        hideSteps();
        showCurrentStepContainer();
    }


    function goToNextStep() {

        $('html, body').scrollTop(0);

        var nextStep = this.dataset.goto;
        removeErrors();

        if (validateStep(currentStep)) {

            removeErrors();

            currentStep = nextStep;

            setStepsClass();
            hideSteps();
            showCurrentStepContainer();
        }

        return false;
    }

    function removeErrors() {

        var errors = document.querySelectorAll('.error-text');
        if (errors) {
            errors.forEach(function (error) {
                error.remove();
            });
        }
    }

    function gotToPrevStep() {

        $('html, body').scrollTop(0);

        var prevStep = this.dataset.goto;

        currentStep = prevStep;

        setStepsClass();
        hideSteps();
        showCurrentStepContainer();
    }

    function setStepsClass() {
        steps.forEach(function (step, key) {
            if (key < currentStep - 1) {
                step.classList.remove('active');
                step.classList.add('complete');
            } else if (key > currentStep - 1) {
                step.classList.remove('active');
                step.classList.remove('complete');
                step.classList.add('disabled');
            } else if (key == currentStep - 1) {
                step.classList.remove('complete');
                step.classList.remove('disabled');
                step.classList.add('active');
            }
        });
    }

    function validateStep(stepNum) {

        switch (stepNum) {
            case 1:
                return validateInfo();
                break;
            case "2":
                return validateEmergency();
                break;
            default:
                return true;
                break;
        }
    }

    function addErrorMessage(inputName, message) {

        var parentDiv;

        if (inputName == "gender") {
            parentDiv = document.querySelector('input[name="' + inputName + '"]').parentNode.parentNode.parentNode;
        } else if (inputName == "relationship") {
            if (window.location.pathname == '/my-account/adult-details/') {
                parentDiv = document.querySelector('input[name="' + inputName + '"]').parentNode;
            } else {
                parentDiv = document.querySelector('select[name="' + inputName + '"]').parentNode;
            }
        } else {
            parentDiv = document.querySelector('input[name="' + inputName + '"]').parentNode;
        }

        if (parentDiv.classList.contains('minimal-form-input')) { // for Salient theme
            parentDiv = parentDiv.parentNode;
        }

        var error = document.createElement('p');
        error.classList.add('text-danger');
        error.classList.add('error-text');
        error.innerText = message;

        parentDiv.appendChild(error);
    }

    function validateInfo() {

        var rules;
        var info;
        var flag = true;
        var gender = document.querySelectorAll('.gender');
        var active_counter = 0;

        if (document.querySelector('input[name="adult-info"]')) { // if adult details page

            rules = {
                name: 'required|max:255',
                dob: 'required|validDate',
                adult_phone: 'required',
                adult_mobile: 'required',
                adult_email: 'required|max:128',
                address: 'required'
            };
            info = {
                name: document.querySelector('input[name="name"]').value,
                dob: document.querySelector('input[name="dob"]').value,
                adult_phone: document.querySelector('input[name="adult_phone"]').value,
                adult_mobile: document.querySelector('input[name="adult_mobile"]').value,
                adult_email: document.querySelector('input[name="adult_email"]').value,
                address: document.querySelector('input[name="address"]').value
            };
        } else {  // child details page

            rules = {
                name: 'required|max:255',
                dob: 'required|validDate',
                school: 'required'
            };
            info = {
                name: document.querySelector('input[name="name"]').value,
                dob: document.querySelector('input[name="dob"]').value,
                school: document.querySelector('input[name="school"]').value
            };
        }

        gender.forEach(function (el) {
            if (el.className.indexOf('active') > -1) {
                active_counter++;
            }
        });

        if (active_counter == 0) {
            addErrorMessage('gender', 'Field gender is required');
            flag = false;
        }

        Object.keys(rules).map(function (objectKey, index) {
            var value = rules[objectKey];
            value = value.split("|");

            value.forEach(function (el) {

                if (el.search("max") == 0) {
                    if (!Validate.prototype.MaxLength(info[objectKey], el.replace("max:", ""))) {
                        var maxLength = el.replace("max:", "");
                        addErrorMessage(objectKey, 'Field ' + objectKey + ' must be ' + maxLength + ' characters length');
                        flag = false;
                    }
                }

                switch (el) {
                    case 'required':
                        if (!Validate.prototype.requiredVal(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' is required');
                            flag = false;
                        }

                        break;
                    case 'numeric':

                        if (!Validate.prototype.isNumeric(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must contain numeric value');
                            flag = false;
                        }

                        break;

                    case 'validDate':

                        if (!Validate.prototype.checkDateOfBirth(info[objectKey])) {
                            var date = new Date();
                            var nowDate = date.getDate() + '.' + (date.getMonth() + 1) + '.' + date.getFullYear();
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must be not more than ' + nowDate);
                            flag = false;
                        }

                        break;
                    case 'phone':
                        if (!Validate.prototype.isPhone(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must be formatted like 0XX XXXX XXXX');
                            flag = false;
                        }
                        break;
                    case 'mobile':
                        if (!Validate.prototype.isMobile(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must be formatted like 0XXXX-XXX-XXX');
                            flag = false;
                        }
                        break;
                }
            });
        });

        return flag;
    }

    function validateEmergency() {

        var rules;
        var info;
        var flag = true;

        if (document.querySelector('input[name="adult-info"]')) { // if adults details form

            rules = {
                name_emergency: 'required|max:128',
                relationship: 'required|max:28',
                phone: 'required',
                mobile: 'required'
            };

            info = {
                name_emergency: document.querySelector('input[name="name_emergency"]').value,
                relationship: document.querySelector('input[name="relationship"]').value,
                phone: document.querySelector('input[name="phone"]').value,
                mobile: document.querySelector('input[name="mobile"]').value
            };

        } else { // if child details form
            rules = {
                name_emergency: 'required|max:128',
                relationship: 'required|max:28',
                phone: 'required',
                mobile: 'required',
                doctor_phone: 'required',
                allergies_details: document.querySelector('input[name="allergies"]').checked ? 'required' : '',
                dosage: document.querySelector('input[name="medications"]').checked ? 'required' : ''
            };

            info = {
                name_emergency: document.querySelector('input[name="name_emergency"]').value,
                relationship: document.querySelector('select[name="relationship"]').value,
                phone: document.querySelector('input[name="phone"]').value,
                mobile: document.querySelector('input[name="mobile"]').value,
                doctor_phone: document.querySelector('input[name="doctor_phone"]').value,
                allergies_details: document.querySelector('input[name="allergies_details"]').value,
                dosage: document.querySelector('input[name="dosage"]').value

            };
        }

        Object.keys(rules).map(function (objectKey, index) {
            var value = rules[objectKey];
            value = value.split("|");

            value.forEach(function (el) {

                if (el.search("max") == 0) {
                    if (!Validate.prototype.MaxLength(info[objectKey], el.replace("max:", ""))) {
                        var maxLength = el.replace("max:", "");
                        addErrorMessage(objectKey, 'Field ' + objectKey + ' must be ' + maxLength + ' characters length');
                        flag = false;
                    }
                }

                switch (el) {
                    case 'required':
                        if (!Validate.prototype.requiredVal(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' is required');
                            flag = false;
                        }

                        break;
                    case 'phone':
                        if (!Validate.prototype.isPhone(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must be formatted like 0XX XXXX XXXX');
                            flag = false;
                        }
                        break;
                    case 'mobile':
                        if (!Validate.prototype.isMobile(info[objectKey])) {
                            addErrorMessage(objectKey, 'Field ' + objectKey + ' must be formatted like 0XXXX-XXX-XXX');
                            flag = false;
                        }
                        break;
                }
            });
        });

        return flag;
    }

    function hideSteps() {

        stepsContainers.forEach(function (el) {
            el.setAttribute('hidden', '');
            el.setAttribute('aria-hidden', 'true');
        });
    }

    function showCurrentStepContainer() {
        stepsContainers[currentStep - 1].removeAttribute('hidden');
        stepsContainers[currentStep - 1].removeAttribute('aria-hidden');
    }


    $('input[name="allergies"]').on('click', function() {

        var allergies_details = $('input[name="allergies_details"]');

        if ($(this).is(':checked')) {
            allergies_details.removeAttr('disabled');
            allergies_details.focus();
        } else {
            allergies_details.attr('disabled', true);
        }
    });

    $('input[name="medications"]').on('click', function() {

        var dosage = $('input[name="dosage"]');

        if ($(this).is(':checked')) {
            dosage.removeAttr('disabled');
            dosage.focus();
        } else {
            dosage.attr('disabled', true);
        }
    });

    $('.child_photo_icon').on('click', function() {
        var child_id = $(this).data('child');
        var parent_id = $(this).data('parent');

        console.log(child_id, parent_id, 'dfgdffdg');

        $('#child_photo_modal img').attr('src', CMW_variables.content_dir + '/uploads/children_photo/' + parent_id + '/' + child_id + '.jpg');
        $('#child_photo_modal').modal('show');
    });

})(jQuery);

(function($) {
    $('.tabs ul li').on('click', function() {
        var tabId = $(this).data('tab');

        $('.tabs ul li').removeClass('active');
        $(this).addClass('active');

        $('.tabs-content .item').removeClass('active');
        $('.tabs-content .item#' + tabId).addClass('active');
    });

    $('#save_consent').on('click', function() {

        var terms = $('input[name="terms"]').is(':checked') ? 1 : 0;
        var policy = $('input[name="policy"]').is(':checked') ? 1 : 0;
        var booking_terms = $('input[name="bookings_terms"]').is(':checked') ? 1 : 0;
        var subscr = $('input[name="subscription"]').map(function() {
            if ($(this).is(':checked')) {
                return $(this).val();
            }
        }).toArray();

        $.ajax({
            url: CMW_variables.admin_url + 'admin-ajax.php?action=saveConsent',
            type: 'post',
            data: {
                terms : terms,
                policy : policy,
                booking_terms: booking_terms,
                subscr : subscr
            },
            success: function() {
                window.location.reload();
            }
        });

    });

    $('#download_personal_data').on('click', function() {
        $.ajax({
            url: CMW_variables.admin_url + 'admin-ajax.php?action=getUserData',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                window.location.href = CMW_variables.content_url + '/uploads/personal_data_' + data.user_login + '.csv';
            },
            error: function() {

            }
        });
    });

    $('#delete_personal_data').on('click', function() {
        if (confirm('Deleting your data will remove all your details and your login from our site. Are you sure you want to proceed?')) {
            // Deleting user

            $.ajax({
                url: CMW_variables.admin_url + 'admin-ajax.php?action=deleteUserData',
                success: function() {
                    window.location.href = CMW_variables.siteurl;
                }
            });
        } else {
            return;
        }
    });
})(jQuery);

(function($) {
    $('.child_photo_icon').on('click', function() {
        var child_id = $(this).data('child');
        var parent_id = $(this).data('parent');
        var src;

        if (parent_id == child_id) { // adult photo
            src = CMW_variables.content_url + '/uploads/adult_photo/' + child_id + '.jpg';
        } else { // child photo
            src = CMW_variables.content_url + '/uploads/children_photo/' + parent_id + '/' + child_id + '.jpg';
        }

        $('#child_photo_modal img').attr('src', src);
        $('#child_photo_modal').modal('show');
    });


    var myTxtArea = document.getElementById('medical_notes');
    if (myTxtArea) {
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }

})(jQuery);
