(function () {
  "use strict";

  function generateSlug(title) {
    return title
      .toLowerCase()
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/đ/g, "d")
      .replace(/[^a-z0-9\s-]/g, "")
      .trim()
      .replace(/[\s-]+/g, "-")
      .replace(/^-+|-+$/g, "");
  }

  document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".js-confirm-delete").forEach((formEl) => {
      formEl.addEventListener("submit", function (e) {
        const msg = formEl.getAttribute("data-confirm-msg") || "Bạn có chắc muốn xóa?";
        if (!window.confirm(msg)) {
          e.preventDefault();
        }
      });
    });

    const form = document.getElementById("adminPostForm");
    if (!form) return;

    const titleInput = document.getElementById("postTitle");
    const slugInput = document.getElementById("postSlug");
    const summaryInput = document.getElementById("postSummary");
    const editorSurface = document.getElementById("postEditorSurface");
    const contentHidden = document.getElementById("postContent");
    const metaTitle = document.getElementById("metaTitle");
    const metaDesc = document.getElementById("metaDescription");
    const fileInput = document.getElementById("postThumbnail");
    const dropzone = document.getElementById("thumbnailDropzone");
    const chooseBtn = document.getElementById("chooseThumbnailBtn");
    const previewImg = document.getElementById("thumbnailPreview");
    const fileNameLabel = document.getElementById("thumbnailFileName");

    let slugTouched = slugInput.value.trim() !== "";

    if (window.tinymce) {
      window.tinymce.init({
        selector: "#postContent",
        license_key: "gpl",
        images_upload_url: form.getAttribute("data-upload-url") || "/admin/posts/upload-image",
        automatic_uploads: true,
        relative_urls: false,
      });
    } else {
      document.querySelectorAll(".ta-editor-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
          editorSurface.focus();
          document.execCommand(this.getAttribute("data-cmd"), false, this.getAttribute("data-val") || null);
        });
      });
    }

    titleInput.addEventListener("input", () => {
      if (!slugTouched) slugInput.value = generateSlug(titleInput.value);
    });
    slugInput.addEventListener("input", () => {
      slugTouched = true;
    });
    document.getElementById("regenerateSlugBtn")?.addEventListener("click", () => {
      slugInput.value = generateSlug(titleInput.value);
      slugTouched = false;
    });

    function bindCounter(inputEl, counterEl, max, suffix) {
      if (!inputEl || !counterEl) return;
      const update = () => {
        const len = inputEl.value.length;
        counterEl.textContent = len + " / " + max + (suffix ? " " + suffix : "");
        counterEl.classList.toggle("is-over", len > max);
      };
      inputEl.addEventListener("input", update);
      update();
    }
    bindCounter(summaryInput, document.getElementById("summaryCounter"), 500, "");
    bindCounter(metaTitle, document.getElementById("metaTitleCounter"), 60, "gợi ý");
    bindCounter(metaDesc, document.getElementById("metaDescCounter"), 160, "gợi ý");

    function handleThumbnailFile(file) {
      const errEl = document.getElementById("postThumbnailError");
      if (!file) return;
      const allowed = ["image/jpeg", "image/png", "image/webp"];
      if (!allowed.includes(file.type)) {
        errEl.textContent = "Chỉ chấp nhận ảnh định dạng JPG, PNG hoặc WEBP.";
        errEl.classList.add("is-visible");
        fileInput.value = "";
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        errEl.textContent = "Dung lượng ảnh vượt quá giới hạn 2MB.";
        errEl.classList.add("is-visible");
        fileInput.value = "";
        return;
      }
      errEl.textContent = "";
      errEl.classList.remove("is-visible");

      const reader = new FileReader();
      reader.onload = (ev) => {
        previewImg.src = ev.target.result;
        previewImg.hidden = false;
        fileNameLabel.textContent = "Đã chọn: " + file.name + " (" + Math.round(file.size / 1024) + " KB)";
      };
      reader.readAsDataURL(file);
    }

    chooseBtn?.addEventListener("click", () => fileInput.click());
    fileInput?.addEventListener("change", () => handleThumbnailFile(fileInput.files[0]));

    dropzone?.addEventListener("dragover", (e) => {
      e.preventDefault();
      dropzone.classList.add("is-dragover");
    });
    dropzone?.addEventListener("dragleave", () => dropzone.classList.remove("is-dragover"));
    dropzone?.addEventListener("drop", (e) => {
      e.preventDefault();
      dropzone.classList.remove("is-dragover");
      const file = e.dataTransfer.files[0];
      if (file) {
        const dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        handleThumbnailFile(file);
      }
    });

    function setErr(el, errId, msg) {
      const errEl = document.getElementById(errId);
      if (msg) {
        el?.classList.add("is-invalid");
        if (errEl) {
          errEl.textContent = msg;
          errEl.classList.add("is-visible");
        }
      } else {
        el?.classList.remove("is-invalid");
        if (errEl) {
          errEl.textContent = "";
          errEl.classList.remove("is-visible");
        }
      }
    }

    function validateTitle() {
      const v = titleInput.value.trim();
      const msg = v.length < 5 ? "Tiêu đề bài viết phải có ít nhất 5 ký tự." : "";
      setErr(titleInput, "postTitleError", msg);
      return msg;
    }

    function validateSlug() {
      const v = slugInput.value.trim();
      const msg = !/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(v)
        ? "Slug chỉ gồm chữ thường không dấu, số và dấu gạch ngang (-)."
        : "";
      setErr(slugInput, "postSlugError", msg);
      return msg;
    }

    function validateContent() {
      if (!window.tinymce && editorSurface) {
        contentHidden.value = editorSurface.innerHTML;
      }
      const plain = window.tinymce
        ? window.tinymce.activeEditor.getContent({ format: "text" }).trim()
        : (editorSurface?.textContent || "").trim();
      const msg = plain.length < 20 ? "Nội dung bài viết cần tối thiểu 20 ký tự." : "";
      setErr(editorSurface, "postContentError", msg);
      return msg;
    }

    titleInput.addEventListener("blur", validateTitle);
    slugInput.addEventListener("blur", validateSlug);
    editorSurface?.addEventListener("blur", validateContent);

    form.addEventListener("submit", function (e) {
      if (window.tinymce) {
        window.tinymce.triggerSave();
      } else if (editorSurface) {
        contentHidden.value = editorSurface.innerHTML;
      }

      const issues = [];
      const tErr = validateTitle();
      if (tErr) issues.push({ id: "postTitle", msg: tErr });
      const sErr = validateSlug();
      if (sErr) issues.push({ id: "postSlug", msg: sErr });
      const cErr = validateContent();
      if (cErr) issues.push({ id: "postEditorSurface", msg: cErr });

      const summaryBox = document.getElementById("postFormErrorSummary");
      const summaryList = document.getElementById("postFormErrorList");

      if (issues.length) {
        e.preventDefault();
        if (summaryList && summaryBox) {
          summaryList.innerHTML = issues
            .map((it) => '<li><a href="#' + it.id + '">' + it.msg + "</a></li>")
            .join("");
          summaryBox.classList.add("is-visible");
          summaryBox.focus();
        }
      }
    });
  });
})();
