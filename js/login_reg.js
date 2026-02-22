/* VARIABILI E COSTANTI DOM */

//Vengono salvati in memoria tutti i campi presi dal DOM tramite il loro id 
const userIn = document.getElementById('username');
const emailIn = document.getElementById('email');
const passIn = document.getElementById('pass');
const btn = document.getElementById('btn-submit');
const tooltip = document.getElementById('password-tooltip');
const iconSlash = document.getElementById('icon-slash');
const iconEye = document.getElementById('icon-eye');
const tabReg = document.getElementById('tab-reg');
const tabLog = document.getElementById('tab-log');
const emailCont = document.getElementById('email-container');
const actionInput = document.getElementById('action-input');
const errorMsg = document.getElementById('error-msg');
const allInputs = document.querySelectorAll('input');

let currentMode = 'register'; // Variabile modificabile (let) per tenere traccia dello stato attuale. Di base, è in modalità "registrazione".


/* FUNZIONI DI GESTIONE INTERFACCIA (UI) */

function showTooltip() { //Funzione per mostrare il tooltip con le regole della password  
    if (currentMode === 'register') { //Il tooltip viene mostrato solo se siamo nl tab di registrazione
        tooltip.style.display = 'block'; //il tooltip viene reso visibili
    }
}

function hideTooltip() {
    tooltip.style.display = 'none'; // il tooltip viene reso invisibile
}

function switchMode(mode, isExplicitClick = false) { //Funzione che trasforma l'estetica del form in base a cosa clicca l'utente(registrati o accedi)
    currentMode = mode; //Aggiorna la variabile GLOBALE
    actionInput.value = mode; //Passa l'informazione al file php 

    if (isExplicitClick && errorMsg) { //Se prima di cambiare tab c'era un messaggio di errore, con il cambio il messaggio scompare
        errorMsg.textContent = "";
    }

    if (mode === 'login') { //Modalità accedi 
        tabLog.className = 'tab-active'; //Il tab accedi diventa giallo appena si compilano i campi
        tabReg.className = 'tab-inactive'; //IL tab registrati si spegne
        emailCont.style.display = 'none'; //Nasconde il campo dell'email 
        btn.value = 'Accedi'; //Permette di inserire "ACCEDI" come testo del bottone
        passIn.placeholder = 'Password'; //imposta il tooltip "Password" dentro il campo 
        tooltip.style.display = 'none'; //nasconde il tooltip con le regole 
    } else { //Modalità registrati 
        tabReg.className = 'tab-active'; //Attiva il tab registrati  
        tabLog.className = 'tab-inactive'; //Spegne il tab accedi 
        emailCont.style.display = 'block'; //Fa ricomparre il campo email 
        btn.value = 'Crea Account'; //Permette di inserire "CREA ACCOUNT come testo del bottone "
        passIn.placeholder = 'Password'; //Imposta il tooltip passoword dentro il campo
    }


    checkInputs(); // Ricontrolla gli input ogni volta che si cambia tab
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


/* FUNZIONI DI VALIDAZIONE */

function checkInputs() { //Campo sicurezza che ogni volta che digitiamo un valore verifica se tutte le regole di validazione sono verificate
    const passValue = passIn.value; //Legge i valori inseriti nel campo password 
    const emailValue = emailIn.value; //Legge i valori inseriri nel campo email 

    if (currentMode === 'register') { //Modalità registrazione
        const hasUpperCase = /[A-Z]/.test(passValue); //Ameno una maiuscola 
        const hasNumber = /[0-9]/.test(passValue); //Almeno un numero 
        const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); //Almeno un carattere speciale 
        const hasLength = passValue.length >= 8; //Almeno 8 caratteri totali 
        const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/; //Struttura email da rispettre
        const isEmailValid = emailRegex.test(emailValue);

        if (userIn.value.trim() !== "" && isEmailValid && hasUpperCase && hasNumber && hasSpecial && hasLength) {
            btn.disabled = false; //Se tutti i controlli di validazione sono superati, il bottone "CREA ACCOUNT " si accende
        } else {
            btn.disabled = true; ////Se i controlli di validazione non sono tutti superati, il bottone "CREA ACCOUNT " rimane spento 
        }
    } else {
        if (userIn.value.trim() !== "" && passValue.trim() !== "") {
            btn.disabled = false; //Se i campi presnenti sono entrambi compilati, il bottone "ACCEDI" si accende
        } else {
            btn.disabled = true; //Se i campi presenti non sono entrambi compilati il bottone il bottone rimane spento
        }
    }
}

/* INIZIALIZZAZIONE E ASSEGNAZIONE EVENTI */

// Eventi per il Tooltip della Password
passIn.addEventListener('mouseenter', showTooltip); //Mostra il tooltip quando con il mouse si passa sopra il campo password 
passIn.addEventListener('mouseleave', hideTooltip); //Nasconde il tooltip quando il mouse esce fuori dl campo password 
passIn.addEventListener('focus', showTooltip); //Mostra il tooltip quando con il mouse si clicca il campo password 


// Eventi per la validazione in tempo reale degli Input
//Dopo ogni carattere digigtato mei tre campi, viene controllato se il bottone si può accendere 
userIn.addEventListener('input', checkInputs);
emailIn.addEventListener('input', checkInputs);
passIn.addEventListener('input', checkInputs);

// Se l'utente clicca dentro un campo, l'errore sparisce subit
userIn.addEventListener('focus', () => {
    if (errorMsg) {
        errorMsg.textContent = "";
    }
});

emailIn.addEventListener('focus', () => {
    if (errorMsg) {
        errorMsg.textContent = "";
    }
});

passIn.addEventListener('focus', () => {
    if (errorMsg) {
        errorMsg.textContent = "";
    }
});

allInputs.forEach(input => {
    input.addEventListener('keydown', (e) => { // Impedisce la digitazione dello spazi
        if (e.code === "Space") {
            e.preventDefault();
        }
    });

});

// Inizializzazione al caricamento della pagina
window.addEventListener('load', () => {
    const savedAction = actionInput.value; //Qundo la pagina web ha finito di caricarsi completamnte legge cosa c'è scritto nel campo nascosto creato dal php
    if (savedAction === 'login') { //Se quello che è scritto è login  allora rimane su login anzichè passare a registrati
        switchMode('login', false);
    } else { //Se la pagina era già su registrati, rimane su registrati 
        switchMode('register', false);
    }
});


checkInputs(); // Avvio iniziale del controllo per impostare lo stato del bottone