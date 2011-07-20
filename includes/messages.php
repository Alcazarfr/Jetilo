<?php

/**
 * Déclenche l'émission d'un événement
 * 
 * @param string $EvenementID			Identifiant de l'événement
 * @param int $TeritoireID				ID du territoire où se passe l'événement
 * @param int $Partie					ID de la partie pendant laquelle se passe l'événement
 * @param int $Source					ID du joueur ayant émis l'événement
 * @param [string=>int|[int]] $Joueurs		Liste des joueurs concernés par l'événement, groupés par "rôles"
 * @param [string=>string] $Parametres	Liste des paramètres à inclure dans les messages générés
 */
function DeclencherEvenement($EvenementID, $TeritoireID, $Partie, $Source, $Joueurs, $Parametres)
{
	global $EVENEMENTS;
	
	$événement = $EVENEMENTS->événement[$EvenementID];
	
	// Récupération des paramètres, avec l'index numérique
	$paramètres = array();
	foreach ($événement->paramètres as $ordre => $nom)
	{
		$paramètres[$ordre] = $Parametres[$nom];
	}
	
	// Stocke en index la liste des joueurs pour lequel un message a déjà été choisi
	$exclus_map = array();
	
	// On envoit pour chaque "rôle" le message qui lui est dédié
	foreach ($événement->destinataires->destinataire as $id => $destinataire)
	{
		if (isSet($Joueurs[$id]))
		{
			// Si $joueurs n'est pas un tableau, on le tableau-tify
			$joueurs = is_array($Joueurs[$id]) ? $Joueurs[$id] : array($Joueurs[$id]);
			
			if (count($joueurs) > 0)
			{
				// Construison le titre et la description pour tous les joueurs ayant ce "rôle"
				$titre = vsprintf($destinataire->titre, $paramètres);
				$description = vsprintf($destinataire->description, $paramètres);

				foreach ($joueurs as $joueur)
				{
					// Envoyons le message à chaque joueur, tout en évitant d'envoyer deux messages à un même joueur
					if (!isSet($exclus_map[$joueur]))
					{						
						Message($Partie, $joueur, $titre, $description, 
								$Source, '', 
								$destinataire->couleur, $destinataire->temps);
						
						$exclus_map[$joueur] = true;
					}
				}
			}
		}
	}
	
	// Les destinataires concernés ont bien tous reçu l'événement.
	// Faisons maintenant de même pour les autres joueurs, en particulier s'ils espionent le territoire.

	// Calculons d'abord la capacité d'information de ce joueur pour ce territoire et cette stratégie 
	$capacites_joueurs = CalculerCapaciteInformation($événement->stratégie, $TeritoireID);
	
	// Ordonne les événements "espionables" par niveau dégressif
	uasort($événement->autres, function ($a, $b) 
	{
		return $a->niveau == $b->niveau ? 0 : 
				$a->niveau < $b->niveau ? 1 : -1;
	});
	
	// Voyons chaque joueur ayant des espions... (on ignore ceux n'en ayant pas)
	foreach ($capacites_joueurs as $joueur => $capacite)
	{
		// Sautons tout joueur concerné par l'événement (on l'a déjà traité)
		if (isSet($exclus_map[$joueur]))
			continue; 
		
		// Trouvons le premier message qu'il a le droit d'entendre
		foreach ($événement->autres as $destinataire)
		{
			// A-t-il une capacite d'espionage suffisante ? Si oui, on lui envoit le message
			if ($capacite >= $destinataire->niveau)
			{
				$titre = vsprintf($destinataire->titre, $paramètres);
				$description = vsprintf($destinataire->description, $paramètres);
								
				Message($Partie, $joueur, $titre, $description, 
						$Source, '', 
						$destinataire->couleur, $destinataire->temps);
				
				$exclus_map[$joueur] = true;
				break;
			}
		}
	}
	
	// Envoyons le message broadcast, le cas échéant
	foreach ($événement->autres as $destinataire)
	{
		if ($destinataire->niveau == 0)
		{
			$titre = vsprintf($destinataire->titre, $paramètres);
			$description = vsprintf($destinataire->description, $paramètres);
			
	
			$exclus_str = join(', ', array_keys($exclus_map));
			
			Message($Partie, 0, $titre, $description, 
					$Source, $exclus_str, 
					$destinataire->couleur, $destinataire->temps);
			
			break;
		}
	}
}


