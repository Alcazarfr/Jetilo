<h2 class="title">Rejoindre une partie</h2><br />
<div class="postgrand">
	<form name="creer" method="post" action="index.php">
	<div class="entry">
		<table style="border: none;" border="0">
			<tr>
				<td width="150px" style="border: none;" border="0">Pseudo</td>
				<td style="border: none;" border="0">
					<?php
						connectMaBase();
						
						$sql = "SELECT JoueurNom
							FROM Joueur
							WHERE JoueurID = " . $Joueur;
						$req = mysql_query($sql) or die('Erreur SQL #025<br />'.$sql.'<br />'.mysql_error());
						
						$JoueurNom = "";
						if ($data = mysql_fetch_array($req))
						{
							$JoueurNom = $data['JoueurNom'] ? $data['JoueurNom'] : '';
							
						} 
						mysql_free_result($req);  
						
						echo '<input type="text" name="JoueurNom" value="' . $JoueurNom . '">';
 					?>
 				</td>
			</tr>
			<tr>
				<td width="250" style="border: none;" border="0">Mot de passe</td>
				<td style="border: none;" border="0"><input type="password" name="JoueurMdp"></td>
			</tr>
			<tr>
				<td width="250" style="border: none;" border="0">Partie</td>
				<td style="border: none;" border="0">
					<select name="RejoindrePartieID">
					<?php						
						$sql = 'SELECT * FROM Partie';
						$req = mysql_query($sql) or die('Erreur SQL #026<br />'.$sql.'<br />'.mysql_error());
						
						while ($data = mysql_fetch_array($req))
						{ 
							echo '<option value="'.$data['PartieID'].'">'.$data['PartieNom'].'</option>';     
						}  
						mysql_free_result($req);  
 						//mysql_close();
 					?>
 					</select>
 				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" style="border: none;" border="0"><input type="submit" name="Rejoindre" value="Rejoindre"/></td>
			</tr>
		</table>
	</div>
	</form>
</div>
