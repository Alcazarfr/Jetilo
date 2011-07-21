<?php

/*

 Fonctions.php Reprend toutes les fonctions utilisées
 
 connectMaBase() 	: Connexion à la Base de données
 Couleur()			: Colorer les données négatives ( <0 ) en rouge
 adjectif()			: 
 Agent() 			: Créer un agent
 Effet()			: Créer un effet
 ChercherEffet()	: Renvoie la valeur d'une variable soumise à un effet
 FormaterLien()		: Formater la fonction pour créer un agent ou un effet
 Message()			: Ajouter un message
 TerritoireAcces()	: Etudier la carte pour trouver la distance entre deux territoires ou ses voisins
 Production()		: Produire des points, faire évoluer la population
 CaptureTerritoire(): Capturer un territoire
 Transaction		: Réaliser une transaction
 Modal()			: Affichage des modales pour les actions
 Attribut()			: Retourner une ou plusieurs variables de la BDD
 Supprimer()		: Supprime une entrée dans la BDD
 print_debug()
 
*/

// 
// Connexion à la Base de données
//
function connectMaBase()
{
	global $MotDePasse;
	
    $base = mysql_connect('localhost', 'root', $MotDePasse);  
    mysql_select_db ('jet', $base) ;
}

// Si $var est négatif, on la colore en rouge
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


/* Créer un agent

AgentNom		: Nom de l'agent
AgentEtat		: Etat propriétaire de l'Agent
AgentEtatOrigine: Etat qui pense Ãªtre propriétaire de l'agent, qui, pour sa part, a retourné sa veste...
AgentStatut		: 1 = l'agent est en opération ;
				  0 = il attend ;
				  -1 = il est compromis, en prison dans le lieu précisié par AgentTerritoire.
AgentSecret		: L'agent est il secret (1) ou public (0) ?
AgentTerritoire	: Lieu oÃ¹ l'agent opÃ¨re, se repose ou est en prison
AgentCapaciteFurtivite	: Capacité à Ãªtre détecté par le contre-espionnage
AgentCapaciteVitesse	: Capacité à se déplacer et à réaliser plus vite ses actions
AgentCapaciteReussite	: Capacité à réussir une action ou à Ãªtre plus efficace
AgentType		: Type d'agent ("Agitateur", "Pretre", "Emissaire") ...

*/
function Agent($AgentNom, $AgentEtat, $AgentStatut, $AgentSecret, $AgentTerritoire, $AgentCapaciteFurtivite, $AgentCapaciteVitesse, $AgentCapaciteReussite, $AgentType)
{
	$sql = "INSERT INTO Agent (AgentNom, AgentEtat, AgentEtatOrigine, AgentStatut, AgentSecret, AgentTerritoire, AgentCapaciteFurtivite, AgentCapaciteVitesse, AgentCapaciteReussite, AgentType, AgentTime)
		VALUES ('".$AgentNom."', " . $AgentEtat . ", " . $AgentEtat . ", " . $AgentStatut . ", " . $AgentSecret . ", " . $AgentTerritoire . ", " . $AgentCapaciteFurtivite . ", " . $AgentCapaciteVitesse . ", " . $AgentCapaciteReussite . ", '" . $AgentType . "', " . time() . ")";
	mysql_query($sql) or die('Erreur SQL #57 Message<br />'.$sql.'<br />'.mysql_error());	

	return true;
}


/* Insérer une action

ActionID 			: ID de l'action, automatique
ActionType	 		: l'ID dans actions.xml
ActionSourceType	: Etat, territoire, agent...
ActionSourceID		: ID de l'Etat, du territoire ou de l'agent
ActionTimeDebut	: timestamp du début de l'action
ActionTimeFin		: timestamp de la fin de l'action

Return				: ID de l'action

*/
function Action($ActionType, $ActionSourceType, $ActionSourceID, $ActionTimeDebut, $ActionTimeFin)
{
	$sql = "INSERT INTO Action (ActionType, ActionSourceType, ActionSourceID, ActionTimeDebut, ActionTimeFin)
		VALUES ('" . $ActionType . "', '" . $ActionSourceType . "', " . $ActionSourceID . ", " . $ActionTimeDebut . ", " . $ActionTimeFin . ")";
	mysql_query($sql) or die('Erreur SQL #63 Message<br />'.$sql.'<br />'.mysql_error());	

	return mysql_insert_id();
}


