/* --- 1. GESTIONE MENU HAMBURGER (Rimasto invariato) --- */
let hamburger = document.querySelector('.hamb-menu');
let body = document.body;

hamburger.addEventListener("click", function() {
    body.classList.toggle('menu-open');
});


/* --- 2. FUNZIONI DI UTILITÀ --- */

// Funzione per controllare se un elemento è visibile nello schermo
function isElementVisible(el) {
    if (!el) return false;
    var rect = el.getBoundingClientRect();
    var windowHeight = window.innerHeight || document.documentElement.clientHeight;
    
    // L'elemento è visibile se la sua parte superiore è entrata nella finestra
    // (Aggiungiamo un piccolo offset di 100px per non farlo scattare proprio al bordo)
    return (rect.top <= windowHeight - 100 && rect.bottom >= 0);
}


/* --- 3. GESTIONE ANIMAZIONI FADE-IN (.watch) --- */
var watchElements = document.querySelectorAll('.watch');

function checkWatchElements() {
    watchElements.forEach(function(el) {
        if (isElementVisible(el)) {
            el.classList.add("in-page");
        } else {
            // Se vuoi che l'animazione si ripeta togliendo la classe quando esce:
            el.classList.remove("in-page");
        }
    });
}




/* --- 5. EVENTO SCROLL PRINCIPALE --- */

// Ascoltiamo l'evento scroll
window.addEventListener('scroll', function() {
    checkWatchElements();
});

// Eseguiamo le funzioni anche al caricamento della pagina (nel caso fossimo già a metà pagina)
window.addEventListener('load', function() {
    checkWatchElements();
});