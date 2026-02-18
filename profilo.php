<?php
/*INIZIALIZZAZIONE*/

session_start(); // Avvia la sessione per gestire l'utente loggato
require_once 'includes/db_config.php'; //Connessione al database PostgreSQL

// // Impedisce l'accesso ai non loggati: se la sessione è vuota, rimanda al login
if (!isset($_SESSION['user'])) {
    header("Location: login_reg.php");
    exit;
}

$currentUser = $_SESSION['user']; // Recupera il nome utente dalla sessione
$msg = ""; // Messaggio di conferma aggiornamento
$errori = ""; // Messaggio di errore validazione

// RECUPERO DATI UTENTE
// Recupera tutte le informazioni dell'utente loggato 
$query_info = "SELECT * FROM utenti WHERE username = $1";
$res_info = pg_query_params($conn, $query_info, array($currentUser));
$user_data = pg_fetch_assoc($res_info);
$user_id = $user_data['id']; 

// GESTIONE MODIFICA CREDENZIALI
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_user = $_POST['username'];
    $new_email = $_POST['email'];
    $new_pass = $_POST['pass'];

    // Validazione email (.com o .it)
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $new_email)) {
        $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
    }
    // Validazione Password
    elseif (strlen($new_pass) < 8 || 
        !preg_match("/[A-Z]/", $new_pass) || 
        !preg_match("/[0-9]/", $new_pass) || 
        !preg_match("/[^a-zA-Z0-9]/", $new_pass)) {
        $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
    } 
    else {
        // Controllo univocità: verifica che il nuovo username/email non appartengano già ad altri utenti (id diverso)
        $check_u = pg_query_params($conn, "SELECT id FROM utenti WHERE username = $1 AND id != $2", array($new_user, $user_id));
        $check_e = pg_query_params($conn, "SELECT id FROM utenti WHERE email = $1 AND id != $2", array($new_email, $user_id));

        if (pg_num_rows($check_u) > 0) {
            $errori = "Username già occupato da un altro utente.";
        } elseif (pg_num_rows($check_e) > 0) {
            $errori = "Email già utilizzata da un altro utente.";
        } else {
            // Aggiornamento credenziali 
            $safe_hash=password_hash($new_pass, PASSWORD_DEFAULT);
            $update_sql = "UPDATE utenti SET username = $1, email = $2, password = $3 WHERE id = $4";
            $res_up = pg_query_params($conn, $update_sql, array($new_user, $new_email, $safe_hash, $user_id));

            if ($res_up) {
                $msg = "Dati aggiornati con successo!";
                $_SESSION['user'] = $new_user;
                $user_data['username'] = $new_user;
                $user_data['email'] = $new_email;
                $user_data['password'] = $new_pass;
            } else {
                $errori = "Errore durante l'aggiornamento.";
            }
        }
    }
}

// RECUPERO PRENOTAZIONI
// Estrae lo storico delle prenotazioni dell'utente ordinate dalla più recente
$query_pren = "SELECT * FROM prenotazioni WHERE id_utente = $1 ORDER BY data_prenotazione ASC";
$res_pren = pg_query_params($conn, $query_pren, array($user_id));
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
     crossorigin=""/>
    
