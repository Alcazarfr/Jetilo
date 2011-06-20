<?php

//
// Fonctions.php Reprend toutes les fonctions utilisées
//

// 
// Connexion à la Base de données
//
function connectMaBase()
{
    $base = mysql_connect('localhost', 'root', 'root');  
    mysql_select_db ('jet', $base) ;
}

function Couleur($var)
{
	$return = ( $var < 0 ) ? "<font color=\"FF0000\"><b>" . $var . "</b></font>" : "<b>" . $var . "</b>";
	return $return;
}

function adjectif($nombre)
{
	if ( $nombre == "pluriel" ) 
	{
		$listeAdjectifs = Array("patéthiques", "modestes", "grandioses", "extradordinaires", "gigantesques", "gargantuesques", "fantasmagoriques", "pitoyables"," inutiles", "intéressants", "sympathiques", "");
	}
	else
	{
		$listeAdjectifs = Array("patéthique", "modeste", "grandiose", "extradordinaire", "gigantesque", "gargantuesque", "fantasmagorique", "pitoyable"," inutile", "intéressant", "sympathique", "");
	}
	$rand 			= mt_rand(1, count($listeAdjectifs));
	$adjectif 		= $listeAdjectifs[$rand];
	return $adjectif;
}

/* Insérer un message

Partie 			: ID de la Partie
Destinataire 	: Joueur à qui le message est reservé; Laisser 0 si le message est visible par tous
Source 			: Origine du message (ID du joueur)
Exlus			: varchar, la liste des joueurs qui ne liront pas ce message
				  Ex : un message public qui a déjà été envoyé à un joueur, de façon personnalisé
Couleur			: La Couleur, noire et rouge (si important)
Durée			: Durée d'affichage
				  0 pour rester à vie, 5 pour un affichage court, 10 pour un normal, 15 pour un long

*/

function Message($Partie, $Destinataire, $Titre, $Texte, $Source, $Exclus, $Couleur, $Duree)
{
	$sql = "INSERT INTO Message (MessagePartie, MessageDestinataire, MessageExclus, MessageTitre, MessageTexte, MessageSource, MessageTime, MessageCouleur, MessageDuree)
		VALUES (".$Partie.", " . $Destinataire . ", '" . $Exclus . "', '" . $Titre . "', '" . $Texte . "', " . $Source . ", " . time() . ", '" . $Couleur . "', " . $Duree . ")";
	mysql_query($sql) or die('Erreur SQL #29 Message<br />'.$sql.'<br />'.mysql_error());	
}



/* Trouver une route entre deux territoires ou les territoires voisins

TerritoireOrigine 		: ID du territoire
TerritoireDestination	: ID du territoire où l'on veut aller
			  
Return 	: 	Array(TerritoireVoisins1, TerritoireVoisinsN) si TerritoireDestination = fasle
			Array(AccèsDirectPossible[TRUE;FALSE], TempsPourYAller)

*/

