<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; 
$email = $_POST['email'] ?? "";
$action = $_POST['action'] ?? "register"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // --- LOGICA REGISTRAZIONE ---
    if ($action === 'register' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];

        // 1. Validazione Email (.com / .it)
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
            $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
        }
        // 2. Validazione Password Complessa
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
    // --- LOGICA LOGIN ---
    elseif ($action === 'login' && isset($_POST['btn_submit'])) {
        $password = $_POST['pass'];
        
        $query = "SELECT * FROM utenti WHERE username = $1 AND password = $2";
        $result = pg_query_params($conn, $query, array($username, $password));

        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $_SESSION['user'] = $row['username'];
            // $_SESSION['nome'] = $row['nome']; 
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
    <link rel="stylesheet" href="css/login_reg.css">
    <link rel="icon" href="assets/favicon.ico">
    
</head>
<body style="background: #1d1d1f; color: white; padding-top: 100px;">

<header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;">
           &larr; Torna alla Home
        </a>
      </div>
    </header>
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
                    <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                </span>
            </div>
            <br><br>
            
            <input type="submit" id="btn-submit" name="btn_submit" value="Crea Account" style="padding: 10px 20px; cursor:pointer;" disabled>
        </form>
    </div>

    

    <script src="js/login_reg.js"></script>
</body>
</html>