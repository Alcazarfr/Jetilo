<?php

$mode = $_POST['mode'] ? $_POST['mode'] : ( $_GET['mode'] ? $_GET['mode'] : 'aucun');
include('fonctions.php');
$message = "";

if ( !$mode )
{
	$message = "Aucun mode???";
}

/*

Tableau Statistiques détaillés
Ajouter centre de commerce
FAIT Ajouter Généraux
Ajouter Technologies
Ajouter infobulle sur les liens pour indiquer les prix
Ajouter div toujours en haut (Ressources, prod, liens)
Ajouter Div edouard pour les actions

FAIT Ajouter: que chaqun puissent créer des cartes / échanges / villages, que les admin ne fassent que valider
FAIT Changer le classement des régions (par tailles, ressources, imagesetc.)

Pour les technologies:
- Commerce: nombre de centre de commerce max = 2 au début, nombre de marchand = 2
- Technologie 1: AJOUTER + 1 au 2 par exemple
- Technologie 2
-> Privilégier les +1, +3, +5 à un nouveau nombre => Pour permettre la création des avantages au départ.

NON Reaload auto
Historique, classement...
Culture nulle...

$file=fopen("test.txt","a");
fwrite($file,"[".date("d/m/Y H:i:s")."] Connecté\r\n");
foreach ($_GET as $k=>$v)
	{
	fwrite($file,"[".date("d/m/Y H:i:s")."] in \$_GET: $k => \"$v\" \r\n");
	}
foreach ($_POST as $k=>$v)
	{
	fwrite($file,"[".date("d/m/Y H:i:s")."] in \$_POST: $k => \"$v\" \r\n");
	}
fwrite($file,"[".date("d/m/Y H:i:s")."] DeconnectÈ\r\n\r\n");
fclose($file);
*/

/*

Aucun
Annuler: une construction
Valider: les constructions
Construire: ajouter un projet
liste-construction
statistiques
ressources: tableau des ressources
liste: des régions

--- Admin
créer

*/
switch ( $mode )
{
	case "aucun":
		$message = "Aucun mode...";
	break;

	case "exportation":
		$Data 		= $_POST['editorId'];
		$Valeur 	= $_POST['value'];
		$MarchandID = $_POST['MarchandID'];
		
		$Analyse	= explode("/", $Data);
		$MarchandID	= $Analyse[1];
		$Ressource	= ( $Analyse[2] == "Or" ) ? "MarchandDealOrValeur" : "MarchandDealRessourceValeur";
		
		if ( is_numeric($Valeur) == FALSE )
		{
			$message = "Erreur";
		}
		else
		{
			connectMaBase();

			$RessourceExportee = $Valeur;
			
			$sql = "SELECT m.*, r.*
				FROM marchand m, region r
				WHERE m.MarchandID = " . $MarchandID . " 
				AND r.RegionID = m.MarchandRegion";
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				$RegionProprietaire = $data['RegionProprietaire'];
				$RegionID 			= $data['RegionID'];
				$RegionNom 			= $data['RegionNom'];
				$RegionPartie 		= $data['RegionPartie'];
				$RegionRessource	= $data['RegionRessource'];
				$MarchandJoueur		= $data['MarchandJoueur'];
				$CommerceMarchands	= $data['CommerceMarchands'];
				$VilleCommerce		= $data['VilleCommerce'];
				
				if ( $Ressource == "MarchandDealRessourceValeur" )
				{
					$RessourceMax		= ( $data['VilleEconomie'] == 3 ) ? 7 : ( ( $data['VilleEconomie'] == 2 ) ? 4 : ( ( $data['VilleEconomie'] == 1 ) ? 2 : 1) );
				}
				else
				{
					$RessourceMax		= ( $data['VilleTaille'] == 3 ) ? 4 : $data['VilleTaille'];
				}
			}
			mysql_free_result($req);


			$sql = "SELECT MarchandID, MarchandDealOrValeur, MarchandDealRessourceValeur
				FROM marchand
				WHERE MarchandRegion = " . $RegionID;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{
				if ( $Ressource == "MarchandDealOrValeur" && $data["MarchandID"] != $MarchandID )
				{
					$RessourceExportee += $data["MarchandDealOrValeur"];
				}
				else if ( $data["MarchandID"] != $MarchandID )
				{
					$RessourceExportee += $data["MarchandDealRessourceValeur"];
				}
			}
			mysql_free_result($req);
			
			$sql = "SELECT TechProdOr, TechProdBle, TechProdBois, TechProdPierre, TechProdFer
				FROM joueur
				WHERE JoueurID = " . $RegionProprietaire;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				$TechProdOr		= $data['TechProdOr'];
				$TechProdBle	= $data['TechProdBle'];
				$TechProdBois	= $data['TechProdBois'];
				$TechProdPierre	= $data['TechProdPierre'];
				$TechProdFer	= $data['TechProdFer'];
			}
			mysql_free_result($req);
			
			if ( $Ressource == "MarchandDealRessourceValeur" )
			{
				switch ( $RegionRessource )
				{
					case "BLE":
						$multiple = $TechProdBle;
					break;
					case "BOIS":
						$multiple = $TechProdBois;
					break;
					case "PIERRE":
						$multiple = $TechProdPierre;
					break;
					case "FER":
						$multiple = $TechProdFer;
					break;
				}
			}
			else
			{
				$multiple = $TechProdOr;				
			}
			
			$RessourceMax *= $multiple;

			if ( $RessourceExportee > $RessourceMax )
			{
				$message = "E2";
				break;
			}
			$sql = "UPDATE marchand
					SET " . $Ressource . " = " . $Valeur . "
						WHERE MarchandID = " . $MarchandID;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			mysql_free_result($req);
			mysql_close();
		
			$message = $Valeur;
			
			// Code copié de plus bas
			sleep(1);
			
			// Mise à jour de la partie "Importation" et Exportation:
			
			
			$ImportationOr 		= 0;
			$ImportationBle 	= 0;
			$ImportationBois 	= 0;
			$ImportationPierre 	= 0;
			$ImportationFer 	= 0;
	
			$ExportationOr 		= 0;
			$ExportationBle 	= 0;
			$ExportationBois 	= 0;
			$ExportationPierre 	= 0;
			$ExportationFer 	= 0;
			
			connectMaBase();
			
			// Il faut mettre à jour les données (1) du proprio de la région; (2) du joueur du marchands
			
			// REGIONPROPRIETAIRE
			// Les marchands du RegionProprietaire à l'étranger
			
			$sql = "SELECT m.*, r.*, j.CommerceCoefficient
				FROM marchand m, region r, joueur j
					WHERE m.MarchandJoueur = " . $RegionProprietaire . "
					AND m.MarchandStatut = 1
					AND r.RegionID = m.MarchandRegion
					AND j.JoueurID = r.RegionProprietaire";
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{
				$RegionProprietaire = $data['RegionProprietaire'];
				
				$RegionRessource 	= $data['RegionRessource'];
				$DealOr				= $data['MarchandDealOr'];
				$DealBle			= $data['MarchandDealBle'];
				$DealBois			= $data['MarchandDealBois'];
				$DealPierre			= $data['MarchandDealPierre'];
				$DealFer			= $data['MarchandDealFer'];
				$DealOrValeur		= $data['MarchandDealOrValeur'];
				$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
				
				$Technologie 		= $data['CommerceCoefficient'];
				
				$ExportationOr		+= $DealOr * $DealRessourceValeur;
				$ExportationBle		+= $DealBle * $DealRessourceValeur;
				$ExportationBois	+= $DealBois * $DealRessourceValeur;
				$ExportationPierre	+= $DealPierre * $DealRessourceValeur;
				$ExportationFer		+= $DealFer * $DealRessourceValeur;

				$ExportationOr		+= $DealOr * $DealOrValeur;
				$ExportationBle		+= $DealBle * $DealOrValeur;
				$ExportationBois	+= $DealBois * $DealOrValeur;
				$ExportationPierre	+= $DealPierre * $DealOrValeur;
				$ExportationFer		+= $DealFer * $DealOrValeur;
			
				$ImportationOr		+= ( $DealOrValeur * $Technologie );
				$ImportationBle		+= ( $RegionRessource == "BLE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationBois	+= ( $RegionRessource == "BOIS" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationPierre	+= ( $RegionRessource == "PIERRE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationFer		+= ( $RegionRessource == "FER" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
	
			}
			// Les marchands qui sont dans les postes de comm du RegionProprietaire
			$sql = "SELECT m.*, r.*
				FROM marchand m, region r
					WHERE m.MarchandStatut = 1
					AND r.RegionID = m.MarchandRegion
					AND r.RegionProprietaire = " . $RegionProprietaire;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{
				$RegionRessource = $data['RegionRessource'];
				$DealOr				= $data['MarchandDealOr'];
				$DealBle			= $data['MarchandDealBle'];
				$DealBois			= $data['MarchandDealBois'];
				$DealPierre			= $data['MarchandDealPierre'];
				$DealFer			= $data['MarchandDealFer'];
				$DealOrValeur		= $data['MarchandDealOrValeur'];
				$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
				
				$ImportationOr		+= $DealOr * $DealRessourceValeur;
				$ImportationBle		+= $DealBle * $DealRessourceValeur;
				$ImportationBois	+= $DealBois * $DealRessourceValeur;
				$ImportationPierre	+= $DealPierre * $DealRessourceValeur;
				$ImportationFer		+= $DealFer * $DealRessourceValeur;
				
				$ImportationOr		+= $DealOr * $DealOrValeur;
				$ImportationBle		+= $DealBle * $DealOrValeur;
				$ImportationBois	+= $DealBois * $DealOrValeur;
				$ImportationPierre	+= $DealPierre * $DealOrValeur;
				$ImportationFer		+= $DealFer * $DealOrValeur;
				
				$ExportationOr		+= $DealOrValeur;
				$ExportationBle		+= ( $RegionRessource == "BLE" ) ? $DealRessourceValeur : 0;
				$ExportationBois	+= ( $RegionRessource == "BOIS" ) ? $DealRessourceValeur : 0;
				$ExportationPierre	+= ( $RegionRessource == "PIERRE" ) ? $DealRessourceValeur : 0;
				$ExportationFer		+= ( $RegionRessource == "FER" ) ? $DealRessourceValeur : 0;
			}
	
			$sql = "UPDATE joueur
				SET ImportationOr = " . $ImportationOr . ", ImportationBle = " . $ImportationBle . ", ImportationBois = " . $ImportationBois . ", ImportationPierre = " . $ImportationPierre . ", ImportationFer = " . $ImportationFer . ", ExportationOr = " . $ExportationOr . ", ExportationBle = " . $ExportationBle . ", ExportationBois = " . $ExportationBois . ", ExportationPierre = " . $ExportationPierre . ", ExportationFer = " . $ExportationFer . "
					WHERE JoueurID = " . $RegionProprietaire;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	
			sleep(1);
	
			// MARCHANDJOUEUR
			
			$ImportationOr 		= 0;
			$ImportationBle 	= 0;
			$ImportationBois 	= 0;
			$ImportationPierre 	= 0;
			$ImportationFer 	= 0;
	
			$ExportationOr 		= 0;
			$ExportationBle 	= 0;
			$ExportationBois 	= 0;
			$ExportationPierre 	= 0;
			$ExportationFer 	= 0;
			
			// Les marchands qui sont dans les postes de comm du MarchandJoueur
			$sql = "SELECT m.*, r.*
				FROM marchand m, region r
					WHERE m.MarchandStatut = 1
					AND r.RegionID = m.MarchandRegion
					AND r.RegionProprietaire = " . $MarchandJoueur;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{			
				$RegionRessource = $data['RegionRessource'];
				$DealOr				= $data['MarchandDealOr'];
				$DealBle			= $data['MarchandDealBle'];
				$DealBois			= $data['MarchandDealBois'];
				$DealPierre			= $data['MarchandDealPierre'];
				$DealFer			= $data['MarchandDealFer'];
				$DealOrValeur		= $data['MarchandDealOrValeur'];
				$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
				
				$ImportationOr		+= $DealOr * $DealRessourceValeur;
				$ImportationBle		+= $DealBle * $DealRessourceValeur;
				$ImportationBois	+= $DealBois * $DealRessourceValeur;
				$ImportationPierre	+= $DealPierre * $DealRessourceValeur;
				$ImportationFer		+= $DealFer * $DealRessourceValeur;
				$ImportationOr		+= $DealOr * $DealOrValeur;
				$ImportationBle		+= $DealBle * $DealOrValeur;
				$ImportationBois	+= $DealBois * $DealOrValeur;
				$ImportationPierre	+= $DealPierre * $DealOrValeur;
				$ImportationFer		+= $DealFer * $DealOrValeur;
				
				$ExportationOr		+= $DealOrValeur;
				$ExportationBle		+= ( $RegionRessource == "BLE" ) ? $DealRessourceValeur : 0;
				$ExportationBois	+= ( $RegionRessource == "BOIS" ) ? $DealRessourceValeur : 0;
				$ExportationPierre	+= ( $RegionRessource == "PIERRE" ) ? $DealRessourceValeur : 0;
				$ExportationFer		+= ( $RegionRessource == "FER" ) ? $DealRessourceValeur : 0;
			}
	
			// Les marchands du MarchandJoueur
			$sql = "SELECT m.*, r.*, j.CommerceCoefficient
				FROM marchand m, region r, joueur j
					WHERE m.MarchandJoueur = " . $MarchandJoueur . "
					AND m.MarchandStatut = 1
					AND r.RegionID = m.MarchandRegion
					AND j.JoueurID = r.RegionProprietaire";
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{
				$RegionProprietaire = $data['RegionProprietaire'];
				$RegionRessource 	= $data['RegionRessource'];
				$DealOr				= $data['MarchandDealOr'];
				$DealBle			= $data['MarchandDealBle'];
				$DealBois			= $data['MarchandDealBois'];
				$DealPierre			= $data['MarchandDealPierre'];
				$DealFer			= $data['MarchandDealFer'];
				$DealOrValeur		= $data['MarchandDealOrValeur'];
				$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
				
				$Technologie 		= $data['CommerceCoefficient'];
				
				$ExportationOr		+= $DealOr * $DealRessourceValeur;
				$ExportationBle		+= $DealBle * $DealRessourceValeur;
				$ExportationBois	+= $DealBois * $DealRessourceValeur;
				$ExportationPierre	+= $DealPierre * $DealRessourceValeur;
				$ExportationFer		+= $DealFer * $DealRessourceValeur;
				
				$ExportationOr		+= $DealOr * $DealOrValeur;
				$ExportationBle		+= $DealBle * $DealOrValeur;
				$ExportationBois	+= $DealBois * $DealOrValeur;
				$ExportationPierre	+= $DealPierre * $DealOrValeur;
				$ExportationFer		+= $DealFer * $DealOrValeur;		
						
				$ImportationOr		+= ( $DealOrValeur * $Technologie );
				$ImportationBle		+= ( $RegionRessource == "BLE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationBois	+= ( $RegionRessource == "BOIS" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationPierre	+= ( $RegionRessource == "PIERRE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
				$ImportationFer		+= ( $RegionRessource == "FER" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
	
			}
			
			
			$sql = "UPDATE joueur
				SET ImportationOr = " . $ImportationOr . ", ImportationBle = " . $ImportationBle . ", ImportationBois = " . $ImportationBois . ", ImportationPierre = " . $ImportationPierre . ", ImportationFer = " . $ImportationFer . ", ExportationOr = " . $ExportationOr . ", ExportationBle = " . $ExportationBle . ", ExportationBois = " . $ExportationBois . ", ExportationPierre = " . $ExportationPierre . ", ExportationFer = " . $ExportationFer . "
					WHERE JoueurID = " . $MarchandJoueur;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	
			mysql_close();
		
			$MarchandJoueurNom = joueur($MarchandJoueur, "JoueurPseudo");
	
			$historiqueProprietaire		= htmlspecialchars(addslashes("Vous avez changé le volume des exportations de <b>" . $MarchandJoueurNom . "</b> à <i>" .$RegionNom . "</i>"));
			
			$historiqueMarchand		= htmlspecialchars(addslashes("Le volume des importations de <b>votre</b> marchand à <i>" .$RegionNom . "</i> a changé"));
			$Tour				= tour($RegionPartie, "PartieTour");
			

			message($RegionProprietaire, $historiqueProprietaire, $Tour, $RegionPartie);
			message($MarchandJoueur, $historiqueMarchand, $Tour, $RegionPartie);
		}

	break;
	
	case "messagePersoAjouter":
		$Partie 		= $_POST['Partie'];
		$Joueur 		= $_POST['Joueur'];
		$Message 		= $_POST['MP'];
		$Destinataire 	= $_POST['DestinataireMP'];
	
		if ( $Message == "Tapez votre message..." )
		{
			break;
		}
		$JoueurNom 		= joueur($Joueur, "JoueurPseudo");					
		$DestinataireNom = joueur($Destinataire, "JoueurPseudo");
		
		$TexteDestinataire	= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> vous a envoyé un message privé: <i>" . $Message ."</i>"));
		$TexteJoueur		= htmlspecialchars(addslashes("Vous avez envoyé un message privé à <b>".$DestinataireNom . "</b>: <i>" . $Message ."</i>"));
		$Tour			= tour($Partie, "PartieTour");
		message($Destinataire, $TexteDestinataire, $Tour, $Partie);
		message($Joueur, $TexteJoueur, $Tour, $Partie);

	break;

	case "priorite":
		$Data 		= $_POST['editorId'];
		$Valeur 	= $_POST['value'];
		
		$Analyse	= explode("/", $Data);
		$RegionID	= $Analyse[1];
		
		if ( is_numeric($Valeur) == FALSE )
		{
			$message = "Erreur";
		}
		else
		{
			connectMaBase();

			$sql = "UPDATE region
					SET RegionPriorite = " . $Valeur . "
						WHERE RegionID = " . $RegionID;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			mysql_free_result($req);
			mysql_close();
		
			$message = $Valeur;
			
			// Code copié de plus bas
			sleep(1);
		}
	break;

	
	case "importationOffre":
		$Data 		= $_POST['editorId'];
		$Valeur 	= $_POST['value'];
		$Analyse	= explode("/", $Data);
		$Ressource	= $Analyse[0];
		$RegionID	= $Analyse[1];
		$MarchandID	= $Analyse[2];

		if ( is_numeric($Valeur) == FALSE )
		{
			$message = "Erreur.";
		}
		else
		{
			$Ressource	= "MarchandDeal" . $Ressource;
	
			connectMaBase();
			$sql = "SELECT m.*, r.*
				FROM marchand m, region r
					WHERE m.MarchandID = " . $MarchandID . " 
					AND r.RegionID = m.MarchandRegion";
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				$RegionProprietaire = $data['RegionProprietaire'];
				$RegionID 			= $data['RegionID'];
				$RegionNom 			= $data['RegionNom'];
				$RegionPartie 		= $data['RegionPartie'];
				$MarchandJoueur		= $data['MarchandJoueur'];
				$CommerceMarchands	= $data['CommerceMarchands'];
				$MarchandStatutEx	= $data['MarchandStatut'];
				$VilleCommerce		= $data['VilleCommerce'];
			}
			mysql_free_result($req);
			
			$sql = "UPDATE marchand
					SET " . $Ressource . " = " . $Valeur . "
						WHERE MarchandID = " . $MarchandID;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			mysql_free_result($req);
			mysql_close();
			$message = $Valeur;
	}
	
