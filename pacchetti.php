<?php
// Inclusione corretta del file di configurazione
require_once 'includes/db_config.php';
session_start();

// Controllo stato autenticazione
$is_logged = isset($_SESSION['user']);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Pacchetti - Salerno Mare e Luci</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="css/pacchetti.css">
    <link rel="icon" href="assets/favicon.ico">
</head>
<body>

    <header class="header">
      <div class="header-content"> 
        <div class="icon-big">
            <a class="icon-big" href="index.php">
          <svg viewBox="0 0 900 260" width="140" height="32" role="img" aria-label="Salerno Mare & Luci">
            <g fill="none" fill-rule="evenodd">
              <rect x="30" y="40" width="170" height="170" stroke="#2E2E2E" stroke-width="4"/>
              <path d="M30 175 C70 155,110 195,150 175 C170 165,190 185,200 175 L200 210 L30 210 Z" fill="#2F86C1"/>
              <path d="M102 170 L138 170 L130 65 L110 65 Z" fill="#F5F5F5" stroke="#2E2E2E" stroke-width="3"/>
              <rect x="112" y="80" width="16" height="20" fill="#FFD94A"/>
              <circle cx="75" cy="75" r="9" fill="#FFD94A" opacity="0.85"/>
              <circle cx="95" cy="95" r="7" fill="#FFD94A" opacity="0.7"/>
              <circle cx="65" cy="105" r="6" fill="#FFD94A" opacity="0.6"/>
              <text x="240" y="145" fill="#8B1E1E" font-size="120" font-family="Georgia, serif" font-style="italic">Salerno</text>
              <text x="250" y="200" fill="#1E5F9C" font-size="52" font-family="Arial, sans-serif" letter-spacing="6">MARE &amp; LUCI</text>
            </g>
          </svg>
        </a>
        </div>
        
        <nav>
            <ul class="header-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="camere.php">Camere</a></li>
                <li><a href="pacchetti.php" style="color:#FFD94A">Pacchetti</a></li>
        
                <?php if ($is_logged): ?>
                    <li class="menu-item-session"><span class="user-name" style="color: #FFD94A;">Ciao, <?php echo htmlspecialchars($_SESSION['user']); ?></span></li>
                    <li class="menu-item-session"><a href="logout.php">Logout</a></li>
                    <li><a href="profilo.php">Profilo</a></li>
                <?php else: ?>
                    <li><a href="login_reg.php">Login / Registrati</a></li>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="hamb-menu">
           <svg width="25" height="25" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke="red" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke="white" stroke-width="2"/><line x1="3" y1="18" x2="21" y2="18" stroke="blue" stroke-width="2"/></svg>
        </div>
      </div>
    </header>
    
    <div class="container">
        <h2 class="object" data-value="3">Pacchetti <br> <span>Love</span> <br> </h2>
        <img src="assets/unnamed-no-bg.png" class="object freccia" data-value="3">
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

   <main class="pannelli">
     <?php
     // Query per la tabella pacchetti
     $query = "SELECT * FROM pacchetti ORDER BY id ASC";
     $result = pg_query($conn, $query);

     if ($result):
         $count = 1;
         $total_rows = pg_num_rows($result);
         
         while ($row = pg_fetch_assoc($result)):
             $id_panel =  "panel" . $count;
             $class_panel = ($count == 1) ? "panel" : "panel" . $count;
     ?>
        <article id="<?php echo $id_panel; ?>" class="<?php echo $class_panel; ?>">        
            <div class="pack-base">
                <h3><?php echo htmlspecialchars($row['nome']); ?> <br> <span>include:</span></h3>
                
                <?php if ($is_logged): ?>
                    <p><?php echo htmlspecialchars($row['descrizione']); ?></p>
                    <p style="font-weight: bold; font-size: 1.5em; color: #FFD94A;">
                        € <?php echo htmlspecialchars($row['prezzo']); ?>
                    </p>
                    
                    <?php 
                        $link_prenotazione = "salva_prenotazione.php?nome=" . urlencode($row['nome']) . "&prezzo=" . $row['prezzo'];
                    ?>
                    <a href="<?php echo $link_prenotazione; ?>" style="display:inline-block; background:#25D366; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; margin-top:10px; font-weight:bold;">
                        Prenota Ora
                    </a>

                <?php else: ?>
                    <p style="font-style: italic; color: #f7f7f7;">
                        Registrati per visualizzare i dettagli completi e i prezzi riservati.
                    </p>
                <?php endif; ?>
            </div>

            <figure>
                <img src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" class="img-pack" alt="Pacchetto">
            </figure>

            <h4>*I PACCHETTI POSSONO ESSERE PERSONALIZZATI SU RICHIESTA <br>
                <a href="#" onclick="scorriA('<?php echo ($count > 1) ? "panel".($count-1) : "panel".$total_rows; ?>'); return false;">
                    <img class="frslider" src="assets/frsx-no-bg.png" alt="Precedente">
                </a> 
                <a href="#" onclick="scorriA('<?php echo ($count < $total_rows) ? "panel".($count+1) : "panel1"; ?>'); return false;">
                    <img class="frslider" src="assets/frdx-no-bg.png" alt="Successivo">
                </a>
            </h4>
        </article>
     <?php 
         $count++;
         endwhile;
     endif; 
     ?>
   </main>

    <script src="js/pacchetti.js"></script>
</body>
</html>