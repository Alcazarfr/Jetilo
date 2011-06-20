<h2 class="title">Créer une Partie</h2><br />
<div class="postgrand">
	<form name="creer" method="post" action="index.php">
		<?php
			echo "<input name=\"Joueur\" id=\"Joueur\" type=\"HIDDEN\" value=\"".$Joueur."\">";
		?>
		<div class="entry">
			<table style="border: none;" border="0">
				<tr>
					<td width="250" style="border: none;" border="0">Nom de la Partie</td>
					<td style="border: none;" border="0"><input type="text" name="PartieNom" value="" size="20" maxlength="30"></td>
				</tr>
				<tr>
					<td width="250" style="border: none;" border="0">Dimensions de la carte</td>
					<td style="border: none;" border="0">X <input type="text" name="DimensionX" value="10" size="2" maxlength="3"> / Y <input type="text" name="DimensionY" value="10" size="2" maxlength="3"></td>
				</tr>
				<tr>
					<td colspan="2" align="center" style="border: none;" border="0"><input type="submit" name="CreerPartie" value="Créer la partie!"/></td>
				</tr>
			</table>
		</div>		
	</form>
</div>