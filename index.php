<?php
session_start();
require_once 'includes/db_config.php';

// Estrazione dati dal database dalla tabella contenuti che include le informazioni per la pagina home oridnate per id
$sql = "SELECT * FROM contenuti ORDER BY id ASC";
$res = pg_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Sito responsive -->
    <title>Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="assets/favicon.ico">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <video class="video-bg" src="assets/videovascasauna.mp4" autoplay muted loop></video>

    <main>
        <!-- titoli e sottotitoli-->
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
            $count = 1;//contatore per distingue i tre pannelli
            while ($row = pg_fetch_assoc($res)):
    
                $panelClass = ($count == 1) ? "panel" : "panel panel" . $count; //operatore ternario che determina il nome del pannello per il CSS 
                $anchorId = ($count == 1) ? "id='suite'" : (($count == 2) ? "id='deluxe'" : ""); //Assegna l'ID univoco: 1 per suite, 2 per deluxe, 3 per pacchetti 
                
                //tutti i pannelli hanno la classe base img-cent per le funzionalità CSS ma se l'ID è quello del secondo o del terzo pannello allora 
                //oltre alla classe base si aggiungono le funzionalità del css scritt per il secondo e il terzo 
                $imgClass = "img-cent";
                if ($count == 2) $imgClass .= " img-panel2";
                if ($count == 3) $imgClass .= " img-panel3";
        ?>
        
        <div <?php echo $anchorId; ?> class="<?php echo $panelClass; ?> watch"> <!-- genera un contenitore dinamico con le informazioni assegnate tramite l'ID per assegnare ad ogni pannello le istruzione per il CSS -->
            
            <h2 class="intro fade-in watch">Esplora</h2>
            <h1 class="text fade-in watch"><?php echo htmlspecialchars($row['titolo']); ?></h1>
            
            <figure>
                <img class="<?php echo $imgClass; ?>" src="assets/<?php echo htmlspecialchars($row['immagine']); ?>" alt="Stanza"> <!-- stampa il titolo del databese usando una funzione che permette di evitare attacchi XSS (furto di dati utente come cookie)-->
            </figure>
            
            <h1 class="tit testo1 watch">Comfort</h1>
            
            <div class="tit testo2 watch">
                <?php if (isset($_SESSION['user'])): ?><!-- se l'utente è loggato-->
                    <?php echo htmlspecialchars($row['descrizione']); ?> <!-- stampa la descrfizione del database usando una funzione che permette di evitare attacchi XSS (furto di dati utente come cookie)-->
                <?php else: ?><!-- se l'utente non è loggato-->
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
    <!-- sezione informtiva-->
    <div class="cont-container watch" id="chi_siamo"> <!-- creazione del container per la sezione-->
        <div class="cont1">
            <h1>DOVE LA TRADIZIONE INCONTRA L'ELEGANZA</h1>
            <div class="client">Clienti soddisfatti</div>
            <div class="cont-client watch" >6500</div>
            <div class="client">dal</div>
            <div class="cont-year watch">2008</div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/index.js"></script>

</body>
</html>