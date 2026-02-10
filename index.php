<?php
session_start();
require_once 'includes/db_config.php';
?>
<!DOCTYPE html>
<html lang="it">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="assets/favicon.ico">
  </head>
  <body>

    <header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php">
          <svg viewBox="0 0 900 260" width="155" height="40" role="img" aria-label="Salerno Mare & Luci">
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
        <li><a href="#footer">Chi Siamo</a></li>
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
            <li><a href="login_reg.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

        <div class="hamb-menu">
           <svg width="25" height="25" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6" stroke="red" stroke-width="2"/><line x1="3" y1="12" x2="21" y2="12" stroke="white" stroke-width="2"/><line x1="3" y1="18" x2="21" y2="18" stroke="blue" stroke-width="2"/></svg>
        </div>
      </div>
    </header>

    <video class="video-bg" src="assets/videovascasauna.mp4" autoplay muted loop></video>

    <main>
      
    <div class="section watch">
      <h1 class="title">SALERNO MARE & LUCI</h1> 
    </div>

    <div class="section watch">
      <h2 class="title">Comfort e relax a pochi passi dal mare</h2> 
    </div>

    <div class="section watch">
      <h2 class="title">Case vacanza accoglienti nel cuore di Salerno</h2> 
    </div>

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
      <div <?php echo $anchorId; ?> class="<?php echo $panelClass; ?> watch">
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
              echo "Contenuto riservato. <a href='login_reg.php' >Accedi</a> per visualizzare i dettagli.";
          }
          ?>
        </div>
        </div>
      <?php $count++; endwhile; } ?>
    </main>
     
    <div class="cont-container">
      <div class="cont1">
        <h1>DOVE LA TRADIZIONE INCONTRA L'ELEGANZA</h1>
        <div class="client">Clienti soddisfatti</div>
        <div class="cont-client cont">0</div>
        <div class="client">dal</div>
        <div class="cont-year cont">2023</div>
      </div>
        </div>

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

    

    <script src="js/index.js"></script>
  </body>
</html>