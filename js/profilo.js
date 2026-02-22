/* VARIABILI E COSTANTI DOM */

//Vengono salvati in memoria tutti i campi presi dal DOM tramite il loro id 
const userIn = document.getElementById('username');
const emailIn = document.getElementById('email');
const passIn = document.getElementById('pass');
const btn = document.getElementById('btn-submit');
const tooltip = document.getElementById('password-tooltip');
const iconSlash = document.getElementById('icon-slash');
const iconEye = document.getElementById('icon-eye');

let map = null; // Variabile modificabile (let) vuota (null) chiamata 'map'. Permette al sistema di ricordarsi se una mappa è già stata disegnata sullo schermo ed evitare di sovrapporne due per errore!


/* FUNZIONI DI GESTIONE INTERFACCIA (UI) */

// GESTIONE TOOLTIP PASSWORD (SOTTO)

function showTooltip() { //Funzione per mostrare il tooltip con le regole della password  
    tooltip.style.display = 'block'; //il tooltip viene reso visibili

}

function hideTooltip() {
    tooltip.style.display = 'none'; // il tooltip viene reso invisibile
}

function togglePassword() { //Funzione che collega l'occhio che permette di rendere visibile la passsword
    if (passIn.type === "password") {
        passIn.type = "text"; //I pallini vengono trasformati in lettere
        iconSlash.hidden = true; //Nasconde l'cona dell'occhio sbarrato 
        iconEye.hidden = false; //Mostra l'icona dell'occhio senza la sbarra
    } else {
        passIn.type = "password"; //Ritrasforma le lettere in puntini 
        iconSlash.hidden = false; //Mostra l'occhio sbarrato 
        iconEye.hidden = true; //Nasconde l'occhio normale 
    }
}

/*  FUNZIONI DI VALIDAZIONE */

function checkInputs() { //Campo sicurezza che ogni volta che digitiamo un valore verifica se tutte le regole di validazione sono verificate
    const passValue = passIn.value; //Legge i valori inseriti nel campo password 
    const emailValue = emailIn.value; //Legge i valori inseriri nel campo email 


    const hasUpperCase = /[A-Z]/.test(passValue); //Ameno una maiuscola 
    const hasNumber = /[0-9]/.test(passValue); //Almeno un numero 
    const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); //Almeno un carattere speciale 
    const hasLength = passValue.length >= 8; //Almeno 8 caratteri totali 
    const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/; //Struttura email da rispettre
    const isEmailValid = emailRegex.test(emailValue); //Verifica se l'email digitata rispetta la regola appena scritta

    if (userIn.value.trim() !== "" && isEmailValid && hasUpperCase && hasNumber && hasSpecial && hasLength) {
        btn.disabled = false; //Se tutti i controlli di validazione sono superati, il bottone "CREA ACCOUNT " si accende
        btn.style.opacity = "1";
    } else {
        btn.disabled = true; ////Se i controlli di validazione non sono tutti superati, il bottone "CREA ACCOUNT " rimane spento 
        btn.style.opacity = "0.5";
    }
}

/* GESTIONE MESSAGGIO DI SUCCESSO ED ERRORE */

document.addEventListener('DOMContentLoaded', () => {

    // Prende i messaggi usando le CLASSI (.success e .error) generate dal PHP
    const successMsg = document.querySelector('.success');
    const errorMsgTop = document.querySelector('.error');

    const allInputs = document.querySelectorAll('input'); // Seleziona TUTTI i campi di testo presenti nella pagina

    allInputs.forEach(input => {
        // Impedisce la digitazione dello spazio
        input.addEventListener('keydown', (e) => {
            if (e.code === "Space") {
                e.preventDefault();
            }
        });
    });
    if (successMsg) { // Se nella pagina è presente il div verde di successo

        setTimeout(() => { //  Scompare dopo 5 secodni 
            successMsg.style.display = 'none';
        }, 5000);

        allInputs.forEach(input => { //Scompare immediatamente al click su un campo
            input.addEventListener('focus', () => {
                successMsg.style.display = 'none';
            });
        });
    }



    if (errorMsgTop) { // Se nella pagina è presente il div rosso di errore
        allInputs.forEach(input => { //Scompare immediatamente al click su un campo
            input.addEventListener('focus', () => {
                errorMsgTop.style.display = 'none';
            });
        });
    }

});

/*  MAPPA E GEOLOCALIZZAZIONE */

function getLocation() {
    const mapContainer = document.getElementById("map"); //Prende il div html dove andrà a disegnare la mappa 

    function showError(error) { //La funzione si applica se ci sono errori con il GPS
        const errorContainer = document.getElementById("geo-error"); //Prende il div per i messaggi di errore
        errorContainer.style.display = 'block'; //Rende l'errore visibile

        switch (error.code) { //In base al codice resitituisce uno dei seguenti errori: 
            case error.PERMISSION_DENIED:
                errorContainer.innerText = "Hai negato il permesso per la geolocalizzazione.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorContainer.innerText = "Le informazioni sulla posizione non sono disponibili.";
                break;
            case error.TIMEOUT:
                errorContainer.innerText = "La richiesta per ottenere la posizione è scaduta.";
                break;
            case error.UNKNOWN_ERROR:
                errorContainer.innerText = "Si è verificato un errore sconosciuto.";
                break;
        }
    }

    navigator.geolocation.getCurrentPosition( //Comando che permette al sito di fornire il popup che richede di consocere la posizione
        (position) => { //Se l'utente accetta di far conoscere la posizione salva latitudine e longitudine dell'utente
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            mapContainer.style.display = 'block'; //Rende visibile il riquadro della mappa

            document.getElementById("geo-error").style.display = 'none'; // Nascondiamo gli errori precedenti

            if (map !== null) { //Se la mappa esiste già, viene rimossa prima di essere caricata
                map.remove();
            }

            map = L.map('map').setView([lat, lon], 11); //Inizializza la mappa centrata alle cordinate dell'utente e alle coordinate delle case
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19
            }).addTo(map);

            //Crea i punti blu sulla mappa
            const suite = L.marker([40.6780, 14.7625]).addTo(map).bindPopup("<b>Suite!</b>").openPopup();
            const deluxe = L.marker([40.67891, 14.75808]).addTo(map).bindPopup("<b>Deluxe!</b>").openPopup();
            const marker = L.marker([lat, lon]).addTo(map).bindPopup("<b>Sei qui!</b>").openPopup();
        },
        showError //Se si presenta un'errore viene lanciata la funzione scritta sopra
    );
}

/* ASSEGNAZIONE EVENTI E INIZIALIZZAZIONE */

// Eventi per il Tooltip della Password
passIn.addEventListener('mouseenter', showTooltip); //Mostra il tooltip quando con il mouse si passa sopra il campo password 
passIn.addEventListener('mouseleave', hideTooltip); //Nasconde il tooltip quando il mouse esce fuori dl campo password 
passIn.addEventListener('focus', showTooltip); //Mostra il tooltip quando con il mouse si clicca il campo password 


// Eventi per la validazione in tempo reale degli Input
// Dopo ogni carattere digitato nei tre campi, viene controllato se il bottone si può accendere 
userIn.addEventListener('input', checkInputs);
emailIn.addEventListener('input', checkInputs);
passIn.addEventListener('input', checkInputs);

checkInputs(); // Avvio iniziale del controllo per impostare lo stato del bottone