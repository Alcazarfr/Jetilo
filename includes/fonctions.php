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
	$return = ( $var < 0 ) ? "<font color=\"FF0000\">" . $var . "</font>" :"<font color=\"green\">" . $var . "</font>";
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
function ActionInserer($ActionType, $ActionSourceType, $ActionSourceID, $ActionCibleID, $ActionTimeDebut, $ActionTimeFin, $ActionDetails, $ActionPartie, $ActionJoueur, $ActionEtat)
{
	$sql = "SELECT *
		FROM Action
		WHERE ActionType = '" . $ActionType . "'
		AND ActionSourceType = '" . $ActionSourceType . "'
		AND ActionSourceID = " . $ActionSourceID . "
		AND ActionCibleID = " . $ActionCibleID . "
		AND ActionDetails = '" . $ActionDetails . "'
		AND ActionStatut = 0
		AND ActionEtat = " . $ActionEtat;
	$req = mysql_query($sql) or die('Erreur SQL #051<br />'.$sql.'<br />'.mysql_error());
	if ( $data = mysql_fetch_array($req) )
	{
		return false;
	}

	$sql = "INSERT INTO Action (ActionType, ActionSourceType, ActionSourceID, ActionTimeDebut, ActionTimeFin, ActionCibleID, ActionDetails, ActionPartie, ActionJoueur, ActionEtat)
		VALUES ('" . $ActionType . "', '" . $ActionSourceType . "', " . $ActionSourceID . ", " . $ActionTimeDebut . ", " . $ActionTimeFin . ", '" . $ActionCibleID . "', '" . $ActionDetails . "', " . $ActionPartie. ", " . $ActionJoueur . ", " . $ActionEtat . ")";
	mysql_query($sql) or die('Erreur SQL #63 Message<br />'.$sql.'<br />'.mysql_error());	

	return mysql_insert_id();
}

function FormaterDetails($Details)
{
	$Champs = explode("=", $Details);
	$EntreeInformations = Array();
		
	for ( $j = 0 ; $j < count($Champs) ; $j++ )
	{
		// On récupère le nom du champ [0] puis sa valeur [1]
		$Explode = explode(":", $Champs[$j]);
		$Entree = $Explode[0];
		$Valeur = $Explode[1];
					
		$EntreeInformations[$j] = Array(
			"Entree" => $Entree,
			"Valeur" => $Valeur
		);
	}
	return $EntreeInformations;
}

function CoutSpecial($Type, $Details)
{
	$Cout = Array();
	
	switch ( $Type )
	{
		case "creer-agent" :
			
		break;
	}
	return $Cout;
}


