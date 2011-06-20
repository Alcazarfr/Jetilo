 <script src="./javascripts/prototype.js" type="text/javascript"></script>
 <script src="./javascripts/scriptaculous.js" type="text/javascript"></script>

<?php


include('includes/fonctions.php');

connectMaBase();

$Partie = ( isset($_GET['Partie']) ) ? $_GET['Partie'] : $_POST['Partie'];
$Joueur = ( isset($_GET['Joueur']) ) ? $_GET['Joueur'] : $_POST['Partie'];
$Mode 	= ( isset($_POST['Mode']) ) ? $_POST['Mode'] : 0;


?>

<html>
    <head><title>Jetilo</title></head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="style.css" />
<script language="javascript">AC_FL_RunContent = 0;</script>
<script language="javascript"> DetectFlashVer = 0; </script>
<script src="AC_RunActiveContent.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
var requiredMajorVersion = 10;
var requiredMinorVersion = 0;
var requiredRevision = 45;
-->
</script>
    <body bgcolor="FFFFFF">
   	<div id="logo">
		<h1><a href="#">JeTiLo - Le jeu sans Nom  </a></h1>
		<p><em> Statistiques</em></p>
	</div>
	<hr />
	<!-- end #logo -->
	<div id="header">
		<div id="menu">
			<ul>
				<li><a href="#mesRessources" class="first">Ressources</a></li>
				<li><a href="#mesConstructions">Construction</a></li>
				<li><a href="#mesMessages">Messages publics</a></li>
				<li><a href="#Classement">Classement</a></li>
			</ul>
		</div>
		<!-- end #menu -->
	</div>
<?php
	header('Content-Type: text/html; charset=utf-8');

	echo "<form action=\"statistiques.php\" method=\"POST\">
