(function () {
    'use strict';

    for (const link of document.querySelectorAll('.shop-gallery__thumbs a')) {
        link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
                return;
            }
            const image = link.closest('.shop-gallery').querySelector('.shop-gallery__primary img');
            if (image) {
                event.preventDefault();
                image.src = link.href;
            }
        });
    }

    for (const form of document.querySelectorAll('form[data-shop-confirm]')) {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.shopConfirm)) {
                event.preventDefault();
            }
        });
    }
}());
