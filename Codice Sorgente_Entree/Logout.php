<!--Logout.php: Viene chiamata una volta che l'utente effettua il logout del sistema; rimanda dopo 2 secondi alla pagina di login-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.: Entree - Logout Utente :.</title>
<link rel="stylesheet" type="text/css" href="stile.php"  />
</head>
<body>
<?php
	session_start();
	
	// cancello tutti i dati di sessione
	$_SESSION = array();

	// Cancelliamo l'eventuale cookie di sessione
	if (isset($_COOKIE[session_name()]))
	{
   setcookie(session_name(), '', time()-42000, '/');
	}

	// distruggiamo la sessione
	session_destroy();
	unset($_SESSION["utente"]);
	echo "<h1>Logout effettuato. Verrai riportato alla pagina di login, attendi...</h1>";
	header("refresh:2;url=Login.php"); 
	
?>
</body>
