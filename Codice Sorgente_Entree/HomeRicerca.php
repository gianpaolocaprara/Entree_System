<!--HomeRicerca.php: pagina che visualizza i risultati della ricerca effettuata dall'utente dall'Home Page; ha lo stesso aspetto dell'home page-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php
	//istruzione per disabilitare i messaggi di NOTICE
	error_reporting(0);
	//caricamento file esterni
	require_once("Funzioni.php");
	require_once("Config.php");
	//inizio (o continuazione) di una sessione
	session_start();
	//estrae i dati dal form
	$preferenze_categorie = isset($_GET["categoria"]) ? $_GET["categoria"] : array();
	$preferenza_citta  = filter_var($_GET['citta'], FILTER_SANITIZE_STRING)
?>

<html>
<head>
<title><?php echo $config["titolo"]; ?></title>
<!--Caricamento foglio di stile esterno-->
<link rel="stylesheet" type="text/css" href="stile.php"  />
</head>
<body>
<div id="left" style="width: 50%; float: left;">
<div id="intestazione">
    	<h1><?php echo $config["titolo"]; ?></h1>
        <h2>Benvenuto: <?php echo $_SESSION["utente"]; ?></h2>
    	<div id="menu">
        	<a href="Home_Page.php">Entree Home Page</a>
            <a href="Logout.php">Logout</a>
            <a href="javascript:history.back()">Torna indietro</a>
        </div>
    </div>
<form action="HomeRicerca.php" method="get" name="dati_iniziali">
<p> Seleziona la citta' di cui vuoi visualizzare i ristoranti </p>   
<p> 
	<select name= "citta"> 
    	<option <?php if ($_GET["citta"] == 'Atlanta') echo "selected=\"selected\"";?>>Atlanta</option>
        <option <?php if ($_GET["citta"] == 'Boston') echo "selected=\"selected\"";?>>Boston</option>
        <option <?php if ($_GET["citta"] == 'Chicago') echo "selected=\"selected\"";?>>Chicago</option>
        <option <?php if ($_GET["citta"] == 'Los Angeles') echo "selected=\"selected\"";?>>Los Angeles</option>
        <option <?php if ($_GET["citta"] == 'New Orleans') echo "selected=\"selected\"";?>>New Orleans</option>
        <option <?php if ($_GET["citta"] == 'New York') echo "selected=\"selected\"";?>>New York</option>
        <option <?php if ($_GET["citta"] == 'San Francisco') echo "selected=\"selected\"";?>>San Francisco</option>
        <option <?php if ($_GET["citta"] == 'Washington DC') echo "selected=\"selected\"";?>>Washington DC</option>
    </select>
</p> 
<p> Seleziona la categoria di tuo interesse
<br />
<div id="options" style="width: 30%; float: left;"><input type="checkbox" name="categoria[]" value="Locali etnici" <?php if(verificaCategoria($_GET["categoria"] , "Locali etnici")) echo "checked=\"checked\"";?> /> Locali Etnici
<br />
<input type="checkbox" name="categoria[]" value="Ristorante Vegetariano" <?php if(verificaCategoria($_GET["categoria"] , "Ristorante Vegetariano")) echo "checked=\"checked\"";?> /> Ristorante Vegetariano
<br />
<input type="checkbox" name="categoria[]" value="Bar" <?php if(verificaCategoria($_GET["categoria"] , 'Bar')) echo "checked=\"checked\"";?> /> Bar
<br />
<input type="checkbox" name="categoria[]" value="Vita Notturna" <?php if(verificaCategoria($_GET["categoria"] , 'Vita Notturna')) echo "checked=\"checked\"";?> /> Vita Notturna
<br />
<input type="checkbox" name="categoria[]" value="Locali prestigiosi" <?php if(verificaCategoria($_GET["categoria"] , 'Locali prestigiosi')) echo "checked=\"checked\"";?> /> Locali Prestigiosi
<br />
<input type="checkbox" name="categoria[]" value="Ristoranti di pesce" <?php if(verificaCategoria($_GET["categoria"] , 'Ristoranti di pesce')) echo "checked=\"checked\"";?> /> Ristoranti di pesce
<hr />
<input type="submit" name="invio_dati" value="Conferma dati" />
</div>
<div id="options" style="width: 40%; float: left;">
<input type="checkbox" name="categoria[]" value="Locale Economico" <?php if(verificaCategoria($_GET["categoria"] , 'Locale Economico')) echo "checked=\"checked\"";?> /> Locale Economico
<br />
<input type="checkbox" name="categoria[]" value="Braceria" <?php if(verificaCategoria($_GET["categoria"] , 'Braceria')) echo "checked=\"checked\"";?> /> Braceria
<br />
<input type="checkbox" name="categoria[]" value="Luoghi Romantici" <?php if(verificaCategoria($_GET["categoria"] , 'Luoghi Romantici')) echo "checked=\"checked\"";?> /> Luoghi Romantici
<br />
<input type="checkbox" name="categoria[]" value="Disponibilita di servizi per disabili" <?php if(verificaCategoria($_GET["categoria"] , 'Disponibilita di servizi per disabili')) echo "checked=\"checked\"";?> /> Disponibilita' di servizi per disabili
<br />
<input type="checkbox" name="categoria[]" value="Pizzerie" <?php if(verificaCategoria($_GET["categoria"] , 'Pizzerie')) echo "checked=\"checked\"";?> /> Pizzerie
<br />
<input type="checkbox" name="categoria[]" value="Birrerie" <?php if(verificaCategoria($_GET["categoria"] , 'Birrerie')) echo "checked=\"checked\"";?> /> Birrerie
<hr />
</div>
</p>

</form>
</div>
<div id="right" style="width: 50%; float: left;">
<?php
	if (!prima_interazione($_SESSION["utente"])){
		echo"<h1 align=\"center\">Ristoranti trovati</h1>";
		echo"<table cellspacing=\"2\" border=\"2\" align=\"center\">";
		echo"<thead></thead>";
		echo"<th>Nome Ristorante</th><th>Votazione</th>
<tbody>";
	ricercaRistorantiPiuVotati($preferenze_categorie,$preferenza_citta);	
	}
	else{
		echo"<h1 align=\"center\">Ristoranti trovati consigliati per te</h1>";
		echo"<table cellspacing=\"2\" border=\"2\" align=\"center\">";
		echo"<thead></thead>";
		echo"<th>Ristorante</th><th>Votazione media Utenti</th>
<tbody>";
		calcolaPredizioneRicerca($_SESSION["utente"], $preferenze_categorie, $preferenza_citta);
	}
?>
</tbody>
</table>
</div>
</body>
</html>