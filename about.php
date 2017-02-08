<?php
require_once("config.php");
?>
<!DOCTYPE HTML>
<html>
	<head>
    <title>About</title>
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
				<div id="about">
					<h3>Zift - About</h3>
					<p>
						Zift is a site where you can upload <?php if(isset($allowed_types)){echo implode(",", $allowed_types);}?> files and download others.
						If the files are unpopular and old (about 2 days) they will be removed to make way for new content.
						<br>
						You can reach me at webmaster@zift.cf
					</p>
				</div>
				<?php include("footer.php"); ?>
			</div>
		</div>
	</body>
</html>
