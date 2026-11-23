/* Admin FAQ: delete dialog + form validation (UI only). */
(function () {
    'use strict';

    var dialog = document.getElementById('faqDeleteDialog');
    if (dialog && typeof dialog.showModal === 'function') {
        var form = document.getElementById('faqDeleteForm');
        var idInput = document.getElementById('faqDeleteId');
        var questionText = dialog.querySelector('[data-faq-question]');
        var cancel = dialog.querySelector('[data-faq-dialog-cancel]');
        var closers = dialog.querySelectorAll('[data-faq-dialog-close]');

        document.querySelectorAll('[data-faq-delete]').forEach(function (button) {
            button.addEventListener('click', function () {
                idInput.value = button.getAttribute('data-faq-delete');
                form.action = button.getAttribute('data-faq-action') || '#';
                if (questionText) {
                    questionText.textContent = button.getAttribute('data-faq-question') || '';
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
    }

    var faqForm = document.querySelector('[data-faq-form]');
    if (!faqForm) return;

    function setErr(name, msg) {
        var el = faqForm.querySelector('[data-error="' + name + '"]');
        var input = faqForm.querySelector('[name="' + name + '"]');
        if (el) el.textContent = msg || '';
        if (input) {
            input.classList.toggle('border-red-400', !!msg);
            input.classList.toggle('border-gray-300', !msg);
            input.setAttribute('aria-invalid', msg ? 'true' : 'false');
        }
    }

    var answer = faqForm.querySelector('[name="answer"]');
    var counter = faqForm.querySelector('[data-answer-count]');
    if (answer && counter) {
        var updateCount = function () { counter.textContent = answer.value.length; };
        answer.addEventListener('input', updateCount);
        updateCount();
    }

    var active = faqForm.querySelector('[name="is_active"]');
    var status = faqForm.querySelector('[data-active-status]');
    if (active && status) {
        var syncStatus = function () { status.textContent = active.checked ? 'Active' : 'Hidden'; };
        active.addEventListener('change', syncStatus);
        syncStatus();
    }

    faqForm.addEventListener('submit', function (e) {
        var ok = true;
        var q = faqForm.querySelector('[name="question"]');
        var a = faqForm.querySelector('[name="answer"]');
        var s = faqForm.querySelector('[name="sort_order"]');
        var qv = q ? q.value.trim() : '';
        var av = a ? a.value.trim() : '';
        var sv = s ? s.value.trim() : '0';

        setErr('question', '');
        setErr('answer', '');
        setErr('sort_order', '');
        if (!qv) { setErr('question', 'Please enter a question.'); ok = false; }
        else if (qv.length > 255) { setErr('question', 'Question must be 255 characters or fewer.'); ok = false; }
        if (!av) { setErr('answer', 'Please enter an answer.'); ok = false; }
        else if (av.length > 5000) { setErr('answer', 'Answer must be 5000 characters or fewer.'); ok = false; }
        if (sv !== '' && (isNaN(sv) || Number(sv) < 0)) { setErr('sort_order', 'Order must be 0 or greater.'); ok = false; }

        if (!ok) {
            e.preventDefault();
            var firstErr = faqForm.querySelector('[data-error]:not(:empty)');
            if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
})();
