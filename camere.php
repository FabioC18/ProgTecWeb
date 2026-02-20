<?php
session_start(); 
require_once 'includes/db_config.php'; 

$query = "SELECT * FROM camere ORDER BY id ASC LIMIT 2"; 
$result = pg_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le Nostre Camere - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/camere.css">
    <link rel="icon" href="assets/favicon.ico"> 
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <div style="height: 100px;"></div>

    <?php 
    if ($result) {
        while ($row = pg_fetch_assoc($result)): 
            $immagini_array = explode(',', $row['galleria']); 
            $link_base = "salva_prenotazione.php?nome=" . urlencode($row['titolo']) . "&prezzo=" . $row['prezzo'] . "&id=" . $row['id']; 
    ?>

    <section class="category-section" id="camera_<?php echo $row['id']; ?>">
        
        <?php if (isset($_GET['error']) && isset($_GET['id']) && $_GET['id'] == $row['id']): ?>
        <div class= "error" id="server_error_<?php echo $row['id']; ?>" >
            <strong>Attenzione:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>

        <div class="category-header">
            <h1 class="category-title"><?php echo htmlspecialchars($row['titolo']); ?></h1>
            <p class="category-desc"><?php echo htmlspecialchars($row['descrizione']); ?></p>
            
            <?php 
            if (isset($_SESSION['user'])): 
            ?>
                <p style="color:#FFD94A; font-weight:bold; font-size:1.5em; margin-top:10px;">
                    A partire da € <?php echo $row['prezzo']; ?> / notte per coppia
                </p>
                
                <div style="margin: 25px 0;">
                    <label for="date_<?php echo $row['id']; ?>" style="color:#ccc; display:block; margin-bottom:8px; font-size: 1.1em;">
                        Seleziona la data del check-in:
                    </label>
                    <input type="date" 
                           id="date_<?php echo $row['id']; ?>" 
                           style="padding: 10px; border-radius: 5px; border: none; font-size: 1em;"
                           min="<?php echo date('Y-m-d'); ?>" 
                           onchange="updateBookingLink(<?php echo $row['id']; ?>)">
                </div>
                
                <a href="#" 
                   id="btn_prenota_<?php echo $row['id']; ?>"
                   data-baseurl="<?php echo $link_base; ?>"
                   class="btn-whatsapp-big"
                   onclick="return checkDateSelected(<?php echo $row['id']; ?>)">
                    Prenota <?php echo htmlspecialchars($row['titolo']); ?>
                </a>
                
                <p id="error_msg_<?php echo $row['id']; ?>" style="color: #ff4444; display: none; margin-top: 10px; font-weight: bold;">
                    Per favore, seleziona una data prima di prenotare.
                </p>

            <?php 
            else: 
            ?>
                <p style="color:#FFD94A; font-weight:bold; font-size:1.5em; margin-top:10px;">
                    Prezzo riservato agli iscritti
                </p>
                
                <div style="margin: 25px 0;">
                    <p style="color:#ccc; font-size: 1.1em; font-style: italic;">
                        Registrati o accedi per visualizzare i dettagli completi, i prezzi e procedere con la prenotazione.
                    </p>
                </div>
                
                <a href="login_reg.php" class="btn-whatsapp-big">
                    Accedi per Prenotare
                </a>
            <?php endif; ?>
        </div>

        <div class="grid-container">
            <?php foreach ($immagini_array as $img_name): ?>
            <div class="photo-card">
                <img class="images" src="assets/<?php echo rawurlencode(trim($img_name)); ?>" alt="Foto Camera">
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php 
        endwhile; 
    } 
    ?>

    <?php include 'includes/footer.php'; ?>

    <script src="js/camere.js"></script>
    
</body>
</html>