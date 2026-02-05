<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrati'])) {
    $password = $_POST['pass'];
    
    // 1. VALIDAZIONE EMAIL RIGIDA (.com o .it)
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
        $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
    }
    // 2. VALIDAZIONE PASSWORD
    elseif (strlen($password) < 8 || 
        !preg_match("/[A-Z]/", $password) || 
        !preg_match("/[0-9]/", $password) || 
        !preg_match("/[^a-zA-Z0-9]/", $password)) {
        
        $errori = "Errore: La password deve essere di almeno 8 caratteri, contenere una maiuscola, un numero e un carattere speciale.";
    }
    else {
        // 3. CONTROLLI DATABASE
        $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
        $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

        if (pg_num_rows($check_user) > 0) {
            $errori = "Errore: lo username è già presente nel database.";
        } 
        elseif (pg_num_rows($check_email) > 0) {
            $errori = "Errore: l'email è già presente nel database.";
        } 
        else {
            // 4. INSERIMENTO
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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login e Registrazione - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="style.css">
    <script src="validation.js"></script> 
    <style>
        input[type="submit"]:disabled {
            background-color: #555 !important;
            color: #888 !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        /* Stile per l'icona occhio */
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 35%; /* Centrato verticalmente rispetto all'input */
            transform: translateY(-50%);
            cursor: pointer;
            color: #333; /* Colore dell'occhio (scuro perché l'input è bianco) */
            user-select: none;
        }
        
        /* Contenitore relativo per posizionare l'occhio */
        .password-container {
            position: relative;
            width: 100%;
        }
    </style>
</head>
<body style="background: #1d1d1f; color: white; padding-top: 100px;">
    <div style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
        <h2>Registrazione</h2>
        <p style="color: red;"><?php echo $errori; ?></p>
        
        <form name="regForm" action="login_reg.php" method="POST">
            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%" required ><br><br>
            
            <label>E-mail:</label><br>
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%" required placeholder="esempio@dominio.it"><br><br>
            
            <label>Password:</label><br>
            <div class="password-container">
                <input type="password" id="pass" name="pass" style="width:100%; padding-right: 40px;" required placeholder="Min 8 car, 1 Maiusc, 1 Num, 1 Spec">
                <span class="toggle-password" onclick="togglePassword()">
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            <br><br>
            
            <input type="submit" id="btn-submit" name="registrati" value="Crea Account" style="padding: 10px 20px; cursor:pointer;" disabled>
        </form>
        <hr>
        <p>Hai già un account? <a href="index.php" style="color: cyan;">Torna alla Home</a> per accedere.</p>
    </div>

    <script>
        const userIn = document.getElementById('username');
        const emailIn = document.getElementById('email');
        const passIn = document.getElementById('pass');
        const btn = document.getElementById('btn-submit');

        // Funzione per mostrare/nascondere la password
        function togglePassword() {
            if (passIn.type === "password") {
                passIn.type = "text";
                // Opzionale: cambia colore o icona per indicare che è visibile
                document.getElementById('eye-icon').style.stroke = "#007bff"; 
            } else {
                passIn.type = "password";
                document.getElementById('eye-icon').style.stroke = "currentColor";
            }
        }

        function checkInputs() {
            const passValue = passIn.value;
            const emailValue = emailIn.value;

            // --- Validazione Password ---
            const hasUpperCase = /[A-Z]/.test(passValue); 
            const hasNumber = /[0-9]/.test(passValue);    
            const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); 
            const hasLength = passValue.length >= 8;      

            // --- Validazione Email (.com o .it) ---
            const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/;
            const isEmailValid = emailRegex.test(emailValue);

            // Attiva il tasto solo se tutto è corretto
            if (userIn.value.trim() !== "" && 
                isEmailValid && 
                hasUpperCase && 
                hasNumber && 
                hasSpecial && 
                hasLength) {
                
                btn.disabled = false; 
                btn.style.backgroundColor = ""; 
            } else {
                btn.disabled = true;  
            }
        }

        userIn.addEventListener('input', checkInputs);
        emailIn.addEventListener('input', checkInputs);
        passIn.addEventListener('input', checkInputs);
    </script>
</body>
</html>