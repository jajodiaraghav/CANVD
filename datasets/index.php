<?php include_once('../common.php'); ?>
<html>
<?php include_once('../partials/header.php'); ?>
	<div class="container">
    <div class="jumbotron">
      <div class="container">
        <h2 style="margin-bottom:30px">
          Available Datasets in 
          <?php include('../partials/logo.php') ?>
        </h2>

        <p class="text-justify">
        This page lists all the datasets that are currently available in <?php include('../partials/logo.php') ?> with short description of each dataset, the description of the processing and formatting steps, the compliance with <?php include('../partials/logo.php') ?> standard data model and the publish article corresponds to the dataset.
        </p>
        <?php
          $query = 'SELECT * FROM T_Dataset';
          $stmt = $dbh->prepare($query);
          $stmt->execute();
          while ($row = $stmt->fetch()) {
        ?>
          <hr>
          <p><strong><?=$row['Title']?></strong></p>
          <p class="text-justify"><?=$row['Description']?></p>
          <p>Author: <?=$row['Author']?></p>
          <p>Publication: <?=$row['Publication']?></p>
          <a href="../admin/dataset_download.php?set=<?=$row['Dataset_ID']?>" class="btn btn-success pull-right" style="margin-top:-60px">Download</a>
        <?php } ?>
        </div>
      </div>
    </div>
  </body>
</html>