<?php
//opzioni di configurazione del sistema
//array associativo contenente le informazioni di configurazione
$config = array();

//inserimento all'interno dell'array associativo del db_utente, del db_password, del db_server e del db_database
$config["db_utente"] = "root";
$config["db_password"] = "toor";
$config["db_server"] = "localhost";
$config["db_database"] = "entree";

//Nome del sistema
$config["titolo"] = "Entree System";

//colori
$config["aspetto"] = array();
$config["aspetto"]["col_sfondo"] = "white";
$config["aspetto"]["col_testo"] = "black";
$config["aspetto"]["col_intestazione"] = "#99C1FF";
$config["aspetto"]["col_menu"] = "#99C1FF";
//font
$config["aspetto"]["font"] = "Arial";
//immagine
$config["aspetto"]["immagine"] = "url(\"Immagini/entree.jpg\")";
?>