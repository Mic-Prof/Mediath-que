<!DOCTYPE html>
<html>
<head>
	<title>Mediatheque</title>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="template/films.css" type="text/css"/>
</head>
<body>
	<header>
		<form method="post" action="films.php">
			<input class="search" type="text" name="recherche" value="<?php echo $mode;?>" 
						placeholder="Rechercher">
			<input type="image" src="icons/search.png" alt="Rechercher"/>
		</form>	
	</header>
	<div id="main">
	<?php
		require_once("functions/template.func.php");
		headerFilms($page,$indiceMaxPage,$mode);
		//var_dump($films);
		afficheFilms2($films,$db);
		
	?>
	</div>
</body>
</html>