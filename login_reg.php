<?php
session_start();
require_once 'includes/db_config.php';

$errori = "";
$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$action = $_POST['action'] ?? "register";

// Accorpamento: Entrambe le logiche (login e register) richiedono che il metodo sia POST e che il bottone sia stato premuto.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_submit'])) {
    
    $password = $_POST['pass']; // Recuperato una sola volta per entrambe le azioni

    /* LOGICA DI REGISTRAZIONE */
    if ($action === 'register') {
        
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
            $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
        } elseif (strlen($password) < 8 ||
            !preg_match("/[A-Z]/", $password) ||
            !preg_match("/[0-9]/", $password) ||
            !preg_match("/[^a-zA-Z0-9]/", $password)) {
            
            $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
        } else {
            $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
            $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

            if (pg_num_rows($check_user) > 0) {
                $errori = "Errore: Username già in uso.";
            } elseif (pg_num_rows($check_email) > 0) {
                $errori = "Errore: Email già registrata.";
            } else {
                $safe_password = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO utenti (username, email, password) VALUES ($1, $2, $3)";
                $res = pg_query_params($conn, $query, array($username, $email, $safe_password));
                
                if ($res) {
                    $_SESSION['user'] = $username;
                    header("Location: index.php");
                    exit;
                }
            }
        }
    }
    /* LOGICA LOGIN */
    elseif ($action === 'login') {
        
        $query = "SELECT * FROM utenti WHERE username = $1";
        $result = pg_query_params($conn, $query, array($username));

        if ($result && pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $saved_password = $row['password'];

            if (password_verify($password, $saved_password)) {
                $_SESSION['user'] = $row['username'];
                header("Location: index.php");
                exit;
            } else {
                $errori = "Errore: Username o Password non corretti.";
            }
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
<body style="background: #1d1d1f; color: white;">

    <header class="header">
        <div class="header-content">
            <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;">
                &larr; Torna alla Home
            </a>
        </div>
    </header>

    <div class="panel">
        
        <div class="tab-switch">
            <span id="tab-reg" class="tab-active" onclick="switchMode('register', true)">Registrati</span>
            <span id="tab-log" class="tab-inactive" onclick="switchMode('login', true)">Login</span>
        </div>

        <p id="error-msg" style="color: red; text-align: center; min-height: 20px; font-weight: bold;">
            <?php echo $errori; ?>
        </p>

        <form name="authForm" action="login_reg.php" method="POST" style="position: sticky;">
            <input type="hidden" id="action-input" name="action" value="<?php echo htmlspecialchars($action); ?>">

            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%" required><br><br>

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