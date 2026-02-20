/* ==========================================
   1. VARIABILI E COSTANTI DOM
   ========================================== */

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

let currentMode = 'register';


/* ==========================================
   2. FUNZIONI DI GESTIONE INTERFACCIA (UI)
   ========================================== */

function showTooltip() {
    if (currentMode === 'register') {
        tooltip.style.display = 'block';
    }
}

function hideTooltip() {
    tooltip.style.display = 'none';
}

function switchMode(mode, isExplicitClick = false) {
    currentMode = mode;
    actionInput.value = mode;

    if (isExplicitClick && errorMsg) {
        errorMsg.textContent = "";
    }

    if (mode === 'login') {
        tabLog.className = 'tab-active';
        tabReg.className = 'tab-inactive';
        emailCont.style.display = 'none';
        btn.value = 'Accedi';
        passIn.placeholder = 'Password';
        tooltip.style.display = 'none';
    } else {
        tabReg.className = 'tab-active';
        tabLog.className = 'tab-inactive';
        emailCont.style.display = 'block';
        btn.value = 'Crea Account';
        passIn.placeholder = 'Password';
    }

    // Ricontrolla gli input ogni volta che si cambia tab
    checkInputs();
}

function togglePassword() {
    if (passIn.type === "password") {
        passIn.type = "text";
        iconSlash.hidden = true;
        iconEye.hidden = false;
    } else {
        passIn.type = "password";
        iconSlash.hidden = false;
        iconEye.hidden = true;
    }
}


/* ==========================================
   3. FUNZIONI DI VALIDAZIONE
   ========================================== */

function checkInputs() {
    const passValue = passIn.value;
    const emailValue = emailIn.value;

    if (currentMode === 'register') {
        const hasUpperCase = /[A-Z]/.test(passValue);
        const hasNumber = /[0-9]/.test(passValue);
        const hasSpecial = /[^a-zA-Z0-9]/.test(passValue);
        const hasLength = passValue.length >= 8;
        const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/;
        const isEmailValid = emailRegex.test(emailValue);

        if (userIn.value.trim() !== "" && isEmailValid && hasUpperCase && hasNumber && hasSpecial && hasLength) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    } else {
        if (userIn.value.trim() !== "" && passValue.trim() !== "") {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }
}


/* ==========================================
   4. INIZIALIZZAZIONE E ASSEGNAZIONE EVENTI
   ========================================== */

// Eventi per il Tooltip della Password
passIn.addEventListener('mouseover', showTooltip);
passIn.addEventListener('mouseenter', showTooltip);
passIn.addEventListener('mouseleave', hideTooltip);
passIn.addEventListener('focus', showTooltip);
passIn.addEventListener('blur', hideTooltip);

// Eventi per la validazione in tempo reale degli Input
userIn.addEventListener('input', checkInputs);
emailIn.addEventListener('input', checkInputs);
passIn.addEventListener('input', checkInputs);

// Inizializzazione al caricamento della pagina
window.addEventListener('load', () => {
    const savedAction = actionInput.value;
    if (savedAction === 'login') {
        switchMode('login', false);
    } else {
        switchMode('register', false);
    }
});

// Primo controllo di validazione all'avvio dello script
checkInputs();