<?php
  include_once('../common.php');
  session_start();
?>
<html>
	<head>
		<title>DV-IMPACT :: Admin</title>
		<link rel="shortcut icon" href="/assets/images/canvd.ico">
		<link href="/assets/css/bootstrap.css" rel="stylesheet">
		<link href="/assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="/assets/css/styles.css" rel="stylesheet" type="text/css">
    <link href="/assets/css/admin.css" rel="stylesheet" type="text/css">    
	</head>
	<body>
	<div class="jumbotron">
	  <div class="container">
	    <h1 class="heading">
        <a href="/admin">
        <span>DV-IMPACT</span>: The <span>D</span>isease <span>V</span>ariant <span>Im</span>pact on Domain-based <span>P</span>rotein Inter<span>act</span>ions
        </a>
      </h1>
      <a href="../partials/logout.php" class="btn btn-md btn-primary pull-right">Log out</a>
      <a href="/admin" class="btn btn-md btn-default pull-right">Home</a>

      <h3>DV-IMPACT Administration Panel</h3>
      <div class="row text-center">
        <div class="col-md-4 col-md-offset-4">
          <label class="radio-inline">
            <input type="radio" name="directory" value="IMG" checked> PNG/IMAGE Folder
          </label>
          <label class="radio-inline">
            <input type="radio" name="directory" value="TXT"> TXT Folder
          </label>
          <label class="radio-inline">
            <input type="radio" name="directory" value="PWM"> PWM Folder
          </label>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 col-md-offset-4">
          <input type="text" name="search" class="form-control">
        </div>
        <div class="col-md-2 delete-selected">
          <button class="btn btn-danger btn-sm disabled">Delete Selected</button>
        </div>
      </div>      
      <br>
      <div class="row row-files">
        <div class="col-md-6 col-md-offset-3">
          <table class="table table-hover table-condensed">
            <thead>
              <th>Filename</th>
              <th>Option</th>
            </thead>
            <tbody class="list-files"></tbody>
          </table>
        </div>
      </div>
      <?php include_once('../partials/footer.php') ?>
    </div>
  </div>

    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/bootstrap.js"></script>    
    <script src="/assets/js/jquery.ui.widget.js"></script>
    <script src="/assets/js/tmpl.min.js"></script>
    <script src="/assets/js/jquery.iframe-transport.js"></script>
    <script src="/assets/js/jquery.fileupload.js"></script>
    <script src="/assets/js/jquery.fileupload-process.js"></script>
    <script src="/assets/js/jquery.fileupload-ui.js"></script>
    <script src="/assets/scripts/admin.js"></script>
	</body>
</html>