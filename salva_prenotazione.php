<?php
/* INIZIALIZZAZIONE */
session_start(); 
require_once 'includes/db_config.php'; 

// 1. Controllo Login
if (!isset($_SESSION['user'])) {
    header("Location: login_reg.php?msg=" . urlencode("Devi accedere per prenotare"));
    exit;
}

// 2. Recupero Dati
$nome_oggetto = $_GET['nome'] ?? '';
$prezzo = $_GET['prezzo'] ?? 0;
$data_prenotazione = $_GET['data'] ?? ''; 
$room_id = $_GET['id'] ?? ''; 
$username = $_SESSION['user'];
$telefono_proprietario = "393497534392"; 

if ($nome_oggetto && $data_prenotazione) {

    // 2.1 VALIDAZIONE DATA
    if (strtotime($data_prenotazione) < strtotime(date('Y-m-d'))) {
        $msg_errore = "Non puoi selezionare una data passata.";
        // MODIFICA: Aggiunto l'ancora #camera_ID alla fine
        header("Location: camere.php?error=" . urlencode($msg_errore) . "&id=" . $room_id . "#camera_" . $room_id);
        exit;
    }

    // 2.2 CHECK DISPONIBILITÀ
    $query_check = "SELECT id FROM prenotazioni 
                    WHERE nome_pacchetto = $1 
                    AND data_prenotazione = $2";
    
    $res_check = pg_query_params($conn, $query_check, array($nome_oggetto, $data_prenotazione));

    if (pg_num_rows($res_check) > 0) {
        // CASO OCCUPATO
        $data_format_err = date("d/m/Y", strtotime($data_prenotazione));
        $msg_errore = "Ci dispiace, la $nome_oggetto è già occupata per il giorno $data_format_err. Scegli un'altra data.";
        
        // MODIFICA: Aggiunto l'ancora #camera_ID alla fine per far scendere la pagina
        header("Location: camere.php?error=" . urlencode($msg_errore) . "&id=" . $room_id . "#camera_" . $room_id);
        exit;
    }

    // 3. Recupero l'ID dell'utente loggato
    $query_user = "SELECT id FROM utenti WHERE username = $1";
    $res_user = pg_query_params($conn, $query_user, array($username));
    
    if ($res_user && pg_num_rows($res_user) > 0) {
        $user_row = pg_fetch_assoc($res_user);
        $user_id = $user_row['id'];

        // 4. INSERISCO LA PRENOTAZIONE
        $query_insert = "INSERT INTO prenotazioni (id_utente, nome_pacchetto, data_prenotazione, prezzo) VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query_insert, array($user_id, $nome_oggetto, $data_prenotazione, $prezzo));

        if ($result) {
            // 5. Reindirizzo a WhatsApp
            $data_format = date("d/m/Y", strtotime($data_prenotazione));
            $messaggio_wa = "Salve, ho effettuato la prenotazione sul sito per: " . $nome_oggetto . " per la data " . $data_format . ". Attendo conferma.";
            $link_wa = "https://wa.me/" . $telefono_proprietario . "?text=" . urlencode($messaggio_wa);
            
            header("Location: " . $link_wa); 
            exit;
        } else {
            echo "Errore nel salvataggio della prenotazione sul database.";
        }
    } else {
        echo "Errore critico: Utente non trovato.";
    }
} else {
    // Se mancano i dati
    header("Location: camere.php?error=" . urlencode("Seleziona una data valida.") . "&id=" . $room_id . "#camera_" . $room_id);
    exit;
}
?>