<script type="text/javascript">

/* In place editing des infos */
 $(document).ready(function() {
     $('.edit').editable('./includes/ajax/administration.php', {
         submit    : 'OK'
     });
 });
function Vider()
{
	$('#selection').val("0");
}
function Selectionner(ID)
{	
	var ValeurActuelle = $('#selection').val();
	var ValeurNouvelle = ValeurActuelle + ", " + ID;
	$('#selection').val(ValeurNouvelle);
}
function Chargement()
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
}
function ListeTerritoires()
{
	var PartieID = $('#Partie').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/administration.php",
		data: "mode=listeTerritoires&Partie="+PartieID,
		processData: false,
		success:
    		function(retour){
			$("#listeTerritoires").empty().append(retour);
			}

	}
	);
}
function ListeJoueurs()
{
	var PartieID = $('#Partie').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/administration.php",
		data: "mode=listeJoueurs&Partie="+PartieID,
		processData: false,
		success:
    		function(retour){
			$("#listeJoueurs").empty().append(retour);
			}

	}
	);
	ListeDeroulanteJoueurs();
}
function ListeDeroulanteJoueurs()
{
	var PartieID = $('#Partie').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/administration.php",
		data: "mode=listeDeroulanteJoueur&Partie="+PartieID,
		success:
    		function(retour){
			$("#listederoulantejoueur").empty().append(retour);
			}
	}
	);
}
function Changement(Statut)
{
	var PartieID = $('#Partie').val();
	var Regions = $('#selection').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/carte.php",
		data: "mode="+Statut+"&Partie="+PartieID+"&Selection="+Regions    		
	}
	);
	setTimeout("Chargement()",500);
	Vider();
}

/* Chargement des fonctions automatiques au chargement de la page */
$(window).load(function(){
	Chargement();
});
</script>

<h2 class="title">Créer la carte de la Partie
	<?php
		$PartieNom = Attribut($Partie, "Partie", "PartieNom");
		echo $PartieNom;
	?>
</h2><br />
<div class="postgrand">
	<form name="creer" method="post" action="index.php">
		<div class="entry">
			<table style="border: none;" border="0" cellspacing="0" cellpadding="10">
				<div id="lacarte">Recharger la carte</div>
				<br />Selection : <input type="text" name="selection" id="selection" size="40" value="0" readonly><br /><br />
				- <a href="#Fusionner" onClick="Changement('fusionner');">Fusionner la sélection</a> : la sélection est unifiée en un unique territoire. Les régions maritimes ne peuvent être fusionnées.<br /><br />
				- <a href="#Separer" onClick="Changement('separer');">Séparer la sélection</a> : les territoires, dont une région a été selectionné, sont divisés en autant de régions possibles.<br /><br />
				- <a href="#Inverser" onClick="Changement('terrain');">Inverser les terrains</a> : les terrains des territoires selectionnés sont inversés (mer-&gt;terre; terre-&gt;mer). Les territoires sont auparavant fragmentées / divisés.<br /><br />
				- <a href="#Vider" onClick="Vider();">Vider la sélection</a> : la sélection est annulée.
			</table><br />

			<div id="listeJoueurs"></div><br />

			<div id="listeTerritoires"></div><br />
			
			<p>Mises à jour manuelles : <br />
			- <a href="#Recharger" onClick="Chargement();">Recharger la carte</a> ;<br />
			- <a href="#ListeDesJoueurs" onClick="ListeJoueurs();">Recharger la liste des joueurs</a> ;<br />
			- <a href="#ListeDesTerritoires" onClick="ListeTerritoires();">Recharger la liste des territoires</a>.<br />
			</p><br /><br />
			<table style="border: none;" border="0">
				<tr>
					<td colspan="2" align="center" style="border: none;" border="0">COULEUR DES JOUEURS ?<input type="submit" name="OuvrirPartie" value="Ouvrir la partie et permettre au joueur de se placer!"/></td>
				</tr>
			</table>
		</div>		
	</form>
</div>