// ___________________________________________________________

	case "marchand":
		// Changer le statut (Actif / En attente)
		$MarchandID 	= ( $mode == "marchand" ) ? $_POST['MarchandID'] : $MarchandID;
		$MarchandStatut = ( $mode == "marchand" ) ? $_POST['MarchandStatut'] : 0;
		$MarchandStatutTexte = ( $MarchandStatut == 0 ) ? "en attente" : "en activité";
		connectMaBase();
		$sql = "SELECT m.*, r.*
			FROM marchand m, region r
				WHERE m.MarchandID = " . $MarchandID . " 
				AND r.RegionID = m.MarchandRegion";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$RegionProprietaire = $data['RegionProprietaire'];
			$RegionID 			= $data['RegionID'];
			$RegionNom 			= $data['RegionNom'];
			$RegionPartie 		= $data['RegionPartie'];
			$MarchandJoueur		= $data['MarchandJoueur'];
			$CommerceMarchands	= $data['CommerceMarchands'];
			$MarchandStatutEx	= $data['MarchandStatut'];
			$VilleCommerce		= $data['VilleCommerce'];
		}
		mysql_free_result($req);

		if ( $VilleCommerce <= $CommerceMarchands && $MarchandStatut == 1 )
		{
			$message = "Vous ne pouvez pas accepter ce marchand: la taille de votre centre de commerce ne vous permet pas plus de " . $VilleCommerce . " marchands actifs";
			break;
		}
		$sql = "UPDATE marchand
				SET MarchandStatut = " . $MarchandStatut . ", MarchandDealOrValeur = 0, MarchandDealRessourceValeur = 0
					WHERE MarchandID = " . $MarchandID;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());


		$NombreActifChangement 	= ( $MarchandStatut == 0 ) ? -1 : 1;
		$NombrePassifChangement = ( $MarchandStatut == 1 ) ? -1 : 1;
		
		/*
		if ( $MarchandJoueur != $JoueurID )
		{
			$sql = "UPDATE joueur
				SET CommerceMarchandsActifNombre = CommerceMarchandsActifNombre + " . $NombreActifChangement . ", CommerceMarchandsPassifNombre = CommerceMarchandsPassifNombre + " . $NombrePassifChangement . "
					WHERE JoueurID = " . $MarchandJoueur;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		}
		$sql = "UPDATE joueur
			SET CommerceMarchands = CommerceMarchands + " . $NombreChangement . "
				WHERE JoueurID = " . $RegionProprietaire;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		*/
		
		if ( ( $MarchandStatutEx == 0 && $MarchandStatut != 0 ) || $MarchandStatutEx == 1 )
		{
			$sql = "UPDATE region
				SET CommerceMarchands = CommerceMarchands + " . $NombreActifChangement . "
					WHERE RegionID = " . $RegionID;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		}

		$message = ( $mode == "importationOffre" ) ? $Valeur :"Le statut du Marchand a été changé avec succès";
		
		if ( $mode == "importationOffre" ) 
		{	
			$MarchandJoueurNom = joueur($MarchandJoueur, "JoueurPseudo");
	
			$historiqueProprietaire		= htmlspecialchars(addslashes("Le marchand de <b>" . $MarchandJoueurNom . "</b> à <i>" .$RegionNom . "</i> a changé son offre"));
			
			$historiqueMarchand		= htmlspecialchars(addslashes("Votre marchand à <i>" .$RegionNom . "</i> a changé son offre"));
		}
		else
		{
			$MarchandJoueurNom = joueur($MarchandJoueur, "JoueurPseudo");

			$historiqueProprietaire		= htmlspecialchars(addslashes("Le statut d'un marchand de <b>" . $MarchandJoueurNom . "</b> à <i>" .$RegionNom . "</i> a changé: il est désormais " . $MarchandStatutTexte));
		
			$historiqueMarchand		= htmlspecialchars(addslashes("Le statut de <b>votre</b> marchand à <i>" .$RegionNom . "</i> a changé: il est désormais " . $MarchandStatutTexte));
		}
		
		sleep(2);
		
		// Mise à jour de la partie "Importation" et Exportation:
		
		
		$ImportationOr 		= 0;
		$ImportationBle 	= 0;
		$ImportationBois 	= 0;
		$ImportationPierre 	= 0;
		$ImportationFer 	= 0;

		$ExportationOr 		= 0;
		$ExportationBle 	= 0;
		$ExportationBois 	= 0;
		$ExportationPierre 	= 0;
		$ExportationFer 	= 0;
		
		connectMaBase();
		
		// Il faut mettre à jour les données (1) du proprio de la région; (2) du joueur du marchands
		
		// REGIONPROPRIETAIRE
		// Les marchands du RegionProprietaire à l'étranger
		
		$sql = "SELECT m.*, r.*, j.CommerceCoefficient
			FROM marchand m, region r, joueur j
				WHERE m.MarchandJoueur = " . $RegionProprietaire . "
				AND m.MarchandStatut = 1
				AND r.RegionID = m.MarchandRegion
				AND j.JoueurID = r.RegionProprietaire";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$RegionProprietaire = $data['RegionProprietaire'];
			$RegionRessource 	= $data['RegionRessource'];
			$DealOr				= $data['MarchandDealOr'];
			$DealBle			= $data['MarchandDealBle'];
			$DealBois			= $data['MarchandDealBois'];
			$DealPierre			= $data['MarchandDealPierre'];
			$DealFer			= $data['MarchandDealFer'];
			$DealOrValeur		= $data['MarchandDealOrValeur'];
			$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
			
			$Technologie 		= $data['CommerceCoefficient'];
			
			$ExportationOr		+= $DealOr * $DealRessourceValeur;
			$ExportationBle		+= $DealBle * $DealRessourceValeur;
			$ExportationBois	+= $DealBois * $DealRessourceValeur;
			$ExportationPierre	+= $DealPierre * $DealRessourceValeur;
			$ExportationFer		+= $DealFer * $DealRessourceValeur;
			$ExportationOr		+= $DealOr * $DealOrValeur;
			$ExportationBle		+= $DealBle * $DealOrValeur;
			$ExportationBois	+= $DealBois * $DealOrValeur;
			$ExportationPierre	+= $DealPierre * $DealOrValeur;
			$ExportationFer		+= $DealFer * $DealOrValeur;
			
			$ImportationOr		+= ( $DealOrValeur * $Technologie );
			$ImportationBle		+= ( $RegionRessource == "BLE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationBois	+= ( $RegionRessource == "BOIS" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationPierre	+= ( $RegionRessource == "PIERRE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationFer		+= ( $RegionRessource == "FER" ) ? ( $DealRessourceValeur * $Technologie ) : 0;

		}
		// Les marchands qui sont dans les postes de comm du RegionProprietaire
		$sql = "SELECT m.*, r.*
			FROM marchand m, region r
				WHERE m.MarchandStatut = 1
				AND r.RegionID = m.MarchandRegion
				AND r.RegionProprietaire = " . $RegionProprietaire;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{			
			$RegionRessource = $data['RegionRessource'];
			$DealOr				= $data['MarchandDealOr'];
			$DealBle			= $data['MarchandDealBle'];
			$DealBois			= $data['MarchandDealBois'];
			$DealPierre			= $data['MarchandDealPierre'];
			$DealFer			= $data['MarchandDealFer'];
			$DealOrValeur		= $data['MarchandDealOrValeur'];
			$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
			
			$ImportationOr		+= $DealOr * $DealRessourceValeur;
			$ImportationBle		+= $DealBle * $DealRessourceValeur;
			$ImportationBois	+= $DealBois * $DealRessourceValeur;
			$ImportationPierre	+= $DealPierre * $DealRessourceValeur;
			$ImportationFer		+= $DealFer * $DealRessourceValeur;
			$ImportationOr		+= $DealOr * $DealOrValeur;
			$ImportationBle		+= $DealBle * $DealOrValeur;
			$ImportationBois	+= $DealBois * $DealOrValeur;
			$ImportationPierre	+= $DealPierre * $DealOrValeur;
			$ImportationFer		+= $DealFer * $DealOrValeur;
			
			$ExportationOr		+= $DealOrValeur;
			$ExportationBle		+= ( $RegionRessource == "BLE" ) ? $DealRessourceValeur : 0;
			$ExportationBois	+= ( $RegionRessource == "BOIS" ) ? $DealRessourceValeur : 0;
			$ExportationPierre	+= ( $RegionRessource == "PIERRE" ) ? $DealRessourceValeur : 0;
			$ExportationFer		+= ( $RegionRessource == "FER" ) ? $DealRessourceValeur : 0;
		}

		$sql = "UPDATE joueur
			SET ImportationOr = " . $ImportationOr . ", ImportationBle = " . $ImportationBle . ", ImportationBois = " . $ImportationBois . ", ImportationPierre = " . $ImportationPierre . ", ImportationFer = " . $ImportationFer . ", ExportationOr = " . $ExportationOr . ", ExportationBle = " . $ExportationBle . ", ExportationBois = " . $ExportationBois . ", ExportationPierre = " . $ExportationPierre . ", ExportationFer = " . $ExportationFer . "
				WHERE JoueurID = " . $RegionProprietaire;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		sleep(1);

		// MARCHANDJOUEUR
		
		$ImportationOr 		= 0;
		$ImportationBle 	= 0;
		$ImportationBois 	= 0;
		$ImportationPierre 	= 0;
		$ImportationFer 	= 0;

		$ExportationOr 		= 0;
		$ExportationBle 	= 0;
		$ExportationBois 	= 0;
		$ExportationPierre 	= 0;
		$ExportationFer 	= 0;
		
		// Les marchands qui sont dans les postes de comm du MarchandJoueur
		$sql = "SELECT m.*, r.*
			FROM marchand m, region r
				WHERE m.MarchandStatut = 1
				AND r.RegionID = m.MarchandRegion
				AND r.RegionProprietaire = " . $MarchandJoueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{			
			$RegionRessource = $data['RegionRessource'];
			$DealOr				= $data['MarchandDealOr'];
			$DealBle			= $data['MarchandDealBle'];
			$DealBois			= $data['MarchandDealBois'];
			$DealPierre			= $data['MarchandDealPierre'];
			$DealFer			= $data['MarchandDealFer'];
			$DealOrValeur		= $data['MarchandDealOrValeur'];
			$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
			
			$ImportationOr		+= $DealOr * $DealRessourceValeur;
			$ImportationBle		+= $DealBle * $DealRessourceValeur;
			$ImportationBois	+= $DealBois * $DealRessourceValeur;
			$ImportationPierre	+= $DealPierre * $DealRessourceValeur;
			$ImportationFer		+= $DealFer * $DealRessourceValeur;
			$ImportationOr		+= $DealOr * $DealOrValeur;
			$ImportationBle		+= $DealBle * $DealOrValeur;
			$ImportationBois	+= $DealBois * $DealOrValeur;
			$ImportationPierre	+= $DealPierre * $DealOrValeur;
			$ImportationFer		+= $DealFer * $DealOrValeur;
			
			$ExportationOr		+= $DealOrValeur;
			$ExportationBle		+= ( $RegionRessource == "BLE" ) ? $DealRessourceValeur : 0;
			$ExportationBois	+= ( $RegionRessource == "BOIS" ) ? $DealRessourceValeur : 0;
			$ExportationPierre	+= ( $RegionRessource == "PIERRE" ) ? $DealRessourceValeur : 0;
			$ExportationFer		+= ( $RegionRessource == "FER" ) ? $DealRessourceValeur : 0;
		}

		// Les marchands du MarchandJoueur
		$sql = "SELECT m.*, r.*, j.CommerceCoefficient
			FROM marchand m, region r, joueur j
				WHERE m.MarchandJoueur = " . $MarchandJoueur . "
				AND m.MarchandStatut = 1
				AND r.RegionID = m.MarchandRegion
				AND j.JoueurID = r.RegionProprietaire";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$RegionProprietaire = $data['RegionProprietaire'];
			$RegionRessource 	= $data['RegionRessource'];
			$DealOr				= $data['MarchandDealOr'];
			$DealBle			= $data['MarchandDealBle'];
			$DealBois			= $data['MarchandDealBois'];
			$DealPierre			= $data['MarchandDealPierre'];
			$DealFer			= $data['MarchandDealFer'];
			$DealOrValeur		= $data['MarchandDealOrValeur'];
			$DealRessourceValeur= $data['MarchandDealRessourceValeur'];
			
			$Technologie 		= $data['CommerceCoefficient'];
			
			$ExportationOr		+= $DealOr * $DealRessourceValeur;
			$ExportationBle		+= $DealBle * $DealRessourceValeur;
			$ExportationBois	+= $DealBois * $DealRessourceValeur;
			$ExportationPierre	+= $DealPierre * $DealRessourceValeur;
			$ExportationFer		+= $DealFer * $DealRessourceValeur;
			$ExportationOr		+= $DealOr * $DealOrValeur;
			$ExportationBle		+= $DealBle * $DealOrValeur;
			$ExportationBois	+= $DealBois * $DealOrValeur;
			$ExportationPierre	+= $DealPierre * $DealOrValeur;
			$ExportationFer		+= $DealFer * $DealOrValeur;
			
			$ImportationOr		+= ( $DealOrValeur * $Technologie );
			$ImportationBle		+= ( $RegionRessource == "BLE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationBois	+= ( $RegionRessource == "BOIS" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationPierre	+= ( $RegionRessource == "PIERRE" ) ? ( $DealRessourceValeur * $Technologie ) : 0;
			$ImportationFer		+= ( $RegionRessource == "FER" ) ? ( $DealRessourceValeur * $Technologie ) : 0;

		}
		
		
		$sql = "UPDATE joueur
			SET ImportationOr = " . $ImportationOr . ", ImportationBle = " . $ImportationBle . ", ImportationBois = " . $ImportationBois . ", ImportationPierre = " . $ImportationPierre . ", ImportationFer = " . $ImportationFer . ", ExportationOr = " . $ExportationOr . ", ExportationBle = " . $ExportationBle . ", ExportationBois = " . $ExportationBois . ", ExportationPierre = " . $ExportationPierre . ", ExportationFer = " . $ExportationFer . "
				WHERE JoueurID = " . $MarchandJoueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		mysql_close();
		
		
		$Tour				= tour($RegionPartie, "PartieTour");
		message($RegionProprietaire, $historiqueProprietaire, $Tour, $RegionPartie);
		message($MarchandJoueur, $historiqueMarchand, $Tour, $RegionPartie);
	
	break;

