<?php
include 'includes/db_config.php';
session_start();

$errori = "";
$username = $_POST['username'] ?? ""; // Per rendere il form sticky [cite: 80]
$nome = $_POST['nome'] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrati'])) {
    $password = $_POST['pass'];
    
    // Controllo se l'utente esiste già [cite: 77]
    $check = pg_query_params($conn, "SELECT * FROM utenti WHERE username = $1", array($username));
    
    if (pg_num_rows($check) > 0) {
        $errori = "Errore: lo username è già presente nel database."; 
    } else {
        // Inserimento nuovo utente
        $query = "INSERT INTO utenti (username, password, nome) VALUES ($1, $2, $3)";
        $res = pg_query_params($conn, $query, array($username, $password, $nome));
        if ($res) {
            header("Location: login_reg.php?msg=Registrazione completata!");
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
    <script src="validation.js"></script> </head>
<body style="background: #1d1d1f; color: white; padding-top: 100px;">
    <div style="max-width: 400px; margin: 0 auto; background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px;">
        <h2>Registrazione</h2>
        <p style="color: red;"><?php echo $errori; ?></p>
        
        <form name="regForm" action="login_reg.php" method="POST" onsubmit="return validateRegForm()">
            <label>Username:</label><br>
            <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" style="width:100%"><br><br>
            
            <label>Nome:</label><br>
            <input type="text" name="nome" value="<?php echo htmlspecialchars($nome); ?>" style="width:100%"><br><br>
            
            <label>Password:</label><br>
            <input type="password" name="pass" style="width:100%"><br><br>
            
            <input type="submit" name="registrati" value="Crea Account" style="padding: 10px 20px; cursor:pointer;">
        </form>
        <hr>
        <p>Hai già un account? <a href="index.php" style="color: cyan;">Torna alla Home</a> per accedere.</p>
    </div>
</body>
</html>