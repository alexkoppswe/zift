<?php
header("Accept-Ranges: bytes");
ini_set('max_input_time', 1200);
date_default_timezone_set('Europe/Stockholm');
$date = date('Y-m-d H:i');
//File upload settings
$imgdir = 'archives/';
$_fsize = 1024 * 512000; //500MB
$allowed_types = array('zip');
$refreshinterval = 2; //Number of seconds to refresh tab.
$deldays = 2; //number of days before files get deleted.
$num_rec_per_page= 10; //number of items per page.
?>
