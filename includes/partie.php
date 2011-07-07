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
	
	/*DeclancherEvenement('famine', 88, $Partie, 0, 
		array('occupant' => $Joueur + 1, 'test' => $Joueur + 1), 
		array('TerritoireNom' => 'Boulgourville', 'PopulationMorte' => 42));*/

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

/* Modal dans les retours Ajax 
Fonctionne : 


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
      style: 'ui-tooltip-light ui-tooltip-rounded',
         events: {
            // Hide the tooltip when any buttons in the dialogue are clicked
            render: function(event, api) {
               $('button', api.elements.content).click(api.hide);
            },
            // Destroy the tooltip once it's hidden as we no longer need it!
            hide: function(event, api) { }
         }
   }, event);
});
*/

$(document).ready(function()
{
   /*
    * Common dialogue() function that creates our dialogue qTip.
    * We'll use this method to create both our prompt and confirm dialogues
    * as they share very similar styles, but with varying content and titles.
    */
   function dialogue(content, title) {
      /* 
       * Since the dialogue isn't really a tooltip as such, we'll use a dummy
       * out-of-DOM element as our target instead of an actual element like document.body
       */
      $('<div />').qtip(
      {
         content: {
            text: content,
            title: title
         },
         position: {
            my: 'center', at: 'center', // Center it...
            target: $(window) // ... in the window
         },
         show: {
            ready: true, // Show it straight away
            modal: {
               on: true, // Make it modal (darken the rest of the page)...
               blur: false // ... but don't close the tooltip when clicked
            }
         },
         hide: false, // We'll hide it maunally so disable hide events
         style: 'ui-tooltip-light ui-tooltip-rounded ui-tooltip-dialogue', // Add a few styles
         events: {
            // Hide the tooltip when any buttons in the dialogue are clicked
            render: function(event, api) {
               $('button', api.elements.content).click(api.hide);
            },
            // Destroy the tooltip once it's hidden as we no longer need it!
            hide: function(event, api) { api.destroy(); }
         }
      });
   }
 
   // Our Alert method
   function Alert(message)
   {
      // Content will consist of the message and an ok button
      var message = $('<p />', { text: message }),
         ok = $('<button />', { text: 'Ok', 'class': 'full' });
   
      dialogue( message.add(ok), 'Alert!' );
   }
 
   // Our Prompt method
   function Prompt(question, initial, callback)
   {
      // Content will consist of a question elem and input, with ok/cancel buttons
      var message = $('<p />', { text: question }),
         input = $('<input />', { val: initial }),
         ok = $('<button />', { 
            text: 'Ok',
            click: function() { callback( input.val() ); }
         }),
         cancel = $('<button />', {
            text: 'Cancel',
            click: function() { callback(null); }
         });
 
      dialogue( message.add(input).add(ok).add(cancel), 'Attention!' );
   }
   
   // Our Confirm method
   function Confirm(question, callback)
   {
      // Content will consist of the question and ok/cancel buttons
      var message = $('<p />', { text: question }),
         ok = $('<button />', { 
            text: 'Ok',
            click: function() { callback(true); }
         }),
         cancel = $('<button />', { 
            text: 'Cancel',
            click: function() { callback(false); }
         });
 
      dialogue( message.add(ok).add(cancel), 'Do you agree?' );
   }
 
   // Setup the buttons to call our new Alert/Prompt/Confirm methods
   $('#alert').click(function() {
      Alert('Custom alert() functions are cool.');
   });
   $('#prompt').click(function() {
      Prompt('How would you describe qTip2?', 'Awesome!', function(response) {
         alert(response);
      });
   });
   $('#confirm').click(function() {
      Confirm('Click Ok if you love qTip2', function(yes) {
         // do something with yes
      });
   });
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
   <a id="prompt" class="nice">Click me for a prompt dialogue</a>
    <a id="teest" name="teest" class="teest" href="#">OQTIP 2</a>
    <a id="modal_2" href="#" class="modal">Ouvrir la modal 2</a>
    <a id="modal_test" href="#" class="modal">Ouvrir la modal test</a>
    <a id="modal_5" href="#" class="modal">Ouvrir la modal 5</a>

   <div style="display: none;">
    <div id="titre_modal_1">Titre1</div>
    <div id="data_modal_1">MyName My ID :<input type="text" size="5" name="MyName" id="MyID"><br /><br /><button type="button">BOUTON</button></div>
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