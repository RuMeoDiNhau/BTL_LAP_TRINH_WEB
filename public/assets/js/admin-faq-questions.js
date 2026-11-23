/* Admin Customer Questions: delete confirmation dialog (UI only). */
(function () {
    'use strict';

    var dialog = document.getElementById('questionDeleteDialog');
    if (!dialog || typeof dialog.showModal !== 'function') {
        return;
    }

    var form = document.getElementById('questionDeleteForm');
    var idInput = document.getElementById('questionDeleteId');
    var subjectText = dialog.querySelector('[data-question-subject]');
    var cancel = dialog.querySelector('[data-question-dialog-cancel]');
    var closers = dialog.querySelectorAll('[data-question-dialog-close]');

    document.querySelectorAll('[data-question-delete]').forEach(function (button) {
        button.addEventListener('click', function () {
            idInput.value = button.getAttribute('data-question-delete');
            form.action = button.getAttribute('data-question-action') || '#';
            if (subjectText) {
                subjectText.textContent = button.getAttribute('data-question-subject') || '';
            }
            dialog.showModal();
            if (cancel) cancel.focus();
        });
    });

    closers.forEach(function (button) {
        button.addEventListener('click', function () { dialog.close(); });
    });

    // Backdrop click (target === dialog) and Escape both close the native dialog.
    dialog.addEventListener('click', function (e) {
        if (e.target === dialog) dialog.close();
    });
}());
