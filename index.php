<?php

define('ROOT_PATH', '');
/* Inclusion des fichiers de configuration et de fonctions */
include ROOT_PATH.'config.php';
include ROOT_PATH.'includes/init.php';

/* Connexion à la Base de données */
connectMaBase();

/* Par défaut, la page est en mode "créer un joueur" */
$mode 	= "CreerUnJoueur";

/* Si elle existe, on récupère les ID de la Partie, du Joueur et de son Etat */
$Partie = ( isset($_POST['Partie']) ) ? $_POST['Partie'] : 0;
$Joueur = ( isset($_POST['Joueur']) ) ? $_POST['Joueur'] : 0;
$Etat 	= ( isset($_POST['Etat']) ) ? $_POST['Etat'] : 0;

/* Prepartie gère les POST envoyés lors de la création de la partie
   (la phase de prépartie)  */
include 'includes/prepartie.php';

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
	<script type="text/javascript" src="./javascripts/jquery.js"></script>
	<script type="text/javascript" src="./javascripts/jquery.jgrowl.js"></script>
	<script type="text/javascript" src="./javascripts/jquery.jeditable.js"></script>
	<script type="text/javascript" src="./javascripts/jquery.countdown.js"></script>
	<script type="text/javascript" src="./javascripts/jquery.countdown-fr.js"></script>
	<script type="text/javascript" src="./javascripts/jquery.qtip.js"></script>

	<title>Jetilo - Version 6</title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link href="./css/jquery.countdown.css" rel="Stylesheet" type="text/css" media="screen" />	
	<link href="./css/style.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="./css/jquery.jgrowl.css" rel="stylesheet" type="text/css" media="screen" />
	<link href="./css/jquery.qtip.css" rel="stylesheet" type="text/css" media="screen" />
	
<?php
	if ($__DEBUG_CONFIG_RECHARGEE)
	{
		print ("<script type=\"text/javascript\">$(window).load(function(){ $.jGrowl('Configuration rechargée', { life: 5000, header : 'Debug' }); });</script>");
	}
?>
	
</head>
<body>
	<div id="logo">
		<h1><a href="#">JeTiLo</a></h1>
	</div>
	<hr />

	<div id="page">
		<div id="content">

		<?php

			// Affichage des 3 champs cachés contenant l'ID de la partie, du joueur et son l'Etat
			if ( $mode != "partie" )
			{
				echo '<form name="creer" method="post" action="index.php">';
			}
			echo "<input name=\"Partie\" id=\"Partie\" type=\"HIDDEN\" value=\"".$Partie."\">";
			echo "<input name=\"Joueur\" id=\"Joueur\" type=\"HIDDEN\" value=\"".$Joueur."\">";
			echo "<input name=\"Etat\" id=\"Etat\" type=\"HIDDEN\" value=\"".$Etat."\">";

	
			// Quelle page faut il afficher?
	
			switch ( $mode )
    		{
    			// Si cette page est accédé directement par le joueur,
    			// il peut créer un joueur ou rejoindre une partie
    			case "CreerUnJoueur":
					include("includes/creerjoueur.php");

				// Si le joueur vient de créer un joueur (une Etat)
				// il peut rejoindre une partie avec ses identifiants
 				case "RejoindreUnePartie":
					include("includes/rejoindre.php");
			
				// Créer une partie si le joueur est connecté
					include("includes/creerpartie.php");
		
 				break;

				// Création de la carte
    			case "Carte":
					include("includes/creercarte.php");
 				break;

    			case "Preparation":
					include("includes/preparation.php");
					if ( $JoueurAdmin )
					{
						include("includes/administration.php");
					}
 				break;
 		
 				// Si l'input "Partie" est renseigné, on affiche la partie
 				case "Partie":
					include("includes/partie.php");
			
					// Si le joueur est administrateur
					// $JoueurAdmin est renseigné dans partie.php
					if ( $JoueurAdmin )
					{
						include("includes/administration.php");
					}
 				break;
 		
 				default:
 					include("includes/creerjoueur.php");
					include("includes/rejoindre.php");
 				break;
			}
			mysql_close();
		?>

		</div><!-- end #content -->
		<div style="clear: both;">&nbsp;</div>
	</div>
	<div id="footer">
		<p>Copyright (c) 2010 JeTiLo Corp. All rights reserved. Design by <a href="http://www.freecsstemplates.org/">Free CSS Templates</a>.</p>
	</div>
    </body>
</html>