function Action($ActionType, $ActionSourceID, $ActionCibleID, $Details, $Partie, $Joueur, $Etat)
{
	global $ACTIONS;
	
	$ActionCibleType 	= strtoupper($ACTIONS->action[$ActionType]->type_cible);
	$ActionSourceType 	= strtoupper($ACTIONS->action[$ActionType]->type_source);

	if ( isset($ACTIONS->action[$ActionType]->couts->Special) )
	{
		$Cout = CoutSpecial($ActionCibleType, FormaterDetails($Details));
	}
	else
	{
		$TableauDesNomsDesCouts	= Array("EtatPointCivil", "EtatPointCommerce", "EtatPointMilitaire", "EtatPointReligion", "EtatOr"); 
		$Cout = Array();
		
		for ( $i = 0; $i < count($TableauDesNomsDesCouts); $i++ )
		{
			$CoutPotentiel = $TableauDesNomsDesCouts[$i];
			if ( isset($ACTIONS->action[$ActionType]->couts->$CoutPotentiel))
			{
				$Cout[$CoutPotentiel] = $ACTIONS->action[$ActionType]->couts->$CoutPotentiel;
			}
		}
	}

	// Cela coute t'il de l'argent ?
	if ( is_array($Cout) )
	{
		// Si oui, On vérifie que le Joueur a les ressources suffisantes
		if ( !Transaction($Partie, $Etat, $Cout, true) )
		{
			Message($Partie, $Joueur, "Echec", "Pas assez de ressource", 0, "", "noire", 10);
			break;
		}
	}
	

	// Création de l'action
	$ActionNom		= $ACTIONS->action[$ActionType]->nom;
	$ActionDelai	= $ACTIONS->action[$ActionType]->delai;
	$ActionDuree	= $ACTIONS->action[$ActionType]->duree; 
	$ActionDebut	= time() + $ActionDelai;
	$ActionFin		= ( $ActionDuree == "illimité" OR $ActionDuree == 0 ) ? 0 : $ActionDebut + $ActionDuree;

	$ActionID	= ActionInserer($ActionType, $ActionSourceType, $ActionSourceID, $ActionCibleID, $ActionDebut, $ActionFin, $Details, $Partie, $Joueur, $Etat);

	// On verifie que l'action a bien été crée
	if ( is_numeric($ActionID) == false )
	{
		// La création de l'action a échoué : on annule tout
		Message($Partie, $Joueur, "Echec", "La création de laction a échoué"  . $CibleID, 0, "", "noire", 10);
		break;
	}
	
	if ( $ActionTimeDebut <= time() )
	{
		// On génère les effets
		$Resultat = ActionProduireEffets($Partie, $Etat, $Joueur, $ActionType, $Details, $ActionSourceID, $ActionCibleID, $ActionID);

		// On réalise l'effet s'il n'y a pas d'erreur
		if ( $Resultat )
		{
			if ( is_array($Cout) )
			{
				// On transmet les ressources au joueur
				$Transaction = Transaction($Partie, $Etat, $Cout, false);
				
				Entretien($ActionType, $Etat, "Ajouter");
			}
			Message($Partie, $Joueur, "Nouvelle Action ", $ActionNom, 0, "", "noire", 10);
		}
		else
		{
			Message($Partie, $Joueur, "Action", "Erreur dans la création de cette action", 0, "", "noire", 10);
		}
	}
	else
	{
		Message($Partie, $Joueur, "Action en cours", $ActionNom, 0, "", "noire", 10);
	}
}

function Entretien($ActionType, $Etat, $Mode)
{
	global $ACTIONS;

	$TableauDesNomsDesEntretiens	= Array("EtatEntretienCivil", "EtatEntretienCommerce", "EtatEntretienMilitaire", "EtatEntretienReligion", "EtatEntretienOr");
	$Entretien = "";

	for ( $i = 0; $i < count($TableauDesNomsDesEntretiens); $i++ )
	{
		$EntretienPotentiel = $TableauDesNomsDesEntretiens[$i];
		if ( isset($ACTIONS->action[$ActionType]->entretien->$EntretienPotentiel))
		{
			$Operation = ( $Mode == "Ajouter" ) ? " + " : " - ";
			$Entretien .= ( $Entretien == "" ) ? $EntretienPotentiel . " = " . $EntretienPotentiel . $Operation . $ACTIONS->action[$ActionType]->entretien->$EntretienPotentiel : ", " . $EntretienPotentiel . " = " . $EntretienPotentiel . $Operation . $ACTIONS->action[$ActionType]->entretien->$EntretienPotentiel;
		}
	}
	if ( $Entretien != "" )
	{
		UpdateTable("Etat", "EtatID = " . $Etat, $Entretien);
	}

}


