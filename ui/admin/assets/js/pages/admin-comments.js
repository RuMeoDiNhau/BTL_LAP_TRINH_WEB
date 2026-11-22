(function () {
  "use strict";

  const PER_PAGE = 5;

  let comments = [
    {
      id: 1,
      author: "Lê Thu Hà",
      email: "thuha@decor.vn",
      postTitle: "7 Xu hướng bài trí phòng khách tối giản ấm cúng năm 2026",
      postSlug: "xu-huong-phong-khach-toi-gian-2026",
      content: "Bài viết rất chi tiết! Mình vừa áp dụng cách phân tầng ánh sáng 3000K cho góc sofa, phòng nhìn ấm hẳn lên.",
      status: "approved",
      created_at: "19/11/2026 09:15",
    },
    {
      id: 2,
      author: "Phạm Quang Huy",
      email: "quanghuy@decor.vn",
      postTitle: "7 Xu hướng bài trí phòng khách tối giản ấm cúng năm 2026",
      postSlug: "xu-huong-phong-khach-toi-gian-2026",
      content: "Phòng khách rộng khoảng 18m2 thì nên chọn sofa dài bao nhiêu để không bị chật ạ?",
      status: "pending",
      created_at: "20/11/2026 14:40",
    },
    {
      id: 3,
      author: "Võ Ngọc Linh",
      email: "ngoclinh@decor.vn",
      postTitle: "Cách chọn đèn thả bàn ăn chuẩn tỷ lệ và nhiệt độ màu",
      postSlug: "chon-den-tha-ban-an-chuan-ty-le",
      content: "Mình rất thích gợi ý khoảng cách treo đèn 75–85cm, cảm ơn Decor đã chia sẻ.",
      status: "approved",
      created_at: "16/11/2026 11:20",
    },
    {
      id: 4,
      author: "Đặng Đức Long",
      email: "duclong@decor.vn",
      postTitle: "Phân biệt gỗ sồi tự nhiên và gỗ óc chó khi chọn nội thất",
      postSlug: "phan-biet-go-soi-va-go-oc-cho",
      content: "Gỗ sồi sơn lau dầu có bị ngả màu sau 3 năm sử dụng không shop?",
      status: "pending",
      created_at: "21/11/2026 08:05",
    },
    {
      id: 5,
      author: "Tài khoản spam",
      email: "spam@example.com",
      postTitle: "Phân biệt gỗ sồi tự nhiên và gỗ óc chó khi chọn nội thất",
      postSlug: "phan-biet-go-soi-va-go-oc-cho",
      content: "Nhận quảng cáo giá rẻ không liên quan nội dung bài viết...",
      status: "hidden",
      created_at: "18/11/2026 22:10",
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

  document.addEventListener("DOMContentLoaded", function () {
    const tbody = document.getElementById("adminCommentsTableBody");
    if (!tbody) return;

    const searchInput = document.getElementById("commentSearchInput");
    const statusFilter = document.getElementById("commentStatusFilter");
    const postFilter = document.getElementById("commentPostFilter");
    const countText = document.getElementById("commentsCountText");
    const pageInfo = document.getElementById("commentsPaginationInfo");
    const pagination = document.getElementById("commentsPagination");
    const moderationToggle = document.getElementById("moderationToggle");
    const moderationLabel = document.getElementById("moderationLabel");
    const moderationDesc = document.getElementById("moderationDesc");

    let currentPage = 1;

    // Nạp danh sách bài viết vào bộ lọc
    const uniquePosts = [...new Map(comments.map((c) => [c.postSlug, c.postTitle])).entries()];
    uniquePosts.forEach(([slug, title]) => {
      const opt = document.createElement("option");
      opt.value = slug;
      opt.textContent = title;
      postFilter?.appendChild(opt);
    });

    moderationToggle?.addEventListener("change", function () {
      const enabled = this.checked;
      moderationLabel.textContent = enabled ? "Đang bật kiểm duyệt" : "Tắt kiểm duyệt";
      moderationDesc.textContent = enabled
        ? "Đang BẬT (1): Bình luận mới sẽ lưu trạng thái Chờ duyệt (pending) và chỉ hiển thị sau khi Admin duyệt."
        : "Đang TẮT (0): Bình luận mới của thành viên sẽ được duyệt tự động (approved) và hiển thị ngay.";
      showToast(enabled ? "Đã bật chế độ kiểm duyệt bình luận." : "Đã tắt chế độ kiểm duyệt bình luận.");
    });

    function updateStats() {
      const total = comments.length;
      const approved = comments.filter((c) => c.status === "approved").length;
      const pending = comments.filter((c) => c.status === "pending").length;
      const hidden = comments.filter((c) => c.status === "hidden").length;

      document.getElementById("statTotalComments").textContent = String(total).padStart(2, "0");
      document.getElementById("statApprovedComments").textContent = String(approved).padStart(2, "0");
      document.getElementById("statPendingHiddenComments").textContent = String(pending + hidden).padStart(2, "0");

      const badge = document.getElementById("sidebarPendingBadge");
      if (badge) {
        badge.textContent = String(pending);
        badge.style.display = pending > 0 ? "inline-block" : "none";
      }
    }

    function render() {
      const kw = (searchInput?.value || "").trim().toLowerCase();
      const status = statusFilter?.value || "all";
      const postSlug = postFilter?.value || "all";

      const filtered = comments.filter((c) => {
        const matchKw =
          !kw ||
          c.content.toLowerCase().includes(kw) ||
          c.author.toLowerCase().includes(kw) ||
          c.email.toLowerCase().includes(kw);
        const matchStatus = status === "all" || c.status === status;
        const matchPost = postSlug === "all" || c.postSlug === postSlug;
        return matchKw && matchStatus && matchPost;
      });

      const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * PER_PAGE;
      const slice = filtered.slice(start, start + PER_PAGE);

      if (countText) countText.textContent = filtered.length + " kết quả";
      if (pageInfo) {
        pageInfo.textContent = filtered.length
          ? "Hiển thị " + (start + 1) + "–" + (start + slice.length) + " trong tổng số " + filtered.length + " bình luận"
          : "Không có bình luận nào";
      }

      if (!slice.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" style="text-align:center;padding:28px;color:#667085;">Không tìm thấy bình luận nào khớp bộ lọc.</td></tr>';
      } else {
        tbody.innerHTML = slice
          .map((c) => {
            const statusMap = {
              approved: '<span class="ta-badge ta-badge--approved">Đã duyệt</span>',
              pending: '<span class="ta-badge ta-badge--pending">Chờ duyệt</span>',
              hidden: '<span class="ta-badge ta-badge--hidden">Đã ẩn</span>',
            };
            return (
              "<tr>" +
              '<td><div style="font-weight:700;color:#101828;">' +
              escapeHtml(c.author) +
              '</div><div style="font-size:12px;color:#667085;">' +
              escapeHtml(c.email) +
              "</div></td>" +
              '<td style="max-width:300px;">' +
              escapeHtml(c.content) +
              "</td>" +
              '<td style="max-width:220px;"><a href="../post-detail.html?slug=' +
              encodeURIComponent(c.postSlug) +
              '">' +
              escapeHtml(c.postTitle) +
              "</a></td>" +
              "<td>" +
              (statusMap[c.status] || "") +
              "</td>" +
              '<td style="white-space:nowrap;">' +
              escapeHtml(c.created_at) +
              "</td>" +
              '<td style="text-align:right;"><div class="ta-row-actions">' +
              (c.status !== "approved"
                ? '<button type="button" class="ta-btn ta-btn--success ta-btn--sm" data-action="approve" data-id="' +
                  c.id +
                  '">Duyệt</button>'
                : "") +
              (c.status !== "hidden"
                ? '<button type="button" class="ta-btn ta-btn--outline ta-btn--sm" data-action="hide" data-id="' +
                  c.id +
                  '">Ẩn</button>'
                : "") +
              '<button type="button" class="ta-btn ta-btn--danger ta-btn--sm" data-action="delete" data-id="' +
              c.id +
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
    postFilter?.addEventListener("change", () => {
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
      const btn = e.target.closest("[data-action]");
      if (!btn) return;
      const action = btn.getAttribute("data-action");
      const id = parseInt(btn.getAttribute("data-id"), 10);
      const item = comments.find((c) => c.id === id);
      if (!item) return;

      if (action === "approve") {
        item.status = "approved";
        showToast("Đã duyệt bình luận của " + item.author + ".");
      } else if (action === "hide") {
        item.status = "hidden";
        showToast("Đã ẩn bình luận của " + item.author + ".");
      } else if (action === "delete") {
        if (!window.confirm("Bạn có chắc muốn xóa vĩnh viễn bình luận này?")) return;
        comments = comments.filter((c) => c.id !== id);
        showToast("Đã xóa bình luận.");
      }
      updateStats();
      render();
    });

    updateStats();
    render();
  });
})();