// ___________________________________________________________
	
	case "marchandretirer":
		// Supprimer un marchand
		$MarchandID 	= $_POST['MarchandID'];
		$JoueurID 		= $_POST['JoueurID'];

		connectMaBase();
		$sql = "SELECT m.*, r.RegionProprietaire, r.RegionNom, r.RegionPartie
			FROM marchand m, region r
				WHERE r.RegionID = m.MarchandRegion";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$RegionProprietaire = $data['RegionProprietaire'];
			$RegionID 			= $data['MarchandRegion'];
			$RegionNom 			= $data['RegionNom'];
			$RegionPartie 		= $data['RegionPartie'];
			$MarchandJoueur		= $data['MarchandJoueur'];
			$MarchandStatut		= $data['MarchandStatut'];
		}
		mysql_free_result($req);

		if ( $MarchandStatut == 1 )
		{
			$sql = "UPDATE region
				SET CommerceMarchands = CommerceMarchands - 1
					WHERE RegionID = " . $RegionID;
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		}
		$sql4 = "UPDATE joueur
			SET CommerceMarchandsEtrangersNombre = CommerceMarchandsEtrangersNombre - 1
				WHERE JoueurID = " . $RegionProprietaire;
		mysql_query($sql4) or die('Erreur SQL !<br />'.$sql4.'<br />'.mysql_error());
								
		$sql = "UPDATE joueur
			SET CommerceMarchandsNombre = CommerceMarchandsNombre - 1
				WHERE JoueurID = " . $MarchandJoueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		$sql = "DELETE FROM marchand
			WHERE MarchandID = " . $MarchandID;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_close();
		$message = "Ce marchand n'est plus sur place";
		
		$MarchandNom 		= joueur($MarchandJoueur, "JoueurPseudo");
		$ProprietaireNom 	= joueur($RegionProprietaire, "JoueurPseudo");
		if ( $RegionProprietaire == $JoueurID )
		{
			$historique		= htmlspecialchars(addslashes("<b>".$ProprietaireNom . "</b> a congédié le marchand de <b>" . $MarchandNom . "</b> de sa région <i>" . $RegionNom . "</i>"));
		}
		else
		{
			$historique		= htmlspecialchars(addslashes("<b>".$MarchandNom . "</b> a retiré son marchand de la région <i>" . $RegionNom . "</i> de <b>".$ProprietaireNom . "</b>"));
		}
		$Tour				= tour($RegionPartie, "PartieTour");
		message(0, $historique, $Tour, $RegionPartie);
		$historiqueMarchand		= htmlspecialchars(addslashes("<b>".$ProprietaireNom . "</b> a congédié <b>votre</b> marchand de sa région <i>" . $RegionNom . "</i>"));

		message($MarchandJoueur, $historiqueMarchand, $Tour, $RegionPartie);
	break;
	
	case "importation":
		$Data 		= $_POST['editorId'];
		$Valeur 	= $_POST['value'];
		$Analyse	= explode("/", $Data);
		$RegionID	= $Analyse[1];
		$Ressource	= ( $Analyse[2] == "Or" ) ? "MarchandImportationOr" : "MarchandImportationRessource";
		connectMaBase();
		
		$sql = "UPDATE marchand
				SET " . $Ressource . " = " . $Valeur . "
					WHERE MarchandID = " . $MarchandID;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		mysql_close();
		$message = $Valeur;
	break;

	case "commerce":
	// Vos postes de commerce
		$message = '<table width="900px" cellpadding="5px">';
		$message .= '<tr>
			<td width="20%">				<b>Région</b>		</td>
			<td width="10%">				<b>Ressources</b>	</td>
			<td width="15%">				<b>Taille</b>		</td>
			<td width="55%">				<b>Marchands</b>	</td>
			</tr>';

		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		$Ordre 		= $_POST['Ordre'];
		$Classement = $_POST['Classement'];
		
		$RegionID = 0;
		
		connectMaBase();

		$MesMarchands		=	Array();
		$MesPartenaires		=	Array();
		$MesPartenairesEnAttente = Array();
		$MesPartenairesNombre	= Array();
		$AutresMarchands	=	Array();
		
		$sql = "SELECT m.*, r.RegionProprietaire, r.RegionRessource
			FROM marchand m, region r
				WHERE r.RegionID = m.MarchandRegion
				ORDER BY m.MarchandStatut";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$MarchandID					= $data['MarchandID'];
			$MarchandJoueur				= $data['MarchandJoueur'];
			$MarchandRegion				= $data['MarchandRegion'];
			$MarchandStatut				= $data['MarchandStatut'];
			$MarchandDealOr				= $data['MarchandDealOr'];
			$MarchandDealBle			= $data['MarchandDealBle'];
			$MarchandDealBois			= $data['MarchandDealBois'];
			$MarchandDealPierre			= $data['MarchandDealPierre'];
			$MarchandDealFer			= $data['MarchandDealFer'];
			$MarchandDealOrValeur		= $data['MarchandDealOrValeur'];
			$MarchandDealRessourceValeur	= $data['MarchandDealRessourceValeur'];
			$RegionProprietaire			= $data['RegionProprietaire'];

			if ( $MarchandJoueur == $Joueur )
			{
				// Mes marchands chez les autres
				$MesMarchands[$MarchandRegion] = Array(
					"MarchandID" 		=> $MarchandID,
					"MarchandJoueur" 	=> $MarchandJoueur,
					"MarchandStatut" 	=> $MarchandStatut,
					"MarchandDealOr" 	=> $MarchandDealOr,
					"MarchandDealBle" 	=> $MarchandDealBle,
					"MarchandDealBois" 	=> $MarchandDealBois,
					"MarchandDealPierre" => $MarchandDealPierre,
					"MarchandDealFer" 	=> $MarchandDealFer,
					"MarchandDealOrValeur" 	=> $MarchandDealOrValeur,
					"MarchandDealRessourceValeur" 	=> $MarchandDealRessourceValeur
				);
			}
			else
			{
				// Les marchands des autres
				if ( $RegionProprietaire == $Joueur )
				{

					$MesPartenairesNombre[$MarchandRegion]++;
					$NumeroPartenaire = $MesPartenairesNombre[$MarchandRegion];
	
					// Les marchands des autres chez moi : MesPartenaires
					$MesPartenaires[$MarchandRegion][$NumeroPartenaire] = Array(
						"MarchandID" 		=> $MarchandID,
						"MarchandJoueur" 	=> $MarchandJoueur,
						"MarchandStatut" 	=> $MarchandStatut,
						"MarchandDealOr" 	=> $MarchandDealOr,
						"MarchandDealBle" 	=> $MarchandDealBle,
						"MarchandDealBois" 	=> $MarchandDealBois,
						"MarchandDealPierre" => $MarchandDealPierre,
						"MarchandDealFer" 	=> $MarchandDealFer,
						"MarchandDealOrValeur" 	=> $MarchandDealOrValeur,
						"MarchandDealRessourceValeur" 	=> $MarchandDealRessourceValeur
					);

					if ( $MarchandStatut == 0 )
					{
						$MesPartenairesEnAttente[$MarchandRegion]++;
					}
				}
				else
				{
					// Les marchands des autres chez les autres
				}
			}
		}
		
		$JoueurTechnologie = Joueur($Joueur, "CommerceCoefficient");
		
		
		connectMaBase();
		$sql = "SELECT TechProdOr, TechProdBle, TechProdBois, TechProdPierre, TechProdFer
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$TechProdOr		= $data['TechProdOr'];
			$TechProdBle	= $data['TechProdBle'];
			$TechProdBois	= $data['TechProdBois'];
			$TechProdPierre	= $data['TechProdPierre'];
			$TechProdFer	= $data['TechProdFer'];
		}
		
		$sql = "SELECT *
			FROM region
			WHERE RegionProprietaire = " . $Joueur . "
				AND RegionPartie = " . $Partie . "
				AND VilleCommerce > 0
				ORDER BY " . $Classement . " " . $Ordre ;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$RegionID			= $data['RegionID'];
			$RegionNom 			= $data['RegionNom']; 
			if ( $data['RegionOccupant'] != $Joueur && $data['RegionOccupant'] != -1 )
			{
				$OccupantNom = joueur($data['RegionOccupant'], "JoueurPseudo");
				$RegionNom .= "<br /><b>Occupée par <i>" . $OccupantNom . "</i></b>";
			}
			else if ( $data['RegionOccupant'] != $Joueur && $data['RegionOccupant'] == -1 )
			{
				$RegionNom .= "<br /><b>En révolte!</b>";			
			}
			$Region	 			= $RegionNom . "<br />" . cout("ville", $data['VilleTaille'], "nom") . " + " . cout("economie", $data['VilleEconomie'], "nom");

			$RessourceOr	 	= ( $data['VilleTaille'] == 3 ) ? 4 : $data['VilleTaille'];
			$Ressource			= ( $data['VilleEconomie'] == 3 ) ? 7 : ( ( $data['VilleEconomie'] == 2 ) ? 4 : ( ( $data['VilleEconomie'] == 1 ) ? 2 : 1) );
			$RegionRessource 	= ucfirst(strtolower($data['RegionRessource']));

			$CommerceTaille		= str_replace("de commerce", "", cout("commerce", $data['VilleCommerce'], "nom"));
			
			$Production			= ( $data['VilleEconomie'] == 3 ) ? 7 : ( ( $data['VilleEconomie'] == 2 ) ? 4 : ( ( $data['VilleEconomie'] == 1 ) ? 2 : 1) );
			
			switch ( $data['RegionRessource'] )
			{
				case "BLE":
					$multiple = $TechProdBle;
				break;
				case "BOIS":
					$multiple = $TechProdBois;
				break;
				case "PIERRE":
					$multiple = $TechProdPierre;
				break;
				case "FER":
					$multiple = $TechProdFer;
				break;
			}
			$Production			*= $multiple;
			
			$ExportationMaxOr	= 3;
			
			$MarchandsTexte		= "";
			$NombreDePartenaires	=	count($MesPartenaires[$RegionID]) + 1;
			if ( $MesPartenaires[$RegionID] )
			{
				for ( $i = 1; $i <= count($MesPartenaires[$RegionID]); $i++)
				{
					$MarchandJoueurNom = joueur($MesPartenaires[$RegionID][$i]["MarchandJoueur"], "JoueurPseudo");

					$TexteCompile	= "";
					$Texte[1] 	= ( $MesPartenaires[$RegionID][$i]["MarchandDealOr"] > 0 ) ? $MesPartenaires[$RegionID][$i]["MarchandDealOr"] . " or" : "";
					$Texte[2] 	= ( $MesPartenaires[$RegionID][$i]["MarchandDealBle"] > 0 ) ? $MesPartenaires[$RegionID][$i]["MarchandDealBle"] . " blé" : "";
					$Texte[3] 	= ( $MesPartenaires[$RegionID][$i]["MarchandDealBois"] > 0 ) ? $MesPartenaires[$RegionID][$i]["MarchandDealBois"] . " bois" : "";
					$Texte[4] 	= ( $MesPartenaires[$RegionID][$i]["MarchandDealPierre"] > 0 ) ? $MesPartenaires[$RegionID][$i]["MarchandDealPierre"] . " pierre" : "";
					$Texte[5] 	= ( $MesPartenaires[$RegionID][$i]["MarchandDealFer"] > 0 ) ? $MesPartenaires[$RegionID][$i]["MarchandDealFer"] . " fer" : "";
					
					$virgule = "";
					for ( $g = 1; $g < 6; $g++ )
					{
						$TexteCompile .= ( $Texte[$g] ) ? $virgule . $Texte[$g] : "";
						$virgule = ( $Texte[$g] || $virgule == ", ") ? ", " : "";
					}
					$TexteCompile = ( !$TexteCompile ) ? "rien pour le moment." : $TexteCompile;

					// X + RegionID + Ressource 
					$ExportationOrData			= "X/" . $MesPartenaires[$RegionID][$i]["MarchandID"] . "/Or";
					$ExportationRessourceData	= "X/" . $MesPartenaires[$RegionID][$i]["MarchandID"] . "/Ressource";
					
					$ExportationOr	= "<span id=\"" . $ExportationOrData . "\">" . $MesPartenaires[$RegionID][$i]["MarchandDealOrValeur"] . "</span> / " . $RessourceOr . " Or (" . $MesPartenaires[$RegionID][$i]["MarchandDealOrValeur"]*$JoueurTechnologie . " TTC)
						<script>
							var url = './includes/ajax.php?mode=exportation';
							var parametres 	= \"mode=exportation\";
							var myAjax = new Ajax.InPlaceEditor(
								$('" . $ExportationOrData . "'),
									url, 
										{ajaxOptions:
											{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
       									}
       							);
						</script>";
					$ExportationRessource	= "<span id=\"" . $ExportationRessourceData . "\">" . $MesPartenaires[$RegionID][$i]["MarchandDealRessourceValeur"] . "</span> / " . $Production . " " . $RegionRessource . " (" . $MesPartenaires[$RegionID][$i]["MarchandDealRessourceValeur"]*$JoueurTechnologie . " TTC)
						<script>
							var url = './includes/ajax.php?mode=exportation';
							var parametres 	= \"mode=exportation\";
							var myAjax = new Ajax.InPlaceEditor(
								$('" . $ExportationRessourceData . "'),
									url, 
										{ajaxOptions:
											{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
       									}
       							);
						</script>";
					if ( $MesPartenaires[$RegionID][$i]["MarchandStatut"] == 0 )
					{
						// Partenaires En attente
						$MarchandsTexte	.= "<tr><td>Marchand en attente de " . $MarchandJoueurNom . " qui propose " . $TexteCompile . ".<br /><a href=\"#MarRe".$MesPartenaires[$RegionID][$i]["MarchandID"]."\" onclick=\"marchandRetirer(".$MesPartenaires[$RegionID][$i]["MarchandID"].", ".$Joueur.")\">Retirer ce marchand</a> - <a href=\"#MarSt1".$MesPartenaires[$RegionID][$i]["MarchandID"]."\" onclick=\"marchandStatut(".$MesPartenaires[$RegionID][$i]["MarchandID"].", 1)\">Accepter l'offre</a></td></tr>";
					}
					else
					{
						// Partenaires en activité
						$MarchandsTexte	.= "<tr><td>Marchand en activité de " . $MarchandJoueurNom . " qui échange " . $TexteCompile . "<br />" . $ExportationOr . " et " . $ExportationRessource ."<br /><a href=\"#MarSt0".$MesPartenaires[$RegionID][$i]["MarchandID"]."\" onclick=\"marchandStatut(".$MesPartenaires[$RegionID][$i]["MarchandID"].", 0)\">Arretez les échanges</a> - <a href=\"#MarRe".$MesPartenaires[$RegionID][$i]["MarchandID"]."\" onclick=\"marchandRetirer(".$MesPartenaires[$RegionID][$i]["MarchandID"].", ".$Joueur.")\">Retirer ce marchand</a></td></tr>";
					}
				}
			}
			
			$Ressource			= $Production . " " . $RegionRessource . "<br />" . $RessourceOr*$TechProdOr . " Or";
			$MarchandsMax		= $data['VilleCommerce'];
			$MarchandsEnAttente	= ( $MesPartenairesEnAttente[$RegionID] ) ? $MesPartenairesEnAttente[$RegionID] : 0;
			$message .= "<tr><td rowspan=\"". $NombreDePartenaires ."\">".$Region."</td><td rowspan=\"". $NombreDePartenaires ."\">".$Ressource."</td><td rowspan=\"". $NombreDePartenaires ."\">".$CommerceTaille."</td><td>" . $data['CommerceMarchands'] . "/".$MarchandsMax." marchands en activité, ".$MarchandsEnAttente." en attente</td></tr>";
			$message .= $MarchandsTexte;
		}
		// Liste des marchands
		if ( !$RegionID ) 
		{
			$message .= '<tr><td colspan="4">Vous n\'avez pas commerce... Bon à rien!</td></tr>';
		}
		
		// Les postes de commerces des autres
/*
<h1 id="tobeeditedblur">To be edited w/ blur</h1>
<script>
new Ajax.InPlaceEditor($('tobeeditedblur'), '_ajax_inplaceeditor_result.html', {
        submitOnBlur: true, okButton: false, cancelLink: true,
        ajaxOptions: {method: 'get'} //override so we can use a static for the result
        });
</script>
*/
		$message .= '</table><br /><table width="900px" cellpadding="5px">
			<tr>
			<td width="20%">				<b>Commerçant</b>	</td>
			<td width="10%">				<b>Ressources</b>	</td>
			<td width="15%">				<b>Taille</b>		</td>
			<td width="55%">				<b>Marchands</b>	</td>
			</tr>';
		connectMaBase();

		$sql = "SELECT *
			FROM region
			WHERE RegionProprietaire != " . $Joueur . "
				AND RegionPartie = " . $Partie . "
				AND VilleCommerce > 0
				ORDER BY " . $Classement . " " . $Ordre ;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$RegionID			= $data['RegionID'];
			$RegionNom 			= $data['RegionNom']; 
			$RegionRessource 	= $data['RegionRessource']; 
			
			if ( $data['RegionOccupant'] != $data['RegionProprietaire'] && $data['RegionOccupant'] != -1 )
			{
				$OccupantNom = joueur($data['RegionOccupant'], "JoueurPseudo");
				$RegionNom .= "<br /><b>Occupée par <i>" . $OccupantNom . "</i></b>";
			}
			else if ( $data['RegionOccupant'] != $data['RegionProprietaire'] && $data['RegionOccupant'] == -1 )
			{
				$RegionNom .= "<br /><b>En révolte!</b>";			
			}
			$Region	 			= joueur($data['RegionProprietaire'], "JoueurPseudo") . "<br />" . $RegionNom;

			$DejaPresent = FALSE;
			if ( $MesMarchands[$RegionID] )
			{
				$DejaPresent = TRUE;
				$OffreOr	= "Or/" . $RegionID . "/" . $MesMarchands[$RegionID]["MarchandID"];
				$OffreBle	= "Ble/" . $RegionID . "/" . $MesMarchands[$RegionID]["MarchandID"];
				$OffreBois	= "Bois/" . $RegionID . "/" . $MesMarchands[$RegionID]["MarchandID"];
				$OffrePierre	= "Pierre/" . $RegionID . "/" . $MesMarchands[$RegionID]["MarchandID"];
				$OffreFer	= "Fer/" . $RegionID . "/" . $MesMarchands[$RegionID]["MarchandID"];
				$Offre	= "<span id=\"" . $OffreOr . "\">" . $MesMarchands[$RegionID]["MarchandDealOr"] . "</span> Or / <span id=\"" . $OffreBle . "\">" . $MesMarchands[$RegionID]["MarchandDealBle"] . "</span> Blé / <span id=\"" . $OffreBois . "\">" . $MesMarchands[$RegionID]["MarchandDealBois"] . "</span> Bois / <span id=\"" . $OffrePierre . "\">" . $MesMarchands[$RegionID]["MarchandDealPierre"] . "</span> Pierre / <span id=\"" . $OffreFer . "\">" . $MesMarchands[$RegionID]["MarchandDealFer"] . "</span> Fer
				<script>
					var url = './includes/ajax.php?mode=importationOffre';
					var parametres 	= \"mode=importationOffre\";
						var myAjax = new Ajax.InPlaceEditor(
							$('" . $OffreOr . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
				       			}
				       		);
				</script>
				<script>
					var url = './includes/ajax.php?mode=importationOffre';
					var parametres 	= \"mode=importationOffre\";
						var myAjax = new Ajax.InPlaceEditor(
							$('" . $OffreBle . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
				       			}
				       		);
				</script>
				<script>
					var url = './includes/ajax.php?mode=importationOffre';
					var parametres 	= \"mode=importationOffre\";
						var myAjax = new Ajax.InPlaceEditor(
							$('" . $OffreBois . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
				       			}
				       		);
				</script>
				<script>
					var url = './includes/ajax.php?mode=importationOffre';
					var parametres 	= \"mode=importationOffre\";
						var myAjax = new Ajax.InPlaceEditor(
							$('" . $OffrePierre . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
  				     			}
 				      		);
				</script>
				<script>
					var url = './includes/ajax.php?mode=importationOffre';
					var parametres 	= \"mode=importationOffre\";
						var myAjax = new Ajax.InPlaceEditor(
							$('" . $OffreFer . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 3, loadingText:'...', 
  				     			}
				       		);
				</script>";
				$importation = ( $MesMarchands[$RegionID]["MarchandDealOrValeur"] > 0 ) ? $MesMarchands[$RegionID]["MarchandDealOrValeur"] . " Or" : "";
				$importation .= ( $MesMarchands[$RegionID]["MarchandDealOrValeur"] > 0 && $MesMarchands[$RegionID]["MarchandDealRessourceValeur"] > 0 ) ? " et " : "";
				$importation .= ( $MesMarchands[$RegionID]["MarchandDealRessourceValeur"] > 0 ) ? $MesMarchands[$RegionID]["MarchandDealRessourceValeur"] . " " . ucfirst(strtolower($RegionRessource)) : "";
				$importation .= " HT";
				
				$Statut	= ( $MesMarchands[$RegionID]["MarchandStatut"] == 1 ) ? "en activité et importe " . $importation : "en attente";
				$Texte = "Votre marchand est " . $Statut . "<br />Votre offre: " . $Offre . "<br /><a href=\"#MarRe".$MarchandID."\" onclick=\"marchandRetirer(".$MesMarchands[$RegionID]["MarchandID"].", ".$Joueur.")\">Retirer votre marchand</a>";
			}
			$MarchandEnvoyerTexte	= "2 Or, 1 Blé";
			$Envoyer	= ( $DejaPresent == FALSE ) ? "<a href=\"#Mar".$RegionID.$VilleTailleAm."\" onmouseover=\"montre('".$MarchandEnvoyerTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'marchand', 1)\">Envoyer un marchand</a>" : $Texte;
			$RessourceOr	 	= ( $data['VilleTaille'] == 3 ) ? 4 : $data['VilleTaille'];
			$Ressource			= ( $data['VilleEconomie'] == 3 ) ? 7 : ( ( $data['VilleEconomie'] == 2 ) ? 4 : ( ( $data['VilleEconomie'] == 1 ) ? 2 : 1) );
			$RegionRessource 	= ucfirst(strtolower($data['RegionRessource']));

			$CommerceTaille		= str_replace("de commerce", "", cout("commerce", $data['VilleCommerce'], "nom"));
			
			$Production			= ( $data['VilleEconomie'] == 3 ) ? 7 : ( ( $data['VilleEconomie'] == 2 ) ? 4 : ( ( $data['VilleEconomie'] == 1 ) ? 2 : 1) );

			$Ressource			= $Production . " " . $RegionRessource . "<br />" . $RessourceOr . " Or";

			$message .= "<tr><td>".$Region."</td><td>".$Ressource."</td><td>".$CommerceTaille."</td><td>".$Envoyer."</td></tr>";
		}
		// Liste non-détaillé, juste le nom des gens, selon les statuts des marchands (en activité / en attente)
		// Ex: Azerty a 1 marchand en activité; Truc & Chgouette ont chacun 1 marchands en attente...
		$message .= "</table>";
		
		mysql_free_result($req);
		mysql_close();
	// Vos marchands
	
	break;
	// envoyer une ressource à un joueur
	
	case "echange":
		$RessourceOr 	= $_POST['RessourceOr'];
		$RessourceBle 	= $_POST['RessourceBle'];
		$RessourceBois 	= $_POST['RessourceBois'];
		$RessourcePierre = $_POST['RessourcePierre'];
		$RessourceFer 	= $_POST['RessourceFer'];

		$RessourceOr 	= ( $RessourceOr == "Or" ) ? 0 : $RessourceOr;
		$RessourceBle 	= ( $RessourceBle == "Blé" ) ? 0 : $RessourceBle;
		$RessourceBois 	= ( $RessourceBois == "Bois" ) ? 0 : $RessourceBois;
		$RessourcePierre = ( $RessourcePierre == "Pierre" ) ? 0 : $RessourcePierre;
		$RessourceFer 	= ( $RessourceFer == "Fer" ) ? 0 : $RessourceFer;

		
		$Joueur 		= $_POST['Joueur'];
		$Partie 		= $_POST['Partie'];
		$Destinataire	= $_POST['Destinataire'];
		
		if ( is_numeric($RessourceOr) == FALSE || is_numeric($RessourceBle) == FALSE || is_numeric($RessourceBois) == FALSE || is_numeric($RessourcePierre) == FALSE || is_numeric($RessourceFer) == FALSE )
		{
			$message = "L'une des ressources n'est pas un chiffre!";
			break;
		}
		else if ( !$RessourceOr && !$RessourceBle && !$RessourceBois && !$RessourcePierre && !$RessourceFer )
		{
			$message = "Vous ne demandez aucun transfert!";
			break;
		}
		else if ( $RessourceOr < 0 || $RessourceBle < 0 || $RessourceBois < 0 || $RessourcePierre < 0 || $RessourceFer < 0)
		{
			$message = "Aucun nombre ne peut être négatif!";
			break;
		}
		$Or 	= 0;
		$Ble 	= 0;
		$Bois 	= 0;
		$Pierre = 0;
		$Fer 	= 0;
		
		$Texte = Array();
		$TexteCompile = "";
		$Texte[1] = ( $RessourceOr > 0 ) ? ( ( $RessourceOr > 1 ) ? $RessourceOr . " unités d'or" : "1 unité d'or" ) : "";
		$Texte[2] = ( $RessourceBle > 0 ) ? ( ( $RessourceBle > 1 ) ? $RessourceBle . " unités de blé" : "1 unité de blé" ) : "";
		$Texte[3] = ( $RessourceBois > 0 ) ? ( ( $RessourceBois > 1 ) ? $RessourceBois . " unités de bois" : "1 unité de bois" ) : "";
		$Texte[4] = ( $RessourcePierre > 0 ) ? ( ( $RessourcePierre > 1 ) ? $RessourcePierre . " unités de pierre" : "1 unité de pierre" ) : "";
		$Texte[5] = ( $RessourceFer > 0 ) ? ( ( $RessourceFer > 1 ) ? $RessourceFer . " unités de fer" : "1 unité de fer" ) : "";

		$virgule = "";
		for ( $i = 1; $i < 6; $i++ )
		{
			$TexteCompile .= ( $Texte[$i] ) ? $virgule . $Texte[$i] : "";
			$virgule = ( $Texte[$i] || $virgule == ", ") ? ", " : "";
		}

		
		$transaction = transaction($Joueur, $RessourceOr, $RessourceBle, $RessourceBois, $RessourcePierre, $RessourceFer, $Destinataire);
		if ( $transaction == FALSE )
		{
			$message = "Erreur dans le transfert";
		}
		else
		{
			$DestinataireNom 	= joueur($Destinataire, "JoueurPseudo");
			$JoueurNom 			= joueur($Joueur, "JoueurPseudo");
			$historique			= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a envoyé " . $TexteCompile . " à " . $DestinataireNom));
			$Tour				= tour($Partie, "PartieTour");
			message(0, $historique, $Tour, $Partie);
			$message = "Vous avez bien envoyé " . $TexteCompile . " à " . $DestinataireNom;
		}


	break;
	
	
	// Demander la construction d'un village, ou autre: action
	
	case "action":
		$Type 		= $_POST['Type'];
		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		$erreur		= FALSE;
		
		switch ( $Type )
		{
			case "technologie":
				$TechnologieID		= $_POST['Argument1'];
				$TechnologieNom		= $technologies[$TechnologieID]["Nom"];
				$Infos = "Technologie:" . $TechnologieID;
				
				
				$listeTechno = joueur($Joueur, "Technologies");
				$tableauTechno = explode(",", $listeTechno);
				$NombreTechno	= count($tableauTechno);
				
				$TechnoMax = joueur($Joueur, "TechnologiesMax");
				if ( $NombreTechno > $TechnoMax )
				{
					$message = "Vous avez atteint votre maximum technologique";
					$erreur = TRUE;
					break;				
				}
				connectMaBase();

				$sql = "SELECT *
					FROM action
					WHERE ActionPartie = " . $Partie . "
						AND ActionJoueur = " . $Joueur . "
							AND ActionType = 'technologie'
							AND ActionInfos = '" . $Infos . "'";
				$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				if ($data = mysql_fetch_array($req))
				{
					$message = "La recherche de la technologie " . $TechnologieNom . " est déjà en cours";
					$erreur = TRUE;
					break;
					mysql_free_result($req);
				}
				mysql_free_result($req);
		
				$message = "Recherche de la technologie " . $TechnologieNom . " en cours";
			break;
			case "armee":
				$Ordre			= $_POST['Argument1'];
				$Nombre 		= $_POST['Argument2'];
		
				if ( $Nombre < 1 || is_numeric($Nombre) == FALSE)
				{
					$message = "Nombre trop faible";
					$erreur = TRUE;
					break;
				}
				else if ( !$Ordre )
				{
					$message = "Veuillez sélectionnez un ordre";
					$erreur = TRUE;
					break;
				}
				else
				{
					$pluriel = $Nombre > 1 ? "s" : "";
					$message = "Votre ordre (" . $Ordre . " " . $Nombre . " armée".$pluriel.") est transféré à l'état major";
				}
				$Infos = "Ordre:" . $Ordre . "/Nombre:" . $Nombre;
			break;
			case "autoMod":
				$Motif		= $_POST['Argument1'];
				$Or 		= $_POST['Argument2'];
				$Ble 		= $_POST['Argument3'];
				$Bois		= $_POST['Argument4'];
				$Fer 		= $_POST['Argument5'];
				$Pierre 	= $_POST['Argument6'];

				$Motif		= ( $Motif == 'Motif' ) ? "" : $Motif;
				$Or			= ( $Or == 'Or' ) ? 0 : $Or;
				$Ble		= ( $Ble == 'Blé' ) ? 0 : $Ble;
				$Bois		= ( $Bois == 'Bois' ) ? 0 : $Bois;
				$Fer		= ( $Fer == 'Fer' ) ? 0 : $Fer;
				$Pierre		= ( $Pierre == 'Pierre' ) ? 0 : $Pierre;

				if ( !$Motif )
				{
					$message = "L'administration est plutôt bureaucrate et a besoin d'un motif pour cette modification!";
					$erreur = TRUE;
					break;
				}
				else if ( is_numeric($Or) == FALSE || is_numeric($Ble) == FALSE || is_numeric($Bois) == FALSE || is_numeric($Pierre) == FALSE || is_numeric($Fer) == FALSE )
				{
					$message = "L'une des ressources n'est pas un chiffre!";
					$erreur = TRUE;
					break;
				}
				else if ( !$Or && !$Ble && !$Bois && !$Pierre && !$Fer )
				{
					$message = "Vous ne demandez aucun changement!";
					$erreur = TRUE;
					break;
				}
				else
				{
					$message = "Votre demande de changement " . $Ressource . " à " . $Nom . " va être examinée par les plus hautes instances";
				}
				$Infos = "Motif:" . $Motif . "/Or:" . $Or . "/Ble:" . $Ble . "/Bois:" . $Bois . "/Fer:" . $Fer . "/Pierre:" . $Pierre;
			break;
			case "village":
				$Ressource 		= $_POST['Argument2'];
				$Nom			= $_POST['Argument1'];
		
				if ( !$Nom || $Nom == "Nom du village")
				{
					$message = "L'administration est très bureaucrate et a besoin d'un nom pour le village!";
					$erreur = TRUE;
					break;
				}
				else
				{
					$RessourceTexte		= ucfirst(strtolower($Ressource));
					$message = "Votre demande pour créer un village de " . $RessourceTexte . " à " . $Nom . " va être examinée par les plus hautes instances";
				}
				$Infos = "Nom:" . $Nom . "/Ressource:" . $Ressource;
			break;
			case "general":
				$Prix			= $_POST['Argument1'];
				$Nom			= $_POST['Argument2'];
				$Ordre			= $_POST['Argument3'];
		
				if ( $Prix <= 0 || is_numeric($Prix) == FALSE)
				{
					$message = "Le prix doit être positif";
					$erreur = TRUE;
					break;
				}
				else if ( $Nom == "Nom" || !$Nom )
				{
					$message = "Merci de spécifier un nom";
					$erreur = TRUE;
					break;
				}
				else
				{
					$OrdreTexte = ( $Ordre == "Enroler" ) ? "d'enrolement" : "de démobilisation";
					$message = "Votre demande " . $OrdreTexte . " de <i>" . $Nom . "</i> est transféré à l'état major";
				}
				$Infos = "Prix:" . $Prix . "/Nom:" . $Nom . "/Ordre:" . $Ordre;
			break;
			case "technologie":
				$Technologie	= $_POST['Argument1'];
				$Valeur 		= $_POST['Argument2'];
				$Point 			= $_POST['Argument3'];
		
				if ( $Valeur < 0 || is_numeric($Valeur) == FALSE)
				{
					$message = "Nombre trop faible";
					$erreur = TRUE;
					break;
				}
				else if ( !$Type )
				{
					$message = "Veuillez sélectionnez une technologie";
					$erreur = TRUE;
					break;
				}
				else
				{
					$pluriel = $Nombre > 1 ? "s" : "";
					$message = "Votre demande de technologie de " . str_replace("TechProd", "Production de ", $Technologie) . " avec comme valeur: " . $Valeur . " a été transmise aux scientifiques";
				}
				$Infos = "Type:" . $Technologie . "/Valeur:" . $Valeur . "/Point:" . $Point;
			break;
		}

		if ( $erreur == FALSE )
		{
			connectMaBase();
			$sql = "INSERT INTO action (ActionPartie, ActionType, ActionJoueur, ActionInfos)
				VALUES (" . $Partie . ", '" . $Type . "', " . $Joueur . ", '" . $Infos . "')";
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());	
			mysql_free_result($req);
			mysql_close();
		}
	break;
	
	// Annuler une demande
	case "annulerAction":
		$adjectif 		= adjectif("singulier");
		$message = "Votre " . $adjectif . " demande a été annulée";
		
		$ActionID	= $_POST['Action'];
		$Joueur		= $_POST['Joueur'];
		
		connectMaBase();
		
		$sql = "DELETE FROM action
			WHERE ActionID = " . $ActionID . "
				AND ActionJoueur = " . $Joueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		mysql_close();
		
	break;
	
	// Annuler une construction, la retirer du panier
	case "annuler":
		$adjectif 		= adjectif("singulier");
		$message = "Votre " . $adjectif . " projet a été annulé. Ca fait quoi d'être pauvre au point de reporter ses projets?";
		
		$ConstructionID	= $_POST['Construction'];
		$Joueur			= $_POST['Joueur'];

		
		connectMaBase();
		
		$sql = "SELECT *
			FROM construction
			WHERE ConstructionID = " . $ConstructionID;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$oui 			= TRUE;
			$ConstructionID = $data['ConstructionID'];
		}
		mysql_free_result($req);

		$CoutOr 	= cout($data['ConstructionType'], $data['ConstructionNumero'], "or");
		$CoutBle 	= cout($data['ConstructionType'], $data['ConstructionNumero'], "ble");
		$CoutBois 	= cout($data['ConstructionType'], $data['ConstructionNumero'], "bois");
		$CoutPierre = cout($data['ConstructionType'], $data['ConstructionNumero'], "pierre");
		$CoutFer 	= cout($data['ConstructionType'], $data['ConstructionNumero'], "fer");

		$sql = "UPDATE joueur
			SET DepenseOr = DepenseOr - ". $CoutOr . ", DepenseBle = DepenseBle - ". $CoutBle . ", DepenseBois = DepenseBois - ". $CoutBois . ", DepensePierre = DepensePierre - ". $CoutPierre . ", DepenseFer = DepenseFer - ". $CoutFer . "
				WHERE JoueurID = " . $Joueur;
		mysql_query($sql) or die('Erreur de Thib!<br />'.$sql.'<br />'.mysql_error());
		
		$sql = "DELETE FROM construction
			WHERE ConstructionID = " . $ConstructionID;
		mysql_query($sql) or die('Erreur de Thib 2!<br />'.$sql.'<br />'.mysql_error());
		
		mysql_close();
		
	break;

	// Construire les constructions présentes dans le panier
	case "valider":

		// Liste des constructions
		// Prix total
		// Le joueur a t'il assez?
		// Mise à jour des infos: régions, joueurs, constructions
		
		$JoueurID		= $_POST['Joueur'];

		connectMaBase();

		$transaction = FALSE;
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurID = " . $JoueurID;
		$req = mysql_query($sql) or die('Erreur SQqqqL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$Partie				= $data['JoueurPartie'];
			$MilitaireArmees	= $data['MilitaireArmees'];
			$CommerceNombre		= $data['CommerceNombre'];
			$CommerceNombreMax	= $data['CommerceNombreMax'];
			$Or 				= $data['DepenseOr'] - $data['MilitaireArmees'];
			$Ble 				= $data['DepenseBle'] - $MilitaireArmees;
			
			$transaction 		= transaction($JoueurID, $Or, $Ble, $data['DepenseBois'], $data['DepensePierre'], $data['DepenseFer'], 0);
		}
		// Transaction
		else
		{
			$message = "Erreur dans la liste des ressources";
		}
		mysql_free_result($req);
		mysql_close();
		
		$erreur = FALSE;
		if ( $transaction == FALSE )
		{
			$message = "Erreur: pas assez de ressources";
			break;
		}
		else
		{
			$taxe			= 0;
			$villes 		= 0;
			$economie 		= 0;
			$commerce 		= 0;
			$militaire 		= 0;
			$culture 		= 0;
			$marchands		= 0;
			
			$eco['OR'] 		= 0;
			$eco['BLE'] 	= 0;
			$eco['BOIS'] 	= 0;
			$eco['PIERRE'] 	= 0;
			$eco['FER'] 	= 0;
			
			$depenseOr		= 0;
			$depenseBle		= 0;
			$depenseBois	= 0;
			$depensePierre	= 0;
			$depenseFer		= 0;
			
			$compteur 		= 0;
			$compteurOk 	= 0;
			$texte 			= "";
			$messageErreur	= "";
			
			$MesTests = "";
			
			// On sélectionne
			connectMaBase();
			
			$sql = "SELECT c.*, r.*
				FROM construction c, region r
				WHERE c.ConstructionJoueur = " . $JoueurID . "
					AND r.RegionID = c.ConstructionRegion";
			$req = mysql_query($sql) or die('Erreur SQL popop !<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req))
			{
				$ConstructionID	= $data['ConstructionID'];
				$Type 			= $data['ConstructionType'];
				$Numero			= $data['ConstructionNumero'];
				$Region 		= $data['ConstructionRegion'];
				$VilleTaille 	= $data['VilleTaille'];
				$VilleEconomie 	= $data['VilleEconomie'];
				$VilleCulture 	= $data['VilleCulture'];
				$VilleMilitaire = $data['VilleMilitaire'];
				$VilleCommerce 	= $data['VilleCommerce'];

				$RegionNom	 		= $data['RegionNom'];
				$RegionID	 		= $data['RegionID'];
				$Ressource 			= $data['RegionRessource'];
				$RegionProprietaire = $data['RegionProprietaire'];

				$thisErreur		= FALSE;
				$compteur++;
				
				$Nom 		= cout($Type, $Numero, "nom");

				$RemboursementOr 		= cout($Type, $Numero, "or");
				$RemboursementBle 		= cout($Type, $Numero, "ble");
				$RemboursementBois 		= cout($Type, $Numero, "bois");
				$RemboursementPierre 	= cout($Type, $Numero, "pierre");
				$RemboursementFer 		= cout($Type, $Numero, "fer");
				
				$Test1	= "1Ok";
				$Test2	= "";
				$Test3	= "";
				$Test4	= "";
				$Test5	= "";
				$Test6	= "";
				$Test7	= "";
				$Test8	= "";
				$Test9	= "";
				$Test10	= "";
				$Test11	= "";

				if ( $Numero > $VilleTaille && $Type != "ville" )
				{
					$messageErreur .= "<br />- Construire <u>" . $Nom . "</u> à <i>" . $RegionNom . "</i> est impossible: vous n'avez pas assez développé la ville";
					$thisErreur = TRUE;
					$erreur = TRUE;
					$Test2	= "2PbDev";
				}
				else
				{
					$Test3	= "3PasdePb";
					switch ( $Type )
					{
						case "ville":
							$mod = "VilleTaille";
							if ( $VilleTaille == 3 )
							{
								$messageErreur .= "<br />- Votre ville <i>" . $RegionNom . "</i> est déjà développée au maximum. Vous ne pouvez pas l'améliorer";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else
							{
								$villes++;
								$taxe++;
						
								// Si la ville devient de taile 3, alors elle gagne 4 taxe, et pas "
								if ( $Numero > 2 )
								{
									$taxe++;
								}
							}
						break;
						case "economie":
							$Test4	= "4Eco";
							$mod = "VilleEconomie";
							if ( $VilleEconomie == 3 )
							{
								$Test5	= "5EcoPbNiveau3deja";
								$messageErreur .= "<br />- Votre ville <i>" . $RegionNom . "</i> a déjà développé son économie au maximum. Vous ne pouvez pas l'améliorer";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else
							{
								$Test6	= "6EcoOk";
								$economie++;
								if ( $Numero >= 2 )
								{
									$eco[$Ressource] = ( $Numero == 2 ) ? $eco[$Ressource] + 2 : $eco[$Ressource] + 3;
								}
								else
								{
									$eco[$Ressource] += 1;
								}
							}
						break;
						case "commerce":
							$mod = "VilleCommerce";
							if ( $VilleCommerce == 3 )
							{
								$messageErreur .= "<br />- Votre ville <i>" . $RegionNom . "</i> a déjà développé son commerce. Vous ne pouvez pas l'améliorer";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else if ( $CommerceNombre >= $CommerceNombreMax && $VilleCommerce == 0 )
							{
								$messageErreur .= "<br />- Impossible de batir une route de commerce à <i>" . $RegionNom . "</i>. Votre technologie ne vous permet pas de bâtir plus de " . $CommerceNombreMax . " routes commerciales";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else
							{
								$commerce++;
							}
						break;
						case "militaire":
							if ( $VilleMilitaire == 3 )
							{
								$messageErreur .= "<br />- Votre ville <i>" . $RegionNom . "</i> a déjà développé ses défenses au maximum. Vous ne pouvez pas l'améliorer";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else
							{
								$mod = "VilleMilitaire";
								$militaire++;
							}
						break;
						case "culture":
							if ( $VilleCulture == 3 )
							{
								$messageErreur .= "<br />- Votre ville <i>" . $RegionNom . "</i> a déjà développé sa culture au maximum. Vous ne pouvez pas l'améliorer";
								$thisErreur = TRUE;
								$erreur = TRUE;
							}
							else
							{
								$mod = "VilleCulture";
								$culture++;
							}
						break;
						case "marchand":
							$mod = "CommerceMarchands";
								$Test9	= "9MarchandOk";
							
							// Si déjà un marchand là bas
							

							$sql5 = "SELECT MarchandID
								FROM marchand
									WHERE MarchandJoueur = " . $JoueurID . "
									AND MarchandRegion = " . $RegionID;
							$resultat = mysql_query($sql5) or die('Erreur SQL kks!<br />'.$sql5.'<br />'.mysql_error());
							while ($dataa = mysql_fetch_array($resultat))
							{
								$Test10	= "10MarchandDeja";
								$messageErreur .= "<br />- Vous avez déjà un marchand à <i>" . $RegionNom . "</i>. Vous ne pouvez pas en envoyer un autre";
								$thisErreur = TRUE;
								// Pb lorsque plusieurs marchands en même temps et que l'un est déjà envoyé
								$erreur = TRUE;
							}
							if ( $thisErreur == FALSE )
							{
								$Test11	= "11MarchandPasDeja";
								$sql4 = "UPDATE joueur
									SET CommerceMarchandsEtrangersNombre = CommerceMarchandsEtrangersNombre + 1
									WHERE JoueurID = " . $RegionProprietaire;
								mysql_query($sql4) or die('Erreur SQL hhh !<br />'.$sql4.'<br />'.mysql_error());
			
								$sql4 = "INSERT INTO marchand (MarchandJoueur, MarchandRegion)
									VALUES ('".$JoueurID."', ".$Region.")";
									mysql_query($sql4) or die('Erreur SQL uuuu!<br />'.$sql4.'<br />'.mysql_error());
								$marchands++;

							}
							mysql_free_result($resultat);
						break;
					}	// Fin du switch
				}
				
				if ( $thisErreur == FALSE)
				{
					$Test7	= "7NoError";
					$texte .= "<br />- <u>" . $Nom . "</u> à <i>" . $RegionNom . "</i>";
					$compteurOk++;
						
					connectMaBase();
					// Mettre à jour les ressources totales
					// Idem à la création d'une région d'ailleurs	
					if ( $Type != "marchand" )
					{
						$sql2 = "UPDATE region
								SET ".$mod." = ".$mod." + 1
									WHERE RegionID = " . $Region;
						mysql_query($sql2) or die('Erreur SQrrrL !<br />'.$sql2.'<br />'.mysql_error());	
						mysql_free_result($sql2);
					}
				
					$sql3 = "DELETE FROM construction
						WHERE ConstructionJoueur = " . $JoueurID . "
							AND ConstructionID = " . $ConstructionID;
					mysql_query($sql3) or die('Erreur SQLzzz !<br />'.$sql3.'<br />'.mysql_error());
					mysql_free_result($sql3);
				}
				else
				{
					$Test8	= "8Remboursement";
					// Ce qui reste à payer si la construction est impossible
					$depenseOr 		+= $RemboursementOr;
					$depenseBle 	+= $RemboursementBle;
					$depenseBois 	+= $RemboursementBois;
					$depensePierre 	+= $RemboursementPierre;
					$depenseFer 	+= $RemboursementFer;
					$remboursement 	= transaction(0, $RemboursementOr, $RemboursementBle, $RemboursementBois, $RemboursementPierre, $RemboursementFer, $JoueurID);
				}
				
				$MesTests	.= "<br /><br />---------------<br /><u>" . $Nom . "</u> à <i>" . $RegionNom . "</i><br />- Test 1: " . $Test1 . "<br />- Test 2: " . $Test2. "<br />- Test 3: " . $Test3. "<br />- Test 4: " . $Test4. "<br />- Test 5: " . $Test5. "<br />- Test 6: " . $Test6. "<br />- Test 9: " . $Test9. "<br />- Test 10: " . $Test10. "<br />- Test 11: " . $Test11."<br />- Test 7: " . $Test7. "<br />- Test 8: " . $Test8;

			}	// Fin de la boucle
			
			if ( !$Numero )
			{
				$message = "Vous n'avez pas de projet. Gros con.";
				break;
			}

			if ( $compteurOk > 0 )
			{
				$JoueurNom 			= joueur($JoueurID, "JoueurPseudo");
				$s					= ( $compteurOk == 1 ) ? "" : "s";
				$historique			= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a construit " . $compteurOk . " projet" . $s . " " . $texte));
				$Tour				= tour($Partie, "PartieTour");
				message(0, $historique, $Tour, $Partie);
			}
			if ( $erreur == TRUE )
			{
				if ( $compteurOk == 0 )
				{
					$message 		= "Aucun de vos projets n'a été construit: " . $messageErreur;						
				}
				else if ( $compteurOk == 1 )
				{
					$adjectif 		= adjectif("pluriel");
					$message 		= "Un de vos " . $adjectif . " projets a été construit: " . $messageErreur;		
				}
				else
				{
					$adjectif 		= adjectif("pluriel");
					$message 		= "Certains de vos " . $adjectif . " projets ont été construit: " . $messageErreur;		
				}
			}
			else
			{
				$adjectif 		= adjectif("pluriel");
				$message 		= "Tous vos " . $adjectif . " projets ont été construits";		
			}
	//		$message .= "<br />" . $MesTests;
	
			// Mise à jour des infos sur la production générale
			connectMaBase();
			// Mettre à jour les revenus par tour en tenant compte des nouvelles constructions et les dépenses du tour suivant à zéro
			$sql = "UPDATE joueur
					SET DepenseOr = MilitaireArmees + " . $depenseOr . ", DepenseBle = MilitaireArmees + " . $depenseBle . ", DepenseBois = " . $depenseBois . ", DepensePierre = " . $depensePierre . ", DepenseFer = " . $depenseFer . ", RevenuOr = RevenuOr + " . $taxe . ", RevenuBle = RevenuBle + " . $eco['BLE'] . ", RevenuBois = RevenuBois + " . $eco['BOIS'] . ", RevenuPierre = RevenuPierre + " . $eco['PIERRE'] . ", RevenuFer = RevenuFER + " . $eco['FER'] . ", DevMilitaire = DevMilitaire + " . $militaire . ", DevCulture = DevCulture + " . $culture . ", DevEconomie = DevEconomie + " . $economie . ", DevCommerce = DevCommerce + " . $commerce . ", DevVille = DevVille + " . $villes . ", CommerceNombre = CommerceNombre + " . $commerce . ", CommerceMarchandsNombre = CommerceMarchandsNombre + " . $marchands . "
						WHERE JoueurID = " . $JoueurID;
			mysql_query($sql) or die('Erraaaaeur SQL !<br />'.$sql.'<br />'.mysql_error());
			mysql_free_result($sql);
		}
		mysql_close();
	break;
	
	
	// Ajouter un projet de construction au panier
	case "construire":

		$adjectif 		= adjectif("singulier");
		
		$ConstructionJoueur		= $_POST['Joueur'];
		$ConstructionRegion		= $_POST['Region'];
		$ConstructionType		= $_POST['Type'];
		$ConstructionNumero		= $_POST['Numero'];
		
		connectMaBase();
		
		$nom = cout($ConstructionType, $ConstructionNumero, "nom");
		
		$sql = "SELECT c.*, r.RegionNom
			FROM construction c, region r
			WHERE c.ConstructionJoueur = " . $ConstructionJoueur . "
				AND c.ConstructionRegion = " . $ConstructionRegion . "
				AND c.ConstructionType = '" . $ConstructionType . "'
				AND c.ConstructionNumero = " . $ConstructionNumero . "
				AND r.RegionID = c.ConstructionRegion";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$message 	= "Vous avez déjà projetté de construire <u>" . $nom . "</u> à <i>" . $data['RegionNom'] ."</i>";
			break;
		}
		else
		{
			$message 	= "Votre " . $adjectif . " projet de  <u>" . $nom . "</u> a été ajouté à votre liste d'attente";
		}
		mysql_free_result($req);
		
		$sql = "INSERT INTO construction (ConstructionJoueur, ConstructionRegion, ConstructionType, ConstructionNumero)
			VALUES ('".$ConstructionJoueur."', ".$ConstructionRegion.", '".$ConstructionType."', ".$ConstructionNumero.")";
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		
		
		$CoutOr 	= cout($ConstructionType, $ConstructionNumero, "or");
		$CoutBle 	= cout($ConstructionType, $ConstructionNumero, "ble");
		$CoutBois 	= cout($ConstructionType, $ConstructionNumero, "bois");
		$CoutPierre = cout($ConstructionType, $ConstructionNumero, "pierre");
		$CoutFer 	= cout($ConstructionType, $ConstructionNumero, "fer");
		
		$sql = "UPDATE joueur
				SET DepenseOr = DepenseOr + ". $CoutOr . ", DepenseBle = DepenseBle + ". $CoutBle . ", DepenseBois = DepenseBois + ". $CoutBois . ", DepensePierre = DepensePierre + ". $CoutPierre . ", DepenseFer = DepenseFer + ". $CoutFer . "
					WHERE JoueurID = " . $ConstructionJoueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());	
		mysql_free_result($req);
		mysql_close();
	break;

	// Affiche la liste des constructions, le panier
	case "liste-construction":
	
		$message = "<table width=\"900px\" cellpadding=\"5px\">";
		$message.= "<tr>
						<td width=\"20%\" class=\"haut\">Construction</td>
						<td width=\"20%\" class=\"haut\">Lieu</td>
						<td width=\"10%\" class=\"haut\">Or</td>
						<td width=\"10%\" class=\"haut\">Blé</td>
						<td width=\"10%\" class=\"haut\">Bois</td>
						<td width=\"10%\" class=\"haut\">Pierre</td>
						<td width=\"10%\" class=\"haut\">Fer</td>
						<td width=\"15%\">&nbsp;</td></tr>";

		$Joueur		 	= $_POST['Joueur'];
		$oui = FALSE;

		connectMaBase();
		$data 				= 0;
		$CoutOrTotal 		= 0;
		$CoutBleTotal 		= 0;
		$CoutBoisTotal	 	= 0;
		$CoutPierreTotal	= 0;
		$CoutFerTotal 		= 0;
		
		$CoutOrAction 		= 0;
		$CoutBleAction 		= 0;
		$CoutBoisAction	 	= 0;
		$CoutPierreAction	= 0;
		$CoutFerAction 		= 0;
		mysql_free_result($req);
		
		$sql = "SELECT c.*, r.RegionNom
			FROM construction c, region r
			WHERE c.ConstructionJoueur = " . $Joueur . "
				AND r.RegionID = c.ConstructionRegion";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$oui 				= TRUE;
			$ConstructionID 	= $data['ConstructionID'];
			$ConstructionNumero = $data['ConstructionNumero'];
			$ConstructionType 	= $data['ConstructionType'];
			
			$Nom 		= cout($ConstructionType, $ConstructionNumero, "nom");
			$Nom		= str_replace(" de commerce", "", $Nom);
			$CoutOr 	= cout($ConstructionType, $ConstructionNumero, "or");
			$CoutBle 	= cout($ConstructionType, $ConstructionNumero, "ble");
			$CoutBois 	= cout($ConstructionType, $ConstructionNumero, "bois");
			$CoutPierre = cout($ConstructionType, $ConstructionNumero, "pierre");
			$CoutFer 	= cout($ConstructionType, $ConstructionNumero, "fer");
			$Region 	= $data['RegionNom'];

			$CoutOrTotal += $CoutOr;
			$CoutBleTotal += $CoutBle;
			$CoutBoisTotal += $CoutBois;
			$CoutPierreTotal += $CoutPierre;
			$CoutFerTotal += $CoutFer;

			$message		.= "<tr>
						<td>".$Nom."</td>
						<td>".$Region."</td>
						<td>".$CoutOr."</td>
						<td>".$CoutBle."</td>
						<td>".$CoutBois."</td>
						<td>".$CoutPierre."</td>
						<td>".$CoutFer."</td>
						<td><a href=\"#Annuler".$ConstructionID."\" onclick=\"annuler(".$ConstructionID.")\">Annuler</a></td></tr>";
		}
		if ( !$oui )
		{
			$message .= "<tr><td colspan=\"8\">Vous n'avez pas de projets de construction</td></tr>";
		}
		$sql = "SELECT *
			FROM action
			WHERE ActionJoueur = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$ouiAction 			= TRUE;
			$ActionID 			= $data['ActionID'];
			$ActionType 		= $data['ActionType'];

			$ActionInfos	= $data['ActionInfos'];
			$Split			= explode("/", $ActionInfos);

			switch ( $ActionType ) 
			{
				case "technologie":
					$TechnologieID			= str_replace("Technologie:", "", $Split[0]);

					$ActionType 	= "R&D";
					$Nom 			= $technologies[$TechnologieID]["Nom"];
					$CoutOr 		= $technologies[$TechnologieID]["CoutOr"];
					$CoutBle 		= $technologies[$TechnologieID]["CoutBle"];
					$CoutBois 		= $technologies[$TechnologieID]["CoutBois"];
					$CoutPierre 	= $technologies[$TechnologieID]["CoutPierre"];
					$CoutFer 		= $technologies[$TechnologieID]["CoutFer"];
				break;
				case "general":
					$Prix			= str_replace("Prix:", "", $Split[0]);
					$Ordre			= str_replace("Ordre:", "", $Split[2]);
					$Nom			= $Ordre . " " . str_replace("Nom:", "", $Split[1]);
					$CoutOr 		= ( $Ordre == "Enroler" || $Ordre == "Ameliorer") ? $Prix : 0;
				break;

				case "armee":					
					$Ordre			= str_replace("Ordre:", "", $Split[0]);
					$Nombre			= str_replace("Nombre:", "", $Split[1]);
					$pluriel		= $Nombre > 1 ? "s" : "";
					$ActionType 	= $Ordre . " " . $Nombre . " armee".$pluriel;
					$Modificateur	= ( $Ordre == "Enroler" ) ? 1 : -1;
					if ( $Ordre == "Enroler" )
					{
						$CoutOr 		= 1 * $Nombre * $Modificateur;
						$CoutBle 		= 1 * $Nombre * $Modificateur;
						$CoutBois 		= 0;
						$CoutPierre 	= 0;
						$CoutFer 		= 1 * $Nombre * $Modificateur;
					}
					else
					{
						$CoutOr 		= 0;
						$CoutBle 		= 0;
						$CoutBois 		= 0;
						$CoutPierre 	= 0;
						$CoutFer 		= 0;
					}
					$Nom			= "";
					
				break;
				
				case "village":
					$CoutOr 		= cout("ville", 1, "or");
					$CoutBle 		= cout("ville", 1, "ble");
					$CoutBois 		= cout("ville", 1, "bois");
					$CoutPierre 	= cout("ville", 1, "pierre");
					$CoutFer 		= cout("ville", 1, "fer");
					$ActionType 	= "Création de village";
					
					$Ressource		= str_replace("Ressource:", "", $Split[1]);
					$Nom			= str_replace("Nom:", "", $Split[0]);
					$Nom			.= " (".$Ressource.")";
				break;
		
				case "autoMod":
					$ActionType 	= "Modif des ressources";				
					$Nom			= str_replace("Motif:", "", $Split[0]);
					$CoutOr 		= str_replace("Or:", "", $Split[1]);
					$CoutBle 		= str_replace("Ble:", "", $Split[2]);
					$CoutBois 		= str_replace("Bois:", "", $Split[3]);
					$CoutFer 		= str_replace("Fer:", "", $Split[4]);
					$CoutPierre 	= str_replace("Pierre:", "", $Split[5]);
					$Nom			= "";
					
					$CoutOr 		*= -1;
					$CoutBle 		*= -1;
					$CoutBois 		*= -1;
					$CoutFer 		*= -1;
					$CoutPierre 	*= -1;

				break;
			}
			$CoutOrTotal 	+= $CoutOr;
			$CoutBleTotal 	+= $CoutBle;
			$CoutBoisTotal 	+= $CoutBois;
			$CoutPierreTotal += $CoutPierre;
			$CoutFerTotal 	+= $CoutFer;
			
			
			$CoutOrAction 	+= $CoutOr;
			$CoutBleAction 	+= $CoutBle;
			$CoutBoisAction += $CoutBois;
			$CoutPierreAction += $CoutPierre;
			$CoutFerAction 	+= $CoutFer;
			
			$message		.= "<tr>
						<td>".$ActionType."</td>
						<td>".$Nom."</td>
						<td>".$CoutOr."</td>
						<td>".$CoutBle."</td>
						<td>".$CoutBois."</td>
						<td>".$CoutPierre."</td>
						<td>".$CoutFer."</td>
						<td><a href=\"#Annuler".$ActionID."\" onclick=\"annulerAction(".$ActionID.")\">Annuler</a></td></tr>";
		}
		if ( !$ouiAction )
		{
			$message .= "<tr><td colspan=\"8\">Vous n'avez pas de demande à l'administration en cours</td></tr>";
		}		

		if ( $oui || $ouiAction )
		{
			$sql = "SELECT *
				FROM joueur
				WHERE JoueurID = " . $Joueur;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				// A la fin du tour
				$FinOr 		= round($data['RessourceOr'] 		- $data['DepenseOr'] 	+ $data['MilitaireArmees'] 	- $CoutOrAction, 2);
				$FinBle 	= round($data['RessourceBle'] 	- $data['DepenseBle'] 	+ $data['MilitaireArmees'] 	- $CoutBleAction, 2);
				$FinBois	= round($data['RessourceBois'] 	- $data['DepenseBois'] 								- $CoutBoisAction, 2);
				$FinPierre 	= round($data['RessourcePierre'] 	- $data['DepensePierre'] 							- $CoutPierreAction, 2);
				$FinFer 	= round($data['RessourceFer'] 	- $data['DepenseFer'] 								- $CoutFerAction, 2);
			}

	
			$message		.= "<tr>
						<td colspan=\"2\" align=\"right\">Coût total</td>
						<td>".$CoutOrTotal."</td>
						<td>".$CoutBleTotal."</td>
						<td>".$CoutBoisTotal."</td>
						<td>".$CoutPierreTotal."</td>
						<td>".$CoutFerTotal."</td>
						<td rowspan=\"2\"></td></tr>";
			$message		.= "<tr>
						<td colspan=\"2\" align=\"right\">Ressources restantes</td>
						<td>".couleur($FinOr)."</td>
						<td>".couleur($FinBle)."</td>
						<td>".couleur($FinBois)."</td>
						<td>".couleur($FinPierre)."</td>
						<td>".couleur($FinFer)."</td>
						</tr>";

		}
		$message .= "</table>";

		mysql_free_result($req);
		mysql_close();
	break;
	
	// Statistiques
	case "statistiques":
		$message = '<table cellpadding="5px">
			<tr>
				<td></td>
				<td><b>Total</b></td>
				<td colspan="2"><b>Moyenne</b></td>
				<td colspan="2"><b>Maximum</b></td>
				<td><b>Rang</b></td>
			</tr>
			';
		
		$Joueur = $_POST['Joueur'];
		$Partie = $_POST['Partie'];
		
		connectMaBase();
		$sql = "SELECT COUNT(*) AS nombre, MAX(VilleTaille) AS MaxVille, MAX(VilleMilitaire) AS MaxMilitaire, MAX(VilleEconomie) AS MaxEconomie, MAX(VilleCulture) AS MaxCulture
			FROM region
			WHERE RegionProprietaire = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$NombreRegion 	= $data['nombre'];
			$MaxVille		= $data['MaxVille'];
			$MaxMilitaire	= $data['MaxMilitaire'];
			$MaxEconomie	= $data['MaxEconomie'];
			$MaxCulture		= $data['MaxCulture'];
		}
		
		$DevCulture = joueur($Joueur, "DevCulture");
		$DevVille = joueur($Joueur, "DevVille");
		$Ratio			= round($DevCulture / $DevVille / 3, 2);

		$Tour				= tour($Partie, "PartieTour");
		$Tour--;
		connectMaBase();
		$sql = "SELECT *
			FROM statistiques
			WHERE StatJoueur = " . $Joueur . "
				AND StatTour = " . $Tour;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$Partie					= $data["StatPartie"];
			
			$DevVilleMoyenne 		= round($data['StatVille'] / $NombreRegion, 1);
			$DevEconomieMoyenne 	= round($data['StatDevEco'] / $NombreRegion, 1);
			$DevMilitaireMoyenne 	= round($data['StatDevMilitaire'] / $NombreRegion, 1);
			$DevCultureMoyenne 		= round($data['StatDevCulture'] / $NombreRegion, 1);
			
			$DevVilleNom			= cout("ville", round($DevVilleMoyenne), "nom");
			$DevEconomieNom			= cout("economie", round($DevEconomieMoyenne), "nom");
			$DevMilitaireNom		= cout("militaire", round($DevMilitaireMoyenne), "nom");
			$DevCultureNom			= cout("culture", round($DevCultureMoyenne), "nom");

			$message .= "<tr><td><b>Villes</b></td><td align=\"center\">".$data['StatVille']."</td><td>".$DevVilleMoyenne."</td><td>".$DevVilleNom."</td><td>" . $MaxVille . "</td><td>".cout("ville", $MaxVille, "nom")."</td><td align=\"center\">".$data['StatVilleClassement']."</td></tr>";
			$message .= "<tr><td><b>Economie</b></td><td align=\"center\">".$data['StatDevEco']."</td><td>".$DevEconomieMoyenne."</td><td>".$DevEconomieNom."</td><td>" . $MaxEconomie . "</td><td>".cout("economie", $MaxEconomie, "nom")."</td><td align=\"center\">".$data['StatDevEcoClassement']."</td></tr>";
			$message .= "<tr><td><b>Militaire</b></td><td align=\"center\">".$data['StatDevMilitaire']."</td><td>".$DevMilitaireMoyenne."</td><td>".$DevMilitaireNom."</td><td>" . $MaxMilitaire . "</td><td>".cout("militaire", $MaxMilitaire, "nom")."</td><td align=\"center\">".$data['StatDevMilitaireClassement']."</td></tr>";
			$message .= "<tr><td><b>Culture</b></td><td align=\"center\">".$data['StatDevCulture']."</td><td>".$DevCultureMoyenne."</td><td>".$DevCultureNom."</td><td>" . $MaxCulture . "</td><td>".cout("culture", $MaxCulture, "nom")."</td><td align=\"center\">".$data['StatDevCultureClassement']."</td></tr>";
			$message .= "<tr><td><b>Ratio</b></td><td align=\"center\" colspan=\"6\"><font size=\"2\">x</font>".$Ratio."</td></tr>";
		}
		else
		{
			$message = "Erreur dans les stats";
		}
	
		$message .= "</table><br /><a href=\"statistiques.php?Partie=".$Partie."&Joueur=".$Joueur."\" target=\"_blank\">Davantage de statistiques</a>";
		mysql_free_result($req);
		mysql_close();
	break;
	
	// Ressources disponible au début du tour
	case "ressources":
		$message = '<table cellpadding="8px">
			<tr>
				<td width="160px">&nbsp;</td>
				<td width="45px"><b>Or</b></td>
				<td width="45px"><b>Blé</b></td>
				<td width="45px"><b>Bois</b></td>
				<td width="45px"><b>Pierre</b></td>
				<td width="45px"><b>Fer</b></td>
			</tr>';
		
		$Joueur = $_POST['Joueur'];
		
		connectMaBase();
		
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			// Ressource
			$message .= "<tr><td><b>Trésorerie</b></td><td>".round($data['RessourceOr'],2)."</td><td>".round($data['RessourceBle'],2)."</td><td>".round($data['RessourceBois'],2)."</td><td>".round($data['RessourcePierre'],2)."</td><td>".round($data['RessourceFer'],2)."</td></tr>";

			$BonusCulture = round($data['DevCulture'] / $data['DevVille'] / 3, 2);
			$message .= "<tr><td colspan=\"6\" class=\"haut\"><font size=\"2\">Plus</font></td></tr>";
			// Revenu par tour
			$message .= "<tr><td>&nbsp;&nbsp;&nbsp;<b>Production</b></td><td>+".round($data['RevenuOr'] * ($data['TechProdOr'] + $BonusCulture), 2)."</td><td> +".round($data['RevenuBle'] * ($data['TechProdBle'] + $BonusCulture), 2)."</td><td>+".round($data['RevenuBois'] * ($data['TechProdBois'] + $BonusCulture), 2)."</td><td>+".round($data['RevenuPierre'] * ($data['TechProdPierre'] + $BonusCulture), 2)."</td><td>+".round($data['RevenuFer'] * ($data['TechProdFer'] + $BonusCulture), 2)."</td></tr>";

			$message .= "<tr><td>&nbsp;&nbsp;&nbsp;<b>Importation TTC</b></td><td>".$data['ImportationOr']."</td><td>".$data['ImportationBle']."</td><td>".$data['ImportationBois']."</td><td>".$data['ImportationPierre']."</td><td>".$data['ImportationFer']."</td></tr>";

			$message .= "<tr><td colspan=\"6\" class=\"haut\"><font size=\"2\">Moins</font></td></tr>";

			$message .= "<tr><td>&nbsp;&nbsp;&nbsp;<b>Exportation HT</b></td><td>-".$data['ExportationOr']."</td><td>-".$data['ExportationBle']."</td><td>-".$data['ExportationBois']."</td><td>-".$data['ExportationPierre']."</td><td>-".$data['ExportationFer']."</td></tr>";

			// Depense
			$DepenseOr 		= $data['DepenseOr'] 	- $data['MilitaireArmees'];
			$DepenseBle 	= $data['DepenseBle'] 	- $data['MilitaireArmees'];
			$DepenseBois	= $data['DepenseBois'];
			$DepensePierre	= $data['DepensePierre'];
			$DepenseFer		= $data['DepenseFer'];

			$DepenseOr 		*= -1;
			$DepenseBle 	*= -1;
			$DepenseBois	*= -1;
			$DepensePierre	*= -1;
			$DepenseFer		*= -1;
			
			$sql2 = "SELECT *
				FROM action
				WHERE ActionJoueur = " . $Joueur;
			$req2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
			while ($data2 = mysql_fetch_array($req2))
			{
				$ActionID 			= $data2['ActionID'];
				$ActionType 		= $data2['ActionType'];
				$ActionInfos		= $data2['ActionInfos'];
				$Split				= explode("/", $ActionInfos);
				switch ( $ActionType ) 
				{
					case "armee":
						$Ordre			= str_replace("Ordre:", "", $Split[0]);
						$Nombre			= str_replace("Nombre:", "", $Split[1]);
						$Modificateur	= ( $Ordre == "Enroler" ) ? 1 : -1;
						$DepenseOr 		-= 1 * $Nombre * $Modificateur;
						$DepenseBle 	-= 1 * $Nombre * $Modificateur;
						$DepenseFer 	-= 1 * $Nombre * $Modificateur;
					break;
				
					case "village":
						$DepenseOr 		-= cout("ville", 1, "or");
						$DepenseBle 	-= cout("ville", 1, "ble");
						$DepenseBois 	-= cout("ville", 1, "bois");
						$DepensePierre 	-= cout("ville", 1, "pierre");
						$DepenseFer 	-= cout("ville", 1, "fer");
					break;

					case "general":
						$Ordre			= str_replace("Ordre:", "", $Split[2]);
						if ( $Ordre == "Enroler" OR $Ordre == "Ameliorer" )
						{
							$DepenseOr	-= str_replace("Prix:", "", $Split[0]);
						}

					break;
		
					case "autoMod":
						$CoutOr 		= str_replace("Or:", "", $Split[1]);
						$CoutBle 		= str_replace("Ble:", "", $Split[2]);
						$CoutBois 		= str_replace("Bois:", "", $Split[3]);
						$CoutFer 		= str_replace("Fer:", "", $Split[4]);
						$CoutPierre 	= str_replace("Pierre:", "", $Split[5]);	
						
						$DepenseOr 		-= $CoutOr;
						$DepenseBle 	-= $CoutBle;
						$DepenseBois 	-= $CoutBois;
						$DepensePierre 	-= $CoutPierre;
						$DepenseFer 	-= $CoutFer;
					break;

				}
			}

			$message .= "<tr><td>&nbsp;&nbsp;&nbsp;<b>Dépenses</b></td><td>".$DepenseOr."</td><td>".$DepenseBle."</td><td>".$DepenseBois."</td><td>".$DepensePierre."</td><td>".$DepenseFer."</td></tr>";

			// Entretien
			$Texte 		= ( $data['MilitaireArmees'] > 1 ) ? $data['MilitaireArmees'] . " armées" : $data['MilitaireArmees'] . " armée";
			$message .= "<tr><td>&nbsp;&nbsp;&nbsp;<b>Entretien</b></td><td>-".$data['MilitaireArmees'] ."</td><td>-".$data['MilitaireArmees'] ."</td><td align=\"center\" colspan=\"3\">".$Texte."</td></tr>";
			$message .= "<tr><td colspan=\"6\" class=\"haut\"><font size=\"2\">Total</font></td></tr>";

			// A la fin du tour
			$FinOr 		= round($data['RessourceOr'] 		- $DepenseOr,2);
			$FinBle 	= round($data['RessourceBle'] 	- $DepenseBle,2);
			$FinBois	= round($data['RessourceBois'] 	- $data['DepenseBois'],2);
			$FinPierre 	= round($data['RessourcePierre'] 	- $data['DepensePierre'],2);
			$FinFer 	= round($data['RessourceFer'] 	- $data['DepenseFer'],2);
			
			$message .= "<tr><td width=\"150px\"><b>Fin du tour</b></td><td>".couleur($FinOr)."</td><td>".couleur($FinBle)."</td><td>".couleur($FinBois)."</td><td>".couleur($FinPierre)."</td><td>".couleur($FinFer)."</td></tr>";

			// Estimation
			$EstimationOr 		= $FinOr 		+ $data['RevenuOr']*($data['TechProdOr']+$BonusCulture)		- $data['MilitaireArmees'] - $data['ExportationOr'] + $data['ImportationOr'];
			$EstimationBle 		= $FinBle 		+ $data['RevenuBle']*($data['TechProdBle']+$BonusCulture) 	+ $data['CommerceBle']	- $data['MilitaireArmees'] - $data['ExportationBle'] + $data['ImportationBle'];
			$EstimationBois 	= $FinBois 		+ $data['RevenuBois']*($data['TechProdBois']+$BonusCulture)  - $data['ExportationBois'] + $data['ImportationBois'];
			$EstimationPierre 	= $FinPierre 	+ $data['RevenuPierre']*($data['TechProdPierre']+$BonusCulture)	 - $data['ExportationPierre'] + $data['ImportationPierre'];
			$EstimationFer 		= $FinFer 		+ $data['RevenuFer']*($data['TechProdFer']+$BonusCulture)	- $data['ExportationFer'] + $data['ImportationFer'];

			
			$message .= "<tr><td><b>Estimation</b></td><td>".round($EstimationOr,2)."</td><td>".round($EstimationBle,2)."</td><td>".round($EstimationBois,2)."</td><td>".round($EstimationPierre,2)."</td><td>".round($EstimationFer,2)."</td></tr>";
		}
		else
		{
			$message = "Erreur dans la liste des ressources";
		}
	
		$message .= "</table>";
		mysql_free_result($req);
		mysql_close();
	break;
	
	case "menu":
		$message = '<ul id="menuduhautul">';
		
		$Joueur = $_POST['Joueur'];
		
		connectMaBase();
		
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			// Ressource
			$BonusCulture = round($data['DevCulture'] / $data['DevVille'] / 3, 2);
			$message .= "
			<li>Or: ".round($data['RessourceOr'],2)." (+".round($data['RevenuOr']*($data['TechProdOr'] + $BonusCulture ), 2).")</li>
			<li>Blé: ".round($data['RessourceBle'],2)." ( +".round($data['RevenuBle']*($data['TechProdBle'] + $BonusCulture ), 2).")</li>
			<li>Bois: ".round($data['RessourceBois'],2)." (+ ".round($data['RevenuBois']*($data['TechProdBois'] + $BonusCulture ), 2).")</li>
			<li>Pierre: ".round($data['RessourcePierre'],2)." (+ ".round($data['RevenuPierre']*($data['TechProdPierre'] + $BonusCulture ), 2).")</li>
			<li>Fer: ".round($data['RessourceFer'],2)." (+ ".round($data['RevenuFer']*($data['TechProdFer'] + $BonusCulture ), 2).")</li>
			<li>Culture: ".round($data['RessourceCulture'],2)." (+ ".round($data['DevCulture'] + ( $data['DevVille'] * 0.2), 2).")</li>";
		}
		else
		{
			$message = "Erreur dans la liste des ressources";
		}
	
		$message .= "</ul>";
		mysql_free_result($req);
		mysql_close();
	break;
	
	// Liste des régions possédées
	case "liste":
		$message = '<table width="900px" cellpadding="5px">';
		$message .= '<tr>
			<td width="18%">			<b>Région</b>	</td>
			<td width="4%">				<b>Prio</b>		</td>
			<td width="11%">			<b>Ville</b>	</td>
			<td width="5%">				<b>Taxe</b>		</td>
			<td width="5%" colspan="2">	<b>Ressource</b></td>
			<td width="14%">			<b>Economie</b>	</td>
			<td width="14%">			<b>Commerce</b>	</td>
			<td width="14%">			<b>Défense</b>	</td>
			<td width="14%">			<b>Culture</b>	</td>
			</tr>';

		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		$Ordre 		= $_POST['Ordre'];
		$Classement = $_POST['Classement'];
		
		$RegionID = 0;
		
		connectMaBase();
		$sql = "SELECT TechProdOr, TechProdBle, TechProdBois, TechProdPierre, TechProdFer
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$TechProdOr		= $data['TechProdOr'];
			$TechProdBle	= $data['TechProdBle'];
			$TechProdBois	= $data['TechProdBois'];
			$TechProdPierre	= $data['TechProdPierre'];
			$TechProdFer	= $data['TechProdFer'];
		}
		
		$sql = "SELECT *
			FROM region
			WHERE RegionProprietaire = " . $Joueur . "
				AND RegionPartie = " . $Partie . "
				ORDER BY " . $Classement . " " . $Ordre ;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$RegionID			= $data['RegionID'];
			$RegionNom 			= $data['RegionNom']; 
			if ( $data['RegionOccupant'] != $Joueur && $data['RegionOccupant'] != -1 )
			{
				$OccupantNom = joueur($data['RegionOccupant'], "JoueurPseudo");
				$RegionNom .= "<br /><b>Occupée par <i>" . $OccupantNom . "</i></b>";
			}
			else if ( $data['RegionOccupant'] != $Joueur && $data['RegionOccupant'] == -1 )
			{
				$RegionNom .= "<br /><b>En révolte!</b>";			
			}
			
			$RegionPriorite		= $data['RegionPriorite'];
			$RegionPrioriteData	= "Prio/" . $data['RegionID'];
					
			$VillePriorite	= "<span id=\"" . $RegionPrioriteData . "\">" . $RegionPriorite . "</span>
				<script>
					var url = './includes/ajax.php?mode=priorite';
					var parametres 	= \"mode=priorite\";
					var myAjax = new Ajax.InPlaceEditor(
						$('" . $RegionPrioriteData . "'),
							url, 
								{ajaxOptions:
									{method: \"post\", parameters: parametres}, submitOnBlur: true, okButton: false, cancelLink: false, size: 2, loadingText:'...', 
       							}
       					);
				</script>";
						
			$VilleTaille	 	= cout("ville", $data['VilleTaille'], "nom") . " (".$data['VilleTaille'].")";
			$VilleTailleAm	 	= $data['VilleTaille'] + 1;
			$NextVille			= cout("ville", $VilleTailleAm, "nom");
			$VilleTailleAmTexte	= "Or: &nbsp;&nbsp;&nbsp;&nbsp;" . cout("ville", $VilleTailleAm, "or") . "<br />Blé: &nbsp;&nbsp;&nbsp;" . cout("ville", $VilleTailleAm, "ble") . "<br />Bois: &nbsp;&nbsp;" . cout("ville", $VilleTailleAm, "bois") . "<br />Pierre: " . cout("ville", $VilleTailleAm, "pierre") . "<br />Fer: &nbsp;&nbsp;&nbsp;" . cout("ville", $VilleTailleAm, "fer");

			$VilleEconomieAm	= $data['VilleEconomie'] + 1;
			$NextEconomie		= cout("economie", $VilleEconomieAm, "nom");
			$VilleEconomieAmTexte	= "Or: &nbsp;&nbsp;&nbsp;&nbsp;" . cout("economie", $VilleEconomieAm, "or") . "<br />Blé: &nbsp;&nbsp;&nbsp;" . cout("economie", $VilleEconomieAm, "ble") . "<br />Bois: &nbsp;&nbsp;" . cout("economie", $VilleEconomieAm, "bois") . "<br />Pierre: " . cout("economie", $VilleEconomieAm, "pierre") . "<br />Fer: &nbsp;&nbsp;&nbsp;" . cout("economie", $VilleEconomieAm, "fer");

			$VilleCommerceAm	= $data['VilleCommerce'] + 1;
			$NextCommerce		= cout("commerce", $VilleCommerceAm, "nom");
			$NextCommerce		= str_replace(" de commerce", "", $NextCommerce);
			$VilleCommerceAmTexte	= "Or: &nbsp;&nbsp;&nbsp;&nbsp;" . cout("commerce", $VilleCommerceAm, "or") . "<br />Blé: &nbsp;&nbsp;&nbsp;" . cout("commerce", $VilleCommerceAm, "ble") . "<br />Bois: &nbsp;&nbsp;" . cout("commerce", $VilleCommerceAm, "bois") . "<br />Pierre: " . cout("commerce", $VilleCommerceAm, "pierre") . "<br />Fer: &nbsp;&nbsp;&nbsp;" . cout("commerce", $VilleCommerceAm, "fer");
			
			$VilleMilitaireAm	= $data['VilleMilitaire'] + 1;
			$NextMilitaire		= cout("militaire", $VilleMilitaireAm, "nom");
			$VilleMilitaireAmTexte	= "Or: &nbsp;&nbsp;&nbsp;&nbsp;" . cout("militaire", $VilleMilitaireAm, "or") . "<br />Blé: &nbsp;&nbsp;&nbsp;" . cout("militaire", $VilleMilitaireAm, "ble") . "<br />Bois: &nbsp;&nbsp;" . cout("militaire", $VilleMilitaireAm, "bois") . "<br />Pierre: " . cout("militaire", $VilleMilitaireAm, "pierre") . "<br />Fer: &nbsp;&nbsp;&nbsp;" . cout("militaire", $VilleMilitaireAm, "fer");
			
			$VilleCultureAm	 	= $data['VilleCulture'] + 1;
			$NextCulture		= cout("culture", $VilleCultureAm, "nom");
			$VilleCultureAmTexte	= "Or: &nbsp;&nbsp;&nbsp;&nbsp;" . cout("culture", $VilleCultureAm, "or") . "<br />Blé: &nbsp;&nbsp;&nbsp;" . cout("culture", $VilleCultureAm, "ble") . "<br />Bois: &nbsp;&nbsp;" . cout("culture", $VilleCultureAm, "bois") . "<br />Pierre: " . cout("culture", $VilleCultureAm, "pierre") . "<br />Fer: &nbsp;&nbsp;&nbsp;" . cout("culture", $VilleCultureAm, "fer");
			
			$RessourceOr	 	= ( $data['VilleTaille'] == 3 ) ? 4 : $data['VilleTaille'];
			$RessourceOr		*= $TechProdOr;
			
			$RegionRessource 	= ucfirst(strtolower($data['RegionRessource']));
			$RegionEconomie 	= cout("economie", $data['VilleEconomie'], "nom") . " (".$data['VilleEconomie'].")";
			$RegionDefense 		= cout("militaire", $data['VilleMilitaire'], "nom") . " (".$data['VilleMilitaire'].")";
			$RegionCulture	 	= cout("culture", $data['VilleCulture'], "nom") . " (".$data['VilleCulture'].")";
			$RegionCommerce	 	= str_replace(" de commerce", "", cout("commerce", $data['VilleCommerce'], "nom")) . " (".$data['VilleCommerce'].")";

			$AmeliorationVille	= ( $VilleTailleAm <= 3 ) ? "<br /><a href=\"#Vil".$RegionID.$VilleTailleAm."\" onmouseover=\"montre('".$VilleTailleAmTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'ville', ".$VilleTailleAm.")\">++ ".$NextVille."</a>" : "";
			$AmeliorationEconomie	= ( $data['VilleEconomie'] <= 2 ) ? "<br /><a href=\"#Eco".$RegionID.$VilleEconomieAm."\" onmouseover=\"montre('".$VilleEconomieAmTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'economie', ".$VilleEconomieAm.")\">++ ".$NextEconomie."</a>" : "";
			$AmeliorationCommerce	= ( $data['VilleCommerce'] <= 2 ) ? "<br /><a href=\"#Com".$RegionID.$VilleCommerceAm."\" onmouseover=\"montre('".$VilleCommerceAmTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'commerce', ".$VilleCommerceAm.")\">++ ".$NextCommerce."</a>" : "";
			$AmeliorationMilitaire	= ( $data['VilleMilitaire'] <= 2 ) ? "<br /><a href=\"#Mil".$RegionID.$VilleMilitaireAm."\" onmouseover=\"montre('".$VilleMilitaireAmTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'militaire', ".$VilleMilitaireAm.")\">++ ".$NextMilitaire."</a>" : "";
			$AmeliorationCulture	= ( $data['VilleCulture'] <= 2 ) ? "<br /><a href=\"#Cul".$RegionID.$VilleCultureAm."\" onmouseover=\"montre('".$VilleCultureAmTexte."');\" onmouseout=\"cache();\" onclick=\"construire(".$RegionID.", 'culture', ".$VilleCultureAm.")\">++ ".$NextCulture."</a>" : "";
			
			switch ( $data['RegionRessource'] )
			{
				case "BLE":
					$multiple = $TechProdBle;
				break;
				case "BOIS":
					$multiple = $TechProdBois;
				break;
				case "PIERRE":
					$multiple = $TechProdPierre;
				break;
				case "FER":
					$multiple = $TechProdFer;
				break;
			}
			$RegionProduction		= ( $data['VilleEconomie'] <= 1 ) ? $data['VilleEconomie'] + 1: ( ( $data['VilleEconomie'] == 2 ) ? 4 : 7);
			$RegionProduction		*= $multiple;
			$message .= "<tr><td>".$RegionNom."</td><td align=\"center\">".$VillePriorite."</td><td>".$VilleTaille."".$AmeliorationVille."</td><td align=\"center\">".$RessourceOr."</td><td>".$RegionRessource."</td><td>".$RegionProduction."</td><td>".$RegionEconomie."".$AmeliorationEconomie."</td><td>".$RegionCommerce."".$AmeliorationCommerce."</td><td>".$RegionDefense."".$AmeliorationMilitaire."</td><td>".$RegionCulture."".$AmeliorationCulture."</td></td></tr>";
		}
		$message .= "</table>";

		if ( !$RegionID ) 
		{
			$message = "Vous n'avez pas région sous votre contrôle. Looser";
		}
	
		mysql_free_result($req);
		mysql_close();
	break;
	
	case "technologies":
		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		
		$message = "<table width=\"900px\" cellpadding=\"2px\">";
		
		for ( $i = 1; $i < count($technologies); $i++ )
		{
			$technologieNom			= $technologies[$i]["Nom"];
			$technologieTexte		= $technologies[$i]["Texte"];
			$technologieNiveau		= $technologies[$i]["Niveau"];
			$technologieParent		= $technologies[$i]["Parent"];
			$technologiePrerequis	= $technologies[$i]["Prerequis"];
			$technologieCoutOr		= $technologies[$i]["CoutOr"] ? "&nbsp;&nbsp;&nbsp;Or: " . $technologies[$i]["CoutOr"] : "";
			$technologieCoutBle		= $technologies[$i]["CoutBle"] ? "&nbsp;&nbsp;&nbsp;Blé: " . $technologies[$i]["CoutBle"] : "";;
			$technologieCoutBois	= $technologies[$i]["CoutBois"] ? "&nbsp;&nbsp;&nbsp;Bois: " . $technologies[$i]["CoutBois"] : "";;
			$technologieCoutPierre	= $technologies[$i]["CoutPierre"] ? "&nbsp;&nbsp;&nbsp;Pierre: " . $technologies[$i]["CoutPierre"] : "";;
			$technologieCoutFer		= $technologies[$i]["CoutFer"] ? "&nbsp;&nbsp;&nbsp;Fer: " . $technologies[$i]["CoutFer"] : "";;
			
			$technologieCouts		= $technologieCoutOr . $technologieCoutBle . $technologieCoutBois . $technologieCoutPierre . $technologieCoutFer;

			$texte 					= "<a href=\"#Techno".$i."\" onmouseover=\"montre('".$technologieTexte."');\" onmouseout=\"cache();\" onclick=\"technologie(".$i.")\">".$technologieNom."</a>";
			$technologieFilles[$i] 	= technologieFille($i);
			
			$TechnologiePossedee = FALSE;
			$PrerequisOK = FALSE;
			$TechnologiesPossedees = joueur($Joueur, "Technologies");
			
			$TechnologiesPossedeesArray = explode(",", $TechnologiesPossedees);

			for ($r = 0; $r <= count($TechnologiesPossedeesArray); $r++ )
			{
				if ( $TechnologiesPossedeesArray[$r] == $i )
				{
					$TechnologiePossedee = TRUE;
				}
				if ( $TechnologiesPossedeesArray[$r] == $technologiePrerequis )
				{
					$PrerequisOK = TRUE;
				}
			}
			
			if ( $TechnologiePossedee == FALSE && $technologieNiveau && $PrerequisOK)
			{
				$technologieTexteArray[$i] = "<a href=\"#Techno".$i."\" onmouseover=\"montre('".$technologieTexte."');\" onmouseout=\"cache();\" onclick=\"action('technologie', " . $i . ", 0, 0, 0, 0, 0)\">".$technologieNom."</a><br /><font size=\"1\">" . $technologieTexte . "</font><br /><font size=\"1\">" . $technologieCouts . "</font>";
			}
			else if ( $TechnologiePossedee )
			{
				$technologieTexteArray[$i] = "<font color=\"blue\">".$technologieNom."</font><br /><font size=\"1\">" . $technologieTexte . "</font><br /><font size=\"1\">" . $technologieCouts . "</font><font size=\"1\"><br /><a href=\"#MontrerTechno".$i."\" onclick=\"montrerTechnologie(".$i.")\">Montrer</a></font>";
			}
			else
			{
				$technologieTexteArray[$i] = $technologieNom."<br /><font size=\"1\">" . $technologieTexte . "</font><br /><font size=\"1\">" . $technologieCouts . "</font>";
			}
			$technologieFilles[$i] = technologieFille($i);
			$message .= $technologie[$i];
		}
		for ( $h = 1 ; $h < count($technologies) ; $h++ )
		{
			$TechnoParent	= $technologies[$h]["Parent"];
			if ( $TechnoParent == 0 )
			{
				// On ne génère que si c'est à la racine
				$message .= arbreTechnologies($h, $technologieFilles, $technologieTexteArray, FALSE);
			}
		}
		$message .= "</table>";
	break;
	
	case "montrerTechnologie":
		$Technologie 	= $_POST['Technologie'];
		$Joueur 		= $_POST['Joueur'];
		$Partie 		= $_POST['Partie'];
		
		$TechnologieNom = $technologies[$Technologie]['Nom'];
		
		$JoueurNom	= joueur($Joueur, "JoueurPseudo");
		$Tour	= tour($Partie, "PartieTour");
		$texte = $JoueurNom . " dispose de la technologie <i>".$TechnologieNom."</i>";
		
		message(0, $texte, $Tour, $Partie);
	break;
	
	case "espionformer":
		$Joueur 	= $_POST['Joueur'];
		$Nom 		= $_POST['espionNom'];

		$Camouflage = $_POST['Camouflage'];
		$Reussite 	= $_POST['Reussite'];
		$Resistance	= $_POST['Resistance'];
		
		if ( is_numeric($Camouflage) == FALSE OR is_numeric($Reussite) == FALSE OR is_numeric($Resistance) == FALSE)
		{
			$message = "Nombre Numérique";
			break;
		}
		if ( $Camouflage + $Reussite + $Resistance < 4 )
		{
			$message = "Vous devez allouer au moins 4 points de culture pour former un espion";
			break;		
		}
		if ( !$Nom )
		{
			$message = "Nom de code incorrect";
			break;		
		}
		
		$PrixCulture 	= $Camouflage + $Reussite + $Resistance;
		$PrixOr			= round($PrixCulture / 4, 1);

		connectMaBase();
		
		$sql = "SELECT RessourceCulture
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		
		$RessourceCulture = $data['RessourceCulture'];
		
		if ( $RessourceCulture < $PrixCulture )
		{
			$message = "Erreur: vous n'avez pas assez de points de culture";
			break;
		}

		mysql_close();
		$transaction 		= transaction($Joueur, $PrixOr, 0, 0, 0, 0, 0);

		if ( $transaction == FALSE )
		{
			$message = "Erreur: vous n'avez pas assez d'or";
			break;
		}
		
		connectMaBase();
		$sql = "UPDATE joueur
				SET RessourceCulture = RessourceCulture - ". $PrixCulture . "
					WHERE JoueurID = " . $Joueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		
		$sql = "INSERT INTO espion (espionNom, espionJoueur, espionFurtivite, espionSucces, espionResistance)
			VALUES ('".$Nom."', '".$Joueur."', " . $Camouflage . ", " . $Reussite . ", " . $Resistance . ")";
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());	
		mysql_free_result($req);
	
		$message = "Votre espion a été formé";
		mysql_close();
		
	break;
	
	case "espionstatut":
		$Partie 		= $_POST['Partie'];
		$espionID 		= $_POST['espionID'];
		$espionStatut 	= $_POST['espionStatut'];

		connectMaBase();
		$sql = "SELECT *
			FROM espion
			WHERE espionID = " . $espionID;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		
		$NomCapture = joueur($data['espionCapture'], "JoueurPseudo");
		$NomChef 	= joueur($data['espionJoueur'], "JoueurPseudo");
		$IDChef 	= $data['espionJoueur'];
		$espionNom 	= $data['espionNom'];
		mysql_free_result($req);
		mysql_close();
		
		$Tour = tour($Partie, "PartieTour");

		connectMaBase();
		switch ( $espionStatut )
		{
			case "LIBERER":
				$sql = "UPDATE espion
						SET espionStatut = 0
							WHERE espionID = " . $espionID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				
				$textePublic = $NomCapture . " a libéré un espion ennemi";
				message(0, $textePublic, $Tour, $Partie);

				$texteChef = $NomCapture . " a libéré un de vos espions";
				message($IDChef, $texteChef, $Tour, $Partie);

				$message = "Vous avez libérer cet espion";
			break;
			case "TUER":
				$sql = "DELETE FROM espion
					WHERE espionID = " . $espionID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				
				$textePublic = $NomCapture . " a exécuté un espion ennemi";
				message(0, $textePublic, $Tour, $Partie);

				$texteChef = $NomCapture . " a exécuté votre espion <i>".$espionNom."</i>";
				message($IDChef, $texteChef, $Tour, $Partie);

				$message = "Vous avez exécuté cet espion";
			break;
		}		
	break;

	case "espiontest":
		$Camouflage = $_POST['Camouflage'];
		$Reussite 	= $_POST['Reussite'];
		$Resistance	= $_POST['Resistance'];
		
		if ( is_numeric($Camouflage) == FALSE OR is_numeric($Reussite) == FALSE OR is_numeric($Resistance) == FALSE)
		{
			$message = "Nombre Numérique";
			break;
		}
		$PrixCulture 	= $Camouflage + $Reussite + $Resistance;
		$PrixOr			= round($PrixCulture / 4, 1);
		
		$DiscressionVol 	= ( round($Camouflage * 90 / 5, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 5, 2);
		$ReussiteVol 		= ( round($Reussite * 90 / 4, 2) > 90 ) ? 90 : round($Reussite * 90 / 8, 2);
		$ResistanceVol 		= round($Resistance * 90 / 7, 2);

		$DiscressionDetournement 	= ( round($Camouflage * 90 / 10, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 10, 2);
		$ReussiteDetournement 		= ( round($Reussite * 90 / 6, 2) > 90 ) ? 90 : round($Reussite * 90 / 6, 2);
		$ResistanceDetournement 	= round($Resistance * 90 / 9, 2);

		$DiscressionRevolte 	= ( round($Camouflage * 90 / 4, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 4, 2);
		$ReussiteRevolte 		= ( round($Reussite * 90 / 11, 2) > 90 ) ? 90 : round($Reussite * 90 / 11, 2);
		$ResistanceRevolte 		= round($Resistance * 90 / 7, 2);

		$DiscressionArmee 	= ( round($Camouflage * 90 / 10, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 10, 2);
		$ReussiteArmee 		= ( round($Reussite * 90 / 10, 2) > 90 ) ? 90 : round($Reussite * 90 / 10, 2);
		$ResistanceArmee 	= round($Resistance * 90 / 10, 2);

		$DiscressionProtectorat 	= ( round($Camouflage * 90 / 15, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 15, 2);
		$ReussiteProtectorat 		= ( round($Reussite * 90 / 10, 2) > 90 ) ? 90 : round($Reussite * 90 / 10, 2);
		$ResistanceProtectorat 		= round($Resistance * 90 / 13, 2);

		$DiscressionCorruption 		= ( round($Camouflage * 90 / 20, 2) >= 90 ) ? 90 : round($Camouflage * 90 / 20, 2);
		$ReussiteCorruption 		= ( round($Reussite * 90 / 8, 2) > 90 ) ? 90 : round($Reussite * 90 / 8, 2);
		$ResistanceCorruption 		= round($Resistance * 90 / 15, 2);
		
		$message = "
			<table cellpadding=\"4\">
				<tr>
					<td><b>Opération</b></td>
					<td colspan=\"2\"><b>Discretion</b></td>
					<td><b>Réussite</b></td>
					<td><b>Silence</b></td>
				</tr>
				<tr>
					<td>Vol de plan</td>
					<td>" . $DiscressionVol . "%</td>
					<td>1</td>
					<td>" . $ReussiteVol . "%</td>
					<td>" . $ResistanceVol . "%</td>
				</tr>
				<tr>
					<td>Détournement</td>
					<td>" . $DiscressionDetournement . "%</td>
					<td>1</td>
					<td>" . $ReussiteDetournement . "%</td>
					<td>" . $ResistanceDetournement . "%</td>
				</tr>
				<tr>
					<td>Révolte</td>
					<td>" . $DiscressionRevolte . "%</td>
					<td>2</td>
					<td>" . $ReussiteRevolte . "%</td>
					<td>" . $ResistanceRevolte . "%</td>
				</tr>
				<tr>
					<td>Trahison armée</td>
					<td>" . $DiscressionArmee . "%</td>
					<td>1</td>
					<td>" . $ReussiteArmee . "%</td>
					<td>" . $ResistanceArmee . "%</td>
				</tr>
				<tr>
					<td>Protectorat</td>
					<td>" . $DiscressionProtectorat . "%</td>
					<td>2</td>
					<td>" . $ReussiteProtectorat . "%</td>
					<td>" . $ResistanceProtectorat . "%</td>
				</tr>
				<tr>
					<td>Corruption</td>
					<td>" . $DiscressionCorruption . "%</td>
					<td>2</td>
					<td>" . $ReussiteCorruption . "%</td>
					<td>" . $ResistanceCorruption . "%</td>
				</tr>
			</table>
			<br />Prix : " . $PrixOr . " Or et " . $PrixCulture . " Points de culture";
	break;
	
	case "Contreespionnage":
		$Joueur 	= $_POST['Joueur'];	
		$contre	 	= $_POST['contre'];
		
		connectMaBase();
		$sql = "SELECT RessourceCulture, DevCulture, DevVille, Contreespionnage
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);

		$RessourceCulture = $data['RessourceCulture'];
		$DevCulture = $data['DevCulture'];
		$DevVille = $data['DevVille'];
		
		if ( $contre >= ( $DevCulture + ( $DevVille * 0.2 ) ) )
		{
			$message = "Vous ne pouvez pas allouer plus de point de culture que vous en gagnez par tour";
			break;
		}
		$sql = "UPDATE Joueur
			SET ContreEspionnage = " . $contre . "
				WHERE JoueurID = " . $Joueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		$message = "Contre espionnage alloué";
	break;
	
	case "CreerMission":
		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		
		$Type	 	= $_POST['MissionType'];
		$Espion	 	= $_POST['AssignerEspion'];
		$Cible	 	= $_POST['espionCible'];

		switch ( $Type )
		{
			case "VOL-ECO":
			case "VOL-COMMERCE":
			case "VOL-TECHNO":
			case "VOL-POLITIQUE":
				$CibleJoueur 	= $Cible;
				$CibleLieu		= 0;
				$TourFin		= tour($Partie, "PartieTour") + 1;
			break;
			case "ARMEE":
			case "DETOURNEMENT-OR":
			case "DETOURNEMENT-RESSOURCE":
				$CibleLieu 		= $Cible;
				$CibleJoueur	= 0;
				$TourFin		= tour($Partie, "PartieTour") + 1;
			break;
			case "REVOLTE":
			case "PROTECTORAT":
			case "CORRUPTION":
				$CibleLieu 		= $Cible;
				$CibleJoueur	= 0;
				$TourFin		= tour($Partie, "PartieTour") + 2;
			break;
		}
		connectMaBase();
		$sql = "SELECT *
			FROM espion
			WHERE espionID = " . $Espion;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		
		if ( $data['espionStatut'] != 0 )
		{
			$message = "Espion indisponible pour une mission: pick up un other";
			break;
		}
		
		$sql = "INSERT INTO espionnage (espionnagePartie, espionnageEspion, espionnageCibleJoueur, espionnageCibleType, espionnageCibleLieu, espionnageTourFin)
			VALUES ('".$Partie."', ".$Espion.", ".$CibleJoueur.", '".$Type."', ".$CibleLieu.", ".$TourFin.")";		
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);

		$sql = "UPDATE espion
			SET espionStatut = 1
				WHERE espionID = " . $Espion;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
			
		mysql_close();
		$message = "La mission a été lancée et se terminera, en cas de succès, au tour n°" . $TourFin;

	break;
	
	case "espionnage":
		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		
		connectMaBase();

		$espion = FALSE;
		
		$sql = "SELECT RessourceCulture, DevCulture, DevVille, Contreespionnage
			FROM joueur
			WHERE JoueurID = " . $Joueur;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		
		$RessourceCulture	= $data['RessourceCulture'];
		$DevCulture			= $data['DevCulture'];
		$DevVille			= $data['DevVille'];
		$Contreespionnage	= $data['Contreespionnage'];
		$RevenuCulture		= round( ( $DevVille / 5 ) + $DevCulture, 2);
		
		$message = "Vous avez " . $RessourceCulture . " " . $points . " de culture disponibles (+".$RevenuCulture." par tour), à assigner à l'espionnage ou au contre-espionnage";
		$messageEspion = "";
		$messageCreerEspion = "";
		$messageMission = "";
		$messageCreerMission = "";
		$messageContreEspionnage = "";
		
		$TableauJoueur = Array();
		$TableauRegion = Array();
		
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurPartie = " . $Partie . "
				ORDER BY JoueurPseudo ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$JoueursDeroulant .= "<option value=\"". $data['JoueurID']."\">===== ".$data['JoueurPseudo'] . " =====</option>";

			$JoueurID = $data['JoueurID'];
			
			$TableauJoueur[$JoueurID] = $data['JoueurPseudo'];
			
			$sql2 = "SELECT *
				FROM region
				WHERE RegionProprietaire = " . $JoueurID . "
					ORDER BY RegionNom ASC";
			$req2 = mysql_query($sql2) or die('Erreur SQL !<br />'.$sql2.'<br />'.mysql_error());
			while ($data2 = mysql_fetch_array($req2))
			{
				$RegionID = $data2['RegionID'];
				$TableauRegion[$RegionID] = $data2['RegionNom'];
				$JoueursDeroulant .= "<option value=\"". $data2['RegionID']."\">-- ". $data2['RegionNom'] . "</option>";
			}
		}
		
		mysql_free_result($req);
		
		$sql = "SELECT *
			FROM espion
			WHERE espionJoueur = " . $Joueur . "
				ORDER BY espionID ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$espion = TRUE;
			
			$Statut	= ( $data['espionStatut'] == 1 ) ? "En mission" : ( ( $data['espionStatut'] == 0 ) ? "A la caserne" : "Compromis" );
			$EspionListe .= "<tr><td>".$data['espionNom'] . "</td><td>".$Statut . "</td><td>".$data['espionFurtivite'] . "</td><td>".$data['espionSucces'] . "</td><td>".$data['espionResistance'] . "</td></tr>";

			if ( $data['espionStatut'] == 0 )
			{
				$EspionDeroulant .= "<option value=\"". $data['espionID']."\">".$data['espionNom'] . "</option>";
			}
		}
		
		mysql_free_result($req);
		$messageEspion .= ( $espion == TRUE ) ? "<div id=\"monEspionMessage\"></div><table cellpadding=\"5\" style=\"border: none;\"><tr><td>Nom de Code</td><td>Statut</td><td>Furtivité</td><td>Succès</td><td>Résistance</td></tr>" . $EspionListe . "</table>": "Vous n'avez pas d'espion";

		$messageCreerEspion = "</div><h3>Recruter un espion</h3><table cellpadding=\"5\" style=\"border: none;\"><tr><td style=\"border: none;\">Camouflage</td><td style=\"border: none;\">Réussite</td><td style=\"border: none;\">Résistance</td><td style=\"border: none;\">Nom</td><td style=\"border: none;\" rowspan=\"2\"><input type=\"button\" onclick=\"espionTest()\" value=\"Tester cet espion !\"> ou <input type=\"button\" onclick=\"espionFormer()\" value=\"Former cet espion !\"></td></tr><tr><td style=\"border: none;\" align=\"center\"><input type=\"text\" id=\"espionCamouflage\" size=\"2\" value=\"\"></td><td style=\"border: none;\" align=\"center\"><input type=\"text\" id=\"espionReussite\" size=\"2\" value=\"\"></td><td style=\"border: none;\" align=\"center\"><input type=\"text\" id=\"espionResistance\" size=\"2\" value=\"\"></td><td style=\"border: none;\" align=\"center\"><input type=\"text\" id=\"espionNom\" size=\"10\" value=\"\"></td></tr></table><div id=\"monEspionTest\"></div>";

		$messageCreerMission = "<h3>Assigner un espion à une mission</h3><br /><select name=\"AssignerEspion\" id=\"AssignerEspion\">".$EspionDeroulant."</select>&nbsp;&nbsp;<select id=\"MissionType\" name=\"MissionType\"><option value=\"VOL-ECO\">Espionnage économique</option><option value=\"VOL-TECHNO\">Espionnage technologique</option><option value=\"VOL-COMMERCE\">Espionnage commerciale</option><option value=\"VOL-POLITIQUE\">Espionnage politique</option><option value=\"DETOURNEMENT-RESSOURCE\">Voler des ressources</option><option value=\"DETOURNEMENT-OR\">Voler de l'or</option><option value=\"REVOLTE\">Encourager une révolte</option><option value=\"ARMEE\">Corrompre une armée</option><option value=\"PROTECTORAT\">Instaurer un protectorat</option></select> sur <select name=\"espionCible\" id=\"espionCible\" onselect=\"espionRegion()\">".$JoueursDeroulant."</select> <input type=\"button\" onclick=\"creerMission()\" value=\"Lancer la mission\"><br />Sélectionner un nom de joueur pour de l'espionnage (les 4 permiers choix), ou une région pour les autres choix";

		$messageMission = "<h3>Suivre vos missions</h3><table cellpadding=\"5\" style=\"border: none;\"><tr><td>Espion</td><td>Mission</td><td>Cible</td><td>Tour</td><td>Furtivité</td><td>Réussite</td><td>Resistance</td></tr>";

		$sql = "SELECT nn.*, e.*
			FROM espionnage nn, espion e
			WHERE e.espionJoueur = " . $Joueur . "
				AND nn.espionnageEspion = e.espionID
				AND nn.espionnagePartie = " . $Partie . "
				ORDER BY nn.espionnageID ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			switch ($data['espionnageCibleType'])
			{
				case "VOL-ECO":
					$Type = "Espionnage économique";
					$Cible = $TableauJoueur[$data['espionnageCibleJoueur']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 5, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 5, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 5, 2);
				break;
				case "VOL-COMMERCE":
					$Type = "Espionnage commerciale";
					$Cible = $TableauJoueur[$data['espionnageCibleJoueur']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 5, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 5, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 5, 2);
				break;
				case "VOL-TECHNO":
					$Type = "Espionnage technologique";
					$Cible = $TableauJoueur[$data['espionnageCibleJoueur']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 5, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 5, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 5, 2);
				break;
				case "VOL-POLITIQUE":
					$Type = "Espionnage politique";
					$Cible = $TableauJoueur[$data['espionnageCibleJoueur']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 5, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 5, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 5, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 5, 2);
				break;
				case "ARMEE":
					$Type = "Corruption d'une armée";
					$Cible = $TableauJoueur[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 10, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 10, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 10, 2);
				break;
				case "REVOLTE":
					$Type = "Pousser à la révolte";
					$Cible = $TableauRegion[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 4, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 4, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 11, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 11, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 7, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 7, 2);
				break;
				case "DETOURNEMENT-OR":
					$Type = "Voler de l'or";
					$Cible = $TableauRegion[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 10, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 6, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 6, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 9, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 9, 2);
				break;
				case "DETOURNEMENT-RESSOURCE":
					$Type = "Voler une ressource";
					$Cible = $TableauRegion[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 10, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 6, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 6, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 9, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 9, 2);
				break;
				case "CORRUPTION":
					$Type = "Instaurer un protectorat";
					$Cible = $TableauRegion[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 20, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 20, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 8, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 8, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 15, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 15, 2);
				break;
				case "PROTECTORAT":
					$Type = "Instaurer un protectorat";
					$Cible = $TableauRegion[$data['espionnageCibleLieu']];
					$Furtivite = ( round($data['espionFurtivite'] * 90 / 15, 2) >= 90 ) ? 90 : round($data['espionFurtivite'] * 90 / 15, 2);
					$Reussite = ( round($data['espionSucces'] * 90 / 10, 2) >= 90 ) ? 90 : round($data['espionSucces'] * 90 / 10, 2);
					$Resistance = ( round($data['espionResistance'] * 90 / 13, 2) >= 90 ) ? 90 : round($data['espionResistance'] * 90 / 13, 2);
				break;
			}
			
			$messageMission .= "<tr><td>".$data['espionNom'] . "</td><td>".$Type . "</td><td>".$Cible . "</td><td>".$data['espionnageTourFin'] . "</td><td>".$Furtivite . "%</td><td>".$Reussite . "%</td><td>".$Resistance . "%</td></tr>";
		}
		$messageMission .= "</table>";
		
		$EspionCaptureListe = "";
		$sql = "SELECT *
			FROM espion
			WHERE espionStatut = -1
				AND espionCapture = " . $Joueur . "
				ORDER BY espionID ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$espionCapture = TRUE;
			
			$EspionCaptureListe .= "<tr><td>".$data['espionNom'] . "</td><td><a href=\"#Liberer".$data['espionNom']."\" onclick=\"espionStatut(".$data['espionID'].", 'LIBERER')\">Libérer</a></td><td><a href=\"#Tuer".$data['espionNom']."\" onclick=\"espionStatut(".$data['espionID'].", 'TUER')\">Exécuter</a></td></tr>";
		}
		$EspionCaptureListe = $espionCapture ? $EspionCaptureListe : "<tr><td>Aucun espion n'est retenu dans vos geoles</td></tr>";
		mysql_free_result($req);
		
		$messageContreEspionnage = "<h3>Contre-espionnage</h3><input type=\"text\" id=\"contreespionnage\" size=\"2\" value=\"".$Contreespionnage."\"><input type=\"button\" onclick=\"Contreespionnage()\" value=\"Contre-espionner\"><br /><table cellpadding=\"5\" style=\"border: none;\"><tr><td colspan=\"3\" align=\"center\"><b>Espions capturés</b></td></tr>".$EspionCaptureListe."</table>";

		$message .= "<br /><br />" . $messageEspion . "<br /><br />" . $messageCreerEspion . "<br /><br />" . $messageCreerMission . "<br /><br /><br />" . $messageMission . "<br /><br />" . $messageContreEspionnage;

		mysql_close();
	break;

	case "raccourcis":
		$Joueur 	= $_POST['Joueur'];
		$Partie 	= $_POST['Partie'];
		
		$tableauRessource = Array("BLE", "BOIS", "PIERRE", "FER");
		$message = "<table cellpadding=\"4px\">";
		
		for ( $i = 0; $i < 4; $i++ )
		{
			$Ressource 	= $tableauRessource[$i];
			$color		= ( $i == 0 OR $i == 2 ) ? "e1e1e1" : "white";
			$message .= "<tr><td width=\"100px\" rowspan=\"3\" style=\"background-color:".$color.";\" align=\"center\" class=\"haut\"><font size=\"3\">" . $Ressource . "</font></td>";
		
			for ( $j = 0; $j < 3; $j++ )
			{
				$Construction	= cout("economie", $j+1, "nom");
				$Ajout			= ( $j == 0 ) ? 1 : ( ( $j == 1 ) ? 2 : 3 );
				
				$message .= "<td style=\"background-color:".$color.";\"> + ".$Ajout." " . ucfirst(strtolower($Ressource)) . "</td><td style=\"background-color:".$color.";\"><table width=\"250px\" style=\"border-style:none;\">";
				$message .= rechercheRaccourci($Joueur, $Ressource, $j, "0");
				$message .= "</table></td></tr><tr>";
			}
			
			$message .= "</td></tr>";
		}
		$message .= "</table>";
	break;

	// Liste des messages
	case "message":
	case "messagePerso":
		$Partie = $_POST['Partie'];
		$Tour 	= $_POST['Tour'] ? $_POST['Tour'] : 0;
		$Joueur = ( $mode == "messagePerso" ) ? $_POST['Joueur'] : 0;
		
		connectMaBase();

		if ( !$Tour )
		{
			$sql = "SELECT PartieTour
				FROM partie
				WHERE PartieID = " . $Partie;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				$Tour = $data['PartieTour'];
			}
			mysql_free_result($req);
		}

		$message = "";
		$sql = "SELECT MessageTexte
			FROM message
			WHERE MessageTour = " . $Tour . "
				AND MessagePartie = " . $Partie . "
				AND MessageJoueur = " . $Joueur . "
				ORDER BY MessageID DESC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$texte = str_replace("&lt;", "<", $data['MessageTexte']);
			$texte = str_replace("&gt;", ">", $texte);
			$message .= "<tr><td style=\"border: none;\" border=\"0\">".$texte . "</td></tr>";
		}

		if ( !$message ) 
		{
			$message = ( $mode == "messagePerso" ) ? "Aucun message personnel pour le moment" : "Aucun nouvel évènement pour le moment";
		}
		else
		{
			$message = "<table cellpadding=\"3\" style=\"border: none;\" border=\"0\">".$message."</table>";
		}
	
		mysql_free_result($req);
		mysql_close();
	break;
	

	// Administration : créer une région
	case "creer":
		$RegionNom 			= $_POST['RegionNom'];
		$RegionRessource 	= $_POST['RegionRessource'];
		$RegionProprietaire = $_POST['RegionProprietaire'];
		$RegionPartie 		= $_POST['RegionPartie'];
		$message = "Création terminée de la région: " . $RegionNom;
	
		if ( empty($RegionNom) != FALSE ) 
		{
			$message = "Pas de nom de région...";
			break;
		}
		connectMaBase();
		
		$sql = "SELECT *
			FROM region
			WHERE RegionNom = '" . $RegionNom . "'";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$message = "Une région avec ce nom existe déjà";
			mysql_free_result($req);
		}
		else
		{
			mysql_free_result($req);
			mysql_close();
	
			$CoutOr 		= cout("ville", 1, "or");
			$CoutBle 		= cout("ville", 1, "ble");
			$CoutBois 		= cout("ville", 1, "bois");
			$CoutPierre 	= cout("ville", 1, "pierre");
			$CoutFer 		= cout("ville", 1, "fer");
					
			$transaction = transaction($RegionProprietaire, $CoutOr, $CoutBle, $CoutBois, $CoutPierre, $CoutFer, 0);
			connectMaBase();

			if ( $transaction == FALSE )
			{
				// Pas assez de ressources pour payer le village...
				$message = "Erreur: le joueur n'a pas assez de ressource";
			}
			else
			{
				switch ($RegionRessource )
				{
					case "BLE":
						$mod = "RevenuBle";
					break;
					case "BOIS":
						$mod = "RevenuBois";
					break;
					case "PIERRE":
						$mod = "RevenuPierre";
					break;
					case "FER":
						$mod = "RevenuFer";
					break;
				}
				$sql = "UPDATE joueur
						SET RevenuOr = RevenuOr + 1, " . $mod . " = " . $mod . " + 1, DevVille = DevVille + 1
						WHERE JoueurID = " . $RegionProprietaire;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
			
				$sql = "INSERT INTO region (RegionNom, RegionPartie, RegionRessource, RegionProprietaire, RegionOccupant)
					VALUES ('".$RegionNom."', ".$RegionPartie.", '".$RegionRessource."', ".$RegionProprietaire.", ".$RegionProprietaire.")";
			
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$JoueurNom 			= joueur($RegionProprietaire, "JoueurPseudo");
				$historique			= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a construit un village dans la région <i>" . $RegionNom . "</i>"));
				$Tour				= tour($RegionPartie, "PartieTour");
				message(0, $historique, $Tour, $RegionPartie);
			}
		}
		// Mettre à jour la production globale et la production régionale
		mysql_close();
	break;

	// Administration : modifier un joueur
	// Modifier ses ressources, et ses revenus pour 1 tour
	case "Mod":
		$ModRessource 	= $_POST['ModRessource'];
		$ModValeur 		= $_POST['ModValeur'];
		$ModJoueur 		= $_POST['ModJoueurID'];
		$SousMode		= $_POST['SousMode'];
		$Partie			= $_POST['Partie'];
		connectMaBase();
		if ( !$ModValeur )
		{
			$message = "Champ manquant";
			break;
		}
		switch ($ModRessource )
		{
			case "OR":
				$mod = "RessourceOr";
				$modi = "RevenuOr";
			break;
			case "BLE":
				$mod = "RessourceBle";
				$modi = "RevenuBle";
			break;
			case "BOIS":
				$mod = "RessourceBois";
				$modi = "RevenuBois";
			break;
			case "PIERRE":
				$mod = "RessourcePierre";
				$modi = "RevenuPierre";
			break;
			case "FER":
				$mod = "RessourceFer";
				$modi = "RevenuFer";
			break;
		}
		switch ( $SousMode )
		{
			// Modifier de bout en blanc
			case "modifier":
				$sql = "UPDATE joueur
						SET " . $mod . " = " . $ModValeur. "
						WHERE JoueurID = " . $ModJoueur;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				$modification = "Ses ressources en " . $ModRessource . " sont désormais égales à " . $ModValeur;
			break;
			
			// AJouter/soustraire X de la ressource
			case "ajouter":
				$sql = "UPDATE joueur
						SET " . $mod . " = " . $mod . " + " . $ModValeur. "
						WHERE JoueurID = " . $ModJoueur;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				$modification = "Ses ressources en " . $ModRessource . " ont été augmentées de " . $ModValeur;
			break;
			
			// Multiplier ou diviser une ressource			
			case "multiplier":
				$ModValeur = $ModValeur / 2;
				$sql = "UPDATE joueur
						SET " . $mod . " = " . $mod . " + " . $modi . " * " . $ModValeur . "
						WHERE JoueurID = " . $ModJoueur;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				$modification = "Ses ressources en " . $ModRessource . " ont été multipliées par " . $ModValeur;
			break;
		}
		mysql_free_result($req);
		mysql_close();

		$JoueurNom 			= joueur($ModJoueur, "JoueurPseudo");
		$historique			= htmlspecialchars(addslashes("<b>Administrateur</b> a modifié <b>".$JoueurNom . "</b>: " . $modification));
		$Tour				= tour($Partie, "PartieTour");
		message(0, $historique, $Tour, $Partie);
		
		$message = "Modification effectuée pour " .$JoueurNom;
	break;
	
	case "Armees":
		$Nombre 	= $_POST['Nombre'];
		$Joueur 	= $_POST['Joueur'];

		if ( !$Nombre )
		{
			$message = "Champ manquant";
			break;
		}
		
		$transaction = transaction($Joueur, 1 * $Nombre, 1 * $Nombre, 1 * $Nombre, 0, 1 * $Nombre, 0);

		if ( $transaction == FALSE )
		{
			$message = "Erreur: le joueur n'a pas assez de ressource";
			break;
		}
			
		connectMaBase();

		if ( $Nombre < 0 )
		{
			$sql = "SELECT MilitaireArmees, JoueurPartie
				FROM joueur
				WHERE JoueurID = " . $Joueur;
			$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			if ($data = mysql_fetch_array($req))
			{
				$Partie = $data['JoueurPartie'];
				if ( $data['MilitaireArmees'] < abs($Nombre) )
				{
					$message = "Le joueur n'a pas autant de troupes";
					break;
				}
			}
			mysql_free_result($req);
		}	
		$sql = "UPDATE joueur
				SET MilitaireArmees = MilitaireArmees + " . $Nombre . ", DepenseOr = DepenseOr + " . $Nombre . ", DepenseBle = DepenseBle + " . $Nombre . "
				WHERE JoueurID = " . $Joueur;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		mysql_free_result($req);
		mysql_close();

		$JoueurNom 	= joueur($Joueur, "JoueurPseudo");
		$texte		= ( $Nombre > 0 ) ? "enrolé" : "démobilisé";
		$armee		= ( abs($Nombre) > 1) ? "armées" : "armée";
		$historique	= htmlspecialchars(addslashes("<b>".$JoueurNom."</b> a ".$texte . " " . abs($Nombre) . " " . $armee));
		$Tour		= tour($Partie, "PartieTour");
		message(0, $historique, $Tour, $Partie);
		
		$message = "Armées modifiées pour " .$JoueurNom;
	break;

	case "Classement":
		
		$Partie			= $_POST['Partie'];
		$Element		= $_POST['Element'];
		$listeJoueur	= '';
		$detail = '<tr>
			<td>Nom</td>
			<td colspan="2">Or</td>
			<td colspan="2">Ble</td>
			<td colspan="2">Bois</td>
			<td colspan="2">Pierre</td>
			<td colspan="2">Fer</td>
			<td colspan="2">Total</td>
			<td colspan="2">Ville</td>
			<td colspan="2">Militaire</td>
			<td colspan="2">Economie</td>
			<td colspan="2">Culture</td>
			<td colspan="2">Armees</td>
			<td colspan="2">Total</td>
			</tr>';	
			
		$Tour				= tour($Partie, "PartieTour");
		$Tour--;

		connectMaBase();
		$sql = "SELECT *
			FROM statistiques
			WHERE StatPartie = " . $Partie . "
				AND StatTour	= " . $Tour . "
				ORDER BY " . $Element . " ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$Total = ceil($data['RevenuOr'] * 2 + $data['RevenuBle'] + $data['RevenuBois'] + $data['RevenuFer'] * 1.5 + $data['RevenuPierre'] * 1.5 + $data['DevVille'] * 2 + $data['DevMilitaire'] + $data['DevCulture'] + $data['DevEconomie'] + $data['MilitaireArmees'] * 2);
			$detail .= "<tr>
			<td>".joueur($data['StatJoueur'], "JoueurPseudo")."</td>
			<td>".$data['StatOrProduction']."</td>
			<td>".$data['StatOrClassement']."</td>
			<td>".$data['StatBleProduction']."</td>
			<td>".$data['StatBleClassement']."</td>
			<td>".$data['StatBoisProduction']."</td>
			<td>".$data['StatBoisClassement']."</td>
			<td>".$data['StatPierreProduction']."</td>
			<td>".$data['StatPierreClassement']."</td>
			<td>".$data['StatFerProduction']."</td>
			<td>".$data['StatFerClassement']."</td>
			<td>".$data['StatProductionPonderee']."</td>
			<td>".$data['StatProductionClassement']."</td>

			<td>".$data['StatVille']."</td>
			<td>".$data['StatVilleClassement']."</td>
			<td>".$data['StatDevMilitaire']."</td>
			<td>".$data['StatDevMilitaireClassement']."</td>
			<td>".$data['StatDevEco']."</td>
			<td>".$data['StatDevEcoClassement']."</td>
			<td>".$data['StatDevCulture']."</td>
			<td>".$data['StatDevCultureClassement']."</td>
			<td>".$data['StatArmees']."</td>
			<td>".$data['StatArmeesClassement']."</td>
			<td>".$data['StatTotal']."</td>
			<td>".$data['StatTotalClassement']."</td>
			</tr>";
		}  
		mysql_free_result($req);  
 		mysql_close();
 		
		$message = '<table cellpadding="5px">' . $detail . '<table>';
		
	break;
	
	case "listeAction":
		
		$Partie			= $_POST['Partie'];

		$listeJoueur	= '';
		connectMaBase();
		$message = "<table width=\"900px\" cellpadding=\"5px\">";
		$message.= "<tr>
						<td width=\"15%\" class=\"haut\">Par</td>
						<td width=\"20%\" class=\"haut\">Demande</td>
						<td width=\"15%\" class=\"haut\">Infos</td>
						<td width=\"4%\" class=\"haut\">Or</td>
						<td width=\"5%\" class=\"haut\">Blé</td>
						<td width=\"6%\" class=\"haut\">Bois</td>
						<td width=\"8%\" class=\"haut\">Pierre</td>
						<td width=\"5%\" class=\"haut\">Fer</td>
						<td width=\"20%\" colspan=\"2\" class=\"haut\">Décision</td></tr>";

		$Joueur		 	= $_POST['Joueur'];
		$oui 			= FALSE;

		$data 			= 0;
		$sql = "SELECT a.*, j.JoueurPseudo
			FROM action a, joueur j
			WHERE a.ActionPartie = " . $Partie . "
				AND j.JoueurID = a.ActionJoueur
				ORDER BY j.JoueurPseudo";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$ouiAction 			= TRUE;
			$Par 				= $data['JoueurPseudo'];
			$ActionID 			= $data['ActionID'];
			$ActionType 		= $data['ActionType'];
			
			$ActionInfos	= $data['ActionInfos'];
			$Split			= explode("/", $ActionInfos);
			
			switch ( $ActionType ) 
			{
				case "technologie":
					$TechnologieID			= str_replace("Technologie:", "", $Split[0]);

					$ActionType 	= "R&D";
					$Nom 			= $technologies[$TechnologieID]["Nom"];
					$CoutOr 		= $technologies[$TechnologieID]["CoutOr"];
					$CoutBle 		= $technologies[$TechnologieID]["CoutBle"];
					$CoutBois 		= $technologies[$TechnologieID]["CoutBois"];
					$CoutPierre 	= $technologies[$TechnologieID]["CoutPierre"];
					$CoutFer 		= $technologies[$TechnologieID]["CoutFer"];
				break;	
				case "armee":					
					$Ordre			= str_replace("Ordre:", "", $Split[0]);
					$Nombre			= str_replace("Nombre:", "", $Split[1]);
					$pluriel		= $Nombre > 1 ? "s" : "";
					$ActionType 	= $Ordre . " " . $Nombre . " armee".$pluriel;
					$CoutOr 		= 1 * $Nombre;
					$CoutBle 		= 1 * $Nombre;
					$CoutBois 		= 0;
					$CoutPierre 	= 0;
					$CoutFer 		= 1 * $Nombre;
					$Nom			= "";
				break;
				case "village":
					$CoutOr 		= cout("ville", 1, "or");
					$CoutBle 		= cout("ville", 1, "ble");
					$CoutBois 		= cout("ville", 1, "bois");
					$CoutPierre 	= cout("ville", 1, "pierre");
					$CoutFer 		= cout("ville", 1, "fer");
					$ActionType 	= "Création de village";

					$Ressource		= str_replace("Ressource:", "", $Split[1]);
					$Nom			= str_replace("Nom:", "", $Split[0]);
					$Nom			.= " (".$Ressource.")";
				break;

				case "autoMod":
					$ActionType 	= "Modif des ressources";				
					$Nom			= str_replace("Motif:", "", $Split[0]);
					$CoutOr 		= str_replace("Or:", "", $Split[1]);
					$CoutBle 		= str_replace("Ble:", "", $Split[2]);
					$CoutBois 		= str_replace("Bois:", "", $Split[3]);
					$CoutFer 		= str_replace("Fer:", "", $Split[4]);
					$CoutPierre 	= str_replace("Pierre:", "", $Split[5]);
				break;
				case "general":
					$CoutOr			= str_replace("Prix:", "", $Split[0]);
					$Nom			= str_replace("Nom:", "", $Split[1]);
					$Ordre			= str_replace("Ordre:", "", $Split[2]);
					$ActionType 	= $Ordre . " un général";
					$CoutBle 		= 0;
					$CoutBois 		= 0;
					$CoutPierre 	= 0;
					$CoutFer 		= 0;
				break;
			}

			$message		.= "<tr>
						<td>".$Par."</td>
						<td>".$ActionType."</td>
						<td>".$Nom."</td>
						<td>".$CoutOr."</td>
						<td>".$CoutBle."</td>
						<td>".$CoutBois."</td>
						<td>".$CoutPierre."</td>
						<td>".$CoutFer."</td>
						<td><a href=\"#Valider".$ActionID."\" onclick=\"validerAction(".$ActionID.", ".$Partie.")\">Valider</a> </td>
						<td><a href=\"#Refuser".$ActionID."\" onclick=\"refuserAction(".$ActionID.", ".$Partie.")\">Refuser</a></td></tr>";
		}
		if ( !$ouiAction )
		{
			$message 	.= "<tr><td colspan=\"9\">Aucune demande</td></tr>";
		}
		$message .= "</table>";
	break;

	// Valider une demande
	case "validerAction":
		$ActionID 	= $_POST['Action'];
		$PartieID 	= $_POST['Partie'];
		
		connectMaBase();
		
		$sql = "SELECT *
			FROM action
			WHERE ActionID = '" . $ActionID . "'";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if (!$data = mysql_fetch_array($req))
		{
			$message = "Erreur: cette demande n'existe pas ou plus";
			mysql_free_result($req);
		}
		else
		{
			$ActionInfos 	= $data['ActionInfos'];
			$ActionType 	= $data['ActionType'];
			$ActionJoueur 	= $data['ActionJoueur'];
			
			$explode		= explode("/", $ActionInfos);					
			mysql_free_result($req);
			mysql_close();
	
			switch ( $ActionType )
			{
				case "armee":
					$Ordre			= str_replace("Ordre:", "", $explode[0]);
					$Nombre			= str_replace("Nombre:", "", $explode[1]);
					$pluriel		= $Nombre > 1 ? "s" : "";
					$CoutOr 		= ( $Ordre == "Enroler" ) ? 1 * $Nombre : 0;
					$CoutBle 		= ( $Ordre == "Enroler" ) ?  1 * $Nombre : 0;
					$CoutBois 		= 0;
					$CoutPierre 	= 0;
					$CoutFer 		= ( $Ordre == "Enroler" ) ? 1 * $Nombre : 0;
				break;
				case "village":
					$Nom			= str_replace("Nom:", "", $explode[0]);
					$Ressource		= str_replace("Ressource:", "", $explode[1]);
					$CoutOr 		= cout("ville", 1, "or");
					$CoutBle 		= cout("ville", 1, "ble");
					$CoutBois 		= cout("ville", 1, "bois");
					$CoutPierre 	= cout("ville", 1, "pierre");
					$CoutFer 		= cout("ville", 1, "fer");
				break;
				case "autoMod":
					$Motif			= str_replace("Motif:", "", $explode[0]);
					$CoutOr			= str_replace("Or:", "", $explode[1]);
					$CoutBle		= str_replace("Ble:", "", $explode[2]);
					$CoutBois		= str_replace("Bois:", "", $explode[3]);
					$CoutFer		= str_replace("Fer:", "", $explode[4]);
					$CoutPierre		= str_replace("Pierre:", "", $explode[5]);
					
					$CoutOr			= $CoutOr * -1;
					$CoutBle		= $CoutBle * -1;
					$CoutBois		= $CoutBois * -1;
					$CoutFer		= $CoutFer * -1;
					$CoutPierre		= $CoutPierre * -1;
				break;
				case "general":
					$Prix			= str_replace("Prix:", "", $explode[0]);
					$Nom			= str_replace("Nom:", "", $explode[1]);
					$Ordre			= str_replace("Ordre:", "", $explode[2]);
					$CoutOr 		= ( $Ordre == "Enroler" ) ? $Prix : 0;
					$CoutBle 		= 0;
					$CoutBois 		= 0;
					$CoutPierre 	= 0;
					$CoutFer 		= 0;
				break;
				case "technologie":
					$TechnologieID		= str_replace("Technologie:", "", $explode[0]);

					$TechnologiePoint 	= $technologies[$TechnologieID]["Point"];
					$TechnologiePointChamp	= $technologies[$TechnologieID]["PointChamp"];
					$TechnologieChamp 	= $technologies[$TechnologieID]["EffetChamp"];
					$TechnologieValeur 	= $technologies[$TechnologieID]["EffetValeur"];
					$TechnologieNom 	= $technologies[$TechnologieID]["Nom"];
					
					$CoutOr 			= $technologies[$TechnologieID]["CoutOr"];
					$CoutBle 			= $technologies[$TechnologieID]["CoutBle"];
					$CoutBois 			= $technologies[$TechnologieID]["CoutBois"];
					$CoutPierre 		= $technologies[$TechnologieID]["CoutPierre"];
					$CoutFer 			= $technologies[$TechnologieID]["CoutFer"];
				break;
			}

			$transaction = transaction($ActionJoueur, $CoutOr, $CoutBle, $CoutBois, $CoutPierre, $CoutFer, 0);
			connectMaBase();

			if ( $transaction == FALSE )
			{
				// Pas assez de ressources pour payer...
				$message = "Erreur: le joueur n'a pas assez de ressource";
			}
			else
			{
				$message = "Action menée à bien";
				switch ( $ActionType )
				{
					case "armee":
						$OrdreTexte = "enrolé";
						if ( $Ordre == "Demobiliser" )
						{
							$OrdreTexte = "démobilisé";
							$sql = "SELECT MilitaireArmees, JoueurPartie
								FROM joueur
								WHERE JoueurID = " . $ActionJoueur;
								$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
							if ($data = mysql_fetch_array($req))
							{
								$Partie = $data['JoueurPartie'];
								if ( $data['MilitaireArmees'] < abs($Nombre) )
								{
									$message = "Le joueur n'a pas autant de troupes";
									break;
								}
							}
							$Nombre = $Nombre  * -1;
							mysql_free_result($req);
						}
						$sql = "UPDATE joueur
							SET MilitaireArmees = MilitaireArmees + " . $Nombre . ", DepenseOr = DepenseOr + " . $Nombre . ", DepenseBle = DepenseBle + " . $Nombre . "
								WHERE JoueurID = " . $ActionJoueur;
						mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
						mysql_free_result($req);
						mysql_close();
						
						$Nombre 		= ( $Ordre == "Demobiliser" ) ? $Nombre * -1 : $Nombre;
						$JoueurNom 		= joueur($ActionJoueur, "JoueurPseudo");					
						$historique		= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a " . $OrdreTexte . " " . $Nombre . " armée" . $pluriel));
						$Tour			= tour($PartieID, "PartieTour");
						message(0, $historique, $Tour, $PartieID);
					break;
					
					case "autoMod":
						$JoueurNom 		= joueur($ActionJoueur, "JoueurPseudo");
						$CoutOr			= $CoutOr * -1;
						$CoutBle		= $CoutBle * -1;
						$CoutBois		= $CoutBois * -1;
						$CoutFer		= $CoutFer * -1;
						$CoutPierre		= $CoutPierre * -1;
						
						$TexteOr		= ( $CoutOr != 0 ) ? "* Or: " . $CoutOr . "<br />": "";
						$TexteBle		= ( $CoutBle != 0 ) ? "* Ble: " . $CoutBle . "<br />": "";
						$TexteBois		= ( $CoutBois != 0 ) ? "* Bois: " . $CoutBois . "<br />": "";
						$TextePierre	= ( $CoutPierre != 0 ) ? "* Pierre: " . $CoutPierre . "<br />": "";
						$TexteFer		= ( $CoutFer != 0 ) ? "* Fer: " . $CoutFer . "<br />": "";
					
						$historique			= htmlspecialchars(addslashes("Changement de ressource de <b>".$JoueurNom . "</b> avec comme motif " . $Motif . "<br />" . $TexteOr. $TexteBle. $TexteBois. $TextePierre. $TexteFer));
						$Tour				= tour($PartieID, "PartieTour");
						message(0, $historique, $Tour, $PartieID);
					break;
					
					case "village":
						switch ($Ressource )
						{
							case "BLE":
								$mod = "RevenuBle";
							break;
							case "BOIS":
								$mod = "RevenuBois";
							break;
							case "PIERRE":
								$mod = "RevenuPierre";
							break;
							case "FER":
								$mod = "RevenuFer";
							break;
						}
						$sql = "UPDATE joueur
								SET RevenuOr = RevenuOr + 1, " . $mod . " = " . $mod . " + 1, DevVille = DevVille + 1
								WHERE JoueurID = " . $ActionJoueur;
						mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
						mysql_free_result($req);

						$sql = "INSERT INTO region (RegionNom, RegionPartie, RegionRessource, RegionProprietaire, RegionOccupant)
							VALUES ('".$Nom."', ".$PartieID.", '".$Ressource."', ".$ActionJoueur.", ".$ActionJoueur.")";
			
						mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
						mysql_free_result($req);
				
						$RessourceTexte		= ucfirst(strtolower($Ressource));
						$JoueurNom 			= joueur($ActionJoueur, "JoueurPseudo");
						$historique			= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a construit un village de " . $RessourceTexte . " dans la région <i>" . $Nom . "</i>"));
						$Tour				= tour($PartieID, "PartieTour");
						message(0, $historique, $Tour, $PartieID);
					break;
					
					case "general":
						if ( $Ordre == "Enroler" )
						{
							$sql = "UPDATE joueur
									SET GeneralNombre = GeneralNombre + 1, GeneralPuissance = GeneralPuissance + " . $Prix . "
									WHERE JoueurID = " . $ActionJoueur;
							mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
							mysql_free_result($req);
						}
						else
						{
							$sql = "UPDATE joueur
									SET GeneralNombre = GeneralNombre - 1, GeneralPuissance = GeneralPuissance - " . $Prix . "
									WHERE JoueurID = " . $ActionJoueur;
							mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
							mysql_free_result($req);
						}
				
						$JoueurNom 			= joueur($ActionJoueur, "JoueurPseudo");
						$historique			= ( $Ordre == "Enroler" ) ? htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a enrolé <i>" . $Nom . "</i> pour " . $Prix . " pièces d'or")) : htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a démobilisé <i>" . $Nom . "</i>"));
						$Tour				= tour($PartieID, "PartieTour");
						message(0, $historique, $Tour, $PartieID);
					break;
					case "technologie":
						$sql = "SELECT Technologies
							FROM joueur
							WHERE JoueurID = " . $ActionJoueur;
							$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
						if ($data = mysql_fetch_array($req))
						{
							$TechnologiesListe = $data['Technologies'];
						}
						mysql_free_result($req);
						
						$TechnologiesListeNouvelle = $TechnologiesListe . "," . $TechnologieID;
						
						if ( $TechnologieChamp )
						{
							$sql = "UPDATE joueur
								SET " . $TechnologiePointChamp . " = " . $TechnologiePointChamp . " + " . $TechnologiePoint . ", " . $TechnologieChamp . " = " . $TechnologieValeur . ", Technologies = '" . $TechnologiesListeNouvelle. "'
									WHERE JoueurID = " . $ActionJoueur;
						}
						else
						{
							$sql = "UPDATE joueur
								SET " . $TechnologiePointChamp . " = " . $TechnologiePointChamp . " + " . $TechnologiePoint . ", Technologies = '" . $TechnologiesListeNouvelle. "'
									WHERE JoueurID = " . $ActionJoueur;
						}						
						mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
						mysql_free_result($req);
						mysql_close();
						
						$JoueurNom 		= joueur($ActionJoueur, "JoueurPseudo");					
						$historique		= htmlspecialchars(addslashes("<b>".$JoueurNom . "</b> a développé la technologie nommée <i>" . $TechnologieNom . "</i>"));
						$Tour			= tour($PartieID, "PartieTour");
						message(0, $historique, $Tour, $PartieID);
					break;
				}
			}
		}
	case "refuserAction":
		if ( $mode == "refuserAction" )
		{
			$message = "Annulé";
		}
		$ActionID 	= $_POST['Action'];
	
		connectMaBase();
		$sql = "DELETE FROM action
			WHERE ActionID = " . $ActionID;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		mysql_close();
					
	break;
	// Liste des joueurs
	case "listeJoueur":
		
		$Partie			= $_POST['Partie'];

		$listeJoueur	= '';
		connectMaBase();
		$detail = '<tr>
			<td class="haut">Nom</td>
			<td class="haut">Or</td>
			<td class="haut">Ble</td>
			<td class="haut">Bois</td>
			<td class="haut">Pierre</td>
			<td class="haut">Fer</td>
			</tr>';		
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$detail .= "<tr>
			<td>".$data['JoueurPseudo']."</td>
			<td>".couleur($data['RessourceOr'])."</td>
			<td>".couleur($data['RessourceBle'])."</td>
			<td>".couleur($data['RessourceBois'])."</td>
			<td>".couleur($data['RessourcePierre'])."</td>
			<td>".couleur($data['RessourceFer'])."</td>
			</tr>";
		}  
		mysql_free_result($req);  
 		mysql_close();
 		
		$message = '<table cellpadding="15px" style="border: solid;" border="1">' . $detail . '<table>';
		
	break;

	// Nouveau tour et production
	case "Statut":
		
		$Region	= $_POST['Region'];
		$Acteur	= $_POST['Acteur'];
		$Action	= $_POST['Action'];
		if ( !$Region )
		{
			$message = "Champ manquant";
			break;
		}
		
		connectMaBase();
		$sql = "SELECT r.*, j.*
			FROM region r, joueur j
			WHERE r.RegionID = " . $Region . "
				AND j.JoueurID = r.RegionProprietaire";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		if ($data = mysql_fetch_array($req))
		{
			$RegionNom 				= $data['RegionNom'];
			$RegionOccupant		 	= $data['RegionOccupant'];
			$Partie		 			= $data['JoueurPartie'];
			$RegionProprietaireNom 	= $data['JoueurPseudo'];
			$RegionProprietaireID 	= $data['RegionProprietaire'];
			$RegionRessource		= $data['RegionRessource'];
			$VilleTaille			= $data['VilleTaille'];
			$VilleEconomie			= $data['VilleEconomie'];
			$VilleMilitaire			= $data['VilleMilitaire'];
			$VilleCulture			= $data['VilleCulture'];
			
			if ( $VilleTaille < 3 )
			{
				$RevenuOr 			= $VilleTaille;
			}
			else
			{
				$RevenuOr 			= 4;
			}
			
			if ( $VilleEconomie < 2 )
			{
				$RevenuRessource 	= 1 + $VilleEconomie;
			}
			else if ( $VilleEconomie == 2 )
			{
				$RevenuRessource 	= 4;
			}
			else 
			{
				$RevenuRessource 	= 7;
			}
			
			switch ($RegionRessource )
			{
				case "BLE":
					$mod = "RevenuBle";
				break;
				case "BOIS":
					$mod = "RevenuBois";
				break;
				case "PIERRE":
					$mod = "RevenuPierre";
				break;
				case "FER":
					$mod = "RevenuFer";
				break;
			}

		}
		mysql_free_result($req);
		mysql_close();
		
		$ActeurNom 				= $Acteur ? joueur($Acteur, "JoueurPseudo") : "";

		connectMaBase();
		switch ( $Action ) 
		{
			case "LIBERATION":
				$sql = "UPDATE region
					SET RegionOccupant = " . $RegionProprietaireID . "
						WHERE RegionID = " . $Region;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$sql = "UPDATE joueur
						SET RevenuOr = RevenuOr + " . $RevenuOr . ", " . $mod . " = " . $mod . " + " . $RevenuRessource . "
						WHERE JoueurID = " . $RegionProprietaireID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$message = "Changement dans la région ". $RegionNom ." libérée...";
				$texte = "La région <i>". $RegionNom ."</i> appartenant à <b>" . $RegionProprietaireNom . "</b> a été libérée du joug de <b>".$ActeurNom."</b>";

			break;
			case "REVOLTE":
				if ( $RegionOccupant != $RegionProprietaireID )
				{
					$message = "Cette région est déjà occupée. Libérez là avant";
					break;
				}
				$sql = "UPDATE region
					SET RegionOccupant = -1
						WHERE RegionID = " . $Region;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$sql = "UPDATE joueur
						SET RevenuOr = RevenuOr - " . $RevenuOr . ", " . $mod . " = " . $mod . " - " . $RevenuRessource . "
						WHERE JoueurID = " . $RegionProprietaireID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$message = "Changement dans la région ". $RegionNom ." révolte...";
				$texte = "La région <i>". $RegionNom ."</i> appartenant à <b>" . $RegionProprietaireNom . "</b> s'est soulevée!";
			break;
			case "OCCUPATION":
				if ( $RegionOccupant != $RegionProprietaireID )
				{
					$message = "Cette région est déjà occupée. Libérez là avant";
					break;
				}
				$sql = "UPDATE region
					SET RegionOccupant = " . $Acteur . "
						WHERE RegionID = " . $Region;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$sql = "UPDATE joueur
						SET RevenuOr = RevenuOr - " . $RevenuOr . ", " . $mod . " = " . $mod . " - " . $RevenuRessource . "
						WHERE JoueurID = " . $RegionProprietaireID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$message = "Changement dans la région ". $RegionNom ." occupée...";
				$texte = "<b>".$ActeurNom."</b> occupe désormais la région <i>". $RegionNom ."</i> appartenant à <b>" . $RegionProprietaireNom . "</b>";
			break;
			case "ANNEXION":
				if ( $RegionOccupant != $Acteur )
				{
					$message = "Le Joueur doit avant occuper cette région avant de pouvoir l'annexer";
					break;
				}
				$sql = "UPDATE region
					SET RegionProprietaire = " . $Acteur. "
						WHERE RegionID = " . $Region;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$sql = "UPDATE joueur
						SET DevVille = DevVille - " . $VilleTaille . ", DevMilitaire = DevMilitaire - " . $VilleMilitaire . ", DevEconomie = DevEconomie - " . $VilleEconomie . ", DevCulture = DevCulture - " . $VilleCulture . "
						WHERE JoueurID = " . $RegionProprietaireID;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
				
				$sql = "UPDATE joueur
						SET RevenuOr = RevenuOr + " . $RevenuOr . ", " . $mod . " = " . $mod . " + " . $RevenuRessource . ", DevVille = DevVille + " . $VilleTaille . ", DevMilitaire = DevMilitaire + " . $VilleMilitaire . ", DevEconomie = DevEconomie + " . $VilleEconomie . ", DevCulture = DevCulture + " . $VilleCulture . "
						WHERE JoueurID = " . $Acteur;
				mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
				mysql_free_result($req);
		
				
				$message = "Changement dans la région ". $RegionNom ." annexée...";
				$texte = "<b>".$ActeurNom."</b> a annexé la région <i>". $RegionNom ."</i> appartenant anciennement à <b>" . $RegionProprietaireNom . "</b>";
			break;
		}
		
		mysql_close();
		$Tour				= tour($Partie, "PartieTour");
		$historique			= htmlspecialchars(addslashes($texte));
		message(0, $historique, $Tour, $Partie);
		message($RegionProprietaireID, $historique, $Tour, $Partie);
		if ( $Acteur )
		{
			message($Acteur, $historique, $Tour, $Partie);
		}
	break;
	
	// Nouveau tour et production
	case "Tour":
		$Partie			= $_POST['Partie'];
		$Ressource		= $_POST['Ressource'];
		$Modificateur	= $_POST['Modificateur'];
		if ( !$Ressource )
		{
			$message = "Champ manquant";
			break;
		}		
		$ModificateurOr 	= ( $Ressource == "OR" ) ? $Modificateur : 1;
		$ModificateurBle 	= ( $Ressource == "BLE" ) ? $Modificateur : 1;
		$ModificateurBois 	= ( $Ressource == "BOIS" ) ? $Modificateur : 1;
		$ModificateurPierre = ( $Ressource == "PIERRE" ) ? $Modificateur : 1;
		$ModificateurFer 	= ( $Ressource == "FER" ) ? $Modificateur : 1;

		$Tour				= tour($Partie, "PartieTour");
		connectMaBase();
		$table = Array();
		$compteur = 1;
		$FuturTour = $Tour + 1;
		$sql = "SELECT nn.*, e.*, j.JoueurPseudo
			FROM espionnage nn, espion e, joueur j
			WHERE nn.espionnagePartie = " . $Partie . "
				AND nn.espionnageTourFin <= " . $FuturTour . "
				AND e.espionID = nn.espionnageEspion
				AND e.espionStatut = 1
				AND j.JoueurID = e.espionJoueur";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$espion = TRUE;
			// Des espions travaillent...
			$NomDuProprio = $data['JoueurPseudo'];
			
			$Jouer = ( $data['espionnageTourFin'] == $FuturTour ) ? TRUE : FALSE;
			$detectionEspion = detectionEspion($data['espionID'], $data['espionnageCibleType'], $Jouer, $FuturTour, $NomDuProprio, $Partie);
		}
		mysql_free_result($req);

		connectMaBase();
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$table[$compteur] = Array("nom" => $data['JoueurPseudo'], "dispo" => 1);

			$compteur++;
			$sqll = "INSERT INTO historique (hPartie, hJoueurID, hTour, hRevenuOr, hRevenuBle, hRevenuBois, hRevenuFer, hRevenuPierre, hDevVille, hDevMilitaire, hDevEconomie, hDevCulture, hMilitaireArmees)
				VALUES (" . $Partie . ", " . $data['JoueurID'] . ", " . $Tour . "," . $data['RevenuOr'] . ", " . $data['RevenuBle'] . ", " . $data['RevenuBois'] . ", " . $data['RevenuFer'] . ", " . $data['RevenuPierre'] . ", " . $data['DevVille'] . ", " . $data['DevMilitaire'] . ", " . $data['DevEconomie'] . ", " . $data['DevCulture'] . ", " . $data['MilitaireArmees'] . ")";
			mysql_query($sqll) or die('Erreur SQL !<br />'.$sqll.'<br />'.mysql_error());
		}
		mysql_free_result($req);

		$sql = "UPDATE joueur
				SET RessourceOr = RessourceOr + ImportationOr - ExportationOr + RevenuOr * (TechProdOr + (DevCulture / DevVille / 3) ) * " . $ModificateurOr . ", RessourceBle = RessourceBle + ImportationBle - ExportationBle + RevenuBle * (TechProdBle + (DevCulture / DevVille / 3) ) * " . $ModificateurBle . ", RessourceBois = RessourceBois + ImportationBois - ExportationBois + RevenuBois * (TechProdBois + (DevCulture / DevVille / 3) ) * " . $ModificateurBois . ", RessourcePierre = RessourcePierre + ImportationPierre - ExportationPierre + RevenuPierre * (TechProdPierre + (DevCulture / DevVille / 3) ) * " . $ModificateurPierre . ", RessourceFer = RessourceFer + ImportationFer - ExportationFer + RevenuFer * (TechProdFer + (DevCulture / DevVille / 3) ) * " . $ModificateurFer . ", RessourceCulture = RessourceCulture - Contreespionnage + DevCulture + DevVille * 0.2
					WHERE JoueurPartie = " . $Partie;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);
		
		$sql = "UPDATE partie
				SET PartieTour = PartieTour + 1
					WHERE PartieID = " . $Partie;
		mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		mysql_free_result($req);

		// Commerce...
	
		// Commerce 0: Charger dans un tableau les ressources de tout le monde
		
		// Commerce 1: Selectionner chaque marchand et la région associée

		
		// Statistiques:
		
		$compte	= 1;
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$Joueur 	= $data['JoueurID'];
			
			$Production			= $data['RevenuBle'] * $data['TechProdBle'] + $data['RevenuBois'] * $data['TechProdBois'] + $data['RevenuPierre'] * $data['TechProdPierre'] + $data['RevenuFer'] * $data['TechProdFer'];
			$ProductionPonderee	= $data['RevenuBle'] * $data['TechProdBle'] + $data['RevenuBois'] * $data['TechProdBois'] + $data['RevenuPierre'] * $data['TechProdPierre'] * 2 + $data['RevenuFer'] * $data['TechProdFer'] * 2;

			$Economie = $ProductionPonderee + $data['DevEconomie'];
			$Militaire = $data['DevMilitaire'] * 2 + $data['TechMilitaire'] + $data['TechMilitaireOffensive'] + $data['TechMilitaireDefense'] + $data['TechMilitaireMouvement'] + $data['GeneralNombre']* 4 + $data['GeneralPuissance'] / 2 + $data['MilitaireArmees'];
			$Commerce = ( $data['TechCommerce'] * 3 ) + $data['CommerceMarchandsNombre'] + ( $data['CommerceMarchandsEtrangersNombre'] * 2 ) - $data['ImportationBle'] - $data['ImportationBois'] - $data['ImportationPierre'] - $data['ImportationFer'] + $data['ImportationOr'] + $data['ExportationBle'] + $data['ExportationBois'] + $data['ExportationPierre']*2 + $data['ExportationFer']*2;
			$Stabilite = $data['DevVille'] * 2 + $data['TechProdOr'] * $data['RevenuOr'] + $data['DevCulture'] * 2 + $data['TechRevolte']*2;
			
			$Total				= 	$Economie + $Militaire + $Commerce + $Stabilite;
			
			$statistiques[$compte] = Array(
				"StatJoueur" 				=> $Joueur,
				"StatProduction" 			=> $Production,
				"StatProductionPonderee"	=> $ProductionPonderee,
				"StatProductionClassement"	=> 1,
				"StatOrProduction" 			=> $data['RevenuOr'] * $data['TechProdOr'],
				"StatOrClassement" 			=> 1,
				"StatBleProduction" 		=> $data['RevenuBle'] * $data['TechProdBle'],
				"StatBleClassement" 		=> 1,
				"StatBoisProduction" 		=> $data['RevenuBois'] * $data['TechProdBois'],
				"StatBoisClassement" 		=> 1,
				"StatPierreProduction"  	=> $data['RevenuPierre'] * $data['TechProdPierre'],
				"StatPierreClassement" 		=> 1,
				"StatFerProduction" 		=> $data['RevenuFer'] * $data['TechProdFer'],
				"StatFerClassement" 		=> 1,
				"StatDevEco" 				=> $data['DevEconomie'],
				"StatDevEcoClassement" 		=> 1,
				"StatDevCulture" 			=> $data['DevCulture'],
				"StatDevCultureClassement" 	=> 1,
				"StatDevMilitaire" 			=> $data['DevMilitaire'],
				"StatDevMilitaireClassement" => 1,
				"StatDevVille" 				=> $data['DevVille'],
				"StatDevVilleClassement" 	=> 1,
				"StatArmees" 				=> $data['MilitaireArmees'],
				"StatArmeesClassement" 		=> 1,
				
				"StatEconomie" 		=> $Economie,
				"StatEconomieClassement" 		=> 1,
				"StatMilitaire" 		=> $Militaire,
				"StatMilitaireClassement" 		=> 1,
				"StatCommerce" 		=> $Commerce,
				"StatCommerceClassement" 		=> 1,
				"StatStabilite" 		=> $Stabilite,
				"StatStabiliteClassement" 		=> 1,

				"StatTechCommerce" 				=> $data['TechCommerce'],
				"StatTechCommerceClassement" 	=> 1,
				"StatTechRevolte" 				=> $data['TechRevolte'],
				"StatTechRevolteClassement" 	=> 1,
				
				"StatTechProductionOr" 				=> $data['TechProductionOr'],
				"StatTechProductionOrClassement" 	=> 1,
				"StatTechProductionBle" 			=> $data['TechProductionBle'],
				"StatTechProductionBleClassement" 	=> 1,
				"StatTechProductionBois" 			=> $data['TechProductionBois'],
				"StatTechProductionBoisClassement" 	=> 1,
				"StatTechProductionPierre" 			=> $data['TechProductionPierre'],
				"StatTechProductionPierreClassement" => 1,
				"StatTechProductionFer" 			=> $data['TechProductionFer'],
				"StatTechProductionFerClassement" 	=> 1,
				
				"StatGeneraux" 							=> ($data['GeneralNombre'] * 4) + $data['GeneralPuissance'],
				"StatGenerauxClassement" 				=> 1,
				"StatTechTechMilitaireGeneral" 			=> $data['TechMilitaire'],
				"StatTechMilitaireGeneralClassement" 	=> 1,
				"StatTechTechMilitaireOffensive" 		=> $data['TechMilitaireOffensive'],
				"StatTechMilitaireOffensiveClassement" 	=> 1,
				"StatTechTechMilitaireDefense" 			=> $data['TechMilitaireDefense'],
				"StatTechMilitaireDefenseClassement" 	=> 1,
				"StatTechTechMilitaireMouvement" 		=> $data['TechMilitaireMouvement'],
				"StatTechMilitaireMouvementClassement" 	=> 1,
				"StatTechMilitaire" 					=> $data['TechMilitaire'] + $data['TechMilitaireOffensive'] + $data['TechMilitaireDefense'] + $data['TechMilitaireMouvement'],
				"StatTechMilitaireClassement" 			=> 1,
				"StatTotal"			 		=> $Total,
				"StatTotalClassement" 		=> 1);
			$compte++;
		}
		mysql_free_result($req);
		
		// On prend les joueurs 1 par 1
		for ( $i = 1; $i <= count($statistiques); $i++)
		{
			$JoueurID	= $statistiques[$i]["StatJoueur"];
			
			// On compare avec les infos des autres
			for ( $y = 1; $y <= count($statistiques); $y++)
			{
				// Economie
				$statistiques[$i]["StatProductionClassement"] 		= statistiqueClassement($statistiques[$i]["StatProductionClassement"], 		$statistiques[$i]["StatProductionPonderee"],$statistiques[$y]["StatProductionPonderee"]);
				$statistiques[$i]["StatDevEcoClassement"] 			= statistiqueClassement($statistiques[$i]["StatDevEcoClassement"], 			$statistiques[$i]["StatDevEco"], 			$statistiques[$y]["StatDevEco"]);

				$statistiques[$i]["StatBleClassement"] 				= statistiqueClassement($statistiques[$i]["StatBleClassement"], 			$statistiques[$i]["StatBleProduction"], 	$statistiques[$y]["StatBleProduction"]);
				$statistiques[$i]["StatBoisClassement"] 			= statistiqueClassement($statistiques[$i]["StatBoisClassement"], 			$statistiques[$i]["StatBoisProduction"], 	$statistiques[$y]["StatBoisProduction"]);
				$statistiques[$i]["StatPierreClassement"] 			= statistiqueClassement($statistiques[$i]["StatPierreClassement"], 			$statistiques[$i]["StatPierreProduction"], 	$statistiques[$y]["StatPierreProduction"]);
				$statistiques[$i]["StatFerClassement"] 				= statistiqueClassement($statistiques[$i]["StatFerClassement"], 			$statistiques[$i]["StatFerProduction"], 	$statistiques[$y]["StatFerProduction"]);
				$statistiques[$i]["StatTechProductionBleClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechProductionBleClassement"], 			$statistiques[$i]["StatTechProductionBleProduction"], 	$statistiques[$y]["StatTechProductionBleProduction"]);
				$statistiques[$i]["StatTechProductionBoisClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechProductionBoisClassement"], 			$statistiques[$i]["StatTechProductionBoisProduction"], 	$statistiques[$y]["StatTechProductionBoisProduction"]);
				$statistiques[$i]["StatTechProductionPierreClassement"] 	= statistiqueClassement($statistiques[$i]["StatTechProductionPierreClassement"], 			$statistiques[$i]["StatTechProductionPierreProduction"], 	$statistiques[$y]["StatTechProductionPierreProduction"]);
				$statistiques[$i]["StatTechProductionFerClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechProductionFerClassement"], 			$statistiques[$i]["StatTechProductionFerProduction"], 	$statistiques[$y]["StatTechProductionFerProduction"]);

				// Commerce
				$statistiques[$i]["StatTechCommerceClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechCommerceClassement"], 			$statistiques[$i]["StatTechCommerce"], 	$statistiques[$y]["StatTechCommerce"]);
				
				
				// Stabilité 
				$statistiques[$i]["StatDevVilleClassement"] 		= statistiqueClassement($statistiques[$i]["StatDevVilleClassement"], 		$statistiques[$i]["StatDevVille"], 			$statistiques[$y]["StatDevVille"]);
				$statistiques[$i]["StatDevCultureClassement"] 		= statistiqueClassement($statistiques[$i]["StatDevCultureClassement"], 		$statistiques[$i]["StatDevCulture"], 		$statistiques[$y]["StatDevCulture"]);
				$statistiques[$i]["StatOrClassement"] 				= statistiqueClassement($statistiques[$i]["StatOrClassement"], 				$statistiques[$i]["StatOrProduction"], 		$statistiques[$y]["StatOrProduction"]);
				$statistiques[$i]["StatTechProductionOrClassement"] 				= statistiqueClassement($statistiques[$i]["StatTechProductionOrClassement"], 				$statistiques[$i]["StatTechProductionOrProduction"], 		$statistiques[$y]["StatTechProductionOrProduction"]);
				$statistiques[$i]["StatTechRevolteClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechRevolteClassement"], 			$statistiques[$i]["StatTechRevolte"], 	$statistiques[$y]["StatTechRevolte"]);
				
				// Militaire
				$statistiques[$i]["StatArmeesClassement"] 			= statistiqueClassement($statistiques[$i]["StatArmeesClassement"], 			$statistiques[$i]["StatArmees"], 			$statistiques[$y]["StatArmees"]);
				$statistiques[$i]["StatGenerauxClassement"] 	= statistiqueClassement($statistiques[$i]["StatGenerauxClassement"], 	$statistiques[$i]["StatGeneraux"], 		$statistiques[$y]["StatGeneraux"]);
				$statistiques[$i]["StatDevMilitaireClassement"] 	= statistiqueClassement($statistiques[$i]["StatDevMilitaireClassement"], 	$statistiques[$i]["StatDevMilitaire"], 		$statistiques[$y]["StatDevMilitaire"]);
				$statistiques[$i]["StatTechMilitaireGeneralClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechMilitaireGeneralClassement"], 			$statistiques[$i]["StatTechMilitaireGeneral"], 	$statistiques[$y]["StatTechMilitaireGeneral"]);
				$statistiques[$i]["StatTechMilitaireOffensiveClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechMilitaireOffensiveClassement"], 			$statistiques[$i]["StatTechMilitaireOffensive"], 	$statistiques[$y]["StatTechMilitaireOffensive"]);
				$statistiques[$i]["StatTechMilitaireDefenseClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechMilitaireDefenseClassement"], 			$statistiques[$i]["StatTechMilitaireDefense"], 	$statistiques[$y]["StatTechMilitaireDefense"]);
				$statistiques[$i]["StatTechMilitaireMouvementClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechMilitaireMouvementClassement"], 			$statistiques[$i]["StatTechMilitaireMouvement"], 	$statistiques[$y]["StatTechMilitaireMouvement"]);
				$statistiques[$i]["StatTechMilitaireClassement"] 		= statistiqueClassement($statistiques[$i]["StatTechMilitaireClassement"], 			$statistiques[$i]["StatTechMilitaire"], 	$statistiques[$y]["StatTechMilitaire"]);

				// Total
				$statistiques[$i]["StatEconomieClassement"] 		= statistiqueClassement($statistiques[$i]["StatEconomieClassement"], 		$statistiques[$i]["StatEconomie"],$statistiques[$y]["StatEconomie"]);
				$statistiques[$i]["StatMilitaireClassement"] 		= statistiqueClassement($statistiques[$i]["StatMilitaireClassement"], 		$statistiques[$i]["StatMilitaire"],$statistiques[$y]["StatMilitaire"]);
				$statistiques[$i]["StatStabiliteClassement"] 		= statistiqueClassement($statistiques[$i]["StatStabiliteClassement"], 		$statistiques[$i]["StatStabilite"],$statistiques[$y]["StatStabilite"]);
				$statistiques[$i]["StatCommerceClassement"] 		= statistiqueClassement($statistiques[$i]["StatCommerceClassement"], 		$statistiques[$i]["StatCommerce"],$statistiques[$y]["StatCommerce"]);
				$statistiques[$i]["StatTotalClassement"] 			= statistiqueClassement($statistiques[$i]["StatTotalClassement"], 			$statistiques[$i]["StatTotal"], 			$statistiques[$y]["StatTotal"]);
			}
		}
		// On met à jour
		
		for ( $i = 1; $i <= count($statistiques); $i++)
		{
			$JoueurID					= $statistiques[$i]["StatJoueur"];

			$StatProduction				= $statistiques[$i]["StatProduction"];
			$StatProductionPonderee		= $statistiques[$i]["StatProductionPonderee"];
			$StatProductionClassement	= $statistiques[$i]["StatProductionClassement"];

			$StatOrProduction			= $statistiques[$i]["StatOrProduction"];
			$StatOrClassement			= $statistiques[$i]["StatOrClassement"];
			$StatBleProduction			= $statistiques[$i]["StatBleProduction"];
			$StatBleClassement			= $statistiques[$i]["StatBleClassement"];
			$StatBoisProduction			= $statistiques[$i]["StatBoisProduction"];
			$StatBoisClassement			= $statistiques[$i]["StatBoisClassement"];
			$StatPierreProduction		= $statistiques[$i]["StatPierreProduction"];
			$StatPierreClassement		= $statistiques[$i]["StatPierreClassement"];
			$StatFerProduction			= $statistiques[$i]["StatFerProduction"];
			$StatFerClassement			= $statistiques[$i]["StatFerClassement"];

			$StatTechCommerce				= $statistiques[$i]["StatTechCommerce"];
			$StatTechCommerceClassement		= $statistiques[$i]["StatTechCommerceClassement"];

			$StatTechRevolte				= $statistiques[$i]["StatTechRevolte"];
			$StatTechRevolteClassement		= $statistiques[$i]["StatTechRevolteClassement"];

			$StatTechProductionOr				= $statistiques[$i]["StatTechProductionOr"];
			$StatTechProductionOrClassement		= $statistiques[$i]["StatTechProductionOrClassement"];
			$StatTechProductionBle				= $statistiques[$i]["StatTechProductionBle"];
			$StatTechProductionBleClassement	= $statistiques[$i]["StatTechProductionBleClassement"];
			$StatTechProductionBois				= $statistiques[$i]["StatTechProductionBois"];
			$StatTechProductionBoisClassement	= $statistiques[$i]["StatTechProductionBoisClassement"];
			$StatTechProductionPierre			= $statistiques[$i]["StatTechProductionPierre"];
			$StatTechProductionPierreClassement	= $statistiques[$i]["StatTechProductionPierreClassement"];
			$StatTechProductionFer				= $statistiques[$i]["StatTechProductionFer"];
			$StatTechProductionFerClassement	= $statistiques[$i]["StatTechProductionFerClassement"];

			$StatTechMilitaireGeneral				= $statistiques[$i]["StatTechMilitaireGeneral"];
			$StatTechMilitaireGeneralClassement		= $statistiques[$i]["StatTechMilitaireGeneralClassement"];
			$StatTechMilitaireOffensive				= $statistiques[$i]["StatTechMilitaireOffensive"];
			$StatTechMilitaireOffensiveClassement		= $statistiques[$i]["StatTechMilitaireOffensiveClassement"];
			$StatTechMilitaireDefense				= $statistiques[$i]["StatTechMilitaireDefense"];
			$StatTechMilitaireDefenseClassement		= $statistiques[$i]["StatTechMilitaireDefenseClassement"];
			$StatTechMilitaireMouvement				= $statistiques[$i]["StatTechMilitaireMouvement"];
			$StatTechMilitaireMouvementClassement		= $statistiques[$i]["StatTechMilitaireMouvementClassement"];

			$StatDevVille				= $statistiques[$i]["StatDevVille"];
			$StatDevVilleClassement		= $statistiques[$i]["StatDevVilleClassement"];
			$StatDevMilitaire			= $statistiques[$i]["StatDevMilitaire"];
			$StatDevMilitaireClassement	= $statistiques[$i]["StatDevMilitaireClassement"];
			$StatDevEco					= $statistiques[$i]["StatDevEco"];
			$StatDevEcoClassement		= $statistiques[$i]["StatDevEcoClassement"];
			$StatDevCulture				= $statistiques[$i]["StatDevCulture"];
			$StatDevCultureClassement	= $statistiques[$i]["StatDevCultureClassement"];

			$StatArmees					= $statistiques[$i]["StatArmees"];
			$StatArmeesClassement		= $statistiques[$i]["StatArmeesClassement"];
			$StatGeneraux				= $statistiques[$i]["StatGeneraux"];
			$StatGenerauxClassement		= $statistiques[$i]["StatGenerauxClassement"];

			$StatEconomie				= $statistiques[$i]["StatEconomie"];
			$StatEconomieClassement		= $statistiques[$i]["StatEconomieClassement"];
			$StatMilitaire				= $statistiques[$i]["StatMilitaire"];
			$StatMilitaireClassement	= $statistiques[$i]["StatMilitaireClassement"];
			$StatStabilite				= $statistiques[$i]["StatStabilite"];
			$StatStabiliteClassement	= $statistiques[$i]["StatStabiliteClassement"];
			$StatCommerce				= $statistiques[$i]["StatCommerce"];
			$StatCommerceClassement		= $statistiques[$i]["StatCommerceClassement"];
			$StatTotal					= $statistiques[$i]["StatTotal"];
			$StatTotalClassement		= $statistiques[$i]["StatTotalClassement"];
						
			$sql = "INSERT INTO statistiques (StatPartie, StatJoueur, StatTour, StatOrProduction, StatOrClassement, StatBleProduction, StatBleClassement, StatBoisProduction, StatBoisClassement, StatPierreProduction, StatPierreClassement, StatFerProduction, StatFerClassement, StatProduction, StatProductionPonderee, StatProductionClassement, StatDevEco, StatDevEcoClassement, StatDevCulture, StatDevCultureClassement, StatDevMilitaire,  StatDevMilitaireClassement, StatVille, StatVilleClassement, StatArmees, StatArmeesClassement, StatTotal, StatTotalClassement, StatEconomie, StatEconomieClassement, StatMilitaire, StatMilitaireClassement, StatStabilite, StatStabiliteClassement, StatCommerce, StatCommerceClassement, StatTechRevolte, StatTechRevolteClassement, StatTechCommerce, StatTechCommerceClassement)
				VALUES (" . $Partie . ", " . $JoueurID . ", " . $Tour . ", " . $StatOrProduction . ", " . $StatOrClassement . ", " . $StatBleProduction . ", " . $StatBleClassement . ", " . $StatBoisProduction . ", " . $StatBoisClassement . ", " . $StatPierreProduction . ", " . $StatPierreClassement . ", " . $StatFerProduction . ", " . $StatFerClassement . ", " . $StatProduction . ", " . $StatProductionPonderee . ", " . $StatProductionClassement . ", " . $StatDevEco . ", " . $StatDevEcoClassement . ", " . $StatDevCulture . ", " . $StatDevCultureClassement . ", " . $StatDevMilitaire . ", " . $StatDevMilitaireClassement . ", " . $StatDevVille . ", " . $StatDevVilleClassement . ", " . $StatArmees . ", " . $StatArmeesClassement . ", " . $StatTotal . ", " . $StatTotalClassement . ", " . $StatEconomie . ", " . $StatEconomieClassement . ", " . $StatMilitaire . ", " . $StatMilitaireClassement . ", " . $StatStabilite . ", " . $StatStabiliteClassement . ", " . $StatCommerce . ", " . $StatCommerceClassement . ", " . $StatTechRevolte . ", " . $StatTechRevolteClassement . ", " . $StatTechCommerce . ", " . $StatTechCommerceClassement . ")";
			mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
			
		}		
		// Test:
		for ( $i = 1; $i < count($statistiques); $i++)
		{
			$RangOr 	= $statistiques[$i]["StatOrClassement"];
			$RangPierre = $statistiques[$i]["StatPierreClassement"];
			$RangFer = $statistiques[$i]["StatFerClassement"];
			$RangBois = $statistiques[$i]["StatBoisClassement"];
			$RangBle = $statistiques[$i]["StatBleClassement"];
			$Joueur = Joueur($statistiques[$i]["StatJoueur"], "JoueurPseudo");
			$message .= $Joueur . " - Or: " .$RangOr ."<br />";
			$message .= $Joueur . " - Ble: " .$RangBle ."<br />";
			$message .= $Joueur . " - Bois: " .$RangBois ."<br />";
			$message .= $Joueur . " - Pierre: " .$RangPierre ."<br />";
			$message .= $Joueur . " - Fer: " .$RangFer ."<br /><br />";
		}
		mysql_close();
		

		// Ordre de combat
		$Ordre = "";	
		
		for ( $i = 1; $i < $compteur; $i++ )
		{
			$ok = FALSE;
			do
			{
				$test = mt_rand(1, $compteur - 1);
				if ( $table[$test]["dispo"] == 1 )
				{
					$table[$test]["dispo"] = 0;
					$ok = TRUE;
				}
			}
			while ( $ok == FALSE );
			$Ordre .= $i . " - " . $table[$test]["nom"] . " <br />";
		}
		
		$Tour				= tour($Partie, "PartieTour");
		$historique			= htmlspecialchars(addslashes("<b>Tour n° " . $Tour . "</b>:  " . $Ressource . " x " . $Modificateur . "<br />" . $Ordre));
		message(0, $historique, $Tour, $Partie);
		
		$message = "Vous êtes passés à un nouveau tour #" . $Tour;

	break;
}
echo $message;

?>