function ActionProduireEffets($Partie, $Etat, $Joueur, $ActionType, $Details, $ActionSourceID, $ActionCibleID, $ActionID)
{
	global $ACTIONS;
	
	$ActionCibleType 	= strtoupper($ACTIONS->action[$ActionType]->type_cible);
	$ActionSourceType 	= strtoupper($ACTIONS->action[$ActionType]->type_source);

	$Erreur = false;
		
	// On fracture champ par champs	
	if ( $Details )
	{
		$EntreeInformations = FormaterDetails($Details);
	}
	$NbEffet = 0;
	$SiErreur = "";
		
	// On vérifie les conditions...
	if ( isset($ACTIONS->action[$ActionType]->conditions[0]->nom) )
	{
		for ( $i = 0 ; $i < count($ACTIONS->action[$ActionType]->conditions) ; $i ++ )
		{
			$Condition = $ACTIONS->action[$ActionType]->conditions[$i]->nom;
			switch ( $Condition )
			{
				case "PasEnCombat" :
					$sql = "SELECT CombattantID
						FROM Combattant
						WHERE CombattantID = " . $ActionCibleID;
					$req 	= mysql_query($sql) or die('Erreur SQL #136!<br />'.$sql.'<br />'.mysql_error());
					if ( $data 	= mysql_fetch_array($req) )
					{
						// L'armée ciblée combat...
						$Erreur = true;
						Message($Partie, $Joueur, "Erreur", "L'armée à déplacer est en train de combattre. Vous ne pouvez pas la dépasser", 0, "", "noire", 10);
						exit;
					}
				break;
				case "PasDeCombatIci" :
					$sql = "SELECT BatailleID
						FROM Bataille
						WHERE BatailleTerritoire = " . $EntreeInformations[1]['Valeur'];
					$req 	= mysql_query($sql) or die('Erreur SQL #136!<br />'.$sql.'<br />'.mysql_error());
					if ( $data 	= mysql_fetch_array($req) )
					{
						// Le territoire est déjà en bataille
						$Erreur = true;
						Message($Partie, $Joueur, "Erreur", "Ce territoire connait déjà une bataille. Vous ne pouvez en créer une seconde, mais vous pouvez rejoindre cette bataille.", 0, "", "noire", 10);
						exit;
					}
				break;
			}
		}
	}
		
	for ( $i = 0 ; $i < count($ACTIONS->action[$ActionType]->effets) ; $i ++ )
	{
		$NbEffet++;
		// On vérifie s'il y a un effet
		if ( isset($ACTIONS->action[$ActionType]->effets[$i]->nom) )
		{
			$EffetTable 	= $ACTIONS->action[$ActionType]->effets[$i]->table_concernee;
			// Majuscule
			switch ( strtoupper($ACTIONS->action[$ActionType]->effets[$i]->type) )
			{
				case "DELETE" :
				case "SUPPRIMER" :
					$Variable 	= $EntreeInformations[$i]["Entree"];
					$Valeur 	= $EntreeInformations[$i]["Valeur"];
					$Donnees	= $Variable . "=" . $Valeur;
					Supprimer($EffetTable, $Donnees);
					// On annule l'entretien s'il y en a un
					if ( isset($ACTIONS->action[$ActionType]->effets[$i]->inverse) )
					{
						Entretien($ACTIONS->action[$ActionType]->effets[$i]->inverse, $Etat, "Supprimer");
					}
					
				break;
					
				case "UPDATE" :
					// On met à jour la variable directement
					$EffetVariable 	= $ACTIONS->action[$ActionType]->effets[$i]->variable_concernee;
					$EffetType 		= strtoupper($ACTIONS->action[$ActionType]->effets[$i]->type_effet);
					$EffetValeur 	= isset($ACTIONS->action[$ActionType]->effets[$i]->valeur) ? $ACTIONS->action[$ActionType]->effets[$i]->valeur : 0 ;

					if ( isset($ACTIONS->action[$ActionType]->effets[$i]->champ) )
					{
						// Si cet effet a sa valeur qui dépend d'un input (champ)...
							
						for ( $j = 0 ; $j < count($EntreeInformations) ; $j++ )
						{
							$Entre = $EntreeInformations[$j]["Entree"];
							if ( $EntreeInformations[$j]["Entree"] == $ACTIONS->action[$ActionType]->effets[$i]->champ )
							{
								// La valeur de l'effet prend la valeur renseigner dans le champ
								$EffetValeur = $EntreeInformations[$j]["Valeur"];
								break;
							}
						}
					}
					switch ( strtoupper($EffetType) )
					{
						case "SUBSTITUTION" :
							$ValeurTexte = $EffetValeur;
						break;
						case "ADDITION" :
							$ValeurTexte = $EffetVariable . " + " . $EffetValeur;
						break;
						case "SOUSTRACTION" :
							$ValeurTexte = $EffetVariable . " - " . $EffetValeur;
						break;
						case "MULTIPLICATION" :
							$ValeurTexte = $EffetVariable . " * " . $EffetValeur;
						break;
					}
					$VariableDeControle = $EffetTable . "ID";
					$sql = "UPDATE " . $EffetTable . " 
						SET " . $EffetVariable . " = " . $ValeurTexte . "
						WHERE " . $VariableDeControle . " = " . $ActionCibleID;
					mysql_query($sql) or die('Erreur SQL #0102<br />'.$sql.'<br />'.mysql_error());
				break;
			
				case "ENTREE" :
					// On ajoute une entrée dans la BDD (une ligne)
		
					if ( $Details )
					{
						for ( $k = 0; $k < count($ACTIONS->action[$ActionType]->modal); $k ++ )
						{
							if ( isset($ACTIONS->action[$ActionType]->modal[$k]->verifier) )
							{
								// On doit vérifier que cette valeur n'existe pas dans la BDD

								// 1. Quelles est cette valeur en fait ?
								for ( $j = 0 ; $j < count($EntreeInformations) ; $j++ )
								{
									$Entre = $EntreeInformations[$j]["Entree"];
									if ( $EntreeInformations[$j]["Entree"] == $ACTIONS->action[$ActionType]->modal[$k]->nom )
									{
										// On a trouvé la valeur renseignée par le joueur dans le champ correspondant
										$ValeurRenseignee = $EntreeInformations[$j]["Valeur"];
										break;
									}
								}
						
								// 2. On cherche si la valeur existe dans la BDD

								$sql = "SELECT *
									FROM " . $EffetTable . "
									WHERE " . $Entre . " = '" . $ValeurRenseignee . "'";
								$req = mysql_query($sql) or die('Erreur SQL # 101<br />'.$sql.'<br />'.mysql_error());
								if ($data = mysql_fetch_array($req))
								{
									// La valeur existe déjà
									$Erreur 	= true;
									Message($Partie, $Joueur, "Erreur", "La valeur renseignée pour " . $Entre . " existe déjà", 0, "", "noire", 10);
									return false;
								}
							}
						}
					}
					$EntreeID = "";
					if ( $Erreur == false )
					{
						$EntreeID 		= Entree($EffetTable, $EntreeInformations);
					}
					// S'il y a une erreur, il faut supprimer les entrées déjà ajoutées à la BDD
					if ( is_numeric($EntreeID) == false )
					{
						return false;
					}
				break;
					
				case "INFLUENCE" :
					// On crée un effet qui se superpose à la variable qu'il influence
							
					$EffetTimeDebut = $ACTIONS->action[$ActionType]->effets[$i]->delai + time();
					$EffetTimeFin 	= ( $ACTIONS->action[$ActionType]->effets[$i]->duree == "illimité" ) ? 2311182703 : $ACTIONS->action[$ActionType]->effets[$i]->duree + $EffetTimeDebut;
					$EffetVariable 	= $ACTIONS->action[$ActionType]->effets[$i]->variable_concernee;
					$EffetType 		= strtoupper($ACTIONS->action[$ActionType]->effets[$i]->type_effet);
					$EffetValeur 	= isset($ACTIONS->action[$ActionType]->effets[$i]->valeur) ? $ACTIONS->action[$ActionType]->effets[$i]->valeur : 0 ;
				
					if ( isset($ACTIONS->action[$ActionType]->effets[$i]->champ) )
					{
						// Si cet effet est régi par un input provenant d'un champ à renseigner
						for ( $j = 0 ; $j < count($EntreeInformations) ; $j++ )
						{
							$Entre = $EntreeInformations[$j]["Entree"];
							if ( $EntreeInformations[$j]["Entree"] == $ACTIONS->action[$ActionType]->effets[$i]->champ )
							{
								// La valeur de l'effet prend la valeur renseigner dans le champ
								$EffetValeur = $EntreeInformations[$j]["Valeur"];
								break;
							}
						}
					}
					$EffetID 		= Effet($ActionID, $ActionCibleType, $ActionCibleID, $EffetTimeDebut, $EffetTimeFin, $EffetTable, $EffetVariable, $EffetType, $EffetValeur);

					$SiErreur		.= ( is_numeric($EffetID) == true ) ? ", " . $EffetID : "";
				
					// Si erreur, On annule les précédents effets et on supprime l'action
					if ( is_numeric($EffetID) == false )
					{
						$Erreur = true;
						Supprimer("Action", "Action = " . $ActionID);
						Supprimer("Effet", "EffetID IN(0" . $SiErreur . ")");
						Supprimer("Effet", "EffetID IN(0" . $SiErreur . ")");	
						return false;
					}
					
				break;
				
				// fin du Switch
			}
		}
		else
		{
			break;
		}
	}
	$sql = "UPDATE Action
		SET ActionStatut = 1
		WHERE ActionID = " . $ActionID;
	mysql_query($sql) or die('Erreur SQL #0102<br />'.$sql.'<br />'.mysql_error());

	return true;
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
Format 					: Faut il afficher un select, une liste ?
Return 	: 	Array(TerritoireVoisins1, TerritoireVoisinsN) si TerritoireDestination = fasle
			Array(AccÃ¨sDirectPossible[TRUE;FALSE], TempsPourYAller)

*/

function TerritoireAcces($TerritoireOrigine, $Format, $TerritoireDestination=false)
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
					if ( $Format == "Liste" )
					{
					
						$ListeTerritoire 	.= ( $Compteur == 0 ) ? "<a href=\"#" . $TerritoireID. "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>": ", <a href=\"#" . $TerritoireID . "\" onClick=\"TerritoireInformations(".$TerritoireID . ");\">" . $data2['TerritoireNom'] . "</a>";
					}
					else if ( $Format = "Select" )
					{
						$ListeTerritoire 	.= "<option id='" . $TerritoireID . "' name='" . $TerritoireID . "'>" . $data2['TerritoireNom'] . "</option>";
					}
					$Compteur++;
					$TerritoireTrouve[$TerritoireID] = TRUE;
				}
			}
		}
		if ( $Format == "Liste" )
		{
			$Resultat = $Compteur > 1 ? $Compteur . " territoires adjacents: " .$ListeTerritoire : $Compteur . " territoires adjacents: " .$ListeTerritoire;
		}
		else if ( $Format == "Select" )
		{
			$Resultat = "<select name='TerritoiresVoisinsArmee' id='TerritoiresVoisinsArmee'><option id='0'>Sélectionner un territoire</option>" .$ListeTerritoire . "</select>";
		}
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
		SET EtatPopulation = " . $PopulationTotale . ", EtatCroissance = " . $CroissanceTotaleFin . ", EtatFamine = " . $EtatFamine . ", EtatDerniereProduction = " . time() . ", EtatPointCivil = EtatPointCivil - EtatEntretienCivil + ( (EtatPopulation*EtatPopulationCivil*".$Coefficient.") / 10000), EtatPointCommerce = EtatPointCommerce - EtatEntretienCommerce + ( (EtatPopulation*EtatPopulationCommerce*".$Coefficient.") / 10000), EtatPointMilitaire = EtatPointMilitaire - EtatEntretienMilitaire + ( (EtatPopulation*EtatPopulationMilitaire*".$Coefficient.") / 10000), EtatPointReligion = EtatPointReligion - EtatEntretienReligion + ( (EtatPopulation*EtatPopulationReligion*".$Coefficient.") / 10000), EtatOr = EtatOr + ( (EtatPopulation*EtatTaxe*".$Coefficient.") / 10000)
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
function Transaction($Partie, $Etat, $Donnees, $Verification, $DestinataireEtat=false)
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
	$Table = $Type;
	$Reference = $Table . "ID";
	
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

