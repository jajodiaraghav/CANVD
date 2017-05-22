<div class="col-md-6 col-md-offset-3">
  <div class="panel panel-default">
    <div class="panel-heading">Add an announcement</div>
    <div class="panel-body">
      <form role="form" action="./create_announce.php" method="get">
      <input type="text" class="form-control" name="title" placeholder="Announcement title">
      <textarea name="body" class="form-control" placeholder="Announcement text (supports html tags)." rows="5"></textarea>
      <button type="submit" class="btn btn-default btn-sm pull-right">Submit</button>
      </form>
    </div>
  </div>
  <?php
    $query = 'SELECT * FROM announcements ORDER BY id DESC;';
    $query_params = array();
    $stmt = $dbh->prepare($query);
    $stmt->execute($query_params);
    while ($row = $stmt->fetch()) {
  ?>
  <div class="panel panel-success">
    <div class="panel-heading">
      <span class="pull-right">
        <?php if ($row[4] == 1) { ?>
          <i class="fa fa-check-square-o show-btn" data-btn-type='show' data-item-id=<?php echo $row[0]?>>
            <span style="font-size:0.7em">Show</span>
          </i>
        <?php } else { ?>
          <i class="fa fa-square-o show-btn" data-btn-type='show' data-item-id=<?php echo $row[0]?>>
            <span style="font-size:0.7em">Show</span>
          </i>
        <?php } if ($row[5] == 1) { ?>
          <i class="fa fa-check-square-o show-btn" data-btn-type='show_homepage' data-item-id=<?php echo $row[0]?> style="margin-left:15px;">
            <span style="font-size:0.7em">Show in Home</span>
          </i>
        <?php } else { ?>
          <i class="fa fa-square-o show-btn" data-btn-type='show_homepage' data-item-id=<?php echo $row[0]?> style="margin-left:15px;">
            <span style="font-size:0.7em">Show in Home</span>
          </i>
        <?php
          }
        ?>
      </span>
      <?php echo $row[2]; ?>
    </div>
    <div class="panel-body"><?php echo $row[3] ?></div>
      <a data-item-id=<?php echo $row[0]?> class="pull-right delete-btn">Delete</a>
      <p><?php echo $row[1] ?></p>
    </div>
  <?php
    }
  ?>
</div>