<?php

//
// AJAX/PARTIE.php = les fonctions ajax utilisées durant les parties
//

// Inclusion des fonctions
define('ROOT_PATH', '../../');
include ROOT_PATH.'config.php';
include ROOT_PATH.'includes/init.php';

// Connexion à la BDD
connectMaBase();

// On récupère l'ID de la partie
$Partie 		= $_POST['Partie'];

$PartieStatut 	= Attribut($Partie, "Partie", "PartieStatut");
if ( $PartieStatut != 1 )
{
	$message = "Partie arrêtée";
	$mode = "non";
	exit;
}
// On récupère le mode = l'action à effeectuer
$mode = $_POST['mode'] ? $_POST['mode'] : ( $_GET['mode'] ? $_GET['mode'] : 'aucun');

$message = "";
if ( !$mode )
{
	$message = "Aucun mode n'est valable";
}

// On précise le code à exécuter
switch ( $mode )
{
	// Affichage dynamiques des messages
	case "messageLire":

		$Joueur 	= $_POST['Joueur'];

		$Time = time() - 120;
		// On ne s'occupe que des msg de moins de 2 minutes (si coupure de connexion) pour ne pas surcharger la fonction
		$sql = "SELECT *
			FROM Message
			WHERE MessagePartie = " . $Partie . "
			AND MessageTime >= " . $Time . "
			AND MessageDestinataire IN (0, " . $Joueur . ")
			AND MessageLu = 0";
		$req = mysql_query($sql) or die('Erreur SQL #033<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req) )
		{
			$afficher = TRUE;
			
			// Certains joueurs sont exclus des messages (s'ils sont à l'origine d'un message public
			// Ex: X déclare la guerre à B. On crée un message public pour C, D et E, mais A et B ne le verront pas.
			if ( $data['MessageExclus'] )
			{
				$explode = explode(", ", $data['MessageExclus']);
				for ( $i = 0 ; $i <= count($data['MessageExclus']) ; $i++ )
				{
					$JoueurExclu = $explode[$i];
					if ( $Joueur == $JoueurExclu )
					{
						$afficher = FALSE;
						break;
					}
				}
			}
			if ( $data['MessageDestinataire'] == 0 )
			{
				// SI le message est public, on passe par une table annexe pour définir qui l'a lu
				$sql = "SELECT *
					FROM MessageLu
					WHERE MessageLuID = " . $data['MessageID'] . "
					AND MessageLuJoueur = " . $Joueur;
				$req2 = mysql_query($sql) or die('Erreur SQL #034<br />'.$sql.'<br />'.mysql_error());
				if ( $data2 = mysql_fetch_array($req2) )
				{
					// Si on est ici, c'est que le message a été lu
					$afficher = FALSE;
				}
				else
				{
					// On met à jour les messages publics lu par le joueur
					$sql = 'INSERT INTO MessageLu (MessageLuID, MessageLuJoueur)
						VALUES(' . $data['MessageID']  . ', ' . $Joueur . ')';
					mysql_query($sql) or die('Erreur SQL #035'.$sql.'<br />'.mysql_error());
				}
			}

			if ( $afficher == TRUE )
			{
				$Duree = $data['MessageDuree'] * 1000;
				$Duree = ( $Duree == 0 ) ? "sticky: true, " : "life: " . $Duree . ",";
				$message .= "$.jGrowl('" . $data['MessageTexte']. "',
					{
						" . $Duree . "
						header : '" . $data['MessageTitre'] . "'
					}
				);";
			}	
		}
		
		// On met à jour les messages privés : ils sont désormais lu
		$sql = "UPDATE Message
			SET MessageLu = 1
				WHERE MessagePartie = " . $Partie . "
				AND MessageDestinataire = " . $Joueur . "
				AND MessageLu = 0";
		mysql_query($sql) or die('Erreur SQL #028<br />'.$sql.'<br />'.mysql_error());
		
	break;
	
	case "EtatInformations":
		$Joueur 	= $_POST['Joueur'];
		$Etat	 	= $_POST['Etat'];
		
		$sql = "SELECT *
			FROM Etat
			WHERE EtatID = " . $Etat;
		$req = mysql_query($sql) or die('Erreur SQL #038<br />'.$sql.'<br />'.mysql_error());
		if ( $data = mysql_fetch_array($req) )
		{
			$message .= "<b><a href='#Etat' class='pointille' onClick='EtatInformations(".$Etat.")'>" . $data['EtatNom'] . "</a></b><br />";
			$message .= Attribut($data['EtatJoueur'], "Joueur", "JoueurNom") . "<br />";
			$message .= "<br />Territoires: " . $data['EtatTerritoires'];
			$message .= "<br />Population: " . $data['EtatPopulation'];

		}
	break;
	
	case "Modal":
		$Joueur 	= $_POST['Joueur'];
		$Etat	 	= $_POST['Etat'];

	break;
	
	case "territoireInformations":
		$Joueur 	= $_POST['Joueur'];
		$Territoire = $_POST['Territoire'];
		$SousMode 	= $_POST['SousMode'];
		
		$sql = "SELECT *
			FROM Territoire
			WHERE TerritoireID = " . $Territoire;
		$req = mysql_query($sql) or die('Erreur SQL #038<br />'.$sql.'<br />'.mysql_error());
		if ( $data = mysql_fetch_array($req) )
		{
			$TerritoireID 		= $data['TerritoireID'];
			$TerritoireNom 		= $data['TerritoireNom'];
			$TerritoireEtat 	= $data['TerritoireEtat'];
			$TerritoireJoueur 	= $data['TerritoireJoueur'];
			$TerritoireTerrain 	= $data['TerritoireTerrain'];
			
			switch ( $SousMode )
			{
				case "Admin" :
					$message = "Changer le nom du territoire: <div class=\"edit\" id=\"TerritoireNom-".$TerritoireID."\">".$TerritoireNom."</div>";
				break;
				
				case "Placement" :
					$message = "<b>" . $TerritoireNom . "</b>";
					if ( $TerritoireTerrain )
					{
						$message .= "<br />";
						$message .= $TerritoireEtat ? Attribut($TerritoireEtat, "Etat", "EtatNom") : "Terra Incognita";
						$Placement = '<a href="#' . $TerritoireID . '-Placer" onClick="Placer(' . $TerritoireID . ')">Capturer le territoire</a>';
						$message .= !$TerritoireEtat ? "<br />Vous pouvez capturer ce territoire: " . $Placement : "";
					}
					$message .= "<br />" . $data['TerritoirePopulation'] . " habitants<br /><br />";
					$message .= "<br /><br />" . TerritoireAcces($TerritoireID);
				break;
				
				case "Jeu" :
					$message = "<b>" . $TerritoireNom . "</b>";
					if ( $TerritoireTerrain )
					{
						$message .= "<br />";
						$message .= $TerritoireEtat ? "<a href='#Etat' class='pointille' onClick='EtatInformations(".$TerritoireEtat.")'>" . Attribut($TerritoireEtat, "Etat", "EtatNom") . "</a>" : "Terra Incognita";
					}
					$message .= "<br />" . $data['TerritoirePopulation'] . " habitants (".round($data['TerritoireCroissance'],1)."%)<br /><br />";
					$message .= "<br /><br />" . TerritoireAcces($TerritoireID);
					
					$TerritoireDefense 	= ChercherEffet("TERRITOIRE", $Territoire, "TerritoireDefense", $data['TerritoireDefense']);
					if ( $TerritoireDefense > 500 )
					{
						$TerritoireDefenseTexte = "Imprenable";
					}
					else if ( $TerritoireDefense > 200 )
					{
						$TerritoireDefenseTexte = "Fortifié";
					}
					else if ( $TerritoireDefense > 100 )
					{
						$TerritoireDefenseTexte = "Consolidée";
					}
					else if ( $TerritoireDefense > 50 )
					{
						$TerritoireDefenseTexte = "Bonne";
					}
					else if ( $TerritoireDefense > 25 )
					{
						$TerritoireDefenseTexte = "Faible";
					}
					else if ( $TerritoireDefense > 10 )
					{
						$TerritoireDefenseTexte = "De fortune";
					}
					else
					{
						$TerritoireDefenseTexte = "Précaire";
					}

					$message .= "<br /><b>Militaire</b><br />Défense : " . $TerritoireDefenseTexte . " (" . $TerritoireDefense . ")";
					$message .= "<br />Armée : ";
					
					$ListeArmee = "";
					$Armees = 0 ;
					$sql = "SELECT *
						FROM Armee
						WHERE ArmeeTerritoire = " . $TerritoireID;
					$req = mysql_query($sql) or die('Erreur SQL #051<br />'.$sql.'<br />'.mysql_error());
					while ( $data = mysql_fetch_array($req) )
					{
						$Armees++;
						$ArmeeNom 		= $data['ArmeeNom'];
						$ArmeeTaille	= $data['ArmeeTaille'];
						$ArmeeType	 	= $data['ArmeeType'];
						$ArmeeXP	 	= $data['ArmeeXP'];
						$ListeArmee		.= $data['ArmeeNom'];
					}
					if ( $Armees == 0 )
					{
						// Aucune armée n'est issue de ce territoire
						$message .= "Aucune armée n'est issue de ce territoire";
					}
					else if ( $Armees == 1 )
					{
						$message .= "Une armée est issue de ce territoire<br />" . $ListeArmees;
					}
					else
					{
						$message .= "Une armée est issue de ce territoire<br />" . $ListeArmees;
					}
					$time = time();


					$ModelAgent = Array(
						"Nom" 			=> "Rondont",
						"Statut"	 	=> 0,
						"Secret" 		=> 0,
						"Territoire" 	=> $TerritoireID,
						"CapaciteFurtivite"	=> 10,
						"CapaciteVitesse" 	=> 5,
						"CapaciteReussite" 	=> 20,
						"Type" 			=> "Général"
					);
					$LienGeneral = FormaterLien("AgentCreer", $ModelAgent);
					
					$message .= "<br /><a href=\"#\" onClick=\"".$LienGeneral."\">Créer un général</a> : ";
					$message .= "<br /><a href=\"#\" onClick=\"ActionCreer('renforcer-defense', " . $TerritoireEtat . ", " . $TerritoireID . ")\">Créer un bonus défensif</a> : ";
					
					$message .= "<br /> <a href=\"#\" id=\"modal_1\" class=\"modal\">Ouvrir la modal 1</a>";
					$message .= '<div style="display: none;">
    					<div id="titre_modal_4">Titre4</div>
    					<div id="data_modal_4">TT4</div></div>';
					$message .= "<br /> <a href=\"#\" id=\"modal_lol\" class=\"modal\">Ouvrir la modal 4</a>";
					$message .= '<div style="display: none;">
    					<div id="titre_modal_lol">TitreLol</div>
    					<div id="data_modal_lol">TTlol</div></div>';
    			break;
			}
		}
		else
		{
			$message = "Le territoire n'existe pas";
		}
	break;

	case "ActionCreer":
		$Partie 	= $_POST['Partie'];
		$Etat 		= $_POST['Etat'];
		$Joueur 	= $_POST['Joueur'];
		$ActionType = $_POST['ActionID'];
		
		$ActionSourceID = $_POST['SourceID'];
		$ActionCibleID 	= $_POST['CibleID'];
		
		$ActionCibleType 		= strtoupper($ACTIONS->action[$ActionType]->type_cible);
		$ActionSourceType 	= strtoupper($ACTIONS->action[$ActionType]->type_source);


		$TableauDesNomsDesCouts	= Array("EtatPointCivil", "EtatPointCommerce", "EtatPointMilitaire", "EtatPointReligion", "EtatOr"); 
		$Cout = Array();
		
		for ( $i = 0; $i < count($TableauDesNomsDesCouts); $i++ )
		{
			$CoutPotentiel = $TableauDesNomsDesCouts[$i];
			if ( $ACTIONS->action[$ActionType]->couts->$CoutPotentiel)
			{
				$Cout[$CoutPotentiel] = $ACTIONS->action[$ActionType]->couts->$CoutPotentiel;
			}
		}

		// Cela coute t'il de l'argent ?
		if ( is_array($Cout) )
		{
			// Si oui, On vérifie que le Joueur a les ressources suffisantes
			if ( !Transaction($Partie, $Joueur, $Etat, $Cout, true) )
			{
				Message($Partie, $Joueur, "Echec", "Pas assez de ressource", 0, "", "noire", 10);
				break;
			}
		}

		// Création de l'action
		$ActionNom			=	$ACTIONS->action[$ActionType]->nom;
		$ActionTimeDebut	=	time() + $ACTIONS->action[$ActionType]->delai;
		$ActionTimeFin		=	$ActionTimeDebut + $ACTIONS->action[$ActionType]->duree;

		$ActionID	= Action($ActionType, $ActionSourceType, $ActionSourceID, $ActionTimeDebut, $ActionTimeFin);

		// On verifie que l'actiona a bien été crée
		if ( is_numeric($ActionID) == false )
		{
			// La création de l'action a échoué : on annule tout
			Message($Partie, $Joueur, "Echec", "La création de laction a échoué"  . $CibleID, 0, "", "noire", 10);
			break;
		}
	
		// RECUPERER LA VALEUR DEPUIS UNE MODAL / UN CHAMP
		// ANNULER LES EFFETS SI ILS BUGENT
		$Erreur = false;
		$SiErreur = "";
		for ( $i = 0 ; $i < 10 ; $i ++ )
		{
			if ( isset($ACTIONS->action[$ActionType]->effets[$i]->nom) )
			{
				$EffetTimeDebut = $ACTIONS->action[$ActionType]->effets[$i]->delai + time();
				$EffetTimeFin 	= $ACTIONS->action[$ActionType]->effets[$i]->duree + $EffetTimeDebut;
				$EffetTable 	= $ACTIONS->action[$ActionType]->effets[$i]->table_concernee;
				$EffetVariable 	= $ACTIONS->action[$ActionType]->effets[$i]->variable_concernee;
				$EffetType 		= strtoupper($ACTIONS->action[$ActionType]->effets[$i]->type_effet);
				$EffetValeur 	= $ACTIONS->action[$ActionType]->effets[$i]->valeur;
				
				$EffetID		= Effet($ActionID, $ActionCibleType, $ActionCibleID, $EffetTimeDebut, $EffetTimeFin, $EffetTable, $EffetVariable, $EffetType, $EffetValeur);

				$SiErreur		.= ( is_numeric($EffetID) == true ) ? ", " . $EffetID : "";
				
				if ( is_numeric($EffetID) == false )
				{
					$Erreur = true;
					Supprimer("Action", "Action = " . $ActionID);
					Supprimer("Effet", "EffetID IN(0" . $SiErreur . ")");
					Supprimer("Effet", "EffetID IN(0" . $SiErreur . ")");
					
					// On annule les précédents effets et on supprime l'action
					break;
				}
			}
			else
			{
				break;
			}
		}
		// On réalise l'effet
		if ( !$Erreur )
		{
			if ( is_array($Cout) )
			{
				// On transmet les ressources au joueur
				$Transaction = Transaction($Partie, $Joueur, $Etat, $Cout, false);
			}
			Message($Partie, $Joueur, "Nouvelle Action", $ActionNom, 0, "", "noire", 10);
		}
		else
		{
			Message($Partie, $Joueur, "Effet", "Création échec", 0, "", "noire", 10);
		}
	break;
	
	case "AgentCreer":
		$Partie 			= $_POST['Partie'];
		$Etat 				= $_POST['Etat'];
		$Joueur 			= $_POST['Joueur'];
		$Nom 				= $_POST['Nom'];
		$Statut 			= $_POST['Statut'];
		$Secret 			= $_POST['Secret'];
		$Territoire 		= $_POST['Territoire'];
		$CapaciteFurtivite 	= $_POST['CapaciteFurtivite'];
		$CapaciteVitesse 	= $_POST['CapaciteVitesse'];
		$CapaciteReussite 	= $_POST['CapaciteReussite'];
		$Type 				= $_POST['Type'];
		$Cout 				= Array("EtatPointMilitaire" => -10, "EtatOr" => -10);

		// Cela coute t'il de l'argent ?
		if ( is_array($Cout) )
		{
			// Si oui, On vérifie que le Joueur a les ressources suffisantes
			if ( !Transaction($Partie, $Joueur, $Etat, $Cout, true) )
			{
				Message($Partie, $Joueur, "Echec", "Pas assez de ressource", 0, "", "noire", 10);
				break;
			}
		}

		// On réalise l'effet
		$Succes		= Agent($Nom, $Etat, $Statut, $Secret, $Territoire, $CapaciteFurtivite, $CapaciteVitesse, $CapaciteReussite, $Type);
		if ( $Succes )
		{
			if ( is_array($Cout) )
			{
				// On transmet les ressources au joueur
				$Transaction = Transaction($Partie, $Joueur, $Etat, $Cout, false);
			}
			Message($Partie, $Joueur, "Effet", "Agent crée", 0, "", "noire", 10);
		}
		else
		{
			Message($Partie, $Joueur, "Effet", "Création échec", 0, "", "noire", 10);
		}
	break;
	
	
	case "EffetCreer":
		$Partie 	= $_POST['Partie'];
		$Etat 		= $_POST['Etat'];
		$Joueur 	= $_POST['Joueur'];
		$CibleType 	= $_POST['CibleType'];
		$CibleID 	= $_POST['CibleID'];
		$AgentEtat 	= $_POST['SourceType'];
		$SourceID 	= $_POST['SourceID'];
		$Nom 		= $_POST['Nom'];
		$TimeDebut 	= $_POST['TimeDebut'];
		$TimeFin 	= $_POST['TimeFin'];
		$Table 		= $_POST['Table'];
		$Variable 	= $_POST['Variable'];
		$Type 		= $_POST['Type'];
		$Valeur 	= $_POST['Valeur'];
		$Cout 		= Array("EtatPointMilitaire" => -10, "EtatOr" => -10);

		// Cela coute t'il de l'argent ?
		if ( is_array($Cout) )
		{
			// Si oui, On vérifie que le Joueur a les ressources suffisantes
			if ( !Transaction($Partie, $Joueur, $Etat, $Cout, true) )
			{
				Message($Partie, $Joueur, "Echec", "Pas assez de ressource", 0, "", "noire", 10);
				break;
			}
		}

		// On réalise l'effet
		$Succes		= Effet($CibleType, $CibleID, $SourceType, $SourceID, $Nom, $TimeDebut, $TimeFin, $Table, $Variable, $Type, $Valeur);
		if ( $Succes )
		{
			if ( is_array($Cout) )
			{
				// On transmet les ressources au joueur
				$Transaction = Transaction($Partie, $Joueur, $Etat, $Cout, false);
			}
			Message($Partie, $Joueur, "Effet", "Création réussie", 0, "", "noire", 10);
		}
		else
		{
			Message($Partie, $Joueur, "Effet", "Création échec", 0, "", "noire", 10);
		}

	break;
	
	case "Production":
		$Etat 	= $_POST['Etat'];

		Production($Partie, $Etat);
	case "AfficherPopulation":
		$Etat 	= $_POST['Etat'];

		$sql = "SELECT *
			FROM Etat
			WHERE EtatID = " . $Etat;
		$req = mysql_query($sql) or die('Erreur SQL #050<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		
		$EtatCroissance		= Couleur(round($data['EtatCroissance'], 1));
		$DerniereProduction = $data["EtatDerniereProduction"];
		$Coefficient 		= (time() - $DerniereProduction ) / $DureeTourProduction;
//		$ProchaineProduction= $DerniereProduction + 30 - time();
//		$ProchaineProduction= ( $ProchaineProduction < 10 ) ? 10 : $ProchaineProduction;
		$Oisifs = (100-$data['EtatPopulationCivil']-$data['EtatPopulationMilitaire']-$data['EtatPopulationCommerce']-$data['EtatPopulationReligion']);
		$PIB	= ( $data['EtatPopulationCivil']*$data['EtatPopulation']/10000 ) / ( $data['EtatPopulationCommerce']*$data['EtatPopulation']/10000 ) + ( $data['EtatPopulationMilitaire']*$data['EtatPopulation']/10000 ) + ( $data['EtatPopulationReligion']*$data['EtatPopulation']/10000 );
		
		$message .= "Prochaine production dans <span id='Next'></span>";
		
		$message .= "<table>";
		$message .= "<tr>";
			$message .= "<td height='40'></td>";
			$message .= "<td width='100' align='center'><b>Civil</b></td>";
			$message .= "<td width='100' align='center'><b>Commerce</b></td>";
			$message .= "<td width='100' align='center'><b>Militaire</b></td>";
			$message .= "<td width='100' align='center'><b>Religion</b></td>";
			$message .= "<td width='100' align='center'><b>Total</b></td>";
			$message .= "<td width='1' align='center'></td>";
			$TexteInfoBulle = "L'or est récupéré par une taxe perçue sur la somme totale des points générés. Le taux d'imposition est spécifiée à la ligne Répartition";
			$message .= "<td width='100' align='center'><b><a class='pointille' title=\"".$TexteInfoBulle."\" id='InfoOrTitre'>Or</span></a></td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Répartition (%)</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-EtatPopulationCivil-".$Etat."\">". $data['EtatPopulationCivil'] . "</span> %</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-EtatPopulationCommerce-".$Etat."\">". $data['EtatPopulationCommerce'] . "</span> %</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-EtatPopulationMilitaire-".$Etat."\">". $data['EtatPopulationMilitaire'] . "</span> %</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-EtatPopulationReligion-".$Etat."\">". $data['EtatPopulationReligion'] . "</span> %</td>";
			$TexteInfoBulle	= "Force de travail non utilisée: ". $Oisifs;
			$message .= "<td align='center'><a id='InfoTotalRep' title=\"".$TexteInfoBulle."\"  class='pointille'>" . round($data['EtatPopulationCivil']+$data['EtatPopulationMilitaire']+$data['EtatPopulationCommerce']+$data['EtatPopulationReligion']) . " %</a></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-EtatTaxe-".$Etat."\">". $data['EtatTaxe'] . "</span> %</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Valeur absolue (hab)</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationCivil']*$data['EtatPopulation']/100) . " hab</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationCommerce']*$data['EtatPopulation']/100) . " hab</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationMilitaire']*$data['EtatPopulation']/100) . " hab</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationReligion']*$data['EtatPopulation']/100) . " hab</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulation']) . " hab</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($PIB) . " pts</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Production (pts/min)</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationCivil']*$data['EtatPopulation']/10000, 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationCommerce']*$data['EtatPopulation']/10000, 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationMilitaire']*$data['EtatPopulation']/10000, 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPopulationReligion']*$data['EtatPopulation']/10000, 1) . " pts</td>";
			$TexteInfoBulle	= "Croissance de votre population. Par défaut, la croissance augmente de +0.05 point par territoire par minute. En cas de famine, la croissance perd 2 points par minute, dans les territoires en surpopulation.";
			$message .= "<td align='center'><a class='pointille' id='InfoTotalProd' title=\"".$TexteInfoBulle."\">" . $EtatCroissance . " %</a></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($data['EtatTaxe']*$PIB/100, 1) . " pts</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Points</td>";
			$message .= "<td align='center'>" . round($data['EtatPointCivil'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointCommerce'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointMilitaire'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointReligion'], 1) . " pts</td>";
			$message .= "<td align='center'> " . round($data['EtatPopulation']*$data['EtatCroissance']/100) . " hab</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($data['EtatOr'], 1) . " or</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40'></td>";
			$message .= "<td width='100' align='center'><b>Civil</b></td>";
			$message .= "<td width='100' align='center'><b>Commerce</b></td>";
			$message .= "<td width='100' align='center'><b>Militaire</b></td>";
			$message .= "<td width='100' align='center'><b>Religion</b></td>";
			$message .= "<td width='100' align='center'><b>Total</b></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td width='100' align='center'><b>Or</b></td>";
		$message .= "</tr>";
		$message .= "</table>";
		$message .= '<script type="text/javascript">

				$(function () {
						$("#Next").countdown({until: +60,layout: "{sn} {sl}"});
					});
$(document).ready(function() {
 			
    			$(\'.edit\').editable(\'./includes/ajax/administration.php?mode=modifierChamp\', {
    				 style   : "display: inline",
    			     callback : function() {
						Population(false);
						MessageLire(false);
    			 	}
    			});
			 });
 		</script>';
	break;
	
	case "Journal":
		$Joueur 	= $_POST['Joueur'];
		$TimeMin 	= $_POST['TimeMin'] ? $_POST['TimeMin'] : time() - 300 ;
		$TimeMax 	= $_POST['TimeMax'] ? $_POST['TimeMax'] : time();
		$Source 	= $_POST['Source'] ? "AND MessageSource = " . $_POST['Source'] : "";

		$sql = "SELECT *
			FROM Message
			WHERE MessagePartie = " . $Partie . "
			AND MessageTime >= " . $TimeMin . "
			AND MessageTime <= " . $TimeMax . "
			" . $Source . "
			AND MessageDestinataire IN (0, " . $Joueur . ")
			ORDER BY MessageTime DESC";
		$req = mysql_query($sql) or die('Erreur SQL #052<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req) )
		{
			$afficher = TRUE;
			
			// Certains joueurs sont exclus des messages (s'ils sont à l'origine d'un message public
			// Ex: X déclare la guerre à B. On crée un message public pour C, D et E, mais A et B ne le verront pas.
			if ( $data['MessageExclus'] )
			{
				$explode = explode(", ", $data['MessageExclus']);
				for ( $i = 0 ; $i <= count($data['MessageExclus']) ; $i++ )
				{
					$JoueurExclu = $explode[$i];
					if ( $Joueur == $JoueurExclu )
					{
						$afficher = FALSE;
						break;
					}
				}
			}
			if ( $afficher == TRUE )
			{
				$Destinataire 	= !$data['MessageDestinataire'] ? "tout le monde" : "vous-même";
				$Source 		= !$data['MessageSource'] ? "l'administration" : Attribut( $data['MessageSource'], "Joueur", "JoueurNom");
				
				// Modulo ?				
				$Time 			= time() - $data['MessageTime'];
				$Minutes		= round($Time / 60);

				$Secondes		= $Time % 60;
				$Time			= $Minutes . "' " . $Secondes . "''";

				$message .= "
				<tr>
					<td width='30%'><b>" . $data['MessageTitre'] . "</b></td>
					<td width='30%'>" . $Time ."</td>
					<td width='40%'>De <i>" . $Source . "</i> à <i>" . $Destinataire . "</i></td>
				</tr>
				<tr>
					<td width='100%' colspan='3'>" . $data['MessageTexte'] . "</td>
				</tr>";
			}	
		}
		$message = "<table cellspacing='0' cellpadding='0'>" . $message . "</table>";
	break;
}
mysql_close();
	
echo $message;

?>