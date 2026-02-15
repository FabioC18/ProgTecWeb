let item = document.querySelector('.hamb-menu');
item.addEventListener("click", function() {
  document.body.classList.toggle('menu-open');
});

document.addEventListener('DOMContentLoaded', function() {
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
        });
    });
});