/* Insérer une entrée dans la BDD (création d'une armée, d'un joueur...)

ActionID 			: ID de l'action, automatique
ActionType	 		: l'ID dans actions.xml
ActionSourceType	: Etat, territoire, agent...
ActionSourceID		: ID de l'Etat, du territoire ou de l'agent
ActionTimeDebut	: timestamp du début de l'action
ActionTimeFin		: timestamp de la fin de l'action

Return				: ID de l'action

*/
function Entree($EntreeTable, $EntreeInformations)
{
	$Entrees = "";
	$Valeurs = "";
	for ( $i = 0; $i < count($EntreeInformations); $i++ )
	{
		// Génération de la ligne "entrée"
		$Entrees .= ( $i == 0 ) ? $EntreeInformations[$i]["Entree"] : ", " . $EntreeInformations[$i]["Entree"];
		
		// Génération de la ligne "valeur"
		$Guillemet = is_numeric($EntreeInformations[$i]["Valeur"]) ? "" : "'";
		$Valeurs .= ( $i == 0 ) ? $Guillemet . $EntreeInformations[$i]["Valeur"] . $Guillemet : ", " . $Guillemet . $EntreeInformations[$i]["Valeur"] . $Guillemet;
	}
	
	$sql = "INSERT INTO " . $EntreeTable . " (" . $Entrees . ")
		VALUES (" . $Valeurs . ")";
	mysql_query($sql) or die('Erreur SQL #101 Entree<br />'.$sql.'<br />'.mysql_error());	

	return mysql_insert_id();
}


/* Insérer un effet : bonus ou malus sur un territoire, un Etat, un agent...

EffetID 		: ID de l'effet, automatique
EffetAction 	: Action qui provoque cet effet
EffetCibleType 	: ETAT -> Effet national (ex: Révolte -5%)
				  TERRITOIRE -> Effet local (ex : Croissance du territoire + 5%)
				  AGENT -> Furtivité de l'agent +5pts, Rapidité+30%, etc.
EffetCibleID	: ID de l'Etat, du territoire ou de l'agent
EffetTimeDebut	: timestamp du début de l'effet
EffetTimeFin	: timestamp de la fin de l'effet
EffetTable		: table surlaquelle il y a un effet
EffetVariable	: Variable impactée par l'effet (TerritoireCroissance, EtatPopulation)
EffetType		: Que se passera t'il? SUBSTITUTION (remplacement), ADDITION, SOUSTRACTION, DIVISION, MULTIPLICATION
EffetValeur		: Valeur (0.25 ; 16 ; -3.5)

*/
function Effet($EffetAction, $EffetCibleType, $EffetCibleID, $EffetTimeDebut, $EffetTimeFin, $EffetTable, $EffetVariable, $EffetType, $EffetValeur)
{
	$sql = "INSERT INTO Effet (EffetAction, EffetCibleType, EffetCibleID, EffetTimeDebut, EffetTimeFin, EffetTable, EffetVariable, EffetType, EffetValeur)
		VALUES (" . $EffetAction . ", '".$EffetCibleType."', " . $EffetCibleID . ", " . $EffetTimeDebut . ", " . $EffetTimeFin . ", '" . $EffetTable . "', '" . $EffetVariable . "', '" . $EffetType . "', " . $EffetValeur . ")";
	mysql_query($sql) or die('Erreur SQL #56 Message<br />'.$sql.'<br />'.mysql_error());	

	return mysql_insert_id();
}


