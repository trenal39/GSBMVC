<?php
	$serveur='localhost';   
   $utilisateur='root';
   $pass='';
   $nom_base='gsb_frais_new';
  
  // connexion à la base
   mysql_connect($serveur,$utilisateur,$pass) or die("connexion impossible");
    
   // sélection de la base
   mysql_select_db($nom_base) or die("base non trouvée");
	
	$sql = "SELECT * FROM utilisateur";
	$query = mysql_query($sql) or die(mysql_error());
	while($data = mysql_fetch_array($query))
	{
		$sql = "UPDATE utilisateur SET mdp =  '".crypt($data['login'], $data['mdp'])."' WHERE  login =  '".$data['login']."';";
		mysql_query($sql) or die(mysql_error());
	}
	echo "les mots de passe on été crypté.";
?>
