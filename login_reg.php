<?php
/*INIZIALIZZAZIONE*/

session_start(); // Avvia la sessione per gestire l'utente loggato
require_once 'includes/db_config.php'; //Connessione al database PostgreSQL


$errori = ""; //variabile per accumulare i messaggi di errore 
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";
$action = $_POST['action'] ?? "register"; //determina se l'utente scegli di registrarsi o fare il login 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    /* LOGICA DI REGISTRAZIONE */
    if ($action === 'register' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];

        // 1. Validazione Email (.com / .it)
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
            $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
        }
        // 2. Validazione Password 
        elseif (strlen($password) < 8 || 
            !preg_match("/[A-Z]/", $password) || 
            !preg_match("/[0-9]/", $password) || 
            !preg_match("/[^a-zA-Z0-9]/", $password)) {
            
            $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
        }
        else {
            // Controlli Database: evita duplicati di username o email 
            $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
            $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

            if (pg_num_rows($check_user) > 0) $errori = "Errore: Username già in uso.";
            elseif (pg_num_rows($check_email) > 0) $errori = "Errore: Email già registrata.";
            else {
                // Inserimento nuovo utente
                $query = "INSERT INTO utenti (username, email, password) VALUES ($1, $2, $3)";
                $res = pg_query_params($conn, $query, array($username, $email, $password));
                if ($res) {
                    $_SESSION['user'] = $username; //permette il login automatico dopo la registrazione
                    header("Location: index.php");
                    exit;
                }
            }
        }
    }
    // --- LOGICA LOGIN ---
    elseif ($action === 'login' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];
        
        //Verifica le credenziali cercando nel DataBase le coppie username e password
        $query = "SELECT * FROM utenti WHERE username = $1 AND password = $2";
        $result = pg_query_params($conn, $query, array($username, $password));

        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $_SESSION['user'] = $row['username'];  //Salva l'utente che sta navigando 
            header("Location: index.php"); //dopo l'accesso, invia l'utente direttamente nella home
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
    <link rel="stylesheet" href="css/login_reg.css">
    <link rel="icon" href="assets/favicon.ico">
    
</head>
<body style="background: #1d1d1f; color: white; ">

<header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;">
           &larr; Torna alla Home
        </a>
      </div>
    </header>
    <div class= "panel">
        
        <div class="tab-switch">
            <span id="tab-reg" class="tab-active" onclick="switchMode('register')">Registrati</span>
            <span id="tab-log" class="tab-inactive" onclick="switchMode('login')">Login</span>
        </div>

        <p style="color: red; text-align: center; min-height: 20px;"><?php echo $errori; ?></p>
        
        <form name="authForm" action="login_reg.php" method="POST" style="position: sticky;">
            <input type="hidden" id="action-input" name="action" value="register">

            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%" required ><br><br>
            
            <div id="email-container">
                <label>E-mail:</label><br>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%" placeholder="esempio@dominio.it"><br><br>
            </div>
            
            <label>Password:</label><br>
            <div class="password-container">
                <div id="password-tooltip" class="tooltip-requirements">
                    <strong>Requisiti Password:</strong>
                    <ul>
                        <li>Minimo 8 caratteri</li>
                        <li>Almeno una Maiuscola</li>
                        <li>Almeno un Numero</li>
                        <li>Almeno un Carattere Speciale</li>
                    </ul>
                </div>
                
                <input type="password" id="pass" name="pass" style="width:100%; padding-right: 40px;" required placeholder="Password">
                <span class="toggle-password" onclick="togglePassword()">
                    <img src="assets/eye-slash.png" id="icon-slash">
                    <img src="assets/eye.png" id="icon-eye" hidden>
                </span>
            </div>
            <br><br>
            
            <input type="submit" id="btn-submit" name="btn_submit" value="Crea Account" style="padding: 10px 20px; cursor:pointer;" disabled>
        </form>
    </div>

    <script src="js/login_reg.js"></script>
</body>
</html>