(function () {
  "use strict";

  const PER_PAGE = 5;

  let posts = [
    {
      id: 1,
      title: "7 Xu hướng bài trí phòng khách tối giản ấm cúng năm 2026",
      slug: "xu-huong-phong-khach-toi-gian-2026",
      summary: "Kết hợp tông màu đá ấm, sofa vải bố tự nhiên và ánh sáng gián tiếp giúp không gian tiếp khách rộng rãi hơn.",
      thumbnail: "../assets/images/posts/post-1.svg",
      author: "Nguyễn Minh Anh",
      status: "published",
      views: 1248,
      comments: 4,
      created_at: "18/11/2026",
    },
    {
      id: 2,
      title: "Cách chọn đèn thả bàn ăn chuẩn tỷ lệ và nhiệt độ màu",
      slug: "chon-den-tha-ban-an-chuan-ty-le",
      summary: "Hướng dẫn tính chiều cao treo đèn, đường kính chao đèn theo chiều dài bàn ăn.",
      thumbnail: "../assets/images/posts/post-2.svg",
      author: "Trần Hoàng Nam",
      status: "published",
      views: 980,
      comments: 2,
      created_at: "15/11/2026",
    },
    {
      id: 3,
      title: "Phân biệt gỗ sồi tự nhiên và gỗ óc chó khi chọn nội thất",
      slug: "phan-biet-go-soi-va-go-oc-cho",
      summary: "So sánh vân gỗ, độ bền, khả năng chống cong vênh và mức chi phí giữa hai dòng gỗ phổ biến.",
      thumbnail: "../assets/images/posts/post-3.svg",
      author: "Nguyễn Minh Anh",
      status: "published",
      views: 1520,
      comments: 5,
      created_at: "12/11/2026",
    },
    {
      id: 4,
      title: "Bí quyết thiết kế phòng ngủ phong cách Bắc Âu dễ ngủ sâu",
      slug: "thiet-ke-phong-ngu-bac-au-thu-gian",
      summary: "Từ cách chọn rèm cản sáng, ga giường cotton thoáng khí đến bố trí tab đầu giường.",
      thumbnail: "../assets/images/posts/post-4.svg",
      author: "Lê Thu Hà",
      status: "draft",
      views: 0,
      comments: 0,
      created_at: "09/11/2026",
    },
    {
      id: 5,
      title: "5 Mẹo bố trí kệ sách kiêm vách ngăn cho căn hộ dưới 60m²",
      slug: "toi-uu-ke-sach-cho-can-ho-nho",
      summary: "Giải pháp phân chia không gian thông minh không cần xây tường bí bách.",
      thumbnail: "../assets/images/posts/post-5.svg",
      author: "Trần Hoàng Nam",
      status: "published",
      views: 712,
      comments: 1,
      created_at: "06/11/2026",
    },
    {
      id: 6,
      title: "Top 6 loại cây xanh nội thất dễ chăm sóc, lọc không khí tốt",
      slug: "cay-xanh-noi-that-de-cham-soc",
      summary: "Gợi ý các loài cây chịu bóng râm tốt cùng cách chọn chậu gốm đồng bộ.",
      thumbnail: "../assets/images/posts/post-6.svg",
      author: "Lê Thu Hà",
      status: "draft",
      views: 0,
      comments: 0,
      created_at: "03/11/2026",
    },
  ];

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function showToast(msg) {
    const toast = document.getElementById("adminToast");
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add("is-visible");
    clearTimeout(showToast._t);
    showToast._t = setTimeout(() => toast.classList.remove("is-visible"), 2600);
  }

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

  /* ========================================================================
     1. DANH SÁCH BÀI VIẾT ADMIN (admin/posts.html)
     ======================================================================== */
  function initAdminPostsList() {
    const tbody = document.getElementById("adminPostsTableBody");
    if (!tbody) return;

    const searchInput = document.getElementById("adminPostSearch");
    const statusFilter = document.getElementById("adminPostStatusFilter");
    const countText = document.getElementById("adminPostsCountText");
    const pageInfo = document.getElementById("adminPostsPaginationInfo");
    const pagination = document.getElementById("adminPostsPagination");

    let currentPage = 1;

    function updateStats() {
      const total = posts.length;
      const published = posts.filter((p) => p.status === "published").length;
      const draft = total - published;
      document.getElementById("statTotalPosts").textContent = String(total).padStart(2, "0");
      document.getElementById("statPublishedPosts").textContent = String(published).padStart(2, "0");
      document.getElementById("statDraftPosts").textContent = String(draft).padStart(2, "0");
    }

    function render() {
      const kw = (searchInput?.value || "").trim().toLowerCase();
      const status = statusFilter?.value || "all";

      const filtered = posts.filter((p) => {
        const matchKw =
          !kw || p.title.toLowerCase().includes(kw) || p.slug.toLowerCase().includes(kw);
        const matchStatus = status === "all" || p.status === status;
        return matchKw && matchStatus;
      });

      const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * PER_PAGE;
      const slice = filtered.slice(start, start + PER_PAGE);

      if (countText) countText.textContent = filtered.length + " kết quả";
      if (pageInfo) {
        pageInfo.textContent = filtered.length
          ? "Hiển thị " + (start + 1) + "–" + (start + slice.length) + " trong tổng số " + filtered.length + " bài viết"
          : "Không có bài viết nào";
      }

      if (!slice.length) {
        tbody.innerHTML =
          '<tr><td colspan="7" style="text-align:center;padding:28px;color:#667085;">Không tìm thấy bài viết nào khớp bộ lọc.</td></tr>';
      } else {
        tbody.innerHTML = slice
          .map((p) => {
            const badge =
              p.status === "published"
                ? '<span class="ta-badge ta-badge--published">Đã xuất bản</span>'
                : '<span class="ta-badge ta-badge--draft">Bản nháp</span>';
            return (
              "<tr>" +
              '<td><div class="ta-post-cell"><img class="ta-post-thumb" src="' +
              p.thumbnail +
              '" alt="' +
              escapeHtml(p.title) +
              '"><div><div class="ta-post-title">' +
              escapeHtml(p.title) +
              '</div><div class="ta-post-slug">/' +
              escapeHtml(p.slug) +
              "</div></div></div></td>" +
              "<td>" +
              escapeHtml(p.author) +
              "</td>" +
              "<td>" +
              badge +
              "</td>" +
              "<td>" +
              p.views +
              "</td>" +
              "<td>" +
              p.comments +
              "</td>" +
              "<td>" +
              escapeHtml(p.created_at) +
              "</td>" +
              '<td style="text-align:right;"><div class="ta-row-actions">' +
              '<a href="../post-detail.html?slug=' +
              encodeURIComponent(p.slug) +
              '" class="ta-btn ta-btn--outline ta-btn--sm">Xem</a>' +
              '<a href="post-form.html?id=' +
              p.id +
              '" class="ta-btn ta-btn--outline ta-btn--sm">Sửa</a>' +
              '<button type="button" class="ta-btn ta-btn--danger ta-btn--sm" data-delete-post="' +
              p.id +
              '">Xóa</button>' +
              "</div></td></tr>"
            );
          })
          .join("");
      }

      if (pagination) {
        let html =
          '<button type="button" class="ta-page-btn" data-page="' +
          (currentPage - 1) +
          '" ' +
          (currentPage === 1 ? "disabled" : "") +
          ' aria-label="Trang trước">&lsaquo;</button>';
        for (let i = 1; i <= totalPages; i++) {
          html +=
            '<button type="button" class="ta-page-btn' +
            (i === currentPage ? " is-active" : "") +
            '" data-page="' +
            i +
            '">' +
            i +
            "</button>";
        }
        html +=
          '<button type="button" class="ta-page-btn" data-page="' +
          (currentPage + 1) +
          '" ' +
          (currentPage === totalPages ? "disabled" : "") +
          ' aria-label="Trang sau">&rsaquo;</button>';
        pagination.innerHTML = html;
      }
    }

    searchInput?.addEventListener("input", () => {
      currentPage = 1;
      render();
    });
    statusFilter?.addEventListener("change", () => {
      currentPage = 1;
      render();
    });
    pagination?.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-page]");
      if (!btn || btn.disabled) return;
      currentPage = parseInt(btn.getAttribute("data-page"), 10);
      render();
    });

    tbody.addEventListener("click", (e) => {
      const btn = e.target.closest("[data-delete-post]");
      if (!btn) return;
      const id = parseInt(btn.getAttribute("data-delete-post"), 10);
      const target = posts.find((p) => p.id === id);
      if (!target) return;
      if (!window.confirm('Bạn có chắc muốn xóa bài viết "' + target.title + '"?')) return;
      posts = posts.filter((p) => p.id !== id);
      updateStats();
      render();
      showToast("Đã xóa bài viết thành công.");
    });

    updateStats();
    render();
  }

  /* ========================================================================
     2. FORM THÊM / SỬA BÀI VIẾT (admin/post-form.html)
     ======================================================================== */
  function initAdminPostForm() {
    const form = document.getElementById("adminPostForm");
    if (!form) return;

    const titleInput = document.getElementById("postTitle");
    const slugInput = document.getElementById("postSlug");
    const summaryInput = document.getElementById("postSummary");
    const editorSurface = document.getElementById("postEditorSurface");
    const contentHidden = document.getElementById("postContent");
    const statusSelect = document.getElementById("postStatus");
    const metaTitle = document.getElementById("metaTitle");
    const metaDesc = document.getElementById("metaDescription");
    const fileInput = document.getElementById("postThumbnail");
    const dropzone = document.getElementById("thumbnailDropzone");
    const chooseBtn = document.getElementById("chooseThumbnailBtn");
    const previewImg = document.getElementById("thumbnailPreview");
    const fileNameLabel = document.getElementById("thumbnailFileName");

    let slugTouched = false;
    let hasThumbnail = false;

    // Nếu đang ở chế độ Sửa (?id=...)
    const params = new URLSearchParams(window.location.search);
    const editId = parseInt(params.get("id") || "0", 10);
    const editingPost = posts.find((p) => p.id === editId);
    if (editingPost) {
      document.getElementById("postFormHeading").textContent = "Chỉnh sửa bài viết";
      document.getElementById("postFormBreadcrumb").textContent = "Chỉnh sửa bài viết";
      titleInput.value = editingPost.title;
      slugInput.value = editingPost.slug;
      summaryInput.value = editingPost.summary;
      editorSurface.innerHTML = "<p>" + escapeHtml(editingPost.summary) + "</p>";
      statusSelect.value = editingPost.status;
      previewImg.src = editingPost.thumbnail;
      previewImg.hidden = false;
      hasThumbnail = true;
      slugTouched = true;
    }

    // Tự động khởi tạo TinyMCE nếu bản self-hosted đã được tải về
    if (window.tinymce) {
      window.tinymce.init({
        selector: "#postContent",
        license_key: "gpl",
        images_upload_url: "/admin/posts/upload-image",
        automatic_uploads: true,
        relative_urls: false,
      });
    } else {
      // Thanh công cụ Rich-Text dự phòng (chạy 100% offline)
      document.querySelectorAll(".ta-editor-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
          editorSurface.focus();
          document.execCommand(this.getAttribute("data-cmd"), false, this.getAttribute("data-val") || null);
        });
      });
    }

    // Auto-slug tiếng Việt
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

    // Bộ đếm ký tự Summary & SEO
    function bindCounter(inputEl, counterEl, max, suffix) {
      const update = () => {
        const len = inputEl.value.length;
        counterEl.textContent = len + " / " + max + (suffix ? " " + suffix : "");
        counterEl.classList.toggle("is-over", len > max);
      };
      inputEl?.addEventListener("input", update);
      update();
    }
    bindCounter(summaryInput, document.getElementById("summaryCounter"), 500, "");
    bindCounter(metaTitle, document.getElementById("metaTitleCounter"), 60, "gợi ý");
    bindCounter(metaDesc, document.getElementById("metaDescCounter"), 160, "gợi ý");

    // Drag & Drop + Single-pointer button upload thumbnail (WCAG 2.2 AA)
    function handleThumbnailFile(file) {
      const errEl = document.getElementById("postThumbnailError");
      if (!file) return;
      const allowed = ["image/jpeg", "image/png", "image/webp"];
      if (!allowed.includes(file.type)) {
        errEl.textContent = "Chỉ chấp nhận ảnh định dạng JPG, PNG hoặc WEBP.";
        errEl.classList.add("is-visible");
        return;
      }
      if (file.size > 2 * 1024 * 1024) {
        errEl.textContent = "Dung lượng ảnh vượt quá giới hạn 2MB.";
        errEl.classList.add("is-visible");
        return;
      }
      errEl.textContent = "";
      errEl.classList.remove("is-visible");

      const reader = new FileReader();
      reader.onload = (ev) => {
        previewImg.src = ev.target.result;
        previewImg.hidden = false;
        fileNameLabel.textContent = "Đã chọn: " + file.name + " (" + Math.round(file.size / 1024) + " KB)";
        hasThumbnail = true;
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
      handleThumbnailFile(e.dataTransfer.files[0]);
    });

    // Validation onBlur + Focusable Error Summary
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
      const plain = (editorSurface.textContent || "").trim();
      contentHidden.value = editorSurface.innerHTML;
      const msg = plain.length < 20 ? "Nội dung bài viết cần tối thiểu 20 ký tự." : "";
      setErr(editorSurface, "postContentError", msg);
      return msg;
    }

    titleInput.addEventListener("blur", validateTitle);
    slugInput.addEventListener("blur", validateSlug);
    editorSurface.addEventListener("blur", validateContent);

    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const issues = [];

      const tErr = validateTitle();
      if (tErr) issues.push({ id: "postTitle", msg: tErr });

      const sErr = validateSlug();
      if (sErr) issues.push({ id: "postSlug", msg: sErr });

      const cErr = validateContent();
      if (cErr) issues.push({ id: "postEditorSurface", msg: cErr });

      if (!hasThumbnail) {
        const msg = "Vui lòng chọn ảnh đại diện (thumbnail) cho bài viết.";
        setErr(null, "postThumbnailError", msg);
        issues.push({ id: "chooseThumbnailBtn", msg: msg });
      } else {
        setErr(null, "postThumbnailError", "");
      }

      const summaryBox = document.getElementById("postFormErrorSummary");
      const summaryList = document.getElementById("postFormErrorList");

      if (issues.length) {
        summaryList.innerHTML = issues
          .map((it) => '<li><a href="#' + it.id + '">' + escapeHtml(it.msg) + "</a></li>")
          .join("");
        summaryBox.classList.add("is-visible");
        summaryBox.focus();
        return;
      }

      summaryBox.classList.remove("is-visible");
      showToast("Đã lưu bài viết thành công! Đang quay lại danh sách...");
      setTimeout(() => {
        window.location.href = "posts.html";
      }, 900);
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initAdminPostsList();
    initAdminPostForm();
  });
})();
