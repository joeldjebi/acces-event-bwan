const revealItems = document.querySelectorAll('[data-reveal]');

if (revealItems.length > 0) {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-visible'));
    } else {
        const revealObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                });
            },
            {
                rootMargin: '0px 0px -12% 0px',
                threshold: 0.16,
            },
        );

        revealItems.forEach((item) => revealObserver.observe(item));
    }
}

// Menu mobile : ouverture/fermeture du menu principal via le bouton burger.
const navToggle = document.querySelector('.nav-toggle');

if (navToggle) {
    const header = navToggle.closest('.site-header-wrap');

    const setMenuOpen = (open) => {
        header.classList.toggle('is-menu-open', open);
        navToggle.setAttribute('aria-expanded', String(open));
        navToggle.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
    };

    navToggle.addEventListener('click', () => {
        setMenuOpen(!header.classList.contains('is-menu-open'));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && header.classList.contains('is-menu-open')) {
            setMenuOpen(false);
            navToggle.focus();
        }
    });
}

// ---------------------------------------------------------------------
// Animations de l'accueil, façon Apple : tout est piloté par le défilement.
// Chaque effet est déclaré en HTML par un attribut data-* ; sans JavaScript
// (ou avec « réduire les animations »), le contenu reste affiché normalement.
// ---------------------------------------------------------------------
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const clamp01 = (value) => Math.min(1, Math.max(0, value));
const scrollScenes = [];

// Photo qui s'agrandit : --p passe de 0 (bas de l'écran) à 1 (haut de la photo à 20 % de l'écran).
document.querySelectorAll('[data-scroll-scale]').forEach((element) => {
    scrollScenes.push(() => {
        const { top } = element.getBoundingClientRect();
        element.style.setProperty('--p', clamp01((window.innerHeight - top) / (window.innerHeight * 0.8)).toFixed(3));
    });
});

// Ligne de progression : se remplit à mesure que la liste traverse le milieu de l'écran.
document.querySelectorAll('[data-scroll-progress]').forEach((element) => {
    scrollScenes.push(() => {
        const { top, height } = element.getBoundingClientRect();
        element.style.setProperty('--p', clamp01((window.innerHeight * 0.6 - top) / height).toFixed(3));
    });
});

// Manifeste : chaque mot s'illumine au fil du défilement.
document.querySelectorAll('[data-word-reveal]').forEach((element) => {
    const words = element.textContent.trim().split(/\s+/);
    element.textContent = '';
    words.forEach((word) => {
        const span = document.createElement('span');
        span.textContent = word;
        element.append(span, ' ');
    });

    const spans = [...element.querySelectorAll('span')];

    scrollScenes.push(() => {
        const { top, height } = element.getBoundingClientRect();
        const progress = clamp01((window.innerHeight * 0.85 - top) / (height + window.innerHeight * 0.3));
        const litCount = Math.round(progress * spans.length);
        spans.forEach((span, index) => span.classList.toggle('is-lit', index < litCount));
    });
});

if (reduceMotion) {
    document.querySelectorAll('[data-scroll-scale], [data-scroll-progress]').forEach((element) => element.style.setProperty('--p', '1'));
    document.querySelectorAll('[data-word-reveal] span').forEach((span) => span.classList.add('is-lit'));
} else if (scrollScenes.length > 0) {
    let ticking = false;

    const render = () => {
        scrollScenes.forEach((scene) => scene());
        ticking = false;
    };

    const requestRender = () => {
        if (!ticking) {
            ticking = true;
            window.requestAnimationFrame(render);
        }
    };

    window.addEventListener('scroll', requestRender, { passive: true });
    window.addEventListener('resize', requestRender);
    render();
}

// Compteurs : les chiffres défilent de 0 à leur valeur lorsqu'ils apparaissent.
const counters = document.querySelectorAll('[data-count-to]');

if (counters.length > 0 && !reduceMotion && 'IntersectionObserver' in window) {
    const counterObserver = new IntersectionObserver(
        (entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                observer.unobserve(entry.target);
                const target = Number(entry.target.dataset.countTo);
                const start = performance.now();
                const duration = 1400;

                const tick = (now) => {
                    const progress = Math.min(1, (now - start) / duration);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    entry.target.textContent = String(Math.round(target * eased));

                    if (progress < 1) {
                        window.requestAnimationFrame(tick);
                    }
                };

                window.requestAnimationFrame(tick);
            });
        },
        { threshold: 0.6 },
    );

    counters.forEach((counter) => counterObserver.observe(counter));
}

