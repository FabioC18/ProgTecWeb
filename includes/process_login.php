<?php
session_start();
include '<includes>db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['pass'];

    // Verifica dell'utente nel database [cite: 77, 88]
    $query = "SELECT * FROM utenti WHERE username = $1 AND password = $2";
    $result = pg_query_params($conn, $query, array($user, $pass));

    if (pg_num_rows($result) == 1) {
        $row = pg_fetch_assoc($result);
        $_SESSION['user'] = $row['username'];
        $_SESSION['nome'] = $row['nome'];
        header("Location: index.php");
    } else {
        header("Location: login_reg.php?error=Login fallito");
    }
}
?>