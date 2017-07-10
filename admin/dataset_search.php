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
						<input type="text" class="form-control" id="search">
					</div>
				</div>
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<table class="table table-bordered table-striped table-condensed">
							<thead>
								<tr>
									<th>Dataset ID</th>
									<th>Publication ID</th>
									<th>Author</th>
									<th>Description</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
							<?php
								$query = 'SELECT * FROM T_Dataset';
								$stmt = $dbh->prepare($query);
								$stmt->execute();
								$arr = array();
								while ($row = $stmt->fetch()) {
									$row[4] = 'DS_' . str_pad($row[0], 4, "0", STR_PAD_LEFT);
									$arr[] = $row;
							?>
								<tr>
									<td><?=$row[4]?></td>
									<td><?=$row[2]?></td>
									<td><?=$row[1]?></td>
									<td><?=$row[3]?></td>
									<td><a href="dataset_download.php?set=<?=$row[0]?>" class="btn btn-xs btn-success">Download</a></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php include_once('../partials/footer.php') ?>
			</div>
		</div>
		<script src="/assets/js/jquery.min.js"></script>
		<script>
			$(function() {
				var arr = <?=json_encode($arr)?>;				
				$('#search').on('keyup paste', function(){

					key = $(this).val().toLowerCase();
					search_array = $.grep(arr, function(tmp) {
						return tmp[4].toLowerCase().indexOf(key) >= 0 || tmp[2].toLowerCase().indexOf(key) >= 0;
					});

					var search_html = '';
					for(var i = 0; i < search_array.length; i++)
					{
						search_html += '<tr>';						
						search_html += '<td>' + search_array[i][4] + '</td>';
						search_html += '<td>' + search_array[i][2] + '</td>';
						search_html += '<td>' + search_array[i][1] + '</td>';
						search_html += '<td>' + search_array[i][3] + '</td>';
						search_html += '<td><a href="dataset_download.php?set=' + search_array[i][0] + '" class="btn btn-xs btn-success">Download</a></td>';
						search_html += '</tr>';
					}
					if(search_array.length == 0 && search_html == '')
					{
						search_html = '<tr><td colspan="6">Nothong Found!</td></tr>';
					}

					$('table tbody').html(search_html);
				});
			});
		</script>
	</body>
</html>