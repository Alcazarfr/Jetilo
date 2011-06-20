<?php

    $base = mysql_connect('localhost', 'root', 'root');  
    mysql_select_db ('jetilo', $base) ;
    
$Partie 	= 1;
$mode 		= "derniers-tours";
$TourMax 	= 8;
$TourMin 	= 5;
$TourLess	= $TourMax - $TourMin;
$Ressource	= "hRevenuOr";
$NombreJoueur = 5;

function joueur($Joueur, $Attribut)
{
    $base = mysql_connect('localhost', 'root', 'root');  
    mysql_select_db ('jetilo', $base) ;
    	
	$sql = "SELECT " . $Attribut . " AS valeur
		FROM joueur
		WHERE JoueurID = " . $Joueur;
	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	if ($data = mysql_fetch_array($req))
	{
		$resultat = $data['valeur'];
	}
	else
	{
		$resultat = FALSE;
	}
	mysql_free_result($req);
	mysql_close();
	return $resultat;
}
switch ( $mode ) 
{
	case "derniers-tours":
		// La production d'une ressource, évolution avec les derniers tours

		$table = Array();
		
		$NombreJoueur 	= 0;
		$TableJoueur	= Array();
		
		$sql = "SELECT *
			FROM joueur
			WHERE JoueurPartie = " . $Partie . "
				ORDER BY JoueurID ASC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$NombreJoueur++;
			$TableJoueur[$NombreJoueur] = $data['JoueurID'];
		}  
		mysql_free_result($req); 
		
		$sql = "SELECT *
			FROM historique
			WHERE hPartie = " . $Partie . "
				AND hTour <= " . $TourMax . "
				AND hTour >= " . $TourMin . "
				ORDER BY hJoueurID
					AND hTour DESC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$JoueurID 	= $data['hJoueurID'];
			$hTour 		= $data['hTour'];
			
			$table[$JoueurID][$hTour] = $data[$Ressource];
		}  
		mysql_free_result($req); 
echo "<chart>
<chart_data>
      <row>
         <null/>";
for ( $i = $TourMin; $i <= $TourMax; $i++ )
{
	$var = "<string>Tour " . $i . "</string>";
	echo $var;
}
     echo  "</row>";

for ( $i = 1; $i <= $NombreJoueur; $i++ )
{
	echo "<row>";
	$IDDuJoueur = $TableJoueur[$i];
	$Nom = joueur($IDDuJoueur, "JoueurPseudo");
	echo "<string>" . $Nom . "</string>";

	for ( $x = $TourMin; $x <= $TourMax; $x++ ) 
	{
		$variable = "<number>" . $table[$IDDuJoueur][$x] . "</number>";
		echo $variable;
	}
echo "</row>";
}
echo "

   </chart_data></chart>
";
	break;
   
   }
  mysql_close();
 
?>

