<?php
session_start();
require_once 'includes/db_config.php';

// 1. PROTEZIONE PAGINA
if (!isset($_SESSION['user'])) {
    header("Location: login_reg.php");
    exit;
}

$currentUser = $_SESSION['user'];
$msg = "";
$errori = "";

// 2. RECUPERO DATI UTENTE
$query_info = "SELECT * FROM utenti WHERE username = $1";
$res_info = pg_query_params($conn, $query_info, array($currentUser));
$user_data = pg_fetch_assoc($res_info);
$user_id = $user_data['id']; 

// 3. GESTIONE MODIFICA CREDENZIALI
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $new_user = $_POST['username'];
    $new_email = $_POST['email'];
    $new_pass = $_POST['pass'];

    // VALIDAZIONE RIGIDA
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
        // Controllo duplicati
        $check_u = pg_query_params($conn, "SELECT id FROM utenti WHERE username = $1 AND id != $2", array($new_user, $user_id));
        $check_e = pg_query_params($conn, "SELECT id FROM utenti WHERE email = $1 AND id != $2", array($new_email, $user_id));

        if (pg_num_rows($check_u) > 0) {
            $errori = "Username già occupato da un altro utente.";
        } elseif (pg_num_rows($check_e) > 0) {
            $errori = "Email già utilizzata da un altro utente.";
        } else {
            // Aggiornamento
            $update_sql = "UPDATE utenti SET username = $1, email = $2, password = $3 WHERE id = $4";
            $res_up = pg_query_params($conn, $update_sql, array($new_user, $new_email, $new_pass, $user_id));

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

// 4. RECUPERO PRENOTAZIONI
$query_pren = "SELECT * FROM prenotazioni WHERE id_utente = $1 ORDER BY data_prenotazione DESC";
$res_pren = pg_query_params($conn, $query_pren, array($user_id));
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Profilo Personale - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background-color: #1d1d1f; color: white; font-family: sans-serif; padding-top: 100px; }
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        @media (max-width: 768px) { .profile-grid { grid-template-columns: 1fr; } }

        .box { background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; border: 1px solid #333; }
        h2 { color: #FFD94A; border-bottom: 1px solid #444; padding-bottom: 10px; margin-top: 0; }

        label { display: block; margin-top: 15px; color: #ccc; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 10px; margin-top: 5px;
            background: #222; border: 1px solid #444; color: white; border-radius: 5px;
        }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #444; }
        th { color: #FFD94A; }
        tr:hover { background: rgba(255,255,255,0.05); }

        .success { color: #4BB543; background: rgba(75, 181, 67, 0.2); padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .error { color: #ff4d4d; background: rgba(255, 77, 77, 0.2); padding: 10px; border-radius: 5px; margin-bottom: 15px; }

        .btn-save {
            background-color: #FFD94A; color: black; border: none; padding: 12px 20px;
            font-weight: bold; cursor: pointer; margin-top: 20px; width: 100%; border-radius: 5px;
            transition: opacity 0.3s;
            text-decoration: none; 
        }
        .btn-save:disabled { background-color: #555; color: #888; cursor: not-allowed; }
        
        .password-container { position: relative; }
        
        .toggle-password {
            position: absolute; right: 10px; top: 38px; cursor: pointer; color: #ccc; z-index: 10;
        }

        /* --- STILE TOOLTIP (SOTTO) --- */
        .tooltip-requirements {
            display: none; /* Nascosto di default */
            position: absolute;
            top: 100%; /* Posizionato SOTTO l'input */
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            font-size: 0.85em;
            margin-top: 5px; /* Spazio tra input e tooltip */
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
            border: 1px solid #FFD94A;
            z-index: 100;
        }
        .tooltip-requirements ul {
            margin: 0;
            padding-left: 20px;
            list-style-type: circle;
        }
        .tooltip-requirements li {
            margin-bottom: 3px;
        }
    </style>
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
            
            <div class="box">
                <h2>I Miei Dati</h2>
                <form method="POST" action="">
                    <label>Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>

                    <label>Email</label>
                    <input type="text" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required placeholder="nome@dominio.it">

                    <label>Password (Modifica)</label>
                    <div class="password-container">
                        <input type="password" id="pass" name="pass" value="<?php echo htmlspecialchars($user_data['password']); ?>" required>
                        <span class="toggle-password" onclick="togglePassword()">
                             👁️
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

    <script>
        const userIn = document.getElementById('username');
        const emailIn = document.getElementById('email');
        const passIn = document.getElementById('pass');
        const btn = document.getElementById('btn-submit');
        const tooltip = document.getElementById('password-tooltip');

        // GESTIONE TOOLTIP PASSWORD (SOTTO)
        function showTooltip() {
            tooltip.style.display = 'block';
        }
        function hideTooltip() {
            tooltip.style.display = 'none';
        }

        passIn.addEventListener('mouseenter', showTooltip);
        passIn.addEventListener('mouseleave', hideTooltip);
        passIn.addEventListener('focus', showTooltip);
        passIn.addEventListener('blur', hideTooltip);


        function togglePassword() {
            if (passIn.type === "password") {
                passIn.type = "text";
            } else {
                passIn.type = "password";
            }
        }

        function checkInputs() {
            const passValue = passIn.value;
            const emailValue = emailIn.value;

            // Validazione Regex JS
            const hasUpperCase = /[A-Z]/.test(passValue); 
            const hasNumber = /[0-9]/.test(passValue);    
            const hasSpecial = /[^a-zA-Z0-9]/.test(passValue); 
            const hasLength = passValue.length >= 8;      
            const emailRegex = /^[^\s@]+@[^\s@]+\.(com|it)$/; 

            if (userIn.value.trim() !== "" && 
                emailRegex.test(emailValue) && 
                hasUpperCase && hasNumber && hasSpecial && hasLength) {
                
                btn.disabled = false;
                btn.style.opacity = "1";
            } else {
                btn.disabled = true;
                btn.style.opacity = "0.5";
            }
        }

        userIn.addEventListener('input', checkInputs);
        emailIn.addEventListener('input', checkInputs);
        passIn.addEventListener('input', checkInputs);
        
        checkInputs();
    </script>
</body>
</html>