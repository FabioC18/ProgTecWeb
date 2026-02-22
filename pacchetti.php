<?php
session_start();

//Permette di evitare che il browser salvi la pagina in cache. Questa istruzione ci ha permesso di evitare dei bug 
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

require_once 'includes/db_config.php';

$is_logged = isset($_SESSION['user']); //verifica se l'utente è loggato 
$prenotazioni_disponibili = []; //Arrai che conterrà le date in cui l'utente avrà una camera alla quale non è ancora stato asseganto un pacchetto 
$messaggio_stato = ""; //Variabile per gestire i mesaggi di errore 

// 1. GESTIONE DELLA LOGICA UTENTE E PRENOTAZIONI

if ($is_logged) {//Se l'utente e loggato 
    $username = $_SESSION['user'];

    //Recupera l'ID dell'utente partendo dallo username in sessione
    $query_user = "SELECT id FROM utenti WHERE username = $1";
    $res_user = pg_query_params($conn, $query_user, array($username));

    //Se l'utente esiste salva il suo ID
    if ($res_user && pg_num_rows($res_user) > 0) {
        $user_row = pg_fetch_assoc($res_user);
        $user_id = $user_row['id'];

        //Recupera tutti i nomi dei pacchetti essistenti dal database e li inserisce in un array 
        $pacchetti_names = [];
        $q_pack = "SELECT nome FROM pacchetti";
        $r_pack = pg_query($conn, $q_pack);
        while ($row_p = pg_fetch_assoc($r_pack)) {
            $pacchetti_names[] = $row_p['nome'];
        }

        // Recupera tutte le prenotazioni effettuate dall'utente loggato
        $q_pren = "SELECT * FROM prenotazioni WHERE id_utente = $1";
        $r_pren = pg_query_params($conn, $q_pren, array($user_id));

        $mie_camere_per_data = [];//array per le camere prenotate 
        $miei_pacchetti_per_data = [];//array per i pacchetti associati

        //Per ogni prenotazione salva la data e il nome della casa e del pacchetto
        while ($row_pr = pg_fetch_assoc($r_pren)) {
            $data = $row_pr['data_prenotazione'];
            $nome_oggetto = $row_pr['nome_pacchetto'];

            //Controlla se la prenotazione effettuata è un pacchetto o una camere:
            // Se è un oacchetto, aggiunge un pacchetto alla lista pacchetti, incrementando il numero dei pacchetti e contando quanti sono
            //Se è una casa, aggiunge il nome della casa all'array per quella data, cioè lo salva come csmera prenotata
            if (in_array($nome_oggetto, $pacchetti_names)) {
                if (!isset($miei_pacchetti_per_data[$data])) {
                    $miei_pacchetti_per_data[$data] = 0;
                }
                $miei_pacchetti_per_data[$data]++;
            } else {
                if (!isset($mie_camere_per_data[$data])) {
                    $mie_camere_per_data[$data] = [];
                }
                $mie_camere_per_data[$data][] = $nome_oggetto;
            }
        }

        //Se l'utente non ha mai prenotato una casa allora non può aggiugnere nessun pacchetto
        if (empty($mie_camere_per_data)) {
            $messaggio_stato = "no_camere";
        } else {//Se l'utente ha prenotato almeno una casa
            foreach ($mie_camere_per_data as $data => $camere) {//Controlla tutte le prenotazioni da oggi in poi e ad ognuna di essa permette di associare un solo pacchetto
                if (strtotime($data) >= strtotime(date('Y-m-d'))) {
                    $num_camere = count($camere);
                    $num_pacchetti = isset($miei_pacchetti_per_data[$data]) ? $miei_pacchetti_per_data[$data] : 0;

                    if ($num_pacchetti < $num_camere) {//Se c'è la casa disponibile per quel giorno, l'utente può prenotare un pacchetto
                        $prenotazioni_disponibili[$data] = $camere[0];
                        $messaggio_stato = "ok";
                    }
                }
            }
            if ($messaggio_stato == "" && !empty($mie_camere_per_data)) {//Se ha le case prenotate ma ognuna di essa ha un pacchetto associato, mostra un messaggio 
                $messaggio_stato = "tutto_pieno";
            }
        }
    }
}

