/*
 * Client-side validation for the FAQ inquiry form.
 * The form uses the HTML5 attributes (required, type, pattern, maxlength) but
 * has novalidate so we can show messages under each field instead of bubbles.
 * The server validation in PageController@ask remains authoritative.
 */
(function () {
    'use strict';

    var form = document.getElementById('ask-form');
    if (!form) {
        return;
    }

    var names = ['name', 'email', 'phone', 'subject', 'message'];
    var controls = [];

    names.forEach(function (name) {
        var field = form.querySelector('[name="' + name + '"]');
        if (field) {
            controls.push(field);
        }
    });

    function setError(field, message) {
        var slot = document.getElementById('faq-' + field.name + '-error');
        if (slot) {
            slot.textContent = message;
        }
        field.classList.toggle('is-invalid', Boolean(message));
        if (message) {
            field.setAttribute('aria-invalid', 'true');
        } else {
            field.setAttribute('aria-invalid', 'false');
        }
    }

    function messageFor(field) {
        var value = field.value.trim();
        var min = parseInt(field.getAttribute('minlength') || '0', 10);
        var max = parseInt(field.getAttribute('maxlength') || '0', 10);

        if (field.required && value === '') {
            return field.dataset.errorRequired || 'Trường này là bắt buộc.';
        }
        if (min > 0 && value.length < min) {
            return field.dataset.errorMinlength || 'Giá trị này quá ngắn.';
        }
        if (max > 0 && value.length > max) {
            return field.dataset.errorMaxlength || 'Giá trị này quá dài.';
        }
        if (field.type === 'email' && value !== '' && field.validity.typeMismatch) {
            return field.dataset.errorEmail || 'Vui lòng nhập địa chỉ email hợp lệ.';
        }
        if (field.name === 'phone' && value !== '' && field.validity.patternMismatch) {
            return field.dataset.errorPattern || 'Vui lòng nhập số điện thoại hợp lệ.';
        }

        return '';
    }

    function validate(field) {
        var message = messageFor(field);
        setError(field, message);
        return message === '';
    }

    controls.forEach(function (field) {
        field.addEventListener('blur', function () {
            validate(field);
        });
        field.addEventListener('input', function () {
            // Clear a visible error as soon as the user fixes the value.
            if (field.classList.contains('is-invalid')) {
                validate(field);
            }
        });
    });

    form.addEventListener('submit', function (event) {
        var firstInvalid = null;

        controls.forEach(function (field) {
            if (!validate(field) && !firstInvalid) {
                firstInvalid = field;
            }
        });

        if (firstInvalid) {
            event.preventDefault();
            firstInvalid.focus();
        }
    });
}());
