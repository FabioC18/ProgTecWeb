<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrati'])) {
    $password = $_POST['pass'];
    
    // VALIDAZIONE PASSWORD (Lato Server)
    // Controlla: lunghezza < 8, nessuna maiuscola, nessun numero, nessun carattere speciale (non alfanumerico)
    if (strlen($password) < 8 || 
        !preg_match("/[A-Z]/", $password) || 
        !preg_match("/[0-9]/", $password) || 
        !preg_match("/[^a-zA-Z0-9]/", $password)) {
        
        $errori = "Errore: La password deve essere di almeno 8 caratteri, contenere una maiuscola, un numero e un carattere speciale.";
    }
    else {
        // Se la password è valida, procedo con i controlli al Database
        
        // 1. Controllo se l'utente esiste già 
        $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
        
        // 2. Controllo separato per l'email
        $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

        if (pg_num_rows($check_user) > 0) {
            $errori = "Errore: lo username è già presente nel database.";
        } 
        elseif (pg_num_rows($check_email) > 0) {
            $errori = "Errore: l'email è già presente nel database.";
        } 
        else {
            // Inserimento nuovo utente
            $query = "INSERT INTO utenti (username, email, password) VALUES ($1, $2, $3)";
            $res = pg_query_params($conn, $query, array($username, $email, $password));
            
            if ($res) {
                // 3. LOGIN AUTOMATICO: Imposto la sessione SUBITO
                $_SESSION['user'] = $username; 
                
                // Reindirizzo alla Home Page con utente loggato
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
        /* Stile aggiuntivo solo per l'effetto del tasto disabilitato */
        input[type="submit"]:disabled {
            background-color: #555 !important; /* Grigio scuro */
            color: #888 !important;
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        /* Classe per evidenziare requisiti password (opzionale, per chiarezza) */
        .password-req {
            font-size: 0.8em;
            color: #ccc;
            margin-top: -15px;
            margin-bottom: 15px;
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
            <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%" required ><br><br>
            
            <label>Password:</label><br>
            <input type="password" id="pass" name="pass" style="width:100%" required placeholder="Min 8 car, 1 Maiusc, 1 Num, 1 Spec"><br><br>
            
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

        function checkInputs() {
            const passValue = passIn.value;

            // Regex per la validazione Javascript (deve combaciare con quella PHP)
            const hasUpperCase = /[A-Z]/.test(passValue); // Almeno una Maiuscola
            const hasNumber = /[0-9]/.test(passValue);    // Almeno un Numero
            const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); // Almeno un carattere speciale
            const hasLength = passValue.length >= 8;      // Almeno 8 caratteri

            // Il tasto si attiva solo se user e email sono pieni E la password rispetta tutte le regole
            if (userIn.value.trim() !== "" && 
                emailIn.value.trim() !== "" && 
                hasUpperCase && 
                hasNumber && 
                hasSpecial && 
                hasLength) {
                
                btn.disabled = false; // Accendi il tasto
                btn.style.backgroundColor = ""; 
            } else {
                btn.disabled = true;  // Spegni il tasto
            }
        }

        // Ascolta ogni tasto premuto
        userIn.addEventListener('input', checkInputs);
        emailIn.addEventListener('input', checkInputs);
        passIn.addEventListener('input', checkInputs);
    </script>
</body>
</html>