// Carrousel : boutons précédent / suivant, désactivés aux extrémités.
document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const section = carousel.closest('section');
    const previousButton = section?.querySelector('[data-carousel-prev]');
    const nextButton = section?.querySelector('[data-carousel-next]');

    if (!previousButton || !nextButton) {
        return;
    }

    const step = () => {
        const card = carousel.firstElementChild;
        const gap = parseFloat(getComputedStyle(carousel).columnGap) || 0;

        return card ? card.getBoundingClientRect().width + gap : carousel.clientWidth;
    };

    const updateButtons = () => {
        previousButton.disabled = carousel.scrollLeft <= 4;
        nextButton.disabled = carousel.scrollLeft + carousel.clientWidth >= carousel.scrollWidth - 4;
    };

    previousButton.addEventListener('click', () => carousel.scrollBy({ left: -step(), behavior: reduceMotion ? 'auto' : 'smooth' }));
    nextButton.addEventListener('click', () => carousel.scrollBy({ left: step(), behavior: reduceMotion ? 'auto' : 'smooth' }));
    carousel.addEventListener('scroll', updateButtons, { passive: true });
    window.addEventListener('resize', updateButtons);
    updateButtons();
});

// ---------------------------------------------------------------------
// Grande photo interactive de l'accueil.
// ---------------------------------------------------------------------

// Inclinaison 3D et reflet qui suivent la souris (souris uniquement, pas au toucher).
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

document.querySelectorAll('[data-tilt]').forEach((frame) => {
    if (reduceMotion || !finePointer) {
        return;
    }

    const target = { x: 0, y: 0 };
    const current = { x: 0, y: 0 };
    let animating = false;

    const animate = () => {
        // Interpolation douce vers la position visée.
        current.x += (target.x - current.x) * 0.08;
        current.y += (target.y - current.y) * 0.08;
        frame.style.setProperty('--tx', current.x.toFixed(4));
        frame.style.setProperty('--ty', current.y.toFixed(4));
        frame.style.setProperty('--gx', `${((current.x + 1) * 50).toFixed(2)}%`);
        frame.style.setProperty('--gy', `${((current.y + 1) * 50).toFixed(2)}%`);

        if (Math.abs(target.x - current.x) > 0.001 || Math.abs(target.y - current.y) > 0.001) {
            window.requestAnimationFrame(animate);
        } else {
            animating = false;
        }
    };

    const start = () => {
        if (!animating) {
            animating = true;
            window.requestAnimationFrame(animate);
        }
    };

    frame.addEventListener('pointermove', (event) => {
        const rect = frame.getBoundingClientRect();
        target.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
        target.y = ((event.clientY - rect.top) / rect.height) * 2 - 1;
        frame.classList.add('is-hovered');
        start();
    });

    frame.addEventListener('pointerleave', () => {
        target.x = 0;
        target.y = 0;
        frame.classList.remove('is-hovered');
        start();
    });
});

// Points interactifs : survol, clic (mobile) et clavier ; un seul ouvert à la fois.
const hotspots = [...document.querySelectorAll('.hotspot')];

const closeHotspots = (except = null) => {
    hotspots.forEach((hotspot) => {
        if (hotspot !== except) {
            hotspot.classList.remove('is-open');
            hotspot.querySelector('.hotspot-dot')?.setAttribute('aria-expanded', 'false');
        }
    });
};

hotspots.forEach((hotspot) => {
    const dot = hotspot.querySelector('.hotspot-dot');

    dot?.addEventListener('click', () => {
        const open = !hotspot.classList.contains('is-open');
        closeHotspots(hotspot);
        hotspot.classList.toggle('is-open', open);
        dot.setAttribute('aria-expanded', String(open));
    });
});

if (hotspots.length > 0) {
    document.addEventListener('click', (event) => {
        if (!event.target.closest('.hotspot')) {
            closeHotspots();
        }
    });

    document.addEventListener('keydown', (event) => {
        const open = hotspots.find((hotspot) => hotspot.classList.contains('is-open'));

        if (event.key === 'Escape' && open) {
            closeHotspots();
            open.querySelector('.hotspot-dot')?.focus();
        }
    });
}

// Fil d'activité : les messages défilent l'un après l'autre.
document.querySelectorAll('[data-live-feed]').forEach((element) => {
    const messages = element.dataset.liveFeed.split('|');

    if (reduceMotion || messages.length < 2) {
        return;
    }

    let index = 0;

    window.setInterval(() => {
        element.classList.add('is-switching');

        window.setTimeout(() => {
            index = (index + 1) % messages.length;
            element.textContent = messages[index];
            element.classList.remove('is-switching');
        }, 300);
    }, 2800);
});

