<?php
$conn = pg_connect("host=localhost port=5432 dbname=gruppo13 user=www password=www");
if (!$conn) {
    die("Errore di connessione al database.");
}
?>