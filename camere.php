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
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/favicon.ico">
    <style>
        .category-section {
            padding: 60px 20px;
            border-bottom: 1px solid #333;
        }
        
        .category-header {
            text-align: center;
            margin-bottom: 40px;
            color: #f7f7f7;
        }

        .category-title {
            font-size: 3.5em;
            color: #FFD94A;
            font-family: 'Intro', sans-serif;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .category-desc {
            font-size: 1.2em;
            max-width: 800px;
            margin: 0 auto;
            color: #ccc;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .photo-card {
            background: #1d1d1f;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
            position: relative;
        }

        .photo-card:hover {
            transform: scale(1.02);
            z-index: 10;
            box-shadow: 0 5px 15px rgba(255, 217, 74, 0.2);
        }

        .photo-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            display: block;
        }

        .btn-whatsapp-big {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.2em;
            margin-top: 30px;
            transition: background 0.3s;
        }
        .btn-whatsapp-big:hover {
            background-color: #128C7E;
            transform: scale(1.05);
        }
    </style>
</head>
<body style="background-color: black;">

    <header class="header">
      <div class="header-content"> 
        <a class="icon-big" href="index.php">
          <svg viewBox="0 0 900 260" width="140" height="32" role="img">
             <g fill="none" fill-rule="evenodd">
              <rect x="30" y="40" width="170" height="170" stroke="#F5F5F5" stroke-width="4"/>
              <path d="M30 175 C70 155,110 195,150 175 C170 165,190 185,200 175 L200 210 L30 210 Z" fill="#2F86C1"/>
              <path d="M102 170 L138 170 L130 65 L110 65 Z" fill="#F5F5F5" stroke="#F5F5F5" stroke-width="3"/>
              <rect x="112" y="80" width="16" height="20" fill="#FFD94A"/>
              <circle cx="75" cy="75" r="9" fill="#FFD94A" opacity="0.85"/>
              <text x="240" y="145" fill="#F5F5F5" font-size="120" font-family="Georgia">Salerno</text>
              <text x="250" y="200" fill="#2F86C1" font-size="52" font-family="Arial">MARE &amp; LUCI</text>
            </g>
          </svg>
        </a>
        <nav>
            <ul class="header-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="camere.php" style="color:#FFD94A">Camere</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="profilo.php">Profilo</a></li>
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login_reg.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
      </div>
    </header>

    <div style="height: 100px;"></div>

    <?php 
    if ($result) {
        while ($row = pg_fetch_assoc($result)): 
            // 1. Array Immagini
            $immagini_array = explode(',', $row['galleria']);
            
            // 2. Link che porta al file di salvataggio (non più diretto a WA)
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
            <?php foreach ($immagini_array as $img_name): ?>
            <div class="photo-card">
                <img src="assets/<?php echo rawurlencode(trim($img_name)); ?>" alt="Foto Camera">
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php 
        endwhile; 
    } 
    ?>

</body>
</html>