<?php

include("vues/v_sommaire.php");

$listeVisiteur=$pdo->visiteurValidé();
$listeMois=$pdo->moisFicheValidée();

if(isset($_GET['action']) && $_GET['action']=="ValideFiche")
{
    $pdo->majEtatFicheFrais($_POST['Id'],$_POST['mois'],"RB");
    echo"<script> alert('Fiche de frais payée.');";
    echo"window.location = 'index.php?uc=suivie&action=selectionnerVisiteur'</script>";
}

?>
 <div id="contenu">
      <h2>Liste des visiteurs ayant des fiches de frais validées et non remboursées</h2>
      <h3><legend>Visiteur à sélectionner :</legend></h3>
	    <div class="corpsForm">
                <form method="POST" action="index.php?uc=suivie&action=VisiteurSelected">
                <br/>
				<label for="visiteur">Visiteur :</label>
				<select name="Id">
		
<?php


		while($visiteur=$listeVisiteur->fetch())
		{
                    if(isset($_POST['Id']) && $_POST['Id']==$visiteur['id'])
                    {
                        echo"<option label='Visiteur' Selected value='".$visiteur['id']."'>".$visiteur['id']." ".$visiteur['nom']."</option>";
                        
                    }
                    else
                    {
                        echo"<option label='Visiteur' value='".$visiteur['id']."'>".$visiteur['id']." ".$visiteur['nom']."</option>";
                    }
		}
		
?>
            </select>
            <br/><br/>
            <label for="mois">Mois :</label>
			<select name='mois' id="mois">

<?php	
		while($mois=$listeMois->fetch())
		{
                    
                    if(isset($_POST['mois']) && $_POST['mois']==$mois['mois'])
                    {
                        echo"<option label='MoisVisiteurs' Selected value='".$mois['mois']."'>".GetLibelleMois($mois['mois'])." ".substr($mois['mois'],0,4)."</option>";
                        
                    }
                    else
                    {
                    echo"<option label='MoisVisiteurs' value='".$mois['mois']."'>".GetLibelleMois($mois['mois'])." ".substr($mois['mois'],0,4)."</option>";
                    }
}
?>  
        </select>
        </div>
        <br/>
        <input type="Submit" value="Valider" style="left: 67%; position: relative"><input type="reset" value="Effacer" style="left: 69%; position: relative"> </form>
<?php
        
		if($_GET['action']=="VisiteurSelected")
        {
            $res=$pdo->TestMoisFiche($_POST['Id'], $_POST['mois']);
            $res=$res->fetch();
            if(empty($res))
		{
			echo"<br> Aucune fiche de frais pour ce visiteur pour ce mois ou de fiche de frais déjà validée.";
			
		}
                else
                {
						$ChaineVAOURB=" Validée depuis le ";
						if($res['idEtat']!="VA")
						{$ChaineVAOURB=" Rembourser depuis le ";}
						echo"<h3>Fiche de frais du mois de ".GetLibelleMois($_POST['mois'])." ".substr($_POST['mois'],0,4)." : ".$ChaineVAOURB.$res['dateModif']."</h3>";
?>
                    <div class="encadre">
<?php
                        $a=$pdo->getLesInfosFicheFrais($_POST['Id'], $_POST['mois']);
                        echo "<b>Montant validé :".$a['montantValide']."</b><br/>";
                        echo '<hr align="left" width="90%" color="Black" size="3"><br/>';
                        echo "<form method='POST' action='index.php?uc=suivie&action=ValideFiche' onsubmit='return confirm(\"Voulez-vous vraiment payer cette fiche ?\")';>";
                        echo "<input TYPE='hidden' NAME='Id' VALUE='".$_POST['Id'] ."'>";
                        echo "<input TYPE='hidden' NAME='mois' VALUE='".$_POST['mois']."'>";
                        echo "<input type='Submit' value='Payer cette fiche' style='left: 40%; position: relative'>";
                        echo "</form>";
                        echo"<br/><br/>";
                        echo"Quantités des éléments forfaitisés";
                        $FraisForfait=$pdo->getLesFraisForfait($_POST['Id'],$_POST['mois']);
                        echo"<table Cellpadding='10' border='2'>";
                        foreach($FraisForfait as $tab)
                         {
                             echo'<th>'.$tab['libelle'].'</th>';
                         }
                         echo "<tr>";
                        foreach($FraisForfait as $tab)
                         {
                             echo'<td>'.$tab['quantite'].'</td>';
                         }
							echo"</tr></table>";
							echo"<br/>";
							echo"Decriptif des éléments hors forfait - ".$a['nbJustificatifs']." justificatifs reçus -";
							echo"<br/>";
                        $FraitHorsForfait=$pdo->getLesFraisHorsForfait($_POST['Id'],$_POST['mois']);
							echo"<table Cellpadding='10' border='2' width='80%'>";
							echo"<th>Date</th><th>Libellé</th><th>Montant</th>";
                       
                        foreach($FraitHorsForfait as $TabHorsForfait)
                        {
                        
							echo"<tr><td>".$TabHorsForfait['date']."</td><td>".$TabHorsForfait['libelle']."</td><td>".$TabHorsForfait['montant']."</td></tr>";

                        }
							echo"</table>";
							echo"</div>";
          
                }
        }
?>