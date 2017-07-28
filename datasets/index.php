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
        <div class="row">
          <div class="col-md-4 col-md-offset-4">
            <input type="text" class="form-control" placeholder="Filter dataset based on Publication">
          </div>
        </div>
        <?php
          $query = 'SELECT * FROM T_Dataset';
          $stmt = $dbh->prepare($query);
          $stmt->execute();
          while ($row = $stmt->fetch()) {
        ?>
          <div class="section">
            <hr>
            <p><strong><?=$row['Title']?></strong></p>
            <p class="text-justify"><?=$row['Description']?></p>
            <p>Publication: <?=$row['Author']?></p>
            <p><span class="target"><?=$row['Publication']?></span></p>
            <a href="../admin/dataset_download.php?set=<?=$row['Dataset_ID']?>" class="btn btn-success pull-right" style="margin-top:-60px">Download</a>
          </div>
        <?php } ?>
        </div>
      </div>
    </div>
    <script>
      $(function(){
        $('input').on('keyup paste', function(){
          $('.section').hide();
          var str = $(this).val().toLowerCase();
          $('.target').each(function (i, e) {
            var pub = $(e).text().toLowerCase();
            if (pub.indexOf(str) >= 0)
              $(this).parent().parent().show();
          });
        });
      });
    </script>
  </body>
</html>