function TerritoireAcces($TerritoireOrigine, $TerritoireDestination=false)
{
	$nombre = 0;
	$ListeTerritoire = "";
	$TerritoireTrouve = Array();
	$TerritoireTrouve[$TerritoireOrigine] = TRUE;
	$Compteur = 0;

	$sql = "SELECT RegionPartie, RegionID, RegionTerrain, RegionCoordonneeX, RegionCoordonneeY
		FROM Region
		WHERE RegionTerritoire = " . $TerritoireOrigine;
	$req = mysql_query($sql) or die('Erreur SQL #042<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req) )
	{
		$nombre++;
		$RegionPartie 		= $data['RegionPartie'];
		$RegionID 			= $data['RegionID'];
		$RegionTerrain 		= $data['RegionTerrain'];
		$RegionCoordonneeX 	= $data['RegionCoordonneeX'];
		$RegionCoordonneeY 	= $data['RegionCoordonneeY'];

		$Combinaison = Array();
		$Combinaison[1] = Array($RegionCoordonneeX, $RegionCoordonneeY - 1);
		$Combinaison[2] = Array($RegionCoordonneeX - 1, $RegionCoordonneeY);
		$Combinaison[3] = Array($RegionCoordonneeX + 1, $RegionCoordonneeY);
		$Combinaison[4] = Array($RegionCoordonneeX, $RegionCoordonneeY + 1);

		for ( $i = 1; $i <= 4; $i++ )
		{
			$x = $Combinaison[$i][0];
			$y = $Combinaison[$i][1];
			
			$Erreur .= "<br />X = " . $x . " - Y = " . $y;
			$sql2 = "SELECT r.RegionID, t.TerritoireID, t.TerritoireNom
				FROM Region r, Territoire t
				WHERE r.RegionPartie = " . $RegionPartie . "
					AND r.RegionCoordonneeX = " . $x . "
					AND r.RegionCoordonneeY = " . $y . "
					AND t.TerritoireID = r.RegionTerritoire";
			$req2 = mysql_query($sql2) or die('Erreur SQL #043<br />'.$sql2.'<br />'.mysql_error());
			if ( $data2 = mysql_fetch_array($req2) )
			{
				$TerritoireID = $data2['TerritoireID'];
				if ( !$TerritoireTrouve[$TerritoireID] )
				{
					$ListeTerritoire 	.= ( $Compteur == 0 ) ? "<a href=\"#" . $RegionTerritoireEnCours . "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>": ", <a href=\"#" . $TerritoireID . "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>";
					$Compteur++;
					$TerritoireTrouve[$TerritoireID] = TRUE;
				}
			}
		}
		$Resultat = $Compteur > 1 ? $Compteur . " territoires adjacents: " .$ListeTerritoire : $Compteur . " territoires adjacents: " .$ListeTerritoire;
	}
	return $Resultat;
}



/* Capturer un territoire 

Partie 		: ID de la partie
Placement 	: est on en phase de placement ? SI oui, TRUE. Si non, FALSE.
			  Cela détermine si l'on doit, ou non, vérifier le nombre de territoires contrôlés
Joueur		: ID du joueur
			  S'il est vide, on le cherche avec Partie et Etat
Etat		: ID de l'Etat du joueur.
			  S'il est vide, on le cherche avec Partie et Joueur
LieuID		: ID du lieu à capturer
isTerritoire: Est ce un territoire? Si TRUE, oui. Si FALSE, c'est une région
			  S'il s'agit d'une région, on détermine l'ID de son territoire
			  
Return 		: FALSE (Erreur) ou TRUE;

*/

function Production($Partie, $Etat)
{
	global $DureeTourProduction, $PopulationSoutenableParTerritoire;
	
	$Recherche 			= Attribut($Etat, "Etat", Array("EtatJoueur", "EtatPopulation", "EtatTerritoires", "EtatCroissance", "EtatDerniereProduction", "EtatFamine"));
	$DerniereProduction = $Recherche["EtatDerniereProduction"];
	$EtatFamine 		= $Recherche["EtatFamine"];
	$EtatPopulation 	= $Recherche["EtatPopulation"];
	$EtatTerritoires 	= $Recherche["EtatTerritoires"];
	$EtatCroissance 	= $Recherche["EtatCroissance"];
	
	$Coefficient 		= ( time() - $DerniereProduction ) / $DureeTourProduction;

	// On trouve les infos des terrritoires du joueur pour créer celles de l'Etat
	
	// Population que l'on peut nourrir
	$Multiplicateur = 1;
	$PopulationTotaleSoutenable = $EtatTerritoires * $PopulationSoutenableParTerritoire * $Multiplicateur;

	// Population qui va mourir maintenant
	$PopulationMorte = 0;
//	$Test = "Soutenable: " . $PopulationTotaleSoutenable . "<br />Pop Totale : " . $EtatPopulation;
//	Message($Partie, $Recherche["EtatJoueur"], "Test", $Test, 0, "", "noire", 5);
	
	if ( $EtatPopulation > $PopulationTotaleSoutenable )
	{
		// Il y a plus de population que de population soutenable = famine !
		// EtatFamine = le temps depuis lequel l'Etat est en famine
		
		$EtatFamine += time() - $DerniereProduction;
		
		$sql = "SELECT TerritoireID, TerritoireNom, TerritoirePopulation, TerritoireCroissance
			FROM Territoire
			WHERE TerritoireEtat = " . $Etat;
		$req 	= mysql_query($sql) or die('Erreur SQL #36!<br />'.$sql.'<br />'.mysql_error());
		while ($data 	= mysql_fetch_array($req) )
		{
			$PopulationLocaleSoutenable = $PopulationSoutenableParTerritoire * $Multiplicateur;
			$PopulationMourrante = 0;
			
			if ( $data['TerritoirePopulation'] > $PopulationLocaleSoutenable )
			{
				$PopulationMourrante = $data['TerritoirePopulation'] - $PopulationLocaleSoutenable;
				$EtatFamineLocale	= $EtatFamine > 600 ? 600 : $EtatFamine;
				
				$DureeCarre			= ( $EtatFamineLocale * $EtatFamineLocale ) / 3600;
				$PopulationMorte	= round( $Coefficient * ( ( $PopulationMourrante / 100) * ( 10 + $DureeCarre ) ) );
				$CroissanceLocale 	= ( $data['TerritoireCroissance'] > 0 ) ? $data['TerritoireCroissance'] - (2*$Coefficient) : 0 ;
				/* Formule
				La population qui doit mourir maintenant, chaque minute, est égale à
				(10 + X )% de la Population Mourrante (c'est à dire en surplus),
				où X = le nombre de minute depuis lequel il y a une famine, au carré
				On divise par 360 car, pour avoir des minutes au carré, il faut diviser par 60 au carré

				Premiere minute = 11% de la surpop décéde.
				2e minute de famine = 14%
				3e -> 19%, etc.
				6e -> 46%
				9e -> 91%
				10e -> Tout le monde y passe

				*/
		//		$Test = "<br />Coeff: " . $Coefficient . "<br />Mourrante: " . $PopulationMourrante . "<br />Sec: " . $EtatFamineLocale . "<br />Min2: " . $DureeCarre;
				$PopulationNouvelle = $data['TerritoirePopulation'] * ( $data['TerritoireCroissance'] / 100 );
				
				$PopulationDuTerritoire = $data['TerritoirePopulation'] - $PopulationMorte + $PopulationNouvelle;
				$sql = "UPDATE Territoire 
					SET TerritoirePopulation = " . $PopulationDuTerritoire . ", TerritoireCroissance = " . $CroissanceLocale . "
					WHERE TerritoireID = " . $data['TerritoireID'];
				mysql_query($sql) or die('Erreur SQL #053<br />'.$sql.'<br />'.mysql_error());
				
				$Texte = $PopulationMorte ? $PopulationMorte . " morts": "Pas de mort pour le moment...";
				Message($Partie, $Recherche["EtatJoueur"], "Famine au " . $data['TerritoireNom'], $Texte, 0, "", "noire", 15);
			}
			else
			{
				$PopulationMorte = 0;
				$PopulationNouvelle = $data['TerritoirePopulation'] * ( $data['TerritoireCroissance'] / 100 );

				$CroissanceLocale = $data['TerritoireCroissance'] + 0.05;
				$sql = "UPDATE Territoire 
					SET TerritoirePopulation = TerritoirePopulation + " . $PopulationNouvelle . ", TerritoireCroissance = TerritoireCroissance + (0.05*".$Coefficient.")
					WHERE TerritoireID = " . $data['TerritoireID'];
				mysql_query($sql) or die('Erreur SQL #054<br />'.$sql.'<br />'.mysql_error());
			}
			$CroissanceTotale += $CroissanceLocale;
			$PopulationTotale = $PopulationTotale + $PopulationNouvelle - $PopulationMorte;
		}
		//
		
		$CroissanceTotale = round(($CroissanceTotale / $NombreTerritoire) *$Coefficient, 2);
	}
	else
	{
		$EtatFamine = 0;
		// Croissance normale
		$sql = "UPDATE Territoire
			SET TerritoirePopulation = TerritoirePopulation + (TerritoirePopulation*TerritoireCroissance*".$Coefficient." / 100), TerritoireCroissance = TerritoireCroissance + (0.05*".$Coefficient.")
			WHERE TerritoireEtat = " . $Etat;
		mysql_query($sql) or die('Erreur SQL #055<br />'.$sql.'<br />'.mysql_error());
		$PopulationTotale	= ( $PopulationTotale * $Croissance / 100 );
	}
	
	$PopulationTotale = 0;
	$CroissanceTotale = 0;
	
	$sql = "SELECT TerritoirePopulation, TerritoireCroissance
		FROM Territoire
		WHERE TerritoireEtat = " . $Etat;
	$req 	= mysql_query($sql) or die('Erreur SQL #36!<br />'.$sql.'<br />'.mysql_error());
	while ($data 	= mysql_fetch_array($req) )
	{
		$PopulationTotale += $data['TerritoirePopulation'];
		$CroissanceTotale += $data['TerritoirePopulation'] * $data['TerritoireCroissance'];
	}
	$CroissanceTotaleFin = $CroissanceTotale / $PopulationTotale;
	
	// Mise à jour des infos de l'Etat + Production
	$sql = "UPDATE Etat 
		SET EtatPopulation = " . $PopulationTotale . ", EtatCroissance = " . $CroissanceTotaleFin . ", EtatFamine = " . $EtatFamine . ", EtatDerniereProduction = " . time() . ", EtatPointCivil = EtatPointCivil + ( (EtatPopulation*EtatPopulationCivil*".$Coefficient.") / 10000), EtatPointCommerce = EtatPointCommerce + ( (EtatPopulation*EtatPopulationCommerce*".$Coefficient.") / 10000), EtatPointMilitaire = EtatPointMilitaire + ( (EtatPopulation*EtatPopulationMilitaire*".$Coefficient.") / 10000), EtatPointReligion = EtatPointReligion + ( (EtatPopulation*EtatPopulationReligion*".$Coefficient.") / 10000), EtatOr = EtatOr + ( (EtatPopulation*EtatTaxe*".$Coefficient.") / 10000)
		WHERE EtatID = " . $Etat;
	mysql_query($sql) or die('Erreur SQL #049<br />'.$sql.'<br />'.mysql_error());

	// Message au joueur auant produit
	Message($Partie, $Recherche["EtatJoueur"], "Production", "Production effectuée", 0, "", "noire", 5);
}

function CaptureTerritoire($Partie, $Placement, $Joueur, $Etat, $LieuID, $isTerritoire)
{
	global $NombreTerritoireAuDebut;
	if ( $isTerritoire == FALSE )
	{
		// LieuID est une région, il faut trouver l'ID son territoire
		$TerritoireID 		= Attribut($LieuID, "Region", "RegionTerritoire");
		if ( $TerritoireID == FALSE )
		{
			Message($Partie, $Joueur, "Capture", "La capture a échoué car le territoire n'a pas été trouvé", 0, "", "noire", 15);
			return FALSE;
		}
	}
	else
	{
		$TerritoireID 	= $LieuID;
	}

	// Recherche des infos sur le territoire
	$Recherche 				= Attribut($TerritoireID, "Territoire", Array("TerritoireNom", "TerritoireJoueur", "TerritoirePopulation"));
	$TerritoireNom			= $Recherche['TerritoireNom'];
	$TerritoireJoueur		= $Recherche['TerritoireJoueur'];
	$TerritoirePopulation	= $Recherche['TerritoirePopulation'];

	if ( !$Joueur && !$Etat )
	{
		// Ni Joueur, ni Etat ne sont renseigné : Erreur
		Message($Partie, $Joueur, "Capture", "La capture a échoué car ni le joueur ni l'Etat ne sont spécifiés", 0, "", "noire", 15);
		return FALSE;
	}
	else if ( $Joueur && !$Etat )
	{
		$sql = "SELECT EtatID
			FROM Etat
			WHERE EtatJoueur = " . $Joueur . "
				AND EtatPartie = " . $Partie;
		$req 	= mysql_query($sql) or die('Erreur SQL #36!<br />'.$sql.'<br />'.mysql_error());
		$data 	= mysql_fetch_array($req);
		$Etat 	= $data['EtatID'];
	}
	else if ( !$Joueur && $Etat )
	{
		$Joueur 		= Attribut($Etat, "Etat", "EtatJoueur");
	}
	
	if ( $Placement == TRUE )
	{
		// On est en phase de placement.
		// On doit vérifier que le nombre de territoire max n'est pas atteint par le joueur
		// Et que le joueur ne capture pas un territoire déjà possédé.
		if ( $TerritoireJoueur )
		{
			// Le territoire est déjà possédé : erreur
			Message($Partie, $Joueur, "Capture", "La capture a échoué car le territoire est déjà possédé par un autre joueur", 0, "", "noire", 15);
			return FALSE;
		}
		
		// Combien le joueur a t'il de territoires ?		
		$EtatTerritoires 	= Attribut($Etat, "Etat", "EtatTerritoires");

		// $NombreTerritoireAuDebut
		if ( $EtatTerritoires >= $NombreTerritoireAuDebut )
		{
			// Le joueur possède plus de territoires qu'il ne peut en avoir au début : erreur
			$texte = "La capture a échoué car vous avez déjà <u>" . $NombreTerritoireAuDebut . "</u> territoires";
			Message($Partie, $Joueur, "Capture", $texte, 0, "", "noire", 15);
			return FALSE;
		}
	}

	$JoueurNom 		= Attribut($Joueur, "Joueur", "JoueurNom");
		
	// Le territoire est occupé
	if ( $TerritoireJoueur )
	{
		if ( $TerritoireJoueur == $Joueur )
		{
			// Message au Joueur qui possède déjà le territoire
			$Texte = "Le territoire " . $TerritoireNom . " vous appartient déjà";
			Message($Partie, $Joueur, "Capture", $Texte, 0, "", "noire", 5);
			return FALSE;
		}
		
		$TerritoireJoueurNom 	= Attribut($TerritoireJoueur, "Joueur", "JoueurNom");

		// Message à l'ancien propriétaire
		$Texte = "Votre territoire " . $TerritoireNom . " a été capturé par " . $JoueurNom;
		Message($Partie, $TerritoireJoueur, "Capture", $Texte, $Joueur, "", "noire", 15);
			
		// Message au Joueur, nouveau propriétaire
		$Texte = "Vous avez capturé le territoire " . $TerritoireNom . " de " . $TerritoireJoueurNom;
		Message($Partie, $Joueur, "Capture", $Texte, 0, "", "noire", 5);

		// Message public 
		$Texte = $JoueurNom . " a capturé le territoire " . $TerritoireNom . " de " . $TerritoireJoueurNom;
		$Exclus = $Joueur . ", " . $TerritoireJoueur;
		Message($Partie, 0, "Capture", $Texte, 0, $Exclus, "noire", 10);
			
		// L'ancien proprio perd un Etat
		$sql = "UPDATE Etat
			SET EtatTerritoires = EtatTerritoires - 1
				WHERE EtatJoueur = " . $TerritoireJoueur . "
					AND EtatPartie = " . $Partie;
		mysql_query($sql) or die('Erreur SQL #038<br />'.$sql.'<br />'.mysql_error());
	}
	else
	{
		// Message au Joueur, nouveau propriétaire
		$Texte = "Vous avez capturé le territoire " . $TerritoireNom;
		Message($Partie, $Joueur, "Capture", $Texte, 0, "", "noire", 5);
			
		// Message public
		$Texte = $JoueurNom . " a capturé le territoire " . $TerritoireNom;
		Message($Partie, 0, "Capture", $Texte, 0, $Joueur, "noire", 10);
	}


	// Mise à jour du territoire = changement d'Etat
	$sql = "UPDATE Etat
		SET EtatTerritoires = EtatTerritoires + 1, EtatPopulation = EtatPopulation + " . $TerritoirePopulation . "
			WHERE EtatID = " . $Etat;
	mysql_query($sql) or die('Erreur SQL #037<br />'.$sql.'<br />'.mysql_error());

	$sql = "UPDATE Territoire
		SET TerritoireEtat = " . $Etat . ", TerritoireJoueur = " . $Joueur . "
			WHERE TerritoireID = " . $TerritoireID;
	mysql_query($sql) or die('Erreur SQL #028<br />'.$sql.'<br />'.mysql_error());

	return TRUE;
}


/* Attribut permet de récupérer une ou plusieurs valeurs d'une table

ReferenceValeur : la valeur d'un champ primary key (ID) de la table. Ex: 5 = l'ID de la Partie, ou d'un Etat
Type			: la table dont il faut extraire une donnée
Attribut		: le ou les s champs dont il faut trouver la valeur
				  Cela peut être un tableau ou un champ unique
				  
Return 			: FALSE s'il y a une erreur ou que la valeur n'est pas trouvée
				  Un tableau si Attribut est un tableau
				  Une valeur unique si Attribut comprend un seul champ
*/

function Attribut($ReferenceValeur, $Type, $Attribut)
{
	$Requete = "";
	switch ( $Type )
	{
		case "Partie" :
			$Table 		= "Partie";
			$Reference 	= "PartieID";
		break;
		case "Territoire" :
			$Table 		= "Territoire";
			$Reference 	= "TerritoireID";
		break;
		case "Region" :
			$Table 		= "Region";
			$Reference 	= "RegionID";
		break;
		case "Joueur" :
			$Table 		= "Joueur";
			$Reference 	= "JoueurID";
		break;
		case "Etat" :
			$Table 		= "Etat";
			$Reference 	= "EtatID";
		break;
	}
	if ( is_array($Attribut) == FALSE )
	{
		$Requete = $Attribut;
	}
	else
	{
		for ($i = 0; $i < count($Attribut); $i++)
		{
			$Requete .= $Attribut[$i] . " AS " . $Attribut[$i];
			$Requete .= ( $i < count($Attribut) - 1 ) ? ', ' : '';
		}
	}		
	$sql = "SELECT " . $Requete . "
		FROM " . $Table . "
		WHERE " . $Reference . " = " . $ReferenceValeur;
	$req = mysql_query($sql) or die('Erreur SQL # 30!<br />'.$sql.'<br />'.mysql_error());
	if ($data = mysql_fetch_array($req))
	{
		if ( is_array($Attribut) == FALSE )
		{
			$resultat = $data[$Attribut];
		}
		else
		{
			for ($i = 0; $i < count($Attribut); $i++)
			{
				$NomAttribut	= $Attribut[$i];
				$resultat[$NomAttribut] = $data[$NomAttribut];
			}
		}
	}
	else
	{
		$resultat = FALSE;
	}
	mysql_free_result($req);
	return $resultat;
}


// A CHANGER
function transaction($Joueur, $Ressource, $Destinataire)
{
	$oui = FALSE;
	
// Ressource = un tableau
	if ( $Joueur != 0 )
	{
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$oui 				= TRUE;
			$RessourceOr		= $data['RessourceOr'];
			$RessourceBle 		= $data['RessourceBle'];
			$RessourceBois 		= $data['RessourceBois'];
			$RessourcePierre 	= $data['RessourcePierre'];
			$RessourceFer 		= $data['RessourceFer'];
		}
		if ( !$oui )
		{
			return FALSE;
		}

		mysql_free_result($req);
		// Le joueur a t'il assez pour payer?
	
		if ( 	$RessourceOr 		< $Or ||
				$RessourceBle	 	< $Ble ||
				$RessourceBois 		< $Bois ||
				$RessourcePierre 	< $Pierre ||
				$RessourceFer 		< $Fer )
		{
			return FALSE;
		}
		// Si oui, on soustrait
		$sql = "UPDATE joueur
				SET RessourceOr = RessourceOr - ". $Or . ", RessourceBle = RessourceBle - ". $Ble . ", RessourceBois = RessourceBois - ". $Bois . ", RessourcePierre = RessourcePierre - ". $Pierre . ", RessourceFer = RessourceFer - ". $Fer . "
					WHERE JoueurID = " . $Joueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
	}
	// Si il y a un destinataire, on lui ajoute.
	if ( $Destinataire != 0 )
	{
		$sql = "UPDATE joueur
				SET RessourceOr = RessourceOr + ". $Or . ", RessourceBle = RessourceBle + ". $Ble . ", RessourceBois = RessourceBois + ". $Bois . ", RessourcePierre = RessourcePierre + ". $Pierre . ", RessourceFer = RessourceFer + ". $Fer . "
					WHERE JoueurID = " . $Destinataire;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
	}
	mysql_close();
	
	return TRUE;
}


