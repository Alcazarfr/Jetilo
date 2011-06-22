<?php
// includes/ajax/administration.php
// Reprend les requêtes AJAX pour l'administration


// Inclusion des fonctions
define(ROOT_PATH, '../../');
include ROOT_PATH.'config.php';
include ROOT_PATH.'includes/fonctions.php';

// Connexion à la BDD
connectMaBase();

//

//


// On récupère l'ID de la partie
$Partie = 0;
$Partie = isset($_POST['Partie']) ? $_POST['Partie'] : (isset($_GET['Partie']) ? $_GET['Partie'] : 0);

// On récupère le mode = l'action à effeectuer
$mode = "";
$mode = isset($_POST['mode']) ? $_POST['mode'] : (isset($_GET['mode']) ? $_GET['mode'] : "aucun");

$message = $mode;
// On précise le code à exécuter
switch ( $mode )
{
	// Affichage de la liste des joueurs
	case "ModificationJoueur":
		$message = '';
		
		$sql = "SELECT j.JoueurNom, j.JoueurID
			FROM Joueur j, Etat e
			WHERE e.EtatPartie = " . $Partie . "
				AND j.JoueurID = e.EtatJoueur";
		$req = mysql_query($sql) or die('Erreur SQL #42!<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$message .= '<option value="' . $data['JoueurID'] . '">' . $data['JoueurNom'] . '</option>';
		}  
		mysql_free_result($req);   		
	break;
	case "modifierChamp":
		$ChampNom 		= $_POST['ChampNom'];
		$ChampValeur 	= $_POST['ChampValeur'];
		
		$explode 	= explode('-', $ChampNom);
		$Partie 	= $explode[0];
		$Type 		= $explode[1];
		$Reference 	= $explode[2];
		
		$MessageNom = $Reference;
		
		$message = FALSE;
		switch ( $Type )
		{
			case "PartieStatut":
				$sql = "UPDATE Partie
					SET " . $Type . " = '" . $ChampValeur . "'
						WHERE PartieID = " . $Reference;
				mysql_query($sql) or die('Erreur SQL #037<br />'.$sql.'<br />'.mysql_error());
				
				$message = TRUE;
				
				if ( $ChampValeur == 1 )
				{
					$sql = "UPDATE Etat
						SET EtatDerniereProduction = " . time() . "
							WHERE EtatPartie = " . $ChampValeur;
					mysql_query($sql) or die('Erreur SQL #048<br />'.$sql.'<br />'.mysql_error());
				}
				$Texte = $ChampValeur == 1 ? "La partie a été lancée. Actualisez la page pour jouer" : "Partie en pause";
				Message($Partie, 0, "Administration", $Texte, 0, "", "noire", 10);
			break;
			
			case "JoueurNom":
			case "JoueurAdmin":
				$sql = "UPDATE Joueur
					SET " . $Type . " = '" . $ChampValeur . "'
						WHERE JoueurID = " . $Reference;
				mysql_query($sql) or die('Erreur SQL #037<br />'.$sql.'<br />'.mysql_error());
				$MessageNom = Attribut($Reference, "Joueur", "JoueurNom");
			break;
			
			case "EtatPopulationCivil":
			case "EtatPopulationMilitaire":
			case "EtatPopulationCommerce":
			case "EtatPopulationReligion":
				$Recherche 		= Attribut($Reference, "Etat", Array("EtatPopulationCivil", "EtatPopulationMilitaire", "EtatPopulationCommerce", "EtatPopulationReligion"));
				$AncienneValeur = $Recherche[$Type];
				$Difference		= ( $AncienneValeur - $ChampValeur > 0 ) ? $AncienneValeur - $ChampValeur : $ChampValeur - $AncienneValeur;
				$Variation		= ( $Difference * 100 ) / $AncienneValeur;
				$Total			= $Recherche["EtatPopulationCivil"] + $Recherche["EtatPopulationMilitaire"] + $Recherche["EtatPopulationCommerce"] + $Recherche["EtatPopulationReligion"] - $AncienneValeur + $ChampValeur;

			case "EtatTaxe":
				$Variation = $Variation ? $Variation : 1;
				if ( $Total	> 100 )
				{
					$sql = "SELECT EtatJoueur
						FROM Etat
						WHERE EtatID = " . $Reference;
					$req = mysql_query($sql) or die('Erreur SQL #46<br />'.$sql.'<br />'.mysql_error());
					$data = mysql_fetch_array($req);
					
					Message($Partie, $data['EtatJoueur'], "Population", "Cette modification est impossible car vous dépasseriez les 100%", 0, "", "noire", 5);
					echo $AncienneValeur;
					exit;
				}
				else if ( $Variation > 35 )
				{
					$sql = "SELECT EtatJoueur
						FROM Etat
						WHERE EtatID = " . $Reference;
					$req = mysql_query($sql) or die('Erreur SQL #46<br />'.$sql.'<br />'.mysql_error());
					$data = mysql_fetch_array($req);
					
					Message($Partie, $data['EtatJoueur'], "Population", "Cette modification est impossible car la variation est supérieure à 35%", 0, "", "noire", 5);
					echo $AncienneValeur;
					exit;
				}
				$message = TRUE;

			case "EtatNom":
			case "EtatCouleur":
			case "EtatPopulation":
			case "EtatCroissance":
			case "EtatPointCivil":
			case "EtatPointReligion":
			case "EtatPointCommerce":
			case "EtatPointMilitaire":
			case "EtatOr":
				$sql = "UPDATE Etat
					SET " . $Type . " = '" . $ChampValeur . "'
						WHERE EtatID = " . $Reference;
				mysql_query($sql) or die('Erreur SQL #041<br />'.$sql.'<br />'.mysql_error());
			$MessageNom = Attribut($Reference, "Etat", "EtatNom");
			break;
			case "TerritoireNom":
			case "TerritoireJoueur":
			case "TerritoirePopulation":
			case "TerritoireCroissance":
				$sql = "UPDATE Territoire
					SET " . $Type . " = '" . $ChampValeur . "'
						WHERE TerritoireID = " . $Reference;
				mysql_query($sql) or die('Erreur SQL #042<br />'.$sql.'<br />'.mysql_error());
			$MessageNom = Attribut($Reference, "Territoire", "TerritoireNom");
			break;
		}
		if ( !$message )
		{
			Message($Partie, 0, "Administration", $Type . " = " . $ChampValeur . " pour " . $MessageNom, 0, "", "noire", 5);
		}
		$message = $ChampValeur;
	break;
	case "listeDeroulanteJoueur":
		$message = '<option value="0">Sélectionner un joueur</option>';
		
		$sql = "SELECT j.JoueurNom, j.JoueurID
			FROM Joueur j, Etat e
			WHERE e.EtatPartie = " . $Partie . "
				AND j.JoueurID = e.EtatJoueur";
		$req = mysql_query($sql) or die('Erreur SQL #39<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$message .= '<option value="' . $data['JoueurID'] . '">' . $data['JoueurNom'] . '</option>';
		}  
		mysql_free_result($req);   		
	break;
	case "listeTerritoires":
	
		$listeJoueur	= '';
		$detail = '<tr>
			<td>ID</td>
			<td>Régions</td>
			<td>Nom du territoire</td>
			<td>Joueur</td>
			<td>Population</td>
			<td>Croissance</td>
			</tr>';
			
		$Regions = "";
		$sql = "SELECT *
			FROM Territoire
			WHERE TerritoirePartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL #40<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{
			$Regions = "";
			$sql = "SELECT RegionID
				FROM Region
				WHERE RegionTerritoire = " . $data['TerritoireID'];
			$req2 = mysql_query($sql) or die('Erreur SQL #41<br />'.$sql.'<br />'.mysql_error());
			while ($data2 = mysql_fetch_array($req2))
			{
				$Regions .= $data2['RegionID'] . "&nbsp;";
			}
			if ( !$Regions )
			{
				$sql = "DELETE FROM Territoire
						WHERE TerritoireID = " . $data['TerritoireID'];
				mysql_query($sql) or die('Erreur SQL #019<br />'.$sql.'<br />'.mysql_error());	
			}
			$detail .= "<tr>
			<td>" . $data['TerritoireID'] . "</td>
			<td>" . $Regions . "</td>
			<td><div class=\"edit\" id=\"".$Partie."-TerritoireNom-".$data['TerritoireID']."\">".$data['TerritoireNom']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-TerritoireJoueur-".$data['TerritoireID']."\">".$data['TerritoireJoueur']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-TerritoirePopulation-".$data['TerritoireID']."\">".$data['TerritoirePopulation']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-TerritoireCroissance-".$data['TerritoireID']."\">".$data['TerritoireCroissance']."</div></td>
			</tr>";
		}  
		mysql_free_result($req);  
 		
		$message = '
			<script type="text/javascript">
			/* In place editing des infos */
 				$(document).ready(function() {
    				$(\'.edit\').editable(\'./includes/ajax/administration.php?mode=modifierChamp&PartieID='.$Partie.'\');
				 });
 			</script>
 			<table cellpadding="3px" style="border: solid;" border="1">' . $detail . '<table>';

	break;
	case "listeJoueurs":
	
		$listeJoueur	= '';
		$detail = '<tr>
			<td>Nom du joueur</td>
			<td>Nom de l\'Etat</td>
			<td>Statut</td>
			<td>Couleur</td>
			<td>Population</td>
			<td>Croissance</td>
			<td>Pts civil</td>
			<td>Pts commerce</td>
			<td>Pts militaire</td>
			<td>Pts religion</td>
			<td>Or</td>
			</tr>';
			
		$sql = "SELECT j.*, e.*
			FROM Joueur j, Etat e
			WHERE e.EtatPartie = " . $Partie . "
				AND j.JoueurID = e.EtatJoueur";
		$req = mysql_query($sql) or die('Erreur SQL #42!<br />'.$sql.'<br />'.mysql_error());

		while ($data = mysql_fetch_array($req))
		{

			$detail .= "<tr>
			<td><div class=\"edit\" id=\"".$Partie."-JoueurNom-".$data['JoueurID']."\">".$data['JoueurNom']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatNom-".$data['EtatID']."\">".$data['EtatNom']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-JoueurAdmin-".$data['JoueurID']."\">".$data['JoueurAdmin']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatCouleur-".$data['EtatID']."\">".$data['EtatCouleur']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatPopulation-".$data['EtatID']."\">".$data['EtatPopulation']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatCroissance-".$data['EtatID']."\">".$data['EtatCroissance']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatPointCivil-".$data['EtatID']."\">".$data['EtatPointCivil']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatPointCommerce-".$data['EtatID']."\">".$data['EtatPointCommerce']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatPointMilitaire-".$data['EtatID']."\">".$data['EtatPointMilitaire']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatPointReligion-".$data['EtatID']."\">".$data['EtatPointReligion']."</div></td>
			<td><div class=\"edit\" id=\"".$Partie."-EtatOr-".$data['EtatID']."\">".$data['EtatOr']."</div></td>
			</tr>";
		}  
		mysql_free_result($req);  
 		
		$message = '
			<script type="text/javascript">
			/* In place editing des infos */
 				$(document).ready(function() {
    				$(\'.edit\').editable(\'./includes/ajax/administration.php?mode=modifierChamp\');
				 });
 			</script>
 			<table cellpadding="3px" style="border: solid;" border="1">' . $detail . '<table>';

	break;
}
mysql_close();
	
echo $message;

?>