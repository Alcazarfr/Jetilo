<?php
/*
$file=fopen("test.txt","a");
fwrite($file,"[".date("d/m/Y H:i:s")."] Connection\r\n");
foreach ($_GET as $k=>$v)
	{
	fwrite($file,"[".date("d/m/Y H:i:s")."] in \$_GET: $k => \"$v\" \r\n");
	}
foreach ($_POST as $k=>$v)
	{
	fwrite($file,"[".date("d/m/Y H:i:s")."] in \$_POST: $k => \"$v\" \r\n");
	}
fwrite($file,"[".date("d/m/Y H:i:s")."] Deconnection\r\n\r\n");
fclose($file);
*/

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
$Partie = isset($_POST['Partie']) ? $_POST['Partie'] : ( $_GET['Partie'] ? $_GET['Partie'] : 0);

$PartieStatut 	= Attribut($Partie, "Partie", "PartieStatut");
if ( $PartieStatut != 1 )
{
	$message = "Partie arrêtée";
	$mode = "non";
	exit;
}
// On récupère le mode = l'action à effeectuer
$mode = isset($_POST['mode']) ? $_POST['mode'] : ( $_GET['mode'] ? $_GET['mode'] : 'aucun');

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
		$Joueur = $_POST['Joueur'];

		$messages = LireMessages($Partie, $Joueur, time() - 120, false, "", false, true, false);
		
		foreach ($messages as $data) 
		{
			$Duree = $data['MessageDuree'] * 1000;
			$Duree = ( $Duree == 0 ) ? "sticky: true, " : "life: " . $Duree . ",";
			$message .= "$.jGrowl('" . str_replace("'", "\\'", $data['MessageTexte']). "',
				{
					" . $Duree . "
					header : '" . str_replace("'", "\\'", $data['MessageTitre']) . "'
				}
			);";
		}
	break;
	
	case "EtatInformations":
		$Joueur 	= $_POST['Joueur'];
		$EtatCible 	= $_POST['EtatCible'];
		$Etat	 	= $_POST['Etat'];
		
		$sql = "SELECT *
			FROM Etat
			WHERE EtatID = " . $EtatCible;
		$req = mysql_query($sql) or die('Erreur SQL #038<br />'.$sql.'<br />'.mysql_error());
		if ( $data = mysql_fetch_array($req) )
		{
			$message .= "<b><a href='#Etat' class='pointille' onClick='EtatInformations(".$Etat.")'>" . $data['EtatNom'] . "</a></b><br />";
			$message .= Attribut($data['EtatJoueur'], "Joueur", "JoueurNom") . "<br />";
			$message .= "<br />Territoires: " . $data['EtatTerritoires'];
			$message .= "<br />Population: " . $data['EtatPopulation'];
			if ( $EtatCible == $Etat )
			{
				$message .= "<br /><br /><a href=\"#\" id=\"creer-agent=" . $Etat . "\" class=\"modal\"><img src='./images/ironsword.gif'>Créer un agent</a><br />";
			}
		}
	break;
	
	case "territoireInformations":
		$Joueur 	= $_POST['Joueur'];
		$Etat	 	= $_POST['Etat'];
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
					$message .= "<br /><br />" . TerritoireAcces($TerritoireID, "Liste");
				break;
				
				case "Jeu" :
					$message = "<b>" . $TerritoireNom . "</b>";
					if ( $TerritoireTerrain )
					{
						$message .= "<br />";
						$message .= $TerritoireEtat ? "<a href='#Etat' class='pointille' onClick='EtatInformations(".$TerritoireEtat.")'>" . Attribut($TerritoireEtat, "Etat", "EtatNom") . "</a>" : "Terra Incognita";
					}
					$message .= ( $Etat == $TerritoireEtat ) ? "<br /><a href='#ActionPopulation' id='ActionPopulation=".$TerritoireID."' class='infobullefixe'><img src='./images/population.png'></a> " : "<br /><img src='./images/population.png'> ";
					$message .= $data['TerritoirePopulation'] . " habitants (".round(ChercherEffet('TERRITOIRE', $TerritoireID, "TerritoireCroissance", $data['TerritoireCroissance']),1)."%)<br /><br />";
					$message .= "<br /><br />" . TerritoireAcces($TerritoireID, "Liste");
					
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
					$ActionsMilitaire = ( $TerritoireEtat == $Etat ) ? "<a href='#ActionMilitaire' id='ActionMilitaire=".$TerritoireID."' class='infobullefixe'>" : "<a href='#Vous-n-etes-pas-chez-vous'>";

					$message .= "<br /><br />" . $ActionsMilitaire . "<img src='./images/epee.jpeg'></a> <b>Militaire</b><br />Défense : " . $TerritoireDefenseTexte . " (" . $TerritoireDefense . ")";
					$message .= "<br />Armée : ";
					
					$ListeVosArmees = "";
					$ListeArmeesAutres = "";
					$VosArmees = 0 ;
					$ArmeesAutres = 0 ;
					$sql = "SELECT a.*, t.TerritoireNom, e.EtatNom
						FROM Armee a, Territoire t, Etat e
						WHERE ( a.ArmeeTerritoire = " . $TerritoireID . " OR a.ArmeeLieu = " . $TerritoireID . " )
							AND t.TerritoireID = a.ArmeeLieu
							AND e.EtatID = a.ArmeeEtat";
					$req = mysql_query($sql) or die('Erreur SQL #051<br />'.$sql.'<br />'.mysql_error());
					while ( $data = mysql_fetch_array($req) )
					{
						$ArmeeID 		= $data['ArmeeID'];
						$ArmeeEtat 		= $data['ArmeeEtat'];
						$ArmeeNom 		= $data['ArmeeNom'];
						$ArmeeTaille	= $data['ArmeeTaille'];
						$ArmeeType	 	= $data['ArmeeType'];
						$ArmeeXP	 	= $data['ArmeeXP'];
						$ArmeeLieu		= $data['ArmeeLieu'];
						$ArmeeStatut	= $data['ArmeeStatut'] ? "en combat" : "au repos";
						$Statut		= "Statut";
						
						$Localisation 	= $data['TerritoireNom'];
						$ActionStatut = ( $data['ArmeeStatut'] == 0 ) ? "id='ActionArmee=".$ArmeeID."' class='infobullefixe'" : "";
						
						// Cas 1 : L'armée est au joueur et vient du territoire
						if ( $data['ArmeeTerritoire'] == $TerritoireID && $data['ArmeeEtat'] == $Etat )
						{
							$VosArmees++;
							$LieuTexte = ( $data['ArmeeLieu'] == $TerritoireID ) ? "sur place" : "<a href=\"#" . $ArmeeLieu. "\" onClick=\"TerritoireInformations(".$ArmeeLieu . ");\">" . $Localisation . "</a>";
							$ListeVosArmees		.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td>" . $LieuTexte . "</td><td>" . $ArmeeStatut . "</td><td align='center'><a href='#ActionArmee' " . $ActionStatut . "><img src='./images/move.png'></a></td></tr>";
						}
						
						// Cas 2 : l'armée est au joueur, sur un autre territoire
						else if ( $data['ArmeeLieu'] == $TerritoireID && $data['ArmeeEtat'] == $Etat )
						{
							$VosArmees++;
							$ListeVosArmees		.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td><a href=\"#" . $ArmeeLieu. "\" onClick=\"TerritoireInformations(".$ArmeeLieu . ");\">" . $Localisation . "</a></td><td>" . $ArmeeStatut . "</td><td align='center'><a href='#ActionArmee' " . $ActionStatut . "><img src='./images/move.png'></a></td></tr>";
						}
						// Cas 3 : l'armée est à un joueur ennemi
						else if ( $data['ArmeeEtat'] != $Etat )
						{
							$ArmeesAutres++;
							$ListeArmeesAutres	.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td>" . Attribut($ArmeeEtat, "Etat", "EtatNom") . "</td><td>" . $Statut . "</td></tr>";						
						}
					}
					// Affichage texte sur les armées locales
					if ( $VosArmees == 0 )
					{
						// Aucune armée n'est issue de ce territoire
						$message .= "Vous n'avez pas d'armée ici<br />";
					}
					else if ( $VosArmees == 1 )
					{
						$ListeVosArmees = "<table><tr><td><b>Armée</b></td><td><b>XP</b></td><td><b>Statut</b></td><td><b>Lieu</b></td><td><b>Actions</b></td></tr>" . $ListeVosArmees . "</table>";
						$message .= ( $TerritoireEtat == $Etat ) ? "Une armée est sur ce territoire" : "Une de vos armées squatte ce territoire";
						
						$message .= "<br />" . $ListeVosArmees . "<br />";
					}
					else
					{
						$ListeVosArmees = "<table><tr><td><b>Armée</b></td><td><b>XP</b></td><td><b>Statut</b></td><td><b>Lieu</b></td><td><b>Actions</b></td></tr>" . $ListeVosArmees . "</table>";
						$message .= ( $TerritoireEtat == $Etat ) ? $VosArmees . " armée ennemie est sur ce territoire" : $VosArmees . " armées alliées squattent ce territoire";
						$message .= "<br />" . $ListeVosArmees . "<br />";
					}
				
					// Affichage texte sur les armées autres (ennemis ou alliées) présentes
					if ( $ArmeesAutres == 0 )
					{
						$message .= "Aucune armée étrangère n'est détectée";
					}
					else if ( $ArmeesAutres == 1 )
					{
						$ListeArmeesAutres = "<table>
							<tr>
								<td><b>Armée</b></td>
								<td><b>XP</b></td>
								<td><b>Etat</b></td>
								<td><b>Région</b></td>
							</tr>" . $ListeArmeesAutres . "</table>";
						$message .= ( $TerritoireEtat == $Etat ) ? "Une armée ennemie attaque votre territoire" : "Une armée ennemie est sur ce territoire";
						$message .= "<br />" . $ListeArmeesAutres;
					}
					else
					{
						$ListeArmeesAutres = "<table>
							<tr>
								<td><b>Armée</b></td>
								<td><b>XP</b></td>
								<td><b>Etat</b></td>
								<td><b>Région</b></td>
							</tr>" . $ListeArmeesAutres . "</table>";
						$message .= ( $TerritoireEtat == $Etat ) ? $ArmeesAutres . " armées ennemies attaquent votre territoire" : $ArmeesAutres . " armées ennemies sont sur ce territoire";
						$message .= "<br />" . $ListeArmeesAutres;
					}

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

    			break;
			}
		}
		else
		{
			$message = "Le territoire n'existe pas";
		}
	break;

	case "RevendiquerVictoire" :
		$Etat	 	= $_POST['Etat'];
		$Bataille	= $_POST['Bataille'];
		$Partie		= $_POST['Partie'];
		$BatailleNom 	= Attribut($Bataille, "Bataille", "BatailleTitre");

		$sql = "SELECT CombattantEquipe, CombattantBataille
			FROM Combattant
			WHERE CombattantEtat = " . $Etat . "
				AND CombattantBataille = " . $Bataille;
		$req = mysql_query($sql) or die('Erreur SQL #116<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		$Equipe = $data['CombattantEquipe'];
		
		$sql = "SELECT CombattantEtat
			FROM Combattant
			WHERE CombattantBataille = " . $Bataille . "
				AND CombattantEquipe != " . $Equipe;
		$req = mysql_query($sql) or die('Erreur SQL #117<br />'.$sql.'<br />'.mysql_error());
		if ( $data = mysql_fetch_array($req) )
		{
			// Il y a des armées en face...
			Message($Partie, 0, "Revendiquer la victoire", "La bataille " . $BatailleNom . " est toujours en cours", $Etat, "", "noire", 10);			
		}
		else
		{
			$EtatNom 		= Attribut($Etat, "Etat", "EtatNom");
			
			// On efface toute trace de la bataille et des combats
			Supprimer("Bataille", "BatailleID = " . $Bataille);
			Supprimer("CombattantCible", "CombattantCibleEtat = " . $Etat);
						
			$sql = "SELECT CombattantID
				FROM Combattant
				WHERE CombattantBataille = " . $Bataille . "
					AND combattantEquipe = " . $Equipe;
			$req = mysql_query($sql) or die('Erreur SQL #117<br />'.$sql.'<br />'.mysql_error());
			while ( $data = mysql_fetch_array($req) )
			{
				$sql = "UPDATE Armee 
					SET ArmeeXP = ArmeeXP + 5, ArmeeStatut = 0
					WHERE ArmeeID = " . $data['CombattantID'];
				mysql_query($sql) or die('Erreur SQL #116<br />'.$sql.'<br />'.mysql_error());
			}

			Supprimer("Combattant", "CombattantBataille = " . $Bataille);

			// Plus aucun ennemi
			Message($Partie, 0, "Victoire", $EtatNom . " a remporté la bataille " . $BatailleNom,  $Etat, "", "noire", 10);
		}
	break;
	
	case "ArmeeAttaquer" :
		$Etat	 	= $_POST['Etat'];
		$Joueur	 	= $_POST['Joueur'];
		$Armee	 	= $_POST['Armee'];

		// Info sur l'armée qui a attaqué
		
		$sql = "SELECT c.*, a.*
			FROM Combattant c, Armee a
			WHERE c.CombattantID = " . $Armee . "
				AND a.ArmeeID = c.CombattantID";
		$req = mysql_query($sql) or die('Erreur SQL #106<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
				

		$ArmeeType 		= $data['ArmeeType'];
		$ArmeeEquipe 	= $data['CombattantEquipe'];
		$ArmeeForce 	= $ARMEES->armee[$ArmeeType]->force;
		$ArmeeVariation = $ARMEES->armee[$ArmeeType]->variation;
		$ArmeeBataille 	= $data['CombattantBataille'];
		$ArmeeTaille 	= $data['ArmeeTaille'];
		$ArmeeNombre 	= $data['ArmeeNombre'];
		$ArmeeCoefficient	= $ArmeeNombre / $ArmeeTaille;
		$ArmeeXP 		= $data['ArmeeXP'] / 20;
		
		$EcartComplet 	= 15;
		$EcartExistant 	=  time() - $data['CombattantProchaineAttaque'];
		$TempsEcoule	= $EcartComplet + $EcartExistant;
							
		$AttaqueCoefficient = round($TempsEcoule/$EcartComplet);
		$AttaqueCoefficient = $AttaqueCoefficient > 3 ? 3 : $AttaqueCoefficient;
		$DeAttaqueMin = 0;
		$DeAttaqueMax = 10;
		$DeAttaque = mt_rand($DeAttaqueMin, $DeAttaqueMax);
		
		$Degats		= $ArmeeForce * ($DeAttaque / $ArmeeForce) * $AttaqueCoefficient * $ArmeeCoefficient;
		$DegatsMin	= $Degats - $Degats*($ArmeeVariation+$ArmeeXP);
		$DegatsMax	= $Degats + $Degats*$ArmeeVariation;
		$Degats		= mt_rand($DegatsMin, $DegatsMax);
		
		// Sélection des armées ciblées en priorité par l'Etat de l'armée qui attaque
		$NombreDeCible = 0;
		$Cibles = Array();
		$sql = "SELECT cc.*, c.*, a.*
			FROM CombattantCible cc, Combattant c, Armee a
			WHERE cc.CombattantCibleEtat = " . $Etat . " 
				AND c.CombattantID = cc.CombattantCibleArmee
				AND c.CombattantBataille = " . $ArmeeBataille . "
				AND a.ArmeeID = c.CombattantID";
		$req = mysql_query($sql) or die('Erreur SQL #107<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$NombreDeCible++;
			$Cibles[$NombreDeCible]['ID'] = $data['ArmeeID'];
			$Cibles[$NombreDeCible]['Nom'] = $data['ArmeeNom'];
			$Cibles[$NombreDeCible]['PV'] = $data['ArmeeNombre'];
			$Cibles[$NombreDeCible]['Armure'] = $ARMEES->armee[$data['ArmeeType']]->armure;
		}
		
		if ( $NombreDeCible == 0 )
		{
			// Aucune cible n'a été désigné. On va donc attaquer les armées ennemies et répartir l'attaque sur une ou plusieurs armées
			
			$sql = "SELECT c.*, a.*
				FROM Combattant c, Armee a
				WHERE c.CombattantBataille = " . $ArmeeBataille . "
					AND c.CombattantEquipe != " . $ArmeeEquipe . "
					AND a.ArmeeID = c.CombattantID";
			$req = mysql_query($sql) or die('Erreur SQL #108<br />'.$sql.'<br />'.mysql_error());
			while ( $data = mysql_fetch_array($req) )
			{
				$NombreDeCible++;
				$Cibles[$NombreDeCible]['ID'] = $data['ArmeeID'];
				$Cibles[$NombreDeCible]['Nom'] = $data['ArmeeNom'];
				$Cibles[$NombreDeCible]['PV'] = $data['ArmeeNombre'];
				$Cibles[$NombreDeCible]['Armure'] = $ARMEES->armee[$data['ArmeeType']]->armure;
			}	
		}
		
		$DegatsParCible = round($Degats / $NombreDeCible);
		
		// On sélectionne les cibles
		$DegatsInfos = "";
		for ( $i = 1; $i <= $NombreDeCible; $i++ )
		{			
			// Fonction pour 
			// Mise à jour des armées
			// Destruction si plus d'armées
			$DegatsInfos .= ArmeeDegats($Cibles[$i]['ID'], $Cibles[$i]['Nom'], $Cibles[$i]['PV'], $Cibles[$i]['Armure'], $DegatsParCible);
		}
		
		$ArmeeXPGagne = $Degats / 50;
		
		$sql = "UPDATE Armee 
			SET ArmeeXP = ArmeeXP + " . $ArmeeXPGagne . "
			WHERE ArmeeID = " . $Armee;
		mysql_query($sql) or die('Erreur SQL #116<br />'.$sql.'<br />'.mysql_error());
		
		$ProchaineAttaque = time() + 15;
		$sql = "UPDATE Combattant 
			SET CombattantProchaineAttaque = " . $ProchaineAttaque . "
			WHERE CombattantID = " . $Armee;
		mysql_query($sql) or die('Erreur SQL #116<br />'.$sql.'<br />'.mysql_error());

		// Info sur les armées ennemis
		Message($Partie, 0, "Assaut", "Dé d'attaque : " . $DeAttaque . " [".$DeAttaqueMin."-".$DeAttaqueMax."]<br />Dégats : " . $Degats . " [". $DegatsMin . "-". $DegatsMax."]<br />Coefficients: " . $AttaqueCoefficient . " & " . $ArmeeCoefficient . $DegatsInfos, $ArmeeBataille, "", "noire", 10, "combat");

	break;
	
	case "afficherBataille" :
		$Etat	 	= $_POST['Etat'];

		$Bataille = FALSE;
		
		// CombattantEquipe = L'équipe dans lequel va se retouver le futur bataillon qui s'engage
		
		$ArmeeID = 0;
			$Batailles = "";
		
		$sql = "SELECT *
			FROM Bataille
			WHERE BatailleAttaquant = " . $Etat . "
			OR BatailleDefenseur = " . $Etat;
		$req = mysql_query($sql) or die('Erreur SQL #050<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$BatailleID 		= $data['BatailleID'];
			$BatailleTerritoire = $data['BatailleTerritoire'];
			$Bataille = TRUE;

			$Combattant = Array();
				
			$CombattantEquipeOk = FALSE;
			// A t'on déjà notre n° d'équipe ?
			$CombattantEquipe = 1;

			$sql = "SELECT *
				FROM Combattant
				WHERE CombattantBataille = " . $BatailleID;
			$req2 = mysql_query($sql) or die('Erreur SQL #050<br />'.$sql.'<br />'.mysql_error());
			while ( $data3 = mysql_fetch_array($req2) )
			{
				$ArmeeID	= $data3['CombattantID'];
				$Combattant[$ArmeeID] = Array(
					"Equipe" 			=> $data3['CombattantEquipe'],
					"ProchaineAttaque" 	=> $data3['CombattantProchaineAttaque']
				);
				if ( $data3['CombattantEtat'] == $Etat )
				{
					$CombattantEquipeOk = TRUE;
					$CombattantEquipe = $data3['CombattantEquipe'];
				}
			}
			
			if ( $CombattantEquipeOk == FALSE && $ArmeeID > 0 )
			{
				$sq = "SELECT MAX(CombattantEquipe) AS CombattantEquipeMax
					FROM Combattant
					WHERE CombattantBataille = " . $BatailleID;
				$re = mysql_query($sq) or die('Erreur SQL #050<br />'.$sq.'<br />'.mysql_error());
				$dat = mysql_fetch_array($re);
				$CombattantEquipe = $dat['CombattantEquipeMax'] + 1;
			}

			$ArmeesEnnemisEngagee = "";
			$ArmeesEnnemisReserve = "";
			$ArmeesReserve = "";
			$ArmeesEngagee = "";
				
			$sql = "SELECT *
				FROM Armee
				WHERE ArmeeLieu = " . $BatailleTerritoire;
			$req2 = mysql_query($sql) or die('Erreur SQL #050<br />'.$sql.'<br />'.mysql_error());
			while ( $data2 = mysql_fetch_array($req2) )
			{
				$ArmeeID 		= $data2['ArmeeID'];
				$ArmeeTaille 	= $data2['ArmeeTaille'];
				$ArmeeNombre 	= $data2['ArmeeNombre'];
						
				$ArmeeMoralMax = $ARMEES->armee[$data2['ArmeeType']]->moral_max;
				


				if ( $data2['ArmeeEtat'] == $Etat ) 
				{
					$Affichage = '<table border="1" cellspacing="0" cellpadding="3">
						<tr>
							<td></td>
						</tr>
					</table>';
					if ( isset($Combattant[$ArmeeID]) )
					{
						// L'armée est engagé
						$Details = "CombattantID:" . $ArmeeID . "=CombattantCibleArmee:" . $ArmeeID . "=";

						$EcartComplet 	= 15;
						$EcartExistant 	=  time() - $Combattant[$ArmeeID]['ProchaineAttaque'];
						$TempsEcoule	= $EcartComplet + $EcartExistant;

						// Attaque de l'armée proportionnelle au nombre d'hommes encore debout
						$ArmeeCoefficient	= 100 * $ArmeeNombre / $ArmeeTaille;
						$ArmeeCoefficientTexte = $ArmeeCoefficient . "%";

						// Attaque de l'armée proportionnelle au temps écoulé depuis la dernière attaque (100% = 15 sec, max 300%)					
						$AttaqueCoefficient = round(100*$TempsEcoule/$EcartComplet);
						$AttaqueCoefficientTexte = $AttaqueCoefficient > 300 ? 300 . " %" : $AttaqueCoefficient . " %";

						$ArmeesEngagee .= '<table border="1" cellspacing="0" cellpadding="3">
							<tr>';
						$ArmeesEngagee .= "<td>" . $data2['ArmeeNom'] . "<br />" . $data2['ArmeeNombre'] . " " . $data2['ArmeeType'] . "<br />" . $data2['ArmeeMoral'] . " (" . round($data2['ArmeeMoral']/$ArmeeMoralMax*100, 1) . "%)</td>";
						$ArmeesEngagee .= $AttaqueCoefficient >= 100 ? "<td><a href='#attaque' onClick=\"ArmeeAttaquer(".$ArmeeID.")\"><b>Attaquer l'ennemi</b></a> : Coefficients :" . $AttaqueCoefficientTexte . "/" . $ArmeeCoefficientTexte ."</td>" : "<td>En préparation: Coefficient = " . $AttaqueCoefficientTexte . "</td>";
						$ArmeesEngagee .= "<td><a href='#desengager' onClick=\"ActionCreer('desengager-armee', " . $Etat . ", ".$ArmeeID.", '" . $Details . "')\"><b>></b></a></td>";
						$ArmeesEngagee .= '</tr>
						</table><br />';
					}
					else
					{
						$ProchaineAttaque = time() + 15;
						$CombattantEquipe = 
						$Details 	= "CombattantBataille:".$BatailleID."=CombattantEtat:".$Etat."=CombattantID:" . $ArmeeID . "=CombattantProchaineAttaque:". $ProchaineAttaque ."=CombattantEquipe:" . $CombattantEquipe;
						$ArmeesReserve .= '<table border="1" cellspacing="0" cellpadding="3">
							<tr>';
						$ArmeesReserve .= "<td><a href='#engager' onClick=\"ActionCreer('engager-armee', " . $Etat . ", ".$ArmeeID.", '" . $Details . "')\"><b><</b></a></td>";
						$ArmeesReserve .= "<td>" . $data2['ArmeeNom'] . "<br />" . $data2['ArmeeNombre'] . " " . $ARMEES->armee[$data2['ArmeeType']]->nom . "<br />" . $data2['ArmeeMoral'] . " (" . round($data2['ArmeeMoral']/$ArmeeMoralMax*100, 1) . "%)</td>";
						$ArmeesReserve .= '</tr>
						</table><br />';
					}
				}
				else
				{
					if ( isset($Combattant[$ArmeeID]) )
					{
						$Moral = round($data2['ArmeeMoral'] / $ArmeeMoralMax*100);

						if ( $Moral >= 80 )
						{
							$Moral = "Enragé";
						}
						else if ( $Moral >= 60 )
						{
							$Moral = "Fort";
						}
						else if ( $Moral >= 40 )
						{
							$Moral = "Bon";
						}
						else if ( $Moral >= 20 )
						{
							$Moral = "Faible";
						}
						else
						{
							$Moral = "Fuyard";
						}
						
						$s = "SELECT *
							FROM CombattantCible
							WHERE CombattantCibleEtat = " . $Etat . "
								AND CombattantCibleArmee = " . $ArmeeID;
						$r = mysql_query($s) or die('Erreur SQL #050<br />'.$s.'<br />'.mysql_error());
						if ( $da = mysql_fetch_array($r) )
						{
							// Cette armée est ciblée
							$Details = "CombattantCibleArmee:".$ArmeeID."=CombattantCibleEtat:".$Etat;
							$Cible = "<a href='#decibler' onClick=\"ActionCreer('decibler-armee', " . $Etat . ", ".$ArmeeID.", '" . $Details . "')\">Décibler</a></td>";
						}
						else
						{
							// Cette armée n'est pas ciblée
							$Details = "CombattantCibleArmee:".$ArmeeID."=CombattantCibleEtat:".$Etat;
							$Cible = "<a href='#cibler' onClick=\"ActionCreer('cibler-armee', " . $Etat . ", ".$ArmeeID.", '" . $Details . "')\">Cibler</a></td>";						
						}
						// L'armée est engagé
						$ArmeesEnnemisEngagee .= '<table border="1" cellspacing="0" cellpadding="3">
							<tr>';
						$ArmeesEnnemisEngagee .= "<td>" . $data2['ArmeeNom'] . "<br />Régiment de " . $data2['ArmeeNombre'] . " " . $ARMEES->armee[$data2['ArmeeType']]->nom . "<br />" . $Moral . "<br />".$Cible."</td>";
						$ArmeesEnnemisEngagee .= '</tr>
						</table><br />';
					}
					else
					{
						$ArmeesEnnemisReserve .= '<table border="1" cellspacing="0" cellpadding="3">
							<tr>';
						$ArmeesEnnemisReserve .= "<td>" . $data2['ArmeeNom'] . "<br />Régiment de " . $data2['ArmeeNombre'] . " " . $ARMEES->armee[$data2['ArmeeType']]->nom . "</td>";
						$ArmeesEnnemisReserve .= '</tr>
						</table><br />';
					}
				}
			}

			$TimeMin = time() - 600;
			$messages = LireMessages($Partie, "", $TimeMin, time(), "combat", $BatailleID, false, true);

			$messagesCombat = "";
			foreach ($messages as $dataMessage) 
			{
				// Modulo ?				
				$Time 			= time() - $dataMessage['MessageTime'];
				$Minutes		= round($Time / 60);
	
				$Secondes		= $Time % 60;
				$Time			= $Minutes . "' " . $Secondes . "''";

				$messagesCombat .= $Time .": " . $dataMessage['MessageTexte'] . "<br /><br />";
			}
			
			$Batailles .= '<br /><a href="javascript:void(0);" onclick="AfficherBataille(false);">MAJ</a> -> <b>'.$data['BatailleTitre'].$BatailleTerritoire.'</b> -> <a href="#revendiquer" onClick="RevendiquerVictoire('.$BatailleID.')">Revendiquer la victoire</a><br /><br /><table border="1" cellspacing="0" cellpadding="3">
					<tr>
						<td width="300" align="center" valign="top" style="border: none;" colspan="2">Ennemis</td>
						<td width="350" align="center" valign="top" style="border: none;">Bataille</td>
						<td width="300" align="center" valign="top" style="border: none;" colspan="2">Forces</td>
					</tr>
					<tr>
						<td width="150" align="center" valign="top" style="border: none;">Réserve</td>
						<td width="150" align="center" valign="top" style="border: none;">Combattants</td>
						<td width="350" align="center" valign="top" style="border: none;"></td>
						<td width="150" align="center" valign="top" style="border: none;">Combattants</td>
						<td width="150" align="center" valign="top" style="border: none;">Réserve</td>
					</tr>

					<tr>
						<td width="150" align="left" valign="top" style="border: none;">' . $ArmeesEnnemisReserve . '</td>
						<td width="150" align="left" valign="top" style="border: none;">' . $ArmeesEnnemisEngagee . '</td>
						<td width="350" align="left" valign="top" style="border: none;"><div style="height: 200px; width: 300; overflow: auto">' . $messagesCombat . '</div></td>
						<td width="150" align="left" valign="top" style="border: none;">' . $ArmeesEngagee . '</td>
						<td width="150" align="left" valign="top" style="border: none;">' . $ArmeesReserve . '</td>
					</tr>
				</table><br />';
		}

		if ( $Bataille == TRUE )
		{
		$message = '
			<div class="postgrand">
			<div class="entry">
				' . $Batailles . '
			</div>
			</div>';
		}
	break;
	
	case "EnvoyerMP":
		$MessageCout 		= Array("EtatOr" => -1);
		$MessageEtat		= $_POST['MessageEtat'];
		$MessageJoueur 		= $_POST['MessageJoueur'];
		$MessageDestinataire= $_POST['MessageDestinataire'];
		$MessageTexte		= $_POST['MessageTexte'] . "<br /><i>Signé " . Attribut($MessageJoueur, "Joueur", "JoueurNom") . "</i>";
		$MessageDestinataire= $_POST['MessageDestinataire'];
		
		if ( $MessageDestinataire == -1 )
		{
			break;
		}

		if ( Transaction($Partie, $MessageEtat, $MessageCout, true) )
		{
			$t = Transaction($Partie, $MessageEtat, $MessageCout, false);
			$m = Message($Partie, $MessageDestinataire, "Message Privé", $MessageTexte, $MessageJoueur, "", "noire", 10);
		}
		return $t;
	
	break;
	
	
	case "InfobulleFixe" :		
		$InfobulleID = isset($_POST['InfobulleID']) ? $_POST['InfobulleID'] : ( isset($_GET['InfobulleID']) ? $_GET['InfobulleID'] : 0);
		$explode 	= explode("=", $InfobulleID);
		$Type		= $explode[0];
		$ID			= isset($explode[1]) ? $explode[1] : 0;
		
		switch ( $Type )
		{
			case "ActionMilitaire":
				$message .= "<a href=\"#\" id=\"creer-armee=" . $ID . "\" class=\"modal\"><img src='./images/ironsword.gif'>Créer une armée</a><br />";
				$message .= "<br /><a href=\"#\" id=\"renforcer-defense=" . $ID . "\" class=\"modal\"><img src='./images/Castle.png'>Renforcer les défenses</a><br />";
				$message .= "<br /><a href=\"#\" id=\"affaiblir-defense=" . $ID . "\" class=\"modal\"><img src='./images/pioche.png'>Affaiblir les défenses</a><br />";
			break;

			case "ActionArmee":
				$message .= "<a href=\"#\" id=\"supprimer-armee=" . $ID . "\" class=\"modal\"><img src='./images/destroy.gif'>Démobilisation</a><br />";
				$message .= "<br /><a href=\"#\" id=\"deplacer-armee=" . $ID . "\" class=\"modal\"><img src='./images/move.png'>Déplacement</a><br />";
				$message .= "<br /><a href=\"#\" id=\"entrainer-armee=" . $ID . "\" class=\"modal\"><img src='./images/TrainingIcon.gif'>Entrainement</a><br />";
				$message .= "<br /><a href=\"#\" id=\"attaquer=" . $ID . "\" class=\"modal\"><img src='./images/epee.jpeg'>Attaquer</a><br />";
			break;
			
			case "ActionPopulation":
				$message .= "Agir sur la population<br /><br />";
				$message .= "&bull; <a href=\"#\" id=\"arreter-croissance-population=" . $ID . "\" class=\"modal\">Arreter la croissance de la population</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"augmenter-croissance-population=" . $ID . "\" class=\"modal\">Augmenter la croissance de la population</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"reduire-croissance-population=" . $ID . "\" class=\"modal\">Réduire la croissance de la population</a><br />";
			break;
			
			default:
				$message .= "ID de bulle inconnu";
			break;
		}
	break;
	
	case "Modal" :
		$Etat 		= isset($_POST['Etat']) ? $_POST['Etat'] : ( isset($_GET['Etat']) ? $_GET['Etat'] : 0);
		$Joueur 	= isset($_POST['Joueur']) ? $_POST['Joueur'] : ( isset($_GET['Joueur']) ? $_GET['Joueur'] : 0);

		$ModalID = isset($_POST['ModalID']) ? $_POST['ModalID'] : ( isset($_GET['ModalID']) ? $_GET['ModalID'] : 0);
		$explode 	= explode("=", $ModalID);
		$Type		= $explode[0];
		$ID			= isset($explode[1]) ? $explode[1] : 0;
		$AutreValeur = Array();
		// Récupération des valeurs supplémentaires
		for ( $i = 2; $i < count($explode) ; $i++ )
		{
			$explodeInterne = explode(":", $explode[$i]);
			$AutreValeur = Array($explodeInterne[0] =>$explodeInterne[1]);
		}

		$message = Modal($Type, $ID, $Etat, $Joueur, $AutreValeur);
	break;

	case "ActionLancer":
		$Partie 	= $_POST['Partie'];
		
		$sql = "SELECT *
			FROM Action
			WHERE ActionPartie = " . $Partie . "
			AND ActionTimeDebut <= " . time() . "
			AND ActionStatut = 0";
		$req = mysql_query($sql) or die('Erreur SQL #051<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$ActionID		= $data['ActionID'];
			$ActionType 	= $data['ActionType'];
			$ActionSourceID = $data['ActionSourceID'];
			$ActionCibleID 	= $data['ActionCibleID'];
			$Details 		= $data['ActionDetails'];
			$Joueur 		= $data['ActionJoueur'];
			$Etat 			= $data['ActionEtat'];
			
			// On génère les effets
			$Resultat = ActionProduireEffets($Partie, $Etat, $Joueur, $ActionType, $Details, $ActionSourceID, $ActionCibleID, $ActionID);

			// On réalise l'effet s'il n'y a pas d'erreur
			if ( $Resultat )
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
				if ( is_array($Cout) )
				{
					// On transmet les ressources au joueur
					$Transaction = Transaction($Partie, $Etat, $Cout, false);
				}
				$ActionNom = $ACTIONS->action[$ActionType]->nom;
				Message($Partie, $Joueur, "Nouvelle Action ", $ActionNom, 0, "", "noire", 10);
			}
			else
			{
				Message($Partie, $Joueur, "Action", "Erreur dans la création de cette action", 0, "", "noire", 10);
			}
		}
	break;
	
	case "ActionCreer":
		$Partie 	= $_POST['Partie'];
		$Etat 		= $_POST['Etat'];
		$Joueur 	= $_POST['Joueur'];
		$ActionType = $_POST['ActionID'];
		$Details	= isset($_POST['Details']) ? $_POST['Details'] : "";

	//	Message($Partie, $Joueur, "Details", "Echo : " . $Details, 0, "", "noire", 10);

		$ActionSourceID = $_POST['SourceID'];
		$ActionCibleID 	= $_POST['CibleID'];
		
		Action($ActionType, $ActionSourceID, $ActionCibleID, $Details, $Partie, $Joueur, $Etat);

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
			if ( !Transaction($Partie, $Etat, $Cout, true) )
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
				$Transaction = Transaction($Partie, $Etat, $Cout, false);
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


		$Oisifs = (100-$data['EtatPopulationCivil']-$data['EtatPopulationMilitaire']-$data['EtatPopulationCommerce']-$data['EtatPopulationReligion']);
		$PIB	= ( $data['EtatPopulationCivil']*$data['EtatPopulation']/10000 ) + ( $data['EtatPopulationCommerce']*$data['EtatPopulation']/10000 ) + ( $data['EtatPopulationMilitaire']*$data['EtatPopulation']/10000 ) + ( $data['EtatPopulationReligion']*$data['EtatPopulation']/10000 );
		
		$message .= "Prochaine production dans <span id='Next'></span>";
		
		$sql = "SELECT *
			FROM CommerceTaux
			WHERE CommercePartie = " . $Partie . "
				ORDER BY CommerceTime DESC LIMIT 1";
				
		$req 	= mysql_query($sql) or die('Erreur SQL #0150<br />'.$sql.'<br />'.mysql_error());
		$data2 	= mysql_fetch_array($req);
		
		$TauxMilitaire 	= $data2['CommerceMilitaire'];
		$TauxReligion 	= $data2['CommerceReligion'];
		$TauxCommerce 	= $data2['CommerceCommerce'];
		$TauxCivil 		= $data2['CommerceCivil'];
		
		$message .= "<table>";
		$message .= "<tr>";
			$message .= "<td height='40' colspan='2'></td>";
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
			$message .= "<td height='40' width='12' rowspan='3' align='center'>P<br />E<br />U<br />P<br />L<br />E</td>";
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
			$message .= "<td align='center'> " . round($data['EtatPopulation']*$data['EtatCroissance']/100) . " hab / <a class='pointille' id='InfoTotalProd' title=\"".$TexteInfoBulle."\">" . $EtatCroissance . " %</a></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($data['EtatTaxe']*$PIB/100, 1) . " pts</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40' colspan='2'></td>";
			$message .= "<td width='100' align='center'><b>Civil</b></td>";
			$message .= "<td width='100' align='center'><b>Commerce</b></td>";
			$message .= "<td width='100' align='center'><b>Militaire</b></td>";
			$message .= "<td width='100' align='center'><b>Religion</b></td>";
			$message .= "<td width='100' align='center'><b>Total</b></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td width='100' align='center'><b>Or</b></td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30' colspan='2'>Entretien (pts/min)</td>";
			$message .= "<td align='center'>" . couleur(round(-$data['EtatEntretienCivil'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . couleur(round(-$data['EtatEntretienCommerce'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . couleur(round(-$data['EtatEntretienMilitaire'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . couleur(round(-$data['EtatEntretienReligion'], 2)) . " pts</td>";
			$message .= "<td align='center'> " . round($data['EtatPopulation']*$data['EtatCroissance']/100) . " hab / <a class='pointille' id='InfoTotalProd' title=\"".$TexteInfoBulle."\">" . $EtatCroissance . " %</a></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($data['EtatTaxe']*$PIB/100, 1) . " pts</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40' colspan='2'></td>";
			$message .= "<td width='100' align='center'><b>Civil</b></td>";
			$message .= "<td width='100' align='center'><b>Commerce</b></td>";
			$message .= "<td width='100' align='center'><b>Militaire</b></td>";
			$message .= "<td width='100' align='center'><b>Religion</b></td>";
			$message .= "<td width='100' align='center'><b>Total</b></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td width='100' align='center'><b>Or</b></td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40' width='12' rowspan='9' align='center'>C<br />O<br />M<br />M<br />E<br />R<br />C<br />E</td>";
			$TexteInfoBulle	= "Prix de 10 Points de ressource en Or.";
			$message .= "<td height='30'><a id='InfoTotalRep' title=\"".$TexteInfoBulle."\"  class='pointille'>Taux</a></td>";
			$message .= "<td align='center'>" . $TauxCivil . " Or</td>";
			$message .= "<td align='center'>" . $TauxCommerce . " Or</td>";
			$message .= "<td align='center'>" . $TauxMilitaire . " Or</td>";
			$message .= "<td align='center'>" . $TauxReligion . " Or </td>";
			$message .= "<td align='center'></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>...</td>";
		$message .= "</tr>";
		$message .= "<tr><td height='5' colspan='8'</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Offre (pts/min)</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceOffreCivil-".$Etat."\">". $data['CommerceOffreCivil'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceOffreCommerce-".$Etat."\">". $data['CommerceOffreCommerce'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceOffreMilitaire-".$Etat."\">". $data['CommerceOffreMilitaire'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceOffreReligion-".$Etat."\">". $data['CommerceOffreReligion'] . "</span> pts</td>";
			$message .= "<td align='center'> " . round(-1*($data['CommerceOffreCivil']+$data['CommerceOffreCommerce']+$data['CommerceOffreMilitaire']+$data['CommerceOffreReligion']), 2) . "</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . Couleur(round((($data['CommerceOffreCivil']*$TauxCivil)+($data['CommerceOffreCommerce']*$TauxCommerce)+($data['CommerceOffreMilitaire']*$TauxMilitaire)+($data['CommerceOffreReligion']*$TauxReligion))*(1/10), 2)) . "</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Demande (pts/min)</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceDemandeCivil-".$Etat."\">". $data['CommerceDemandeCivil'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceDemandeCommerce-".$Etat."\">". $data['CommerceDemandeCommerce'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceDemandeMilitaire-".$Etat."\">". $data['CommerceDemandeMilitaire'] . "</span> pts</td>";
			$message .= "<td align='center'><span class=\"edit\" id=\"".$Partie."-CommerceDemandeReligion-".$Etat."\">". $data['CommerceDemandeReligion'] . "</span> pts</td>";
			$message .= "<td align='center'> " . round($data['CommerceDemandeCivil']+$data['CommerceDemandeCommerce']+$data['CommerceDemandeMilitaire']+$data['CommerceDemandeReligion']) . "</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . Couleur(round((($data['CommerceDemandeCivil']*$TauxCivil)+($data['CommerceDemandeCommerce']*$TauxCommerce)+($data['CommerceDemandeMilitaire']*$TauxMilitaire)+($data['CommerceDemandeReligion']*$TauxReligion))*(-1/10), 2)) . "</td>";
		$message .= "</tr>";
		$message .= "<tr><td height='5' colspan='8'</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Exportation (pts/min)</td>";
			$message .= "<td align='center'>" . round($data['CommerceExportationCivil'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceExportationCommerce'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceExportationMilitaire'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceExportationReligion'], 2) . " pts</td>";
			$message .= "<td align='center'> " . round($data['CommerceExportationCivil']+$data['CommerceExportationCommerce']+$data['CommerceExportationMilitaire']+$data['CommerceExportationReligion']) . "</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . Couleur(round((($data['CommerceExportationCivil']*$TauxCivil)+($data['CommerceExportationCommerce']*$TauxCommerce)+($data['CommerceExportationMilitaire']*$TauxMilitaire)+($data['CommerceExportationReligion']*$TauxReligion))*(-1/10), 2)) . "</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Importation (pts/min)</td>";
			$message .= "<td align='center'>" . round($data['CommerceImportationCivil'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceImportationCommerce'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceImportationMilitaire'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceImportationReligion'], 2) . " pts</td>";
			$message .= "<td align='center'>" . round($data['CommerceImportationCivil']+$data['CommerceImportationCommerce']+$data['CommerceImportationMilitaire']+$data['CommerceImportationReligion'], 2) . "</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . Couleur(round((($data['CommerceImportationCivil']*$TauxCivil)+($data['CommerceImportationCommerce']*$TauxCommerce)+($data['CommerceImportationMilitaire']*$TauxMilitaire)+($data['CommerceImportationReligion']*$TauxReligion))*(-1/10), 2)) . "</td>";
		$message .= "</tr>";
		$message .= "<tr><td height='5' colspan='8'</tr>";
		$message .= "<tr>";
			$message .= "<td height='30'>Solde (pts/min)</td>";
			$message .= "<td align='center'>" . Couleur(round($data['CommerceImportationCivil']+$data['CommerceExportationCivil'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . Couleur(round($data['CommerceExportationCommerce']+$data['CommerceImportationCommerce'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . Couleur(round($data['CommerceExportationMilitaire']+$data['CommerceImportationMilitaire'], 2)) . " pts</td>";
			$message .= "<td align='center'>" . Couleur(round($data['CommerceExportationReligion']+$data['CommerceImportationReligion'], 2)) . " pts</td>";
			$message .= "<td align='center'> " . Couleur(round($data['CommerceExportationCivil']+$data['CommerceImportationCivil']+$data['CommerceExportationMilitaire']-$data['CommerceImportationMilitaire']+$data['CommerceExportationCommerce']-$data['CommerceImportationCommerce']+$data['CommerceExportationReligion']-$data['CommerceImportationReligion']),2) . " pts</td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>".
			Couleur(round(
					(
						(
							($data['CommerceExportationCivil']*$TauxCivil)+($data['CommerceExportationCommerce']*$TauxCommerce)
							+($data['CommerceExportationMilitaire']*$TauxMilitaire)
							+($data['CommerceExportationReligion']*$TauxReligion)
						)
						*
						(-1/10)
					)
					+
					(
						(
							($data['CommerceImportationCivil']*$TauxCivil)
							+($data['CommerceImportationCommerce']*$TauxCommerce)
							+($data['CommerceImportationMilitaire']*$TauxMilitaire)
							+($data['CommerceImportationReligion']*$TauxReligion)
						)
						*(-1/10)
					), 2))."</td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40' colspan='2'></td>";
			$message .= "<td width='100' align='center'><b>Civil</b></td>";
			$message .= "<td width='100' align='center'><b>Commerce</b></td>";
			$message .= "<td width='100' align='center'><b>Militaire</b></td>";
			$message .= "<td width='100' align='center'><b>Religion</b></td>";
			$message .= "<td width='100' align='center'><b>Total</b></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td width='100' align='center'><b>Or</b></td>";
		$message .= "</tr>";
		$message .= "<tr>";
			$message .= "<td height='40'></td>";
			$message .= "<td height='30'>Points</td>";
			$message .= "<td align='center'>" . round($data['EtatPointCivil'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointCommerce'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointMilitaire'], 1) . " pts</td>";
			$message .= "<td align='center'>" . round($data['EtatPointReligion'], 1) . " pts</td>";
			$message .= "<td align='center'></td>";
			$message .= "<td width='1' align='center'></td>";
			$message .= "<td align='center'>" . round($data['EtatOr'], 1) . " or</td>";
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

	case "Commercer":
		$sql = "SELECT *
			FROM CommerceTaux
			WHERE CommercePartie = " . $Partie . "
				ORDER BY CommerceTime DESC LIMIT 1";
		$req = mysql_query($sql) or die('Erreur SQL #150<br />'.$sql.'<br />'.mysql_error());
		if ( $data = mysql_fetch_array($req) )
		{
			$PartieTauxProchain = $data['CommerceTime'] + 30;
			$Prix['Civil'] 		= $data['CommerceCivil'];
			$Prix['Commerce'] 	= $data['CommerceCommerce'];
			$Prix['Militaire'] 	= $data['CommerceMilitaire'];
			$Prix['Religion'] 	= $data['CommerceReligion'];
		}
		else
		{
			// Première utilisation
			$PartieTauxProchain = time() - 60;
			$Prix['Civil'] 		= 10;
			$Prix['Commerce'] 	= 10;
			$Prix['Militaire'] 	= 10;
			$Prix['Religion'] 	= 10;
		}
	
		if ( time() < $PartieTauxProchain )
		{
			// Il y a commerce et changement de taux toutes les 2 minutes
			return false;
		}
		
		$NombreEtat = 0;
		$Etats = Array();
		
		$Offre 		= Array(
			"Civil" => 0, 
			"Commerce" => 0, 
			"Militaire" => 0, 
			"Religion" => 0);
		
		$Demande 	= Array(
			"Civil" => 0, 
			"Commerce" => 0, 
			"Militaire" => 0, 
			"Religion" => 0);
			
		$Variation 	= Array(
			"Civil" => 0, 
			"Commerce" => 0, 
			"Militaire" => 0, 
			"Religion" => 0);

		$NombreOffrant	= Array(
			"Civil" => 0, 
			"Commerce" => 0, 
			"Militaire" => 0, 
			"Religion" => 0);
			
		$Offrant	= Array(
			"Civil" => "", 
			"Commerce" => "", 
			"Militaire" => "", 
			"Religion" => "");

		$Exportation	= Array(
			"Civil" => "", 
			"Commerce" => "", 
			"Militaire" => "", 
			"Religion" => "");


		$NombreDemandeur	= Array(
			"Civil" => 0, 
			"Commerce" => 0, 
			"Militaire" => 0, 
			"Religion" => 0);

		$Demandeur	= Array(
			"Civil" => "", 
			"Commerce" => "", 
			"Militaire" => "", 
			"Religion" => "");

		$Importation	= Array(
			"Civil" => "", 
			"Commerce" => "", 
			"Militaire" => "", 
			"Religion" => "");

		$Allocation	= Array();

		$sql = "SELECT EtatID, EtatJoueur, CommerceOffreCivil, CommerceOffreCommerce, CommerceOffreMilitaire, CommerceOffreReligion, CommerceDemandeCivil, CommerceDemandeCommerce, CommerceDemandeMilitaire, CommerceDemandeReligion
			FROM Etat
			WHERE EtatPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL #2134<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$Etat 	= $data['EtatID'];
			$Joueur = $data['EtatJoueur'];
			$Etats[$NombreEtat] = $Etat;
			$NombreEtat++;
			
			$Organisation = Array(
				0 => Array($data['CommerceOffreCivil'], "Civil", "EtatPointCivil"),
				1 => Array($data['CommerceOffreCommerce'], "Commerce", "EtatPointCommerce"),
				2 => Array($data['CommerceOffreMilitaire'], "Militaire", "EtatPointMilitaire"),
				3 => Array($data['CommerceOffreReligion'], "Religion", "EtatPointReligion")
			);
			for ( $b = 0; $b < 4; $b++ )
			{
				$dataValeur = $Organisation[$b][0];
				$dataTexte 	= $Organisation[$b][1];
				$dataPoint 	= $Organisation[$b][2];

				if ( $dataValeur > 0 && Transaction($Partie, $Etat, Array($dataPoint => $dataValeur*-1), true) == true )
				{
					// On ne prend que les offrants qui peuvent envoyer leur marchandise.
					$Offre[$dataTexte] 	+= $dataValeur;
					$Nombre = $NombreOffrant[$dataTexte]++;

					$Offrant[$dataTexte][$Nombre] = Array(
						"Etat" => $Etat,
						"Valeur" => $dataValeur);
					$Exportation[$dataTexte][$Etat] = 0;
			//		Message($Partie, 0, "Test", "Etat " . $Etat . " offre " . $dataTexte . " : Q=" . $dataValeur, 0, "", "noire", 10);			

				}
			}

			$Organisation = Array(
				0 => Array($data['CommerceDemandeCivil'], "Civil"),
				1 => Array($data['CommerceDemandeCommerce'], "Commerce"),
				2 => Array($data['CommerceDemandeMilitaire'], "Militaire"),
				3 => Array($data['CommerceDemandeReligion'], "Religion")
			);

			for ( $b = 0; $b < 4; $b++ )
			{
				$dataValeur = $Organisation[$b][0];
				$dataTexte 	= $Organisation[$b][1];

				if ( $dataValeur > 0 )
				{
					$Nombre = $NombreDemandeur[$dataTexte]++;
					$Demande[$dataTexte] += $dataValeur;

					$Demandeur[$dataTexte][$Nombre] = Array(
						"Etat" => $Etat,
						"Valeur" => $dataValeur);
					$Importation[$dataTexte][$Etat] = 0;
			//		Message($Partie, 0, "Test", "Etat " . $Etat . " demande " . $dataTexte . " : Q=" . $dataValeur, 0, "", "noire", 10);			
				}
			}
		}
		// Création Demandeur et Offrant vérifiée et OK

		$Allocation["Civil"] 		= CommerceAllocation($Offrant['Civil'], $Demandeur['Civil'], $Offre['Civil'], $Demande['Civil']);
		$Allocation["Commerce"] 	= CommerceAllocation($Offrant['Commerce'], $Demandeur['Commerce'], $Offre['Commerce'], $Demande['Commerce']);
		$Allocation["Militaire"] 	= CommerceAllocation($Offrant['Militaire'], $Demandeur['Militaire'], $Offre['Militaire'], $Demande['Militaire']);
		$Allocation["Religion"] 	= CommerceAllocation($Offrant['Religion'], $Demandeur['Religion'], $Offre['Religion'], $Demande['Religion']);
		
		// Echanges
		$Ressources = Array("Civil", "Commerce", "Militaire", "Religion");
		$RessourcesEtat = Array("EtatPointCivil", "EtatPointCommerce", "EtatPointMilitaire", "EtatPointReligion");
		for ( $i = 0; $i < 4; $i++ )
		{
			// Boucle 1 : Les 4 ressources
			$Ressource 		= $Ressources[$i];
			$RessourceEtat 	= $RessourcesEtat[$i];
			
			for ( $j = 0; $j < $NombreEtat; $j++ )
			{
				// Boucle 2 : on prend tous les Etats
				$Etat = $Etats[$j];		// OK
				
				// Niveau 3 : Importation
				if ( isset($Allocation[$Ressource][$Etat]["Importation"]) )
				{
					$transaction 	= Array($RessourceEtat => $Allocation[$Ressource][$Etat]["Importation"]);
					Transaction($Partie, $Etat, $transaction, false);
					
					$ImportationSet = "CommerceImportation" . $Ressource;
					$Set 			= $ImportationSet . " = " . $Allocation[$Ressource][$Etat]["Importation"];
					$Where			= "EtatID = " . $Etat;
					$Table 			= "Etat";
					UpdateTable($Table, $Where, $Set);
			//	Message($Partie, 0, "Commerce - Taux", "M : " . $Set, 0, "", "noire", 10);			

					$CoutOr			= $Allocation[$Ressource][$Etat]["Importation"] * $Prix[$Ressource] * -1;
					$transactionOr 	= Array("EtatOr" => $CoutOr);
					Transaction($Partie, $Etat, $transaction, false);
				}
				
				// Niveau 3 : Exportation
				if ( isset($Allocation[$Ressource][$Etat]["Exportation"]) )
				{
					$transaction 	= Array($RessourceEtat => $Allocation[$Ressource][$Etat]["Exportation"]*-1);
					$Montant 		= $Allocation[$Ressource][$Etat]["Exportation"]*-1;
					Transaction($Partie, $Etat, $transaction, false);

					$ExportationSet = "CommerceExportation" . $Ressource;
					$Set 			= $ExportationSet . " = " . $Montant;
					$Where			= "EtatID = " . $Etat;
					$Table 			= "Etat";
					UpdateTable($Table, $Where, $Set);
				//	Message($Partie, 0, "Commerce - Taux", "X : " .$Set, 0, "", "noire", 10);			
					
					$GainOr		= $Allocation[$Ressource][$Etat]["Exportation"] * $Prix[$Ressource];
					$transactionOr = Array("EtatOr" => $GainOr);
					Transaction($Partie, $Etat, $transaction, false);
				}
			}
		}		
			
		
		$Variation['Civil'] 	= DeterminerVariationTaux($Offre['Civil'] , $Demande['Civil']); 
		$Variation['Commerce'] 	= DeterminerVariationTaux($Offre['Commerce'] , $Demande['Commerce']); 
		$Variation['Militaire'] = DeterminerVariationTaux($Offre['Militaire'] , $Demande['Militaire']); 
		$Variation['Religion'] 	= DeterminerVariationTaux($Offre['Religion'] , $Demande['Religion']); 
		
		$Prix['Civil'] 		+= round($Prix['Civil'] * $Variation['Civil'],2);
		$Prix['Commerce'] 	+= round($Prix['Commerce'] * $Variation['Commerce'],2);
		$Prix['Militaire'] 	+= round($Prix['Militaire'] * $Variation['Militaire'],2);
		$Prix['Religion'] 	+= round($Prix['Religion'] * $Variation['Religion'],2);
		
		$Texte = "";
		$Texte	.= ( $Variation['Civil'] != 0 ) ? "<br />- Civil: " . $Prix['Civil'] . " (".$Variation['Civil']."%)" : "";
		$Texte	.= ( $Variation['Commerce'] != 0 ) ? "<br />- Commerce: " . $Prix['Commerce'] . " (".$Variation['Commerce']."%)" : "";
		$Texte	.= ( $Variation['Militaire'] != 0 ) ? "<br />- Militaire: " . $Prix['Militaire'] . " (".$Variation['Militaire']."%)" : "";
		$Texte	.= ( $Variation['Religion'] != 0 ) ? "<br />- Religion: " . $Prix['Religion'] . " (".$Variation['Religion']."%)" : "";
		
		$sql = "INSERT INTO CommerceTaux (CommercePartie, CommerceCivil, CommerceCommerce, CommerceMilitaire, CommerceReligion, CommerceTime)
			VALUES (" . $Partie . ", " . $Prix['Civil'] . ", " . $Prix['Commerce'] . ", " . $Prix['Militaire'] . ", " . $Prix['Religion'] . ", " . time() . ")";
		mysql_query($sql) or die('Erreur SQL #157 Commerce<br />'.$sql.'<br />'.mysql_error());	
		
		if ( $Texte != "")
		{
			Message($Partie, 0, "Commerce - Taux", "De nouveaux taux ont été fixés: " . $Texte, 0, "", "noire", 10);			
		}
	break;
	
	case "ActionsEnCours":
		$Etat 	= $_POST['Etat'];		

		$message = time();
		$sql = "SELECT *
			FROM Action
			WHERE ActionEtat = " . $Etat . "
				AND ActionTimeDebut > " . time();
		$req = mysql_query($sql) or die('Erreur SQL #150<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$TempsRestant 	= $data['ActionTimeFin'] - time();
			$ActionNom		= $ACTIONS->action[$data['ActionType']]->nom;
			$ActionCible	= $ACTIONS->action[$data['ActionType']]->type_cible;
			$message .= "
			<tr>
				<td width='30%'><b>" . $ActionNom . "</b></td>
				<td width='30%'>sur " . $ActionCible ."</td>
				<td width='40%'>" . $TempsRestant . " sec</td>
			</tr>";
		}
		
		$message = "<table cellspacing='0' cellpadding='0'>" . $message . "</table>";
	break;


	case "Journal":
		
		$Joueur 	= $_POST['Joueur'];		
		$TimeMin 	= $_POST['TimeMin'] ? $_POST['TimeMin'] : time() - 300 ;
		$TimeMax 	= $_POST['TimeMax'] ? $_POST['TimeMax'] : time();
		$Source 	= $_POST['Source'] ? "AND MessageSource = " . $_POST['Source'] : "";

		$messages = LireMessages($Partie, $Joueur, $TimeMin, $TimeMax, "", $Source, false, true);
		
		foreach ($messages as $data) 
		{
			$Destinataire 	= !$data['MessageDestinataire'] ? "tout le monde" : "vous-même";
			$Source 		= !$data['MessageSource'] ? "l'administration" : Attribut( $data['MessageSource'], "Joueur", "JoueurNom");
			
			// Modulo ?				
			$Time 			= time() - $data['MessageTime'];
			$Minutes		= floor($Time / 60);
			// ICI, il faut arondir au négatif

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
		
		$message = "<table cellspacing='0' cellpadding='0'>" . $message . "</table>";
	break;
}
mysql_close();
	
echo $message;

?>