function technologieFille($technologie)
{
	global $technologies;
	$Filles = Array();
	
	// technologie = l'ID de la technologie
	for ( $j = 0 ; $j < count($technologies) ; $j++ )
	{
		$TechnoParent	= $technologies[$j]["Parent"];
		
		if ( $TechnoParent == $technologie )
		{
			$Nombre = count($Filles) + 1;
			$Filles[$Nombre] = $j;
		}
	}
	return $Filles;
}

function arbreTechnologies($technologieID, $technologieFilles, $technologieTexteArray, $unique)
{
	global $technologies;
	$texte = "";
	
	$texte = "<tr>";
	// Si la technologie a des filles
	if ( isset($technologieFilles[$technologieID]) && count($technologieFilles[$technologieID]) >= 1 )
	{
		$texte .= ( $unique == FALSE ) ? "<td>" : "<td style=\"border:none;\">" ;
		$texte .= $technologieTexteArray[$technologieID];
		$texte .= "</td>";
		$texte .= ( $unique == FALSE ) ? "<td>" : "<td style=\"border:none;\"><font size=\"1\"><b>>></b></font></td><td style=\"border:none;\">" ;
		$texte .= ( $unique == FALSE ) ? "<table>" : "<table style=\"border:none;\">" ;
		
		$uniqueNew = (count($technologieFilles[$technologieID]) > 1 ) ? FALSE : TRUE;
		for ( $j = 1 ; $j <= count($technologieFilles[$technologieID]) ; $j++ )
		{
			$FilleID= $technologieFilles[$technologieID][$j];			
			$texte	.= arbreTechnologies($FilleID, $technologieFilles, $technologieTexteArray, $uniqueNew);
		}
		$texte .= "</table>";
		$texte .= "</td>";

	}
	else
	{
		if ( $unique == TRUE )
		{
			$texte .= "<td height=\"20\" style=\"border:none;\">";
			$texte .= $technologieTexteArray[$technologieID];
			$texte .= "</td>";
		}
		else
		{
			$texte .= "<td height=\"20\">";
			$texte .= $technologieTexteArray[$technologieID];
			$texte .= "</td>";
		}
	}
	$texte .= "<tr>";
	return $texte;
}


?>