function DeterminerVariationTaux($Offre, $Demande)
{
	$Difference = abs($Offre-$Demande);
	if ( $Offre <= 3 OR $Demande <= 3 )
	{
		// Si l'offre ou la demande est faible, pas de variation de prix
		return 0;
	}
	else if ( $Difference <= 2 )
	{
		// Si l'offre et la demande sont proche (+/-5), pas de variation
		return 0;
	}
	
	// Les variations sont étalées sur 18 tours.
	// On divise par 25 pour prendre en compte la suite géométrique que compose les variations chaque minute.
	if ( $Offre > $Demande )
	{
		$DifferencePourcent = $Difference / $Offre;
		$VariationPrix = round(($DifferencePourcent * -1) / 25, 2);
	}
	else if ( $Offre < $Demande )
	{
		// La demande est supérieure à l'offre
		//
		$DifferencePourcent = $Difference / $Demande;
		$VariationPrix = round($DifferencePourcent / 25, 2);
	}
	else
	{
		$VariationPrix = 0;
	}
	return $VariationPrix;
}

function CommerceAllocation($Offrant, $Demandeur, $Offre, $Demande)
{
	$Resultat = Array();
	// Offre et demande sont bien envoyé
//	Message(1, 0, "OFFRE ET DEMANDE", $Offre . " / " . $Demande, 0, "", "noire", 10);			

	if ( $Demande >= $Offre && $Demande > 0 && $Offre > 0 )
	{
		if ( count($Offrant) >= 1 )
		{
			for ( $i = 0; $i < count($Offrant); $i++)
			{
				$Etat 	= $Offrant[$i]["Etat"];
				$Valeur = $Offrant[$i]["Valeur"];
				$Resultat[$Etat]["Exportation"] = $Valeur;
			}
		}

		if ( count($Demandeur) >= 1 )
		{
			for ( $i = 0; $i < count($Demandeur); $i++)
			{
				$Etat 	= $Demandeur[$i]["Etat"];
				$Valeur = $Demandeur[$i]["Valeur"];
				$Proportion = round($Valeur / $Demande, 2);
				$Resultat[$Etat]["Importation"] = round($Offre * $Proportion, 2);
			}
		}
	}
	else if ( $Demande < $Offre && $Demande > 0  && $Offre > 0 )
	{
		if ( count($Offrant) >= 1 )
		{
			for ( $i = 0; $i < count($Offrant); $i++)
			{
				$Etat 	= $Offrant[$i]["Etat"];
				$Valeur = $Offrant[$i]["Valeur"];
				$Proportion = round($Valeur / $Offre, 2);
				$Resultat[$Etat]["Exportation"] = round($Demande * $Proportion, 2);
			}
		}
		if ( count($Demandeur) >= 1 )
		{
			for ( $i = 0; $i < count($Demandeur); $i++)
			{
				$Etat 	= $Demandeur[$i]["Etat"];
				$Valeur = $Demandeur[$i]["Valeur"];
				$Resultat[$Etat]["Importation"] = $Valeur;
			}
		}
	}
	return $Resultat;
}

