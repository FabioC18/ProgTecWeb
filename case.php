<?php
session_start();
require_once 'includes/db_config.php';

// NUMERO DI TELEFONO PER WHATSAPP (Inserisci il tuo numero qui, col prefisso 39)
$phone_number = "393497534392"; 

// Recupero le ultime 8 immagini inserite (o quelle specifiche per le stanze)
// Modifica la query se vuoi filtrare per una categoria specifica
$query = "SELECT * FROM contenuti ORDER BY id DESC LIMIT 8"; 
$result = pg_query($conn, $query);

$suite_items = [];
$deluxe_items = [];

if ($result) {
    $count = 0;
    while ($row = pg_fetch_assoc($result)) {
        // Le prime 4 vanno in Suite (indice 0,1,2,3)
        // Le successive 4 vanno in Deluxe (indice 4,5,6,7)
        // Nota: L'ordine dipende da come le hai inserite nel DB
        if ($count < 4) {
            $suite_items[] = $row;
        } else {
            $deluxe_items[] = $row;
        }
        $count++;
    }
    // Inverto gli array se l'ORDER BY id DESC li ha presi al contrario rispetto all'inserimento
    $suite_items = array_reverse($suite_items);
    $deluxe_items = array_reverse($deluxe_items);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Le Nostre Camere - Salerno Mare e Luci</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="assets/favicon.ico">
    <style>
        /* CSS SPECIFICO PER QUESTA PAGINA */
        .room-section {
            padding: 50px 20px;
            text-align: center;
        }
        
        .room-title {
            font-size: 3em;
            color: #FFD94A;
            margin-bottom: 40px;
            font-family: 'Intro', sans-serif;
            text-transform: uppercase;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: #1d1d1f;
            border: 1px solid #333;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.03);
            border-color: #FFD94A;
        }

        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .card-body {
            padding: 20px;
        }

        .card h3 {
            color: white;
            margin: 10px 0;
        }

        .card p {
            color: #ccc;
            font-size: 0.9em;
            height: 40px; /* Altezza fissa per allineare i bottoni */
            overflow: hidden;
        }

        .btn-whatsapp {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
            transition: background 0.3s;
        }

        .btn-whatsapp:hover {
            background-color: #128C7E;
        }
        
        /* Separatore tra le sezioni */
        .separator {
            height: 2px;
            background: linear-gradient(90deg, transparent, #FFD94A, transparent);
            width: 80%;
            margin: 50px auto;
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
                    <li><a href="logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="login_reg.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
      </div>
    </header>

    <div style="height: 100px;"></div> <section class="room-section" id="suite">
        <h1 class="room-title">Area Suite</h1>
        <div class="grid-container">
            <?php foreach ($suite_items as $item): 
                // Preparo il link WhatsApp
                $msg = "Salve, vorrei prenotare la stanza: " . $item['titolo'];
                $wa_link = "https://wa.me/" . $phone_number . "?text=" . urlencode($msg);
            ?>
            <div class="card">
                <img src="assets/<?php echo rawurlencode($item['immagine']); ?>" alt="<?php echo htmlspecialchars($item['titolo']); ?>">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($item['titolo']); ?></h3>
                    <p><?php echo htmlspecialchars($item['descrizione']); ?></p>
                    <a href="<?php echo $wa_link; ?>" class="btn-whatsapp" target="_blank">
                        Prenota su WhatsApp
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <div class="separator"></div>

    <section class="room-section" id="deluxe">
        <h1 class="room-title">Area Deluxe</h1>
        <div class="grid-container">
            <?php foreach ($deluxe_items as $item): 
                $msg = "Salve, vorrei prenotare la stanza Deluxe: " . $item['titolo'];
                $wa_link = "https://wa.me/" . $phone_number . "?text=" . urlencode($msg);
            ?>
            <div class="card">
                <img src="assets/<?php echo rawurlencode($item['immagine']); ?>" alt="<?php echo htmlspecialchars($item['titolo']); ?>">
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($item['titolo']); ?></h3>
                    <p><?php echo htmlspecialchars($item['descrizione']); ?></p>
                    <a href="<?php echo $wa_link; ?>" class="btn-whatsapp" target="_blank">
                        Prenota su WhatsApp
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

</body>
</html>