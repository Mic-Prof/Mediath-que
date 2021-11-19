<?php
function connexion(){
	$db = new PDO('mysql:host=localhost;dbname=mediatheque;port=3308;charset=UTF8',
		'root','');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $db;
}

function getFilms($db,&$indiceMaxPage,$nbFilmsParPage,$page){

	//compter le nombre de films présents dans la table films 
		$requete = $db->query('SELECT count(films_id) FROM films');
		//fetchColumn(0) récupère la valeur de la première colonne
		$totalFilms = $requete->fetchColumn(0);

		$indiceMaxPage = (int)($totalFilms/$nbFilmsParPage);	
		if($totalFilms%$nbFilmsParPage == 0){
			//$indinceMAxPage = $indiceMaxPage - 1;
			$indiceMaxPage --; 
		}

		//déduire l'indice en fonction de la page et du nombre de films par page
		$indice = $page * $nbFilmsParPage;


		//création de la requete Limit : à partir de la suivante et on en prends 2e param.
		/*$requete = $db->query(
			'SELECT * FROM films JOIN realisateurs ON real_id=films_real_id ORDER BY films_annee DESC, films_titre LIMIT '.$indice.','.$nbFilmsParPage);*/

		// fecthAll va exécuter la requête
		// et récupérer le résultat sous 
		// forme de tableau php
		/*$films = $requete->fetchAll();*/


		$sql = 'SELECT * FROM films JOIN realisateurs ON real_id=films_real_id ORDER BY films_annee DESC, films_titre LIMIT '.$indice.','.$nbFilmsParPage;
    	$stmt = $db -> prepare($sql);

   		 //3. Exécution de la requête
    	$stmt->execute(); 
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	/*$films=[];
    	foreach ($stmt as $row) {
    		$films[]=$row;
    	}*/
    	$films=$stmt->fetchAll();
    	/*foreach ($films as $key => $row) {
    		foreach ($row as $key2 => $value) {
    			echo $key2,$value;
    		}
    		
    	}*/

    	/*SELECT films_id,films_titre,films_annee,films_duree,
				films_affiche, films_resume, real_nom, GROUP_CONCAT(distinct acteurs_nom),GROUP_CONCAT(distinct genres_nom)
			 FROM films 
			JOIN realisateurs ON real_id=films_real_id 
			JOIN films_acteurs ON films_id=fa_films_id
			JOIN acteurs ON acteurs_id=fa_acteurs_id JOIN films_genres on fg_films_id=films_id JOIN genres on genres_id=fg_genres_id group by films_id having et non where pour ajouter les conditions de recherche sinon  les group_concat seront incomplets*/

		return $films;
}

function getFilmsRech($db,&$indiceMaxPage,$nbFilmsParPage,$page,&$mode){
		$requete = $db->prepare('SELECT count( distinct films_id ) FROM films JOIN realisateurs ON real_id=films_real_id JOIN films_acteurs ON films_id=fa_films_id JOIN acteurs ON acteurs_id=fa_acteurs_id JOIN films_genres on fg_films_id=films_id JOIN genres on genres_id=fg_genres_id WHERE films_titre LIKE :search OR real_nom LIKE :search OR acteurs_nom LIKE :search or genres_nom LIKE :search or films_resume LIKE :search');
		$mode=isset($_POST['recherche'])?$_POST['recherche']:$mode;
		$rech="";
		if($mode<>""){
			$rech=$mode;
		}else{
			$rech=$_POST["recherche"];
		}
		$requete->execute( array('search' => '%'.$rech.'%') );
		//fetchColumn(0) récupère la valeur de la première colonne
		
		$totalFilms = $requete->fetchColumn(0);
		$indiceMaxPage = (int)($totalFilms/$nbFilmsParPage);	
		if($totalFilms%$nbFilmsParPage == 0 && $indiceMaxPage!=0){
			//$indinceMAxPage = $indiceMaxPage - 1;
			$indiceMaxPage --;
		}

		//déduire l'indice en fonction de la page et du nombre de films par page
		$indice = $page * $nbFilmsParPage;

		//Si on a lancé la recherche 
		$requete = $db->prepare("SELECT films_id,films_titre, films_resume, films_affiche, films_duree, films_annee, real_id, real_nom, GROUP_CONCAT(DISTINCT acteurs_nom SEPARATOR ', ') AS acteurs, GROUP_CONCAT(DISTINCT genres_nom SEPARATOR ' | ') AS genres FROM films LEFT OUTER JOIN realisateurs ON films_real_id = real_id LEFT OUTER JOIN films_acteurs ON films_id = fa_films_id LEFT OUTER JOIN acteurs ON fa_acteurs_id = acteurs_id LEFT OUTER JOIN films_genres ON fg_films_id = films_id LEFT OUTER JOIN genres ON fg_genres_id = genres_id GROUP BY films_id HAVING acteurs LIKE :search OR films_titre LIKE :search OR real_nom LIKE :search OR genres LIKE :search OR films_resume LIKE :search limit ".$indice.','.$nbFilmsParPage);

			


		$requete->execute( array('search' => '%'.$rech.'%') );

		$films = $requete->fetchAll();
		return $films;
	}