/* Modal crée les infobulles modales pour les actions

ActionType	: ID de l'action tel que référencé dans actions.xml
Information	: ID du territoire ou de l'Etat ou du Joueur utilisée par ajax/partie.php -> CréerAction

*/


function Modal($ActionType, $Information, $Etat, $Joueur, $AutresInformations)
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
		$CoutTexte = $CoutsPossible[$i];
		if ( isset($ACTIONS->action[$ActionType]->couts->$CoutTexte) )
		{
			$Modal .= "- " . $CoutsNom[$i] . ": " . $ACTIONS->action[$ActionType]->couts->$CoutTexte . "<br />";
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
	
	$Details = '';
	for ( $i = 0; $i < count($ACTIONS->action[$ActionType]->modal) ; $i++ )
	{
		if ( isset($ACTIONS->action[$ActionType]->modal[$i]->nom) )
		{
			$Nom 	= $ACTIONS->action[$ActionType]->modal[$i]->nom;
			$Texte 	= isset($ACTIONS->action[$ActionType]->modal[$i]->texte) ? $ACTIONS->action[$ActionType]->modal[$i]->texte : "";
			$Taille	= isset($ACTIONS->action[$ActionType]->modal[$i]->taille) ? $ACTIONS->action[$ActionType]->modal[$i]->taille : "";
			$Nom 	= $ACTIONS->action[$ActionType]->modal[$i]->nom;
			$Valeur = isset($ACTIONS->action[$ActionType]->modal[$i]->valeur) ? $ACTIONS->action[$ActionType]->modal[$i]->valeur : "";

			if ( isset($AutresInformations[$Valeur]) )
			{
				$Valeur = $AutresInformations[$Valeur];
			}
			switch ($ACTIONS->action[$ActionType]->modal[$i]->type)
			{
				case "text" :
					$Modal .= $Texte . " : ";
					$Modal .= '<input type="text" size="'.$Taille.'" name="' . $Nom . '" id="' . $Nom . '" value="'.$Valeur.'">';
					$Modal .= "<br />";
				break;
				case "special" :
					$Modal .= $Texte . " : ";
					$Modal .= ModalChampSpecial($Nom, $Information, $Etat, $Joueur);
					$Modal .= "<br />";
				break;
				case "hidden" :
					$Valeur	= RemplacerValeur($Valeur, $Etat, $Joueur, $Information);
					$Modal .= '<input type="hidden" name="' . $Nom . '" id="' . $Nom . '" value="'.$Valeur.'">';
				break;
			}
			// Ne pas changer : résolution d'un bug
			$Details .= ( $i == 0 ) ? '\''.$Nom.':\'+document.getElementById(\''.$Nom.'\').value' : '+\'='.$Nom.':\'+document.getElementById(\''.$Nom.'\').value';
		}
		else
		{
			break;
		}
	}
	
	// Résolution d'un bug :
	$Details = !$Details ? 0 : $Details;
	$Modal .= '<hr /><br /><button type="actioncreer" id="actioncreer" class="actioncreer" onClick="ActionCreer(\''.$ActionType.'\', 1, '.$Information.', ' . $Details . ')">Lancer l\'action (button)</button>';
//  $Modal .= '<input type="submit" value="Lancer l\'action (submit)" onClick="ActionCreer(\''.$ActionType.'\', 1, '.$Information.', ' . $Details . ')">';
//	$Modal = "<form>" . $Modal . "</form>";
	return $Modal;
}

