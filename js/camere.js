// ==========================================
// 1. GESTIONE EVENTI DOM (Caricamento pagina)
// ==========================================

document.addEventListener('DOMContentLoaded', function() {

    // --- Menu Hamburger ---
    const hambMenu = document.querySelector('.hamb-menu');
    if (hambMenu) {
        hambMenu.addEventListener("click", function() {
            document.body.classList.toggle('menu-open');
        });
    }

    // --- Aggiornamento Link Prenotazione tramite Classi ---
    // Seleziona tutti gli input di tipo date con la nostra classe
    const dateInputs = document.querySelectorAll('.date_picker_input');

    dateInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Trova il contenitore genitore (category-header)
            const parentDiv = this.closest('.category-header');

            // Trova il bottone di prenotazione dentro questo contenitore
            const btn = parentDiv.querySelector('.link-prenotazione');

            // Ottieni la data selezionata
            const selectedDate = this.value;

            // Esegue solo se il bottone esiste
            if (btn) {
                if (selectedDate) {
                    // Recupera il link base (es: salva_prenotazione.php?nome=...&prezzo=...)
                    const baseUrl = btn.getAttribute('data-baseurl');

                    // Crea il nuovo link aggiungendo la data
                    const newUrl = baseUrl + '&data=' + encodeURIComponent(selectedDate);

                    // Aggiorna l'href del bottone
                    btn.setAttribute('href', newUrl);

                    // Rimuovi la classe che lo disabilita
                    btn.classList.remove('btn-disabled');
                } else {
                    // Se l'utente cancella la data, disabilita di nuovo
                    btn.classList.add('btn-disabled');
                    btn.setAttribute('href', '#');
                }
            }
        });
    });

});


// ==========================================
// 2. FUNZIONI GLOBALI (Richiamate dall'HTML)
// ==========================================

function updateBookingLink(id) {
    // Recupera gli elementi
    const dateInput = document.getElementById('date_' + id);
    const btn = document.getElementById('btn_prenota_' + id);

    // Errori: Sia quello client (js) che quello server (php)
    const errorMsgClient = document.getElementById('error_msg_' + id);
    const errorMsgServer = document.getElementById('server_error_' + id);

    // Blocco di sicurezza se mancano gli elementi
    if (!dateInput || !btn) return;

    // Recupera il link base (senza data)
    const baseUrl = btn.getAttribute('data-baseurl');
    const selectedDate = dateInput.value;

    if (selectedDate) {
        // Aggiunge la data selezionata alla fine del link
        btn.href = baseUrl + "&data=" + encodeURIComponent(selectedDate);

        // NASCONDE TUTTI GLI ERRORI (Client e Server)
        // Così se l'utente cambia data, il box rosso sparisce
        if (errorMsgClient) errorMsgClient.style.display = 'none';
        if (errorMsgServer) errorMsgServer.style.display = 'none';
    }
}

function checkDateSelected(id) {
    const dateInput = document.getElementById('date_' + id);
    const errorMsgClient = document.getElementById('error_msg_' + id);

    // Blocco di sicurezza
    if (!dateInput) return false;

    // Se l'input data è vuoto, blocca il click e mostra errore
    if (!dateInput.value) {
        if (errorMsgClient) errorMsgClient.style.display = 'block';
        return false;
    }

    return true;
}