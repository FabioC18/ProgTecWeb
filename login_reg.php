<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrati'])) {
    $password = $_POST['pass'];
    
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
            // $_SESSION['nome'] rimosso come richiesto
            
            // Reindirizzo alla Home Page con utente loggato
            header("Location: index.php");
            exit;
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
            <input type="password" id="pass" name="pass" style="width:100%" required><br><br>
            
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
            // Se tutti i campi sono pieni (trim toglie gli spazi vuoti)
            if (userIn.value.trim() !== "" && emailIn.value.trim() !== "" && passIn.value.trim() !== "") {
                btn.disabled = false; // Accendi il tasto
                btn.style.backgroundColor = ""; // Ripristina colore originale (o quello del CSS)
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