<?php include_once('../common.php'); ?>
<html>
	<?php include_once('../partials/header.php'); ?>
  	<div class="jumbotron">
  	  <div class="container" style="margin-bottom:15px;">
  	    <p>
          <h2 style="margin-bottom:30px;">
            News and Announcements of <?php include('../partials/logo.php'); ?>
          </h2>
        </p>
        <br/>
        <?php
          $query = 'SELECT * FROM announcements WHERE `show`=1 ORDER BY id DESC;';
          $query_params = array();
          $stmt = $dbh->prepare($query);
          $stmt->execute($query_params);
          while ($row = $stmt->fetch()) {
        ?>
        <div class="margin-top">
          <h3><?php echo $row[2];?></h3>
          <i><?php echo $row[1];?></i>
          <p class="text-justify"><?php echo $row[3];?></p>
        </div><br/>
        <?php } ?>
        <?php include_once('../partials/footer.php'); ?>
      </div>
  	</div>
	</body>
</html>