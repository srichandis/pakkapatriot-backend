import './bootstrap';
import initPanchanga from './panchanga';

/**
 * Pakka Patriot — front-end behaviour.
 *
 * Everything stateful now lives in Livewire components (cart, product modal,
 * Join the Journey, newsletter, site search, shop filters). What remains here
 * is presentation-only behaviour that would be wasted round-trips on the
 * server: the mobile menu, the video lightbox, carousels, the game-server
 * probe, debounced story search and scroll helpers.
 */
(function () {
    'use strict';

    const $ = (sel, root = document) => root.querySelector(sel);
    const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

    const show = (el) => el?.classList.remove('hidden');
    const hide = (el) => el?.classList.add('hidden');

    /* ─── Video lightbox ───────────────────────────────────────────────── */

    const openVideo = () => {
        const frame = $('#videoFrame');
        if (frame) frame.src = 'https://www.youtube.com/embed/S70tC0A6wVw?autoplay=1';
        show($('#videoModal'));
    };

    const closeVideo = () => {
        const frame = $('#videoFrame');
        if (frame) frame.src = '';
        hide($('#videoModal'));
    };

    /* ─── Shop carousel ────────────────────────────────────────────────── */

    const initShopCarousel = () => {
        const section = $('[data-shop-carousel]');
        if (!section) return;

        const slidesPerView = 4;
        const totalPages = parseInt(section.dataset.totalPages || '1', 10);
        if (totalPages <= 1) return;

        const slides = $$('[data-shop-slide]', section);
        const dots = $$('[data-shop-dot]', section);
        const status = $('[data-shop-status]', section);
        const pageLabel = $('[data-shop-page]', section);
        let page = 0;
        let timer = null;

        const paint = () => {
            slides.forEach((slide, i) => {
                const visible = i >= page * slidesPerView && i < (page + 1) * slidesPerView;
                slide.classList.toggle('hidden', !visible);
            });
            dots.forEach((dot, i) => {
                dot.className =
                    'h-2 rounded-full transition-all duration-300 ' +
                    (i === page ? 'w-8 bg-[#F6B828]' : 'w-2 bg-[#E4DCB9] hover:bg-[#F6B828]/50');
            });
            if (pageLabel) pageLabel.textContent = page + 1;
        };

        const goTo = (next) => {
            page = next < 0 ? totalPages - 1 : next >= totalPages ? 0 : next;
            paint();
        };

        const start = () => {
            timer = setInterval(() => goTo(page + 1), 4500);
        };

        const stop = () => {
            if (timer) clearInterval(timer);
            timer = null;
        };

        $('[data-shop-prev]', section)?.addEventListener('click', () => goTo(page - 1));
        $('[data-shop-next]', section)?.addEventListener('click', () => goTo(page + 1));
        dots.forEach((dot, i) => dot.addEventListener('click', () => goTo(i)));

        section.addEventListener('mouseenter', () => {
            stop();
            if (status) status.textContent = '⏸ PAUSED';
        });
        section.addEventListener('mouseleave', () => {
            if (status) status.textContent = '▶ AUTO-SCROLLING';
            start();
        });

        paint();
        start();
    };

    /* ─── Stories carousel ─────────────────────────────────────────────── */

    const initStoriesCarousel = () => {
        const carousel = $('[data-stories-carousel]');
        if (!carousel) return;

        const track = $('[data-stories-track]', carousel);
        if (!track) return;

        const scroll = (direction) => {
            const amount = track.clientWidth * 0.8;
            track.scrollTo({
                left: direction === 'left' ? track.scrollLeft - amount : track.scrollLeft + amount,
                behavior: 'smooth',
            });
        };

        $('[data-stories-prev]', carousel)?.addEventListener('click', () => scroll('left'));
        $('[data-stories-next]', carousel)?.addEventListener('click', () => scroll('right'));
    };

    /* ─── Story listing search (filters as you type) ───────────────────── */

    const initBlogSearch = () => {
        const form = $('[data-blog-search]');
        if (!form) return;

        const input = $('input[name="q"]', form);
        if (!input) return;

        let timer = null;

        input.addEventListener('input', () => {
            clearTimeout(timer);
            // Debounced so a word is not requested per keystroke.
            timer = setTimeout(() => form.requestSubmit(), 500);
        });
    };

    /* ─── Toast (the create pages' "coming soon" feedback) ─────────────── */

    let toastTimer = null;

    const showToast = (message) => {
        let toast = $('#ppToast');

        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'ppToast';
            toast.setAttribute('role', 'status');
            toast.className =
                'fixed bottom-6 left-1/2 -translate-x-1/2 z-50 bg-[#0A2240] text-white text-xs sm:text-sm font-bold px-6 py-3.5 rounded-2xl shadow-2xl flex items-center gap-2 max-w-[92vw] text-center';
            document.body.appendChild(toast);
        }

        toast.innerHTML =
            '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" ' +
            'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="shrink-0 text-[#F6B828]">' +
            '<path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>' +
            '</svg><span></span>';
        toast.querySelector('span').textContent = message;
        toast.classList.remove('hidden');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.add('hidden'), 3200);
    };

    /* ─── Game shell (server probe + join link, mirrors the game pages) ── */

    const initGameShell = () => {
        const shell = $('[data-game-shell]');
        if (!shell) return;

        const server = shell.dataset.gameServer;
        const status = $('#gameServerStatus');
        const banner = $('#gameServerBanner');

        const paint = (ready) => {
            if (status) {
                status.className =
                    'hidden md:flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1 rounded-full ' +
                    (ready ? 'bg-[#1E4D2B] text-[#7CE38B]' : 'bg-[#4D1B1B] text-[#FF9B9B]');
                status.title = ready
                    ? `Online rooms available via ${server}`
                    : `No game server at ${server} — online mode will be offline.`;
                status.innerHTML =
                    `<span class="w-1.5 h-1.5 rounded-full ${ready ? 'bg-[#7CE38B] animate-pulse' : 'bg-[#FF9B9B]'}"></span>` +
                    `<span>${ready ? 'Online server' : 'Offline mode'}</span>`;
            }

            if (!ready && banner) {
                banner.classList.remove('hidden');
                banner.classList.add('flex');
            }
        };

        // Share only the room join link — never bake the server address into it.
        $('#gameCopyLink')?.addEventListener('click', async () => {
            const label = $('#gameCopyLabel');
            const icon = $('#gameCopyIcon');
            const room = new URLSearchParams(window.location.search).get('room');
            const url = `${window.location.origin}${window.location.pathname}${room ? `?room=${room.toUpperCase()}` : ''}`;

            try {
                await navigator.clipboard.writeText(url);
                if (label) label.textContent = 'Copied!';
                icon?.classList.add('text-[#7CE38B]');
                setTimeout(() => {
                    if (label) label.textContent = 'Copy link';
                    icon?.classList.remove('text-[#7CE38B]');
                }, 1500);
            } catch { /* clipboard unavailable */ }
        });

        $('#gameServerBannerClose')?.addEventListener('click', () => {
            banner?.classList.add('hidden');
            banner?.classList.remove('flex');
        });

        // Lightweight reachability probe: one socket, with a 3.5s timeout.
        let socket = null;
        let timer = null;

        const settle = (ready) => {
            clearTimeout(timer);
            paint(ready);
            try { socket?.close(); } catch { /* already closing */ }
        };

        try {
            socket = new WebSocket(server);
            socket.onopen = () => settle(true);
            socket.onerror = () => settle(false);
            timer = setTimeout(() => settle(false), 3500);
        } catch {
            paint(false);
        }
    };

    /* ─── Boot ─────────────────────────────────────────────────────────── */

    document.addEventListener('DOMContentLoaded', () => {
        // Mobile menu
        const menuToggle = $('#mobileMenuToggle');
        const menuPanel = $('#mobileMenuPanel');
        menuToggle?.addEventListener('click', () => {
            const open = menuPanel.classList.toggle('hidden');
            menuToggle.setAttribute('aria-expanded', String(!open));
            $('#mobileMenuIcon')?.classList.toggle('hidden', !open);
            $('#mobileMenuCloseIcon')?.classList.toggle('hidden', open);
        });

        // Video lightbox
        $$('[data-video-open]').forEach((el) => el.addEventListener('click', openVideo));
        $$('[data-video-close]').forEach((el) => el.addEventListener('click', closeVideo));
        $('#videoModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'videoModal') closeVideo();
        });

        // Buttons that only acknowledge a click (the create pages' "coming soon")
        document.addEventListener('click', (event) => {
            const trigger = event.target.closest('[data-toast]');
            if (!trigger) return;
            event.preventDefault();
            showToast(trigger.dataset.toast);
        });

        // Escape closes the lightbox and asks the Livewire overlays to close.
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;

            closeVideo();

            if (window.Livewire) {
                window.Livewire.dispatch('close-cart');
                window.Livewire.dispatch('close-product');
                window.Livewire.dispatch('close-journey');
            }
        });

        // Smooth scroll helpers
        $$('[data-scroll-top]').forEach((el) =>
            el.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
        );
        $$('[data-scroll-to]').forEach((el) =>
            el.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById(el.dataset.scrollTo)?.scrollIntoView({ behavior: 'smooth' });
            })
        );

        initBlogSearch();
        initGameShell();
        initPanchanga();
        initShopCarousel();
        initStoriesCarousel();
    });
})();
