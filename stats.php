<?php

$base = mysql_connect('localhost', 'root', 'root');  
mysql_select_db('jetilo', $base);
    
$Partie 	= 1;
$Joueur 	= 3;

$mode 		= "derniers-tours";
$Infos 		= $_GET['Infos'] ? $_GET['Infos'] : 0;
// $Infos		= "Or-Partie-Joueur-TourMin-TourMax";
$explode	= explode("-", $Infos);

$Mode 		= $explode[0];
$Partie 	= is_numeric($explode[1]) ? $explode[1] : 1;
$Joueur 	= is_numeric($explode[2]) ? $explode[2] : 1;
$TourMin 	= is_numeric($explode[3]) ? $explode[3] : 1;
$TourMax 	= is_numeric($explode[4]) ? $explode[4] : 3;


$TourLess	= $TourMax - $TourMin;
$Ressource	= "Stat" . $Mode . "Production";
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
		
		$min = 400;
		$max = 0;
		
		$sql = "SELECT *
			FROM statistiques
			WHERE StatPartie = " . $Partie . "
				AND StatTour <= " . $TourMax . "
				AND StatTour >= " . $TourMin . "
				ORDER BY StatJoueur
					AND StatTour DESC";
		$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
		while ($data = mysql_fetch_array($req))
		{
			$JoueurID 	= $data['StatJoueur'];
			$hTour 		= $data['StatTour'];
			$valeur		= $data[$Ressource];
			$table[$JoueurID][$hTour] = $valeur;
			$max = ( $valeur > $max ) ? $valeur : $max;
			$min = ( $valeur < $min ) ? $valeur : $min;
		}  
		mysql_free_result($req);
		$min -= 1;
		$max += 1;
echo "<chart>

	<axis_category size='16' alpha='85' shadow='medium' />
	<axis_ticks value_ticks='false' category_ticks='true' major_thickness='2' minor_thickness='1' minor_count='1' minor_color='222222' position='inside' />";
	
echo"	<axis_value shadow='medium' min='" . $min . "' max='" . $max . "' size='10' color='ffffff' alpha='65' steps='6' show_min='false' />

<chart_data>
      <row>
         <null/>";
for ( $i = $TourMin; $i <= $TourMax; $i++ )
{
	$var = "<string># " . $i . "</string>";
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
		if ( $IDDuJoueur != $Joueur )
		{
			$variable = "<number shadow='low' tooltip='".$table[$IDDuJoueur][$x]."'>" . $table[$IDDuJoueur][$x] . "</number>";
		}
		else
		{
			$variable = "<number shadow='medium' tooltip='".$table[$IDDuJoueur][$x]."'>" . $table[$IDDuJoueur][$x] . "</number>";
		}
		echo $variable;
	}
echo "</row>";
}
echo "

   </chart_data>
  	<chart_grid_h alpha='10' thickness='1' />
	<chart_guide horizontal='true' vertical='true' thickness='1' alpha='25' type='dashed' text_h_alpha='0' text_v_alpha='0' />
	<chart_note type='flag' size='11' color='000000' alpha='70' x='-10' y='-36' background_color_1='ffffaa' background_color_2='ccff00' background_alpha='90' shadow='medium' bevel='note' />
	<chart_pref line_thickness='2' point_shape='circle' point_size='7' fill_shape='false' />
	<chart_rect x='30' y='60' width='550' height='300' positive_color='ffffee' positive_alpha='65' negative_color='ff8888' negative_alpha='65' bevel='bg' shadow='high' />
	<chart_transition type='slide_left' delay='.5' duration='1' order='series' />
	<chart_type>Line</chart_type>
	
	<draw>
		<rect layer='background' x='0' y='0' width='800' height='400' fill_color='ff7733' />
		<text shadow='high' transition='dissolve' delay='0' duration='0.5' alpha='50' size='28' x='35' y='4' width='500' height='30' h_align='center' v_align='bottom'>- ".$Mode." -</text>
	</draw>
	<filter>
		<shadow id='low' distance='2' angle='45' alpha='20' blurX='5' blurY='5' />
		<shadow id='medium' distance='2' angle='45' alpha='40' blurX='7' blurY='7' />
		<shadow id='high' distance='5' angle='45' alpha='25' blurX='10' blurY='10' />
		<bevel id='bg' angle='45' blurX='15' blurY='15' distance='5' highlightAlpha='25' shadowAlpha='50' type='outer' />
		<bevel id='note' angle='45' blurX='10' blurY='10' distance='3' highlightAlpha='60' shadowAlpha='15' />
	</filter>
   
	<legend shadow='low' transition='dissolve' delay='0' duration='0.5' x='30' y='35' width='550' height='5' layout='horizontal' margin='5' bullet='line' size='10' color='ffffff' alpha='75' fill_color='000000' fill_alpha='10' line_color='000000' line_alpha='0' line_thickness='0' />
	
	<tooltip color='FFFFFF' alpha='90' background_color_1='8888FF' background_alpha='90' shadow='medium' />
    
    <context_menu save_as_bmp='true' save_as_jpeg='true' save_as_png='true' /> 
    
	<series_color>
		<color>ff4422</color>
		<color>ffee00</color>
		<color>8844ff</color>
	</series_color>
	<series_explode>
		<number>400</number>
	</series_explode>
   </chart>
";
	break;
   
   }
  mysql_close();
 
?>