/* Insérer un message

Partie 			: ID de la Partie
Destinataire 	: Joueur à qui le message est reservé; Laisser 0 si le message est visible par tous
Source 			: Origine du message (ID du joueur)
Exlus			: varchar, la liste des joueurs qui ne liront pas ce message
				  Ex : un message public qui a déjà été envoyé à un joueur, de faÃ§on personnalisé
Couleur			: La Couleur, noire et rouge (si important)
Durée			: Durée d'affichage
				  0 pour rester à vie, 5 pour un affichage court, 10 pour un normal, 15 pour un long

*/
/**
 * Insérer un message
 * @param unknown_type $Partie 			ID de la Partie
 * @param unknown_type $Destinataire	Joueur à qui le message est reservé; Laisser 0 si le message est visible par tous
 * @param unknown_type $Titre			Titre du message
 * @param unknown_type $Texte			Contenu du message
 * @param unknown_type $Source			Origine du message (ID du joueur)
 * @param unknown_type $Exclus			La liste des joueurs qui ne liront pas ce message; Ex : un message public qui a déjà été envoyé à un joueur, de façon personnalisée
 * @param unknown_type $Couleur			La Couleur, noire et rouge (si important)
 * @param unknown_type $Duree			Durée d'affichage (0 pour rester à vie, 5 pour un affichage court, 10 pour un normal, 15 pour un long)
 */
function Message($Partie, $Destinataire, $Titre, $Texte, $Source, $Exclus, $Couleur, $Duree)
{
	/*$Titre = mysql_escape_string($Titre);
	$Texte = mysql_escape_string($Texte);*/
	
	$sql = "INSERT INTO Message (MessagePartie, MessageDestinataire, MessageExclus, MessageTitre, MessageTexte, MessageSource, MessageTime, MessageCouleur, MessageDuree)
		VALUES (".$Partie.", " . $Destinataire . ", '" . $Exclus . "', '" . htmlspecialchars(addslashes($Titre)) . "', '" . htmlspecialchars(addslashes($Texte)) . "', " . $Source . ", " . time() . ", '" . $Couleur . "', " . $Duree . ")";
	mysql_query($sql) or die('Erreur SQL #29 Message<br />'.$sql.'<br />'.mysql_error());
}

/**
 * Retourne une liste de messages, selon certains paramètres.
 * 
 * @param IDPartie	$Partie		Contexte de partie
 * @param IDJoueur 	$Joueur		Joueur à qui est adressé le message
 * @param TimeStamp $TimeMin	(optionel) Limite de date minimale
 * @param TimeStamp $TimeMax	(optionel) Limite de date maximale
 * @param IDJoueur 	$Source		Auteur du message
 * @param Booléen 	$Nouveaux	Si true, ne récupère que les messages "non lus", et les passes en "lus"
 * @param Booléen 	$Descendant	Si true, trie les messages du plus récent au plus ancien 
 */
