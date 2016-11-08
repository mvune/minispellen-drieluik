<?php
require_once 'classes/Hanoi.php';
session_start();

if(isset($_SESSION['hanoi'])) {
	$hanoi = $_SESSION['hanoi'];
} else {
	$hanoi = new Hanoi();
}

if(isset($_POST['reset'])) {
	$hanoi = !empty($_POST['number']) ? new Hanoi($_POST['number']) : new Hanoi();
}

$hanoi->executeTurn($_POST);
$_SESSION['hanoi'] = $hanoi;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Hanoi</title>
	<link rel="stylesheet" type="text/css" href="style/hanoi-style.css">
</head>

<body>
<a href="/minispellen-drieluik">
	<img id="back" src="style/images/terug.png" alt="Terug naar luik" title="Terug naar luik">
</a>
<h1>Toren van Hanoi</h1>
<section>
<?php echo $hanoi->display(); ?>
</section>
<br>
<?php echo $hanoi->getMessage(); ?>
<br><br>
<form action="" method="POST">
	Aantal schijven:
	<input type="number" name="number" value="<?php echo $hanoi->numberOfDiscs(); ?>" min="3" max="8">
	<input type="submit" name="reset" value="Opnieuw">
</form>
<div id="uitleg">
	<h2>Uitleg:</h2>
	Doel van het spel is om de gehele toren van schijven te verplaatsen naar een van de andere twee stokken, waarbij rekening moet worden gehouden met de volgende twee regels:
	<ol>
		<li>Er mag slechts 1 schijf tegelijk worden verplaatst.</li>
		<li>Een grotere schijf mag nooit op een kleinere liggen.</li>
	</ol>
</div>
</body>
</html>
