<!--Home_Page.php: pagina principale del sistema; da qui si può accedere a tutte le altre sezioni del sistema-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<?php
	//istruzione per disabilitare i messaggi di NOTICE
	error_reporting(0);
	//caricamento file esterni 
	require_once("Funzioni.php");
	require_once("Config.php");
	global $config;
	unset($_SESSION["utente"]);
	//inizio (o continuazione) di una sessione
	session_start();
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
    	<option selected="selected">Atlanta</option>
        <option>Boston</option>
        <option>Chicago</option>
        <option>Los Angeles</option>
        <option>New Orleans</option>
        <option>New York</option>
        <option>San Francisco</option>
        <option>Washington DC</option>
    </select>
</p> 
<p> Seleziona la categoria di tuo interesse
<br />
<div id="options" style="width: 30%; float: left;"><input type="checkbox" name="categoria[]" value="Locali etnici" /> Locali Etnici
<br />
<input type="checkbox" name="categoria[]" value="Ristorante Vegetariano" /> Ristorante Vegetariano
<br />
<input type="checkbox" name="categoria[]" value="Bar" /> Bar
<br />
<input type="checkbox" name="categoria[]" value="Vita Notturna" /> Vita Notturna
<br />
<input type="checkbox" name="categoria[]" value="Locali prestigiosi" /> Locali Prestigiosi
<br />
<input type="checkbox" name="categoria[]" value="Ristoranti di pesce" /> Ristoranti di pesce
<hr />
<input type="submit" name="invio_dati" value="Conferma dati" />
</div>
<div id="options" style="width: 40%; float: left;">
<input type="checkbox" name="categoria[]" value="Locale Economico" /> Locale Economico
<br />
<input type="checkbox" name="categoria[]" value="Braceria" /> Braceria
<br />
<input type="checkbox" name="categoria[]" value="Luoghi Romantici" /> Luoghi Romantici
<br />
<input type="checkbox" name="categoria[]" value="Disponibilita di servizi per disabili" /> Disponibilita' di servizi per disabili
<br />
<input type="checkbox" name="categoria[]" value="Pizzerie" /> Pizzerie
<br />
<input type="checkbox" name="categoria[]" value="Birrerie" /> Birrerie
<hr />
</div>
</p>

</form>
</div>
<div id="right" style="width: 50%; float: left;">
<?php
	if (!prima_interazione($_SESSION["utente"])){
		echo"<h1 align=\"center\">I piu' popolari</h1>";
		echo"<h4 align=\"center\">Di seguito sono elencati i ristoranti piu' votati dagli utenti del sistema Entree</h4>";
		echo"<table cellspacing=\"2\" border=\"2\" align=\"center\">";
		echo"<thead></thead>";
		echo"<th>Ristorante</th><th>Citta'</th><th>Votazione</th>
<tbody>";
	calcolaPiuVotati();	
	}
	else{
		echo"<h1 align=\"center\">Ristoranti consigliati</h1>";
		echo"<h4 align=\"center\">Di seguito sono elencati i ristoranti piu' votati dagli utenti del sistema, ordiati in base alle tue precedenti interazioni con Entree</h4>";
		echo"<table cellspacing=\"2\" border=\"2\" align=\"center\">";
		echo"<thead></thead>";
		echo"<th>Ristorante</th><th>Citta'</th><th>Votazione media Utenti</th>
<tbody>";
		calcolaPredizioneIniziale($_SESSION["utente"]);
	}
?>
</tbody>
</table>
</div>
</body>
</html>