<input name=\"Partie\" id=\"Partie\" type=\"HIDDEN\" value=\"".$Partie."\">";
	echo "<input name=\"Joueur\" id=\"Joueur\" type=\"HIDDEN\" value=\"".$Joueur."\">";

	$sql = "SELECT PartieTour
		FROM partie
		WHERE PartieID = " . $Partie;
	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req))
	{
		$Tour = $data['PartieTour'] - 1;
	}  
	mysql_free_result($req);
	
	$StatTab	= Array();

	$nombreJoueur = 0;
	$NumeroJoueur = Array();
	$NomJoueur = Array();
	
	$sql = "SELECT JoueurID, JoueurPseudo
		FROM joueur
		WHERE JoueurPartie = " . $Partie;
	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req))
	{
		$nombreJoueur++;
		$JoueurID		= $data['JoueurID'];
		$JoueurNom		= $data['JoueurPseudo'];
		$NumeroJoueur[$JoueurID] = $nombreJoueur;
		$NomJoueur[$nombreJoueur] = $JoueurNom;
	}
	mysql_free_result($req);
	
	$largeur = round(800/$nombreJoueur);
	$sql = "SELECT *
		FROM statistiques
		WHERE StatPartie = " . $Partie . "
			ORDER BY StatTour DESC";
	$req = mysql_query($sql) or die('Erreur SQL !<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req))
	{
		$TourStat 						= $data['StatTour'];
		$StatJoueur 					= $data['StatJoueur'];
		$StatJoueurNumero				= $NumeroJoueur[$StatJoueur];
		
		$StatTab[$TourStat][$StatJoueurNumero]	= Array(
			"StatEconomie"				=> $data['StatEconomie'],
			"StatEconomieClassement"	=> $data['StatEconomieClassement'],
			"StatStabilite"				=> $data['StatStabilite'],
			"StatStabiliteClassement"	=> $data['StatStabiliteClassement'],
			"StatCommerce"				=> $data['StatCommerce'],
			"StatCommerceClassement"	=> $data['StatCommerceClassement'],
			"StatMilitaire"				=> $data['StatMilitaire'],
			"StatMilitaireClassement"	=> $data['StatMilitaireClassement'],
			"StatTotal"					=> $data['StatTotal'],
			"StatTotalClassement"		=> $data['StatTotalClassement'],

			"StatBleProduction"			=> $data['StatBleProduction'],
			"StatBoisProduction"		=> $data['StatBoisProduction'],
			"StatPierreProduction"		=> $data['StatPierreProduction'],
			"StatFerProduction"			=> $data['StatFerProduction'],
			"StatOrClassement"			=> $data['StatOrClassement'],
			"StatBleClassement"			=> $data['StatBleClassement'],
			"StatBoisClassement"		=> $data['StatBoisClassement'],
			"StatPierreClassement"		=> $data['StatPierreClassement'],
			"StatFerClassement"			=> $data['StatFerClassement'],
			"StatProduction"			=> $data['StatProduction'],
			"StatProductionPonderee"	=> $data['StatProductionPonderee'],
			"StatProductionClassement"	=> $data['StatProductionClassement'],
			"StatDevEco"				=> $data['StatDevEco'],
			"StatDevEcoClassement"		=> $data['StatDevEcoClassement'],
			"StatDevCulture"			=> $data['StatDevCulture'],
			"StatDevCultureClassement"	=> $data['StatDevCultureClassement'],
			"StatDevMilitaire"			=> $data['StatDevMilitaire'],
			"StatDevMilitaireClassement"	=> $data['StatDevMilitaireClassement'],
			"StatVille"					=> $data['StatVille'],
			"StatVilleClassement"		=> $data['StatVilleClassement']
		);
	}
	mysql_free_result($req);
 	mysql_close();
	if ( !$TourStat )
	{
		echo "Erreur No data";
	}
	
	// Generation des tableaux
	
	$TourCourtTerme = ( isset($_POST['TourCourtTerme']) ) ? $_POST['TourCourtTerme'] : $Tour ;
	$TourMoyenTerme = ( isset($_POST['TourMoyenTerme']) ) ? $_POST['TourMoyenTerme'] : $Tour - 1;
	$TourLongTerme = ( isset($_POST['TourLongTerme']) ) ? $_POST['TourLongTerme'] : $Tour - 3;
		
 	
?>
Long Terme <select name="TourLongTerme">
<?php
	for ( $i = 1; $i <= $Tour - 2; $i++ )
	{
		$verrouiller	= ( $i == $TourLongTerme ) ? "selected=\"selected\"" : "";
		echo '<option value="'.$i.'" ' . $verrouiller . '>'.$i.'</option>';     
	}
?>
</select> 
Moyen Terme <select name="TourMoyenTerme">
<?php
	for ( $i = 2; $i <= $Tour - 1; $i++ )
	{
		$verrouiller	= ( $i == $TourMoyenTerme ) ? "selected=\"selected\"" : "";
		echo '<option value="'.$i.'" ' . $verrouiller . '>'.$i.'</option>';     
	}
?>
</select>
Court Terme <select name="TourCourtTerme">
<?php
	for ( $i = 3; $i <= $Tour; $i++ )
	{
		$verrouiller	= ( $i == $TourCourtTerme ) ? "selected=\"selected\"" : "";
		echo '<option value="'.$i.'" ' . $verrouiller . '>'.$i.'</option>';     
	}
?>
</select>
<input type="submit" name="submit" value="Charger"/><br /><br />

<br /><br />
	Economie
	<table width="900">
		<tr>
			<td width="100">Economie</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center' colspan='3'>";
				printf($NomJoueur[$i]);
				echo "</td>";
			}
			?>
			</td>
		</tr>
		<tr>
			<td width="100" rowspan="2">Blé</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatBleClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatBleClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatBleClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatBleProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatBleProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatBleProduction']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Bois</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatBoisClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatBoisClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatBoisClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatPierreProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatPierreProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatPierreProduction']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Pierre</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatPierreClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatPierreClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatPierreClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatFerProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatFerProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatFerProduction']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Fer</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatFerClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatFerClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatFerClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatBleProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatBleProduction']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatBleProduction']);
				echo "</td>";
			}
			?>
		</tr>
	</table>
