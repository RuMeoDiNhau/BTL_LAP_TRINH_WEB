(function () {
  "use strict";

  const SESSION_KEY = "decor_auth_session";
  const PER_PAGE = 6;

  const SAMPLE_POSTS = [
    {
      id: 1,
      slug: "xu-huong-phong-khach-toi-gian-2026",
      title: "7 Xu hướng bài trí phòng khách tối giản ấm cúng năm 2026",
      summary: "Kết hợp tông màu đá ấm, sofa vải bố tự nhiên và ánh sáng gián tiếp giúp không gian tiếp khách rộng rãi và sang trọng hơn.",
      thumbnail: "assets/images/posts/post-1.svg",
      author: "Nguyễn Minh Anh",
      date: "2026-11-18",
      dateLabel: "18/11/2026",
      views: 1248,
      content:
        "<p>Phong cách tối giản năm 2026 không còn lạnh lẽo với toàn bộ sắc trắng, mà chuyển dịch mạnh sang bảng màu <strong>Warm Minimalism</strong> gồm kem sữa, xám đá và nâu gỗ sồi.</p>" +
        "<h2>1. Ưu tiên chất liệu tự nhiên và bề mặt thô mộc</h2>" +
        "<p>Sofa bọc vải linen dệt thô, bàn trà gỗ nguyên khối bo góc mềm mại và thảm sợi đay giúp phòng khách giữ được cảm giác gần gũi ngay cả khi giảm thiểu số lượng đồ nội thất.</p>" +
        "<blockquote>Ít đồ đạc hơn nhưng mỗi món đồ đều có câu chuyện về chất liệu và tỉ lệ chuẩn xác.</blockquote>" +
        "<h2>2. Phân tầng ánh sáng thay vì một đèn trần duy nhất</h2>" +
        "<ul><li>Đèn trần ánh sáng trung tính 4000K cho sinh hoạt chung.</li><li>Đèn sàn góc sofa 3000K tạo điểm nhấn đọc sách buổi tối.</li><li>Đèn hắt khe rèm giúp trần nhà trông cao và thoáng hơn.</li></ul>",
    },
    {
      id: 2,
      slug: "chon-den-tha-ban-an-chuan-ty-le",
      title: "Cách chọn đèn thả bàn ăn chuẩn tỷ lệ và nhiệt độ màu",
      summary: "Hướng dẫn tính chiều cao treo đèn, đường kính chao đèn theo chiều dài bàn ăn giúp bữa cơm gia đình thêm ấm áp.",
      thumbnail: "assets/images/posts/post-2.svg",
      author: "Trần Hoàng Nam",
      date: "2026-11-15",
      dateLabel: "15/11/2026",
      views: 980,
      content:
        "<p>Ánh sáng trên bàn ăn ảnh hưởng trực tiếp tới màu sắc món ăn và cảm giác thư giãn của cả gia đình. Nguyên tắc vàng là chọn bóng có chỉ số hoàn màu <strong>CRI &ge; 90</strong> và nhiệt độ màu 2700K–3000K.</p>" +
        "<h2>Khoảng cách treo đèn lý tưởng</h2>" +
        "<p>Đáy chao đèn nên cách mặt bàn ăn từ <strong>75cm đến 85cm</strong> để ánh sáng tập trung xuống mặt bàn mà không chiếu thẳng vào mắt người ngồi đối diện.</p>",
    },
    {
      id: 3,
      slug: "phan-biet-go-soi-va-go-oc-cho",
      title: "Phân biệt gỗ sồi tự nhiên và gỗ óc chó khi chọn nội thất",
      summary: "So sánh vân gỗ, độ bền, khả năng chống cong vênh và mức chi phí giữa hai dòng gỗ được ưa chuộng nhất hiện nay.",
      thumbnail: "assets/images/posts/post-3.svg",
      author: "Nguyễn Minh Anh",
      date: "2026-11-12",
      dateLabel: "12/11/2026",
      views: 1520,
      content:
        "<p>Gỗ sồi (Oak) nổi bật với sắc vàng sáng trẻ trung, hợp căn hộ diện tích vừa và nhỏ; trong khi gỗ óc chó (Walnut) mang sắc nâu socola trầm sang trọng cùng đường vân cuộn xoáy độc bản.</p>" +
        "<h2>Nên chọn loại nào cho căn hộ chung cư?</h2>" +
        "<p>Nếu ưu tiên sự sáng sủa theo phong cách Bắc Âu hoặc Nhật Bản, gỗ sồi là lựa chọn kinh tế và dễ phối màu nhất.</p>",
    },
    {
      id: 4,
      slug: "thiet-ke-phong-ngu-bac-au-thu-gian",
      title: "Bí quyết thiết kế phòng ngủ phong cách Bắc Âu dễ ngủ sâu",
      summary: "Từ cách chọn rèm 2 lớp cản sáng, ga giường cotton thoáng khí đến việc bố trí tab đầu giường gọn gàng.",
      thumbnail: "assets/images/posts/post-4.svg",
      author: "Lê Thu Hà",
      date: "2026-11-09",
      dateLabel: "09/11/2026",
      views: 845,
      content:
        "<p>Phòng ngủ chuẩn Bắc Âu (Scandinavian) đặt sự thư thái lên hàng đầu thông qua bảng màu trung tính dịu mắt và ánh sáng tự nhiên dồi dào vào ban ngày.</p>",
    },
    {
      id: 5,
      slug: "toi-uu-ke-sach-cho-can-ho-nho",
      title: "5 Mẹo bố trí kệ sách kiêm vách ngăn cho căn hộ dưới 60m²",
      summary: "Giải pháp phân chia không gian thông minh không cần xây tường bí bách, vừa tăng diện tích lưu trữ vừa tạo điểm nhấn decor.",
      thumbnail: "assets/images/posts/post-5.svg",
      author: "Trần Hoàng Nam",
      date: "2026-11-06",
      dateLabel: "06/11/2026",
      views: 712,
      content:
        "<p>Kệ sách khung mảnh không hậu lưng giúp ánh sáng vẫn xuyên qua giữa phòng khách và góc làm việc, tạo cảm giác căn hộ rộng hơn thực tế.</p>",
    },
    {
      id: 6,
      slug: "cay-xanh-noi-that-de-cham-soc",
      title: "Top 6 loại cây xanh nội thất dễ chăm sóc, lọc không khí tốt",
      summary: "Gợi ý các loài cây chịu bóng râm tốt như Bàng Singapore, Trầu bà lá xẻ, Lưỡi hổ cùng cách chọn chậu gốm đồng bộ.",
      thumbnail: "assets/images/posts/post-6.svg",
      author: "Lê Thu Hà",
      date: "2026-11-03",
      dateLabel: "03/11/2026",
      views: 1105,
      content:
        "<p>Một chậu cây xanh đặt ở góc tường trống hoặc cạnh sofa mang lại sức sống tức thì và làm mềm đi các đường thẳng kiến trúc trong nhà.</p>",
    },
    {
      id: 7,
      slug: "chon-sofa-vai-hay-sofa-da",
      title: "Nên chọn sofa vải bố hay sofa da cho gia đình có trẻ nhỏ?",
      summary: "Đánh giá thực tế độ thoáng mát, khả năng vệ sinh vết bẩn và tuổi thọ trung bình của từng loại bọc sofa.",
      thumbnail: "assets/images/posts/post-1.svg",
      author: "Nguyễn Minh Anh",
      date: "2026-10-29",
      dateLabel: "29/10/2026",
      views: 640,
      content:
        "<p>Sofa da dễ lau sạch nước đổ nhưng cần dưỡng định kỳ; trong khi sofa vải nỉ chống thấm thế hệ mới có thể tháo vỏ giặt máy rất tiện lợi.</p>",
    },
    {
      id: 8,
      slug: "phoi-mau-tham-trai-san-phong-khach",
      title: "Nguyên tắc chọn kích thước và phối màu thảm trải sàn phòng khách",
      summary: "Tránh lỗi chọn thảm quá nhỏ khiến khu vực tiếp khách bị rời rạc; cách cân bằng họa tiết thảm với rèm cửa và gối tựa.",
      thumbnail: "assets/images/posts/post-4.svg",
      author: "Trần Hoàng Nam",
      date: "2026-10-24",
      dateLabel: "24/10/2026",
      views: 530,
      content:
        "<p>Tối thiểu hai chân trước của ghế sofa phải đặt lên trên mép thảm để toàn bộ cụm bàn ghế liên kết thành một khối thống nhất.</p>",
    },
    {
      id: 9,
      slug: "decor-goc-lam-viec-tai-nha",
      title: "Thiết kế góc làm việc tại nhà gọn gàng và tăng tập trung",
      summary: "Bố trí bàn gỗ gần cửa sổ, khay giấu dây điện thông minh và đèn bàn chống lóa cho người làm việc từ xa.",
      thumbnail: "assets/images/posts/post-5.svg",
      author: "Lê Thu Hà",
      date: "2026-10-19",
      dateLabel: "19/10/2026",
      views: 890,
      content:
        "<p>Đặt màn hình vuông góc với cửa sổ giúp tận dụng ánh sáng tự nhiên mà không bị chói màn hình vào buổi trưa.</p>",
    },
  ];

  let comments = [
    {
      id: 1,
      initials: "TH",
      author: "Lê Thu Hà",
      date: "19/11/2026 09:15",
      content: "Bài viết rất chi tiết! Mình vừa áp dụng cách phân tầng ánh sáng 3000K cho góc sofa, phòng khách nhìn ấm hẳn lên.",
    },
    {
      id: 2,
      initials: "QH",
      author: "Phạm Quang Huy",
      date: "20/11/2026 14:40",
      content: "Cho mình hỏi nếu phòng khách rộng khoảng 18m2 thì nên chọn sofa dài bao nhiêu để không bị chật ạ?",
    },
  ];

  function escapeHtml(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function getSession() {
    try {
      const raw = localStorage.getItem(SESSION_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (e) {
      return null;
    }
  }

  function renderHeaderAuth() {
    const area = document.getElementById("headerAuthArea");
    if (!area) return;
    const session = getSession();
    if (!session) return;

    area.innerHTML =
      '<span style="font-size:13.5px;font-weight:600;color:#0f172a;">' +
      escapeHtml(session.full_name) +
      "</span>" +
      '<a href="login.html?logout=1" class="btn btn--outline btn--sm">Đăng xuất</a>';
  }

  function renderMiniList(containerId, items) {
    const el = document.getElementById(containerId);
    if (!el) return;
    el.innerHTML = items
      .map(
        (p) =>
          '<li class="mini-post-item">' +
          '<a href="post-detail.html?slug=' +
          encodeURIComponent(p.slug) +
          '" class="mini-post-thumb">' +
          '<img src="' +
          p.thumbnail +
          '" alt="' +
          escapeHtml(p.title) +
          '" loading="lazy">' +
          "</a>" +
          "<div>" +
          '<a href="post-detail.html?slug=' +
          encodeURIComponent(p.slug) +
          '" class="mini-post-title">' +
          escapeHtml(p.title) +
          "</a>" +
          '<span class="mini-post-date">' +
          escapeHtml(p.dateLabel) +
          " &bull; " +
          p.views +
          " lượt xem</span>" +
          "</div>" +
          "</li>"
      )
      .join("");
  }

  function renderPostCard(p) {
    const href = "post-detail.html?slug=" + encodeURIComponent(p.slug);
    return (
      '<article class="post-card">' +
      '<a href="' +
      href +
      '" class="post-card-media">' +
      '<img src="' +
      p.thumbnail +
      '" alt="' +
      escapeHtml(p.title) +
      '" loading="lazy">' +
      "</a>" +
      '<div class="post-card-body">' +
      '<div class="post-meta-row">' +
      '<span class="post-meta-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' +
      escapeHtml(p.dateLabel) +
      "</span>" +
      '<span class="post-meta-item"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>' +
      p.views +
      "</span>" +
      "</div>" +
      '<h3 class="post-card-title"><a href="' +
      href +
      '">' +
      escapeHtml(p.title) +
      "</a></h3>" +
      '<p class="post-card-summary">' +
      escapeHtml(p.summary) +
      "</p>" +
      '<div class="post-card-footer">' +
      '<span style="color:#475569;font-weight:500;">' +
      escapeHtml(p.author) +
      "</span>" +
      '<a href="' +
      href +
      '" class="post-read-more">Đọc tiếp <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>' +
      "</div>" +
      "</div>" +
      "</article>"
    );
  }

  /* ========================================================================
     TRANG DANH SÁCH BÀI VIẾT (posts.html)
     ======================================================================== */
  function initPostsListing() {
    const grid = document.getElementById("postsGrid");
    if (!grid) return;

    const searchInput = document.getElementById("postSearchInput");
    const searchForm = document.getElementById("postSearchForm");
    const resultText = document.getElementById("searchResultText");
    const clearBtn = document.getElementById("clearSearchBtn");
    const paginationEl = document.getElementById("postsPagination");

    const params = new URLSearchParams(window.location.search);
    let currentKeyword = (params.get("keyword") || "").trim();
    let currentPage = Math.max(1, parseInt(params.get("page") || "1", 10) || 1);

    if (searchInput) searchInput.value = currentKeyword;

    function render() {
      const kw = currentKeyword.toLowerCase();
      const filtered = SAMPLE_POSTS.filter(
        (p) =>
          !kw ||
          p.title.toLowerCase().includes(kw) ||
          p.summary.toLowerCase().includes(kw)
      );

      const totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;

      const start = (currentPage - 1) * PER_PAGE;
      const pageItems = filtered.slice(start, start + PER_PAGE);

      if (resultText) {
        resultText.textContent = currentKeyword
          ? 'Tìm thấy ' + filtered.length + ' bài viết cho "' + currentKeyword + '"'
          : "Hiển thị " + filtered.length + " bài viết";
      }
      if (clearBtn) clearBtn.hidden = currentKeyword === "";

      if (!pageItems.length) {
        grid.style.display = "block";
        grid.innerHTML =
          '<div class="empty-state"><h3>Không tìm thấy bài viết phù hợp</h3><p>Hãy thử tìm với từ khóa khác như "sofa", "gỗ sồi", "phòng ngủ" hoặc xóa bộ lọc hiện tại.</p></div>';
      } else {
        grid.style.display = "grid";
        grid.innerHTML = pageItems.map(renderPostCard).join("");
      }

      if (paginationEl) {
        if (totalPages <= 1) {
          paginationEl.innerHTML = "";
        } else {
          let html = '<ul class="pagination-list">';
          html +=
            '<li><button type="button" class="page-btn" data-page="' +
            (currentPage - 1) +
            '" ' +
            (currentPage === 1 ? "disabled" : "") +
            ' aria-label="Trang trước">&lsaquo;</button></li>';
          for (let i = 1; i <= totalPages; i++) {
            html +=
              '<li><button type="button" class="page-btn' +
              (i === currentPage ? " is-active" : "") +
              '" data-page="' +
              i +
              '"' +
              (i === currentPage ? ' aria-current="page"' : "") +
              ">" +
              i +
              "</button></li>";
          }
          html +=
            '<li><button type="button" class="page-btn" data-page="' +
            (currentPage + 1) +
            '" ' +
            (currentPage === totalPages ? "disabled" : "") +
            ' aria-label="Trang sau">&rsaquo;</button></li></ul>';
          paginationEl.innerHTML = html;
        }
      }
    }

    searchForm?.addEventListener("submit", function (e) {
      e.preventDefault();
      currentKeyword = (searchInput?.value || "").trim().slice(0, 100);
      currentPage = 1;
      render();
    });

    clearBtn?.addEventListener("click", function () {
      currentKeyword = "";
      currentPage = 1;
      if (searchInput) searchInput.value = "";
      render();
    });

    paginationEl?.addEventListener("click", function (e) {
      const btn = e.target.closest("[data-page]");
      if (!btn || btn.disabled) return;
      currentPage = parseInt(btn.getAttribute("data-page"), 10);
      render();
      window.scrollTo({ top: 0, behavior: "smooth" });
    });

    renderMiniList("latestPostsList", SAMPLE_POSTS.slice(0, 4));
    renderMiniList(
      "popularPostsList",
      SAMPLE_POSTS.slice().sort((a, b) => b.views - a.views).slice(0, 4)
    );
    render();
  }

  /* ========================================================================
     TRANG CHI TIẾT BÀI VIẾT & BÌNH LUẬN (post-detail.html)
     ======================================================================== */
  function initPostDetail() {
    const container = document.getElementById("articleContainer");
    if (!container) return;

    const params = new URLSearchParams(window.location.search);
    const slug = params.get("slug");
    const post = SAMPLE_POSTS.find((p) => p.slug === slug) || SAMPLE_POSTS[0];

    document.title = post.title + " | Decor E-Commerce";
    document.getElementById("detailBreadcrumbTitle").textContent = post.title;
    document.getElementById("detailTitle").textContent = post.title;
    document.getElementById("detailAuthor").textContent = post.author;
    const dateEl = document.getElementById("detailDate");
    dateEl.textContent = post.dateLabel;
    dateEl.setAttribute("datetime", post.date);
    document.getElementById("detailViews").textContent = post.views + " lượt xem";
    const thumbEl = document.getElementById("detailThumb");
    thumbEl.src = post.thumbnail;
    thumbEl.alt = post.title;
    document.getElementById("detailContent").innerHTML = post.content;

    // Bài viết liên quan
    const relatedGrid = document.getElementById("relatedPostsGrid");
    if (relatedGrid) {
      const related = SAMPLE_POSTS.filter((p) => p.id !== post.id).slice(0, 3);
      relatedGrid.innerHTML = related.map(renderPostCard).join("");
    }
    renderMiniList("latestPostsList", SAMPLE_POSTS.slice(0, 4));

    // Bình luận
    const listEl = document.getElementById("commentList");
    const countEl = document.getElementById("commentCount");
    const guestBox = document.getElementById("guestCommentPrompt");
    const memberBox = document.getElementById("memberCommentBox");
    const authorLabel = document.getElementById("commentAuthorLabel");

    function renderComments() {
      if (countEl) countEl.textContent = String(comments.length);
      if (!listEl) return;
      listEl.innerHTML = comments
        .map(
          (c) =>
            '<article class="comment-item">' +
            '<span class="comment-avatar" aria-hidden="true">' +
            escapeHtml(c.initials) +
            "</span>" +
            "<div>" +
            '<div class="comment-meta">' +
            '<span class="comment-author">' +
            escapeHtml(c.author) +
            "</span>" +
            '<span class="comment-date">' +
            escapeHtml(c.date) +
            "</span>" +
            "</div>" +
            '<p class="comment-body">' +
            escapeHtml(c.content) +
            "</p>" +
            "</div>" +
            "</article>"
        )
        .join("");
    }

    function syncCommentAuth(forcedRole) {
      let session = getSession();
      if (forcedRole === "guest") session = null;
      if (forcedRole === "member") {
        session = { full_name: "Lê Thu Hà", role: "member" };
      }

      if (session) {
        if (guestBox) guestBox.hidden = true;
        if (memberBox) memberBox.hidden = false;
        if (authorLabel) authorLabel.textContent = session.full_name;
      } else {
        if (guestBox) guestBox.hidden = false;
        if (memberBox) memberBox.hidden = true;
      }
    }

    document.querySelectorAll("[data-demo-role]").forEach((btn) => {
      btn.addEventListener("click", function () {
        syncCommentAuth(this.getAttribute("data-demo-role"));
      });
    });

    // Validation onBlur + Focusable Error Summary
    const form = document.getElementById("commentForm");
    const input = document.getElementById("commentInput");
    const errorEl = document.getElementById("commentInputError");
    const charCount = document.getElementById("commentCharCount");
    const summaryEl = document.getElementById("commentErrorSummary");
    const summaryList = document.getElementById("commentErrorList");
    const successEl = document.getElementById("commentSuccessBanner");

    function validateCommentField() {
      const val = (input?.value || "").trim();
      if (val.length < 5) {
        input.classList.add("is-invalid");
        errorEl.textContent = "Nội dung bình luận phải có ít nhất 5 ký tự.";
        errorEl.classList.add("is-visible");
        return "Nội dung bình luận phải có ít nhất 5 ký tự.";
      }
      if (val.length > 1000) {
        input.classList.add("is-invalid");
        errorEl.textContent = "Nội dung bình luận tối đa 1000 ký tự.";
        errorEl.classList.add("is-visible");
        return "Nội dung bình luận tối đa 1000 ký tự.";
      }
      input.classList.remove("is-invalid");
      errorEl.textContent = "";
      errorEl.classList.remove("is-visible");
      return null;
    }

    input?.addEventListener("input", function () {
      if (charCount) charCount.textContent = input.value.length + " / 1000";
    });
    input?.addEventListener("blur", validateCommentField);

    form?.addEventListener("submit", function (e) {
      e.preventDefault();
      if (successEl) successEl.classList.remove("is-visible");

      const err = validateCommentField();
      if (err) {
        summaryList.innerHTML = '<li><a href="#commentInput">' + escapeHtml(err) + "</a></li>";
        summaryEl.classList.add("is-visible");
        summaryEl.focus();
        return;
      }

      summaryEl.classList.remove("is-visible");
      const authorName = authorLabel?.textContent || "Thành viên";
      const initials = authorName
        .split(/\s+/)
        .map((w) => w[0])
        .join("")
        .slice(0, 2)
        .toUpperCase();

      comments.unshift({
        id: Date.now(),
        initials: initials,
        author: authorName,
        date: "Vừa xong",
        content: input.value.trim(),
      });

      input.value = "";
      if (charCount) charCount.textContent = "0 / 1000";
      renderComments();

      if (successEl) {
        successEl.textContent = "Bình luận của bạn đã được gửi thành công!";
        successEl.classList.add("is-visible");
      }
    });

    renderComments();
    syncCommentAuth();
  }

  document.addEventListener("DOMContentLoaded", function () {
    renderHeaderAuth();
    initPostsListing();
    initPostDetail();
  });
})();
