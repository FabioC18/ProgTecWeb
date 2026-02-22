<?php
session_start(); 
require_once 'includes/db_config.php'; 

if (!isset($_SESSION['user'])) {
    header("Location: login_reg.php");
    exit;
}

$currentUser = $_SESSION['user']; 
$msg = ""; 
$errori = ""; 

$query_info = "SELECT * FROM utenti WHERE username = $1";
$res_info = pg_query_params($conn, $query_info, array($currentUser));
$user_data = pg_fetch_assoc($res_info);
$user_id = $user_data['id']; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_user  = str_replace(' ', '', $_POST['username']);
    $new_email = str_replace(' ', '', $_POST['email']);
    $new_pass = $_POST['pass'];

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.(com|it)$/", $new_email)) {
        $errori = "Errore: L'email deve terminare obbligatoriamente con .com o .it";
    }
    elseif (strlen($new_pass) < 8 || 
        !preg_match("/[A-Z]/", $new_pass) || 
        !preg_match("/[0-9]/", $new_pass) || 
        !preg_match("/[^a-zA-Z0-9]/", $new_pass)) {
        $errori = "Errore: Password debole (Min 8 car, 1 Maiusc, 1 Num, 1 Spec).";
    } 
    else {
        $check_u = pg_query_params($conn, "SELECT id FROM utenti WHERE username = $1 AND id != $2", array($new_user, $user_id));
        $check_e = pg_query_params($conn, "SELECT id FROM utenti WHERE email = $1 AND id != $2", array($new_email, $user_id));

        if (pg_num_rows($check_u) > 0) {
            $errori = "Username già occupato da un altro utente.";
        } elseif (pg_num_rows($check_e) > 0) {
            $errori = "Email già utilizzata da un altro utente.";
        } else {
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
             <li class="menu-item-session"><a href="logout.php" onclick="return confirm('Sei sicuro di voler uscire?');">Logout</a></li>
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
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required pattern= "[^\s]+">

                    <label>Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required placeholder="nome@dominio.it" pattern= "[^\s]+">

                    <label>Password (Modifica)</label>
                    <div class="password-container">
                        <input type="password" id="pass" name="pass" placeholder="Inserisci la nuova password" required pattern="[^\s]+">
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

    <?php include 'includes/footer.php'; ?>
</body>
</html>