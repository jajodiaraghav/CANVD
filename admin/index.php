<?php
  include_once('../common.php');
  session_start();
?>
<html>
	<head>
		<title>DV-IMPACT :: Admin</title>
		<link rel="shortcut icon" href="/assets/images/canvd.ico">
		<link href="/assets/css/bootstrap.css" rel="stylesheet">
		<link href="http://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css" rel="stylesheet">
    <link type="text/css" href="/assets/css/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/admin.css"  rel="stylesheet" type="text/css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="/assets/js/site.js"></script>
    <script src="/assets/scripts/admin.js"></script>
	</head>

	<body style="background:#fafafa;">
	<div class="jumbotron" style="margin-bottom:0px;height:100%">
	  <div class="container" style="margin-bottom:15px;">
	    <h1>
        <a href="/admin">
        <span style="color:#ea2f10">DV-IMPACT</span>: The <span style="color:#ea2f10">D</span>isease <span style="color:#ea2f10">V</span>ariant <span style="color:#ea2f10">Im</span>pact on Domain-based <span style="color:#ea2f10">P</span>rotein Inter<span style="color:#ea2f10">act</span>ions
        </a>
      </h1>

    <?php
        $failure = false;
        $error = '';
        if (isset($_POST['username'])) {
          $query = 'SELECT password FROM admin WHERE username = :usr;';
          $query_params = array(':usr' => $_POST['username']);
          $stmt = $dbh->prepare($query);
          $stmt->execute($query_params);
          $pass = $stmt->fetch()[0];
          if (password_verify($_POST['password'],$pass)) {
            $_SESSION['user']=$_POST['username'];
          } else {
            $failure = true;
            $error = "<div class=\"alert alert-warning\" style='margin-right:450px;margin-left:50px;'><p class='lead' style='color:white;'>Error: Wrong Username or Password.</p></div>";
          }
        } else {
          $failure = true;
        }

        if ($failure)
        {
          if (isset($_SESSION['user']) && $_SESSION['user'] == 'admin') {
            $failure = false;
          }
        }
        if ($failure) {
          echo $error;
    ?>

      <h2 class="form-signin" style="padding-left:10px;">DV-IMPACT Admin- Please Sign In</h2>
      <form class="form-signin" role="form" action="../admin/" method="post">
        <input type="input" name="username" style="margin-left:120px;margin-top:40px;margin-bottom:20px;width:300px;" class="input-group" placeholder="Username" required autofocus>
        <input type="password" name="password" style="margin-left:120px;margin-top:20px;margin-bottom:20px;width:300px;" class="input-group input-group-lg" placeholder="Password" required>
        <button class="btn btn-md btn-primary" style="margin-top:20px;margin-left:200px;" type="submit">Sign in</button>
      </form>
    <?php
      } else {
    ?>
      <form action="../logout.php" method="post">
        <button class="btn btn-md btn-primary pull-right" type="submit" style="margin-top:5px;margin-left:10px;">Log out</button>
      </form>
      <button class="btn btn-md btn-default pull-right" id="data-btn" style="margin-top:1px;margin-left:10px;">Data </button>
      <button class="btn btn-md btn-success pull-right" id="ann-btn"  style="margin-top:1px;margin-left:10px;">Announcements</button>

    <?php
      if (isset($_GET['submit'])) {
    ?>
      <div class="alert alert-info pull-right" id="quickalert" role="alert" style="width:300px;text-align:center;">Table "<?php echo $_GET['submit']; ?>" was altered successfully.</div>
    <?php
      }
    ?>
                          
    <h2 style="margin-top:20px;"> DV-IMPACT Administration Panel </h2>
      <div class="row" style="margin-top:50px" id="announce-view">
      <div class="col-md-6 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">Add an announcement</div>
          <div class="panel-body">
            <form role="form" action="./create_announce.php" method="get">
            <input type="text" style="width:90%" class="form-control" name="title" placeholder="Announcement title">
            <textarea style="width:90%;margin-bottom:25px;margin-top:20px;color:#6f6f6f;resize:none;" name="body" placeholder="Announcement text (supports html tags)." rows="5"></textarea>
            <button type="submit" class="btn btn-default">Submit</button>
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
                <?php
                  if ($row[4] == 1) {
                ?>
                <i class="fa fa-check-square-o show-btn" data-btn-type='show' data-item-id=<?php echo $row[0]?>> <span style="font-size:0.7em"> Show</span></i>
                <?php
                  } else {
                ?>
                  <i class="fa fa-square-o show-btn" data-btn-type='show' data-item-id=<?php echo $row[0]?>> <span style="font-size:0.7em"> Show</span></i>
                <?php
                  }

                  if ($row[5] == 1) {
                ?>
                  <i class="fa fa-check-square-o show-btn" data-btn-type='show_homepage' data-item-id=<?php echo $row[0]?> style="margin-left:15px;"> <span style="font-size:0.7em"> Show in Home</span></i>
                <?php
                  } else {
                ?>
                  <i class="fa fa-square-o show-btn" data-btn-type='show_homepage' data-item-id=<?php echo $row[0]?> style="margin-left:15px;"> <span style="font-size:0.7em"> Show in Home</span></i>
                <?php
                  }
                ?>
              </span>
                <?php echo $row[2]; ?>
                </div>
                  <div class="panel-body"><?php echo $row[3]; ?></div>
                  <a style="font-size:0.7em;margin-bottom:5px;margin-right:10px;" data-item-id=<?php echo $row[0]?> class="pull-right delete-btn">Delete</a>
                  <p style="font-size:0.7em;margin-bottom:5px;margin-left:10px;font-style:italic;">
                    <?php echo $row[1];?>
                  </p>
                  <div class="clearfix"></div>
                </div>
                <?php
                  }
                ?>
                </div>
              </div>
              <div class="row" style="margin-top:50px;display:none;" id="data-view">
                  <div class="col-md-8 col-md-offset-2">
                    <div class="panel panel-default">
                      <div class="panel-heading" id="table-name-header">
                        Please upload a PSI MI-TAB file or <a href="update_table.php">Click here to update the tissues table.</a></div>
                        <div class="panel-body">
                            <div id="panel-content">
                              <div id='fields'>
                              <b>The following fields (columns) are required for this table:
                                <p style='font-size:0.7em'>
                                  Unique identifier for interactor A, Unique identifier for interactor B, Alternative identifier for interactor A, Alternative identifier for interactor B, Aliases for A, Aliases for B, Interaction detection methods, First author, Identifier of the publication, NCBI Taxonomy identifier for interactor A, NCBI Taxonomy identifier for interactor B, Interaction types, Source databases, Interaction identifier(s), Confidence score, Complex expansion, Biological role A, Biological role B, Experimental role A, Experimental role B, Interactor type A, Interactor type B, Xref for interactor A, Xref for interactor B, Xref for the interaction, Annotations for interactor A, Annotations for interactor B, Annotations for the interaction, NCBI Taxonomy identifier for the host organism, Parameters of the interaction, Creation date, Update date, Checksum for interactor A, Checksum for interactor B, Checksum for interaction, negative,  Feature(s) for interactor A:  Feature(s) for interactor B, Stoichiometry for interactor A, Stoichiometry for interactor B, Participant identification method for interactor A, Participant identification method for interactor B
                                </p>
                              </div>
                              <p>
                              <p>Note: To upload data to DV-IMPACT, the PSI-MI-TAB file must be tab separated text file with the appropriate column structure needed for the table.</p>

                              <form action="upload_psi.php" method="post" enctype="multipart/form-data">
                                <label for="file">Select a PSI-MI-TAB File to upload:</label>
                                <input type="file" name="file" id="file"><br>
                                <div data-toggle="buttons" style="margin-top:5px;">
                                  <label>
                                    <input type="radio" name="action" value="add" checked>
                                    Append this data to the table
                                  </label>
                                  <label>
                                    <input type="radio" name="action" value="replace">
                                    Replace the ENTIRE table with this data
                                  </label>
                                </div>
                                <button class="btn btn-md btn-primary" style="margin-top:15px;" type="submit">Submit</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  <?php
                    }
                  ?>
                </div>
                <div class="container">
                  <?php include '../footer.php'; ?>
	              </div>
	            </div>
	</body>
</html>