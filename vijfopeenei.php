<?php
session_start();
require_once 'classes/Vijfopeenei.php';

if(isset($_POST['restart'])) {
	$opponent 	= $_POST['opponent'];
	$board_size = $_POST['board_size'];
	$in_a_row 	= isset($_POST['in_a_row']) ? $_POST['in_a_row'] : 5;
	$tictactoe 	= new Tictactoe($opponent, $board_size, $in_a_row);
} else if(!isset($_SESSION['tictactoe'])) {
	$tictactoe = new Tictactoe();
} else {
	$tictactoe = unserialize($_SESSION['tictactoe']);
}

if(isset($_POST['field'])) {
	$tictactoe->executeTurn($_POST['field']);
}

$_SESSION['tictactoe'] = serialize($tictactoe);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Vijf op een ei</title>
	<link rel="stylesheet" type="text/css" href="style/vijfopeenei-style.css">
</head>
<body>
<a href="./">
	<img id="back" src="style/images/terug.png" alt="Terug naar luik" title="Terug naar luik">
</a>
<section>
<h1>Vijf op een ei</h1>
<?php echo $tictactoe->displayBoard(); ?>
<br>
<?php echo $tictactoe->getResult(); ?>
<br><br>
<form action="" method="post">
	<button type="submit" name="restart">Herstart</button><br><br>
	Tegenstander:
	<select name="opponent">
		<!--
		<option value="cpu-beginner"<?php echo ($tictactoe->opponent == 'cpu-beginner') ? ' selected' : ''; ?>>CPU - beginner</option>
		-->
		<option value="cpu-expert"<?php echo ($tictactoe->opponent == 'cpu-expert') ? ' selected' : ''; ?>>CPU - expert</option>
		<option value="person"<?php echo ($tictactoe->opponent == 'person') ? ' selected' : ''; ?>>Persoon</option>
	</select>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	Bordgrootte:
	<input type="number" name="board_size" value="<?php echo $tictactoe->board_size; ?>" min="7" max="21">
	<br>
	<!--
	Op-een-rij:
	<input type="number" name="in_a_row" value="<?php echo $tictactoe->in_a_row; ?>" min="3" max="5">
	-->
</form>
<div id="egg-outer">
	<div id="egg-inner"></div>
</div>
</section>
</body>
</html>
