<?php
session_start();
// Collegamento al database nella cartella includes
require_once 'includes/db_config.php';
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/favicon.ico">
    <script src="js/validation.js"></script>
  </head>
  <body>

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
        <li><a href="#suite">Suite</a></li>
        <li><a href="pacchetti.php">Pacchetti</a></li>
        
        <?php if (isset($_SESSION['user'])): ?>
            <li class="menu-item-session"><span class="user-name" style="color: #FFD94A;">Ciao, <?php echo htmlspecialchars($_SESSION['nome']); ?></span></li>
            <li class="menu-item-session"><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login_reg.php">Login / Registrati</a></li>
        <?php endif; ?>

        <li><a href="#chi-siamo">Chi Siamo</a></li>
    </ul>
</nav>

        <div class="hamb-menu">
           <svg width="25" height="25" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke="white" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke="white" stroke-width="2"/><line x1="3" y1="18" x2="21" y2="18" stroke="white" stroke-width="2"/></svg>
        </div>
      </div>
    </header>

    <video class="video-bg" src="assets/videovascasauna.mp4" autoplay muted loop></video>

    <main>
      <section class="section watch">
        <h1 class="title">Benvenuti a Salerno Mare e Luci</h1> 
      </section>

      <?php
      $sql = "SELECT * FROM contenuti ORDER BY id ASC";
      $res = pg_query($conn, $sql);
      $count = 1;
      if($res) {
        while ($row = pg_fetch_assoc($res)):
          // Alternanza classi per i pannelli
          $panelClass = ($count == 1) ? "panel" : "panel panel" . $count;
          $anchorId = ($count == 1) ? "id='suite'" : (($count == 2) ? "id='deluxe'" : "");
      ?>
      <article <?php echo $anchorId; ?> class="<?php echo $panelClass; ?> watch">
        <h2 class="intro fade-in watch">Esplora</h2>
        <h1 class="text fade-in watch"><?php echo htmlspecialchars($row['titolo']); ?></h1>
        <figure>
          <img class="img-cent <?php echo ($count == 2) ? 'img-panel2' : (($count == 3) ? 'img-panel3' : ''); ?>" 
               src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" alt="Stanza">
        </figure>
        <h1 class="tit testo1 watch">Comfort</h1>
        <div class="tit testo2 watch">
          <?php 
          if (isset($_SESSION['user'])) {
              echo htmlspecialchars($row['descrizione']);
          } else {
              echo "Contenuto riservato. <a href='login_reg.php' style='color:#FFD94A;'>Accedi</a> per visualizzare i dettagli.";
          }
          ?>
        </div>
      </article>
      <?php $count++; endwhile; } ?>
    </main>
     
    <footer id="chi-siamo" class="cont-container">
      <div class="cont1">
        <h1>DOVE LA TRADIZIONE INCONTRA L'ELEGANZA</h1>
        <div class="client">Clienti soddisfatti</div>
        <div class="cont-client cont">0</div>
        <div class="client">dal</div>
        <div class="cont-year cont">2023</div>
      </div>
    </footer>

    <script src="js/script.js"></script>
  </body>
</html>