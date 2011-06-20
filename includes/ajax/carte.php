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

// Inclusion des fonctions
include '../../config.php';
include '../fonctions.php';

// Connexion à la BDD
connectMaBase();

// On récupère l'ID de la partie
$Partie 		= $_POST['Partie'];

// On récupère le mode = l'action à effeectuer
$mode = $_POST['mode'] ? $_POST['mode'] : ( $_GET['mode'] ? $_GET['mode'] : 'aucun');

if ( !$mode )
{
	$message = "Aucun mode n'est valable";
}

// On précise le code à exécuter
switch ( $mode )
{
	// Affichage de la carte
	case "afficher":

		$PartieStatut = Attribut($Partie, "Partie", "PartieStatut");
		
		// On charge les couleurs des joueurs
		
		$sql = "SELECT t.TerritoireID, e.EtatCouleur
			FROM Territoire t, Etat e
			WHERE t.TerritoirePartie = " . $Partie . "
				AND e.EtatID = t.TerritoireEtat";
		$req = mysql_query($sql) or die('Erreur SQL #015<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req) )
		{
			$TerritoireID 	= $data['TerritoireID'];
			$EtatCouleur 	= $data['EtatCouleur'];
			$Couleur[$TerritoireID] = $EtatCouleur;
		}

		// On charge, dans un tableau, toutes les régions existantes et leur territoire
		$RegionTerritoire 	= Array();
		$RegionID 			= Array();
		$sql = "SELECT *
			FROM Region
			WHERE RegionPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL #015<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req) )
		{
			$CoordonneesX = $data['RegionCoordonneeX'];
			$CoordonneesY = $data['RegionCoordonneeY'];
			$Region['Territoire'][$CoordonneesX][$CoordonneesY] = $data['RegionTerritoire'];
			$Region['ID'][$CoordonneesX][$CoordonneesY] 		= $data['RegionID'];
			$Region['Terrain'][$CoordonneesX][$CoordonneesY] 	= $data['RegionTerrain'];
		}
		$sql = "SELECT MAX(RegionCoordonneeX) AS MaxX, MAX(RegionCoordonneeY) AS MaxY, MIN(RegionCoordonneeX) AS MinX, MIN(RegionCoordonneeY) AS MinY
			FROM Region
			WHERE RegionPartie = " . $Partie;
		$req = mysql_query($sql) or die('Erreur SQL #016<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		$MinX = $data['MinX'];
		$MinY = $data['MinY'];
		$MaxX = $data['MaxX'];
		$MaxY = $data['MaxY'];
		
		for ( $y = 1; $y <= $MaxY ; $y++ )
		{
			echo "<tr>";
			for ( $x = 1; $x <= $MaxX ; $x++ )
			{
				echo '<td style="border: none;" width="40px">';
				// On test chaque région...
				$RegionNord 	= $y - 1;
				$RegionSud 		= $y + 1;
				$RegionEst 		= $x + 1;
				$RegionOuest 	= $x - 1;

				$RegionTerritoireEnCours 	= $Region['Territoire'][$x][$y];
				$RegionIDEnCours 			= $Region['ID'][$x][$y];
				
				if ( $Region['Terrain'][$x][$y] )
				{
					$Nord 	= ( $Region['Territoire'][$x][$RegionNord] != $RegionTerritoireEnCours 	|| !$Region['Territoire'][$x][$RegionNord] ) ? 1 : 0 ;
					$Sud 	= ( $Region['Territoire'][$x][$RegionSud] != $RegionTerritoireEnCours 	|| !$Region['Territoire'][$x][$RegionSud] ) ? 1 : 0 ;
					$Est 	= ( $Region['Territoire'][$RegionEst][$y] != $RegionTerritoireEnCours 	|| !$Region['Territoire'][$RegionEst][$y] ) ? 1 : 0 ;
					$Ouest 	= ( $Region['Territoire'][$RegionOuest][$y] != $RegionTerritoireEnCours 	|| !$Region['Territoire'][$RegionOuest][$y] ) ? 1 : 0 ;
	
					$RegionImage = $Nord . "-" . $Sud . "-" . $Est . "-" . $Ouest;
				}
				else
				{
					$RegionImage = "poisson";
				}
				$Couleur[$RegionTerritoireEnCours] = $Couleur[$RegionTerritoireEnCours] ? $Couleur[$RegionTerritoireEnCours] : "blanche";
				$RegionImage = $Couleur[$RegionTerritoireEnCours] . '/' . $RegionImage;
				$Fonction = ( $PartieStatut == -1 ) ? "Selectionner" : ( ( $PartieStatut == 0 ) ? "Placer" : "Cibler");
//				$Fonction = $Region['Terrain'][$x][$y] ? $Fonction : "Rien";
				if ( $PartieStatut == -1 )
				{
					echo '<a href="#' . $x . '-' . $y . '-'.$Fonction.'" onClick="'.$Fonction.'(' . $RegionIDEnCours . ');"><img src="./images/carte/'.$RegionImage.'.gif"></a>';
				}
				else
				{
					echo '<a href="#' . $RegionTerritoireEnCours . '" onClick="TerritoireInformations(' . $RegionTerritoireEnCours . ');"><img src="./images/carte/'.$RegionImage.'.gif"></a>';
				}

				echo "</td>";
			}
		echo "<tr>";
		}
	break;

	// Séparation d'un territoire constituté de plusieurs régions
	// Changement de terrain
	case "terrain":
	case "separer":
		$Selection 	= $_POST['Selection'];
		
		// On récupère le plus grand ID des territoires actuels
		$sql = "SELECT MAX(RegionTerritoire) AS MaxTerritoireID
			FROM Region";
		$req = mysql_query($sql) or die('Erreur SQL #028<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		$MaxTerritoireID = $data['MaxTerritoireID'];
		
		$sql = "SELECT MAX(TerritoireID) AS MaxTerritoireID
			FROM Territoire";
		$req = mysql_query($sql) or die('Erreur SQL #028<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		$MaxTerritoireID = ( $MaxTerritoireID > $data['MaxTerritoireID'] ) ? $MaxTerritoireID : $data['MaxTerritoireID'];
		
		// MaxTerritoireID permet de définir l'ID des nouveaux territoires
		$explode = explode(", ", $Selection);
		
		for ( $i = 1; $i <= count($explode); $i++ )
		{

			// Pour chaque région sélectionnée, on va fractionner son territoire
			
			// 1. On sélectionne le territoire de la région
			$RegionID = $explode[$i];
			
			$sql = "SELECT RegionTerritoire
				FROM Region
				WHERE RegionID = " . $RegionID;
			$req = mysql_query($sql) or die('Erreur SQL #027<br />'.$sql.'<br />'.mysql_error());
			$data = mysql_fetch_array($req);
			$RegionTerritoire = $data['RegionTerritoire'];
			
			// 2. On va sélectionner chacunes des régions composant le territoire
			$sql = "SELECT RegionID, RegionTerrain
				FROM Region
				WHERE RegionTerritoire = " . $RegionTerritoire;
			$req = mysql_query($sql) or die('Erreur SQL #024<br />'.$sql.'<br />'.mysql_error());
			while ($data = mysql_fetch_array($req) )
			{
				$RegionID 		= $data['RegionID'];
				$RegionTerrain 	= $data['RegionTerrain'];
				$MaxTerritoireID++;
				
				// Changement de terrain
				$RegionTerrainNouveau = ( $mode == "terrain" ) ? ( $RegionTerrain ? 0 : 1 ) : $RegionTerrain;

				// Pour chaque région concernée, on crée un territoire
				$sql = 'INSERT INTO Territoire (TerritoireID, TerritoireEtat, TerritoirePartie, TerritoireTerrain)
					VALUES(' . $MaxTerritoireID . ', 0, ' . $Partie . ', ' . $RegionTerrainNouveau . ')';
				mysql_query($sql) or die('Erreur SQL #023 Carte'.$sql.'<br />'.mysql_error());

				// Puis on met à jour la région
				$sql = "UPDATE Region
					SET RegionTerritoire = " . $MaxTerritoireID . ", RegionTerrain = " . $RegionTerrainNouveau . "
						WHERE RegionID = " . $RegionID;
				mysql_query($sql) or die('Erreur SQL #026<br />'.$sql.'<br />'.mysql_error());
			}
		}
	break;

	// Fusionner plusieurs régions en un seul territoire
	case "fusionner":
		$Selection 	= $_POST['Selection'];

		// Selection de l'ID du territoire d'une région (la plus petite), qui deviendra le territoire des autres régions
		$sql = "SELECT MIN(RegionTerritoire) AS MinRegionTerritoire
			FROM Region
				WHERE RegionID IN(".$Selection.")";
		$req = mysql_query($sql) or die('Erreur SQL #017<br />'.$sql.'<br />'.mysql_error());
		$data = mysql_fetch_array($req);
		$RegionsTerritoire = $data['MinRegionTerritoire'];

		// On selectionne les territoires qui doivent être détruit
		$TerritoiresDetruits = "0";
		$sql = "SELECT RegionTerritoire, RegionTerrain
			FROM Region
				WHERE RegionID IN(".$Selection.")
					AND RegionTerritoire != " . $RegionsTerritoire;
		$req = mysql_query($sql) or die('Erreur SQL #020<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req) )
		{
			$TerritoiresDetruits .= ", " . $data['RegionTerritoire'];
			if ( $data['RegionTerrain'] == 0 )
			{
				exit;
			}
		}
		
		// Destruction des anciens territoires
		$sql = "DELETE FROM Territoire
				WHERE TerritoireID IN(".$TerritoiresDetruits.")";
		mysql_query($sql) or die('Erreur SQL #019<br />'.$sql.'<br />'.mysql_error());	
		
		// Mise à jour du territoire des régions sélectionnées
		$sql = "UPDATE Region
			SET RegionTerritoire = " . $RegionsTerritoire . "
				WHERE RegionID IN(".$Selection.")";
		mysql_query($sql) or die('Erreur SQL #018<br />'.$sql.'<br />'.mysql_error());

	break;
	case "placer":
		$Territoire = $_POST['Territoire'];
		$Joueur 	= $_POST['Joueur'];
		$Etat 		= $_POST['Etat'];

		$capture = CaptureTerritoire($Partie, TRUE, $Joueur, $Etat, $Territoire, TRUE);
	break;
}
mysql_close();
	
echo $message;

?>