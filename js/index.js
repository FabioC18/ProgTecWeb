/* --- 1. GESTIONE MENU HAMBURGER --- */
const hamburger = document.querySelector('.hamb-menu');
const body = document.body;

if (hamburger) {
    hamburger.addEventListener("click", function() {
        body.classList.toggle('menu-open');
    });
}

/* --- 2. FUNZIONI DI UTILITÀ --- */
function isElementVisible(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    return (rect.top <= windowHeight - 50 && rect.bottom >= 0);
}

/* --- 3. ANIMAZIONI FADE-IN (.watch) --- */
const watchElements = document.querySelectorAll('.watch');

function checkWatchElements() {
    watchElements.forEach(function(el) {
        if (isElementVisible(el)) {
            el.classList.add("in-page");
        }
    });
}

/* --- 4. GESTIONE CONTATORI (VERSIONE FORZATA) --- */
function animateValue(el, start, end, duration) {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        el.innerHTML = Math.floor(progress * (end - start) + start);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            el.innerHTML = end;
        }
    };
    window.requestAnimationFrame(step);
}

function handleCounters() {
    const counters = document.querySelectorAll('.cont-client, .cont-year');

    counters.forEach(el => {
        if (isElementVisible(el) && !el.classList.contains('animated')) {
            const target = parseInt(el.getAttribute('data-target'));
            if (!isNaN(target)) {
                el.classList.add('animated');
                animateValue(el, 0, target, 2000);
            }
        }
    });
}

/* --- 5. INIZIALIZZAZIONE E EVENTI --- */

// Funzione unica di controllo
function runAllChecks() {
    checkWatchElements();
    handleCounters();
}

// Avvio al caricamento
window.addEventListener('DOMContentLoaded', () => {
    // Reset preventivo dei contatori a 0
    document.querySelectorAll('.cont-client, .cont-year').forEach(el => el.innerHTML = "0");
    runAllChecks();
});

// Avvio allo scroll
window.addEventListener('scroll', runAllChecks);

// Avvio al caricamento completo (immagini/video)
window.addEventListener('load', runAllChecks);