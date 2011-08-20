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
	
	/*DeclencherEvenement('famine', 88, $Partie, 0, 
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
function EtatInformations(EtatCible)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID 		= $('#Etat').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=EtatInformations&Partie="+PartieID+"&Etat="+EtatID+"&EtatCible="+EtatCible+"&Joueur="+JoueurID,
		success:
    		function(retour){
			$("#leterritoire").empty().append(retour);
			}
	}
	);
}

/* Affichage des infos sur un Etat */
function ArmeeAttaquer(ArmeeID)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();
	var JoueurID 	= $('#Joueur').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=ArmeeAttaquer&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID+"&Armee="+ArmeeID
	}
	);
	setTimeout("AfficherBataille(false)",500);
	setTimeout("MessageLire(false)",500);
}

/* Revendiquer une victoire */
function RevendiquerVictoire(BatailleID)
{
	var EtatID 		= $('#Etat').val();
	var PartieID 	= $('#Partie').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=RevendiquerVictoire&Partie="+PartieID+"&Etat="+EtatID+"&Bataille="+BatailleID
	}
	);
	setTimeout("AfficherBataille(false)",500);
	setTimeout("MessageLire(false)",500);
}

/* Affichage des infos sur un territoire */
function TerritoireInformations(TerritoireID)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID	 	= $('#Etat').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=territoireInformations&Partie="+PartieID+"&Territoire="+TerritoireID+"&Joueur="+JoueurID+"&Etat="+EtatID+"&SousMode=Jeu",
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
function ActionCreer(ActionID, SourceID, CibleID, Details)
{
	var PartieID 	= $('#Partie').val();
	var JoueurID 	= $('#Joueur').val();
	var EtatID 		= $('#Etat').val();

	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=ActionCreer&Partie="+PartieID+"&Etat="+EtatID+"&Joueur="+JoueurID+"&ActionID="+ActionID+"&SourceID="+SourceID+"&CibleID="+CibleID+"&Details="+Details
	});

	setTimeout("CarteChargement(false)",200);
	setTimeout("Population(false)",200);
	setTimeout("MessageLire(false)",200);
	setTimeout("AfficherBataille(false)",200);
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

/* Affichage des actions en cours */
function ActionsEnCours(Boucle)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();

	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=ActionsEnCours&Partie="+PartieID+"&Etat="+EtatID,
		success:
    		function(retour){
			$("#ActionsEnCours").empty().append(retour);
			}
	}
	);
	if ( Boucle == true )
	{
		setTimeout("ActionsEnCours(true)",13000);
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
function ActionLancer(Boucle)
{
	var PartieID 	= $('#Partie').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=ActionLancer&Partie="+PartieID
	}
	);
	setTimeout("ActionsEnCours(false)",500);
	if ( Boucle == true )
	{
		setTimeout("ActionLancer(true)",50000);
	}
}


