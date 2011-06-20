<?php

// Si quelqu'un veut créer une partie
if ( isset($_POST['CreerPartie']) )
{
	$PartieNom 		= htmlentities($_POST['PartieNom'], ENT_QUOTES);
	
	$sql = "SELECT PartieID
		FROM Partie
		WHERE PartieNom = '" . $PartieNom ."'";
	$req = mysql_query($sql) or die('Erreur SQL #032<br />'.$sql.'<br />'.mysql_error());
	$data = mysql_fetch_array($req);
	if ( $data['PartieID'] )
	{
		echo "Erreur : Le nom de la partie est déjà utilisé";
		exit;
	}

	// Mise à jour du joueur, qui devient administrateur par défaut
	if ( !$Joueur )
	{
		// L'ID de l'administrateur n'a pas été trouvé
		echo "Erreur : Pas d'administrateur";
		exit;
	}
	// Création de la partie
	$sql = 'INSERT INTO Partie (PartieNom)
	VALUES("'.$PartieNom.'")';
	mysql_query($sql) or die('Erreur SQL #002'.$sql.'<br />'.mysql_error());

	// Le mode passe en Carte, pour l'éditer
	$mode = "Carte";
	
	// Partie prend la valeur du dernier ID inséré
	$Partie = mysql_insert_id();
	
	// Mise à jour du statut du joueur, qui devient admin
	$sql = "UPDATE Joueur
		SET JoueurAdmin = 1
			WHERE JoueurID = " . $Joueur;
	mysql_query($sql) or die('Erreur SQL #003<br />'.$sql.'<br />'.mysql_error());

	// Création de son Etat
	$sql = 'INSERT INTO Etat (EtatJoueur, EtatPartie)
		VALUES("'.$Joueur.'","'.$Partie.'")';
	mysql_query($sql) or die('Erreur SQL #009'.$sql.'<br />'.mysql_error());
	
	
	//
	// Création de la carte brute
	//
	
	$DimensionX = $_POST['DimensionX'];
	$DimensionY = $_POST['DimensionY'];
	
	// On récupère le dernier ID des régions pour déterminer le prochain ID des régions et territoires
	$sql = "SELECT MAX(TerritoireID) AS MaxID
		FROM Territoire";
	$req = mysql_query($sql) or die('Erreur SQL #013<br />'.$sql.'<br />'.mysql_error());

	$data = mysql_fetch_array($req);
	$MaxID = $data['MaxID'];
	
	$sql = "SELECT MAX(RegionID) AS MaxID
		FROM Region";
	$req = mysql_query($sql) or die('Erreur SQL #31<br />'.$sql.'<br />'.mysql_error());

	$data = mysql_fetch_array($req);
	$MaxID = ( $MaxID > $data['MaxID'] ) ? $MaxID : $data['MaxID'] ;

	$Compteur = $MaxID;
	
	for ( $CoordonneeX = 1 ; $CoordonneeX <= $DimensionX ; $CoordonneeX++ )
	{
		for ( $CoordonneeY = 1 ; $CoordonneeY <= $DimensionY ; $CoordonneeY++ )
		{
			// Une région et un territoire sont crées par carré. Les régions pourront être fusionnées pour constituer un plus grand territoire, dans la phase suivante.
			$Compteur++;
			$sql = 'INSERT INTO Region (RegionTerritoire, RegionPartie, RegionCoordonneeX, RegionCoordonneeY)
				VALUES(' . $Compteur . ',' . $Partie . ',' . $CoordonneeX . ',' . $CoordonneeY . ')';
			mysql_query($sql) or die('Erreur SQL #010 Carte'.$sql.'<br />'.mysql_error());

			$sql = 'INSERT INTO Territoire (TerritoireID, TerritoireEtat, TerritoirePartie)
				VALUES(' . $Compteur . ', 0, ' . $Partie . ')';
			mysql_query($sql) or die('Erreur SQL #014 Carte'.$sql.'<br />'.mysql_error());
		}
	}
}

else if ( isset($_POST['OuvrirPartie']) )
{
	$NouveauStatut 	= 0;
	// Mise à jour du statut de la partie, une fois que la carte est OK
	$sql = "UPDATE Partie
		SET PartieStatut = " . $NouveauStatut . "
			WHERE PartieID = " . $Partie;
	mysql_query($sql) or die('Erreur SQL #011<br />'.$sql.'<br />'.mysql_error());
	
	Message($Partie, 0, "Placement", " Actualisez la page pour capturer, <u>selon votre ordre</u>, vos territoires", 0, "", "noire", 15);

	$mode = ( $NouveauStatut == 1 ) ? "Partie" : "Preparation";
}