// 2. RECUPERO DEI PACCHETTI DAL DATABASE 
$query_pacchetti = "SELECT * FROM pacchetti ORDER BY id ASC"; //Seleziona tutti i pcchetti dal db per mostrarli nella pagina 
$result_pacchetti = pg_query($conn, $query_pacchetti);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset='utf-8'>
    <title>Pacchetti - Salerno Mare e Luci</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/pacchetti.css">
    <link rel="icon" href="assets/favicon.ico">
</head>
<body>

    <?php include 'includes/header.php'; ?>

<!-- Contenitore di tutti gli oggetti decorativi della pagina-->
    <div class="container">
        <img src="assets/unnamed-no-bg.png" class="object freccia" data-value="3"> <!-- data value è il valore numeri che influenzerà la velocità dell'animazione (parallax) -->
        <img src="assets/bacchette-no-bg.png" class="object top-right" data-value="12 ">
        <img src="assets/pall-no-bg.png" class ="object cent-top" data-value="-7">
        <img src="assets/bicchiere-no-bg.png" class="object top-left" data-value="-20">
        <img src="assets/bottiglia-no-bg.png" class="object top-left" data-value="11">
        <img src="assets/cuore1-no-bg.png" class="object cent-right" data-value="17">
        <img src="assets/cuore2-no-bg.png" class="object cent-right" data-value="-19">
        <img src="assets/cuore3-no-bg.png" class="object cent-left" data-value="-13">
        <img src="assets/cuore4-no-bg.png" class="object cent-left" data-value="15">
        <img src="assets/cuore5-no-bg.png" class="object cent-left" data-value="19">
        <img src="assets/noccioline-no-bg.png" class="object bott-left" data-value="11">
        <img src="assets/patatine-no-bg.png" class="object bott-left" data-value="-10">
        <img src="assets/sushi-no-bg.png" class="object top-right" data-value="-16">
        <img src="assets/pane-no-bg.png" class="object bott-right pane" data-value="12">
        <img src="assets/tagliere-no-bg.png" class="object bott-right tagliere" data-value="-18">
        <img src="assets/mais-no-bg.png" class="object bott-left" data-value="3">
    </div>

    <div class="wrapper">
        <div class="pannelli">
            <?php
            if ($result_pacchetti)://Ciclo per generare automaticamente i pannelli dei pacchetti 
                $count = 1;
                $total_rows = pg_num_rows($result_pacchetti); //Numero totale di pacchetti

                while ($row = pg_fetch_assoc($result_pacchetti))://Creazione di ID e classi CSS per ogni pacchetto
                    $id_panel = "panel" . $count;
                    $class_panel = ($count == 1) ? "panel" : "panel" . $count;
            ?>
            
            <article id="<?php echo $id_panel; ?>" class="<?php echo $class_panel; ?>">  <!-- Ogni pacchetto è un articolo unico con ID e classi dinamiche -->
                
                <div class="pack-base"> <!-- titolo pacchetto e parola include-->
                    <h3><?php echo htmlspecialchars($row['nome']); ?> <br> <span>include:</span></h3>

                    <?php if ($is_logged): ?><!-- Solo se l'utente è loggato mostra tutte le informazioni disponibili per ogni pacchetto-->
                        <p><?php echo htmlspecialchars($row['descrizione']); ?></p>
                        <p style="font-weight: bold; font-size: 1.5em; color: #FFD94A;">
                            € <?php echo htmlspecialchars($row['prezzo']); ?>
                        </p>

                       <!--Se l'utente non ha case dispoibili ma prova a prenotare un pacchetto, riceve a schermo la scritto seguente, con il link che riporta l'utente nella sezione case vacanza -->
                        <?php if ($messaggio_stato == "no_camere"): ?>
                            <p class="msg-warning">
                                <a href="camere.php" style="color: white; text-decoration: none;">
                                    Devi prenotare una camera per una data futura prima di aggiungere un pacchetto.
                                </a>
                            </p>

                       <!--Se l'utente ha tutte case con pacchetto associato ma prova a prenotare un pacchetto, riceve a schermo la scritto seguente, con il link che riporta l'utente nella sezione case vacanza -->
                        <?php elseif ($messaggio_stato == "tutto_pieno"): ?>
                            <p class="msg-warning">
                                <a href="camere.php" style="color: inherit; text-decoration: none;">
                                    Hai già associato un pacchetto a tutte le tue prenotazioni future. Prenota una nuova camera per avere un nuovo pacchetto.
                                </a>
                            </p>

                        <!-- form per prenotare un paccheto, include nome e prezzi nascosti -->
                        <?php elseif ($messaggio_stato == "ok"): ?>
                            <form action="salva_prenotazione.php" method="GET" class="booking-form"> <!-- crea un modulo da inviare al server, tramite metodo GET-->
                                <input type="hidden" name="nome" value="<?php echo htmlspecialchars($row['nome']); ?>">
                                <input type="hidden" name="prezzo" value="<?php echo $row['prezzo']; ?>">

                                 <!-- menu a tendina che permette di selezionare una delle date disponibili di una casa già prenotata per poter prenotare anche un pacchetto-->
                                <label for="data_pack_<?php echo $row['id']; ?>" style="font-size: 0.9em;">Associa alla prenotazione del:</label>
                                <select name="data" id="data_pack_<?php echo $row['id']; ?>" class="date-select" required>
                                    <?php foreach ($prenotazioni_disponibili as $data_disp => $nome_camera): ?>
                                        <option value="<?php echo $data_disp; ?>"> <!-- il valore che attende il server è la data -->
                                            <?php echo date("d/m/Y", strtotime($data_disp)); ?> - <?php echo $nome_camera; ?> <!-- il essaggio mostrato all'utnete e la data scelta con il nome della casa-->
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" class="btn-prenota-pack">Prenota Ora</button> <!--Quando il bottone viene premuto, tutti i dati creati precedentemente saranno inviati a salva_prenotazione.php-->
                            </form>
                        <?php endif; ?>

                    <?php else: ?><!-- se l'utente non è loggato, permette all'utente di cliccare il link che lo porterà nel punto di login -->
                        <p style="font-style: italic; color: #f7f7f7;">
                            <a href="login_reg.php" style="color: inherit; text-decoration: none;">
                                Registrati per visualizzare i dettagli completi e i prezzi riservati.
                            </a>
                        </p>
                    <?php endif; ?>
                </div>

                <figure> <!-- mostra l'immagine del pacchetto prendendola dalla cartella assets-->
                    <img src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" class="img-pack" alt="Pacchetto">
                </figure>

                <h4>*I PACCHETTI POSSONO ESSERE PERSONALIZZATI SU RICHIESTA <br>
                    <a href="#" onclick="scorriA('<?php echo ($count > 1) ? "panel".($count-1) : "panel".$total_rows; ?>'); return false;"> <!-- permette di spostarsi tra i pannelli-->
                        <img class="frslider" src="assets/arrow-circle-left.png" alt="Precedente"> <!-- icona freccia sinistra -->
                    </a>
                    <a href="#" onclick="scorriA('<?php echo ($count < $total_rows) ? "panel".($count+1) : "panel1"; ?>'); return false;">
                        <img class="frslider" src="assets/arrow-circle-right.png" alt="Successivo"> <!-- icona freccia destra -->
                    </a>
                </h4>
                
            </article>
            
            <?php
                    $count++;
                endwhile;
            endif;
            ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/pacchetti.js"></script>
    
</body>
</html>