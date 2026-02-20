<?php
session_start();
require_once 'includes/db_config.php';

// Estrazione dati dal database spostata in alto per separare la logica dalla visualizzazione
$sql = "SELECT * FROM contenuti ORDER BY id ASC";
$res = pg_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="assets/favicon.ico">
</head>
<body>

    <?php include 'includes/header.php'; ?>

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
        if ($res) {
            $count = 1;
            while ($row = pg_fetch_assoc($res)):
                
                // Raggruppamento delle variabili CSS e ID per mantenere l'HTML pulito
                $panelClass = ($count == 1) ? "panel" : "panel panel" . $count;
                $anchorId = ($count == 1) ? "id='suite'" : (($count == 2) ? "id='deluxe'" : "");
                
                // Unificazione della logica per la classe dell'immagine
                $imgClass = "img-cent";
                if ($count == 2) $imgClass .= " img-panel2";
                if ($count == 3) $imgClass .= " img-panel3";
        ?>
        
        <div <?php echo $anchorId; ?> class="<?php echo $panelClass; ?> watch">
            
            <h2 class="intro fade-in watch">Esplora</h2>
            <h1 class="text fade-in watch"><?php echo htmlspecialchars($row['titolo']); ?></h1>
            
            <figure>
                <img class="<?php echo $imgClass; ?>" src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" alt="Stanza">
            </figure>
            
            <h1 class="tit testo1 watch">Comfort</h1>
            
            <div class="tit testo2 watch">
                <?php if (isset($_SESSION['user'])): ?>
                    <?php echo htmlspecialchars($row['descrizione']); ?>
                <?php else: ?>
                    Contenuto riservato. <a href="login_reg.php">Accedi</a> per visualizzare i dettagli.
                <?php endif; ?>
            </div>
            
        </div>
        
        <?php 
                $count++; 
            endwhile; 
        } 
        ?>
        
    </main>
      
    <div class="cont-container watch" id="chi_siamo">
        <div class="cont1">
            <h1>DOVE LA TRADIZIONE INCONTRA L'ELEGANZA</h1>
            <div class="client">Clienti soddisfatti</div>
            <div class="cont-client watch" data-target="6500">0</div>
            <div class="client">dal</div>
            <div class="cont-year watch" data-target="2008">0</div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/index.js"></script>

</body>
</html>