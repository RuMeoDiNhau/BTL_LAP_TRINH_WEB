/**
 * DECOR E-COMMERCE - ROLES & PERMISSIONS MATRIX + RBAC SESSION DEMO
 * Phục vụ: ui/admin/roles.html
 * Hiển thị ma trận quyền hạn của 2 vai trò trong schema.sql:
 *  - Quản trị viên (admin): 20/20 quyền
 *  - Thành viên (member): 6/20 quyền
 * Đồng thời đồng bộ với phiên đăng nhập từ login.html / register.html (decor_auth_session)
 */

(function () {
  "use strict";

  const STORAGE_SESSION_KEY = "decor_auth_session";

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

  function getSession() {
    try {
      const raw = localStorage.getItem(STORAGE_SESSION_KEY);
      return raw ? JSON.parse(raw) : null;
    } catch (err) {
      return null;
    }
  }

  function setSimulatedSession(type) {
    if (type === "guest") {
      localStorage.removeItem(STORAGE_SESSION_KEY);
      window.location.href = "../login.html?reason=unauthenticated";
      return;
    }
    const sample =
      type === "admin"
        ? {
            id: 1,
            initials: "MA",
            full_name: "Nguyễn Minh Anh",
            email: "admin@decor.vn",
            role: "admin",
            status: "active",
          }
        : {
            id: 2,
            initials: "TH",
            full_name: "Lê Thu Hà",
            email: "customer@decor.vn",
            role: "member",
            status: "active",
          };
    localStorage.setItem(STORAGE_SESSION_KEY, JSON.stringify(sample));
    syncSessionUI();
  }

  function renderCell(allowed) {
    if (allowed) {
      return '<td class="ta-center"><span class="ta-perm-box ta-perm-box--allowed" title="Được phép">&#10003;</span></td>';
    }
    return '<td class="ta-center"><span class="ta-perm-box ta-perm-box--denied" title="Bị giới hạn">&minus;</span></td>';
  }

  function selectRoleCard(roleKey) {
    const cards = document.querySelectorAll("[data-role-card]");
    cards.forEach((c) => {
      const active = c.getAttribute("data-role-card") === roleKey;
      c.classList.toggle("is-selected", active);
      c.setAttribute("aria-pressed", active ? "true" : "false");
    });
    renderMatrix(roleKey);
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
          "<tr>" +
          "<td>" +
          '<div class="ta-module-cell">' +
          '<span class="ta-module-icon" aria-hidden="true">' +
          m.icon +
          "</span>" +
          "<div>" +
          '<div class="ta-module-name">' +
          m.name +
          "</div>" +
          '<div class="ta-module-desc">' +
          m.desc +
          "</div>" +
          "</div>" +
          "</div>" +
          "</td>" +
          renderCell(m.perms.view) +
          renderCell(m.perms.create) +
          renderCell(m.perms.edit) +
          renderCell(m.perms.delete) +
          "</tr>"
      )
      .join("");
  }

  function syncSessionUI() {
    const session = getSession() || {
      initials: "MA",
      full_name: "Nguyễn Minh Anh",
      email: "admin@decor.vn",
      role: "admin",
    };

    const isAdmin = session.role === "admin";
    const sidebarAvatar = document.getElementById("sidebarUserAvatar");
    const topbarAvatar = document.getElementById("topbarUserAvatar");
    const sidebarName = document.getElementById("sidebarUserName");
    const sidebarRole = document.getElementById("sidebarUserRole");
    const roleBadge = document.getElementById("currentSessionRoleBadge");
    const guardNotice = document.getElementById("rbacGuardNotice");
    const guardText = document.getElementById("rbacGuardText");

    if (sidebarAvatar) sidebarAvatar.textContent = session.initials || "TV";
    if (topbarAvatar) topbarAvatar.textContent = session.initials || "TV";
    if (sidebarName) sidebarName.textContent = session.full_name;
    if (sidebarRole) {
      sidebarRole.textContent = isAdmin ? "Quản trị viên" : "Thành viên";
    }

    if (roleBadge) {
      roleBadge.className = isAdmin
        ? "ta-badge ta-badge--admin"
        : "ta-badge ta-badge--member";
      roleBadge.textContent =
        "Đang đăng nhập: " +
        session.full_name +
        " (" +
        (isAdmin ? "admin" : "member") +
        ")";
    }

    if (guardNotice && guardText) {
      if (isAdmin) {
        guardNotice.style.borderLeftColor = "#4640de";
        guardText.innerHTML =
          "<strong>Trạng thái phân quyền:</strong> Tài khoản <strong>" +
          session.email +
          "</strong> có vai trò <strong>Quản trị viên (admin)</strong> – được cấp 20/20 quyền trên toàn hệ thống.";
      } else {
        guardNotice.style.borderLeftColor = "#b54708";
        guardText.innerHTML =
          "<strong>Cảnh báo phân quyền:</strong> Tài khoản <strong>" +
          session.email +
          "</strong> có vai trò <strong>Thành viên (member)</strong> – chỉ có 6/20 quyền (không được phép thêm/sửa/xóa dữ liệu trong khu vực Quản trị). " +
          '<a href="../login.html?reason=forbidden">Đăng nhập bằng tài khoản Quản trị viên &rarr;</a>';
      }
    }

    // Tự động chọn đúng thẻ vai trò của người đang đăng nhập
    selectRoleCard(isAdmin ? "admin" : "member");
  }

  document.addEventListener("DOMContentLoaded", function () {
    const cards = document.querySelectorAll("[data-role-card]");
    cards.forEach((card) => {
      card.addEventListener("click", function () {
        selectRoleCard(this.getAttribute("data-role-card"));
      });
    });

    document.querySelectorAll("[data-sim-session]").forEach((btn) => {
      btn.addEventListener("click", function () {
        setSimulatedSession(this.getAttribute("data-sim-session"));
      });
    });

    syncSessionUI();
  });
})();
