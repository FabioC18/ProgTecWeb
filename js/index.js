/* FUNZIONI DI UTILITÀ GLOBALI */

function isElementVisible(el) { //calcola matematicamente se un elemento HTML (el) è  visibile sullo schermo dell'utente.
    if (!el) return false;
    const rect = el.getBoundingClientRect(); // Ottiene posizione e dimensioni dell’elemento rispetto alla finestra visibile.
    const windowHeight = window.innerHeight || document.documentElement.clientHeight; // Calcola quanto è alto Il viewport cioè la parte visibile dello schermo. Usa OR '||' per garantire la massima compatibilità con tutti i browser vecchi e nuovi.
    return (rect.top <= windowHeight - 50 && rect.bottom >= 0); // Ritorna true se l'elemento è visibile nella viewport (con 50px di tolleranza)
}

/* INIZIALIZZAZIONE ED EVENTI DOM */

document.addEventListener('DOMContentLoaded', () => { // Mette lo script in pausa e aspetta che tutto l'HTML della pagina sia stato letto e caricato dal browser prima di iniziare a cercare gli elementi. Quando il DOM è pronto viene eseguito il codice

    /* --- 2.1 GESTIONE MENU HAMBURGER --- */
    const hamburger = document.querySelector('.hamb-menu'); // Cerca nel documento l'icona del menu a panino (.hamb-menu) e la salva nella variabile 'hambMenu'
    if (hamburger) {
        hamburger.addEventListener("click", function() { //Se qualcuno clicca sull'icona esegue l'azione "menu open"
            document.body.classList.toggle('menu-open');
        });
    }

    /* --- 2.2 CACHE DEGLI ELEMENTI DOM --- */
    const watchElements = document.querySelectorAll('.watch'); // Salviamo gli elementi (.watch) per migliorare le prestazioni allo scroll ed evitare ricerche nel DOM

    /* --- 2.3 FUNZIONE UNICA DI CONTROLLO SCROLL --- */
    function runAllChecks() { //funzione attivata ogni volta che l'utnete scorre nella home del sito 

        watchElements.forEach(function(el) { //Scorrre gli elementi .watch salvati in precedenza
            if (isElementVisible(el)) { //Uso della funzione precendente per verificare se l'elemento salvato è visibile 
                el.classList.add("in-page"); //Se è visibile, aggiunge la classe in-page che attiva le animazioni CSS
            } else {
                el.classList.remove("in-page"); //Se non è visibile, rimuove la classe in-page, permettendo comunque le rianimazioni nel riscroll 
            }
        });
    }

    /*  ASSEGNAZIONE EVENT LISTENERS */


    runAllChecks(); // Controlla gli elementi visibili appena la pagina si apre, animando gli elementi già presenti senza aspettare che l'utente debba per forza scorrere.

    window.addEventListener('scroll', runAllChecks); //Permette di continaure le animazioni mentre l'utente scorre la pagina 

    window.addEventListener('load', runAllChecks); //Quando tutti gli elementi della pagina sono presenti (immagini, video) effettua un ulteriore controllo per vedere chi è visibile e chi no

});