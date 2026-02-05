<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";
$action = $_POST['action'] ?? "register"; // Capisce se stai facendo login o registrazione

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- LOGICA REGISTRAZIONE ---
    if ($action === 'register' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];

        // 1. Validazione Email (.com / .it)
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
            $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
        }
        // 2. Validazione Password Compleessa
        elseif (strlen($password) < 8 || 
            !preg_match("/[A-Z]/", $password) || 
            !preg_match("/[0-9]/", $password) || 
            !preg_match("/[^a-zA-Z0-9]/", $password)) {
            
            $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
        }
        else {
            // Controlli Database
            $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
            $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

            if (pg_num_rows($check_user) > 0) $errori = "Errore: Username già in uso.";
            elseif (pg_num_rows($check_email) > 0) $errori = "Errore: Email già registrata.";
            else {
                // Inserimento
                $query = "INSERT INTO utenti (username, email, password) VALUES ($1, $2, $3)";
                $res = pg_query_params($conn, $query, array($username, $email, $password));
                if ($res) {
                    $_SESSION['user'] = $username; 
                    header("Location: index.php");
                    exit;
                }
            }
        }
    }
    // --- LOGICA LOGIN (Codice che mi hai mandato integrato qui) ---
    elseif ($action === 'login' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];
        
        $query = "SELECT * FROM utenti WHERE username = $1 AND password = $2";
        $result = pg_query_params($conn, $query, array($username, $password));

        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $_SESSION['user'] = $row['username'];
            // $_SESSION['nome'] = $row['nome']; // Decommenta se hai la colonna nome
            header("Location: index.php");
            exit;
        } else {
            $errori = "Errore: Username o Password non corretti.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Accedi - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script> 
    <style>
        input[type="submit"]:disabled {
            background-color: #555 !important;
            color: #888 !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35%; 
            transform: translateY(-50%);
            cursor: pointer;
            color: #333; 
            user-select: none;
        }
        .password-container {
            position: relative;
            width: 100%;
        }
        /* Stili per il Tab Switch */
        .tab-switch {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 20px;
            cursor: pointer;
            user-select: none;
        }
        .tab-active {
            color: #FFD94A;
            font-weight: bold;
            text-decoration: underline;
        }
        .tab-inactive {
            color: #888;
        }
        .tab-inactive:hover {
            color: white;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body style="background: #1d1d1f; color: white; padding-top: 100px;">
    <div style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
        
        <div class="tab-switch">
            <span id="tab-reg" class="tab-active" onclick="switchMode('register')">Registrati</span>
            <span> / </span>
            <span id="tab-log" class="tab-inactive" onclick="switchMode('login')">Login</span>
        </div>

        <p style="color: red; text-align: center; min-height: 20px;"><?php echo $errori; ?></p>
        
        <form name="authForm" action="login_reg.php" method="POST">
            <input type="hidden" id="action-input" name="action" value="register">

            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%" required ><br><br>
            
            <div id="email-container">
                <label>E-mail:</label><br>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%" placeholder="esempio@dominio.it"><br><br>
            </div>
            
            <label>Password:</label><br>
            <div class="password-container">
                <input type="password" id="pass" name="pass" style="width:100%; padding-right: 40px;" required placeholder="Password">
                <span class="toggle-password" onclick="togglePassword()">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            <br><br>
            
            <input type="submit" id="btn-submit" name="btn_submit" value="Crea Account" style="padding: 10px 20px; cursor:pointer;" disabled>
        </form>
    </div>

    <script>
        const userIn = document.getElementById('username');
        const emailIn = document.getElementById('email');
        const passIn = document.getElementById('pass');
        const btn = document.getElementById('btn-submit');
        
        const tabReg = document.getElementById('tab-reg');
        const tabLog = document.getElementById('tab-log');
        const emailCont = document.getElementById('email-container');
        const actionInput = document.getElementById('action-input');

        let currentMode = 'register'; // Stato iniziale

        // Funzione per cambiare tra Login e Registrazione
        function switchMode(mode) {
            currentMode = mode;
            actionInput.value = mode; // Dice al PHP cosa fare

            if (mode === 'login') {
                // UI Login
                tabLog.className = 'tab-active';
                tabReg.className = 'tab-inactive';
                emailCont.style.display = 'none'; // Nascondi Email
                btn.value = 'Accedi';
                passIn.placeholder = 'Inserisci la password';
            } else {
                // UI Registrazione
                tabReg.className = 'tab-active';
                tabLog.className = 'tab-inactive';
                emailCont.style.display = 'block'; // Mostra Email
                btn.value = 'Crea Account';
                passIn.placeholder = 'Min 8 car, 1 Maiusc, 1 Num, 1 Spec';
            }
            // Resetta validazione
            checkInputs();
        }

        function togglePassword() {
            if (passIn.type === "password") {
                passIn.type = "text";
                document.getElementById('eye-icon').style.stroke = "#007bff"; 
            } else {
                passIn.type = "password";
                document.getElementById('eye-icon').style.stroke = "currentColor";
            }
        }

        function checkInputs() {
            const passValue = passIn.value;
            const emailValue = emailIn.value;

            // Logica diversa a seconda della modalità
            if (currentMode === 'register') {
                // --- REGOLE SEVERE PER REGISTRAZIONE ---
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
                // --- REGOLE SEMPLICI PER LOGIN ---
                // Basta che username e password non siano vuoti
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
        
        // Avvia il controllo iniziale
        checkInputs();
    </script>
</body>
</html>