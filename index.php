<?php
require_once("config.php");
require_once("sql_connect.php");
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
    <title>Zift.cf</title>
	  <meta charset="utf-8">
		<meta http-equiv="refresh" content="120">
		<meta name="description" content="Zift is a site where you upload any file you want and download zip archives.">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=yes">
    <link rel="icon" href="favicon.ico">
		<link rel="stylesheet" href="css/stylesheet.css">
		<link rel="shortcut icon" type="image/png" href="favicon.png">
	</head>

	<body>
		<div id="wrapper">
			<div id="content">
				<?php include("header.php"); ?>
        <div id="upload-form">
					<form method="post" enctype="multipart/form-data">
						<div class="file-describe-text">
							<input id="choose-file" name="upfile" type="file">
							<p>Max filesize: <span class="marked"><?php if(isset($_fsize)){echo get_file_size($_fsize);}?></span></p>
              <p>Allowed files: <span class="marked"><?php if(isset($allowed_types)){echo implode(",", $allowed_types);}?></span></p>
							<div id="progress"></div>
            </div>
						<input id="upload-button" type="submit" name="submitbtn" value="Upload">
				  </form>
					<?php
						//mb,gb osv...
						function get_file_size($size) {
							$units = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
							return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 1).' '.$units[$i];
						}
						//check if the folder is writable
						if (is_writable($imgdir)) {
	            //check if the form is submited
							if (isset($_POST['submitbtn'])) {
	              echo '<div id="upload-result">';

	              if(isset($allowed_types)) {
	                $allowed_file_types = implode(",", $allowed_types);
	              }

	              $_filetype = $_FILES["upfile"]["type"];
	              $_filesize = $_FILES["upfile"]["size"];
	              $_filename = strip_tags($_FILES['upfile']['name']);
	              $_filetmp = $_FILES['upfile']['tmp_name'];
	              $_fileeror = $_FILES["upfile"]["error"];

	              //Rename filename to 32 bit string
	              $randstring = md5(time());
	              $splitname = explode(".", $_filename);
	              $fileext = end($splitname);
	              $newfilename  = strtolower($randstring.'.'.$fileext);

	              $target_path = $imgdir.$newfilename;
								if (isset($_filename) && $_filename !='') { //Check if file is loaded
	                //upload local file code
	                $ext = pathinfo($_filename, PATHINFO_EXTENSION); //Get file extension
	                  if(in_array($ext,$allowed_types)) { //Check file extension
	                		if($_filesize <= $_fsize) { //Check filesize
													//Check if file exist in db
													$mdfile = md5_file($_filetmp);
													$mdsf = $dbh->query("SELECT 1 FROM files WHERE md = '$mdfile'");
													if ($mdsf->rowCount() == 0) {
														if(move_uploaded_file($_filetmp, $target_path)) {
															if($ext == "zip") {
																$open = zip_open($target_path);
																if ($open) {
																	$zopen = zip_read($open);
																	$file_name = zip_entry_name($zopen);
																	$zipext = pathinfo($file_name, PATHINFO_EXTENSION);
																	if(strtoupper($zipext) == 'EXE') {
																		echo '<span class="display-msg">Illigal file inside zip.</span>';
																		header("Refresh:2");
																		die();
																	}
																}
															}
															//Insert data in db
															$gfsiz = get_file_size($_filesize);
															$numbz = 0;
															try {
																$iivdb = $dbh->prepare("INSERT INTO files (Actuname, FileName, Dateum, ActuSize, Size, Type, Views, md) VALUES (:newname, :filename, :dateum, :bytesize, :fsize, :fext, :views, :md)");
																$iivdb->bindParam(":newname", $newfilename);
																$iivdb->bindParam(":filename", $_filename);
																$iivdb->bindParam(":dateum", $date);
																$iivdb->bindParam(":bytesize", $_filesize);
																$iivdb->bindParam(":fsize", $gfsiz);
																//$iivdb->bindParam(":fext", $fileext);
																$iivdb->bindParam(":fext", $zipext);
																$iivdb->bindParam(":views", $numbz);
																$iivdb->bindParam(":md", $mdfile);
																$iivdb->execute();
															}
															catch(PDOException $e) {
																echo "<br>".$e->getMessage();
																die();
															}
															echo '<span class="display-msg">'.  basename($_filename).' has been uploaded.</span></br>';
														} elseif(!move_uploaded_file($_filetmp, $target_path)) {
		                					echo '<span class="display-msg">File could not be uploaded.</span>';
															if(isset($_fileeror) && !$_fileeror == 0) {
																echo '<span class="display-msg">'.$_fileeror.'</span>';
															}
		              					}
													} elseif (!$mdsf->rowCount() == 0) {
														echo '<span class="display-msg">File does already exist.</span>';
	              					}
											} elseif($_filesize > $_fsize) {
			          						echo '<span class="display-msg">filesize is to big. Maximum filesize '.get_file_size($_fsize).'</span>';
			          					}
										} elseif(!in_array($ext,$allowed_types)) {
			          	     echo '<span class="display-msg">Illigal file! Allowed files are: '.$allowed_file_types.'</span>';
			          	    } else {
			                  if(isset($_fileeror) && $_fileeror !='0'){echo 'File upload error: '.$_fileeror;}
			                }
					    } else {
								echo '<span class="display-msg">File is not loaded.</span>';
							}
					    echo '</div>';
						}
					} else {
						echo '<span class="display-msg">Warning! uppload directory is not writable.</span>';
					}
        echo '</div>';

				echo '<script type="text/javascript"> function requestPrice() { jQuery("#ftable").load("dblist.php"); } setInterval(requestPrice, 3000); </script>'; //Refresh db every x seconds

						if(!isset($urlorder)) { $urlorder = null; }
						if(isset($_GET["order"])) {
							if($_GET["order"]) {
								$urlorder = 0;
								$order = "DESC";
							} else {
								$urlorder = 1;
								$order = "ASC";
							}
						} else {
							$urlorder = 1;
							$order = "ASC"; //or "DESC"
						}
						//return results from db
						echo '<div id="ftable">';
						echo '<div class="descbar">
						<span class="tbl-image"></span>
						<span class="tbl-link2"><a href="index.php?sortby=FileName&order='.$urlorder.'">Filename</a></span>
						<span class="tbl-type"><a href="index.php?sortby=Type&order='.$urlorder.'">Type</a></span>
						<span class="tbl-date"><a href="index.php?sortby=Dateum&order='.$urlorder.'">Date</a></span>
						<span class="tbl-size"><a href="index.php?sortby=ActuSize&order='.$urlorder.'">Size</a></span>
						<span class="tbl-view"><a href="index.php?sortby=Views&order='.$urlorder.'">Views</a></span>
						</div>';

						//List results from db in a table
						try {
							$allstmt = $dbh->prepare("SELECT * FROM files");
							$allstmt->execute();
							if (!$allstmt->rowCount() == 0) {
								//$num_rec_per_page= 10;
								if (isset($_GET["page"]) && is_numeric($_GET["page"])) {
									$page  = $_GET["page"];
								} else { $page=1; };
								$start_from = ($page-1) * $num_rec_per_page;
								//sort catagories links
								$validorning = array('FileName', 'Type', 'Dateum', 'ActuSize', 'Views');
								if(isset($_GET["sortby"]) && $_GET["sortby"] !='') {
									$sortby = $_GET["sortby"];
									$sortby = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $sortby);
									$sortby = mb_ereg_replace("([\.]{2,})", '', $sortby);
									if (in_array($sortby, $validorning)) {
										$allstmt = $dbh->prepare("SELECT * FROM files ORDER BY $sortby $order LIMIT $start_from, $num_rec_per_page");
										$allstmt->execute();
									}
								} else {
									$allstmt = $dbh->prepare("SELECT * FROM files ORDER BY Dateum DESC LIMIT $start_from, $num_rec_per_page");
									$allstmt->execute();
								}
									while ($results = $allstmt->fetch(PDO::FETCH_ASSOC)) {
										echo '<div class="row">';
										echo '<div class="cell tbl-image"><img src="img/gift-128.png" alt="A present icon"></div>';
										//limit the filename and adds a ... the end
										if (strlen($results["FileName"]) > 44) {
											$stringCut = substr($results["FileName"], 0, 44).'...';
											echo '<div class="cell ftxt tbl-link"><a href="view.php?url='.$results["Actuname"].'" target="_blank">'.$stringCut.'</a></div>';
										} else {
											echo '<div class="cell ftxt tbl-link"><a href="view.php?url='.$results["Actuname"].'" target="_blank">'.$results["FileName"].'</a></div>';
										}
										echo '<div class="cell ftxt tbl-type">'.$results["Type"].'</div>';
										echo '<div class="cell ftxt tbl-date">'.$results["Dateum"].'</div>';
										echo '<div class="cell ftxt tbl-size">'.$results["Size"].'</div>';
										if (strlen($results["Views"]) > 5) {
											$stringCut = substr($results["FileName"], 0, 5).'+';
											echo '<div class="cell ftxt tbl-view">'.$stringCut.'</div>';
										} else {
											echo '<div class="cell ftxt tbl-view">'.$results["Views"].'</div>';
										}
										echo '</div>';
									}
									$sfl = $dbh->prepare("SELECT * FROM files");
									$sfl->execute();
									$total_records = $sfl->rowCount();  //count number of records
									$total_pages = ceil($total_records / $num_rec_per_page);
									echo '<div class="pagination row">';
									echo '<a href="index.php?page=1">'."<".'</a> '; // Goto 1st page

									for ($i=1; $i<=$total_pages; $i++) {
										echo '<a href="index.php?page='.$i.'">'.$i.'</a> ';
									};
									echo '<a href="index.php?page='.$total_pages.'">'.">".'</a> '; // Goto last page
									echo '</div>';
								} else {
										echo '<div id="nofile">No files found</div>';
								}
				 	}
					 catch(PDOException $e) {
						 echo $e->getMessage();
						 die();
					 }
						echo '</div>';

						//take files older than 2 days and less than avrage score and delete them
						try {
							if (!$allstmt->rowCount() == 0) {
							$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
							$average = $dbh->query("SELECT AVG(Views) FROM files")->fetchColumn();
							$allsfwaa = $dbh->query("SELECT * FROM files WHERE Dateum <= CURRENT_DATE() AND Dateum <= DATE_SUB(CURRENT_DATE(), INTERVAL $deldays DAY) AND Views < $average");
							$delsfwaa = $dbh->query("DELETE FROM files WHERE Dateum <= CURRENT_DATE() AND Dateum <= DATE_SUB(CURRENT_DATE(), INTERVAL $deldays DAY) AND Views < $average");
							//Dev delete
							//$allsfwaa = $dbh->query("SELECT * FROM files");
							//$delsfwaa = $dbh->query("DELETE FROM files");
							if (!$allsfwaa->rowCount() == 0) {
								while ($forall = $allsfwaa->fetch()) {
										if(!unlink($imgdir.$forall["Actuname"])) {
											die("Could not delete from folder");
										} else {
											if(!$delsfwaa) {
												die("Could not delete from db");
											}
										}
								}
							}
						}
						}
						catch(PDOException $e) {
							echo $e->getMessage();
							die();
						}
						?>
				</div>
				<?php include("footer.php"); ?>
			</div>
		</div>
    <script src="scripts/jquery-2.1.4.min.js"></script>
    <script src="scripts/upload-jquery.js"></script>
				<!--
		<script>
			function opendesc() {
			      $.ajax({
			           type: "POST",
			           url: 'dl.php',
			           data: {action: 'call_this'},
			           success: function(data) {
			             //alert(data);
									 $("#show-zip").load('dl.php');
			           }

			      });
			 }
		</script>
		<script type="text/javascript">
		$('.row').click(function() {
	    $(this).toggleClass('showmore');
			$('#view-zip').toggleClass('showmore');
	    return false;
		})
		</script>
		<script type="text/javascript">
		$( ".row" ).click(function() {
		$( "#view-zip" ).toggle();
		$( ".tbl-image .tbl-link .tbl-type .tbl-date .tbl-size .tbl-view" ).toggle();
		});
		</script>
		-->
	</body>
</html>
