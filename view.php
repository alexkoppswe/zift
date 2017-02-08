<?php
require_once("config.php");
require_once("sql_connect.php");
if(!isset($_SESSION)){session_start();}

//URL sanitazion
if(isset($_GET["url"])) {
	$filen = $_GET["url"];
	$filen = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filen);
	$filen = mb_ereg_replace("([\.]{2,})", '', $filen);
	$ext = pathinfo($imgdir.$filen, PATHINFO_EXTENSION);
} else {
	$filen = null;
	$ext = null;
}
?>
<!DOCTYPE HTML>
<html>
	<head>
    <title>files</title>
	  <meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <link rel="icon" href="favicon.ico">
		<link rel="stylesheet" href="css/stylesheet.css">
		<link rel="shortcut icon" type="image/png" href="favicon.png">
	</head>

	<body>
		<div id="wrapper">
			<div id="content">
				<?php include("header.php"); ?>
				<div id="view-button">
					<?php
					if (file_exists($imgdir.$filen)) {
						if ($ext == 'zip') {
							echo '<a href="'.$imgdir.$filen.'" title="'.$filen.'" download>download</a>';
							/*
							echo '<a style="color:white;" href="javascript:;" onclick="location.href=\'http://w3bm.cf/\'">weebm</a>';
							echo '<noscript><a href="'.$imgdir.$filen.'" title="'.$filen.'" download>download</a></noscript>';
							echo '<a href="javascript:;" onclick="location.href=\''.$imgdir.$filen.'\'"title="'.$filen.'" download>download</a>';
							*/
						} elseif (isset($filen)) {
						  echo '<div id="download-txt">Unsupported file.</div>';
						}
					}
					?>
				</div>
				<div id="view-zip">
					<div class="view-zip-left">
							<img src="img/gift-128.png">
					</div>
					<div class="view-zip-right">
						<?php
						if (isset($filen)) {
							$filen = $filen;
							$filen = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $filen);
							$filen = mb_ereg_replace("([\.]{2,})", '', $filen);
							$ext = pathinfo($imgdir.$filen, PATHINFO_EXTENSION);
								//Add 1 view per link clicked.
								if(isset($_SESSION['last_submit'])) { //time in seconds
									if($filen == $_SESSION['last_submit'][0] && ((time() - $_SESSION['last_submit'][1]) < $refreshinterval)) {
										die('<p class="view-zip-txt">Whoo whoo, You´re refreshing to fast!</p>');
							    }
								}
								$file_time = array($filen, time());
								$_SESSION['last_submit'] = $file_time;

								$viewfn = $dbh->prepare("UPDATE files SET Views = Views + 1 WHERE ActuName=:fil");
								$viewfn->bindParam(":fil", $filen);
								$viewfn->execute();

								$sfw = $dbh->prepare("SELECT * FROM files WHERE ActuName=:fil");
								$sfw->bindParam(":fil", $filen);
								$sfw->execute();

								while ($forall = $sfw->fetch()) {
									$open = zip_open($imgdir.$forall["Actuname"]);
									if($ext == "zip") {
										if ($open) {
											$zopen = zip_read($open);
											$file_name = zip_entry_name($zopen);
											if (strlen($file_name) > 41) {
													$stringCut = substr($file_name, 0, 41).'...';
													echo '<div class="view-zip-txt"><span class="view-zip-title">Filename:</span>'.$stringCut.'</div>';
											} else {
												echo '<div class="view-zip-txt"><span class="view-zip-title">Inside Zip:</span>'.$file_name.'</div>';
											}
										}
									}
									if (strlen($forall["FileName"]) > 41) {
											$stringCut = substr($forall["FileName"], 0, 41).'...';
											echo '<div class="view-zip-txt"><span class="view-zip-title">Filename:</span>'.$stringCut.'</div>';
									} else {
										echo '<div class="view-zip-txt"><span class="view-zip-title">Filename:</span>'.$forall["FileName"].'</div>';
									}
									echo '<div class="view-zip-txt"><span class="view-zip-title">Filedate:</span>'.$forall["Dateum"].'</div>';
									echo '<div class="view-zip-txt"><span class="view-zip-title">filesize:</span>'.$forall["Size"].'</div>';
									echo '<div class="view-zip-txt"><span class="view-zip-title">Views:</span>'.$forall["Views"].'</div>';
									echo '<div class="view-zip-txt"><span class="view-zip-title">Info Hash:</span>'.$forall["md"].'</div>';
								}
						}
						?>
					</div>
				</div>
				<?php include("footer.php"); ?>
			</div>
		</div>
    <script src="scripts/jquery-2.1.4.min.js"></script>
	</body>
</html>
