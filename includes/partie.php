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

/* Affichage des infos sur un Etat */
function EtatInformations(EtatID)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=EtatInformations&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID,
		success:
    		function(retour){
			$("#leterritoire").empty().append(retour);
			}
	}
	);
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

/* Affichage des infos sur un territoire */
function Modal()
{	
   $('a[rel="modal"]:first').qtip(
   {
      id: 'modal', // Since we're only creating one modal, give it an ID so we can style it
      content: {
         text: $('div:hidden'),
         title: {
            text: 'Modal qTip',
            button: true
         }
      },
      position: {
         my: 'center', // ...at the center of the viewport
         at: 'center',
         target: $(window)
      },
      show: {
         event: 'click', // Show it on click...
         solo: true, // ...and hide all other tooltips...
         modal: true // ...and make it modal
      },
      hide: false,
      style: 'ui-tooltip-light ui-tooltip-rounded'
   });
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

/* Création d'un agent */
function AgentCreer(Bidon, Nom, Statut, Secret, Territoire, CapaciteFurtivite, CapaciteVitesse, CapaciteReussite, Type)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID 		= $('#Etat').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=AgentCreer&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID+"&Nom="+Nom+"&Statut="+Statut+"&Secret="+Secret+"&Territoire="+Territoire+"&CapaciteFurtivite="+CapaciteFurtivite+"&CapaciteVitesse="+CapaciteVitesse+"&CapaciteReussite="+CapaciteReussite+"&Type="+Type
	}
	);
	setTimeout("CarteChargement(false)",200);
	setTimeout("Population(false)",200);
	setTimeout("MessageLire(false)",200);

}

/* Création d'un agent */
function ActionCreer(ActionID, SourceID, CibleID)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID 		= $('#Etat').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=ActionCreer&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID+"&ActionID="+ActionID+"&SourceID="+SourceID+"&CibleID="+CibleID
	}
	);
	setTimeout("CarteChargement(false)",200);
	setTimeout("Population(false)",200);
	setTimeout("MessageLire(false)",200);

}

/* Création d'un effet */
function EffetCreer(Bidon, CibleType, CibleID, SourceType, SourceID, Nom, TimeDebut, TimeFin, Table, Variable, Type, Valeur, Cout)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID 		= $('#Etat').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=EffetCreer&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID+"&CibleType="+CibleType+"&CibleID="+CibleID+"&SourceType="+SourceType+"&SourceID="+SourceID+"&Nom="+Nom+"&TimeDebut="+TimeDebut+"&TimeFin="+TimeFin+"&Table="+Table+"&Variable="+Variable+"&Type="+Type+"&Valeur="+Valeur+"&Cout="+Cout
	}
	);
	setTimeout("CarteChargement(false)",200);
	setTimeout("Population(false)",200);
	setTimeout("MessageLire(false)",200);

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

/* Modal dans les retours Ajax */

$('.modal[id^="modal_"]').live('mouseover', function(event) {
    $(this).qtip(
   {
      id: 'modaltooltip', // Since we're only creating one modal, give it an ID so we can style it
      content: {
         text: $('#data_' + $(this).attr('id')),
         title: {
            text: $('#titre_' + $(this).attr('id')),
            button: true
         }
      },
      position: {
         my: 'center', // ...at the center of the viewport
         at: 'center',
         target: $(window)
      },
      show: {
         event: 'click', // Show it on click...
         solo: true, // ...and hide all other tooltips...
         modal: true // ...and make it modal
      },
      hide: false,
      style: 'ui-tooltip-light ui-tooltip-rounded'
   }, event);
});


/* InfosBulle dans les retours Ajax */

$('a[title]').live('mouseover', function(event) {
   $(this).qtip({
      overwrite: false,
      show: {
         event: event.type, 
         ready: true 
      }
   }, event);
})
 
.each(function(i) {
   $.attr(this, 'oldtitle', $.attr(this, 'title'));
   this.removeAttribute('title');
});


/* Chargement des fonctions automatiques au chargement de la page */
$(window).load(function(){
	Production(true);
	EtatInformations(<?php echo $Etat ?>);
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
					echo $JoueurNom . " - <a href='#Etat' onClick='EtatInformations(".$Etat.")'>" . $EtatNom . "</a>";
					$Image = "./images/carte/" . $EtatCouleur . "/1-1-1-1.gif";
					echo "&nbsp;&nbsp;<img src=\"" . $Image . "\" width=\"20px\">";
				?>
			</td>
		</tr>
	</table>
</div>


<div id="demo-modal">
    <a id="modal_2" href="#" class="modal">Ouvrir la modal 2</a>
    <a id="modal_test" href="#" class="modal">Ouvrir la modal test</a>
    <a id="modal_5" href="#" class="modal">Ouvrir la modal 5</a>

   <div style="display: none;">
    <div id="titre_modal_1">Titre1</div>
    <div id="data_modal_1">Texte1</div>
    <div id="titre_modal_2">Titre2</div>
    <div id="data_modal_2">TT2</div>
    <div id="data_modal_3">TT3</div>
    <div id="titre_modal_test">tesst</div>
    <div id="data_modal_test">Tsest</div>
   </div>
   
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