<!--Registrazione_Utente.php: Pagina di registrazione dell'utente nel sistema Entree-->
<?php
	//istruzione per disabilitare i messaggi di NOTICE
	error_reporting(0);
	//caricamento file esterni
	require_once("Funzioni.php");
	require_once("Config.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.: Entree - Registrazione Utente :.</title>
<link rel="stylesheet" type="text/css" href="stile.php"  />
</head>

<body>
<?php 
	if ($_SERVER['REQUEST_METHOD'] == "GET"){
		//la pagina è stata chiamata con il metodo GET		
?>
	<h1>Registrazione utente</h1>
    <p>Compila il form sottostante e premi su "Registrati!" per poter entrare su Entree!</p>
    <!--action = $_SERVER['PHP_SELF']: all'atto dell'invio dei dati, il form richiama se stesso e specisce i dati con il metodo POST-->
    <form method="post" <?php echo "action=\"", $_SERVER['PHP_SELF'], "\""; ?> >
    	Username: <input type="text" name="nuovo_utente" size="10" />
        Password: <input type="password" name="password_utente" size="10" />
        <br />
        <hr />
        <input type="submit" value="Registrati!" />
    </form>
    <hr />
    <p>Torna indietro alla pagina di <a href="Login.php">login</a>.</p>
<?php
} else //la pagina è stata chiamata con il POST
	//chiamata alla funzione registra_utente contenuto nel file "funzioni.php", a cui vengono passati i due parametri del nome utente e della password
	if(registra_utente($_POST["nuovo_utente"] , $_POST["password_utente"])){
	//utente o password sono sbagliati = non è presente l'utente all'interno del DB
?>
	<h2>Benvenuto</h2>
	<p>Registrazione completata. Ora puoi entrare anche tu su Entree! Attendi il caricamento della pagina di login...</p>
<?php
	header("refresh:2;url=Login.php"); 
	} else {
		
		echo "<h2>Errore</h2>";
		echo "<p>Il nome utente è già presente nel Database. Effettua nuovamente la <a href=\"Registrazione_Utente.php\">registrazione</a> indicando un altro nome utente.</p>";
	}
?>
</body>
</html>