/* Tương tác progressive enhancement; mọi kiểm tra quan trọng vẫn chạy trong PHP. */
(() => {
    "use strict";
    const bytes = (value) => new TextEncoder().encode(value).length;
    function error(input, message) {
        if (!input) return;
        input.setAttribute("aria-invalid", message ? "true" : "false");
        const target = document.getElementById(input.id + "Error");
        if (target) target.textContent = message;
    }
    document.querySelectorAll("[data-toggle-password]").forEach((button) => {
        button.addEventListener("click", () => {
            const input = document.getElementById(button.dataset.togglePassword);
            const visible = input.type === "password";
            input.type = visible ? "text" : "password";
            button.textContent = visible ? "Ẩn" : "Hiện";
            button.setAttribute("aria-label", visible ? "Ẩn mật khẩu" : "Hiện mật khẩu");
            button.setAttribute("aria-pressed", String(visible));
        });
    });
    document.querySelectorAll("form[data-validate]").forEach((form) => {
        const field = (name) => form.elements.namedItem(name);
        const mode = form.dataset.validate;
        const email = field("email");
        const emailBox = form.querySelector("[data-email-confirmation]");
        function emailChanged() {
            if (!emailBox) return;
            const changed = email.value.trim().toLowerCase() !== form.dataset.currentEmail.toLowerCase();
            emailBox.hidden = !changed;
            field("email_password").required = changed;
            if (!changed) {
                field("email_password").value = "";
                error(field("email_password"), "");
            }
        }
        email?.addEventListener("input", emailChanged);
        emailChanged();
        const password = field("password");
        const current = field("current_password");
        function rules() {
            if (!password) return;
            const value = password.value;
            const states = {
                length: [...value].length >= 8 && bytes(value) <= 72,
                case: /[A-Z]/.test(value) && /[a-z]/.test(value),
                number: /[0-9]/.test(value),
                different: value.length > 0 && value !== current?.value,
            };
            form.querySelectorAll("[data-password-rule]").forEach((item) => item.classList.toggle("is-valid", states[item.dataset.passwordRule]));
        }
        password?.addEventListener("input", rules);
        current?.addEventListener("input", rules);
        form.addEventListener("submit", (event) => {
            const errors = {};
            form.querySelectorAll(".field-error").forEach((item) => { item.textContent = ""; });
            form.querySelectorAll("[aria-invalid]").forEach((item) => item.removeAttribute("aria-invalid"));
            if (field("full_name")) {
                const name = field("full_name").value.trim();
                if ([...name].length < 2 || [...name].length > 100 || /[\x00-\x1F\x7F]/.test(name)) errors.full_name = "Họ và tên cần từ 2 đến 100 ký tự hợp lệ.";
            }
            if (email && (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim()) || bytes(email.value.trim()) > 150)) errors.email = "Vui lòng nhập địa chỉ email hợp lệ.";
            const phone = field("phone");
            if (phone?.value.trim() && !/^\+?[0-9]{9,15}$/.test(phone.value.trim())) errors.phone = "Số điện thoại cần từ 9 đến 15 chữ số, có thể bắt đầu bằng +.";
            if (emailBox && !emailBox.hidden && !field("email_password").value) errors.email_password = "Nhập mật khẩu hiện tại để xác nhận đổi email.";
            if (current && !current.value) errors.current_password = "Vui lòng nhập mật khẩu hiện tại.";
            if (password && mode !== "login") {
                const value = password.value;
                if ([...value].length < 8 || bytes(value) > 72 || !/[A-Z]/.test(value) || !/[a-z]/.test(value) || !/[0-9]/.test(value) || value.includes("\0")) errors.password = "Mật khẩu cần từ 8 ký tự, tối đa 72 byte; gồm chữ hoa, chữ thường và số.";
                if (current && value === current.value) errors.password = "Mật khẩu mới phải khác mật khẩu hiện tại.";
                if (field("password_confirmation").value !== value) errors.password_confirmation = "Mật khẩu nhập lại chưa khớp.";
            }
            if (Object.keys(errors).length) {
                event.preventDefault();
                Object.entries(errors).forEach(([name, message]) => error(field(name), message));
                field(Object.keys(errors)[0]).focus();
            }
        });
    });

    const avatarForm = document.querySelector("[data-avatar-form]");
    if (!avatarForm) return;
    const input = avatarForm.elements.avatar;
    const preview = avatarForm.querySelector("[data-avatar-preview]");
    const initial = preview.cloneNode(true);
    const save = avatarForm.querySelector("[data-avatar-submit]");
    const revert = avatarForm.querySelector("[data-avatar-revert]");
    let objectUrl;
    let selection = 0;
    save.disabled = true;
    function clearPreview() {
        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = null;
        preview.replaceChildren(...Array.from(initial.cloneNode(true).childNodes));
    }
    function reset() {
        selection++;
        input.value = "";
        clearPreview();
        save.disabled = true;
        revert.hidden = true;
        error(input, "");
    }
    revert.addEventListener("click", reset);
    input.addEventListener("change", () => {
        const version = ++selection;
        clearPreview();
        save.disabled = true;
        revert.hidden = true;
        error(input, "");
        const file = input.files[0];
        if (!file) return;
        if (!["image/jpeg", "image/png", "image/webp"].includes(file.type) || file.size === 0 || file.size > 2 * 1024 * 1024) {
            error(input, "Chọn ảnh JPG, PNG hoặc WebP hợp lệ, không vượt quá 2 MB.");
            input.value = "";
            return;
        }
        objectUrl = URL.createObjectURL(file);
        const image = new Image();
        image.alt = "Xem trước ảnh đại diện mới";
        image.onload = () => {
            if (version !== selection) return;
            if (image.naturalWidth > 4096 || image.naturalHeight > 4096) {
                reset();
                error(input, "Kích thước ảnh không được vượt quá 4096 × 4096 pixel.");
                return;
            }
            preview.replaceChildren(image);
            save.disabled = false;
            revert.hidden = false;
        };
        image.onerror = () => {
            if (version !== selection) return;
            reset();
            error(input, "Không thể đọc ảnh này. Vui lòng chọn ảnh khác.");
        };
        image.src = objectUrl;
    });
    avatarForm.addEventListener("submit", (event) => {
        if (event.submitter?.name !== "remove_avatar" && (save.disabled || !input.files.length)) {
            event.preventDefault();
            error(input, "Vui lòng chọn một ảnh hợp lệ trước khi lưu.");
        }
    });
    window.addEventListener("pagehide", () => { if (objectUrl) URL.revokeObjectURL(objectUrl); });
})();
