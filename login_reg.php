<?php
session_start();
require_once 'includes/db_config.php';

$errori = ""; //Variabile che conterrà i messaggi di errore da mostrare nella pagina 

//Controlla se il campo email e username sono stati correttamente inviati ed elimina possibili spazi all'interno (anche se bloccati da js)
$username = isset($_POST['username']) ? str_replace(' ', '', $_POST['username']) : "";
$email = isset($_POST['email']) ? str_replace(' ', '', $_POST['email']) : "";
$action = $_POST['action'] ?? "register";// Recupera l'azione invisibile login o register; se non specificata, il valore predefinito è 'register'

// Accorpamento: Entrambe le logiche (login e register) richiedono che il metodo sia POST e che il bottone sia stato premuto.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['btn_submit'])) {
    
    $password = $_POST['pass']; // Recupera la password per entrambe le azioni

    /* LOGICA DI REGISTRAZIONE */
    if ($action === 'register') {
        //verifica il formato standard dell'email
        if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $email)) {
            $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
        } elseif (strlen($password) < 8 || //verifica dei requisiti di sicurezza della password
            !preg_match("/[A-Z]/", $password) ||
            !preg_match("/[0-9]/", $password) ||
            !preg_match("/[^a-zA-Z0-9]/", $password)) {
        } else {//controlla nel database se username o email scelte sono già presenti
            $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
            $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));
            //Se username o l'mail esistono  gia vengono inviati i rispettivi messaggi di errore
            if (pg_num_rows($check_user) > 0) {
                $errori = "Errore: Username già in uso.";
            } elseif (pg_num_rows($check_email) > 0) {//
                $errori = "Errore: Email già registrata.";
            } else {
                $safe_password = password_hash($password, PASSWORD_DEFAULT); //crea un hash sicuro per salvare la password nel database. Non viene mai salvata in chiaro
                $query = "INSERT INTO utenti (username, email, password) VALUES ($1, $2, $3)";//Prepara la query SQL per inserire il nuovo utente
                $res = pg_query_params($conn, $query, array($username, $email, $safe_password)); //esegue la query con i parametri creati e resi sicuri 
                

                if ($res) {// Se l'inserimento ha successo, salva lo username in sessione e reindirizza alla home
                    $_SESSION['user'] = $username;
                    header("Location: index.php");
                    exit;
                }
            }
        }
    }
    /* LOGICA LOGIN */
    elseif ($action === 'login') {//Se l'utente vuole acceddere viene eseguito questo codice 
        
        $query = "SELECT * FROM utenti WHERE username = $1"; //Cerca nel database l'utente corrispondente allo username inserito 
        $result = pg_query_params($conn, $query, array($username));

        if ($result && pg_num_rows($result) == 1) {//Se viene trovato l'utente, estrai i suoi dati e recupera la password scritta tramite hash
            $row = pg_fetch_assoc($result);
            $saved_password = $row['password'];

            if (password_verify($password, $saved_password)) {//Confronta la password inserita sul sito con quello in hash nel servere
                $_SESSION['user'] = $row['username']; //Se le 2 password corrispondono l'utente viene indirizzato alla home
                header("Location: index.php");
                exit;
            } else {//Se le 2 password non corrispondono, l'utente invia un messaggio di errore
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

<!-- HEADER-->
    <header class="header">
        <div class="header-content">
            <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;"> <!--- design della freccia -->
                &larr; Torna alla Home <!-- &larr: entità di carattere che permette di visualizzare la freccia a sinistra-->
            </a>
        </div>
    </header>

    <div class="panel">
        
        <div class="tab-switch"><!-- tag che permette di attivare la modalità registrazione o login-->
            <span id="tab-reg" class="tab-active" onclick="switchMode('register', true)">Registrati</span>
            <span id="tab-log" class="tab-inactive" onclick="switchMode('login', true)">Login</span>
        </div>

        <!--stampa dei messaggi di errore -->
        <p id="error-msg" class="error" style="color: red; text-align: center; min-height: 20px; font-weight: bold;">
            <?php echo $errori; ?>
        </p>

        <!-- creazione del modulo html per l'inserimento dei dati -->
        <form name="authForm" action="login_reg.php" method="POST" style="position: sticky;">
            <input type="hidden" id="action-input" name="action" value="<?php echo htmlspecialchars($action); ?>">

            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%" required pattern="[^\s]+"><br><br>

            <div id="email-container"> <!-- Creazione di un div separato per l'email perchè non utilizzata nella sezione login-->
                <label>E-mail:</label><br>
                <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%" placeholder="esempio@dominio.it" pattern="[^\s]+"><br><br>
            </div>

            <label>Password:</label><br>
            <div class="password-container"> <!-- qui creiamo il div per contenre più elementi, occhio e tooltip-->
                <!--tooltip per il corretto inserimento della password -->
                <div id="password-tooltip" class="tooltip-requirements">
                    <strong>Requisiti Password:</strong>
                    <ul>
                        <li>Minimo 8 caratteri</li>
                        <li>Almeno una Maiuscola</li>
                        <li>Almeno un Numero</li>
                        <li>Almeno un Carattere Speciale</li>
                    </ul>
                </div>
                
                <!--campo di input per la password i cui caratteri vengono nascosti tramite o pallini o mostrati -->
                <input type="password" id="pass" name="pass" style="width:100%; padding-right: 40px;" required placeholder="Password" pattern="[^\s]+">
                <span class="toggle-password" onclick="togglePassword()"> <!--richiama la funzione js che mostra o nasconde la password -->
                    <img src="assets/eye-slash.png" id="icon-slash"><!-- occhio barrato-->
                    <img src="assets/eye.png" id="icon-eye" hidden> <!--occhio aperto -->
                </span>
                
            </div>
            <br><br>

            <input type="submit" id="btn-submit" name="btn_submit" value="Crea Account" style="padding: 10px 20px; cursor:pointer;" disabled> <!-- bottone che permette di inviare i dati inseriti che passerà da "crea account" ad "accedi" e viceversa tramite lo script js-->
        </form>
        
    </div>

    <script src="js/login_reg.js"></script>
    
</body>
</html>