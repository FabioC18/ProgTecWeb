<?php
session_start();
require_once 'includes/db_config.php';

// 1. Controllo se l'utente è loggato
if (!isset($_SESSION['user'])) {
    // Se non è loggato, lo mando al login
    header("Location: login_reg.php?msg=Devi accedere per prenotare");
    exit;
}

// 2. Recupero i dati dal link (GET)
$nome_oggetto = $_GET['nome'] ?? '';
$prezzo = $_GET['prezzo'] ?? 0;
$username = $_SESSION['user'];
$telefono_proprietario = "393497534392"; // Il tuo numero

if ($nome_oggetto) {
    // 3. Recupero l'ID dell'utente loggato
    $query_user = "SELECT id FROM utenti WHERE username = $1";
    $res_user = pg_query_params($conn, $query_user, array($username));
    
    if ($res_user && pg_num_rows($res_user) > 0) {
        $user_row = pg_fetch_assoc($res_user);
        $user_id = $user_row['id'];

        // 4. INSERISCO LA PRENOTAZIONE NEL DATABASE
        $query_insert = "INSERT INTO prenotazioni (id_utente, nome_pacchetto, prezzo) VALUES ($1, $2, $3)";
        $result = pg_query_params($conn, $query_insert, array($user_id, $nome_oggetto, $prezzo));

        if ($result) {
            // 5. Se salvato con successo, reindirizzo a WhatsApp
            $messaggio_wa = "Salve, ho effettuato la prenotazione sul sito per: " . $nome_oggetto . ". Attendo conferma.";
            $link_wa = "https://wa.me/" . $telefono_proprietario . "?text=" . urlencode($messaggio_wa);
            
            header("Location: " . $link_wa);
            exit;
        } else {
            echo "Errore nel salvataggio della prenotazione.";
        }
    } else {
        echo "Utente non trovato.";
    }
} else {
    echo "Dati mancanti.";
    echo "<br><a href='index.php'>Torna indietro</a>";
}
?>