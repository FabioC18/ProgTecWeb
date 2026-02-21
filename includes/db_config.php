<?php 
$conn = pg_connect("host=localhost port=5432 dbname=gruppo13 user=www password=www"); 
/*  pg_connect(): È la funzione nativa di PHP che tenta fisicamente di collegarsi a un database PostgreSQL.
    "host=localhost": Dice a PHP di cercare il database sul tuo stesso computer (in locale), e non su un server esterno.
    "port=5432": È la "porta" standard attraverso cui PostgreSQL ascolta le comunicazioni.
    "dbname=gruppo13": Specifica esattamente quale "raccoglitore" di dati aprire tra tutti quelli presenti nel database.
    "user=www" e "password=www": Fornisce le credenziali di accesso per poter leggere e scrivere i dati.
*/

if (!$conn) { // Se la connessione ($conn) è fallita 

    die("Errore di connessione al database."); 
    //Il server si ferma, non carica il resto del sito e mostra a schermo intero solo la frase tra virgolette. 
} 
?> 