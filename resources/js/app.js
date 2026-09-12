import './bootstrap';

(function () {
    let overlay;

    function ensureOverlay() {
        if (overlay) return overlay;

        overlay = document.createElement('div');
        overlay.className = 'lightbox';
        overlay.innerHTML =
            '<div class="lightbox__backdrop" data-lightbox-close></div>' +
            '<div class="lightbox__box">' +
            '<button type="button" class="lightbox__close" data-lightbox-close aria-label="Close">&times;</button>' +
            '<img class="lightbox__img" src="" alt="">' +
            '<p class="lightbox__caption"></p>' +
            '</div>';

        document.body.appendChild(overlay);

        overlay.addEventListener('click', function (e) {
            if (e.target.hasAttribute('data-lightbox-close')) closeLightbox();
        });

        return overlay;
    }

    function openLightbox(src, caption) {
        const box = ensureOverlay();
        const img = box.querySelector('.lightbox__img');
        const cap = box.querySelector('.lightbox__caption');

        img.src = src;
        img.alt = caption || '';
        cap.textContent = caption || '';

        box.classList.add('is-open');
        document.body.classList.add('lightbox-open');
    }

    function closeLightbox() {
        if (!overlay) return;
        overlay.classList.remove('is-open');
        document.body.classList.remove('lightbox-open');
    }

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-lightbox]');
        if (!trigger) return;

        e.preventDefault();
        e.stopPropagation();

        openLightbox(trigger.currentSrc || trigger.src, trigger.getAttribute('data-lightbox'));
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeLightbox();
    });
})();