<br /><br />
	Total
	<table width="900">
		<tr>
			<td width="100">Total</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center' colspan='3'>";
				printf($NomJoueur[$i]);
				echo "</td>";
			}
			?>
			</td>
		</tr>
		<tr>
			<td width="100" rowspan="2">Economie</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatEconomieClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatEconomieClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatEconomieClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatEconomie']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatEconomie']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatEconomie']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Militaire</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatMilitaireClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatMilitaireClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatMilitaireClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatMilitaire']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatMilitaire']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatMilitaire']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Commerce</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatCommerceClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatCommerceClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatCommerceClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatCommerce']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatCommerce']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatCommerce']);
				echo "</td>";
			}
			?>
		</tr>
		<tr>
			<td width="100" rowspan="2">Stabilite</td>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatStabiliteClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatStabiliteClassement']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'><b>";
				printf($StatTab[$TourCourtTerme][$i]['StatStabiliteClassement']);
				echo "</b></td>";
			}
			?>
		</tr>
		<tr>
			<?php
			for ( $i = 1 ; $i <= $nombreJoueur ; $i++ )
			{
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourLongTerme][$i]['StatStabilite']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourMoyenTerme][$i]['StatStabilite']);
				echo "</td>";
				echo "<td width='".$largeur."' align='center'>";
				printf($StatTab[$TourCourtTerme][$i]['StatStabilite']);
				echo "</td>";
			}
			?>
		</tr>
	</table>
	<br /><br />
<?php
 	
	$TourMax 	= ( isset($_POST['TourMax']) ) ? $_POST['TourMax'] : $Tour;
	$TourMinPotentiel	= $Tour - 5;
	$TourMinPotentiel	=	( $TourMinPotentiel <= 0 ) ? 1 : $TourMinPotentiel;
 	$TourMin 	= ( isset($_POST['TourMin']) ) ? $_POST['TourMin'] : $TourMinPotentiel;

 ?>
Minimum <select name="TourMin">
<?php
	for ( $i = 1; $i < $Tour; $i++ )
	{
		$verrouiller	= ( $i == $TourMin ) ? "selected=\"selected\"" : "";
		echo '<option value="'.$i.'" ' . $verrouiller . '>'.$i.'</option>';     
	}
?>
</select> 
 Maximum <select name="TourMax">
<?php
	for ( $i = 2; $i <= $Tour; $i++ )
	{
		$verrouiller	= ( $i == $TourMax ) ? "selected=\"selected\"" : "";
		echo '<option value="'.$i.'" ' . $verrouiller . '>'.$i.'</option>';     
	}
	
	$Infos = $Partie . "-" . $Joueur . "-" . $TourMin . "-" . $TourMax;
?>
</select>
<input type="submit" name="submit" value="Charger"/><br /><br />
</form>

<table width="1200">
<tr>
<td width="50%">
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Or-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<br /><br />
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Ble-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<br /><br />
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Pierre-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
</td>
<td width="50%">
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Or-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<br /><br />
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Bois-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<br /><br />
<script language="JavaScript" type="text/javascript">
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert("This page requires AC_RunActiveContent.js.");
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=10,0,45,2',
			'width', '600',
			'height', '400',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#777788',
			'wmode', 'opaque',
			'movie', 'charts',
			'src', 'charts',
			'FlashVars', 'library_path=charts_library&xml_source=stats.php?Infos=Fer-<?php echo $Infos ?>', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'https://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=https://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
</td>
</tr>
</table>
<noscript>
	<P>This content requires JavaScript.</P>
</noscript>

    </body>
</html>

<?php
mysql_close();

?>