// Si quelqu'un veut créer un joueur
else if ( isset($_POST['CreerJoueur']) )
{
	$JoueurNom 		= $_POST['JoueurNom'];
	$JoueurNation 	= $_POST['JoueurNation'];
	$JoueurMdp 		= sha1($_POST['JoueurMdp']);
	
	$sql = "SELECT JoueurID
		FROM Joueur
		WHERE JoueurNom = '" . $JoueurNom ."'";
	$req = mysql_query($sql) or die('Erreur SQL #004<br />'.$sql.'<br />'.mysql_error());

	if ( !$data = mysql_fetch_array($req))
	{
		$sql = 'INSERT INTO Joueur (JoueurNom, JoueurMdp)
		VALUES("'.$JoueurNom.'","'.$JoueurMdp.'")';
		mysql_query($sql) or die('Erreur SQL #005'.$sql.'<br />'.mysql_error());
		$Joueur = mysql_insert_id();
		$message = "Vous pouvez désormais vous connecter à une partie ou créer une partie";
		$mode = "RejoindreUnePartie";
	}
	else
	{
		// Le Pseudo est déjà pris
		$message = "Ce pseudonyme est déjà pris";
	}
}

// Si un joueur veut rejoindre une partie
else if ( isset($_POST['Rejoindre']) )
{
	$JoueurNom 		= $_POST['JoueurNom'];
	$JoueurMdp 		= sha1($_POST['JoueurMdp']);
	$PartieID 		= $_POST['RejoindrePartieID'];

	$sql = "SELECT JoueurID, JoueurMdp, JoueurAdmin
		FROM Joueur
		WHERE JoueurNom = '" . $JoueurNom ."'";
	$req = mysql_query($sql) or die('Erreur SQL #006<br />'.$sql.'<br />'.mysql_error());

	if ($data = mysql_fetch_array($req))
	{
		$JoueurID		= $data['JoueurID'];
		$JoueurAdmin	= $data['JoueurAdmin'];
		
		if ( $data['JoueurMdp'] == $JoueurMdp )
		{
			// Le mot de passe donné est correct
			// On va alors regarder si le joueur a déjà rejoint la partie qu'il
			// souhaite rejoindre ou s'il faut lui créer un Etat
			
			$sql = "SELECT EtatID
				FROM Etat
				WHERE EtatJoueur = " . $JoueurID ."
					AND EtatPartie = " . $PartieID;
			$requete = mysql_query($sql) or die('Erreur SQL #007<br />'.$sql.'<br />'.mysql_error());
			
			if (!$data2 = mysql_fetch_array($requete))
			{
				// Le Joueur n'a pas encore d'Etat dans cette partie. On lui en crée un.
				$sql = 'INSERT INTO Etat (EtatJoueur, EtatPartie)
					VALUES('.$JoueurID.','.$PartieID.')';
				mysql_query($sql) or die('Erreur SQL #008'.$sql.'<br />'.mysql_error());

				$Etat =  mysql_insert_id();
				Message($PartieID, $JoueurID, "Bienvenue", "Nous vous souhaitons la bienvenue", 0, "", "noire", 8);
				Message($PartieID, 0, "Nouveau Joueur", $JoueurNom . " a rejoint la partie", 0, $JoueurID, "noire", 8);
			}
			else
			{
				$Etat = $data2['EtatID'];
			}
			mysql_free_result($requete);  

			// Le Joueur rejoint la Partie.
			$Partie = $PartieID;
			$Joueur = $JoueurID;
			
			$sql = "SELECT PartieStatut
				FROM Partie
				WHERE PartieID = " . $Partie;
			$req = mysql_query($sql) or die('Erreur SQL #006<br />'.$sql.'<br />'.mysql_error());
			$data = mysql_fetch_array($req);
			
			$PartieStatut = $data['PartieStatut'];
			
			$message = "";
			$mode = ( $PartieStatut == 1 ) ? "Partie" : "Preparation";
			if ( $JoueurAdmin && $mode == "Preparation" && $PartieStatut == -1)
			{
				$mode = "Carte";
			}
		}
		else
		{
			$mode = "RejoindreUnePartie";
			$message = "Votre mot de passe est incorrect";
		}
	}
	else
	{
		$mode = "RejoindreUnePartie";
		$message = "Votre identifiant est incorrect";
	}
	mysql_free_result($req);  
}

?>