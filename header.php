<div id="banner">
  <div id="domainname">
    <a href="index.php">Zift.cf</a>
  </div>
  <form id="searchfield" method="post">
      <input id="searchinput" type="text" name="search" placeholder="Search"><!--
      --><select name="searchpar">
        <option value="Filename" selected>Filename</option>
        <option value="Type">Filetype</option>
        <option value="Views">Views</option>
        <option value="Size">filesize</option>
      </select><!--
      --><input id="searchbtn" type="submit" name="submit" value="Search">
  </form>
  <?php
  require_once("config.php");
  require_once("sql_connect.php");
    if(isset($_POST['search']) && $_POST['search'] !='') {
      // Search from MySQL database table
      $searchpar = $_POST['searchpar'];
      $search = $_POST['search'];
      $search = mb_ereg_replace("([^\w\s\d\-_~,;:\[\]\(\).])", '', $search);
      $search = mb_ereg_replace("([\.]{2,})", '', $search);
      try {
        $query = $dbh->prepare("SELECT * FROM files WHERE LOWER($searchpar) LIKE LOWER(:keywords) LIMIT 0 , $num_rec_per_page");
        $query->bindValue(':keywords', '%' . $search . '%');
        $query->execute();
      }
      catch(PDOException $e) {
        echo "<br>".$e->getMessage();
        die();
      }
      // Display search result
      echo '<div id="search-result">';
       if (!$query->rowCount() == 0) {
         echo '<div id="ftable">';
         echo '<div class="descbar">
         <span class="tbl-image"></span>
         <span class="tbl-link2">Filename</a></span>
         <span class="tbl-type">Type</span>
         <span class="tbl-date">Date</span>
         <span class="tbl-size">Size</span>
         <span class="tbl-view">Views</span>
         </div>';
          while ($results = $query->fetch()) {
            echo '<div class="row">';
            echo '<div class="cell tbl-image"><img src="img/gift-128.png"></div>';
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
      echo '</div>';
      echo '</div>';
      } else {
          echo '<div id="nofile">No files found</div>';
      }
    }
  ?>
</div>
