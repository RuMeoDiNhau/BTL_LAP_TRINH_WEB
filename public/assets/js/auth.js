(function () {
  "use strict";

  const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  function initRolePreviewSwitcher() {
    const roleButtons = document.querySelectorAll("[data-role-preview]");
    if (!roleButtons.length) return;

    const avatarEl = document.getElementById("previewAvatar");
    const nameEl = document.getElementById("previewName");
    const emailEl = document.getElementById("previewEmail");
    const permPills = document.querySelectorAll("[data-perm-pill]");

    const roleProfiles = {
      admin: {
        initials: "MA",
        name: "Nguyễn Minh Anh",
        email: "admin@decor.vn",
        allowed: ["view", "create", "edit", "delete"],
      },
      member: {
        initials: "TH",
        name: "Lê Thu Hà",
        email: "customer@decor.vn",
        allowed: ["view", "create"],
      },
    };

    roleButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const profile = roleProfiles[this.getAttribute("data-role-preview")];
        if (!profile) return;

        roleButtons.forEach((b) => {
          b.classList.toggle("is-active", b === this);
          b.setAttribute("aria-pressed", b === this ? "true" : "false");
        });

        if (avatarEl) avatarEl.textContent = profile.initials;
        if (nameEl) nameEl.textContent = profile.name;
        if (emailEl) emailEl.textContent = profile.email;

        permPills.forEach((pill) => {
          const perm = pill.getAttribute("data-perm-pill");
          pill.classList.toggle("is-disabled", !profile.allowed.includes(perm));
        });
      });
    });
  }

  function initPasswordToggles() {
    document.querySelectorAll("[data-toggle-password]").forEach((btn) => {
      btn.addEventListener("click", function () {
        const input = document.getElementById(this.getAttribute("data-toggle-password"));
        if (!input) return;

        const isPassword = input.getAttribute("type") === "password";
        input.setAttribute("type", isPassword ? "text" : "password");
        this.setAttribute("aria-label", isPassword ? "Ẩn mật khẩu" : "Hiện mật khẩu");

        const eyeOpen = this.querySelector(".icon-eye-open");
        const eyeClosed = this.querySelector(".icon-eye-closed");
        if (eyeOpen && eyeClosed) {
          eyeOpen.style.display = isPassword ? "none" : "block";
          eyeClosed.style.display = isPassword ? "block" : "none";
        }
      });
    });
  }

  function setFieldError(input, errorEl, message) {
    if (!input) return;
    if (message) {
      input.classList.add("is-invalid");
      input.classList.remove("is-valid");
      if (errorEl) {
        errorEl.textContent = message;
        errorEl.classList.add("is-visible");
      }
    } else {
      input.classList.remove("is-invalid");
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.remove("is-visible");
      }
    }
  }

  function showFormAlert(alertId, message) {
    const alertBox = document.getElementById(alertId);
    if (!alertBox) return;
    const textEl = alertBox.querySelector("[data-alert-text]");
    if (textEl) textEl.textContent = message;
    alertBox.classList.add("is-visible");
  }

  function isStrongPassword(password) {
    return password.length >= 8 && /[a-z]/.test(password) && /[A-Z]/.test(password) && /\d/.test(password);
  }

  function initLoginValidation() {
    const form = document.getElementById("loginForm");
    if (!form) return;

    const emailInput = document.getElementById("loginEmail");
    const passwordInput = document.getElementById("loginPassword");
    const emailError = document.getElementById("loginEmailError");
    const passwordError = document.getElementById("loginPasswordError");

    form.addEventListener("submit", function (e) {
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      let valid = true;

      if (!email) {
        setFieldError(emailInput, emailError, "Vui lòng nhập địa chỉ email.");
        valid = false;
      } else if (!EMAIL_REGEX.test(email)) {
        setFieldError(emailInput, emailError, "Địa chỉ email không đúng định dạng.");
        valid = false;
      } else {
        setFieldError(emailInput, emailError, "");
      }

      if (!password) {
        setFieldError(passwordInput, passwordError, "Vui lòng nhập mật khẩu.");
        valid = false;
      } else {
        setFieldError(passwordInput, passwordError, "");
      }

      if (!valid) {
        e.preventDefault();
        showFormAlert("loginAlert", "Thông tin đăng nhập chưa hợp lệ. Vui lòng kiểm tra lại.");
      }
    });
  }

  function initRegisterValidation() {
    const form = document.getElementById("registerForm");
    if (!form) return;

    const fullNameInput = document.getElementById("regFullName");
    const emailInput = document.getElementById("regEmail");
    const passwordInput = document.getElementById("regPassword");
    const confirmInput = document.getElementById("regPasswordConfirm");
    const termsCheckbox = document.getElementById("regTerms");

    const fullNameError = document.getElementById("regFullNameError");
    const emailError = document.getElementById("regEmailError");
    const passwordError = document.getElementById("regPasswordError");
    const confirmError = document.getElementById("regPasswordConfirmError");
    const termsError = document.getElementById("regTermsError");
    const passwordHint = document.getElementById("regPasswordHint");

    function updatePasswordUI() {
      if (passwordHint) {
        passwordHint.classList.toggle("is-valid", isStrongPassword(passwordInput.value));
      }
      if (confirmInput.value.length > 0) {
        if (confirmInput.value !== passwordInput.value) {
          setFieldError(confirmInput, confirmError, "Mật khẩu nhập lại chưa khớp. Vui lòng nhập lại.");
        } else {
          setFieldError(confirmInput, confirmError, "");
          confirmInput.classList.add("is-valid");
        }
      }
    }

    passwordInput.addEventListener("input", updatePasswordUI);
    confirmInput.addEventListener("input", updatePasswordUI);

    form.addEventListener("submit", function (e) {
      const fullName = fullNameInput.value.trim();
      const email = emailInput.value.trim();
      const password = passwordInput.value;
      const confirmPass = confirmInput.value;
      let valid = true;

      if (fullName.length < 2 || fullName.length > 100) {
        setFieldError(fullNameInput, fullNameError, "Họ và tên phải từ 2 đến 100 ký tự.");
        valid = false;
      } else {
        setFieldError(fullNameInput, fullNameError, "");
      }

      if (!EMAIL_REGEX.test(email)) {
        setFieldError(emailInput, emailError, "Vui lòng nhập địa chỉ email hợp lệ.");
        valid = false;
      } else {
        setFieldError(emailInput, emailError, "");
      }

      if (!isStrongPassword(password)) {
        setFieldError(passwordInput, passwordError, "Mật khẩu cần ít nhất 8 ký tự, gồm chữ hoa, chữ thường và số.");
        valid = false;
      } else {
        setFieldError(passwordInput, passwordError, "");
      }

      if (!confirmPass || confirmPass !== password) {
        setFieldError(confirmInput, confirmError, "Mật khẩu nhập lại chưa khớp. Vui lòng nhập lại.");
        valid = false;
      } else {
        setFieldError(confirmInput, confirmError, "");
      }

      if (!termsCheckbox.checked) {
        if (termsError) {
          termsError.textContent = "Bạn cần đồng ý với Điều khoản sử dụng và Chính sách quyền riêng tư.";
          termsError.classList.add("is-visible");
        }
        valid = false;
      } else if (termsError) {
        termsError.textContent = "";
        termsError.classList.remove("is-visible");
      }

      if (!valid) {
        e.preventDefault();
        showFormAlert("registerAlert", "Chưa thể tạo tài khoản. Vui lòng kiểm tra lại các thông tin bên dưới.");
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initRolePreviewSwitcher();
    initPasswordToggles();
    initLoginValidation();
    initRegisterValidation();
  });
})();
