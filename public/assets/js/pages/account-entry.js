/* Điểm vào từ ui/account khi project được phục vụ từ thư mục gốc bằng PHP. */
(() => {
    if (!/^https?:$/.test(window.location.protocol)) return;
    const route = document.body.dataset.accountRoute;
    if (!["/account/profile", "/account/change-password"].includes(route)) return;
    const target = new URL("../../public/index.php", window.location.href);
    target.searchParams.set("route", route);
    window.location.replace(target.href);
})();
