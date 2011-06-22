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
					echo $JoueurNom . " - <a href='#Etat' onClick='EtatInformations(".$Etat.")'>" . $EtatNom . "</a>";
					$Image = "./images/carte/" . $EtatCouleur . "/1-1-1-1.gif";
					echo "&nbsp;&nbsp;<img src=\"" . $Image . "\" width=\"20px\">";
				?>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">

$(document).ready(function()
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
});

</script>

<div id="demo-modal">
   <a href="#" rel="modal">Click here</a> to see a qTp modal dialog.
 
   <div style="display: none;">
      Heres an example of a natural extension to qTip... use as a <b>modal dialogue</b>!
      <br /><br />
      Much like the <a href="http://onehackoranother.com/projects/jquery/boxy/">Boxy</a> plugin, but if you're already
      using qTip on your page, why not utilise the same library for your dailogues too?
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