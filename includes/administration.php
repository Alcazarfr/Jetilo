<script type="text/javascript">

/* In place editing des infos */
 $(document).ready(function() {
     $('.edit').editable('./includes/ajax/administration.php?mode=modifierChamp');
 });


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

</script>

<h2 class="title">Administration

</h2><br />
<div class="postgrand">
		<div class="entry">
			<div id="listeJoueurs"></div><br />

			<div id="listeTerritoires"></div><br />
			
			<p>Mises à jour manuelles : <br />
			- <a class="pointille" href="#ListeDesJoueurs" onClick="ListeJoueurs();">Recharger la liste des joueurs</a> ;<br />
			- <a href="#ListeDesTerritoires" onClick="ListeTerritoires();">Recharger la liste des territoires</a>.<br />
			</p><br /><br />
			Statut de la partie : <div class="edit" id="<?php echo $Partie; ?>-PartieStatut-<?php echo $Partie; ?>"><?php echo Attribut($Partie, "Partie", "PartieStatut"); ?></div><br />
		</div>		
</div>