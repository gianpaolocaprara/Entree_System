<!--Login.php: Pagina di login per poter entrare nel sistema-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
	
	//istruzione per disabilitare i messaggi di NOTICE
	error_reporting(0);
	//caricamento file esterni
	require_once("Funzioni.php");
	require_once("Config.php");

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>.: Entree - Login Utente :.</title>
<link rel="stylesheet" type="text/css" href="stile.php"  />
</head>
<body>
<?php	
	if ($_SERVER['REQUEST_METHOD'] == "GET"){
		//la pagina è stata chiamata con il metodo GET		
?>
	<h1>Login utente</h1>
    <p>Inserisci username e password per poter accedere al sistema Entree</p>
    <!--action = $_SERVER['PHP_SELF']: all'atto dell'invio dei dati, il form richiama se stesso e specisce i dati con il metodo POST-->
    <form method="post" <?php echo "action=\"", $_SERVER['PHP_SELF'], "\""; ?> >
    	Username: <input type="text" name="nome_utente" size="20" />
        Password: <input type="password" name="password_utente" size="15" />
        <br />
        <hr />
        <input type="submit" value="Login!" />
    </form>
    <hr />
    <p>Sei un nuovo utente? Clicca <a href="Registrazione_Utente.php">qui</a> per registrarti al sistema!</p>
<?php
} else //la pagina è stata chiamata con il POST
	//chiamata alla funzione registra_utente contenuto nel file "funzioni.php", a cui vengono passati i due parametri del nome utente e della password
	if(login_utente($_POST["nome_utente"] , $_POST["password_utente"])){
	//utente o password sono sbagliati = non è presente l'utente all'interno del DB
?>
	<h1>Benvenuto</h1>
	<h3>Sei entrato nel sistema Entree. Attendi finche' la pagina non viene caricata...</h3>
    <?php 
		header("refresh:2;url=Home_Page.php"); 
	?> 
<?php
	} else {
		
		echo "<h1>Errore</h1>";
		echo "<h3>Le credenziali non sono valide. Effettua nuovamente l'accesso cliccando <a href=\"Login.php\">qui</a>.</h3>";
	}
?>
</body>
</html>