function ModalChampSpecial($Type, $Infos, $Etat, $Joueur)
{
	global $ARMEES, $AGENTS;
	
	switch ( $Type )
	{
		case "AgentType":
			$Agents = Array("diplomate", "commando", "missionnaire", "escroc");
			$Champ = "<select name='" . $Type . "' id='" . $Type . "'>";
			for ( $i = 0; $i < count($Agents); $i++ )
			{
				$ID 	= $Agents[$i];
				$Champ .= "<option value='" . $ID . "'>" . $AGENTS->agent[$ID]->nom . "</option>";
			}
			$Champ .= "</select>";
		break;

		case "TimeProchaineAttaque":
			$Champ 			= "<input type='hidden' name='" . $Type . "' id='" . $Type . "' value='" . time() . "'>";
		break;
		case "BatailleTerritoire":
			$TerritoireID	=	Attribut($Infos, "Armee", "ArmeeLieu");
			$TerritoireNom	=	Attribut($TerritoireID, "Territoire", "TerritoireNom");
			$Champ 			= "<select name='" . $Type . "' id='" . $Type . "'>
			<option value='" . $TerritoireID . "'>" . $TerritoireNom . "</option>
			</select>";
		break;
		case "BatailleDefenseur":
			$TerritoireID	=	Attribut($Infos, "Armee", "ArmeeLieu");
			$EtatID			=	Attribut($TerritoireID, "Territoire", "TerritoireEtat");
			$Champ 			= "<input type='hidden' name='" . $Type . "' id='" . $Type . "' value='" . $EtatID . "'>";
		break;
		case "ArmeeNombre":
			$Champ = "<select name='" . $Type . "' id='" . $Type . "'>
			<option value='100'>100</option>
			<option value='150'>150</option>
			<option value='200'>200</option>
			</select>";
		break;
		case "ArmeeTaille":
			$Champ = "<select name='" . $Type . "' id='" . $Type . "'>
			<option value='100'>100</option>
			<option value='150'>150</option>
			<option value='200'>200</option>
			</select>";
		break;
		case "ArmeeType":
				$Nom1 = $ARMEES->armee["infanterie"]->nom;
				$Nom2 = $ARMEES->armee["infanterie-legere"]->nom;
				$Nom3 = $ARMEES->armee["infanterie-lourde"]->nom;
				$Champ = "<select name='" . $Type . "' id='" . $Type . "'>
			<option value='infanterie'>" . $Nom1 . "</option>
			<option value='infanterie-legere'>" . $Nom2 . "</option>
			<option value='infanterie-lourde'>" . $Nom3 . "</option>
			</select>";
		break;
		case "ArmeeID":
			$Champ = "<select name='" . $Type . "' id='" . $Type . "'>";
			$sql = "SELECT ArmeeID, ArmeeNom
				FROM Armee
				WHERE ArmeeEtat = " . $Etat . "
					AND ArmeeStatut = 0";
			$req = mysql_query($sql) or die('Erreur SQL # 61R!<br />'.$sql.'<br />'.mysql_error());
			while ( $data = mysql_fetch_array($req) )
			{
				$Champ .= "<option value='" . $data['ArmeeID'] . "'>" . $data['ArmeeNom'] . "</option>";
			}
			$Champ .= "</select>";
		break;
		case "TerritoiresVoisinsArmee":
			$TerritoireID	=	Attribut($Infos, "Armee", "ArmeeLieu");
			$Champ = TerritoireAcces($TerritoireID, "Select");
		break;
	}
	return $Champ;
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
		default:
			return $ValeurCherchee;
		break;
	}
}