/* Chercher un effet

SourceType		: TERRITOIRE, ETAT, AGENT...
SourceID		: ID de la source
Variable		: Variable qui subit, peut Ãªtre, un effet
Valeur			: Valeur originelle, sans effet, de la variable

*/
function ChercherEffet($SourceType, $SourceID, $SourceVariable, $SourceValeur)
{
	switch ( $SourceType )
	{
		case "TERRITOIRE" :
			$Table = "Territoire";
		break;
	}
	
	$Resultat = $SourceValeur;
	$sql = "SELECT *
		FROM Effet
		WHERE EffetCibleType = '" . $SourceType . "'
			AND EffetCibleID = " . $SourceID . "
			AND EffetTimeDebut <= " . time() . "
			AND EffetTimeFin >= " . time() . "
			AND EffetVariable = '" . $SourceVariable . "'
		ORDER BY EffetID ASC";
	$req = mysql_query($sql) or die('Erreur SQL #042<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req) )
	{
		switch ( $data['EffetType'] )
		{
			case "ADDITION" :
				$Resultat += $data['EffetValeur'];
			break;
			case "SOUSTRACTION" :
				$Resultat -= $data['EffetValeur'];
			break;
			case "MULTIPLICATION" :
				$Resultat *= $data['EffetValeur'];
			break;
			case "DIVISION" :
				$Resultat = round($Resultat / $data['EffetValeur']);
			break;
			case "SUBSTITUTION" :
				$Resultat = $data['EffetValeur'];
			break;
		}
	}
	return $Resultat;
}



/* Créer le lien Ajax vers Partie.php, qui appellera la fonction Effet ou Agent

Utilité : générer le lien pour créer un agent ou un effet est long. D'oÃ¹ cette fonction.
1. On remplit le tableau ci-dessous
2. On


Exemple : Création d'un agent puis d'un effet
	  	  Dans 1 minute et pour 10 minutes,
		  un agent (ID=33) réduit la croissance d'un territoire (ID=6) de 10%
		  (=multiplication par 0.9)

1. Remplissable du tableau
$ModelEffet	= Array(
	"CibleType" => "TERRITOIRE",
	"CibleID" => 6,
	"SourceType" => "AGENT",
	"SourceID" => 33,
	"Nom" => "Reduction Croissance",
	"TimeDebut" => time() + 60,
	"TimeFin" => time() + 660,
	"Table" => "Territoire",
	"Variable" => "TerritoireCroissance",
	"Type" => "MULTIPLICATION",
	"Valeur" => 0.90
);

2. Appel de la fonction
$Lien = FormaterLien("Effet", $ModelEffet);

3. On insÃ¨re le lien

*/
function FormaterLien($Mode, $Tableau)
{
	$Lien = $Mode . "('Bidon'";
	
	foreach ($Tableau as $Cle => $Valeur)
	{
		if ( is_numeric($Valeur) )
		{
			$Lien .= ", " . $Valeur;
		}
//		else if ( is_array($Valeur) )
//		{
//			$Lien .= ", " . serialize_array($Valeur);
//		}
		else
		{
			$Lien .= ", '" . $Valeur . "'";
		}
	}
	
	$Lien .= ")";
	return $Lien;
}

/* Trouver une route entre deux territoires ou les territoires voisins

TerritoireOrigine 		: ID du territoire
TerritoireDestination	: ID du territoire oÃ¹ l'on veut aller
			  
Return 	: 	Array(TerritoireVoisins1, TerritoireVoisinsN) si TerritoireDestination = fasle
			Array(AccÃ¨sDirectPossible[TRUE;FALSE], TempsPourYAller)

*/

function TerritoireAcces($TerritoireOrigine, $TerritoireDestination=false)
{
	$nombre = 0;
	$ListeTerritoire = "";
	$TerritoireTrouve = Array();
	$TerritoireTrouve[$TerritoireOrigine] = TRUE;
	$Compteur = 0;

	$Erreur = "";

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
				if ( !@$TerritoireTrouve[$TerritoireID] )
				{
					$ListeTerritoire 	.= ( $Compteur == 0 ) ? "<a href=\"#" . $TerritoireID. "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>": ", <a href=\"#" . $TerritoireID . "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>";
					$Compteur++;
					$TerritoireTrouve[$TerritoireID] = TRUE;
				}
			}
		}
		$Resultat = $Compteur > 1 ? $Compteur . " territoires adjacents: " .$ListeTerritoire : $Compteur . " territoires adjacents: " .$ListeTerritoire;
	}
	return $Resultat;
}