function LireMessages($Partie, $Joueur, $TimeMin, $TimeMax, $Source, $Nouveaux, $Descendant)
{
	$tables = "Message m";
	$conditions = "MessagePartie = " . $Partie . "
		AND MessageDestinataire IN (0, " . $Joueur . ")";
	
	// Filtres
	
	if ($TimeMin)
	{
		$conditions .= " AND m.MessageTime >= " . $TimeMin;
	} 
	
	if ($TimeMax)
	{
		$conditions .= " AND m.MessageTime <= " . $TimeMax;
	}
		
	if ($Source)
	{
		$conditions .= " AND m.MessageSource = " . Source;
	}
	
	if ($Nouveaux)
	{
		// On a besoins de faire une jointure avec la table des messages lus (pour les messages à plusieurs destinataires)
		$tables .= ' LEFT JOIN MessageLu ml ON (ml.MessageLuID = m.MessageID AND ml.MessageLuJoueur = ' . $Joueur . ')';
		
		// Un message est non-lu s'il est marqué comme tel ET, pour les broadcast, s'il n'y a pas d'entrée correspondante dans la table MessageLu
		$conditions .= " AND m.MessageLu = 0 AND (m.MessageDestinataire <> 0 OR ml.MessageLuJoueur IS NULL)";
	}
	
	// Construction de la requête
	$sql = "SELECT m.* FROM " . $tables . " WHERE " . $conditions . " ORDER BY MessageTime " . ($Descendant ? 'DESC' : 'ASC');
	
	$liste = array();	
	$nouveauxMessageLus = array();
			
	// Récupération des messages
	$req = mysql_query($sql) or die('Erreur SQL #052<br />'.$sql.'<br />'.mysql_error());
	while ($data = mysql_fetch_array($req) )
	{
		$afficher = TRUE;
		
		// Certains joueurs sont exclus des messages (s'ils sont à l'origine d'un message public
		// Ex: X déclare la guerre à B. On crée un message public pour C, D et E, mais A et B ne le verront pas.
		if ( $data['MessageExclus'] )
		{
			$explode = explode(", ", $data['MessageExclus']);
			for ( $i = 0 ; $i < count($explode) ; $i++ )
			{
				$JoueurExclu = $explode[$i];
				if ( $Joueur == $JoueurExclu )
				{
					$afficher = FALSE;
					break;
				}
			}
		}
		
		// SI le joueur n'est pas exclu, alors on récupère le message
		if ($afficher)
		{
			$data['MessageTitre'] = htmlspecialchars_decode($data['MessageTitre']);
			$data['MessageTexte'] = htmlspecialchars_decode($data['MessageTexte']);
				
			$liste[] = $data;
			
			// Gardons la liste des messages broadcast lus : il faudra ajouter des entrées pour chacun d'eux
			if ($data['MessageDestinataire'] == 0)
			{				
				$nouveauxMessageLus[] = '(' . $data['MessageID']  . ', ' . $Joueur . ')';
			}
		}
	}
	
	// Si on veut ne récupérer que les nouveaux messages, ces derniers ne sont par définition plus nouveaux : on les marque comme lus
	if ($Nouveaux)
	{
		// Ajoute dans MessageLu les entrées correspondantes pour les messages broadcast non-lus
		if (count($nouveauxMessageLus) > 0)
		{
			// On met à jour les messages publics lu par le joueur
			$sql = 'INSERT INTO MessageLu (MessageLuID, MessageLuJoueur) VALUES '
				. join(',', $nouveauxMessageLus);
			mysql_query($sql) or die('Erreur SQL #035'.$sql.'<br />'.mysql_error());
		}
		
		// On met à jour les messages privés : ils sont désormais lu
		$sql = "UPDATE Message
			SET MessageLu = 1
				WHERE MessagePartie = " . $Partie . "
				AND MessageDestinataire = " . $Joueur . "
				AND MessageLu = 0";
		mysql_query($sql) or die('Erreur SQL #028<br />'.$sql.'<br />'.mysql_error());
	}
	
	return $liste;
}

/**
 * Calcule la capacité d'espionage des joueurs sur un territoire et pour une stratégie donnés 
 * @param string $Stratégie		Nom de la stratégie
 * @param int $TeritoireID		ID du territoire à sonder
 * @return [int=>int]			Capacité d'information par joueur ayant des agents sur place
 */
function CalculerCapaciteInformation($Stratégie, $TeritoireID)
{
	global $AGENTS;
	
	$maxValeurs = array();
	
	// Récupérons les agents du territoire, ainsi que les joueurs qui les possèdent
	$sql = "SELECT a.*, j.JoueurID "
			." FROM Agent a "
			." LEFT JOIN Etat e ON a.AgentEtat = e.EtatId "
			." LEFT JOIN Joueur j ON e.EtatJoueur = j.JoueurID "
			." WHERE a.AgentTerritoire = " . $TeritoireID;

	$req = mysql_query($sql) or die('Erreur SQL #052<br />'.$sql.'<br />'.mysql_error());
	while ($agent = mysql_fetch_array($req) )
	{		
		// Récupérons les paramètres de l'agent
		$parametres = $AGENTS->agent[$agent['AgentType']];
		
		// Les agents qui ne font pas de renseignement ne captent que les infos. de niveau 0 et 1
		if (!$parametres->renseignement)
		{
			$valeur = 1;
		}
		else
		{
			// Aléa de +0 à 20% 
			$valeur = $agent['AgentCapaciteReussite'] * mt_rand(10, 12) / 10;
		
			// Un agent inadapté pour sa stratégie voit sa capacité divisée par 4, et toutes les infos de niveau <= 2 sont systématiquement obtenues
			if ($parametres->stratégie != $Stratégie)
			{
				$valeur /= 4;
			}
			// Un agent adapté est à plein potentiel, et toutes les infos de niveau <= 2 sont systématiquement obtenues
			
			$valeur += 2;
		}
		
		// On sauvegarde la valeur maximale par joueur		
		if (!isSet($maxValeurs[$agent['JoueurID']]) || $maxValeurs[$agent['JoueurID']] < $valeur)
		{
			$maxValeurs[$agent['JoueurID']] = $valeur;
		}
	}	
	
	mysql_free_result($req);
	
	return $maxValeurs;
}

?>