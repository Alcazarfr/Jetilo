<?php

	// On charge les informations sur la partie, le joueur et son Etat
	$Recherche 		= Attribut($Partie, "Partie", Array("PartieNom", "PartieStatut"));
	$PartieNom 		= $Recherche['PartieNom'];
	$PartieStatut 	= $Recherche['PartieStatut'];

	$Recherche 		= Attribut($Etat, "Etat", Array("EtatNom", "EtatCouleur"));
	$EtatNom 		= $Recherche['EtatNom'];
	$EtatCouleur 	= $Recherche['EtatCouleur'];
	
	$JoueurNom 		= Attribut($Joueur, "Joueur", "JoueurNom");

?>

<script type="text/javascript">

/* In place editing des infos */
 $(document).ready(function() {
    $('.edit').editable('./includes/ajax/administration.php?mode=modifierChamp'), { 
         submit    : 'OK'
     }
 });

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
		setTimeout("CarteChargement(true)",12000);
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
		data: "mode=territoireInformations&Partie="+PartieID+"&Territoire="+TerritoireID+"&Joueur="+Joueur+"&SousMode=Placement",
		success:
    		function(retour){
			$("#leterritoire").empty().append(retour);
			}
	}
	);
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
	if ( Boucle == true )
	{
		setTimeout("MessageLire(true)",12000);
	}
}

/* Capture d'un territoire */
function Placer(Territoire)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();
	var JoueurID 	= $('#Joueur').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/carte.php",
		data: "mode=placer&Partie="+PartieID+"&Territoire="+Territoire+"&Joueur="+JoueurID+"&Etat="+EtatID
	}
	);
	setTimeout("MessageLire(false)",200);
	setTimeout("CarteChargement(false)",500);
}

/* Chargement des fonctions automatiques au chargement de la page */
$(window).load(function(){
	CarteChargement(true);
	MessageLire(true);
});

</script>
<h2 class="title">Préparation de la Partie</h2><br />
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
						echo $JoueurNom;
						$Image = "./images/carte/" . $EtatCouleur . "/1-1-1-1.gif";
						echo "&nbsp;&nbsp;<img src=\"" . $Image . "\" width=\"20px\">";
					?>
				</td>
			</tr>
		</table>
</div>
<div class="postgrand">

	<form name="creer" method="post" action="index.php">
		<div class="entry">Changer le nom de votre État : 
			<?php
				echo "<div class=\"edit\" id=\"EtatNom-".$Etat."\">".$EtatNom."</div>";
			?>
		</div>
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
			<p>Mises à jour manuelles : <br />- <a href="#" onClick="CarteChargement(false);">Recharger la carte</a><br />- <a href="javascript:void(0);" onclick="MessageLire(true);">Lire les messages</a></p>
		</div>

	</form>
</div>