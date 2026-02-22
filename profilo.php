<?php
session_start(); 
require_once 'includes/db_config.php'; 

if (!isset($_SESSION['user'])) { //Se l'utente non è loggato viene rispedito al login
    header("Location: login_reg.php");
    exit;
}

//inizializzazione delle varibili di stato e dei messaggi di errore e successo 
$currentUser = $_SESSION['user']; 
$msg = ""; 
$errori = ""; 

//Recupera le informazioni complete dell'utente dal database effettuando la query in modo sicuro 
$query_info = "SELECT * FROM utenti WHERE username = $1";
$res_info = pg_query_params($conn, $query_info, array($currentUser));
$user_data = pg_fetch_assoc($res_info);
$user_id = $user_data['id']; //salva l'id dell'utentes

//Controlla se il bottone premuto sia quello di "salva modifiche"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    //elimina possibili spazi nei form anche se bloccati già da js
    $new_user  = str_replace(' ', '', $_POST['username']);
    $new_email = str_replace(' ', '', $_POST['email']);
    $new_pass = $_POST['pass'];

    
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $new_email)) { // validazione del formato email
        $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
    }
    elseif (strlen($new_pass) < 8 || //validazione del formato password
        !preg_match("/[A-Z]/", $new_pass) || 
        !preg_match("/[0-9]/", $new_pass) || 
        !preg_match("/[^a-zA-Z0-9]/", $new_pass)) {
        $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
    } 
    else {
        $check_u = pg_query_params($conn, "SELECT id FROM utenti WHERE username = $1 AND id != $2", array($new_user, $user_id)); //Legge gli username inserite nel database 
        $check_e = pg_query_params($conn, "SELECT id FROM utenti WHERE email = $1 AND id != $2", array($new_email, $user_id)); //Leggi le email inserite nel database

        if (pg_num_rows($check_u) > 0) {// verifica se lo username inserito è gia usato da un altro utente
            $errori = "Username già occupato da un altro utente.";
        } elseif (pg_num_rows($check_e) > 0) { // verifica se l'email inserita è gia usata da un altro utente 
            $errori = "Email già utilizzata da un altro utente.";
        } else { //Trasforma la nuova password in caratteri hash
            $safe_hash=password_hash($new_pass, PASSWORD_DEFAULT);
            $update_sql = "UPDATE utenti SET username = $1, email = $2, password = $3 WHERE id = $4"; //Aggiona la password nel database
            $res_up = pg_query_params($conn, $update_sql, array($new_user, $new_email, $safe_hash, $user_id));

            if ($res_up) {//Se lìaggiornamento è andato a buon fine invia un messaggio di successo
                $msg = "Dati aggiornati con successo!";
                $_SESSION['user'] = $new_user;
                $user_data['username'] = $new_user;
                $user_data['email'] = $new_email;
                $user_data['password'] = $new_pass;
            } else {//Se l'aggiornamento non è andato a buon fine invia un messaggio di errore 
                $errori = "Errore durante l'aggiornamento.";
            }
        }
    }
}

$query_pren = "SELECT * FROM prenotazioni WHERE id_utente = $1 ORDER BY data_prenotazione ASC"; //Prende le prenotazioni dell'utente
$res_pren = pg_query_params($conn, $query_pren, array($user_id)); //Esegue la query 
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Personale - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/profilo.css">
    <link rel="icon" href="assets/favicon.ico">

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""/> <!-- import della libreria leafeet per visualizzare la mappa esteticamente in modo corretto-->
    
</head>
<body>