// Filtres de la page Solutions : affiche les cartes de la famille choisie.
document.querySelectorAll('[data-filter-group]').forEach((filterGroup) => {
    const list = filterGroup.parentElement.querySelector('[data-filter-list]');
    const status = filterGroup.parentElement.querySelector('[data-filter-status]');

    if (!list) {
        return;
    }

    const cards = [...list.children];

    filterGroup.addEventListener('click', (event) => {
        const button = event.target.closest('[data-filter]');

        if (!button) {
            return;
        }

        const filter = button.dataset.filter;

        filterGroup.querySelectorAll('[data-filter]').forEach((other) => {
            const active = other === button;
            other.classList.toggle('is-active', active);
            other.setAttribute('aria-pressed', String(active));
        });

        // La carte mise en avant ne s'agrandit que lorsque tout le catalogue est affiché.
        list.classList.toggle('is-filtered', filter !== 'all');

        let visible = 0;

        cards.forEach((card) => {
            // Une carte peut appartenir à plusieurs familles (liste séparée par des espaces).
            const show = filter === 'all' || (card.dataset.group || '').split(' ').includes(filter);
            card.hidden = !show;

            if (show) {
                visible += 1;
                card.classList.add('is-visible');

                if (!reduceMotion) {
                    card.animate(
                        [{ opacity: 0, transform: 'translateY(16px)' }, { opacity: 1, transform: 'none' }],
                        { duration: 480, delay: (visible - 1) * 50, easing: 'cubic-bezier(0.16, 1, 0.3, 1)', fill: 'backwards' },
                    );
                }
            }
        });

        if (status) {
            status.textContent = `${visible} domaine${visible > 1 ? 's' : ''} affiché${visible > 1 ? 's' : ''}`;
        }
    });
});

// Barre de navigation d'une fiche domaine : surligne la section en cours de lecture.
document.querySelectorAll('[data-localnav]').forEach((localNav) => {
    const links = [...localNav.querySelectorAll('a[href^="#"]')];
    const sections = links.map((link) => document.querySelector(link.getAttribute('href'))).filter(Boolean);

    if (sections.length === 0 || !('IntersectionObserver' in window)) {
        return;
    }

    const sectionObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                links.forEach((link) => {
                    const active = link.getAttribute('href') === `#${entry.target.id}`;
                    link.classList.toggle('is-active', active);
                    link.toggleAttribute('aria-current', active);
                });
            });
        },
        { rootMargin: '-45% 0px -50% 0px' },
    );

    sections.forEach((section) => sectionObserver.observe(section));
});

// Vidéos techniques : lecture dans un lecteur intégré (fenêtre modale) plutôt qu'en quittant le site.
const videoDialog = document.querySelector('[data-video-dialog]');

if (videoDialog && typeof videoDialog.showModal === 'function') {
    const frame = videoDialog.querySelector('iframe');
    const title = videoDialog.querySelector('[data-video-title]');

    document.querySelectorAll('[data-video-id]').forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            frame.src = `https://www.youtube-nocookie.com/embed/${trigger.dataset.videoId}?autoplay=1&rel=0`;
            title.textContent = trigger.dataset.videoTitle || '';
            videoDialog.showModal();
        });
    });

    // Fermer : bouton, touche Échap (native) ou clic en dehors de la vidéo ; la lecture s'arrête.
    videoDialog.addEventListener('click', (event) => {
        if (event.target === videoDialog || event.target.closest('[data-video-close]')) {
            videoDialog.close();
        }
    });

    videoDialog.addEventListener('close', () => {
        frame.src = 'about:blank';
    });
}

// Onglets accessibles (formats d'événements) : clic, flèches gauche/droite, Début/Fin.
document.querySelectorAll('[data-tabs]').forEach((tabs) => {
    const buttons = [...tabs.querySelectorAll('[role="tab"]')];

    const select = (button) => {
        buttons.forEach((other) => {
            const active = other === button;
            other.setAttribute('aria-selected', String(active));
            other.tabIndex = active ? 0 : -1;
            document.getElementById(other.getAttribute('aria-controls')).hidden = !active;
        });
    };

    buttons.forEach((button, index) => {
        button.addEventListener('click', () => select(button));
        button.addEventListener('keydown', (event) => {
            const moves = { ArrowRight: index + 1, ArrowLeft: index - 1, Home: 0, End: buttons.length - 1 };

            if (!(event.key in moves)) {
                return;
            }

            event.preventDefault();
            const next = buttons[(moves[event.key] + buttons.length) % buttons.length];
            select(next);
            next.focus();
        });
    });
});

// En-tête : une ombre apparaît dès que la page défile.
const siteHeader = document.querySelector('.site-header-wrap');

if (siteHeader) {
    const updateHeader = () => siteHeader.classList.toggle('is-scrolled', window.scrollY > 8);
    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();
}
