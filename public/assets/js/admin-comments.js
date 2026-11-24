(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".js-confirm-delete").forEach((formEl) => {
      formEl.addEventListener("submit", function (e) {
        const msg = formEl.getAttribute("data-confirm-msg") || "Bạn có chắc muốn xóa bình luận này?";
        if (!window.confirm(msg)) {
          e.preventDefault();
        }
      });
    });
  });
})();
