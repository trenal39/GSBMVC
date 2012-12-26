<?php
include("vues/v_sommaire.php");

$listeVisiteur=$pdo->visiteurFicheEnCours();
$listeMois=$pdo->moisFicheEnCours();


    

?>

 <div id="contenu">
     
<h2>Valider les fiches de frais des visiteurs médicaux</h2>
<h3><legend>Visiteur à sélectionner :</legend></h3>
<div class="corpsForm">
    <form method="POST" action="index.php?uc=ValiderFicheFrais&action=VisiteurSelect">
      <br/>
      <label for="nomVisiteur">Visiteur :</label>
      <select name="visiteur">
		
		<?php
		while($visiteur=$listeVisiteur->fetch())
		{
                    if(isset($_POST['visiteur']) && $_POST['visiteur']==$visiteur['id'])
                    {
                        echo"<option Selected value='".$visiteur['id']."'>".$visiteur['id']." ".$visiteur['nom']."</option>";
                        
                    }
                    else
                    {
                        echo"<option value='".$visiteur['id']."'>".$visiteur['id']." ".$visiteur['nom']."</option>";
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
                        
                        echo"<option Selected value='".$mois['mois']."'>".GetLibelleMois(substr($mois['mois'],4,2))." ".substr($mois['mois'],0,4)."</option>";
                        
                    }
                    else
                    {
                        
                        echo"<option value='".$mois['mois']."'>".$lib=GetLibelleMois(substr($mois['mois'],4,2))." ".substr($mois['mois'],0,4)."</option>";
                    }
		}
            ?>  
            </select>
            
</div>
        <br/>
        <input type="Submit" value="Valider" style="left: 83%; position: relative">
    </form>
        <?php
        
        
        if($_GET['action']=="VisiteurSelect")
        {
           $res=$pdo->getLesFraisForfait($_POST['visiteur'],$_POST['mois']);
           
            if(isset($_GET['action2']) && $_GET['action2']=="FraitForfait")
             {
               $desFrais =array();
               foreach($res as $tab)
                {
                  $desFrais[$tab['idfrais']]=$_POST[$tab['idfrais']];
                }
                $pdo->majFraisForfait($_POST['Id'], $_POST['mois'], $desFrais);
             }
            if(empty($res))
		{
			echo"<br> Pas de fiche de frais pour ce visiteur et ce mois ou fiche de frais déjà validée.";
			
		}
		else
		{
                    // Met à jour l'état de la fiche de frais    
                    if($_GET['action']=="selectionnerVisiteur" && isset($_GET['action2']) && $_GET['action2']=="FicheValide")
                        {
                            $pdo->majEtatFicheFrais($_POST['identifiant'],$_POST['mois'],"VA");
                            echo"<script> alert('La fiche de frais a été validée avec succès ! ');";
                            echo"window.location : ('index.php?uc=ValiderFicheFrais&action=selectionnerVisiteur'</script>";
                        }
         ?>
                   
                        <form method="POST" onSubmit="return(confirm('Etes-vous sûr de vouloir valider cette fiche ?'));" action="index.php?uc=ValiderFicheFrais&action=SelectionVisiteur&action2=FicheValide">
                            <input TYPE='hidden' NAME='Id' VALUE='<?php echo $_POST['visiteur'] ?>'>
                            <input TYPE='hidden' NAME='mois' VALUE='<?php echo $_POST['mois'] ?>'>
                            <input type='Submit' value='Valider cette fiche' style='left: 37%; position: relative'>
                        </form>
    
                        <div class="corpsForm">
                        <form method="POST" action="index.php?uc=ValiderFicheFrais&action=VisiteurSelect&action2=FraitForfait" onsubmit="return confirm('Voulez-vous vraiment modifier les frais forfaits ?');">
                        <input TYPE='hidden' NAME='Id' VALUE='<?php echo $_POST['visiteur'] ?>'>
                        <input TYPE='hidden' NAME='mois' VALUE='<?php echo $_POST['mois'] ?>'>
                        <fieldset>
                        <legend><h4>Eléments forfaitisés :</h4></legend>
                        <p id="FraisForfait">
                        <?php 
                        foreach($res as $tab)
                         {
                             echo'<label for="'.$tab['libelle'].'">* '.$tab['libelle'].' :</label><input size="10" maxlength="5" type="text" id="Forfait" Value="'.$tab['quantite'].'" name="'.$tab['idfrais'].'"><br><br>';
                         }
                         ?>
                         </p> 
                         <?php
                          if(isset($_GET['action2']) && $_GET['action2']=="FraitForfait")
                            {echo "Element modifiés avec succès !";}
                            ?>
                         </fieldset>
                         </div>
                        <br><input type="Submit" value="Valider" style="left: 87%; position: relative"></form>
                        <br>
			
                        
                    <?php
                    // Supprime un frais non compris dans le forfait
                    if($_GET['action']=="VisiteurSelect")
                    {
                         if(isset($_GET['action2']) && $_GET['action2']=="FraitHorsForfait")
                            {
                                $pdo->supprimerFraisHorsForfait($_POST['IdHorsForfait']);
                                echo"<script> alert('Le frais hors forfait à été supprimer !');";
                                echo"window.location : ('index.php?uc=ValiderFicheFrais&action=VisiteurSelect'</script>";

                            }
                        $FraitHorsForfait=$pdo->getLesFraisHorsForfait($_POST['visiteur'],$_POST['mois']);
                    }
                          if(empty($FraitHorsForfait))
                          {
                              echo "<h4>Pas de frais hors forfait pour cette fiche</h4>";
                          }
                          else
                          {
                    ?>
                        <h4>Descriptif des éléments hors-forfait</h4>
                        <table width="100%" Cellpadding="10">
			<th>Date</th><th>Libellé</th><th>Montant</th><th></th>
                    <?php 
                  
                        foreach($FraitHorsForfait as $TabHorsForfait)
                        {?>
                        
                        <form method="POST" action ="index.php?uc=ValiderFicheFrais&action=VisiteurSelect&action2=FraitHorsForfait" onsubmit="return confirm('Voulez-vous vraiment supprimer ce frais hors forfait ?');">
                        <input TYPE='hidden' NAME='Id' VALUE='<?php echo $_POST['visiteur'] ?>'>
                        <input TYPE='hidden' NAME='mois' VALUE='<?php echo $_POST['mois'] ?>'>
			<tr><td><?php echo $TabHorsForfait['date']?></td><td><?php echo $TabHorsForfait['libelle']?></td><td><?php echo $TabHorsForfait['montant']?></td><td align=center><input  type="Submit" value="Supprimer"/></td></tr>
                        <input TYPE='hidden' NAME='IdHorsForfait' VALUE='<?php echo $TabHorsForfait['id']; ?>'>
                        <input TYPE='hidden' NAME='LibHorsForfait' VALUE='<?php echo $TabHorsForfait['libelle']; ?>'></form>
                    <?php 
                        }
                     echo"</table>";
                          }
                    if(isset($_GET['action2']) && $_GET['action2']=="FraitHorsForfait")
                    {
                      echo "Le frais hors forfait ".$_POST['LibHorsForfait']." à été supprimé.";
                    }
                  }
       
        } ?>
 </div>