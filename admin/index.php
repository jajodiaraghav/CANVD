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
          $error = '<div class="alert alert-danger">Error: Wrong Username or Password!</div>';
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
    <div class="row">
      <div class="col-md-4 col-md-offset-4 text-center">
        <h3>Please Sign In</h3>
        <form role="form" action="../admin/" method="post">
          <input type="text" name="username" class="input-group form-control" placeholder="Username" required autofocus>
          <input type="password" name="password" class="input-group form-control input-group-lg" placeholder="Password" required>
          <button class="btn btn-sm btn-primary" type="submit">Sign in</button>
        </form>
      </div>
    </div>
    <?php
      } else {
    ?>
      <a href="../partials/logout.php" class="btn btn-md btn-primary pull-right">Log out</a>
      <button class="btn btn-md btn-default pull-right" id="data-btn">Data</button>
      <button class="btn btn-md btn-success pull-right" id="ann-btn">Announcements</button>

    <?php
      if (isset($_GET['submit'])) {
    ?>
      <div class="alert alert-info pull-right alert-dismissible" role="alert">
        <button class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        Table "<?php echo $_GET['submit']; ?>" was altered successfully.
      </div>
    <?php
      } elseif (isset($_GET['message'])) {
    ?>
      <div class="alert alert-info pull-right" id="quickalert" role="alert">
        Operation Successful!
      </div>
    <?php
      }
    ?>

    <h3>DV-IMPACT Administration Panel</h3>
      <div class="row" id="announce-view">
        <?php include_once('includes/announcement.php') ?>
      </div>
      <div class="row" id="data-view">
        <div class="col-md-8 col-md-offset-2">
          <ul class="nav nav-tabs nav-justified" role="tablist">
            <li role="presentation" class="active">
              <a href="#home" aria-controls="home" role="tab" data-toggle="tab">Upload Data</a>
            </li>
            <li role="presentation">
              <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Upload Files</a>
            </li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
              <?php include_once('includes/PSI_upload.php') ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="profile">
              <?php include_once('includes/DATA_upload.php') ?>
            </div>
          </div>
        </div>
      </div>
      <?php
        }
      ?>
    </div>
      <div class="container">
        <?php include_once('../partials/footer.php') ?>
      </div>
    </div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td class="text-center">
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary btn-sm start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning btn-sm cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <p class="name">
              <span>{%=file.name%}</span>
            </p>
            {% if (file.error) { %}
              <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>        
    </tr>
{% } %}
</script>

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