//ancien
/*SELECT DISTINCT films_id,films_titre,films_annee,films_duree,
				films_affiche, films_resume, real_nom
			 FROM films 
			JOIN realisateurs ON real_id=films_real_id 
			JOIN films_acteurs ON films_id=fa_films_id
			JOIN acteurs ON acteurs_id=fa_acteurs_id
			WHERE films_titre LIKE :search OR real_nom LIKE :search OR acteurs_nom LIKE :search
			UNION SELECT DISTINCT films_id,films_titre,films_annee,films_duree,
				films_affiche, films_resume, real_nom
			 FROM films JOIN realisateurs ON real_id=films_real_id JOIN films_genres on fg_films_id=films_id JOIN genres on genres_id=fg_genres_id WHERE genres_nom LIKE :search limit '.$indice.','.$nbFilmsParPage*/

function getFilms2($db,&$indiceMaxPage,$nbFilmsParPage,$page){

	//compter le nombre de films présents dans la table films 
		$requete = $db->query('SELECT count(films_id) FROM films');
		//fetchColumn(0) récupère la valeur de la première colonne
		$totalFilms = $requete->fetchColumn(0);

		$indiceMaxPage = (int)($totalFilms/$nbFilmsParPage);	
		if($totalFilms%$nbFilmsParPage == 0){
			//$indinceMAxPage = $indiceMaxPage - 1;
			$indiceMaxPage --; 
		}

		//déduire l'indice en fonction de la page et du nombre de films par page
		$indice = $page * $nbFilmsParPage;


		//création de la requete Limit : à partir de la suivante et on en prends 2e param.
		// fecthAll va exécuter la requête
		// et récupérer le résultat sous 
		// forme de tableau php
		/*$films = $requete->fetchAll();*/


		$sql = "SELECT films_id,films_titre, films_resume, films_affiche, films_duree, films_annee, real_id, real_nom, GROUP_CONCAT(DISTINCT acteurs_nom SEPARATOR ', ') AS acteurs, GROUP_CONCAT(DISTINCT genres_nom SEPARATOR ' | ') AS genres FROM films LEFT OUTER JOIN realisateurs ON films_real_id = real_id LEFT OUTER JOIN films_acteurs ON films_id = fa_films_id LEFT OUTER JOIN acteurs ON fa_acteurs_id = acteurs_id LEFT OUTER JOIN films_genres ON fg_films_id = films_id LEFT OUTER JOIN genres ON fg_genres_id = genres_id GROUP BY films_id ORDER BY films_annee DESC, films_titre LIMIT ".$indice.','.$nbFilmsParPage;
    	$stmt = $db -> prepare($sql);

   		 //3. Exécution de la requête
    	$stmt->execute(); 
    	$stmt->setFetchMode(PDO::FETCH_ASSOC);
    	/*$films=[];
    	foreach ($stmt as $row) {
    		$films[]=$row;
    	}*/
    	$films=$stmt->fetchAll();
		return $films;
}

?>