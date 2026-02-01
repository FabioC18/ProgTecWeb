<?php
session_start();
// Percorso corretto dopo lo spostamento in sottocartelle
require_once 'includes/db_config.php';
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.ico">
    <script src="js/validation.js"></script>
  </head>

  <body>
    <header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 900 260" width="140" height="32">...</svg>
        </a>

        <ul class="header-menu">
          <li><a href="#suite">Suite</a></li>
          <li><a href="#deluxe">Deluxe</a></li>
          <li><a href="pacchetti.php">Pacchetti</a></li>
          
          <?php if (isset($_SESSION['user'])): ?>
            <li><span style="color: #FFD94A;">Ciao, <?php echo htmlspecialchars($_SESSION['nome']); ?></span></li>
            <li><a href="logout.php">Logout</a></li>
          <?php else: ?>
            <li><a href="login_reg.php">Login / Registrati</a></li>
          <?php endif; ?>
          <li><a href="#chi-siamo">Chi Siamo</a></li>
        </ul>

        <div class="hamb-menu">
           </div>
      </div>
    </header>

    <video class="video-bg" src="assets/videovascasauna.mp4" autoplay muted loop></video>

    <div class="section watch">
      <h1 class="title">Benvenuti a Salerno Mare e Luci</h1> 
    </div>

    <?php
    $sql = "SELECT * FROM contenuti ORDER BY id ASC";
    $res = pg_query($conn, $sql);
    
    if($res):
      $count = 1;
      while ($row = pg_fetch_assoc($res)):
          $panelClass = "panel";
          if ($count == 2) $panelClass = "panel panel2";
          if ($count == 3) $panelClass = "panel panel3";
          
          $anchorId = "";
          if ($count == 1) $anchorId = "id='suite'";
          if ($count == 2) $anchorId = "id='deluxe'";
    ?>
    <div <?php echo $anchorId; ?> class="<?php echo $panelClass; ?> watch">
      <h2 class="intro fade-in watch">Esplora</h2>
      <h1 class="text fade-in watch"><?php echo htmlspecialchars($row['titolo']); ?></h1>

      <img class="img-cent <?php echo ($count == 2) ? 'img-panel2' : (($count == 3) ? 'img-panel3' : ''); ?>" 
           src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" alt="Suite">

      <h1 class="tit testo1 watch">Comfort</h1>
      <h2 class="tit testo2 watch">
        <?php 
        if (isset($_SESSION['user'])) {
            echo htmlspecialchars($row['descrizione']);
        } else {
            echo "Contenuto riservato. <a href='login_reg.php' style='color:#FFD94A;'>Accedi</a> per i dettagli.";
        }
        ?>
      </h2>
    </div>
    <?php 
        $count++;
      endwhile; 
    endif;
    ?>
     
    <div id="chi-siamo" class="cont-container">
      <div class="cont1">
        <h1>DOVE LA TRADIZIONE INCONTRA L'ELEGANZA</h1>
        <div class="client">Clienti soddisfatti</div>
        <div class="cont-client cont">0</div>
        <div class="client">dal</div>
        <div class="cont-year cont">0</div>
      </div>
    </div>

    <script src="js/script.js"></script>
  </body>
</html>
