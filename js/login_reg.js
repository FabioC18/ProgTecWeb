const userIn = document.getElementById('username');
const emailIn = document.getElementById('email');
const passIn = document.getElementById('pass');
const btn = document.getElementById('btn-submit');
const tooltip = document.getElementById('password-tooltip'); // Riferimento al tooltip
const iconSlash = document.getElementById('icon-slash');
const iconEye = document.getElementById('icon-eye');
const tabReg = document.getElementById('tab-reg');
const tabLog = document.getElementById('tab-log');
const emailCont = document.getElementById('email-container');
const actionInput = document.getElementById('action-input');

let currentMode = 'register';

passIn.addEventListener('mouseover', showTooltip);
passIn.addEventListener('mouseenter', showTooltip);
passIn.addEventListener('mouseleave', hideTooltip);
passIn.addEventListener('focus', showTooltip); // Utile per chi usa tab
passIn.addEventListener('blur', hideTooltip);

// GESTIONE TOOLTIP PASSWORD
// Mostra quando il mouse entra o il campo ha il focus
function showTooltip() {
    if (currentMode === 'register') {
        tooltip.style.display = 'block';
    }
}
// Nascondi quando il mouse esce o perde focus
function hideTooltip() {
    tooltip.style.display = 'none';
}


function switchMode(mode) {
    currentMode = mode;
    actionInput.value = mode;

    if (mode === 'login') {
        // UI Login
        tabLog.className = 'tab-active';
        tabReg.className = 'tab-inactive';
        emailCont.style.display = 'none';
        btn.value = 'Accedi';
        passIn.placeholder = 'Password';
        // Nasconde tooltip se era aperto
        tooltip.style.display = 'none';
    } else {
        // UI Registrazione
        tabReg.className = 'tab-active';
        tabLog.className = 'tab-inactive';
        emailCont.style.display = 'block';
        btn.value = 'Crea Account';
        passIn.placeholder = 'Password';
    }
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
            btn.style.backgroundColor = "";
        } else {
            btn.disabled = true;
        }

    } else {
        if (userIn.value.trim() !== "" && passValue.trim() !== "") {
            btn.disabled = false;
            btn.style.backgroundColor = "";
        } else {
            btn.disabled = true;
        }
    }
}

userIn.addEventListener('input', checkInputs);
emailIn.addEventListener('input', checkInputs);
passIn.addEventListener('input', checkInputs);

checkInputs();