function ArmeeDegats($ArmeeID, $ArmeeNom, $ArmeeNombre, $ArmeeArmure, $Degats)
{
	// Armure
	$DegatsAbsorbes = mt_rand(0, $ArmeeArmure);
	$Degats -= $DegatsAbsorbes;
	$texte = "";
	$texte .= "<br />Dégats absorbés : " . $DegatsAbsorbes . " [0, " . $ArmeeArmure . "]<br />";
	
	if ( $Degats >= $ArmeeNombre )
	{
		// Suppression de l'armée et de la cible, si elle existe de la BDD
		$Donnees = "ArmeeID = " . $ArmeeID;
		Supprimer("Armee", $Donnees);

		$Donnees = "CombattantID = " . $ArmeeID;
		Supprimer("Combattant", $Donnees);
		
		$Donnees = "CombattantCibleArmee = " . $ArmeeID;
		Supprimer("CombattantCible", $Donnees);
		
		$TexteAleatoire = Array("détruite", "annihilée", "grave niquée", "atomisée", "réduite en poussière comme chaque homme que nous sommes", "a fait pchit");
		$TexteDe = mt_rand(1, count($TexteAleatoire));
		$TexteSelectionne = isset($TexteAleatoire[$TexteDe]) ? $TexteAleatoire[$TexteDe] : "écrasée";
		$texte .= "=> <i>" . $ArmeeNom . "</i> est attaquée et " . $TexteSelectionne;
	}
	else if ( $Degats > 0 )
	{
		// Blessés ou morts ?
		$Blesses = mt_rand(0, $Degats);
		$Morts = $Degats - $Blesses;
		
		$texte .= "=> <i>" . $ArmeeNom . "</i> est attaquée et perd " . $Degats . " hommes, dont " . $Blesses . " blessés";

		$sql = "UPDATE Armee
			SET ArmeeNombre = ArmeeNombre - " . $Degats . ", ArmeeBlesses = ArmeeBlesses + " . $Blesses . "
				WHERE ArmeeID = " . $ArmeeID;
		mysql_query($sql) or die('Erreur SQL #0119<br />'.$sql.'<br />'.mysql_error());

		$sql = "UPDATE Combattant
			SET CombattantMorts = CombattantMorts + " . $Morts . "
				WHERE CombattantID = " . $ArmeeID;
		mysql_query($sql) or die('Erreur SQL #0120<br />'.$sql.'<br />'.mysql_error());
	}
	else
	{
		$TexteAleatoire = Array("Ils sont trop nuls!", "Joli coup!", "Pffff.", "Loser!", "zzZzZzZzZZzzz", "Apprenez à viser !");
		$TexteDe = mt_rand(1, count($TexteAleatoire));
		$TexteSelectionne = isset($TexteAleatoire[$TexteDe]) ? $TexteAleatoire[$TexteDe] : "A coté !";

		$texte .= "=> <i>" . $ArmeeNom . "</i> est attaquée mais ne subit aucune perte. " . $TexteSelectionne;
	}
	
	return $texte;
}

function ExplodeArray($Array)
{
	$Return = "";
	foreach( $Array as $cle => $element)
	{
		if ( is_array($element) == true )
		{
			$element = " Array -> <br />" . ExplodeArray($element);
		}
	    $Return .= '[' . $cle . '] vaut ' . $element . '<br />';
	}
	return $Return;
}

function UpdateTable($Table, $Where, $Set)
{
	$sql = "UPDATE " . $Table . "
		SET " . $Set . "
		WHERE " . $Where;
	$req = mysql_query($sql) or die('Erreur SQL #134<br />'.$sql.'<br />'.mysql_error());
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