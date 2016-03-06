<!--Funzioni.php: File che contiene tutte le funzioni utilizzate nel sistema-->
<?php
	require_once("config.php");
	//dbConnect: funzione che si connette al database
	function dbConnect(){
		global $config;
		$conn = mysql_connect($config["db_server"], $config["db_utente"], $config["db_password"]) or die ("Errore nella connessione al db: " . mysql_error());
		mysql_select_db($config["db_database"]) or die ("Errore nella selezione del db: " . mysql_error());
		return $conn;	
	}
	
	//login_utente: verifica che i dati passati come parametri coincidano con quelli presenti nel database
	function login_utente($utente, $password){
		global $config;
		//inizio (o continuazione) di una sessione
		session_start();
		$_SESSION["utente"] = $utente;
		$conn = dbConnect();
		$sql = "SELECT password FROM utenti WHERE nome_utente = '" . $utente . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		if(mysql_num_rows($risposta) == 0)
			return FALSE;
		
		$riga = mysql_fetch_row ($risposta);
		mysql_close($conn);
		
		return ($password == $riga[0]);	
	}
	
		//registra_utente: funzione che restituisce TRUE se l'utente è stato inserito (e quindi registrato) correttamente sul DB, altrimenti restituisce FALSE se l'utente è già presente (e quindi la registrazione non è andata a buon fine)
	function registra_utente($utente , $password ) {
		$conn = dbConnect();
		
		$sql = "SELECT nome_utente FROM utenti WHERE nome_utente = '" . $utente . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		//se la risposta alla query è diversa da 0 (quindi contiene un utente con lo stesso nome), restituisce falso
		if(mysql_num_rows($risposta) != 0)
			return FALSE;
	
		$sql = "INSERT INTO utenti(nome_utente, password) VALUES ('" . $utente . "' , '" . $password . "')";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n!" . mysql_error());
		
		mysql_close ($conn);	
		return TRUE;
		
	}

	//funzione prima_interazione: verifica se l'utente ha eseguito l'accesso per la prima volta nel sistema dopo la registrazione e/o non ha effettuato alcuna votazione nel sistema
	function prima_interazione($utente){
		$conn = dbConnect();
		$sql = "SELECT DISTINCT nome_utente FROM utenti inner join interazioni on interazioni.id_utente = utenti.ID_UTENTE WHERE utenti.NOME_UTENTE = '" . $utente . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		if(mysql_num_rows($risposta) == 0)
			return FALSE;
		
		
		mysql_close($conn);
		return TRUE;
		
	}
	
	//calcolaPiuVotati: funzione che stampa la lista dei 15 ristoranti più votati
	function calcolaPiuVotati(){
		$conn = dbConnect();
		//seleziona i 15 ristoranti più votati
		$sql = "SELECT ristoranti.nome_ristorante, citta.NOME_CITTA, AVG(interazioni.votazione), ristoranti.id_ristorante AS votazione FROM interazioni inner join ristoranti on interazioni.id_ristorante = ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.ID_CITTA GROUP BY ristoranti.id_ristorante ORDER BY AVG(interazioni.votazione) DESC LIMIT 15";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[3] . "\">" . $riga[0] . "</a></td><td>" . $riga[1]. "</td><td>" . $riga[2] ."</td></tr>";	
		}
		
	}
	
	//ottieniInformazioniRistorante: restituisce le informazioni del ristorante
	function ottieniInformazioniRistorante($ristorante){
		$conn = dbConnect();	
		//seleziona il ristorante
		$sql = "SELECT ristoranti.nome_ristorante,citta.NOME_CITTA,categorie.NOME_CATEGORIA FROM ristoranti inner join categorie_ristoranti on ristoranti.id_ristorante = categorie_ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.ID_CITTA inner join categorie on categorie_ristoranti.id_categoria = categorie.ID_CATEGORIA WHERE ristoranti.id_ristorante =\"" . $ristorante . "\"";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());

		$risultato = array();
		while($riga = mysql_fetch_row($risposta))
			$risultato[] = $riga;
			
		mysql_close($conn);
		return $risultato;
	}
	
	//inserisciVotazione: funzione che inserisce la votazione di un ristorante all'interno del DB.
	function inserisciVotazione($utente,$ristorante,$citta,$votazione){
		$conn = dbConnect();
		$sql = "SELECT utenti.ID_UTENTE from utenti where utenti.NOME_UTENTE = '" . $utente . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$id_utente = mysql_fetch_row($risposta);
		
		$sql = "SELECT citta.ID_CITTA from citta where citta.NOME_CITTA = '" . $citta . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$id_citta = mysql_fetch_row($risposta);
		
		$sql = "INSERT INTO interazioni(id_utente,id_ristorante,id_citta,votazione) values('" . $id_utente[0] . "','".$ristorante."','" . $id_citta[0] . "','" . $votazione . "')";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		header("refresh:0;url=Ristorante.php?ristorante=" . $_POST["id_rist"]);
	}
	
	//ristoranteGiaVotato: funzione che verifica se un ristorante è già votato
	function ristoranteGiaVotato($utente,$ristorante){
		$conn = dbConnect();
		$sql = "SELECT utenti.NOME_UTENTE, interazioni.id_ristorante from interazioni inner join utenti on interazioni.id_utente = utenti.ID_UTENTE where utenti.NOME_UTENTE = '" . $utente . "' and interazioni.id_ristorante = '" . $ristorante . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		if(mysql_num_rows($risposta) == 0)
			return FALSE;
		
		
		mysql_close($conn);
		return TRUE;

	}

	//ricercaRistorantiPiuVotati: funzione che restituisce, a seconda della scelta dell'utente, i ristoranti, partendo da quelli con valutazione maggiore
	function ricercaRistorantiPiuVotati($categorie , $citta){
		$conn = dbConnect();
		$query = "";
		
		if (count($categorie) != 0){
		$sql = "SELECT citta.ID_CITTA from citta where citta.NOME_CITTA = '" . $citta . "'";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$id_citta = mysql_fetch_row($risposta);
		for ($i=0;$i<count($categorie);$i++){
			if ($i == 0)
				$query = $query . "categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";
			else	
			$query = $query . " and categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";	
		}
		$sql = "CREATE VIEW V1 AS SELECT DISTINCT ristoranti.nome_ristorante, categorie.NOME_CATEGORIA, ristoranti.id_ristorante FROM ristoranti inner join categorie_ristoranti on ristoranti.id_ristorante = categorie_ristoranti.id_ristorante inner join categorie on categorie_ristoranti.id_categoria = categorie.ID_CATEGORIA where ristoranti.id_citta =" . $id_citta[0] . " and ( " .  $query . " ) ";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		
		$query = "";
		
		for ($i=0;$i<count($categorie);$i++){
			if ($i == 0)
				$query = $query . "categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";
			else	
			$query = $query . " or categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";	
		}
		$sql = "CREATE VIEW V2 AS SELECT DISTINCT ristoranti.nome_ristorante, categorie.NOME_CATEGORIA, ristoranti.id_ristorante FROM ristoranti inner join categorie_ristoranti on ristoranti.id_ristorante = categorie_ristoranti.id_ristorante inner join categorie on categorie_ristoranti.id_categoria = categorie.ID_CATEGORIA where ristoranti.id_citta =" . $id_citta[0] . " and ( " .  $query . " ) ";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		
		$sql = "CREATE VIEW V3 AS SELECT DISTINCT v1.nome_ristorante,v1.id_ristorante FROM v1 left join v2 on v1.nome_ristorante = v2.nome_ristorante union SELECT DISTINCT v2.nome_ristorante,v2.id_ristorante FROM v1 right join v2 on v1.nome_ristorante = v2.nome_ristorante";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		
		$sql = "CREATE VIEW V4 AS SELECT v3.nome_ristorante,v3.id_ristorante,AVG(interazioni.votazione) as votazione FROM V3 left join interazioni on v3.id_ristorante = interazioni.id_ristorante GROUP BY v3.nome_ristorante ORDER BY (interazioni.votazione) DESC";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		$sql = "SELECT DISTINCT v4.nome_ristorante,v4.id_ristorante,v4.votazione FROM v4 GROUP BY v4.id_ristorante ORDER BY (v4.votazione) DESC LIMIT 20";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[1] . "\">" . $riga[0] . "</a></td><td>" . $riga[2] . "</td></tr>";
		}
		
		//distruggo le tabelle
		$sql = "DROP VIEW V1";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V2";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V3";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V4";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		} else {
			$sql = "SELECT ristoranti.nome_ristorante,ristoranti.id_ristorante,AVG(interazioni.votazione) FROM ristoranti left join interazioni on ristoranti.id_ristorante = interazioni.id_ristorante inner join citta on ristoranti.id_citta = citta.ID_CITTA WHERE citta.NOME_CITTA='" .$citta ."' GROUP BY ristoranti.id_ristorante ORDER BY AVG(interazioni.votazione) DESC LIMIT 20";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[1] . "\">" . $riga[0] . "</a></td><td>" . $riga[2] . "</td></tr>";
			}
		}
		mysql_close($conn);
	}
	
	function verificaCategoria($categoria, $stringa){
		for($i=0;$i<count($categoria);$i++){
			//echo $categoria[$i] . "<br />" . $stringa;
			if ($categoria[$i] == $stringa){
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	//pearson: funzione che calcola il coefficente di correlazione di pearson
	function pearson($X,$Y)
	{
	  if (!is_array($X) && !is_array($Y)) return false;
	  if (count($X) <> count($Y)) return false;
	  if (empty($X) || empty($Y)) return false;
	 
	  $n = count($X);
	  $mediaX = array_sum($X)/$n; // media delle x
	  $mediaY = array_sum($Y)/$n; // media delle y
	 
	  $SS = 0;
	  $SX = 0;
	  $SY = 0;
	 
	  for($i=0;$i<$n;$i++){
		$SS += ($X[$i] - $mediaX) * ($Y[$i] - $mediaY);
		$SX += pow(($X[$i] - $mediaX),2);
		$SY += pow(($Y[$i] - $mediaY),2);
	  }
	
	  $pearson = $SS / (sqrt($SX) * sqrt($SY));
	 
	  return $pearson;
	}
	
	//calcolaPredizioneIniziale: algoritmo di raccomandazione che viene applicato, una volta entrato nel sistema, per ordinare, a seconda delle preferenze dell'utente, i 15 ristoranti più votati
	function calcolaPredizioneIniziale($utente){
		$conn = dbConnect();
		//creazione tabella dove inserire le medie pesate dei ristoranti
		$sql = "CREATE TABLE risultati(id_ristorante char(8), media_pesata decimal(16,13))";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		//seleziona i 15 ristoranti più votati
		$sql = "CREATE VIEW v1 AS SELECT ristoranti.nome_ristorante, citta.NOME_CITTA, AVG(interazioni.votazione), ristoranti.id_ristorante FROM interazioni inner join ristoranti on interazioni.id_ristorante = ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.ID_CITTA GROUP BY ristoranti.id_ristorante ORDER BY AVG(interazioni.votazione) DESC LIMIT 15";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "SELECT v1.id_ristorante FROM v1 ORDER BY (v1.id_ristorante)";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		while($ristorante = mysql_fetch_row($risposta)){
			//array dove ci sono tutti i ristoranti piu votati
			$array_ristorante[] = $ristorante[0];
		}
	
		for ($i = 0; $i < count($array_ristorante); $i++){
			$media_pesata_totale = 0;
			//per ogni ristorante recupera le feature del ristorante
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristorante[$i] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
			//array in cui vengono salvate le feature di un ristorante
			$array_feature = Array();
			while($feature = mysql_fetch_row($risposta))	
				$array_feature[] = $feature[0];

			//array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayX = Array();
			for ($k=0;$k<256;$k++){
				$arrayX[$k] = 0;	
			}
			
			for ($j=0;$j<count($array_feature);$j++){
				//$arrayX[$array_feature[$j]] = $array_feature[$j];
				$arrayX[$array_feature[$j]] = 1;
			}
		
			//recupera istanza utente loggato
			$sql = "SELECT interazioni.id_ristorante from interazioni inner join utenti on interazioni.id_utente = utenti.ID_UTENTE where utenti.NOME_UTENTE = '". $utente ."' ORDER BY(interazioni.id_ristorante)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono i ristoranti votati dall'utente X
			$array_ristoranti_votati = Array();
			while($ristorante_sin = mysql_fetch_row($risposta)){
			$array_ristoranti_votati[] = $ristorante_sin[0];
			}
			
			for ($z = 0; $z < count($array_ristoranti_votati) ; $z++){
			//while($array_ristoranti_votati = mysql_fetch_row($risposta)){
				//per ogni ristorante recupera le feature del ristorante votato
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristoranti_votati[$z] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono le feature di UN ristorante votato dall'utente X
			$array_feature2 = Array();
			while($feature2 = mysql_fetch_row($risposta))
				$array_feature2[] = $feature2[0];

			//secondo array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayY = Array();
			for ($q=0;$q<256;$q++){
				$arrayY[$q] = 0;	
			}
			
			//avvalora i campi
			for ($w=0;$w<count($array_feature2);$w++){
				//$array_booleano2[$array_feature2[$w]] = $array_feature2[$w];
				$arrayY[$array_feature2[$w]] = 1;	
			}
			
			//calcola il coefficiente di correlazione di Pearson tra i due array creati
			$pearson = pearson($arrayX,$arrayY);
			//recupero della votazione del ristorante votato dall'utente
			$sql = "SELECT interazioni.votazione FROM interazioni inner join utenti on interazioni.id_utente = utenti.id_utente WHERE interazioni.id_ristorante = '" . $array_ristoranti_votati[$z] . "' and utenti.nome_utente='" . $utente . "'";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			$voto = mysql_fetch_row($risposta);
			//calcolo della media pesata tra il voto dato all'utente e il coefficiente di correlazione
			$media_pesata = $pearson * $voto[0];
			//variabile che tiene traccia della media pesata totale
			$media_pesata_totale = $media_pesata_totale + $media_pesata;	
			}	
			//echo $media_pesata_totale . "<br />";	
			//inserimento nel DB del risultato ottenuto (inserimento  ID ristorante dei 15 + mediapesatatotale)
		$sql = "INSERT INTO risultati(id_ristorante, media_pesata) VALUES ('" . $array_ristorante[$i] . "','" . $media_pesata_totale . "')";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
		}
		
		//mostra risultati all'utente
		$sql = "SELECT DISTINCT ristoranti.nome_ristorante,risultati.media_pesata,risultati.id_ristorante,citta.nome_citta FROM risultati inner join ristoranti on risultati.id_ristorante = ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.id_citta GROUP BY risultati.id_ristorante ORDER BY risultati.media_pesata DESC";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[2] . "\">" . $riga[0] . "</a></td><td>" . $riga[3] . "</td>";
			$sql2 = "SELECT AVG(interazioni.votazione) FROM interazioni inner join ristoranti on interazioni.id_ristorante = ristoranti.id_ristorante WHERE interazioni.id_ristorante = '" . $riga[2] . "'";
			$risp = mysql_query($sql2) or die ("Errore nella query: " . $sql2 . "\n" . mysql_error());
			$riga_2 = mysql_fetch_row($risp);
			echo "<td align=\"center\">" . $riga_2[0] . "</td></tr>";
		}
		//distruggi tabella
		$sql = "DROP VIEW V1";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP TABLE risultati";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
	}
	
	//calcolaPredizioneRicerca: algoritmo di raccomandazione che viene applicato una volta che l'utente (nel caso in cui abbia già votato almeno una volta) sceglie le opzioni di ricerca
	function calcolaPredizioneRicerca($utente, $categorie, $citta){
		$conn = dbConnect();
		//creazione tabella dove inserire le medie pesate dei ristoranti
		$sql = "CREATE TABLE risultati(id_ristorante char(8), media_pesata decimal(16,13))";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		if(count($categorie) != 0){
			$sql = "SELECT citta.ID_CITTA from citta where citta.NOME_CITTA = '" . $citta . "'";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			$id_citta = mysql_fetch_row($risposta);
			for ($i=0;$i<count($categorie);$i++){
				if ($i == 0)
					$query = $query . "categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";
				else	
				$query = $query . " and categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";	
			}//end for
			
			$sql = "CREATE VIEW V1 AS SELECT DISTINCT ristoranti.nome_ristorante, categorie.NOME_CATEGORIA, ristoranti.id_ristorante FROM ristoranti inner join categorie_ristoranti on ristoranti.id_ristorante = categorie_ristoranti.id_ristorante inner join categorie on categorie_ristoranti.id_categoria = categorie.ID_CATEGORIA where ristoranti.id_citta =" . $id_citta[0] . " and ( " .  $query . " ) ";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		
		$query = "";
		
		for ($i=0;$i<count($categorie);$i++){
			if ($i == 0)
				$query = $query . "categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";
			else	
			$query = $query . " or categorie.NOME_CATEGORIA = '" . $categorie[$i] . "'";	
		}
		$sql = "CREATE VIEW V2 AS SELECT DISTINCT ristoranti.nome_ristorante, categorie.NOME_CATEGORIA, ristoranti.id_ristorante FROM ristoranti inner join categorie_ristoranti on ristoranti.id_ristorante = categorie_ristoranti.id_ristorante inner join categorie on categorie_ristoranti.id_categoria = categorie.ID_CATEGORIA where ristoranti.id_citta =" . $id_citta[0] . " and ( " .  $query . " ) ";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		
		$sql = "CREATE VIEW V3 AS SELECT DISTINCT v1.nome_ristorante,v1.id_ristorante FROM v1 left join v2 on v1.nome_ristorante = v2.nome_ristorante union SELECT DISTINCT v2.nome_ristorante,v2.id_ristorante FROM v1 right join v2 on v1.nome_ristorante = v2.nome_ristorante";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		
		$sql = "CREATE VIEW V4 AS SELECT v3.nome_ristorante,v3.id_ristorante,AVG(interazioni.votazione) as votazione FROM V3 left join interazioni on v3.id_ristorante = interazioni.id_ristorante GROUP BY v3.nome_ristorante ORDER BY (interazioni.votazione) DESC LIMIT 50";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		$sql = "SELECT v4.id_ristorante FROM v4 ORDER BY (v4.id_ristorante)";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		while($ristorante = mysql_fetch_row($risposta)){
			//array dove ci sono tutti i ristoranti piu votati a seconda delle scelte dell'utente
			$array_ristorante[] = $ristorante[0];
		}
		
		for ($i = 0; $i < count($array_ristorante); $i++){
			$media_pesata_totale = 0;
			//per ogni ristorante recupera le feature del ristorante
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristorante[$i] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
			//array in cui vengono salvate le feature di un ristorante
			$array_feature = Array();
			while($feature = mysql_fetch_row($risposta))	
				$array_feature[] = $feature[0];

			//array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayX = Array();
			for ($k=0;$k<256;$k++){
				$arrayX[$k] = 0;	
			}
			
			for ($j=0;$j<count($array_feature);$j++){
				//$arrayX[$array_feature[$j]] = $array_feature[$j];
				$arrayX[$array_feature[$j]] = 1;
			}
		
			//recupera istanza utente loggato
			$sql = "SELECT interazioni.id_ristorante from interazioni inner join utenti on interazioni.id_utente = utenti.ID_UTENTE where utenti.NOME_UTENTE = '". $utente ."' ORDER BY(interazioni.id_ristorante)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono i ristoranti votati dall'utente X
			$array_ristoranti_votati = Array();
			while($ristorante_sin = mysql_fetch_row($risposta)){
			$array_ristoranti_votati[] = $ristorante_sin[0];
			}
			
			for ($z = 0; $z < count($array_ristoranti_votati) ; $z++){
			//while($array_ristoranti_votati = mysql_fetch_row($risposta)){
				//per ogni ristorante recupera le feature del ristorante votato
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristoranti_votati[$z] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono le feature di UN ristorante votato dall'utente X
			$array_feature2 = Array();
			while($feature2 = mysql_fetch_row($risposta))
				$array_feature2[] = $feature2[0];

			//secondo array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayY = Array();
			for ($q=0;$q<256;$q++){
				$arrayY[$q] = 0;	
			}
			
			//avvalora i campi
			for ($w=0;$w<count($array_feature2);$w++){
				//$array_booleano2[$array_feature2[$w]] = $array_feature2[$w];
				$arrayY[$array_feature2[$w]] = 1;	
			}
			
			//calcola il coefficiente di correlazione di Pearson tra i due array creati
			$pearson = pearson($arrayX,$arrayY);
			//recupero della votazione del ristorante votato dall'utente
			$sql = "SELECT interazioni.votazione FROM interazioni inner join utenti on interazioni.id_utente = utenti.id_utente WHERE interazioni.id_ristorante = '" . $array_ristoranti_votati[$z] . "' and utenti.nome_utente='" . $utente . "'";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			$voto = mysql_fetch_row($risposta);
			//calcolo della media pesata tra il voto dato all'utente e il coefficiente di correlazione
			$media_pesata = $pearson * $voto[0];
			//variabile che tiene traccia della media pesata totale
			$media_pesata_totale = $media_pesata_totale + $media_pesata;	
			}	
			//echo $media_pesata_totale . "<br />";	
			//inserimento nel DB del risultato ottenuto (inserimento  ID ristorante dei 15 + mediapesatatotale)
			$sql = "INSERT INTO risultati(id_ristorante, media_pesata) VALUES ('" . $array_ristorante[$i] . "','" . $media_pesata_totale . "')";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
		}
		
		//mostra risultati all'utente
		$sql = "SELECT DISTINCT ristoranti.nome_ristorante,risultati.media_pesata,risultati.id_ristorante,citta.nome_citta FROM risultati inner join ristoranti on risultati.id_ristorante = ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.id_citta GROUP BY risultati.id_ristorante ORDER BY risultati.media_pesata DESC LIMIT 20";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[2] . "\">" . $riga[0] . "</a></td>";
			$sql2 = "SELECT AVG(interazioni.votazione) FROM interazioni inner join ristoranti on interazioni.id_ristorante = ristoranti.id_ristorante WHERE interazioni.id_ristorante = '" . $riga[2] . "'";
			$risp = mysql_query($sql2) or die ("Errore nella query: " . $sql2 . "\n" . mysql_error());
			$riga_2 = mysql_fetch_row($risp);
			echo "<td align=\"center\">" . $riga_2[0] . "</td></tr>";
		}
		
		//distruggo le tabelle
		$sql = "DROP VIEW V1";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V2";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V3";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP VIEW V4";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		$sql = "DROP TABLE risultati";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		} else {
			
			$sql = "CREATE VIEW v1 AS SELECT ristoranti.nome_ristorante,ristoranti.id_ristorante,AVG(interazioni.votazione) FROM ristoranti left join interazioni on ristoranti.id_ristorante = interazioni.id_ristorante inner join citta on ristoranti.id_citta = citta.ID_CITTA WHERE citta.NOME_CITTA='" .$citta ."' GROUP BY ristoranti.id_ristorante ORDER BY AVG(interazioni.votazione) DESC LIMIT 50";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
			$sql = "SELECT v1.id_ristorante FROM v1 ORDER BY (v1.id_ristorante)";
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		
		while($ristorante = mysql_fetch_row($risposta)){
			//array dove ci sono tutti i ristoranti piu votati a seconda delle scelte dell'utente
			$array_ristorante[] = $ristorante[0];
		}
		
		for ($i = 0; $i < count($array_ristorante); $i++){
			$media_pesata_totale = 0;
			//per ogni ristorante recupera le feature del ristorante
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristorante[$i] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
			//array in cui vengono salvate le feature di un ristorante
			$array_feature = Array();
			while($feature = mysql_fetch_row($risposta))	
				$array_feature[] = $feature[0];

			//array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayX = Array();
			for ($k=0;$k<256;$k++){
				$arrayX[$k] = 0;	
			}
			
			for ($j=0;$j<count($array_feature);$j++){
				//$arrayX[$array_feature[$j]] = $array_feature[$j];
				$arrayX[$array_feature[$j]] = 1;
			}
		
			//recupera istanza utente loggato
			$sql = "SELECT interazioni.id_ristorante from interazioni inner join utenti on interazioni.id_utente = utenti.ID_UTENTE where utenti.NOME_UTENTE = '". $utente ."' ORDER BY(interazioni.id_ristorante)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono i ristoranti votati dall'utente X
			$array_ristoranti_votati = Array();
			while($ristorante_sin = mysql_fetch_row($risposta)){
			$array_ristoranti_votati[] = $ristorante_sin[0];
			}
			
			for ($z = 0; $z < count($array_ristoranti_votati) ; $z++){
			//while($array_ristoranti_votati = mysql_fetch_row($risposta)){
				//per ogni ristorante recupera le feature del ristorante votato
			$sql = "SELECT feature_ristoranti.id_feature FROM feature_ristoranti WHERE feature_ristoranti.id_ristorante = '" . $array_ristoranti_votati[$z] . "' ORDER BY (feature_ristoranti.id_feature)";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			//array dove ci sono le feature di UN ristorante votato dall'utente X
			$array_feature2 = Array();
			while($feature2 = mysql_fetch_row($risposta))
				$array_feature2[] = $feature2[0];

			//secondo array di 256 elementi dove verranno avvalorati ad 1 le posizioni in cui la feature del ristorante è presente
			$arrayY = Array();
			for ($q=0;$q<256;$q++){
				$arrayY[$q] = 0;	
			}
			
			//avvalora i campi
			for ($w=0;$w<count($array_feature2);$w++){
				//$array_booleano2[$array_feature2[$w]] = $array_feature2[$w];
				$arrayY[$array_feature2[$w]] = 1;	
			}
			
			//calcola il coefficiente di correlazione di Pearson tra i due array creati
			$pearson = pearson($arrayX,$arrayY);
			//recupero della votazione del ristorante votato dall'utente
			$sql = "SELECT interazioni.votazione FROM interazioni inner join utenti on interazioni.id_utente = utenti.id_utente WHERE interazioni.id_ristorante = '" . $array_ristoranti_votati[$z] . "' and utenti.nome_utente='" . $utente . "'";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			$voto = mysql_fetch_row($risposta);
			//calcolo della media pesata tra il voto dato all'utente e il coefficiente di correlazione
			$media_pesata = $pearson * $voto[0];
			//variabile che tiene traccia della media pesata totale
			$media_pesata_totale = $media_pesata_totale + $media_pesata;	
			}	
			//echo $media_pesata_totale . "<br />";	
			//inserimento nel DB del risultato ottenuto (inserimento  ID ristorante dei 15 + mediapesatatotale)
			$sql = "INSERT INTO risultati(id_ristorante, media_pesata) VALUES ('" . $array_ristorante[$i] . "','" . $media_pesata_totale . "')";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			
		}
		
		//mostra risultati all'utente
		$sql = "SELECT DISTINCT ristoranti.nome_ristorante,risultati.media_pesata,risultati.id_ristorante,citta.nome_citta FROM risultati inner join ristoranti on risultati.id_ristorante = ristoranti.id_ristorante inner join citta on ristoranti.id_citta = citta.id_citta GROUP BY risultati.id_ristorante ORDER BY risultati.media_pesata DESC LIMIT 20";
		
		$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());	
		while($riga = mysql_fetch_row($risposta)){
			echo "<tr><td><a href=\"Ristorante.php?ristorante=" . $riga[2] . "\">" . $riga[0] . "</a></td>";
			$sql2 = "SELECT AVG(interazioni.votazione) FROM interazioni inner join ristoranti on interazioni.id_ristorante = ristoranti.id_ristorante WHERE interazioni.id_ristorante = '" . $riga[2] . "'";
			$risp = mysql_query($sql2) or die ("Errore nella query: " . $sql2 . "\n" . mysql_error());
			$riga_2 = mysql_fetch_row($risp);
			echo "<td align=\"center\">" . $riga_2[0] . "</td></tr>";
		}
			//distruzione tabelle
			$sql = "DROP VIEW V1";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
			$sql = "DROP TABLE risultati";
			$risposta = mysql_query($sql) or die ("Errore nella query: " . $sql . "\n" . mysql_error());
		}
		mysql_close($conn);
	}
	
	
	
?>