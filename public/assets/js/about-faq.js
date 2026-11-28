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
    var menus = root.querySelectorAll('.dc-shell-menu');

    function closeMenus(except) {
        menus.forEach(function (menu) {
            if (menu !== except) {
                menu.open = false;
            }
        });
    }

    function setOpen(open) {
        panel.hidden = !open;
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        if (backdrop) {
            backdrop.hidden = !open;
        }
        if (root) {
            root.classList.toggle('dc-shell-menu-open', open);
        }
        if (open) {
            closeMenus(null);
        }
    }

    menus.forEach(function (menu) {
        menu.addEventListener('toggle', function () {
            if (menu.open) {
                setOpen(false);
                closeMenus(menu);
            }
        });
    });

    document.addEventListener('click', function (event) {
        menus.forEach(function (menu) {
            if (menu.open && !menu.contains(event.target)) {
                menu.open = false;
            }
        });
    });

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
        if (event.key !== 'Escape') {
            return;
        }
        if (!panel.hidden) {
            setOpen(false);
            toggle.focus();
            return;
        }
        var openMenu = root.querySelector('.dc-shell-menu[open]');
        if (openMenu) {
            openMenu.open = false;
            openMenu.querySelector('summary').focus();
        }
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992 && !panel.hidden) {
            setOpen(false);
        }
    });
}());
