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

    /* --- 2.3 FUNZIONE UNICA DI CONTROLLO SCROLL --- */
    function runAllChecks() {

        // A. Animazioni fade-in (.watch)
        watchElements.forEach(function(el) {
            if (isElementVisible(el)) {
                el.classList.add("in-page");
            }else{
                el.classList.remove("in-page");
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