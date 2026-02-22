<?php

session_start(); 
require_once 'includes/db_config.php'; 

//Controllo Login: se l'utente non è loggato non può prenotare
if (!isset($_SESSION['user'])) {
    header("Location: login_reg.php?msg=" . urlencode("Devi accedere per prenotare")); // Reindirizza al login passando un messaggio informativo
    exit;
}

// Recupera le informazioni inviate tramite GET(nome camera/pacchetto, prezzo,data,id,nome utente)
$nome_oggetto = $_GET['nome'] ?? '';
$prezzo = $_GET['prezzo'] ?? 0;
$data_prenotazione = $_GET['data'] ?? ''; 
$room_id = $_GET['id'] ?? ''; 
$username = $_SESSION['user'];
$telefono_proprietario = "393497534392"; 

if ($nome_oggetto && $data_prenotazione) {//Verifiica che i dati nome e data siano presenti

    // 
    if (strtotime($data_prenotazione) < strtotime(date('Y-m-d'))) {//Impedisce che vengano effettuate prenotazioni per giorni già passati
        $msg_errore = "Non puoi selezionare una data passata.";
        header("Location: camere.php?error=" . urlencode($msg_errore) . "&id=" . $room_id . "#camera_" . $room_id); // torna alla pagina camere utilizzando però un ancora:
                                                                                                                    //  questo ci permette di ritornare indietro nel punto che abbiamo lasciato per effettuare la prenotazione e non all'inizio della pagina
        exit;
    }

    // CHECK DISPONIBILITÀ
    $query_check = "SELECT id FROM prenotazioni WHERE nome_pacchetto = $1 AND data_prenotazione = $2"; //interroga il database per vedere se la casa richiesta è gia occupata per quel giorno
    
    $res_check = pg_query_params($conn, $query_check, array($nome_oggetto, $data_prenotazione)); //Esegue la query

    if (pg_num_rows($res_check) > 0) {//Se il giorno scelto è gia occupato
        $data_format_err = date("d/m/Y", strtotime($data_prenotazione));
        $msg_errore = "Ci dispiace, la $nome_oggetto è già occupata per il giorno $data_format_err. Scegli un'altra data."; //Invia il messaggio di errore
        
        header("Location: camere.php?error=" . urlencode($msg_errore) . "&id=" . $room_id . "#camera_" . $room_id); //Utilizzando l'ancora, permette di ritornare alla case scelta per visualizzare l'errore e non a inizio pagina
        exit;
    }

    $query_user = "SELECT id FROM utenti WHERE username = $1"; // Recupero l'ID dell'utente loggato dal database
    $res_user = pg_query_params($conn, $query_user, array($username)); //Esegue la query 
    
    if ($res_user && pg_num_rows($res_user) > 0) {//Verifica che l'utente esista, confermando l'ID
        $user_row = pg_fetch_assoc($res_user);
        $user_id = $user_row['id'];

        $query_insert = "INSERT INTO prenotazioni (id_utente, nome_pacchetto, data_prenotazione, prezzo) VALUES ($1, $2, $3, $4)";  // Dopo aver effettuato tutti i controlli inserisce la prenotazione nel database
        $result = pg_query_params($conn, $query_insert, array($user_id, $nome_oggetto, $data_prenotazione, $prezzo)); //Esegue la query 

        if ($result) {//Se il salvataggio va a buon fine
            // Reindirizzamento a WhatsApp,  viene creato il link per poter confermare ulteriormente su whatsapp 
            $data_format = date("d/m/Y", strtotime($data_prenotazione));
            $messaggio_wa = "Salve, ho effettuato la prenotazione sul sito per: " . $nome_oggetto . " per la data " . $data_format . ". Attendo conferma."; //Messaggio precompilato che viene scritto appena si entra su whatsapp
            $link_wa = "https://wa.me/" . $telefono_proprietario . "?text=" . urlencode($messaggio_wa); //creazione del link
            
            header("Location: " . $link_wa); //sposta l'utente dal sito a whatsapp
            exit;
        } else {//Se il salvataggio non va a buon fine
            echo "Errore nel salvataggio della prenotazione sul database."; //invio del messaggio di errore 
        }
    } 
    } else {
        // Se mancano i dati
        header("Location: camere.php?error=" . urlencode("Seleziona una data valida.") . "&id=" . $room_id . "#camera_" . $room_id); //torna alla pagina delle case e mostra l'erroe
        exit;
    }
?>