<!-- HEADER-->
    <header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;"> <!--- design della freccia -->
           &larr; Torna alla Home<!-- &larr: entità di carattere che permette di visualizzare la freccia a sinistra-->
        </a>
        <div style="display:flex; gap:20px; align-items:center;">
             <span>Ciao, <strong><?php echo htmlspecialchars($currentUser); ?></strong></span>
             <li class="menu-item-session"><a href="logout.php" onclick="return confirm('Sei sicuro di voler uscire?');">Logout</a></li> <!-- messaggio di logout di conferma-->
        </div>
      </div>
    </header>

    <div class="container">
        
        <?php if($msg): ?> <div class="success"><?php echo $msg; ?></div> <?php endif; ?> <!-- mostra messaggio di successo-->
        <?php if($errori): ?> <div class="error"><?php echo $errori; ?></div> <?php endif; ?> <!-- mostra messaggio di errore-->

        <div class="profile-grid"> <!-- grigli del profilo, a sinistra dati utente e dentra le prenotazioni  -->
            
            <div class="box1"> <!-- contenitore dati utente-->
                <h2>I Miei Dati</h2>
                <form method="POST" action="">
                    <label>Username</label> <!-- campo di testo per lo username-->
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required pattern= "[^\s]+"> 

                    <label>Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required placeholder="nome@dominio.it" pattern= "[^\s]+">

                    <label>Password (Modifica)</label>
                    <div class="password-container"> <!-- creazione di un nuovo contenitore per la password perchè dobbiamo aggungere occhio e tooltip-->
                        <input type="password" id="pass" name="pass" placeholder="Inserisci la nuova password" required pattern="[^\s]+"> <!-- destro dentro il form della password-->
                        <span class="toggle-password" onclick="togglePassword()"> <!--chiamata alla funzione js che permette di mostrare e nascondere la password -->
                             <img src="assets/eye-slash.png" id="icon-slash"> <!-- icona che mostra la passoword nascosta -->
                              <img src="assets/eye.png" id="icon-eye" hidden> <!--icona che mostra la password visibile-->
                        </span>

                        <!--tooltip che mostra le regole della password -->
                        <div id="password-tooltip" class="tooltip-requirements">
                            <strong>Requisiti Password:</strong>
                            <ul>
                                <li>Minimo 8 caratteri</li>
                                <li>Almeno una Maiuscola</li>
                                <li>Almeno un Numero</li>
                                <li>Almeno un Carattere Speciale</li>
                            </ul>
                        </div>
                    </div>

                    <input type="submit" id="btn-submit" name="update_profile" value="Salva Modifiche" class="btn-save"> <!-- invio del form-->
                </form>
            </div>

            <div class="box"> <!-- contenitore prenotazioni-->
                <h2>Le Mie Prenotazioni</h2>
                <?php if (pg_num_rows($res_pren) > 0): ?>
                    <table> <!-- tabella con campi: prenotazionw, data prenotazione, Prezzo-->
                        <thead>
                            <tr>
                                <th>Prenotazione</th>
                                <th>Data prenotazione</th>
                                <th>Prezzo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = pg_fetch_assoc($res_pren)): ?> <!-- permette di scorrere tutte le prenotazioni --> 
                            <tr>
                                <td><?php echo htmlspecialchars($row['nome_pacchetto']); ?></td> <!-- mostra il nome del pachetto-->
                                <td><?php echo date("d/m/Y", strtotime($row['data_prenotazione'])); ?></td> <!-- mostra la data di prenotazione-->
                                <td>€ <?php echo $row['prezzo']; ?></td> <!-- mostra il prezzo-->
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?> 
                    <p style="margin-top:20px; color:#ccc;">Non hai ancora effettuato prenotazioni.</p> <!-- testo mostrato se non ci sono prenotazioni-->
                    <a href="camere.php"  class="btn-save" >Vai alle case</a> <!-- link che se non ci sono case prenotate, riporta alla sezione case-->
                    <a href="pacchetti.php"  class="btn-save" >Vai ai pacchetti</a><!-- link che se non ci sono pacchetti prenotati, riporta alla sezione pacchetti-->
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div class="container">
        <button onclick="getLocation()" class="btn-save" style="margin-bottom: 20px;"> <!-- Quando si clicca il bottone, il browser richiede di poter usare la posizione attuale-->
            📍 Mostra la mia posizione sulla mappa
        </button>
        
        <div id="map"></div> <!-- contenitore dove verrà inserita la mappa-->
        <p id="geo-error" class="error" style="display:none;"></p> <!-- mostra errori: ad esempio l'utente nega la posizione -->
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script> <!-- codice javaScript importato, che permette all'utente di interagire con la mappa-->

    <script src="js/profilo.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>