</head>
<body>

    <header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php" style="color:#FFD94A; text-decoration:none; font-weight:bold; font-size:1.2em;">
           &larr; Torna alla Home
        </a>
        <div style="display:flex; gap:20px; align-items:center;">
             <span>Ciao, <strong><?php echo htmlspecialchars($currentUser); ?></strong></span>
             <a href="logout.php" style="color:white; text-decoration:underline;">Logout</a>
        </div>
      </div>
    </header>

    <div class="container">
        
        <?php if($msg): ?> <div class="success"><?php echo $msg; ?></div> <?php endif; ?>
        <?php if($errori): ?> <div class="error"><?php echo $errori; ?></div> <?php endif; ?>

        <div class="profile-grid">
            
            <div class="box1">
                <h2>I Miei Dati</h2>
                <form method="POST" action="">
                    <label>Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

                    <label>Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required placeholder="nome@dominio.it">

                    <label>Password (Modifica)</label>
                    <div class="password-container">
                        <input type="password" id="pass" name="pass" placeholder="Inserisci la nuova password" required>
                        <span class="toggle-password" onclick="togglePassword()">
                             <img src="assets/eye-slash.png" id="icon-slash">
                              <img src="assets/eye.png" id="icon-eye" hidden>
                        </span>

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

                    <input type="submit" id="btn-submit" name="update_profile" value="Salva Modifiche" class="btn-save">
                </form>
            </div>

            <div class="box">
                <h2>Le Mie Prenotazioni</h2>
                <?php if (pg_num_rows($res_pren) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Prenotazione</th>
                                <th>Data prenotazione</th>
                                <th>Prezzo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = pg_fetch_assoc($res_pren)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nome_pacchetto']); ?></td>
                                <td><?php echo date("d/m/Y", strtotime($row['data_prenotazione'])); ?></td>
                                <td>€ <?php echo $row['prezzo']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="margin-top:20px; color:#ccc;">Non hai ancora effettuato prenotazioni.</p>
                    <a href="camere.php"  class="btn-save" >Vai alle case</a>
                    <a href="pacchetti.php"  class="btn-save" >Vai ai pacchetti</a>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <div class="container">
        <button onclick="getLocation()" class="btn-save" style="margin-bottom: 20px;">
            📍 Mostra la mia posizione sulla mappa
        </button>
        
        <div id="map"></div>
        <p id="geo-error" class="error" style="display:none;"></p>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>

    <script src="js/profilo.js"></script>
</body>

<footer id="footer">
          <div class="info">
            <h1>Salerno Mare e Luci</h1>
            <h2>Piazza Sedile di Portanova, 20<br> 
                Vicoletto S.Lucia, 6 <br>
                84121 Salerno/Italia
            </h2>
          </div>
          <div class="contatti">
            <h1>Contattaci</h1>
            <h2>Cellulare: +393497534392<br> 
                Email: gruppo13@gmail.com
            </h2>
          </div>
     
          <div class="social">
            <h1>Social <br></h1>

            <h2>
            <a class="ig" href="https://www.instagram.com/salernomareeluci?igsh=a253aHZ6cXE1YW56">
              <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 31.518 31.51" >
              <path id="Icon_awesome-instagram" data-name="Icon awesome-instagram" d="M15.757,9.914a8.079,8.079,0,1,0,8.079,8.079A8.066,8.066,0,0,0,15.757,9.914Zm0,13.331a5.252,5.252,0,1,1,5.252-5.252,5.262,5.262,0,0,1-5.252,5.252ZM26.051,9.584A1.884,1.884,0,1,1,24.166,7.7,1.88,1.88,0,0,1,26.051,9.584ZM31.4,11.5a9.325,9.325,0,0,0-2.545-6.6,9.387,9.387,0,0,0-6.6-2.545c-2.6-.148-10.4-.148-13,0a9.373,9.373,0,0,0-6.6,2.538,9.356,9.356,0,0,0-2.545,6.6c-.148,2.6-.148,10.4,0,13a9.325,9.325,0,0,0,2.545,6.6,9.4,9.4,0,0,0,6.6,2.545c2.6.148,10.4.148,13,0a9.325,9.325,0,0,0,6.6-2.545,9.387,9.387,0,0,0,2.545-6.6c.148-2.6.148-10.392,0-12.994ZM28.041,27.281a5.318,5.318,0,0,1-3,3c-2.074.823-7,.633-9.288.633s-7.221.183-9.288-.633a5.318,5.318,0,0,1-3-3c-.823-2.074-.633-7-.633-9.288s-.183-7.221.633-9.288a5.318,5.318,0,0,1,3-3c2.074-.823,7-.633,9.288-.633s7.221-.183,9.288.633a5.318,5.318,0,0,1,3,3c.823,2.074.633,7,.633,9.288S28.863,25.214,28.041,27.281Z" transform="translate(0.005 -2.238)" fill="currentColor"></path>
              </svg>
            </a>
            </h2>

          <h2>
            <a class="fb" href="https://www.facebook.com/share/16ow7kgKgY/?mibextid=wwXIfr">
            <svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 31.702 31.51">
              <path id="Icon_awesome-facebook" data-name="Icon awesome-facebook" d="M32.264,16.413A15.851,15.851,0,1,0,13.937,32.073V21H9.91V16.413h4.027V12.921c0-3.972,2.365-6.167,5.987-6.167a24.394,24.394,0,0,1,3.549.309v3.9h-2a2.291,2.291,0,0,0-2.583,2.475v2.975h4.4L22.583,21H18.89V32.073A15.857,15.857,0,0,0,32.264,16.413Z" transform="translate(-0.563 -0.563)" fill="currentColor"></path>
            </svg>
           </a>
          </h2>

          <h2>
              <a class="tk" href="https://www.tiktok.com/@salernomareeluci?_r=1&_t=ZN-93hbMdaFklK">
            
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"  viewBox="0 0 32 32" version="1.1">
            <path d="M16.656 1.029c1.637-0.025 3.262-0.012 4.886-0.025 0.054 2.031 0.878 3.859 2.189 5.213l-0.002-0.002c1.411 1.271 3.247 2.095 5.271 2.235l0.028 0.002v5.036c-1.912-0.048-3.71-0.489-5.331-1.247l0.082 0.034c-0.784-0.377-1.447-0.764-2.077-1.196l0.052 0.034c-0.012 3.649 0.012 7.298-0.025 10.934-0.103 1.853-0.719 3.543-1.707 4.954l0.020-0.031c-1.652 2.366-4.328 3.919-7.371 4.011l-0.014 0c-0.123 0.006-0.268 0.009-0.414 0.009-1.73 0-3.347-0.482-4.725-1.319l0.040 0.023c-2.508-1.509-4.238-4.091-4.558-7.094l-0.004-0.041c-0.025-0.625-0.037-1.25-0.012-1.862 0.49-4.779 4.494-8.476 9.361-8.476 0.547 0 1.083 0.047 1.604 0.136l-0.056-0.008c0.025 1.849-0.050 3.699-0.050 5.548-0.423-0.153-0.911-0.242-1.42-0.242-1.868 0-3.457 1.194-4.045 2.861l-0.009 0.030c-0.133 0.427-0.21 0.918-0.21 1.426 0 0.206 0.013 0.41 0.037 0.61l-0.002-0.024c0.332 2.046 2.086 3.59 4.201 3.59 0.061 0 0.121-0.001 0.181-0.004l-0.009 0c1.463-0.044 2.733-0.831 3.451-1.994l0.010-0.018c0.267-0.372 0.45-0.822 0.511-1.311l0.001-0.014c0.125-2.237 0.075-4.461 0.087-6.698 0.012-5.036-0.012-10.060 0.025-15.083z"/>
            </svg>
            </a>
          </h2>
          </div>
          
    </footer>
</html>
