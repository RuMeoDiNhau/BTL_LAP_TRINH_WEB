(function () {
  "use strict";

  const ROLE_MATRICES = {
    admin: {
      title: "Quyền của vai trò Quản trị viên (admin)",
      badge: "20/20 quyền được cấp • Chỉ xem",
      modules: [
        {
          icon: "&#128101;",
          name: "Xác thực & Phân quyền tài khoản",
          desc: "Quản lý phân quyền hệ thống và kiểm soát truy cập theo vai trò",
          perms: { view: true, create: true, edit: true, delete: true },
        },
        {
          icon: "&#128240;",
          name: "Tin tức & Bài viết (Phần #4)",
          desc: "Quản lý bài viết, từ khóa SEO, upload ảnh đại diện & WYSIWYG",
          perms: { view: true, create: true, edit: true, delete: true },
        },
        {
          icon: "&#128172;",
          name: "Bình luận & Đánh giá (Phần #4)",
          desc: "Gửi bình luận và kiểm duyệt (duyệt / ẩn / xóa) bình luận trên bài viết",
          perms: { view: true, create: true, edit: true, delete: true },
        },
        {
          icon: "&#128717;",
          name: "Sản phẩm & Đơn hàng (Phần #3)",
          desc: "Danh mục, sản phẩm, giỏ hàng và xử lý trạng thái đơn hàng",
          perms: { view: true, create: true, edit: true, delete: true },
        },
        {
          icon: "&#9881;",
          name: "Cấu hình, Liên hệ & Hỏi/Đáp (Phần #1, #2)",
          desc: "Thông tin công ty, nội dung trang chủ/giới thiệu, liên hệ và FAQ",
          perms: { view: true, create: true, edit: true, delete: true },
        },
      ],
    },
    member: {
      title: "Quyền của vai trò Thành viên (member)",
      badge: "6/20 quyền được cấp • Chỉ xem",
      modules: [
        {
          icon: "&#128101;",
          name: "Xác thực & Tài khoản cá nhân",
          desc: "Đăng nhập, đăng xuất và sử dụng quyền thành viên trên trang khách",
          perms: { view: true, create: false, edit: true, delete: false },
        },
        {
          icon: "&#128240;",
          name: "Tin tức & Bài viết (Phần #4)",
          desc: "Xem danh sách, tìm kiếm theo từ khóa và đọc chi tiết bài viết đã xuất bản",
          perms: { view: true, create: false, edit: false, delete: false },
        },
        {
          icon: "&#128172;",
          name: "Bình luận & Đánh giá (Phần #4)",
          desc: "Xem bình luận đã duyệt và gửi bình luận mới trên bài viết",
          perms: { view: true, create: true, edit: false, delete: false },
        },
        {
          icon: "&#128717;",
          name: "Sản phẩm & Đơn hàng (Phần #3)",
          desc: "Xem sản phẩm, thêm vào giỏ hàng và tạo đơn đặt hàng mới",
          perms: { view: true, create: true, edit: false, delete: false },
        },
        {
          icon: "&#9881;",
          name: "Cấu hình quản trị hệ thống",
          desc: "Thay đổi thông tin website, quản lý liên hệ và FAQ trong trang Admin",
          perms: { view: false, create: false, edit: false, delete: false },
        },
      ],
    },
  };

  function renderCell(allowed) {
    return allowed
      ? '<td class="ta-center"><span class="ta-perm-box ta-perm-box--allowed" title="Được phép">&#10003;</span></td>'
      : '<td class="ta-center"><span class="ta-perm-box ta-perm-box--denied" title="Bị giới hạn">&minus;</span></td>';
  }

  function renderMatrix(roleKey) {
    const data = ROLE_MATRICES[roleKey];
    const tbody = document.getElementById("permissionMatrixBody");
    const titleEl = document.getElementById("matrixRoleTitle");
    const badgeEl = document.getElementById("matrixModeBadge");
    if (!data || !tbody) return;

    if (titleEl) titleEl.textContent = data.title;
    if (badgeEl) badgeEl.textContent = data.badge;

    tbody.innerHTML = data.modules
      .map(
        (m) =>
          "<tr><td><div class=\"ta-module-cell\"><span class=\"ta-module-icon\" aria-hidden=\"true\">" +
          m.icon +
          "</span><div><div class=\"ta-module-name\">" +
          m.name +
          "</div><div class=\"ta-module-desc\">" +
          m.desc +
          "</div></div></div></td>" +
          renderCell(m.perms.view) +
          renderCell(m.perms.create) +
          renderCell(m.perms.edit) +
          renderCell(m.perms.delete) +
          "</tr>"
      )
      .join("");
  }

  document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll("[data-role-card]");
    if (!cards.length) return;

    cards.forEach((card) => {
      card.addEventListener("click", function () {
        const key = this.getAttribute("data-role-card");
        cards.forEach((c) => {
          const active = c === this;
          c.classList.toggle("is-selected", active);
          c.setAttribute("aria-pressed", active ? "true" : "false");
        });
        renderMatrix(key);
      });
    });

    renderMatrix("admin");
  });
})();
