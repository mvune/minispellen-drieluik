<?php
require_once('classes/Kluiscodekraker.php');
$mastermind = Mastermind::loadGame();
$mastermind->playGame($_POST);
?>
<!DOCTYPE html>
<title>Kluiscodekraker</title>
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Qwigley" />
<link rel="stylesheet" type="text/css" href="style/kluiscodekraker-style.css" />
<script src="javascript/draai-wiel.js"></script>

<body>
<a href="./">
	<img id="back" src="style/images/terug.png" alt="Terug naar luik" title="Terug naar luik">
</a>
<div id="container">
<div id="left-container">
	<h1>Kluiscodekraker</h1>
	<p>Kraak de code. De code bestaat uit 4 getallen van 1 t/m 6.
	Je mag 10 keer raden.</p>
<?php echo $mastermind->showSafe(); ?>
<?php echo $mastermind->showResultMessage(); ?>
	<p><form action="" method="post">Druk op <button type="submit" 
	name="reset" id="reset-button">Reset</button> om opnieuw te 
	beginnen.</form></p>
	<div id="colors-explanation">
		<h2>Uitleg kleurcodes:</h2>
		<div class="color green"></div> = Een juist cijfer op de juiste plek.
		<div class="color yellow"></div> = Een juist cijfer, maar op de verkeerde plek.
		<div class="color red"></div> = Een onjuist cijfer.
	</div>
</div>

<div id="notepaper">
	Geprobeerd:
<?php echo $mastermind->showGuessedList(); ?>
</div>
</div>
</body>
</html>
