<?php

	// On charge les informations sur la partie, le joueur et son Etat
	$Recherche 		= Attribut($Partie, "Partie", Array("PartieNom", "PartieStatut"));
	$PartieNom 		= $Recherche['PartieNom'];
	$PartieStatut 	= $Recherche['PartieStatut'];

	$Recherche 		= Attribut($Etat, "Etat", Array("EtatNom", "EtatCouleur", "EtatPopulation", "EtatTerritoires", "EtatCroissance"));
	$EtatNom 		= $Recherche['EtatNom'];
	$EtatCouleur 	= $Recherche['EtatCouleur'];
	$EtatPopulation = $Recherche['EtatPopulation'];
	$EtatTerritoires= $Recherche['EtatTerritoires'];

	$JoueurNom 		= Attribut($Joueur, "Joueur", "JoueurNom");

?>


<script type="text/javascript">

/* Chargement de la carte */
function CarteChargement(Boucle)
{
	var PartieID = $('#Partie').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/carte.php",
		data: "mode=afficher&Partie="+PartieID,
		success:
    		function(retour){
			$("#lacarte").empty().append(retour);
			}
	}
	);
	if ( Boucle == true )
	{
		setTimeout("CarteChargement(true)",50000);
	}
}

/* Affichage des infos sur un territoire */
function TerritoireInformations(TerritoireID)
{
	var PartieID 		= $('#Partie').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=territoireInformations&Partie="+PartieID+"&Territoire="+TerritoireID+"&Joueur="+Joueur+"&SousMode=Jeu",
		success:
    		function(retour){
			$("#leterritoire").empty().append(retour);
			}
	}
	);
}

/* Production */
function Production(Boucle)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();

	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=Production&Partie="+PartieID+"&Etat="+EtatID,
		success:
    		function(retour){
			$("#Population").empty().append(retour);
			}
	}
	);
	setTimeout("MessageLire(false)",500);
	if ( Boucle == true )
	{
		setTimeout("Production(true)",60000);
	}
}

/* Lecture et affichage des messages, en haut à droite */
function MessageLire(Boucle)
{
	var PartieID = $('#Partie').val();
	var JoueurID = $('#Joueur').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=messageLire&Partie="+PartieID+"&Joueur="+JoueurID,
		dataType: "script",
		success:
    		function(retour)
    		{
    			retour;
			}
	}
	);
	setTimeout("Journal()",500);
	if ( Boucle == true )
	{
		setTimeout("MessageLire(true)",12000);
	}
}

/* Affichage des infos sur un territoire */
function Population(Boucle)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=AfficherPopulation&Partie="+PartieID+"&Etat="+EtatID,
		success:
    		function(retour){
			$("#Population").empty().append(retour);
			}
	}
	);
	if ( Boucle == true )
	{
		setTimeout("Population(true)",25000);
	}
}

/* Affichage du journal des messages */
function Journal()
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var TimeMin		= $('#JournalTimeMin').val();
	var TimeMax 	= $('#JournalTimeMax').val();
	var Source	 	= $('#JournalSource').val();

	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=Journal&Partie="+PartieID+"&Joueur="+JoueurID+"&TimeMax=0&TimeMin=0&Source=0",
		success:
    		function(retour){
			$("#Journal").empty().append(retour);
			}
	}
	);
}

/* Chargement des fonctions automatiques au chargement de la page */
$(window).load(function(){
	Production(true);
	CarteChargement(true);
	MessageLire(true);
});



</script>
<div class="postgrand">
	<table width="100%" style="border: none;" cellpadding="5">
		<tr>
			<td width="50%" align="left" style="border: none;">
				<?php
					echo $PartieNom;
				?>				
			</td>
			<td width="50%" align="right" style="border: none;">
				<?php
					echo $JoueurNom . " - " . $EtatNom;
					$Image = "./images/carte/" . $EtatCouleur . "/1-1-1-1.gif";
					echo "&nbsp;&nbsp;<img src=\"" . $Image . "\" width=\"20px\">";
				?>
			</td>
		</tr>
	</table>
</div>
<div class="postgrand">
	<div class="entry">
		<table style="border: none;" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="650" align="left" valign="top" style="border: none;">
					<table style="border: none;" border="0" cellspacing="0" cellpadding="10">
						<div id="lacarte">Chargement de la carte en cours...</div>
					</table><br />
				</td>
				<td width="250" align="left" valign="top" style="border: none;">
					<div id="leterritoire">Cliquez sur un territoire...</div>						
				</td>
			</tr>
		</table>
		Mises à jour manuelles : <a href="#" onClick="CarteChargement(false);">Carte</a> • <a href="javascript:void(0);" onclick="MessageLire(false);">Messages</a> • <a href="javascript:void(0);" onclick="Population(false);">Population</a> • <a href="javascript:void(0);" onclick="Production(false);">Production</a>
	</div>
</div>
<div class="postgrand">
	<div class="entry">
		<div id="Population"></div>
	</div>
</div>
<div class="postgrand">
	<div class="entry"><h2>Journal des messages</h2>
		<div id="Journal"></div>
	</div>
</div>