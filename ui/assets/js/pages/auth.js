/**
 * DECOR E-COMMERCE - AUTHENTICATION & CLIENT-SIDE RBAC (PHÂN QUYỀN)
 * Phục vụ: login.html, register.html, forgot-password.html, reset-password.html
 * Bao gồm:
 *  - Quản lý phiên đăng nhập & phân quyền (Guest / Member / Admin) qua localStorage
 *  - Chuyển đổi xem thử vai trò (Quản trị viên / Thành viên) ở cột trái
 *  - Ẩn/hiện mật khẩu cho mọi ô password
 *  - Kiểm tra dữ liệu đầu vào (Client-side Validation)
 *  - Đo độ mạnh mật khẩu & khớp mật khẩu realtime (+ nút Xem thử trạng thái lỗi mẫu)
 *  - Bộ đếm ngược gửi lại email quên mật khẩu
 */

(function () {
  "use strict";

  const STORAGE_SESSION_KEY = "decor_auth_session";
  const STORAGE_USERS_KEY = "decor_registered_users";
  const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

  // Danh sách tài khoản khởi tạo theo đúng database/seed.sql
  const DEFAULT_USERS = [
    {
      id: 1,
      initials: "MA",
      full_name: "Nguyễn Minh Anh",
      email: "admin@decor.vn",
      password: "admin123",
      role: "admin",
      status: "active",
    },
    {
      id: 2,
      initials: "KH",
      full_name: "Lê Thu Hà",
      email: "customer@decor.vn",
      password: "123456",
      role: "member",
      status: "active",
    },
    {
      id: 3,
      initials: "TM",
      full_name: "Bùi Thanh Mai",
      email: "banned@decor.vn",
      password: "123456",
      role: "member",
      status: "banned",
    },
  ];

  function getUsersStore() {
    try {
      const raw = localStorage.getItem(STORAGE_USERS_KEY);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (Array.isArray(parsed) && parsed.length) return parsed;
      }
    } catch (err) {
      // ignore storage error
    }
    return DEFAULT_USERS.slice();
  }

  function saveUsersStore(list) {
    try {
      localStorage.setItem(STORAGE_USERS_KEY, JSON.stringify(list));
    } catch (err) {
      // ignore
    }
  }

  function getCurrentSession() {
    try {
      const raw = localStorage.getItem(STORAGE_SESSION_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (err) {
      return null;
    }
  }

  function setCurrentSession(user) {
    const payload = {
      id: user.id,
      initials: user.initials || getInitials(user.full_name),
      full_name: user.full_name,
      email: user.email,
      role: user.role, // 'admin' | 'member'
      status: user.status || "active",
      logged_in_at: new Date().toISOString(),
    };
    try {
      localStorage.setItem(STORAGE_SESSION_KEY, JSON.stringify(payload));
    } catch (err) {
      // ignore
    }
    return payload;
  }

  function clearCurrentSession() {
    try {
      localStorage.removeItem(STORAGE_SESSION_KEY);
    } catch (err) {
      // ignore
    }
  }

  function getInitials(fullName) {
    const parts = String(fullName || "")
      .trim()
      .split(/\s+/)
      .filter(Boolean);
    if (!parts.length) return "TV";
    if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
    return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
  }

  /**
   * 1. HIỂN THỊ TRẠNG THÁI PHIÊN ĐĂNG NHẬP & THÔNG BÁO PHÂN QUYỀN TỪ URL
   */
  function initSessionStatusBanner() {
    const params = new URLSearchParams(window.location.search);
    if (params.get("logout") === "1") {
      clearCurrentSession();
    }

    const reason = params.get("reason");
    if (reason === "unauthenticated") {
      showFormAlert(
        "loginAlert",
        "Vui lòng đăng nhập để truy cập trang quản trị.",
        "error"
      );
    } else if (reason === "forbidden") {
      showFormAlert(
        "loginAlert",
        "Tài khoản Thành viên (member) không có quyền truy cập khu vực Quản trị viên. Vui lòng đăng nhập bằng tài khoản Quản trị viên.",
        "error"
      );
    } else if (params.get("registered") === "1") {
      showFormAlert(
        "loginAlert",
        "Đăng ký tài khoản Thành viên thành công! Bạn có thể đăng nhập ngay bên dưới.",
        "success"
      );
    }

    const sessionBox = document.getElementById("activeSessionBanner");
    const session = getCurrentSession();
    if (!sessionBox) return;

    if (!session) {
      sessionBox.style.display = "none";
      return;
    }

    const roleLabel =
      session.role === "admin"
        ? "Quản trị viên (admin)"
        : "Thành viên (member)";
    const targetUrl =
      session.role === "admin" ? "admin/roles.html" : "posts.html";

    sessionBox.style.display = "flex";
    sessionBox.innerHTML =
      '<div style="flex:1">' +
      '<div class="auth-alert-title">Đang đăng nhập: ' +
      session.full_name +
      " (" +
      session.email +
      ")</div>" +
      "<div>Vai trò hiện tại: <strong>" +
      roleLabel +
      '</strong> &bull; <a href="' +
      targetUrl +
      '">Đi tới trang của bạn &rarr;</a></div>' +
      "</div>" +
      '<button type="button" id="quickLogoutBtn" class="demo-fill-btn">Đăng xuất</button>';

    document
      .getElementById("quickLogoutBtn")
      ?.addEventListener("click", function () {
        clearCurrentSession();
        sessionBox.style.display = "none";
        showFormAlert("loginAlert", "Đã đăng xuất khỏi phiên làm việc.", "success");
      });
  }

  /**
   * 2. CỘT TRÁI: Chuyển đổi xem thử quyền giữa 2 vai trò (admin / member)
   */
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
        const roleKey = this.getAttribute("data-role-preview");
        const profile = roleProfiles[roleKey];
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
          const isAllowed = profile.allowed.includes(perm);
          pill.classList.toggle("is-disabled", !isAllowed);
        });
      });
    });
  }

  /**
   * 3. ẨN / HIỆN MẬT KHẨU
   */
  function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll("[data-toggle-password]");
    toggleButtons.forEach((btn) => {
      btn.addEventListener("click", function () {
        const targetId = this.getAttribute("data-toggle-password");
        const input = document.getElementById(targetId);
        if (!input) return;

        const isPassword = input.getAttribute("type") === "password";
        input.setAttribute("type", isPassword ? "text" : "password");
        this.setAttribute(
          "aria-label",
          isPassword ? "Ẩn mật khẩu" : "Hiện mật khẩu"
        );

        const eyeOpen = this.querySelector(".icon-eye-open");
        const eyeClosed = this.querySelector(".icon-eye-closed");
        if (eyeOpen && eyeClosed) {
          eyeOpen.style.display = isPassword ? "none" : "block";
          eyeClosed.style.display = isPassword ? "block" : "none";
        }
      });
    });
  }

  /**
   * 4. HÀM HỖ TRỢ HIỂN THỊ LỖI FIELD & ALERT
   */
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

  function showFormAlert(alertId, message, type) {
    const alertBox = document.getElementById(alertId);
    if (!alertBox) return;
    const textEl = alertBox.querySelector("[data-alert-text]");
    if (textEl && message) {
      textEl.textContent = message;
    }
    alertBox.classList.remove("auth-alert--error", "auth-alert--success");
    alertBox.classList.add(
      type === "success" ? "auth-alert--success" : "auth-alert--error",
      "is-visible"
    );
  }

  function hideFormAlert(alertId) {
    const alertBox = document.getElementById(alertId);
    if (alertBox) {
      alertBox.classList.remove("is-visible");
    }
  }

  /**
   * 5. KIỂM TRA ĐỘ MẠNH MẬT KHẨU (>= 8 ký tự, chữ hoa, chữ thường, số)
   */
  function evaluatePassword(password) {
    return {
      length: password.length >= 8,
      upperLower: /[a-z]/.test(password) && /[A-Z]/.test(password),
      number: /\d/.test(password),
      get isAllValid() {
        return this.length && this.upperLower && this.number;
      },
    };
  }

  /**
   * 6. FORM ĐĂNG NHẬP & PHÂN QUYỀN (login.html)
   */
  function initLoginForm() {
    const form = document.getElementById("loginForm");
    if (!form) return;

    const emailInput = document.getElementById("loginEmail");
    const passwordInput = document.getElementById("loginPassword");
    const emailError = document.getElementById("loginEmailError");
    const passwordError = document.getElementById("loginPasswordError");

    // Nút điền nhanh tài khoản mẫu (Admin / Member / Banned)
    document.querySelectorAll("[data-demo-email]").forEach((btn) => {
      btn.addEventListener("click", function () {
        emailInput.value = this.getAttribute("data-demo-email") || "";
        passwordInput.value = this.getAttribute("data-demo-password") || "";
        setFieldError(emailInput, emailError, "");
        setFieldError(passwordInput, passwordError, "");
        hideFormAlert("loginAlert");
      });
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      hideFormAlert("loginAlert");

      const email = emailInput.value.trim().toLowerCase();
      const password = passwordInput.value;
      let valid = true;

      if (!email) {
        setFieldError(emailInput, emailError, "Vui lòng nhập địa chỉ email.");
        valid = false;
      } else if (!EMAIL_REGEX.test(email)) {
        setFieldError(
          emailInput,
          emailError,
          "Địa chỉ email không đúng định dạng."
        );
        valid = false;
      } else {
        setFieldError(emailInput, emailError, "");
      }

      if (!password) {
        setFieldError(passwordInput, passwordError, "Vui lòng nhập mật khẩu.");
        valid = false;
      } else if (password.length < 6) {
        setFieldError(
          passwordInput,
          passwordError,
          "Mật khẩu phải có ít nhất 6 ký tự."
        );
        valid = false;
      } else {
        setFieldError(passwordInput, passwordError, "");
      }

      if (!valid) {
        showFormAlert(
          "loginAlert",
          "Thông tin đăng nhập chưa hợp lệ. Vui lòng kiểm tra lại các trường bên dưới.",
          "error"
        );
        return;
      }

      // Kiểm tra thông tin đăng nhập và phân quyền
      const users = getUsersStore();
      const matched = users.find((u) => u.email.toLowerCase() === email);

      if (!matched || matched.password !== password) {
        showFormAlert(
          "loginAlert",
          "Email hoặc mật khẩu không chính xác. Bạn có thể bấm nút 'Tài khoản mẫu' bên dưới để điền nhanh.",
          "error"
        );
        return;
      }

      if (matched.status === "banned") {
        showFormAlert(
          "loginAlert",
          "Tài khoản của bạn đã bị khóa (status = banned). Vui lòng liên hệ Quản trị viên.",
          "error"
        );
        return;
      }

      const session = setCurrentSession(matched);
      const isAdmin = session.role === "admin";

      showFormAlert(
        "loginAlert",
        isAdmin
          ? "Đăng nhập thành công với vai trò Quản trị viên (admin)! Đang chuyển tới trang Vai trò & quyền hạn..."
          : "Đăng nhập thành công với vai trò Thành viên (member)! Đang chuyển tới trang Vai trò & quyền hạn để xem quyền của bạn...",
        "success"
      );

      setTimeout(() => {
        window.location.href = "admin/roles.html";
      }, 900);
    });
  }

  /**
   * 7. FORM ĐĂNG KÝ (register.html)
   */
  function initRegisterForm() {
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

    function updatePasswordHintUI() {
      const status = evaluatePassword(passwordInput.value);
      if (passwordHint) {
        passwordHint.classList.toggle("is-valid", status.isAllValid);
      }
      if (confirmInput.value.length > 0) {
        if (confirmInput.value !== passwordInput.value) {
          setFieldError(
            confirmInput,
            confirmError,
            "Mật khẩu nhập lại chưa khớp. Vui lòng nhập lại."
          );
        } else {
          setFieldError(confirmInput, confirmError, "");
          confirmInput.classList.add("is-valid");
        }
      }
      return status.isAllValid;
    }

    passwordInput.addEventListener("input", updatePasswordHintUI);
    confirmInput.addEventListener("input", updatePasswordHintUI);

    // Nút điền mẫu trạng thái lỗi (giống ảnh thiết kế sign_up.png) hoặc trạng thái hợp lệ
    document
      .getElementById("fillErrorSampleBtn")
      ?.addEventListener("click", function () {
        fullNameInput.value = "Lê Thu Hà";
        emailInput.value = "thuha@decor.vn";
        passwordInput.value = "Decor@2026";
        confirmInput.value = "Decor@202";
        updatePasswordHintUI();
        showFormAlert(
          "registerAlert",
          "Chưa thể tạo tài khoản. Vui lòng kiểm tra lại mật khẩu nhập lại bên dưới.",
          "error"
        );
      });

    document
      .getElementById("fillValidSampleBtn")
      ?.addEventListener("click", function () {
        fullNameInput.value = "Lê Thu Hà";
        emailInput.value = "thuha@decor.vn";
        passwordInput.value = "Decor@2026";
        confirmInput.value = "Decor@2026";
        setFieldError(fullNameInput, fullNameError, "");
        setFieldError(emailInput, emailError, "");
        setFieldError(passwordInput, passwordError, "");
        setFieldError(confirmInput, confirmError, "");
        hideFormAlert("registerAlert");
        updatePasswordHintUI();
      });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      hideFormAlert("registerAlert");

      const fullName = fullNameInput.value.trim();
      const email = emailInput.value.trim().toLowerCase();
      const password = passwordInput.value;
      const confirmPass = confirmInput.value;
      let valid = true;

      if (fullName.length < 2 || fullName.length > 100) {
        setFieldError(
          fullNameInput,
          fullNameError,
          "Họ và tên phải từ 2 đến 100 ký tự."
        );
        valid = false;
      } else {
        setFieldError(fullNameInput, fullNameError, "");
      }

      if (!EMAIL_REGEX.test(email)) {
        setFieldError(
          emailInput,
          emailError,
          "Vui lòng nhập địa chỉ email hợp lệ."
        );
        valid = false;
      } else {
        setFieldError(emailInput, emailError, "");
      }

      const passStatus = evaluatePassword(password);
      if (!passStatus.isAllValid) {
        setFieldError(
          passwordInput,
          passwordError,
          "Mật khẩu cần ít nhất 8 ký tự, gồm chữ hoa, chữ thường và số."
        );
        valid = false;
      } else {
        setFieldError(passwordInput, passwordError, "");
      }

      if (!confirmPass || confirmPass !== password) {
        setFieldError(
          confirmInput,
          confirmError,
          "Mật khẩu nhập lại chưa khớp. Vui lòng nhập lại."
        );
        valid = false;
      } else {
        setFieldError(confirmInput, confirmError, "");
      }

      if (!termsCheckbox.checked) {
        if (termsError) {
          termsError.textContent =
            "Bạn cần đồng ý với Điều khoản sử dụng và Chính sách quyền riêng tư.";
          termsError.classList.add("is-visible");
        }
        valid = false;
      } else if (termsError) {
        termsError.textContent = "";
        termsError.classList.remove("is-visible");
      }

      if (!valid) {
        showFormAlert(
          "registerAlert",
          "Chưa thể tạo tài khoản. Vui lòng kiểm tra lại các thông tin bên dưới.",
          "error"
        );
        return;
      }

      const users = getUsersStore();
      if (users.some((u) => u.email.toLowerCase() === email)) {
        setFieldError(
          emailInput,
          emailError,
          "Địa chỉ email này đã được đăng ký."
        );
        showFormAlert(
          "registerAlert",
          "Địa chỉ email đã tồn tại trên hệ thống.",
          "error"
        );
        return;
      }

      const newUser = {
        id: users.length + 1,
        initials: getInitials(fullName),
        full_name: fullName,
        email: email,
        password: password,
        role: "member", // Mặc định tài khoản mới luôn có role = 'member'
        status: "active",
      };
      users.push(newUser);
      saveUsersStore(users);
      setCurrentSession(newUser);

      showFormAlert(
        "registerAlert",
        "Tạo tài khoản Thành viên (member) thành công! Đang chuyển tới trang xem quyền hạn của bạn...",
        "success"
      );

      setTimeout(() => {
        window.location.href = "admin/roles.html";
      }, 1000);
    });
  }

  /**
   * 8. FORM QUÊN MẬT KHẨU & ĐẾM NGƯỢC (forgot-password.html)
   */
  function initForgotPasswordForm() {
    const form = document.getElementById("forgotPasswordForm");
    if (!form) return;

    const emailInput = document.getElementById("forgotEmail");
    const emailError = document.getElementById("forgotEmailError");
    const submitBtn = document.getElementById("forgotSubmitBtn");
    const sentEmailSpan = document.getElementById("forgotSentEmail");
    let timerId = null;

    function startCooldown(seconds) {
      let remaining = seconds;
      if (timerId) clearInterval(timerId);
      submitBtn.disabled = true;

      const render = () => {
        const mm = String(Math.floor(remaining / 60)).padStart(2, "0");
        const ss = String(remaining % 60).padStart(2, "0");
        submitBtn.innerHTML =
          '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
          "<span>Gửi lại sau " +
          mm +
          ":" +
          ss +
          "</span>";
      };

      render();
      timerId = setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
          clearInterval(timerId);
          submitBtn.disabled = false;
          submitBtn.innerHTML = "<span>Gửi hướng dẫn đặt lại mật khẩu</span>";
        } else {
          render();
        }
      }, 1000);
    }

    if (submitBtn && submitBtn.hasAttribute("data-initial-cooldown")) {
      const initialSec =
        parseInt(submitBtn.getAttribute("data-initial-cooldown"), 10) || 45;
      startCooldown(initialSec);
    }

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const email = emailInput.value.trim();

      if (!email || !EMAIL_REGEX.test(email)) {
        setFieldError(
          emailInput,
          emailError,
          "Vui lòng nhập địa chỉ email hợp lệ đã đăng ký."
        );
        return;
      }

      setFieldError(emailInput, emailError, "");
      if (sentEmailSpan) sentEmailSpan.textContent = email;

      const alertBox = document.getElementById("forgotSuccessAlert");
      if (alertBox) alertBox.classList.add("is-visible");

      startCooldown(45);
    });
  }

  /**
   * 9. FORM ĐẶT LẠI MẬT KHẨU MỚI (reset-password.html)
   */
  function initResetPasswordForm() {
    const form = document.getElementById("resetPasswordForm");
    if (!form) return;

    const passInput = document.getElementById("resetPassword");
    const confirmInput = document.getElementById("resetPasswordConfirm");
    const passError = document.getElementById("resetPasswordError");
    const confirmError = document.getElementById("resetPasswordConfirmError");
    const passHint = document.getElementById("resetPasswordHint");

    passInput.addEventListener("input", function () {
      const status = evaluatePassword(passInput.value);
      if (passHint) passHint.classList.toggle("is-valid", status.isAllValid);
    });

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      hideFormAlert("resetAlert");

      const status = evaluatePassword(passInput.value);
      let valid = true;

      if (!status.isAllValid) {
        setFieldError(
          passInput,
          passError,
          "Mật khẩu mới cần ít nhất 8 ký tự, gồm chữ hoa, chữ thường và số."
        );
        valid = false;
      } else {
        setFieldError(passInput, passError, "");
      }

      if (!confirmInput.value || confirmInput.value !== passInput.value) {
        setFieldError(
          confirmInput,
          confirmError,
          "Mật khẩu nhập lại chưa khớp."
        );
        valid = false;
      } else {
        setFieldError(confirmInput, confirmError, "");
      }

      if (!valid) return;

      showFormAlert(
        "resetAlert",
        "Đặt lại mật khẩu thành công! Đang chuyển về trang Đăng nhập...",
        "success"
      );

      setTimeout(() => {
        window.location.href = "login.html";
      }, 1000);
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initSessionStatusBanner();
    initRolePreviewSwitcher();
    initPasswordToggles();
    initLoginForm();
    initRegisterForm();
    initForgotPasswordForm();
    initResetPasswordForm();
  });
})();
