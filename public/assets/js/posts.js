(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("commentForm");
    const input = document.getElementById("commentInput");
    if (!form || !input) return;

    const errorEl = document.getElementById("commentInputError");
    const charCount = document.getElementById("commentCharCount");
    const summaryEl = document.getElementById("commentErrorSummary");
    const summaryList = document.getElementById("commentErrorList");

    function validateField() {
      const val = input.value.trim();
      let msg = "";
      if (val.length < 5) {
        msg = "Nội dung bình luận phải có ít nhất 5 ký tự.";
      } else if (val.length > 1000) {
        msg = "Nội dung bình luận tối đa 1000 ký tự.";
      }

      if (msg) {
        input.classList.add("is-invalid");
        if (errorEl) {
          errorEl.textContent = msg;
          errorEl.classList.add("is-visible");
        }
      } else {
        input.classList.remove("is-invalid");
        if (errorEl) {
          errorEl.textContent = "";
          errorEl.classList.remove("is-visible");
        }
      }
      return msg;
    }

    input.addEventListener("input", function () {
      if (charCount) charCount.textContent = input.value.length + " / 1000";
    });
    input.addEventListener("blur", validateField);

    form.addEventListener("submit", function (e) {
      const err = validateField();
      if (err) {
        e.preventDefault();
        if (summaryList && summaryEl) {
          summaryList.innerHTML = '<li><a href="#commentInput">' + err + "</a></li>";
          summaryEl.classList.add("is-visible");
          summaryEl.focus();
        }
      }
    });
  });
})();