/* Gestion de la production et de la croissance des territoires, par Etat */

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
	$Coefficient		= $Coefficient > 5 ? 5 : $Coefficient;
	
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
		
		$CroissanceTotale = 0;	
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
				$CroissanceLocale	= ChercherEffet("TERRITOIRE", $data['TerritoireID'], "TerritoireCroissance", $data['TerritoireCroissance']);
				$CroissanceLocale 	= ( $CroissanceLocale > 0 ) ? $CroissanceLocale - (2*$Coefficient) : 0 ;
				$CroissanceLocale	= $CroissanceLocale < -0.2 ? -0.2 : $CroissanceLocale;
				
				/* Formule
				La population qui doit mourir maintenant, chaque minute, est égale à
				(10 + X )% de la Population Mourrante (c'est à dire en surplus),
				oÃ¹ X = le nombre de minute depuis lequel il y a une famine, au carré
				On divise par 360 car, pour avoir des minutes au carré, il faut diviser par 60 au carré

				Premiere minute = 11% de la surpop décéde.
				2e minute de famine = 14%
				3e -> 19%, etc.
				6e -> 46%
				9e -> 91%
				10e -> Tout le monde y passe

				*/
		//		$Test = "<br />Coeff: " . $Coefficient . "<br />Mourrante: " . $PopulationMourrante . "<br />Sec: " . $EtatFamineLocale . "<br />Min2: " . $DureeCarre;
				$PopulationNouvelle = $data['TerritoirePopulation'] * ( $CroissanceLocale / 100 );
				
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
			$EtatPopulation += $PopulationNouvelle - $PopulationMorte;
		}
		//
		
		$EtatCroissance = round(($CroissanceTotale / $EtatTerritoires) *$Coefficient, 2);
	}
	else
	{
		$EtatFamine = 0;
		// Croissance normale
		$sql = "UPDATE Territoire
			SET TerritoirePopulation = TerritoirePopulation + (TerritoirePopulation*TerritoireCroissance*".$Coefficient." / 100), TerritoireCroissance = TerritoireCroissance + (0.05*".$Coefficient.")
			WHERE TerritoireEtat = " . $Etat;
		mysql_query($sql) or die('Erreur SQL #055<br />'.$sql.'<br />'.mysql_error());
		$EtatPopulation	= ( $EtatPopulation * $EtatCroissance / 100 );
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
			// Le joueur possÃ¨de plus de territoires qu'il ne peut en avoir au début : erreur
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
			// Message au Joueur qui possÃ¨de déjà le territoire
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


/* Réaliser une Transaction (paiement, échange...)
Partie			: ID de la partie
Joueur			: ID du joueur
Etat			: ID de son Etat
Donnees			: Array(
					"TypeDePoint" => +/- X,
					"TypeDePoint2" => +/- X
				   );
Verification	: TRUE On ne fait que vérifier que la transaction est possible
					   et que le joueur a assez de ressources
				  FALSE On procÃ¨de à la transaction dans son ensemble
				
DestinataireEtat : ID de l'Etat destinataire, s'il y en a un

return TRUE ou FALSE;
*/
function Transaction($Partie, $Joueur, $Etat, $Donnees, $Verification, $DestinataireEtat=false)
{
	$Requete = "";
	$RequeteDestinataire = "";

	foreach ($Donnees as $TypeDePoint => $Valeur)
	{
		$Requete .= ( $Verification ) ? ", " . $TypeDePoint : ", " . $TypeDePoint . " = " . $TypeDePoint . " + " . $Valeur;
		$RequeteDestinataire .= ", " . $TypeDePoint . " = " . $TypeDePoint . " - " . $Valeur;
	}
	
	if ( $Verification )
	{
		$sql = "SELECT EtatJoueur " . $Requete . "
			FROM Etat
			WHERE EtatID = " . $Etat;
		$req = mysql_query($sql) or die('Erreur SQL # 61!<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		foreach ($Donnees as $TypeDePoint => $Valeur)
		{
			if ( $data[$TypeDePoint] + $Valeur < 0 )
			{
				// On arrive ici, c'est sur
				return false;
			}
		}
	}
	else
	{	
		$sql = "UPDATE Etat
			SET EtatJoueur = EtatJoueur" . $Requete . "
				WHERE EtatID = " . $Etat;
		mysql_query($sql) or die('Erreur SQL #059<br />'.$sql.'<br />'.mysql_error());

		if ( $DestinataireEtat )
		{
			$sql = "UPDATE Etat
				SET EtatJoueur = EtatJoueur" . $RequeteDestinataire . "
					WHERE EtatID = " . $DestinataireEtat;
			mysql_query($sql) or die('Erreur SQL #060<br />'.$sql.'<br />'.mysql_error());
		}
	}
	return true;
}


/* Attribut permet de récupérer une ou plusieurs valeurs d'une table

ReferenceValeur : la valeur d'un champ primary key (ID) de la table. Ex: 5 = l'ID de la Partie, ou d'un Etat
Type			: la table dont il faut extraire une donnée
Attribut		: le ou les s champs dont il faut trouver la valeur
				  Cela peut Ãªtre un tableau ou un champ unique
				  
Exemple 		: Nom d'un joueur
				  Attribut(ID_du_joueur, "Joueur", "JoueurNom");
				  
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


/* Modal crée les infobulles modales pour les actions

ActionType	: ID de l'action tel que référencé dans actions.xml
Information	: ID du territoire ou de l'Etat ou du Joueur utilisée par ajax/partie.php -> CréerAction

*/


function Modal($ActionType, $Information, $Etat, $Joueur)
{
	global $ACTIONS;
	
	$Modal = "<b>" . $ACTIONS->action[$ActionType]->nom . "</b>";
	$Modal .= "<br />";
	$Modal .= "<i>" . $ACTIONS->action[$ActionType]->description . "</i>";
	$Modal .= "<br /><br />";

	// Affichage des couts
	$Modal .= "<b>Couts</b> :<br />";
	$CoutsPossible = Array("EtatPointCivil", "EtatPointMilitaire", "EtatPointCommerce", "EtatPointReligion", "EtatOr");
	$CoutsNom = Array("Point Civil", "Point Militaire", "Point de Commerce", "Point de Religion", "Or");
	for ( $i = 0; $i < 5; $i++)
	{
		$CoutTeste = $CoutsPossible[$i];
		if ( isset($ACTIONS->action[$ActionType]->couts->$CoutTeste) )
		{
			$Modal .= "- " . $CoutsNom[$i] . ": " . $ACTIONS->action[$ActionType]->couts->$CoutTeste . "<br />";
		}
	}
	
	// Affichage des effets
	$Modal .= "<br /><b>Effets</b> :<br />";
	$Condition = TRUE;
	$i = 0;
	do
	{
		if ( isset($ACTIONS->action[$ActionType]->effets[$i]->nom) )
		{
			// Il y a un effet
			$EffetNumero = $i + 1;
			
			$Duree = $ACTIONS->action[$ActionType]->effets[$i]->duree > 0 ? $ACTIONS->action[$ActionType]->effets[$i]->duree . "sec" : "illimité";
			$Delai = $ACTIONS->action[$ActionType]->effets[$i]->delai > 0 ? $ACTIONS->action[$ActionType]->effets[$i]->delai . "sec" : "immédiat";
			
			$Modal .= $EffetNumero . ". " . $ACTIONS->action[$ActionType]->effets[$i]->nom . "<br />";
			$Modal .= "--- Délai : " . $Delai . "<br />";
			$Modal .= "--- Durée : " . $Duree . "<br />";
			
			$i++;
		}
		else
		{
			// Il n'y a plus d'effet
			$Condition = FALSE;
		}
	} while ( $Condition == TRUE );
	
	// Affichage des champs = A RECODER
	$Details = "";
	for ( $i = 0; $i < 10 ; $i++ )
	{
		if ( isset($ACTIONS->action[$ActionType]->modal[$i]->nom) )
		{
			$Nom 	= $ACTIONS->action[$ActionType]->modal[$i]->nom;
			$Texte 	= isset($ACTIONS->action[$ActionType]->modal[$i]->texte) ? $ACTIONS->action[$ActionType]->modal[$i]->texte : "";
			$Taille	= isset($ACTIONS->action[$ActionType]->modal[$i]->taille) ? $ACTIONS->action[$ActionType]->modal[$i]->taille : "";
			$Nom 	= $ACTIONS->action[$ActionType]->modal[$i]->nom;
			$Valeur = $ACTIONS->action[$ActionType]->modal[$i]->valeur;
	//		$Modal .= '<input type="text" size="'.$Taille.'" name="JoueurID" id="JoueurID" value="'.$Valeur.'">';

			switch ($ACTIONS->action[$ActionType]->modal[$i]->type)
			{
				case "text" :
					$Modal .= $Texte . " : ";
					$Modal .= '<input type="text" size="'.$Taille.'" name="' . $Nom . '" id="' . $Nom . '" value="'.$Valeur.'">';
					$Modal .= "<br />";
				break;
				case "hidden" :
					$Valeur	= RemplacerValeur($Valeur, $Etat, $Joueur, $Information);
					$Modal .= '<input type="hidden" name="' . $Nom . '" id="' . $Nom . '" value="'.$Valeur.'">';
				break;
			}

			$Details .= ( $i == 0 ) ? '\''.$Nom.':\'+document.getElementById(\''.$Nom.'\').value' : '+\'='.$Nom.':\'+document.getElementById(\''.$Nom.'\').value';
		}
		else
		{
			break;
		}
	}
	$Modal .= '<hr /><br /><button type="actioncreer" id="actioncreer" class="actioncreer" onClick="ActionCreer(\''.$ActionType.'\', 1, '.$Information.', ' . $Details . ')">Lancer l\'action (button)</button>';
  	$Modal .= '<input type="submit" value="Lancer l\'action (submit)" onClick="ActionCreer(\''.$ActionType.'\', 1, '.$Information.', ' . $Details . ')">';

	return $Modal;
}

function RemplacerValeur($ValeurCherchee, $Etat, $Joueur, $Territoire)
{
	switch ( $ValeurCherchee )
	{
		case "EtatID" :
			return $Etat;
		break;
		case "JoueurID" :
			return $Joueur;
		break;
		case "TerritoireID" :
			return $Territoire;
		break;
	}
}

/* Supprimer permet de supprimer une ou plusieurs entrées de la BDD

Table 		: Table dans laquelle on va supprimer la ou les entrées
Variable	: Variable de contrôle

Exemple: Table = "Agent"
		 Variable = "AgentID = 5"
=> Suppression de l'entrée, dans la table Agent, à celle correspondant à un AgentID = 5

Exemple : Table = Effet
		  Variable = "EffetTimeFin < time()"
=> Suppression de toutes les effets périmés

Exemple : Table = Action
		  Variable = "ActionSourceID = 3 AND ActionSourceType = 'Agent'"
=> Suppression des toutes les actions de l'agent 3

*/

function Supprimer($Table, $Donnees)
{
	$sql = "DELETE FROM " . $Table . "
		WHERE " . $Donnees;
	$req = mysql_query($sql) or die('Erreur SQL #64<br />'.$sql.'<br />'.mysql_error());
}


function print_debug_code($code)
{
	global $PARAMETRES;
	global $ACTIONS;
	
	print_debug($code, eval('return '.$code.';'));
}

function print_debug($description, $variable){		print("<pre style='background-color:white; margin-bottom:0px;'>$description</pre>\r\n");	print("<pre style='background-color:black; margin-top:0px; color:white;'>\r\n");
	print_r($variable);
	print("</pre>\r\n");
}


?>