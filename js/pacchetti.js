/* FUNZIONI GLOBALI (Richiamate dall'HTML) */

// Funzione per lo scorrimento usata dalle frecce nello slider pacchetti
function scorriA(idElemento) {
    const elemento = document.getElementById(idElemento); // Cerca nella pagina l' elemento HTML che possiede quell'ID e lo salva nella costante 'elemento'.
    if (elemento) {
        elemento.scrollIntoView({ //Permette alla finestra di scorrere automaticamente fino a quando non viene inquadrato l'elemento 
            behavior: 'smooth', //Permette uno scorrimento fluido
            block: 'nearest', //Allineamento verticale 
            inline: 'center' //Allineamento orizzontale
        });
    }
}

/* GESTIONE EVENTI DOM E INTERFACCIA*/

document.addEventListener('DOMContentLoaded', () => { // Cerca nel documento l'icona del menu a panino (.hamb-menu) e la salva nella variabile 'hambMenu'

    // --- Menu Hamburger ---
    const item = document.querySelector('.hamb-menu');
    if (item) {
        item.addEventListener("click", function() { //Se qualcuno clicca sull'icona esegue l'azione "menu open"
            document.body.classList.toggle('menu-open');
        });
    }

    //Attiva l'effetto parallax: in base al valore di data value si decide la velocità di movimento degli oggetti facendo sembrare gli oggetti più vicini o piu lontani allo schermo
    const parallaxObjects = document.querySelectorAll(".object"); // Cerca tutti gli elementi che hanno la classe .object e li raggruppa in una lista. Se il browser dovesse cercare gli elementi ad ogni spostamento del mouse, la pagina andrebbe a scatti

    // Attiviamo il listener solo se ci sono oggetti parallax nella pagina
    if (parallaxObjects.length > 0) {
        document.addEventListener("mousemove", function parallax(e) {
            parallaxObjects.forEach(function(move) {
                const moving_value = move.getAttribute("data-value"); //Legge il numero nascosto nell'HTML che determinerà la velocità di movimento 
                //Prende la posizione (x o y ) la moltiplica per la velocità e la divide per 200 per attenuare lo spostamento
                const x = (e.clientX * moving_value) / 200;
                const y = (e.clientY * moving_value) / 200;
                move.style.transform = "translateX(" + x + "px) translateY(" + y + "px)"; // Prende le coordinate X e Y appena calcolate e le inserisce nel CSS dell'oggetto, spostandolo fisicamente sullo schermo. Il comando viene ripetuto decine di volte al secondo, creando l'illusione ottica della fluidità 3D
            });
        });
    }

});