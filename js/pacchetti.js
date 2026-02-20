/* ==========================================
   1. FUNZIONI GLOBALI (Richiamate dall'HTML)
   ========================================== */

// Funzione per lo scorrimento fluido (usata dalle frecce nello slider pacchetti)
function scorriA(idElemento) {
    const elemento = document.getElementById(idElemento);
    if (elemento) {
        elemento.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center'
        });
    }
}


/* ==========================================
   2. GESTIONE EVENTI DOM E INTERFACCIA
   ========================================== */

document.addEventListener('DOMContentLoaded', () => {

    // --- Menu Hamburger ---
    const item = document.querySelector('.hamb-menu');
    if (item) {
        item.addEventListener("click", function() {
            document.body.classList.toggle('menu-open');
        });
    }

    // --- Animazione Parallax ---
    // Caching: Troviamo gli elementi ".object" una sola volta all'avvio 
    // invece di cercarli ad ogni minimo movimento del mouse
    const parallaxObjects = document.querySelectorAll(".object");

    // Attiviamo il listener solo se ci sono oggetti parallax nella pagina
    if (parallaxObjects.length > 0) {
        document.addEventListener("mousemove", function parallax(e) {
            parallaxObjects.forEach(function(move) {
                const moving_value = move.getAttribute("data-value");
                const x = (e.clientX * moving_value) / 200;
                const y = (e.clientY * moving_value) / 200;
                move.style.transform = "translateX(" + x + "px) translateY(" + y + "px)";
            });
        });
    }

});


