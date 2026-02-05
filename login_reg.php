<?php
session_start();
require_once 'includes/db_config.php'; // Percorso corretto include

$errori = "";
// Inizializzo variabili per rendere il form "sticky" (non cancella i dati se c'è un errore)
$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$nome = $_POST['nome'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrati'])) {
    $password = $_POST['pass'];
    
    // 1. Controllo se lo USERNAME esiste già
    $check_user = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
    
    // 2. Controllo se l'EMAIL esiste già
    $check_email = pg_query_params($conn, "SELECT * FROM utenti WHERE email = $1", array($email));

    if (pg_num_rows($check_user) > 0) {
        $errori = "Errore: questo username è già in uso.";
    } 
    elseif (pg_num_rows($check_email) > 0) {
        $errori = "Errore: questa email è già registrata.";
    } 
    else {
        // 3. Inserimento nuovo utente (Aggiunto il campo 'nome' per la home page)
        // Assicurati che la tua tabella abbia le colonne: username, password, email, nome
        $query = "INSERT INTO utenti (username, email, password, nome) VALUES ($1, $2, $3, $4)";
        $res = pg_query_params($conn, $query, array($username, $email, $password, $nome));
        
        if ($res) {
            // 4. LOGIN AUTOMATICO: Imposto la sessione SUBITO
            $_SESSION['user'] = $username; 
            $_SESSION['nome'] = $nome; // Fondamentale per il "Ciao, Nome" nella home
            
            // Reindirizzo alla Home Page che ora ti vedrà loggato
            header("Location: index.php");
            exit;
        } else {
            $errori = "Errore generico durante la registrazione.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Login e Registrazione - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js"></script> 
    <style>
        /* Stile per il bottone disabilitato */
        input[type="submit"]:disabled {
            background-color: #555 !important;
            color: #888 !important;
            cursor: not-allowed !important;
            border: 1px solid #444 !important;
        }
        /* Stile per il bottone abilitato */
        input[type="submit"] {
            background-color: #FFD94A;
            color: black;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body style="background: #1d1d1f; color: white; padding-top: 100px;">

    <div style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
        <h2 style="text-align:center; color:#FFD94A;">Registrazione</h2>
        
        <?php if($errori): ?>
            <p style="color: #ff4d4d; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px; text-align: center;">
                <?php echo $errori; ?>
            </p>
        <?php endif; ?>
        
        <form name="regForm" action="" method="POST">
            
            <label>Nome Completo (per il saluto):</label><br>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" style="width:100%; padding:8px; margin-bottom:10px;" required placeholder="Es. Mario Rossi"><br>

            <label>Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%; padding:8px; margin-bottom:10px;" required placeholder="Scrivi il tuo username"><br>
            
            <label>E-mail:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width:100%; padding:8px; margin-bottom:10px;" required placeholder="esempio@email.com"><br>
            
            <label>Password:</label><br>
            <input type="password" id="pass" name="pass" style="width:100%; padding:8px; margin-bottom:20px;" required placeholder="Scegli una password"><br>
            
            <input type="submit" id="btn-submit" name="registrati" value="Crea Account" style="width: 100%; padding: 10px 20px; cursor:pointer; font-weight: bold;" disabled>
        </form>
        
        <hr style="border-color: #444; margin-top: 20px;">
        <p style="text-align:center;">Hai già un account? <a href="index.php" style="color: #FFD94A; text-decoration: none;">Torna alla Home</a></p>
    </div>

    <script>
        // Seleziono gli elementi
        const nomeInput = document.getElementById('nome');
        const userInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const passInput = document.getElementById('pass');
        const submitBtn = document.getElementById('btn-submit');

        // Funzione di controllo
        function checkForm() {
            // Verifica se tutti i campi hanno del testo (trim toglie spazi vuoti)
            if (nomeInput.value.trim() !== "" && 
                userInput.value.trim() !== "" && 
                emailInput.value.trim() !== "" && 
                passInput.value.trim() !== "") {
                
                submitBtn.disabled = false; // Attiva il tasto
            } else {
                submitBtn.disabled = true;  // Disattiva il tasto
            }
        }

        // Aggiungo "l'ascolto" su ogni tasto che premi nei campi
        nomeInput.addEventListener('input', checkForm);
        userInput.addEventListener('input', checkForm);
        emailInput.addEventListener('input', checkForm);
        passInput.addEventListener('input', checkForm);
    </script>
</body>
</html>