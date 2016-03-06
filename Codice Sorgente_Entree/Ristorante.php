<!--Ristorante.php: Pagina che mostra le informazioni di un singolo ristorante-->
<?php
	//error reporting --> viene utilizzato per non far visualizzare i NOTICE durante l'esecuzione del programma
	error_reporting(0);
	//caricamento file di configurazione e delle funzioni utilizzate
	require_once("Config.php");
	require_once("Funzioni.php");
	//se l'immagine non è corretta segnala errore
	if (!isset($_GET["ristorante"]))
		die("Errore: stai cercando di accedere al ristorante in modo scorretto\n");
	
	//pone in $id_ristorante l'id del ristorante corrente	
	$id_ristorante = $_GET["ristorante"];
	//inizio (o continuazione) di una sessione
	session_start();
	$info_ristorante = ottieniInformazioniRistorante($id_ristorante);
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link rel="stylesheet" type="text/css" href="stile.php"  />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo "Ristorante: " . $info_ristorante[0][0]; ?></title>
</head>

<body>
<div id="intestazione">
    	<h1><?php echo $config["titolo"]; ?></h1>
        <h2>Benvenuto: <?php echo $_SESSION["utente"]; ?></h2>
    	<div id="menu">
        	<a href="Home_Page.php">Entree Home Page</a>
            <a href="Logout.php">Logout</a>
            <a href="javascript:history.back()">Torna indietro</a>
        </div>
    </div>
<?php 
	if (!ristoranteGiaVotato($_SESSION["utente"],$id_ristorante) && $_SERVER['REQUEST_METHOD'] == "GET"){
		if (count($info_ristorante) > 0){
				echo "<h1>Nome ristorante:" . $info_ristorante[0][0] . "</h1>"; 
				echo "<h3>Il ristorante si trova nella citta' di: " . $info_ristorante[0][1] . " </h3>"; 
				echo "<h4>Il ristorante selezionato rientra nell'ambito delle seguenti categorie:</h4><br/>";
				$i=1;
				foreach ($info_ristorante as $categoria){
					echo $i . ":" . $categoria[2] . "<br />";	
					$i++;	
				}
			}
			echo "<hr />";
?>	
<p>Se hai già provato questo ristorante e vuoi dargli una votazione, puoi farlo qui sotto!</p>
<form <?php echo "action=\"", $_SERVER['PHP_SELF'] . "?ristorante=" . $id_ristorante, "\""; ?>  method="post" name="votazione">
<input type="hidden" name="id_rist" value="<?=$id_ristorante;?>" />
<input type="hidden" name="utente" value="<?=$_SESSION["utente"];?>" />
<input type="hidden" name="citta" value="<?=$info_ristorante[0][1];?>" />
<p><input type="radio" name="voto" value="1" /><img src="Immagini/1_star.png" alt="1 Stella" />
<input type="radio" name="voto" value="2" /><img src="Immagini/2_star.png" alt="2 Stella" />
<input type="radio" name="voto" value="3" /><img src="Immagini/3_star.png" alt="3 Stella" />
<input type="radio" name="voto" value="4" /><img src="Immagini/4_star.png" alt="4 Stella" />
<input type="radio" name="voto" value="5" /><img src="Immagini/5_star.png" alt="5 Stella" />
</p>
<br />
<input type="submit" value="Vota!" />
</form>
<?php
	}else if (!ristoranteGiaVotato($_SESSION["utente"],$id_ristorante) && $_SERVER['REQUEST_METHOD'] == "POST") {
		inserisciVotazione($_POST["utente"],$_POST["id_rist"],$_POST["citta"],$_POST["voto"]);
	}else {
		if (count($info_ristorante) > 0){
				echo "<h1>Nome ristorante:" . $info_ristorante[0][0] . "</h1>"; 
				echo "<h3>Il ristorante si trova nella citta' di: " . $info_ristorante[0][1] . " </h3>"; 
				echo "<h4>Il ristorante selezionato rientra nell'ambito delle seguenti categorie:</h4><br/>";
				$i=1;
				foreach ($info_ristorante as $categoria){
					echo $i . ":" . $categoria[2] . "<br />";	
					$i++;	
				}
			}
			echo "<hr />";
			echo "Hai gia' dato una votazione a questo ristorante!";
				//Il tuo voto e' " . ottieniValutazione() . " stelle.";
	}
	?>
</body>
</html>