/* Affichage des infos sur un territoire */
function Commercer(Boucle)
{
	var PartieID 	= $('#Partie').val();
	
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=Commercer&Partie="+PartieID
	}
	);
	if ( Boucle == true )
	{
		setTimeout("Commercer(true)",60000);
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

/* Chargement des batailles */
function AfficherBataille(Boucle)
{
	var PartieID = $('#Partie').val();
	var EtatID = $('#Etat').val();
	$.ajax(
	{
		type: "POST",
		url: "./includes/ajax/partie.php",
		data: "mode=afficherBataille&Partie="+PartieID+"&Etat="+EtatID,
		success:
    		function(retour){
			$("#Bataille").empty().append(retour);
			}
	}
	);
	if ( Boucle == true )
	{
		setTimeout("AfficherBataille(true)",20000);
	}
}


/* InfosBulle dans les retours Ajax */

$('a[title]').live('click', function(event) {
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


$('.infobullefixe').live('click', function(event)
{
	var PartieID 	= $('#Partie').val();
	$(this).each(function()
	{
		$(this).qtip(
   		{
			content:
			{
				text: 'Loading...',
				ajax:
               	{
               		url: "./includes/ajax/partie.php",
                  	data: { mode: 'InfobulleFixe', Partie: PartieID, InfobulleID: $.attr(this, 'id')},
                  	method: 'post',
                  	success: function(data,status)
                  	{
                  		// data holds the html with the button for close the tooltip
                     	this.set('content.text', data); // set ajax content to show
                  	}
                },
				title: 
				{
					text: 'Actions',
           			button: true
         		}
      		},
      		position:
      		{
         		my: 'bottom center', // ...at the center of the viewport
         		at: 'top center'
         	},
      		show:
      		{
         		event: 'mouseover', // Show it on click...
         		ready: true
       		},
       		hide: false,
       		style: 'ui-tooltip-light ui-tooltip-rounded',
        	events: 
        	{
         	}
    	}, event);
   	});
});

$('.modal').live('mouseover', function(event)
{
	var PartieID 	= $('#Partie').val();
	var EtatID 		= $('#Etat').val();
	var JoueurID 	= $('#Joueur').val();
	$(this).each(function()
	{
		$(this).qtip(
   		{
			content:
			{
				text: 'Loading...',
				ajax:
               	{
               		url: "./includes/ajax/partie.php",
                  	data: { mode: 'Modal', Partie: PartieID, Etat: EtatID, Joueur: JoueurID, ModalID: $.attr(this, 'id') },
                  	method: 'post',
   					success: function(data, status)
  					{
      					// data holds the html with the button for close the tooltip
      					this.set('content.text', data); // set ajax content to show

     					// Bind your events
      					$('button', this.elements.content).click($.proxy(this.hide, this));
   					}
                },
				title: 
				{
					text: $(this).attr('id'),
           			button: true
         		}
      		},
      		position:
      		{
         		my: 'center', // ...at the center of the viewport
         		at: 'center',
        		target: $(window)
     		},
      		show:
      		{
         		event: 'click', // Show it on click...
         		solo: true, // ...and hide all other tooltips...
         		modal: true // ...and make it modal
       		},
       		hide: false,
       		style: 'ui-tooltip-light ui-tooltip-rounded',
        	events: 
        	{
  				render: function(event, api)
  				{
               		$('button', api.elements.content).click(api.hide);
   				},
            	hide: function(event, api) { api.destroy(); }
         	}
    	}, event);
   	});
});


/* Chargement des fonctions automatiques au chargement de la page */

$(window).load(function(){
	Production(true);
	EtatInformations(<?php echo $Etat ?>);
	CarteChargement(true);
	MessageLire(true);
	AfficherBataille(true);
	Commercer(true);
});


</script>


<?php
	/* Test sur la croissance de la population
	echo "<div class='postgrand'> Test de croissance de la population<br />";
	
	$Population = 500;
	$Croissance = 1.02;
	$Minute		= 0;
	do
	{
		echo $Minute . " : " . round($Population) . "<br />";
		$Minute++;
		$Population *= $Croissance;
	} while ( $Population < 10000 )
	
	echo "</div>";
	*/
?>
<div id="Bataille"></div>

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
		Mises à jour manuelles : <a href="#" onClick="CarteChargement(false);">Carte</a> • <a href="javascript:void(0);" onclick="MessageLire(false);">Messages</a> • <a href="javascript:void(0);" onclick="Population(false);">Population</a> • <a href="javascript:void(0);" onclick="Production(false);">Production</a> • <a href="javascript:void(0);" onclick="ActionLancer(false);">Actions</a> • <a href="javascript:void(0);" onclick="AfficherBataille(false);">Bataille</a> • <a href="javascript:void(0);" onclick="Commercer(false);">Commercer</a>
	</div>
</div>
<div class="postgrand">
	<div class="entry">
		<div id="Population"></div>
	</div>
</div>
<div class="postgrand">
	<div class="entry"><h2>Journal des messages</h2>
		<table width="100%">
			<tr>
				<td width="50%">
					<div id="Journal"></div>
				</td>
				<td width="50%">				
					<div id="ActionsEnCours"></div>
				</td>
			</tr>
		</table>
	</div>
</div>