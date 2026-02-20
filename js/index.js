/* ==========================================
   1. FUNZIONI DI UTILITÀ GLOBALI
   ========================================== */

function isElementVisible(el) {
    if (!el) return false;
    const rect = el.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    // Ritorna true se l'elemento è visibile nella viewport (con 50px di tolleranza)
    return (rect.top <= windowHeight - 50 && rect.bottom >= 0);
}

/* GESTIONE CONTATORI (VERSIONE FORZATA) */
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

/* ==========================================
   2. INIZIALIZZAZIONE ED EVENTI DOM
   ========================================== */

document.addEventListener('DOMContentLoaded', () => {

    /* --- 2.1 GESTIONE MENU HAMBURGER --- */
    const hamburger = document.querySelector('.hamb-menu');
    if (hamburger) {
        hamburger.addEventListener("click", function() {
            document.body.classList.toggle('menu-open');
        });
    }

    /* --- 2.2 CACHE DEGLI ELEMENTI DOM --- */
    // Salviamo gli elementi una sola volta per migliorare le prestazioni allo scroll
    const watchElements = document.querySelectorAll('.watch');
    const counters = document.querySelectorAll('.cont-client, .cont-year');

    // Reset preventivo dei contatori a 0
    counters.forEach(el => el.innerHTML = "0");

    /* --- 2.3 FUNZIONE UNICA DI CONTROLLO SCROLL --- */
    function runAllChecks() {

        // A. Animazioni fade-in (.watch)
        watchElements.forEach(function(el) {
            if (isElementVisible(el)) {
                el.classList.add("in-page");
            }
        });

        // B. Animazioni Contatori
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

    /* --- 2.4 ASSEGNAZIONE EVENT LISTENERS --- */

    // Esecuzione immediata al caricamento
    runAllChecks();

    // Avvio allo scroll
    window.addEventListener('scroll', runAllChecks);

    // Avvio al caricamento completo (es. quando terminano di caricare immagini/video)
    window.addEventListener('load', runAllChecks);

});