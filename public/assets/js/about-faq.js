/* DECOR - JS tối thiểu: Swiper testimonials, search overlay, active menu. */
(function () {
    'use strict';

    // 1. Testimonials chạy bằng Swiper (slidesPerView = 4 như mẫu ở màn hình lớn).
    var slider = document.querySelector('.dc-testimonial-swiper');
    if (slider && typeof window.Swiper === 'function') {
        new Swiper(slider, {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            pagination: { el: '.dc-testimonial-pagination', clickable: true },
            breakpoints: {
                768: { slidesPerView: 2 },
                992: { slidesPerView: 4 }
            }
        });
    }

    // 2. Đóng/mở ô tìm kiếm.
    var panel = document.getElementById('dc-search');
    var openBtn = document.querySelector('[data-dc-search-open]');
    var closeBtn = panel && panel.querySelector('[data-dc-search-close]');

    function showSearch(open) {
        if (!panel) {
            return;
        }
        panel.hidden = !open;
        document.body.classList.toggle('dc-search-open', open);
        if (openBtn) {
            openBtn.setAttribute('aria-expanded', String(open));
        }
        if (open) {
            var input = panel.querySelector('input[type="search"]');
            if (input) {
                input.focus();
            }
        }
    }

    if (openBtn) {
        openBtn.addEventListener('click', function () { showSearch(true); });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', function () { showSearch(false); });
    }
    if (panel) {
        panel.addEventListener('click', function (event) {
            if (event.target === panel) {
                showSearch(false);
            }
        });
    }
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            showSearch(false);
        }
    });

    // 3. Đánh dấu menu active (server đã làm; đây chỉ là fallback khi đổi route).
    document.querySelectorAll('.dc-nav .nav-link').forEach(function (link) {
        if (new URL(link.href, location.href).pathname === location.pathname) {
            link.classList.add('active');
            link.setAttribute('aria-current', 'page');
        }
    });
}());
