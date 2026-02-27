/* GESTIONE EVENTI DOM (Caricamento pagina) */

document.addEventListener('DOMContentLoaded', function() { // Mette in "pausa" il JavaScript finché l'intero scheletro HTML della pagina non è stato caricato. 

    //  Menu Hamburger 
    const hambMenu = document.querySelector('.hamb-menu'); // Cerca nel documento l'icona del menu a panino (.hamb-menu) e la salva nella variabile 'hambMenu'
    if (hambMenu) {
        hambMenu.addEventListener("click", function() { //Se qualcuno clicca sull'icona esegue l'azione "menu open"
            document.body.classList.toggle('menu-open');
        });
    }
})


/* FUNZIONI GLOBALI (Richiamate dall'HTML) */

function updateBookingLink(id) { //Funzione che aggiorna il link di prenotazione. id è il numeero specificato dalla camera
    const dateInput = document.getElementById('date_' + id); // Recupera la data specifica
    const btn = document.getElementById('btn_prenota_' + id); //Recupera il bottone prenota della casa scelta

    // Errori: Sia quello client (js) che quello server (php)
    const errorMsgClient = document.getElementById('error_msg_' + id);
    const errorMsgServer = document.getElementById('server_error_' + id);

    if (!dateInput || !btn) return; // Blocco di sicurezza: se mancano gli elementi esce fuori

    // Recupera il link base (senza data): legge poi bottone e data salvata nel link base
    const baseUrl = btn.getAttribute('data-baseurl');
    const selectedDate = dateInput.value;

    if (selectedDate) { //Se è stata selezionata una data
        btn.href = baseUrl + "&data=" + encodeURIComponent(selectedDate); // Aggiunge la data selezionata alla fine del link

        // Nasconde gli errori, così se l'utente cambia data, il box sparisce
        if (errorMsgClient) errorMsgClient.style.display = 'none';
        if (errorMsgServer) errorMsgServer.style.display = 'none';
    }
}

//Funzione di controllo che scatta quando l'utente clicca sul link prenota senza aver inserito una data
function checkDateSelected(id) {
    const dateInput = document.getElementById('date_' + id); //Prende il bottone per la scelta della data della prenotazione
    const errorMsgClient = document.getElementById('error_msg_' + id); //Prende il messaggio di errore nascosto per quella casa

    if (!dateInput) return false; // Se non trova una data , blocca l'azione (restituisce 'falso', il click si annulla)

    // Se l'input data è vuoto, blocca il click e mostra errore
    if (!dateInput.value) {
        if (errorMsgClient) errorMsgClient.style.display = 'block'; //Messaggio di errore
        return false;
    }

    return true; //Se la data c'è, viene effettuata la prenotazione
}