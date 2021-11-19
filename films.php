<?php

/*function bidon($fi){
foreach ($fi as $key => $value) {
	# code...
}
return "";
}*/
//toto
	require_once("functions/db.func.php");

	try{
		
	$db=connexion();
	$mode=""; //sert à transmettre le mot recherché aux anchors
	$nbFilmsParPage = 10;
	$page = 0;
	if(isset($_GET['page']) && $_GET['page']>0){
			$page = $_GET['page'];
	}
	if(isset($_GET['mode'])){
			$mode = $_GET['mode'];
	}

	//test que $_POST['recherche'] existe et qu'il est non vide, non null, non = à 0
	if((!empty($_POST['recherche']) && strlen(trim($_POST['recherche']))>2) || $mode <> "") {
		$indiceMaxPage=0;
		$films=getFilmsRech($db,$indiceMaxPage,$nbFilmsParPage,$page,$mode);

	}else{
		$indiceMaxPage=0;
		$films=getFilms2($db,$indiceMaxPage,$nbFilmsParPage,$page);
		//bidon($films);
	}

	}catch (Exception $ex) {
    die("ERREUR FATALE : ". $ex->getMessage().'<form><input type="button" value="Retour" onclick="history.go(-1)"></form>');
    }
	require_once("template/films.template.php");

?>




