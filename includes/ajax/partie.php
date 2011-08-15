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
					$message .= "<br />" . $data['TerritoirePopulation'] . " habitants (".round(ChercherEffet('TERRITOIRE', $TerritoireID, "TerritoireCroissance", $data['TerritoireCroissance']),1)."%) <a href='#ActionPopulation' id='ActionPopulation=".$TerritoireID."' class='infobullefixe'>Actions</a><br /><br />";
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

					$message .= "<br /><b>Militaire</b><br />Défense : " . $TerritoireDefenseTexte . " (" . $TerritoireDefense . ")";
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
						$Statut		= "Statut";
						
						$Localisation 	= $data['TerritoireNom'];
						
						// Cas 1 : L'armée est au joueur et vient du territoire
						if ( $data['ArmeeTerritoire'] == $TerritoireID && $data['ArmeeEtat'] == $Etat )
						{
							$VosArmees++;
							$ActionTexte = ( $data['ArmeeLieu'] == $TerritoireID ) ? "<a href='#ActionArmee' id='ActionArmee=".$ArmeeID."' class='infobullefixe'>Actions</a>" : "";
							$ListeVosArmees		.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td><a href=\"#" . $ArmeeLieu. "\" onClick=\"TerritoireInformations(".$ArmeeLieu . ");\">" . $Localisation . "</a></td><td>" . $ActionTexte . "</td></tr>";
						}
						
						// Cas 2 : l'armée est au joueur, sur un autre territoire
						else if ( $data['ArmeeLieu'] == $TerritoireID && $data['ArmeeEtat'] == $Etat )
						{
							$VosArmees++;
							$ActionTexte = ( $data['ArmeeLieu'] == $TerritoireID ) ? "<a href='#ActionArmee' id='ActionArmee=".$ArmeeID."' class='infobullefixe'>Actions</a>" : "";
							$ListeVosArmees		.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td><a href=\"#" . $ArmeeLieu. "\" onClick=\"TerritoireInformations(".$ArmeeLieu . ");\">" . $Localisation . "</a></td><td>" . $ActionTexte . "</td></tr>";
						}
						// Cas 3 : l'armée est à un joueur ennemi
						else if ( $data['ArmeeEtat'] != $Etat )
						{
							$ArmeesAutres++;
							$ListeArmeesAutres	.= "<tr><td>" . $data['ArmeeNom'] . "</td><td> " . $ArmeeXP . "</td><td>" . Attribut($ArmeeEtat, "Etat", "EtatNom") . "</td><td>" . $Statut . "</td><td><a href='#ActionArmee' id='ActionArmee=".$ArmeeID."' class='infobullefixe'>Actions</a></td></tr>";						
						}
					}
					// Affichage texte sur les armées locales
					if ( $VosArmees == 0 )
					{
						// Aucune armée n'est issue de ce territoire
						$message .= "Aucune armée n'est issue de ce territoire";
					}
					else if ( $VosArmees == 1 )
					{
						$ListeVosArmees = "<table><tr><td><b>Armée</b></td><td><b>XP</b></td><td><b>Lieu</b></td><td><b>Actions</b></td></tr>" . $ListeVosArmees . "</table>";
						$message .= "Une armée est issue de ce territoire<br />" . $ListeVosArmees;
					}
					else
					{
						$ListeVosArmees = "<table><tr><td><b>Armée</b></td><td><b>XP</b></td><td><b>Lieu</b></td><td><b>Actions</b></td></tr>" . $ListeVosArmees . "</table>";
						$message .= $VosArmees . " armées sont issues de ce territoire<br />" . $ListeVosArmees;
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
								<td><b>Actions</b></td>
							</tr>" . $ListeArmeesAutres . "</table>";
						$message .= "Une armée squatte ce territoire<br />" . $ListeArmeesAutres;
					}
					else
					{
						$ListeArmeesAutres = "<table>
							<tr>
								<td><b>Armée</b></td>
								<td><b>XP</b></td>
								<td><b>Etat</b></td>
								<td><b>Région</b></td>
								<td><b>Actions</b></td>
							</tr>" . $ListeArmeesAutres . "</table>";
						$message .= $ArmeesAutres . " armées squattent grave ce territoire<br />" . $ListeArmeesAutres;
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

					$message .= "<br /><a href='#ActionMilitaire' id='ActionMilitaire=".$TerritoireID."' class='infobullefixe'>Actions</a>";
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

		$sql = "SELECT CombattantEquipe
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
			Supprimer("Bataille", "BatailleID = " . $Bataille);
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
		
		$EcartComplet 	= 15;
		$EcartExistant 	=  time() - $data['CombattantProchaineAttaque'];
		$TempsEcoule	= $EcartComplet + $EcartExistant;
							
		$AttaqueCoefficient = round($TempsEcoule/$EcartComplet);
		$AttaqueCoefficient = $AttaqueCoefficient > 3 ? 3 : $AttaqueCoefficient;
		$DeAttaqueMin = 0;
		$DeAttaqueMax = 10;
		$DeAttaque = mt_rand($DeAttaqueMin, $DeAttaqueMax);
		
		$Degats		= $ArmeeForce * ($DeAttaque / $ArmeeForce) * $AttaqueCoefficient * $ArmeeCoefficient;
		$DegatsMin	= $Degats - $Degats*$ArmeeVariation;
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
		$Batailles = "";
		$ArmeesEnnemisEngagee = "";
		$ArmeesEnnemisReserve = "";
		$ArmeesReserve = "";
		$ArmeesEngagee = "";
		$Combattant = Array();
				
		$CombattantEquipeOk = FALSE;
		// A t'on déjà notre n° d'équipe ?
		$CombattantEquipe = 1;
		
		// CombattantEquipe = L'équipe dans lequel va se retouver le futur bataillon qui s'engage
		
		$ArmeeID = 0;
		
		$sql = "SELECT *
			FROM Bataille
			WHERE BatailleAttaquant = " . $Etat . "
			OR BatailleDefenseur = " . $Etat;
		$req = mysql_query($sql) or die('Erreur SQL #050<br />'.$sql.'<br />'.mysql_error());
		while ( $data = mysql_fetch_array($req) )
		{
			$BatailleID = $data['BatailleID'];
			$Bataille = TRUE;
	
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
			
			$sql = "SELECT *
				FROM Armee
				WHERE ArmeeLieu = " . $data['BatailleTerritoire'];
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
			
			$Batailles .= '<br /><a href="javascript:void(0);" onclick="AfficherBataille(false);">MAJ</a> -> <b>'.$data['BatailleTitre'].'</b> -> <a href="#revendiquer" onClick="RevendiquerVictoire('.$BatailleID.')">Revendiquer la victoire</a><br /><br /><table border="1" cellspacing="0" cellpadding="3">
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
	
	case "InfobulleFixe" :		
		$InfobulleID = isset($_POST['InfobulleID']) ? $_POST['InfobulleID'] : ( isset($_GET['InfobulleID']) ? $_GET['InfobulleID'] : 0);
		$explode 	= explode("=", $InfobulleID);
		$Type		= $explode[0];
		$ID			= isset($explode[1]) ? $explode[1] : 0;
		
		switch ( $Type )
		{
			case "ActionMilitaire":
				$message .= "&bull; <a href=\"#\" id=\"creer-armee=" . $ID . "\" class=\"modal\">Créer une armée</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"renforcer-defense=" . $ID . "\" class=\"modal\">Renforcer les défenses</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"affaiblir-defense=" . $ID . "\" class=\"modal\">Affaiblir les défenses</a><br />";
			break;

			case "ActionArmee":
				$message .= "&bull; <a href=\"#\" id=\"supprimer-armee=" . $ID . "\" class=\"modal\">Démobilisation</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"deplacer-armee=" . $ID . "\" class=\"modal\">Déplacement</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"entrainer-armee=" . $ID . "\" class=\"modal\">Entrainement</a><br />";
				$message .= "&bull; <a href=\"#\" id=\"attaquer=" . $ID . "\" class=\"modal\">Attaquer</a><br />";
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
	
	case "ActionCreer":
		$Partie 	= $_POST['Partie'];
		$Etat 		= $_POST['Etat'];
		$Joueur 	= $_POST['Joueur'];
		$ActionType = $_POST['ActionID'];
		$Details	= isset($_POST['Details']) ? $_POST['Details'] : "";

	//	Message($Partie, $Joueur, "Details", "Echo : " . $Details, 0, "", "noire", 10);

		$ActionSourceID = $_POST['SourceID'];
		$ActionCibleID 	= $_POST['CibleID'];
		
		$ActionCibleType 	= strtoupper($ACTIONS->action[$ActionType]->type_cible);
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
		$ActionTimeFin		=	( $ACTIONS->action[$ActionType]->duree == "illimité" ) ?  $ActionTimeDebut + 999999 : $ActionTimeDebut + $ACTIONS->action[$ActionType]->duree;

		$ActionID	= Action($ActionType, $ActionSourceType, $ActionSourceID, $ActionTimeDebut, $ActionTimeFin);

		// On verifie que l'action a bien été crée
		if ( is_numeric($ActionID) == false )
		{
			// La création de l'action a échoué : on annule tout
			Message($Partie, $Joueur, "Echec", "La création de laction a échoué"  . $CibleID, 0, "", "noire", 10);
			break;
		}
	
		// Si l'action = création d'une entrée dans la BDD
		$Erreur = false;
		
		// On fracture champ par champs	
		if ( $Details )
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
		}
		$NbEffet = 0;
		$SiErreur = "";
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
										break;
										exit;
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
							$Erreur 	= true;
							break;
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
							break;
						}
					
					break;
				
					// fin du Switch
				}
			}
			else
			{
				break;
			}
		} // Fin du For

		
		// On réalise l'effet s'il n'y a pas d'erreur
		if ( !$Erreur )
		{
			if ( is_array($Cout) )
			{
				// On transmet les ressources au joueur
				$Transaction = Transaction($Partie, $Joueur, $Etat, $Cout, false);
			}
			Message($Partie, $Joueur, "Nouvelle Action : " . $NbEffet, $ActionNom, 0, "", "noire", 10);
		}
		else
		{
			Message($Partie, $Joueur, "Action", "Erreur dans la création de cette action", 0, "", "noire", 10);
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

		$messages = LireMessages($Partie, $Joueur, $TimeMin, $TimeMax, "", $Source, false, true);
		
		foreach ($messages as $data) 
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
		
		$message = "<table cellspacing='0' cellpadding='0'>" . $message . "</table>";
	break;
}
mysql_close();
	
echo $message;

?>