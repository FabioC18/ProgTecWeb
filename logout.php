<?php

/* LOGICA DI LOGOUT */

session_start(); 
session_unset(); 
session_destroy();

/* REINDIRIZZAMENTO */

header("Location: index.php"); // Invia l'istruzione al browser di tornare alla homepage
exit;                          // Blocca l'esecuzione dello script per garantire il reindirizzamento
?>