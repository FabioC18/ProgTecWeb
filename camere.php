<?php
session_start();
require_once 'includes/db_config.php';

// Query per prendere le categorie dalla tabella 'camere'
$query = "SELECT * FROM camere ORDER BY id ASC"; 
$result = pg_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le Nostre Camere - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/camere.css">
    <link rel="icon" href="assets/favicon.ico">
</head>
<body style= "background-color: #3a0707ff ">

    <header class="header">
      <div class="header-content"> 
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

<nav>
    <ul class="header-menu">
        <li><a href="camere.php">Case vacanza</a></li>
        <li><a href="pacchetti.php">Pacchetti</a></li>
         <?php if (isset($_SESSION['user'])): ?>
            <li class="menu-item-session"><a class="user" href="profilo.php" ><script
  src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js"
  type="module"></script>

<dotlottie-wc
  src="https://lottie.host/73049aba-3e4d-41d1-a8bc-0cb9982ffb58/EV4SRIloZW.lottie"
  autoplay
  loop
></dotlottie-wc></a></li>
            <li class="menu-item-session"><a href="logout.php">Logout</a></li>
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

    <div style="height: 100px;"></div>

    <?php 
    if ($result) {
        while ($row = pg_fetch_assoc($result)): 
            $immagini_array = explode(',', $row['galleria']);
            
            $link_prenotazione = "salva_prenotazione.php?nome=" . urlencode($row['titolo']) . "&prezzo=" . $row['prezzo'];
    ?>
    
    <section class="category-section">
        <div class="category-header">
            <h1 class="category-title"><?php echo htmlspecialchars($row['titolo']); ?></h1>
            <p class="category-desc"><?php echo htmlspecialchars($row['descrizione']); ?></p>
            <p style="color:#FFD94A; font-weight:bold; font-size:1.5em; margin-top:10px;">
                A partire da € <?php echo $row['prezzo']; ?> / notte per coppia
            </p>
            
            <a href="<?php echo $link_prenotazione; ?>" class="btn-whatsapp-big">
                Prenota <?php echo htmlspecialchars($row['titolo']); ?>
            </a>
        </div>

        <div class="grid-container">
            <?php $count =1;
            foreach ($immagini_array as $img_name): 
                
                
                ?>
 
            <div class="<?php echo "photo-card".$count;?>">
                <img class="images" src="assets/<?php echo rawurlencode(trim($img_name)); ?>" >
            </div>

            <?php $count ++; endforeach; ?>
        </div>
    </section>

    <?php 
        endwhile; 
    } 
    ?>

    <script src="js/camere.js"></script>

</body>
</html>