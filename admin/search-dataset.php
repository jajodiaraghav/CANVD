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
						<input type="text" class="form-control" id="search" placeholder="Search using Dataset-ID or Publication">
					</div>
				</div>
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<table class="table table-bordered table-striped table-condensed">
							<thead>
								<tr>
									<th>Dataset ID</th>
									<th>Publication ID</th>
									<th>Author</th>
									<th>Title</th>
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
									$row[5] = 'DS_' . str_pad($row[0], 4, "0", STR_PAD_LEFT);
									$arr[] = $row;
							?>
								<tr>
									<td><?=$row[5]?></td>
									<td><?=$row[2]?></td>
									<td><?=$row[1]?></td>
									<td><?=$row[4]?></td>
									<td><?=$row[3]?></td>
									<td class="actions">
										<a href="download.php?set=<?=$row[0]?>" class="btn btn-xs btn-success">Download</a>
										<a href="#" data-id="<?=$row[0]?>" class="btn btn-xs btn-warning">Edit</a>
									</td>
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
		<script src="/assets/js/bootstrap.js"></script>
		<script>
			$(function() {
				var arr = <?=json_encode($arr)?>;				
				$('#search').on('keyup paste', function(){

					key = $(this).val().toLowerCase();
					search_array = $.grep(arr, function(tmp) {
						return tmp[5].toLowerCase().indexOf(key) >= 0 || tmp[2].toLowerCase().indexOf(key) >= 0;
					});

					var search_html = '';
					for(var i = 0; i < search_array.length; i++)
					{
						search_html += '<tr>';						
						search_html += '<td>' + search_array[i][5] + '</td>';
						search_html += '<td>' + search_array[i][2] + '</td>';
						search_html += '<td>' + search_array[i][1] + '</td>';
						search_html += '<td>' + search_array[i][4] + '</td>';
						search_html += '<td>' + search_array[i][3] + '</td>';
						search_html += '<td class="actions">'
										+'<a href="download.php?set='+search_array[i][0]+'"'
										+' class="btn btn-xs btn-success">Download</a>\n'
										+'<a href="#" data-id='+search_array[i][0]
										+' class="btn btn-xs btn-warning">Edit</a>'
										+'</td>';
						search_html += '</tr>';
					}
					if(search_array.length == 0 && search_html == '')
					{
						search_html = '<tr><td colspan="6">Nothong Found!</td></tr>';
					}

					$('table tbody').html(search_html);
				});

				$('body').on('click', '.actions .btn-warning', function(){
					button = $(this);
					var data1 = button.parent().prev().prev().prev().prev().text();
					var data2 = button.parent().prev().prev().prev().text();
					var data3 = button.parent().prev().prev().text();
					var data4 = button.parent().prev().text();
					var id = button.data('id');
					var modal = $('#editModal');
					modal.find('.modal-body input[name=pub]').val(data1);
					modal.find('.modal-body input[name=author]').val(data2);
					modal.find('.modal-body input[name=title]').val(data3);
					modal.find('.modal-body textarea').text(data4);
					modal.find('.modal-body input[name=id]').val(id);
					modal.modal();
				});
			});
		</script>

		<div class="modal fade" tabindex="-1" role="dialog" id="editModal">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
				    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				    	<h4 class="modal-title">Editing Dataset</h4>
				  	</div>
				  	<form action="backend/dataset/update.php" method="POST">
					  	<div class="modal-body">				    	
					    		<input type="hidden" name="id">
					    		<input type="text" class="form-control" placeholder="Publication" name="pub">
					    		<input type="text" class="form-control" placeholder="Author" name="author">
					    		<input type="text" class="form-control" placeholder="Title" name="title">
					    		<textarea class="form-control" rows="10" placeholder="Description" name="descr"></textarea>
						</div>
						<div class="modal-footer">
					    	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					    	<button type="submit" class="btn btn-primary">Save changes</button>
						</div>
					</form>
				</div>
			</div>
		</div>

	</body>
</html>