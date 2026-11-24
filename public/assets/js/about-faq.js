/* ===== #2 About/FAQ ===== JS thuần: Swiper testimonial + menu drawer mobile. */
(function () {
    'use strict';

    // 1. Testimonials chạy bằng Swiper (4 slide ở màn hình lớn).
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

    // 2. Menu drawer trên mobile: aria-expanded, đóng bằng link/backdrop/Esc/resize.
    var root = document.querySelector('.dc-shell');
    var toggle = root ? root.querySelector('.dc-shell-toggle') : null;
    var panel = document.getElementById('dcShellMenu');
    var backdrop = root ? root.querySelector('.dc-shell-backdrop') : null;
    if (!toggle || !panel) {
        return;
    }

    function setOpen(open) {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (backdrop) {
            backdrop.hidden = !open;
        }
        if (root) {
            root.classList.toggle('dc-shell-menu-open', open);
            // Đóng dropdown search/account để không chồng lên drawer.
            var details = root.querySelectorAll('.dc-shell-menu[open]');
            for (var i = 0; i < details.length; i++) {
                details[i].open = false;
            }
        }
    }

    toggle.addEventListener('click', function () {
        setOpen(panel.hidden);
    });

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            setOpen(false);
        });
    }

    var links = panel.querySelectorAll('a[href]');
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function () {
            setOpen(false);
        });
    }

    document.addEventListener('keydown', function (event) {
        if (!panel.hidden && (event.key === 'Escape' || event.keyCode === 27)) {
            setOpen(false);
            toggle.focus();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992 && !panel.hidden) {
            setOpen(false);
        }
    });
}());
